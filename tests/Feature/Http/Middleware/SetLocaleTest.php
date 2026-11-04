<?php

use App\Actions\Localization\UpdateLocalePreference;
use App\Models\User;
use Illuminate\Support\Facades\Route;

test('pages are rendered in the language of their url prefix', function (string $locale, string $text) {
    $this->withHeaders(pageOpenedFrom('none'))
        ->get(route('home', ['locale' => $locale]))
        ->assertOk()
        ->assertSee('<html lang="'.$locale.'"', false)
        ->assertSee($text)
        ->assertCookie(UpdateLocalePreference::COOKIE, $locale);
})->with([
    'english' => ['en', 'Create, edit and publish your company website from one dashboard.'],
    'turkish' => ['tr', 'Şirketinizin web sitesini tek bir panelden oluşturun, düzenleyin ve yayınlayın.'],
]);

test('the language switcher links to the same page in the other language', function () {
    $this->get(route('home', ['locale' => 'en']).'?utm.source=ad&tag=a&tag=b')
        ->assertOk()
        ->assertSee('href="'.e(route('home', ['locale' => 'tr']).'?utm.source=ad&tag=a&tag=b').'"', false);
});

test('the language switcher leads signed urls to the home page', function () {
    $this->get(route('password.reset', ['locale' => 'en', 'token' => 'token']).'?signature=abc')
        ->assertOk()
        ->assertSee('href="'.route('home', ['locale' => 'tr']).'"', false);
});

test('pages under unsupported prefixes are not found', function () {
    $this->get('/de/login')->assertRedirect('/en/de/login');
    $this->get('/en/de/login')->assertNotFound();
});

test('unknown file paths are not found without a redirect', function (string $path) {
    $this->get($path)
        ->assertNotFound()
        ->assertCookieMissing(UpdateLocalePreference::COOKIE);
})->with(['/wp-login.php', '/.env']);

test('the root redirects to the default language without a previous choice', function () {
    $this->get(route('root'))->assertRedirect(route('home', ['locale' => 'en']));
});

test('unprefixed paths redirect to the visitor\'s last language with their query string', function () {
    $this->withCookie(UpdateLocalePreference::COOKIE, 'tr')
        ->get('/login?utm.source=email&tag=a&tag=b')
        ->assertRedirect('/tr/login?utm.source=email&tag=a&tag=b');
});

test('unsupported stored languages fall back to the default language', function () {
    $this->withCookie(UpdateLocalePreference::COOKIE, 'xx')
        ->get('/login')
        ->assertRedirect('/en/login');
});

test('unprefixed paths redirect signed-in users to their stored language', function () {
    $user = User::factory()->create(['locale' => 'en']);

    $this->actingAs($user)
        ->withCookie(UpdateLocalePreference::COOKIE, 'tr')
        ->get('/dashboard')
        ->assertRedirect('/en/dashboard');
});

test('opening a page in another language stores it as the user\'s preference', function (array $headers) {
    $user = User::factory()->create(['locale' => 'tr']);

    $this->actingAs($user)
        ->withHeaders($headers)
        ->get(route('dashboard', ['locale' => 'en']))
        ->assertSee('Welcome, '.$user->name)
        ->assertCookie(UpdateLocalePreference::COOKIE, 'en');

    expect($user->refresh()->locale)->toBe('en');
})->with([
    'typed or bookmarked' => [pageOpenedFrom('none')],
    'link on the platform' => [pageOpenedFrom('same-origin')],
    'prerendered from the address bar' => [[...pageOpenedFrom('none'), 'Sec-Purpose' => 'prefetch;prerender']],
]);

test('opening a page stores its language for the user even when the visitor already chose it', function () {
    $user = User::factory()->create(['locale' => 'tr']);

    $this->actingAs($user)
        ->withCookie(UpdateLocalePreference::COOKIE, 'en')
        ->withHeaders(pageOpenedFrom('none'))
        ->get(route('dashboard', ['locale' => 'en']))
        ->assertOk();

    expect($user->refresh()->locale)->toBe('en');
});

test('pages not opened by the user show the language without storing it', function (array $headers) {
    $user = User::factory()->create(['locale' => 'tr']);

    $this->actingAs($user)
        ->withHeaders($headers)
        ->get(route('dashboard', ['locale' => 'en']))
        ->assertSee('Welcome, '.$user->name)
        ->assertCookieMissing(UpdateLocalePreference::COOKIE);

    expect($user->refresh()->locale)->toBe('tr');
})->with([
    'link on another site' => [pageOpenedFrom('cross-site')],
    'link on a tenant subdomain' => [pageOpenedFrom('same-site')],
    'no fetch metadata' => [[]],
    'prefetch' => [[...pageOpenedFrom('none'), 'Sec-Purpose' => 'prefetch']],
    'prerendered from a link' => [[...pageOpenedFrom('same-origin'), 'Sec-Purpose' => 'prefetch;prerender']],
    'script request' => [['Sec-Fetch-Site' => 'same-origin', 'Sec-Fetch-Mode' => 'cors', 'Sec-Fetch-Dest' => 'empty']],
]);

test('links from other sites keep the visitor\'s last language', function () {
    $this->withCookie(UpdateLocalePreference::COOKIE, 'tr')
        ->withHeaders(pageOpenedFrom('cross-site'))
        ->get(route('login', ['locale' => 'en']))
        ->assertOk()
        ->assertCookieMissing(UpdateLocalePreference::COOKIE);

    $this->withCookie(UpdateLocalePreference::COOKIE, 'tr')
        ->get('/login')
        ->assertRedirect('/tr/login');
});

test('livewire updates answer in the page\'s language without storing it', function () {
    $user = User::factory()->create(['locale' => 'en']);

    $page = $this->actingAs($user)->get(route('profile.edit', ['locale' => 'tr']));
    preg_match('/wire:snapshot="([^"]+)"/', $page->getContent(), $snapshot);

    $response = $this->actingAs($user)
        ->withHeader('X-Livewire', '1')
        ->postJson(route('livewire.update'), ['components' => [[
            'snapshot' => html_entity_decode($snapshot[1]),
            'updates' => ['name' => ''],
            'calls' => [['method' => 'updateProfileInformation', 'params' => [], 'metadata' => []]],
        ]]]);

    $response->assertOk()->assertCookieMissing(UpdateLocalePreference::COOKIE);

    expect($response->json('components.0.effects.html'))->toContain('Ad alanı zorunludur.')
        ->and($user->refresh()->locale)->toBe('en');
});

test('tenant sites are left to their own language handling', function () {
    Route::domain('{tenant}.localhost')->middleware('web')->get('tr/about', fn (): string => app()->getLocale());

    $user = User::factory()->create(['locale' => 'tr']);

    $this->actingAs($user)
        ->withHeaders(pageOpenedFrom('none'))
        ->get('http://acme.localhost/tr/about')
        ->assertContent('en')
        ->assertCookieMissing(UpdateLocalePreference::COOKIE);
});
