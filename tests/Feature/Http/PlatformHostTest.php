<?php

use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

test('platform pages are not reachable on tenant hosts', function (string $method, string $path) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->call($method, "http://acme.localhost{$path}");

    $response->assertNotFound();
})->with([
    'home' => ['GET', '/'],
    'login page' => ['GET', '/login'],
    'login submission' => ['POST', '/login'],
    'dashboard' => ['GET', '/dashboard'],
    'profile settings' => ['GET', '/settings/profile'],
]);

test('the livewire update endpoint only matches the platform host', function () {
    $path = parse_url(route('livewire.update'), PHP_URL_PATH);
    $routes = app('router')->getRoutes();

    expect($routes->match(Request::create("http://localhost{$path}", 'POST'))->getName())->toBe('livewire.update');

    expect(fn () => $routes->match(Request::create("http://acme.localhost{$path}", 'POST')))
        ->toThrow(NotFoundHttpException::class);
});

test('platform pages are reachable on the platform host', function () {
    $user = User::factory()->create();

    $this->get('http://localhost/login')->assertOk();
    $this->actingAs($user)->get('http://localhost/dashboard')->assertOk();
});

test('the health check is reachable on tenant hosts', function () {
    $this->get('http://acme.localhost/up')->assertOk();
});
