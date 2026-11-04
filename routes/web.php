<?php

use App\Http\Controllers\RedirectToLocalizedPathController;
use Illuminate\Support\Facades\Route;

Route::domain(config('app.host'))->group(function () {
    Route::prefix('{locale}')->group(function () {
        Route::view('/', 'welcome')->name('home');

        Route::middleware(['auth', 'verified'])->group(function () {
            Route::livewire('onboarding', 'pages::onboarding.company')->name('onboarding');

            Route::middleware('company')->group(function () {
                Route::livewire('onboarding/details', 'pages::onboarding.details')->name('onboarding.details');

                Route::middleware('onboarded')->group(function () {
                    Route::view('dashboard', 'dashboard')->name('dashboard');
                });
            });
        });

        require __DIR__.'/settings.php';
    });

    Route::get('/', RedirectToLocalizedPathController::class)->name('root');

    Route::fallback(RedirectToLocalizedPathController::class);
});
