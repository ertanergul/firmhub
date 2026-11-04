<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <main class="flex min-h-svh flex-col items-center justify-center gap-8 p-6 text-center md:p-10">
            <div class="flex flex-col items-center gap-3">
                <span class="flex size-12 items-center justify-center rounded-lg bg-accent-content text-accent-foreground">
                    <x-app-logo-icon class="size-7 fill-current text-white dark:text-black" />
                </span>
                <flux:heading size="xl" level="1">{{ config('app.name') }}</flux:heading>
            </div>

            <div class="flex max-w-md flex-col gap-2">
                <flux:text size="lg">{{ __('pages.welcome.tagline') }}</flux:text>
            </div>

            <nav class="flex flex-wrap items-center justify-center gap-3" aria-label="{{ __('common.account') }}">
                @auth
                    <flux:button variant="primary" :href="route('dashboard')">{{ __('common.dashboard') }}</flux:button>
                @else
                    <flux:button variant="primary" :href="route('login')">{{ __('common.log_in') }}</flux:button>

                    @if (Route::has('register'))
                        <flux:button :href="route('register')">{{ __('common.register') }}</flux:button>
                    @endif
                @endauth
            </nav>

            <x-locale-switcher />
        </main>

        @fluxScripts
    </body>
</html>
