<?php

use App\Actions\Localization\UpdateLocalePreference;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

new class extends Component {
    public string $locale = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->locale = app()->getLocale();
    }

    /**
     * Save the dashboard language and reload the page in it (OD-6).
     */
    public function updatedLocale(UpdateLocalePreference $updateLocalePreference): void
    {
        $validated = $this->validate(['locale' => UpdateLocalePreference::rules()]);

        $updateLocalePreference->handle(Auth::user(), $validated['locale']);

        $this->redirect(route('appearance.edit', ['locale' => $validated['locale']]), navigate: true);
    }

    /**
     * Render the component with its page title in the current language.
     */
    public function render(): View
    {
        return $this->view()->title(__('pages.settings.appearance.page_title'));
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('pages.settings.appearance.page_title') }}</flux:heading>

    <x-pages::settings.layout :heading="__('pages.settings.appearance.title')" :subheading="__('pages.settings.appearance.description')">
        <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
            <flux:radio value="light" icon="sun">{{ __('pages.settings.appearance.light') }}</flux:radio>
            <flux:radio value="dark" icon="moon">{{ __('pages.settings.appearance.dark') }}</flux:radio>
            <flux:radio value="system" icon="computer-desktop">{{ __('pages.settings.appearance.system') }}</flux:radio>
        </flux:radio.group>

        <div class="mt-6 max-w-xs">
            <flux:select wire:model.live="locale" :label="__('common.language')" data-test="locale-select">
                @foreach (config('app.supported_locales') as $code => $name)
                    <flux:select.option :value="$code" wire:key="locale-{{ $code }}">{{ $name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </x-pages::settings.layout>
</section>
