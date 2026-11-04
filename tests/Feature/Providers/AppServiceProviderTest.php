<?php

use App\Providers\AppServiceProvider;

test('an unsupported default language stops the application from booting', function () {
    config(['app.locale' => 'de']);

    expect(fn () => app()->getProvider(AppServiceProvider::class)->boot())
        ->toThrow(InvalidArgumentException::class, 'APP_LOCALE must be one of the supported locales: en, tr.');
});

test('the configuration commands still run with an unsupported default language', function (string $command) {
    config(['app.locale' => 'de']);

    $arguments = $_SERVER['argv'];
    $_SERVER['argv'] = ['artisan', $command];

    try {
        expect(fn () => app()->getProvider(AppServiceProvider::class)->boot())->not->toThrow(InvalidArgumentException::class);
    } finally {
        $_SERVER['argv'] = $arguments;
    }
})->with(['config:cache', 'config:clear', 'optimize', 'optimize:clear']);
