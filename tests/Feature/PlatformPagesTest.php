<?php

use App\Models\User;

test('guest platform pages are not indexed', function (string $routeName) {
    $this->get(route($routeName))
        ->assertOk()
        ->assertSee('<meta name="robots" content="noindex, nofollow" />', false);
})->with([
    'home' => 'home',
    'login' => 'login',
    'register' => 'register',
    'forgot password' => 'password.request',
]);

test('authenticated platform pages are not indexed', function (string $routeName) {
    $this->actingAs(User::factory()->create())
        ->get(route($routeName))
        ->assertOk()
        ->assertSee('<meta name="robots" content="noindex, nofollow" />', false);
})->with([
    'dashboard' => 'dashboard',
    'profile settings' => 'profile.edit',
]);

test('the home page offers login and registration to guests', function () {
    $this->get(route('home'))
        ->assertSee(route('login'), false)
        ->assertSee(route('register'), false)
        ->assertDontSee(route('dashboard'), false);
});

test('the home page links authenticated users to the dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('home'))
        ->assertSee(route('dashboard'), false)
        ->assertDontSee(route('login'), false);
});

test('the dashboard shows the firmhub brand without starter kit links', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertSee(config('app.name'))
        ->assertDontSee('github.com/laravel', false)
        ->assertDontSee('laravel.com/docs', false);
});
