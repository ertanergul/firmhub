<?php

use App\Actions\Company\UpdateCompanyProfile;
use App\Models\Company;
use App\Models\Industry;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public string $name = '';

    public string $industry = '';

    public string $established_year = '';

    public string $contact_email = '';

    public string $phone = '';

    public string $whatsapp_phone = '';

    public string $address_line1 = '';

    public string $address_line2 = '';

    public string $region = '';

    public string $city = '';

    public string $postal_code = '';

    public string $legal_name = '';

    public string $tax_number = '';

    public string $tax_office = '';

    public string $trade_registry_number = '';

    /**
     * Fill the form from the signed-in user's company.
     */
    public function mount(): void
    {
        $company = $this->company();

        $this->authorize('view', $company);

        $location = $company->primaryLocation;

        $this->name = $company->name;
        $this->industry = $company->industry->slug;
        $this->established_year = (string) $company->established_year;
        $this->contact_email = $company->contact_email;
        $this->phone = $company->phone ?? '';
        $this->whatsapp_phone = $company->whatsapp_phone ?? '';
        $this->address_line1 = $location->address_line1 ?? '';
        $this->address_line2 = $location->address_line2 ?? '';
        $this->region = $location->region ?? '';
        $this->city = $location->city ?? '';
        $this->postal_code = $location->postal_code ?? '';
        $this->legal_name = $company->legal_name ?? '';
        $this->tax_number = $company->tax_number ?? '';
        $this->tax_office = $company->tax_office ?? '';
        $this->trade_registry_number = $company->trade_registry_number ?? '';
    }

    /**
     * Whether the signed-in user may change the company details (Owner and Admin).
     */
    #[Computed]
    public function canUpdate(): bool
    {
        return Auth::user()->can('update', $this->company());
    }

    /**
     * The industries a company can choose: the offered ones and its current one.
     *
     * @return Collection<int, Industry>
     */
    #[Computed]
    public function industries(): Collection
    {
        $company = $this->company();

        return Industry::query()
            ->where(fn ($query) => $query->where('is_active', true)->orWhere('id', $company->industry_id))
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Save the company details (0.2 §7.1: Owner and Admin).
     */
    public function save(UpdateCompanyProfile $updateCompanyProfile): void
    {
        $company = $this->company();

        $this->authorize('update', $company);

        $validated = $this->validate([...UpdateCompanyProfile::profileRules($company), ...UpdateCompanyProfile::contactRules()]);

        $industry = null;

        if ($validated['industry'] !== $company->industry->slug) {
            $this->authorize('changeIndustry', $company);

            $industry = Industry::query()->where('slug', $validated['industry'])->firstOrFail();
        }

        $details = array_map(fn (mixed $value): mixed => $value === '' ? null : $value, Arr::except($validated, 'industry'));

        $updateCompanyProfile->handle($company, $details, $industry);

        Flux::toast(variant: 'success', text: __('pages.company.profile.saved'));
    }

    /**
     * The signed-in user's company; it is never taken from the request.
     */
    private function company(): Company
    {
        $company = Auth::user()?->company;

        abort_if($company === null, 404);

        return $company;
    }

    /**
     * Render the component with its page title in the current language.
     */
    public function render(): View
    {
        return $this->view()->title(__('pages.company.profile.page_title'));
    }
}; ?>

<section class="w-full">
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('pages.company.profile.title') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('pages.company.profile.description') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    @unless ($this->canUpdate)
        <flux:callout icon="lock-closed" class="mb-6 max-w-2xl" data-test="company-read-only">
            <flux:callout.text>{{ __('pages.company.profile.read_only') }}</flux:callout.text>
        </flux:callout>
    @endunless

    <form wire:submit="save" class="w-full max-w-2xl space-y-10">
        <fieldset class="space-y-6" @disabled(! $this->canUpdate)>
            <flux:heading level="2">{{ __('pages.company.profile.profile') }}</flux:heading>

            <flux:input wire:model="name" :label="__('pages.company.fields.name')" required autocomplete="organization" />

            <flux:select wire:model="industry" :label="__('pages.company.fields.industry')" required>
                @foreach ($this->industries as $option)
                    <flux:select.option :value="$option->slug" wire:key="industry-{{ $option->slug }}">{{ $option->label() }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="established_year" :label="__('pages.company.fields.established_year')" type="number" min="1800" :max="now()->year" />
        </fieldset>

        <fieldset class="space-y-6" @disabled(! $this->canUpdate)>
            <flux:heading level="2">{{ __('pages.company.profile.contact') }}</flux:heading>

            <flux:input wire:model="contact_email" :label="__('pages.company.fields.contact_email')" type="email" required autocomplete="email" />

            <div class="grid gap-6 sm:grid-cols-2">
                <flux:input wire:model="phone" :label="__('pages.company.fields.phone')" type="tel" autocomplete="tel" />

                <flux:input wire:model="whatsapp_phone" :label="__('pages.company.fields.whatsapp_phone')" type="tel" />
            </div>
        </fieldset>

        <fieldset class="space-y-6" @disabled(! $this->canUpdate)>
            <flux:heading level="2">{{ __('pages.company.profile.address') }}</flux:heading>

            <flux:input wire:model="address_line1" :label="__('pages.company.fields.address_line1')" required autocomplete="address-line1" />

            <flux:input wire:model="address_line2" :label="__('pages.company.fields.address_line2')" autocomplete="address-line2" />

            <div class="grid gap-6 sm:grid-cols-3">
                <flux:input wire:model="region" :label="__('pages.company.fields.region')" autocomplete="address-level2" />

                <flux:input wire:model="city" :label="__('pages.company.fields.city')" required autocomplete="address-level1" />

                <flux:input wire:model="postal_code" :label="__('pages.company.fields.postal_code')" autocomplete="postal-code" />
            </div>
        </fieldset>

        <fieldset class="space-y-6" @disabled(! $this->canUpdate)>
            <div>
                <flux:heading level="2">{{ __('pages.company.profile.legal') }}</flux:heading>
                <flux:text class="mt-1">{{ __('pages.company.profile.legal_description') }}</flux:text>
            </div>

            <flux:input wire:model="legal_name" :label="__('pages.company.fields.legal_name')" />

            <div class="grid gap-6 sm:grid-cols-3">
                <flux:input wire:model="tax_number" :label="__('pages.company.fields.tax_number')" />

                <flux:input wire:model="tax_office" :label="__('pages.company.fields.tax_office')" />

                <flux:input wire:model="trade_registry_number" :label="__('pages.company.fields.trade_registry_number')" />
            </div>
        </fieldset>

        @if ($this->canUpdate)
            <flux:button variant="primary" type="submit" data-test="save-company-button">
                {{ __('common.save') }}
            </flux:button>
        @endif
    </form>
</section>
