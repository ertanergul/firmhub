<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies named rate limiters to Fortify routes that Fortify itself does not throttle.
 */
class ThrottleAuthenticationRequests
{
    /**
     * Rate limiter names keyed by the route name they protect.
     *
     * @var array<string, string>
     */
    private const LIMITERS = [
        'register.store' => 'register',
        'password.email' => 'password-reset-link',
    ];

    public function __construct(private ThrottleRequests $throttle) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();
        $limiter = $routeName !== null ? (self::LIMITERS[$routeName] ?? null) : null;

        if ($limiter === null) {
            return $next($request);
        }

        return $this->throttle->handle($request, $next, $limiter);
    }
}
