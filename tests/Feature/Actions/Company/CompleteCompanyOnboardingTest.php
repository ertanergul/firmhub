<?php

use App\Actions\Company\CompleteCompanyOnboarding;
use App\Models\Company;
use Illuminate\Support\Carbon;

/**
 * Valid onboarding details, with the given values replaced.
 *
 * @param  array<string, string|null>  $overrides
 * @return array{contact_email: string, phone: string|null, whatsapp_phone: string|null, address_line1: string, address_line2: string|null, region: string|null, city: string, postal_code: string|null}
 */
function onboardingDetails(array $overrides = []): array
{
    return [
        'contact_email' => 'Hello@Acme.test',
        'phone' => '+90 212 555 00 00',
        'whatsapp_phone' => null,
        'address_line1' => 'Bağdat Caddesi No: 1',
        'address_line2' => null,
        'region' => 'Kadıköy',
        'city' => 'İstanbul',
        'postal_code' => '34710',
        ...$overrides,
    ];
}

test('onboarding saves the contact details and a primary location in Türkiye', function () {
    $company = Company::factory()->onboardingIncomplete()->create();

    app(CompleteCompanyOnboarding::class)->handle($company, onboardingDetails());

    $company->refresh();

    expect($company->only('contact_email', 'phone', 'whatsapp_phone'))->toBe([
        'contact_email' => 'hello@acme.test',
        'phone' => '+90 212 555 00 00',
        'whatsapp_phone' => null,
    ])
        ->and($company->onboarding_completed_at)->not->toBeNull()
        ->and($company->primaryLocation->only('address_line1', 'region', 'city', 'postal_code', 'country_code', 'is_primary'))->toBe([
            'address_line1' => 'Bağdat Caddesi No: 1',
            'region' => 'Kadıköy',
            'city' => 'İstanbul',
            'postal_code' => '34710',
            'country_code' => 'TR',
            'is_primary' => true,
        ]);
});

test('saving again updates the primary location and keeps the completion time', function () {
    $company = Company::factory()->onboardingIncomplete()->create();
    $action = app(CompleteCompanyOnboarding::class);

    Carbon::setTestNow('2026-11-04 10:00:00');
    $action->handle($company, onboardingDetails());

    Carbon::setTestNow('2026-11-05 10:00:00');
    $action->handle($company->refresh(), onboardingDetails(['city' => 'Ankara']));

    expect($company->locations()->count())->toBe(1)
        ->and($company->refresh()->primaryLocation->city)->toBe('Ankara')
        ->and($company->onboarding_completed_at->toDateTimeString())->toBe('2026-11-04 10:00:00');
});
