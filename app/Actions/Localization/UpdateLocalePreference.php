<?php

namespace App\Actions\Localization;

use App\Models\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\Rule;

/**
 * Stores a language choice for the visitor and, when signed in, the user (OD-6).
 *
 * The only place that decides which locales are accepted; every input (URL prefix,
 * cookie, form, Livewire property, stored preference) is checked against it.
 */
class UpdateLocalePreference
{
    /**
     * The cookie that remembers the visitor's last language.
     */
    public const COOKIE = 'locale';

    /**
     * The supported locales, such as ['en', 'tr'].
     *
     * @return array<int, string>
     */
    public static function supportedLocales(): array
    {
        return array_map(strval(...), array_keys(config('app.supported_locales')));
    }

    /**
     * Determine whether the value is a supported locale.
     */
    public static function isSupported(mixed $locale): bool
    {
        return is_string($locale) && array_key_exists($locale, config('app.supported_locales'));
    }

    /**
     * Validation rules for a submitted locale.
     *
     * @return array<int, mixed>
     */
    public static function rules(): array
    {
        return ['required', 'string', Rule::in(self::supportedLocales())];
    }

    /**
     * Remember a validated locale for the user and the visitor's next requests.
     */
    public function handle(?User $user, string $locale): void
    {
        $user?->update(['locale' => $locale]);

        Cookie::queue(self::COOKIE, $locale, 60 * 24 * 365);
    }
}
