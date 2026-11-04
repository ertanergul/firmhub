<?php

namespace App\Http\Controllers;

use App\Actions\Localization\GenerateLocalizedUrl;
use App\Actions\Localization\UpdateLocalePreference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RedirectToLocalizedPathController extends Controller
{
    /**
     * Send an unprefixed platform URL to the same path in the request language (OD-6).
     *
     * The language comes from SetLocale: the user's preference, the visitor's last choice
     * or the default. Paths that already start with a supported locale were not found, and
     * file paths (such as /wp-login.php or /.env, usually probes) are no pages to redirect to.
     */
    public function __invoke(Request $request, GenerateLocalizedUrl $generateLocalizedUrl): RedirectResponse
    {
        abort_if(
            UpdateLocalePreference::isSupported($request->segment(1)) || str_contains((string) last($request->segments()), '.'),
            404,
        );

        return redirect()->to($generateLocalizedUrl->forPath($request, app()->getLocale()));
    }
}
