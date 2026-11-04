<?php

use App\Actions\Localization\UpdateLocalePreference;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;

/**
 * Translation keys used in views and application code: __('…') and @lang('…').
 *
 * @return array<int, string>
 */
function usedTranslationKeys(): array
{
    $keys = [];

    foreach (['resources/views', 'app'] as $directory) {
        foreach (File::allFiles(base_path($directory)) as $file) {
            preg_match_all("/(?:__|@lang)\(\s*'((?:[^'\\\\]|\\\\.)*)'/", $file->getContents(), $matches);

            array_push($keys, ...array_map(fn (string $key): string => str_replace("\\'", "'", $key), $matches[1]));
        }
    }

    return array_values(array_unique($keys));
}

/**
 * The language files in lang/en, which every other language folder mirrors.
 *
 * @return array<int, string>
 */
function englishLanguageFiles(): array
{
    return array_map(fn (string $path): string => basename($path, '.php'), glob(dirname(__DIR__, 2).'/lang/en/*.php'));
}

test('every translation key used in the application is defined in english', function () {
    $undefined = array_filter(usedTranslationKeys(), fn (string $key): bool => ! Lang::has($key, 'en', false));

    expect(array_values($undefined))->toBe([]);
});

test('every language folder defines the same lines as the english one', function (string $file) {
    $lines = fn (string $locale): array => array_keys(array_filter(
        Arr::dot(require lang_path("{$locale}/{$file}.php")),
        fn (mixed $line): bool => $line !== [],
    ));

    foreach (UpdateLocalePreference::supportedLocales() as $locale) {
        expect(array_values(array_diff($lines('en'), $lines($locale))))->toBe([], "lang/{$locale}/{$file}.php is missing lines");
    }
})->with(englishLanguageFiles());

test('strings that packages pass by their english text are translated from the json files', function (string $english, array $replace, string $turkish) {
    expect(__($english, $replace, 'tr'))->toBe($turkish)
        ->and(__($english, $replace, 'en'))->toBe(strtr($english, [':count' => (string) ($replace['count'] ?? '')]));
})->with([
    'laravel error page' => ['Not Found', [], 'Bulunamadı'],
    'laravel email with a replacement' => ['This password reset link will expire in :count minutes.', ['count' => 60], 'Bu şifre sıfırlama bağlantısının süresi 60 dakika içinde dolacak.'],
    'fortify message' => ['The provided password was incorrect.', [], 'Girilen şifre hatalı.'],
    'flux label' => ['Toggle password visibility', [], 'Şifreyi göster/gizle'],
]);

test('page titles are translated where they are set', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('profile.edit', ['locale' => 'tr']))
        ->assertSee('Profil ayarları - '.config('app.name'));
});

test('page titles are shown as given, even when they match a language file', function () {
    $this->blade('<x-layouts::auth title="pages">content</x-layouts::auth>')
        ->assertSee('pages - '.config('app.name'));
});

test('unknown strings are shown as they are', function () {
    expect(__('Some unknown text.', [], 'tr'))->toBe('Some unknown text.');
});

test('error pages under a locale prefix are shown in that language without storing it', function () {
    $this->withCookie(UpdateLocalePreference::COOKIE, 'en')
        ->withHeaders(pageOpenedFrom('none'))
        ->get('/tr/missing-page')
        ->assertNotFound()
        ->assertSee('Bulunamadı')
        ->assertCookieMissing(UpdateLocalePreference::COOKIE);
});

test('every language translates the strings packages render', function () {
    $required = array_filter(
        packageSourceStrings(),
        fn (string $string): bool => ! preg_match('/^[a-z_]+(\.[a-z_]+)+$/', $string) && ! in_array($string, unusedPackageStrings(), true),
    );

    foreach (array_diff(UpdateLocalePreference::supportedLocales(), ['en']) as $locale) {
        $path = lang_path("{$locale}.json");

        expect(File::exists($path))->toBeTrue("lang/{$locale}.json is missing");

        $translated = jsonTranslationKeys($path);
        $missing = array_filter($required, fn (string $string): bool => ! collect($translated)->contains(
            fn (string $key): bool => isPackageString($key, $string),
        ));

        expect(array_values($missing))->toBe([], "lang/{$locale}.json is missing package strings");
    }
});

test('the json files only translate strings the packages still render', function () {
    foreach (File::glob(lang_path('*.json')) as $path) {
        $stale = array_filter(
            jsonTranslationKeys($path),
            fn (string $key): bool => ! collect(packageSourceStrings())->contains(fn (string $string): bool => isPackageString($key, $string)),
        );

        expect(array_values($stale))->toBe([], basename($path).' translates strings no package renders');
    }
});

/**
 * The strings Laravel, Fortify and Flux pass to __() in the views and classes FirmHub uses, read once.
 *
 * @return array<int, string>
 */
function packageSourceStrings(): array
{
    static $strings = null;

    if ($strings !== null) {
        return $strings;
    }

    $sources = [
        'vendor/laravel/framework/src/Illuminate/Auth/Notifications',
        'vendor/laravel/framework/src/Illuminate/Notifications/resources/views',
        'vendor/laravel/framework/src/Illuminate/Mail/resources/views',
        'vendor/laravel/framework/src/Illuminate/Foundation/Exceptions/views',
        'vendor/laravel/fortify/src',
        'vendor/livewire/flux/stubs/resources/views/flux',
    ];

    $strings = [];

    foreach ($sources as $directory) {
        foreach (File::allFiles(base_path($directory)) as $file) {
            preg_match_all('/(?:__|@lang|Lang::get|trans)\(\s*(?:\'((?:\\\\.|[^\'\\\\])*)\'|"((?:\\\\.|[^"\\\\])*)")/', $file->getContents(), $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $strings[] = ($match[2] ?? '') !== '' ? stripcslashes($match[2]) : str_replace("\\'", "'", $match[1]);
            }
        }
    }

    return $strings = array_values(array_unique($strings));
}

/**
 * The keys of a JSON translation file: the English strings it translates.
 *
 * @return array<int, string>
 */
function jsonTranslationKeys(string $path): array
{
    return array_keys(json_decode(File::get($path), true, flags: JSON_THROW_ON_ERROR));
}

/**
 * Determine whether a translation key is the given package string.
 *
 * A string ending in a line break is the first of several literals the package joins
 * (the mail button fallback), so the key only has to start with it.
 */
function isPackageString(string $key, string $string): bool
{
    return $key === $string || (str_ends_with($string, "\n") && str_starts_with($key, $string));
}

/**
 * Package strings of components and rules FirmHub does not use yet; translate them once they are used.
 *
 * @return array<int, string>
 */
function unusedPackageStrings(): array
{
    return [
        // Flux file upload, clearable, copyable and search inputs, and pagination.
        'Choose file',
        'Choose files',
        'No file chosen',
        'files',
        'Clear input',
        'Copy to clipboard',
        'Search...',
        'Showing',
        'to',
        'of',
        'results',
        // Fortify's password rule; the application uses Laravel's Password rule (lang/{locale}/validation.php).
        'The :attribute must be at least :length characters.',
        'The :attribute must be at least :length characters and contain at least one uppercase character.',
        'The :attribute must be at least :length characters and contain at least one number.',
        'The :attribute must be at least :length characters and contain at least one special character.',
        'The :attribute must be at least :length characters and contain at least one uppercase character and one number.',
        'The :attribute must be at least :length characters and contain at least one uppercase character and one special character.',
        'The :attribute must be at least :length characters and contain at least one uppercase character, one number, and one special character.',
        'The :attribute must be at least :length characters and contain at least one special character and one number.',
    ];
}
