<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectUnverifiedUsers
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->hasVerifiedEmail() || $this->isVerificationRoute($request)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return redirect()->route('verification.notice');
    }

    private function isVerificationRoute(Request $request): bool
    {
        return $request->routeIs(
            'verification.notice',
            'verification.verify',
            'verification.send',
            'logout',
        );
    }
}
