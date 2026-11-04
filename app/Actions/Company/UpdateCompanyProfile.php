<?php

namespace App\Actions\Company;

use App\Models\Company;
use App\Models\CompanyLocation;
use App\Models\Industry;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Saves validated company details and the primary location in one transaction (0.5 §10 Company).
 *
 * The validation rules for these fields live here too, shared by onboarding and the company profile.
 *
 * Only the given company fields are changed; the location fields are always saved, and
 * during the proof of concept every location is in Türkiye. Callers authorize the change
 * (CompanyPolicy `update`, and `changeIndustry` for a new industry).
 */
class UpdateCompanyProfile
{
    /**
     * The country of every location during the proof of concept.
     */
    public const COUNTRY_CODE = 'TR';

    /**
     * The company fields that can be saved.
     *
     * @var array<int, string>
     */
    private const COMPANY_FIELDS = ['name', 'contact_email', 'phone', 'whatsapp_phone', 'legal_name', 'tax_number', 'tax_office', 'trade_registry_number', 'established_year'];

    /**
     * The primary location fields that are saved.
     *
     * @var array<int, string>
     */
    private const LOCATION_FIELDS = ['address_line1', 'address_line2', 'region', 'city', 'postal_code'];

    /**
     * Get the rules for the contact details and the primary location (0.1 FR-COMP-03).
     *
     * Phone numbers get a light format check and are stored as entered (DECISIONS, task 2.4).
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    public static function contactRules(): array
    {
        $phone = ['nullable', 'string', 'regex:/^\+?[0-9 ()-]{7,20}$/'];

        return [
            'contact_email' => ['required', 'string', 'email', 'max:254'],
            'phone' => $phone,
            'whatsapp_phone' => $phone,
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
        ];
    }

    /**
     * Get the rules for the rest of the company profile.
     *
     * The industry must be offered, unless it is the company's current one: a company keeps
     * an industry that was deactivated later. Legal fields are free text, because their
     * display requirements are not verified yet (0.1 Q14).
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    public static function profileRules(Company $company): array
    {
        $legal = ['nullable', 'string', 'max:50'];

        return [
            'name' => ['required', 'string', 'max:255'],
            'industry' => [
                'required',
                'string',
                Rule::exists('industries', 'slug')->where(
                    fn ($query) => $query->where('is_active', true)->orWhere('id', $company->industry_id),
                ),
            ],
            'established_year' => ['nullable', 'integer', 'between:1800,'.now()->year],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'tax_number' => $legal,
            'tax_office' => $legal,
            'trade_registry_number' => $legal,
        ];
    }

    /**
     * Save the details, and the new industry when one is given.
     *
     * @param  array<string, mixed>  $details  validated company and location fields
     */
    public function handle(Company $company, array $details, ?Industry $industry = null): void
    {
        DB::transaction(function () use ($company, $details, $industry): void {
            $company->fill(Arr::only($details, self::COMPANY_FIELDS));

            if ($industry !== null) {
                $company->industry()->associate($industry);
            }

            $company->save();

            $location = $company->primaryLocation ?? new CompanyLocation(['is_primary' => true]);
            $location->fill([...Arr::only($details, self::LOCATION_FIELDS), 'country_code' => self::COUNTRY_CODE]);
            $company->locations()->save($location);
        });
    }
}
