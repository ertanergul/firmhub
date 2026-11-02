<x-layouts::app :title="__('Dashboard')">
    <div class="flex w-full flex-1 flex-col gap-6">
        <div>
            <flux:heading size="xl" level="1">{{ __('Dashboard') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Welcome, :name.', ['name' => auth()->user()->name]) }}</flux:text>
        </div>

        <flux:separator variant="subtle" />

        <flux:text>{{ __('Your companies and their websites will be managed here.') }}</flux:text>
    </div>
</x-layouts::app>
