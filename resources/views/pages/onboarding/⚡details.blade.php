<?php

use App\Actions\Company\CompleteCompanyOnboarding;
use App\Actions\Company\UpdateCompanyProfile;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::auth')] class extends Component {
    public string $contact_email = '';

    public string $phone = '';

    public string $whatsapp_phone = '';

    public string $address_line1 = '';

    public string $address_line2 = '';

    public string $region = '';

    public string $city = '';

    public string $postal_code = '';

    /**
     * Fill the form from the company, or send finished companies to the dashboard.
     */
    public function mount(): void
    {
        $company = $this->company();

        $this->authorize('completeOnboarding', $company);

        if ($company->onboarding_completed_at !== null) {
            $this->redirectRoute('dashboard', navigate: true);

            return;
        }

        $location = $company->primaryLocation;

        $this->contact_email = $company->contact_email;
        $this->phone = $company->phone ?? '';
        $this->whatsapp_phone = $company->whatsapp_phone ?? '';
        $this->address_line1 = $location->address_line1 ?? '';
        $this->address_line2 = $location->address_line2 ?? '';
        $this->region = $location->region ?? '';
        $this->city = $location->city ?? '';
        $this->postal_code = $location->postal_code ?? '';
    }

    /**
     * Save the contact details and address and finish onboarding.
     */
    public function save(CompleteCompanyOnboarding $completeCompanyOnboarding): void
    {
        $company = $this->company();

        $this->authorize('completeOnboarding', $company);

        $validated = $this->validate(UpdateCompanyProfile::contactRules());

        $completeCompanyOnboarding->handle($company, array_map(fn (string $value): ?string => $value === '' ? null : $value, $validated));

        $this->redirectRoute('dashboard', navigate: true);
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
        return $this->view()->title(__('pages.onboarding.details.page_title'));
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('pages.onboarding.details.title')" :description="__('pages.onboarding.details.description')" />

    <form wire:submit="save" class="flex flex-col gap-6">
        <flux:input wire:model="contact_email" :label="__('pages.company.fields.contact_email')" type="email" required autocomplete="email" />

        <flux:input wire:model="phone" :label="__('pages.company.fields.phone')" type="tel" autocomplete="tel" />

        <flux:input wire:model="whatsapp_phone" :label="__('pages.company.fields.whatsapp_phone')" type="tel" />

        <flux:separator variant="subtle" />

        <flux:input wire:model="address_line1" :label="__('pages.company.fields.address_line1')" required autocomplete="address-line1" />

        <flux:input wire:model="address_line2" :label="__('pages.company.fields.address_line2')" autocomplete="address-line2" />

        <div class="grid gap-6 sm:grid-cols-2">
            <flux:input wire:model="region" :label="__('pages.company.fields.region')" autocomplete="address-level2" />

            <flux:input wire:model="city" :label="__('pages.company.fields.city')" required autocomplete="address-level1" />
        </div>

        <flux:input wire:model="postal_code" :label="__('pages.company.fields.postal_code')" autocomplete="postal-code" />

        <flux:button variant="primary" type="submit" class="w-full" data-test="finish-onboarding-button">
            {{ __('pages.onboarding.details.submit') }}
        </flux:button>
    </form>
</div>
