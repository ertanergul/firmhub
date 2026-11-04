<x-layouts::app :title="__('common.dashboard')">
    <div class="flex w-full flex-1 flex-col gap-6">
        <div>
            <flux:heading size="xl" level="1">{{ __('common.dashboard') }}</flux:heading>
            <flux:text class="mt-2">{{ __('pages.dashboard.welcome', ['name' => auth()->user()->name]) }}</flux:text>
        </div>

        <flux:separator variant="subtle" />

        <flux:text>{{ __('pages.dashboard.description') }}</flux:text>
    </div>
</x-layouts::app>
