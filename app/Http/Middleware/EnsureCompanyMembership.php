<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Requires a company for the company dashboard (0.2 AR-1).
 *
 * During the proof of concept the company is the user's only membership
 * (DECISIONS 2026-11-04); users without one are sent to onboarding. The company
 * is never taken from the request: pages and Livewire actions read it from
 * the signed-in user and authorize every action with CompanyPolicy.
 */
class EnsureCompanyMembership
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->company === null) {
            return redirect()->route('onboarding');
        }

        return $next($request);
    }
}
