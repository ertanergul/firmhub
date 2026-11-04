<?php

use App\Enums\CompanyRole;
use App\Models\Company;
use App\Models\User;
use Livewire\Livewire;

/**
 * An Owner of a company that has not finished onboarding.
 */
function onboardingOwner(): User
{
    return memberOf(Company::factory()->onboardingIncomplete()->create(['contact_email' => 'hello@acme.test']), CompanyRole::Owner);
}

test('guests are redirected to the login page', function () {
    $this->get(route('onboarding.details'))->assertRedirect(route('login'));
});

test('users without a company start with the first step', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('onboarding.details'))
        ->assertRedirect(route('onboarding'));
});

test('the page is shown in the page language with the current contact email', function () {
    $this->actingAs(onboardingOwner())
        ->get(route('onboarding.details', ['locale' => 'tr']))
        ->assertOk()
        ->assertSee('Şirket bilgileri')
        ->assertSee('hello@acme.test');
});

test('an owner finishes onboarding and goes to the dashboard', function () {
    $owner = onboardingOwner();

    Livewire::actingAs($owner)
        ->test('pages::onboarding.details')
        ->set([
            'contact_email' => 'info@acme.test',
            'phone' => '0212 555 00 00',
            'address_line1' => 'Bağdat Caddesi No: 1',
            'city' => 'İstanbul',
        ])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $company = $owner->company->refresh();

    expect($company->onboarding_completed_at)->not->toBeNull()
        ->and($company->contact_email)->toBe('info@acme.test')
        ->and($company->whatsapp_phone)->toBeNull()
        ->and($company->primaryLocation->city)->toBe('İstanbul')
        ->and($company->primaryLocation->address_line2)->toBeNull();
});

test('invalid details are rejected and nothing is saved', function (array $input, string $field) {
    $owner = onboardingOwner();

    Livewire::actingAs($owner)
        ->test('pages::onboarding.details')
        ->set(['address_line1' => 'Bağdat Caddesi No: 1', 'city' => 'İstanbul', ...$input])
        ->call('save')
        ->assertHasErrors($field);

    expect($owner->company->refresh()->onboarding_completed_at)->toBeNull()
        ->and($owner->company->locations()->count())->toBe(0);
})->with([
    'missing contact email' => [['contact_email' => ''], 'contact_email'],
    'invalid contact email' => [['contact_email' => 'not-an-email'], 'contact_email'],
    'invalid phone' => [['phone' => 'call me'], 'phone'],
    'invalid whatsapp' => [['whatsapp_phone' => '12'], 'whatsapp_phone'],
    'missing address' => [['address_line1' => ''], 'address_line1'],
    'missing city' => [['city' => ''], 'city'],
]);

test('validation messages name the fields in the page language', function () {
    app()->setLocale('tr');

    Livewire::actingAs(onboardingOwner())
        ->test('pages::onboarding.details')
        ->set('city', '')
        ->call('save')
        ->assertHasErrors(['city' => 'İl alanı zorunludur.']);
});

test('roles without the onboarding permission cannot open or save the step', function (CompanyRole $role) {
    $member = memberOf(Company::factory()->onboardingIncomplete()->create(), $role);

    $this->actingAs($member)->get(route('onboarding.details'))->assertForbidden();

    expect($member->company->refresh()->onboarding_completed_at)->toBeNull();
})->with([CompanyRole::Editor, CompanyRole::Viewer]);

test('companies that finished onboarding go to the dashboard', function () {
    Livewire::actingAs(companyOwner())
        ->test('pages::onboarding.details')
        ->assertRedirect(route('dashboard'));
});
