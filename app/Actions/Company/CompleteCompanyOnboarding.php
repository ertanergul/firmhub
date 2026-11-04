<?php

namespace App\Actions\Company;

use App\Models\Company;
use Illuminate\Support\Facades\DB;

/**
 * Saves the company's contact details and primary location and completes onboarding (0.1 FR-COMP-03).
 *
 * Everything is written in one transaction, so a company is never marked as onboarded
 * without its details.
 */
class CompleteCompanyOnboarding
{
    public function __construct(private UpdateCompanyProfile $updateCompanyProfile) {}

    /**
     * Save the validated details and mark onboarding as completed.
     *
     * @param  array{contact_email: string, phone: string|null, whatsapp_phone: string|null, address_line1: string, address_line2: string|null, region: string|null, city: string, postal_code: string|null}  $details
     */
    public function handle(Company $company, array $details): void
    {
        DB::transaction(function () use ($company, $details): void {
            $this->updateCompanyProfile->handle($company, $details);

            $company->onboarding_completed_at ??= now();
            $company->save();
        });
    }
}
