<?php

use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

test('platform pages are not reachable on tenant hosts', function (string $method, string $path) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->call($method, "http://acme.localhost{$path}");

    $response->assertNotFound();
})->with([
    'root' => ['GET', '/'],
    'home' => ['GET', '/en'],
    'login page' => ['GET', '/en/login'],
    'login submission' => ['POST', '/en/login'],
    'dashboard' => ['GET', '/en/dashboard'],
    'profile settings' => ['GET', '/en/settings/profile'],
    'unprefixed path' => ['GET', '/dashboard'],
]);

test('the livewire update endpoint only matches the platform host', function () {
    $path = parse_url(route('livewire.update'), PHP_URL_PATH);
    $routes = app('router')->getRoutes();

    expect($routes->match(Request::create("http://localhost{$path}", 'POST'))->getName())->toBe('livewire.update');

    expect(fn () => $routes->match(Request::create("http://acme.localhost{$path}", 'POST')))
        ->toThrow(NotFoundHttpException::class);
});

test('platform pages are reachable on the platform host', function () {
    $user = companyOwner();

    $this->get('http://localhost/en/login')->assertOk();
    $this->actingAs($user)->get('http://localhost/en/dashboard')->assertOk();
});

test('the health check is reachable on tenant hosts', function () {
    $this->get('http://acme.localhost/up')->assertOk();
});

test('the platform host is kept in lower case', function () {
    $server = $_SERVER;
    $environment = $_ENV;
    $_SERVER['APP_URL'] = $_ENV['APP_URL'] = 'https://FirmHub.Test';

    try {
        expect((require config_path('app.php'))['host'])->toBe('firmhub.test');
    } finally {
        $_SERVER = $server;
        $_ENV = $environment;
    }
});
