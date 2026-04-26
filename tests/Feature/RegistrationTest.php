<?php

namespace Tests\Feature;

use App\Models\PendingRegistration;
use App\Models\User;
use App\Notifications\CompleteRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Laravel\Jetstream\Jetstream;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->get('/register')
            ->assertStatus(200);
    }

    public function test_registration_request_sends_a_completion_link_without_creating_a_user(): void
    {
        Notification::fake();

        $this->from('/register')
            ->post('/register', [
                'email' => ' Test@Example.com ',
            ])
            ->assertRedirect('/register')
            ->assertSessionHas('status', 'registration-link-sent');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com',
        ]);

        $pendingRegistration = PendingRegistration::query()->firstOrFail();

        $this->assertSame('test@example.com', $pendingRegistration->email);
        $this->assertTrue($pendingRegistration->expires_at->isFuture());
        Notification::assertSentOnDemand(CompleteRegistration::class);
    }

    public function test_registration_request_cannot_use_a_verified_email(): void
    {
        Notification::fake();

        User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        $this->from('/register')
            ->post('/register', [
                'email' => 'test@example.com',
            ])
            ->assertRedirect('/register')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertSame(0, PendingRegistration::query()->count());
        Notification::assertNothingSent();
    }

    public function test_duplicate_registration_request_replaces_the_previous_pending_link(): void
    {
        Notification::fake();

        $this->post('/register', [
            'email' => 'test@example.com',
        ]);

        $firstPendingRegistration = PendingRegistration::query()->firstOrFail();

        $this->post('/register', [
            'email' => 'test@example.com',
        ]);

        $secondPendingRegistration = PendingRegistration::query()->firstOrFail();

        $this->assertSame(1, PendingRegistration::query()->count());
        $this->assertSame('test@example.com', $secondPendingRegistration->email);
        $this->assertNotSame($firstPendingRegistration->token_hash, $secondPendingRegistration->token_hash);
        Notification::assertSentOnDemandTimes(CompleteRegistration::class, 2);
    }

    public function test_registration_completion_screen_can_be_rendered(): void
    {
        [$pendingRegistration, $token] = $this->pendingRegistration();

        $this->get($this->completionUrl($pendingRegistration, $token))
            ->assertStatus(200)
            ->assertSee('test@example.com');
    }

    public function test_registration_completion_creates_a_verified_user(): void
    {
        [$pendingRegistration, $token] = $this->pendingRegistration();

        $response = $this->post($this->completionUrl($pendingRegistration, $token), [
            'name' => 'Test User',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
        ]);

        $user = User::query()->where('email', 'test@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertSame('Test User', $user->name);
        $this->assertTrue($user->hasVerifiedEmail());
        $this->assertSame(0, PendingRegistration::query()->count());
        $response->assertRedirect(route('dashboard', ['verified' => 1], absolute: false));
    }

    public function test_registration_completion_cannot_use_a_verified_username(): void
    {
        User::factory()->create([
            'name' => 'Chris',
            'email_verified_at' => now(),
        ]);

        [$pendingRegistration, $token] = $this->pendingRegistration();

        $this->from($this->completionUrl($pendingRegistration, $token))
            ->post($this->completionUrl($pendingRegistration, $token), [
                'name' => 'Chris',
                'password' => 'password',
                'password_confirmation' => 'password',
                'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
            ])
            ->assertRedirect($this->completionUrl($pendingRegistration, $token))
            ->assertSessionHasErrors('name');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com',
        ]);
        $this->assertSame(1, PendingRegistration::query()->count());
    }

    public function test_registration_completion_reclaims_a_legacy_unverified_user(): void
    {
        $legacyUser = User::factory()->unverified()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        [$pendingRegistration, $token] = $this->pendingRegistration();

        $this->post($this->completionUrl($pendingRegistration, $token), [
            'name' => 'Test User',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
        ]);

        $user = User::query()->where('email', 'test@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertTrue($user->hasVerifiedEmail());
        $this->assertFalse(User::query()->whereKey($legacyUser->id)->exists());
        $this->assertNotSame($legacyUser->id, $user->id);
        $this->assertSame(1, User::query()->where('email', 'test@example.com')->count());
        $this->assertSame(0, PendingRegistration::query()->count());
    }

    public function test_registration_completion_rejects_invalid_tokens(): void
    {
        [$pendingRegistration] = $this->pendingRegistration();

        $this->get($this->completionUrl($pendingRegistration, 'wrong-token'))
            ->assertForbidden();

        $this->assertGuest();
        $this->assertSame(0, User::query()->count());
        $this->assertSame(1, PendingRegistration::query()->count());
    }

    /**
     * @return array{PendingRegistration, string}
     */
    private function pendingRegistration(): array
    {
        $token = 'test-registration-token';

        return [
            PendingRegistration::create([
                'email' => 'test@example.com',
                'token_hash' => hash('sha256', $token),
                'expires_at' => now()->addMinutes(60),
            ]),
            $token,
        ];
    }

    private function completionUrl(PendingRegistration $pendingRegistration, string $token): string
    {
        return URL::temporarySignedRoute(
            'register.complete',
            $pendingRegistration->expires_at,
            [
                'pendingRegistration' => $pendingRegistration,
                'token' => $token,
            ],
        );
    }
}
