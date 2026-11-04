<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    /**
     * Whether the user is the last Owner of a company and so cannot delete the account (0.2 RM-8).
     */
    #[Computed]
    public function isLastOwner(): bool
    {
        return Auth::user()->isLastOwnerOfACompany();
    }
}; ?>

<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <flux:heading>{{ __('pages.settings.delete_account.title') }}</flux:heading>
        <flux:subheading>{{ __('pages.settings.delete_account.description') }}</flux:subheading>
    </div>

    @if ($this->isLastOwner)
        <flux:callout icon="building-office" class="max-w-2xl" data-test="delete-user-last-owner">
            <flux:callout.text>{{ __('pages.settings.delete_account.last_owner') }}</flux:callout.text>
        </flux:callout>
    @else
        <flux:modal.trigger name="confirm-user-deletion">
            <flux:button variant="danger" data-test="delete-user-button">
                {{ __('pages.settings.delete_account.title') }}
            </flux:button>
        </flux:modal.trigger>

        <livewire:pages::settings.delete-user-modal />
    @endif
</section>
