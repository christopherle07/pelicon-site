<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffTwoFactorIsEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->isStaff() && ! $user->hasConfirmedTwoFactor()) {
            return redirect()
                ->route('profile.show')
                ->with('status', 'staff-two-factor-required');
        }

        return $next($request);
    }
}
