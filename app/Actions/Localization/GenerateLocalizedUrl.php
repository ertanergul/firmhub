<?php

namespace App\Actions\Localization;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;

/**
 * Builds the URL of the requested page in a given language (OD-6).
 *
 * The query string is copied as the browser sent it, because parsing it again would
 * rename keys with dots or spaces and drop repeated keys.
 */
class GenerateLocalizedUrl
{
    /**
     * The requested path under the locale prefix.
     */
    public function forPath(Request $request, string $locale): string
    {
        return url($locale.'/'.ltrim($request->path(), '/')).$this->queryString($request);
    }

    /**
     * The requested page in another language.
     *
     * Only path parameters are kept, because route defaults such as Route::view()'s view and
     * status would become query strings. Pages without a locale prefix, and signed URLs whose
     * signature covers the prefix, lead to the home page instead.
     */
    public function forPage(Request $request, string $locale): string
    {
        $route = $request->route();

        if (! $route instanceof Route || $route->getName() === null || ! $route->hasParameter('locale') || $request->query->has('signature')) {
            return route('home', ['locale' => $locale]);
        }

        $parameters = ['locale' => $locale] + Arr::only($route->parameters(), $route->parameterNames());

        return route($route->getName(), $parameters).$this->queryString($request);
    }

    /**
     * The original query string with its leading question mark, if there is one.
     */
    private function queryString(Request $request): string
    {
        $query = $request->server->get('QUERY_STRING');

        return is_string($query) && $query !== '' ? '?'.$query : '';
    }
}
