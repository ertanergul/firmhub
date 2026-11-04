<?php

namespace App\Actions\Company;

use App\Models\Company;
use App\Models\CompanyLocation;
use Illuminate\Support\Facades\DB;

/**
 * Saves the company's contact details and primary location and completes onboarding (0.1 FR-COMP-03).
 *
 * Everything is written in one transaction, so a company is never marked as onboarded
 * without its details. During the proof of concept every location is in Türkiye.
 */
class CompleteCompanyOnboarding
{
    /**
     * The country of every location during the proof of concept.
     */
    public const COUNTRY_CODE = 'TR';

    /**
     * Save the validated details and mark onboarding as completed.
     *
     * @param  array{contact_email: string, phone: string|null, whatsapp_phone: string|null, address_line1: string, address_line2: string|null, region: string|null, city: string, postal_code: string|null}  $details
     */
    public function handle(Company $company, array $details): void
    {
        DB::transaction(function () use ($company, $details): void {
            $company->fill([
                'contact_email' => $details['contact_email'],
                'phone' => $details['phone'],
                'whatsapp_phone' => $details['whatsapp_phone'],
            ]);
            $company->onboarding_completed_at ??= now();
            $company->save();

            $location = $company->primaryLocation ?? new CompanyLocation(['is_primary' => true]);
            $location->fill([
                'address_line1' => $details['address_line1'],
                'address_line2' => $details['address_line2'],
                'region' => $details['region'],
                'city' => $details['city'],
                'postal_code' => $details['postal_code'],
                'country_code' => self::COUNTRY_CODE,
            ]);
            $company->locations()->save($location);
        });
    }
}
