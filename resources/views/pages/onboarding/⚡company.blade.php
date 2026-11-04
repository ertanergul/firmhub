<?php

use App\Actions\Company\CreateCompany;
use App\Models\Industry;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::auth')] class extends Component {
    public string $name = '';

    public string $industry = '';

    /**
     * Send users who already have a company on to the next onboarding step or their dashboard.
     */
    public function mount(): void
    {
        if (Auth::user()->company !== null) {
            $this->redirectRoute('dashboard', navigate: true);
        }
    }

    /**
     * The industries offered during onboarding, in display order.
     *
     * @return Collection<int, Industry>
     */
    #[Computed]
    public function industries(): Collection
    {
        return Industry::query()->offered()->get();
    }

    /**
     * Create the company and make the signed-in user its Owner (0.1 FR-COMP-01).
     */
    public function createCompany(CreateCompany $createCompany): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'industry' => ['required', 'string', Rule::exists('industries', 'slug')->where('is_active', true)],
        ]);

        $createCompany->handle(
            Auth::user(),
            $validated['name'],
            Industry::query()->where('slug', $validated['industry'])->firstOrFail(),
        );

        $this->redirectRoute('onboarding.details', navigate: true);
    }

    /**
     * Render the component with its page title in the current language.
     */
    public function render(): View
    {
        return $this->view()->title(__('pages.onboarding.company.page_title'));
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('pages.onboarding.company.title')" :description="__('pages.onboarding.company.description')" />

    <form wire:submit="createCompany" class="flex flex-col gap-6">
        <flux:input
            wire:model="name"
            :label="__('pages.company.fields.name')"
            type="text"
            required
            autofocus
            autocomplete="organization"
        />

        <flux:select wire:model="industry" :label="__('pages.company.fields.industry')" :placeholder="__('pages.company.fields.industry_placeholder')" required>
            @foreach ($this->industries as $industry)
                <flux:select.option :value="$industry->slug" wire:key="industry-{{ $industry->slug }}">{{ $industry->label() }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:error name="company" />

        <flux:button variant="primary" type="submit" class="w-full" data-test="create-company-button">
            {{ __('pages.onboarding.company.submit') }}
        </flux:button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="text-center">
        @csrf
        <flux:button variant="ghost" type="submit" class="text-sm cursor-pointer" data-test="logout-button">
            {{ __('common.log_out') }}
        </flux:button>
    </form>
</div>
