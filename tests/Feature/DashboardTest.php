<?php

use App\Enums\CompanyRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Routing\Route;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('users with an unverified email are asked to verify it', function () {
    $this->actingAs(User::factory()->unverified()->create())
        ->get(route('dashboard'))
        ->assertRedirect(route('verification.notice'));
});

test('users without a company are sent to onboarding', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertRedirect(route('onboarding'));
});

test('members see their own company on the dashboard', function (CompanyRole $role) {
    $company = Company::factory()->create(['name' => 'Acme Retail']);
    $otherCompany = Company::factory()->withOwner()->create(['name' => 'Globex Legal']);

    $this->actingAs(memberOf($company, $role))
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Acme Retail')
        ->assertDontSee($otherCompany->name);
})->with(CompanyRole::cases());

test('every dashboard route requires a verified member of a company', function () {
    $routes = collect(app('router')->getRoutes()->getRoutes())
        ->filter(fn (Route $route): bool => str_starts_with($route->uri(), '{locale}/dashboard'));

    expect($routes)->not->toBeEmpty();

    $routes->each(fn (Route $route) => expect(array_values(array_diff(['auth', 'verified', 'company'], $route->gatherMiddleware())))
        ->toBe([], "Route {$route->uri()} is missing a middleware"));
});
