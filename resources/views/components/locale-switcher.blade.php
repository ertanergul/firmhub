{{-- Links to the same page in another language (OD-6). --}}
@inject('generateLocalizedUrl', \App\Actions\Localization\GenerateLocalizedUrl::class)

<nav {{ $attributes->class('flex items-center gap-1 text-sm') }} aria-label="{{ __('common.language') }}">
    @foreach (config('app.supported_locales') as $code => $name)
        @if ($code === app()->getLocale())
            <span class="rounded-md px-2 py-1 font-medium text-zinc-900 dark:text-white" aria-current="true" lang="{{ $code }}">{{ $name }}</span>
        @else
            <a href="{{ $generateLocalizedUrl->forPage(request(), $code) }}" class="rounded-md px-2 py-1 text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white" hreflang="{{ $code }}" lang="{{ $code }}">{{ $name }}</a>
        @endif
    @endforeach
</nav>
