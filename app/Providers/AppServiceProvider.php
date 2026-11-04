<?php

namespace App\Providers;

use App\Actions\Localization\UpdateLocalePreference;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Events\LocaleUpdated;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use InvalidArgumentException;
use Livewire\Livewire;
use Livewire\Mechanisms\HandleRequests\RequireLivewireHeaders;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->configureLocaleRoutePattern();
        $this->configureLivewireUpdateRoute();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureLocaleUrls();
        $this->configureGates();
    }

    /**
     * Accept only supported locales as the {locale} route prefix, including Fortify's routes (OD-6).
     *
     * Registered before any provider boots, because patterns apply when routes are added.
     */
    protected function configureLocaleRoutePattern(): void
    {
        Route::pattern('locale', implode('|', UpdateLocalePreference::supportedLocales()));
    }

    /**
     * Constrain the Livewire update endpoint to the platform host (0.4 SD-2).
     *
     * Registered before Livewire boots, so Livewire does not add its unconstrained default route.
     */
    protected function configureLivewireUpdateRoute(): void
    {
        Livewire::setUpdateRoute(fn (array $handle, string $path) => Route::post($path, $handle)
            ->middleware(['web', RequireLivewireHeaders::class])
            ->domain(config('app.host')));
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
            : null,
        );
    }

    /**
     * Generate locale-prefixed URLs in the current language (OD-6).
     *
     * Following every locale change keeps links in the same language as the text, including
     * notifications sent in the user's preferred language and requests SetLocale changes.
     * An unsupported default would make every generated link a 404, so it stops the boot,
     * except for the commands that rebuild or clear a cached configuration to fix it.
     */
    protected function configureLocaleUrls(): void
    {
        throw_unless(
            UpdateLocalePreference::isSupported(config('app.locale'))
                || $this->app->runningConsoleCommand('config:cache', 'config:clear', 'optimize', 'optimize:clear'),
            InvalidArgumentException::class,
            'APP_LOCALE must be one of the supported locales: '.implode(', ', UpdateLocalePreference::supportedLocales()).'.',
        );

        URL::defaults(['locale' => app()->getLocale()]);

        Event::listen(function (LocaleUpdated $event): void {
            if (UpdateLocalePreference::isSupported($event->locale)) {
                URL::defaults(['locale' => $event->locale]);
            }
        });
    }

    /**
     * Define the platform-level gates.
     *
     * Platform admins get no implicit tenant permissions, so there is no Gate::before bypass (0.2 §10).
     */
    protected function configureGates(): void
    {
        Gate::define('platform.admin', fn (User $user): bool => $user->is_platform_admin);
    }
}
