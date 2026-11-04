<?php

namespace App\Http\Middleware;

use App\Actions\Localization\UpdateLocalePreference;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Selects the platform request language (OD-6): the URL prefix, then the signed-in user's
 * preference, then the visitor's last choice, then the default locale.
 *
 * Opening a page in another language stores that language as the user's preference
 * and the visitor's last choice, unless the page was reached from another site.
 * Tenant sites on other hosts are left to their own language handling.
 */
class SetLocale
{
    public function __construct(private UpdateLocalePreference $updateLocalePreference) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->getHost() !== config('app.host')) {
            return $next($request);
        }

        $urlLocale = $request->route('locale');
        $user = $request->user();

        // A path under a locale prefix that matches no page (a 404) is still shown in that language.
        $locale = collect([$urlLocale ?? $request->segment(1), $user?->locale, $request->cookie(UpdateLocalePreference::COOKIE)])
            ->first(fn (mixed $candidate): bool => UpdateLocalePreference::isSupported($candidate), config('app.locale'));

        App::setLocale($locale);

        // Fortify redirects to this path after login, registration and email verification.
        config(['fortify.home' => route('dashboard', absolute: false)]);

        if ($urlLocale !== null && $this->isPageOpenedByUser($request) && $this->isNewChoice($request, $user, $locale)) {
            $this->updateLocalePreference->handle($user, $locale);
        }

        return $next($request);
    }

    /**
     * Determine whether the language differs from the one stored for the user or the visitor.
     */
    private function isNewChoice(Request $request, ?User $user, string $locale): bool
    {
        return ($user !== null && $user->locale !== $locale)
            || $request->cookie(UpdateLocalePreference::COOKIE) !== $locale;
    }

    /**
     * Determine whether the user opened the page from the address bar, a bookmark or
     * one of our own links.
     *
     * A link on another site (including tenant subdomains, which are same-site),
     * prefetches, and requests made by scripts (Livewire included) do not count. A
     * prerender started from the address bar does, because Chrome shows that page
     * without a new request when the user presses Enter. Links opened from email or
     * chat apps cannot be told apart from the address bar and count as well.
     */
    private function isPageOpenedByUser(Request $request): bool
    {
        $fetchSite = $request->headers->get('Sec-Fetch-Site');
        $purpose = $request->headers->get('Sec-Purpose');

        return $request->isMethod('GET')
            && in_array($fetchSite, ['none', 'same-origin'], true)
            && $request->headers->get('Sec-Fetch-Mode') === 'navigate'
            && $request->headers->get('Sec-Fetch-Dest') === 'document'
            && ($purpose === null || ($fetchSite === 'none' && str_contains($purpose, 'prerender')));
    }
}
