<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sends members who may complete onboarding back to it until it is done (0.1 FR-COMP-03).
 *
 * Runs after EnsureCompanyMembership. Roles without the permission (Editor, Viewer)
 * see the dashboard even while onboarding is incomplete.
 */
class EnsureOnboardingCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $company = $user?->company;

        if ($company !== null && $company->onboarding_completed_at === null && $user->can('completeOnboarding', $company)) {
            return redirect()->route('onboarding.details');
        }

        return $next($request);
    }
}
