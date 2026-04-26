<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\PasswordValidationRules;
use App\Http\Controllers\Controller;
use App\Models\PendingRegistration;
use App\Models\User;
use App\Notifications\CompleteRegistration;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravel\Jetstream\Jetstream;

class RegisteredUserController extends Controller
{
    use PasswordValidationRules;

    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => Str::lower(trim((string) $request->input('email'))),
        ]);

        $validated = $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (User::query()->where('email', $value)->whereNotNull('email_verified_at')->exists()) {
                        $fail(__('This email address is already registered.'));
                    }
                },
            ],
        ]);

        $token = Str::random(64);

        $pendingRegistration = DB::transaction(function () use ($validated, $token): PendingRegistration {
            PendingRegistration::query()
                ->where('expires_at', '<=', now())
                ->delete();

            PendingRegistration::query()
                ->where('email', $validated['email'])
                ->delete();

            $legacyUsers = User::query()
                ->where('email', $validated['email'])
                ->whereNull('email_verified_at')
                ->pluck('id');

            if ($legacyUsers->isNotEmpty()) {
                DB::table('sessions')->whereIn('user_id', $legacyUsers)->delete();
                User::query()->whereKey($legacyUsers)->delete();
            }

            return PendingRegistration::create([
                'email' => $validated['email'],
                'token_hash' => hash('sha256', $token),
                'expires_at' => now()->addMinutes(60),
            ]);
        });

        Notification::route('mail', $pendingRegistration->email)
            ->notify(new CompleteRegistration($pendingRegistration, $token));

        return redirect()
            ->route('register')
            ->with('status', 'registration-link-sent');
    }

    public function complete(Request $request, PendingRegistration $pendingRegistration, string $token): View
    {
        $this->ensurePendingRegistrationIsValid($pendingRegistration, $token);

        return view('auth.complete-registration', [
            'pendingRegistration' => $pendingRegistration,
            'token' => $token,
        ]);
    }

    public function save(Request $request, PendingRegistration $pendingRegistration, string $token): RedirectResponse
    {
        $this->ensurePendingRegistrationIsValid($pendingRegistration, $token);

        $request->merge([
            'name' => trim((string) $request->input('name')),
        ]);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'name')->whereNotNull('email_verified_at'),
            ],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ]);

        $user = DB::transaction(function () use ($pendingRegistration, $validated): User {
            $pendingRegistration = PendingRegistration::query()
                ->whereKey($pendingRegistration->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($pendingRegistration->expires_at->isPast()) {
                $pendingRegistration->delete();

                throw ValidationException::withMessages([
                    'email' => __('This registration link has expired. Please request a new one.'),
                ]);
            }

            if (User::query()->where('email', $pendingRegistration->email)->whereNotNull('email_verified_at')->exists()) {
                $pendingRegistration->delete();

                throw ValidationException::withMessages([
                    'email' => __('This email address is already registered.'),
                ]);
            }

            if (User::query()->where('name', $validated['name'])->whereNotNull('email_verified_at')->exists()) {
                throw ValidationException::withMessages([
                    'name' => __('This username is already taken.'),
                ]);
            }

            $legacyUsers = User::query()
                ->whereNull('email_verified_at')
                ->where(function ($query) use ($pendingRegistration, $validated): void {
                    $query
                        ->where('email', $pendingRegistration->email)
                        ->orWhere('name', $validated['name']);
                })
                ->pluck('id');

            if ($legacyUsers->isNotEmpty()) {
                DB::table('sessions')->whereIn('user_id', $legacyUsers)->delete();
                User::query()->whereKey($legacyUsers)->delete();
            }

            $user = User::create([
                'name' => $validated['name'],
                'email' => $pendingRegistration->email,
                'password' => Hash::make($validated['password']),
            ]);

            $user->markEmailAsVerified();
            $pendingRegistration->delete();

            return $user;
        });

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('dashboard', ['verified' => 1]);
    }

    private function ensurePendingRegistrationIsValid(PendingRegistration $pendingRegistration, string $token): void
    {
        if (
            $pendingRegistration->expires_at->isPast()
            || ! hash_equals($pendingRegistration->token_hash, hash('sha256', $token))
        ) {
            abort(403);
        }
    }
}
