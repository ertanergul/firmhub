<?php

namespace App\Providers;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Livewire\Livewire;
use Livewire\Mechanisms\HandleRequests\RequireLivewireHeaders;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->configureLivewireUpdateRoute();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureGates();
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
     * Define the platform-level gates.
     *
     * Platform admins get no implicit tenant permissions, so there is no Gate::before bypass (0.2 §10).
     */
    protected function configureGates(): void
    {
        Gate::define('platform.admin', fn (User $user): bool => $user->is_platform_admin);
    }
}
