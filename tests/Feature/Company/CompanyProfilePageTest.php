<?php

use App\Enums\CompanyRole;
use App\Models\Company;
use App\Models\CompanyLocation;
use App\Models\Industry;
use App\Models\User;
use Livewire\Livewire;

/**
 * A company with a primary location, for the profile page.
 */
function profileCompany(): Company
{
    $company = Company::factory()->create([
        'name' => 'Acme Retail',
        'contact_email' => 'hello@acme.test',
        'industry_id' => Industry::factory()->create(['slug' => 'retail', 'sort_order' => 1])->id,
    ]);

    CompanyLocation::factory()->for($company)->primary()->create(['address_line1' => 'Bağdat Caddesi No: 1', 'city' => 'İstanbul']);

    return $company;
}

/**
 * Valid profile input, with the given values replaced.
 *
 * @param  array<string, string>  $overrides
 * @return array<string, string>
 */
function profileInput(array $overrides = []): array
{
    return [
        'name' => 'Acme Retail Ltd.',
        'industry' => 'retail',
        'established_year' => '1998',
        'contact_email' => 'info@acme.test',
        'phone' => '0212 555 00 00',
        'whatsapp_phone' => '',
        'address_line1' => 'İstiklal Caddesi No: 5',
        'address_line2' => '',
        'region' => 'Beyoğlu',
        'city' => 'İstanbul',
        'postal_code' => '34430',
        'legal_name' => 'Acme Perakende A.Ş.',
        'tax_number' => '1234567890',
        'tax_office' => 'Beyoğlu',
        'trade_registry_number' => '',
        ...$overrides,
    ];
}

test('guests are redirected to the login page', function () {
    $this->get(route('company.profile'))->assertRedirect(route('login'));
});

test('every member sees the company details in the page language', function (CompanyRole $role) {
    $member = memberOf(profileCompany(), $role);

    $this->actingAs($member)
        ->get(route('company.profile', ['locale' => 'tr']))
        ->assertOk()
        ->assertSee('Yasal bilgiler');

    Livewire::actingAs($member)
        ->test('pages::company.profile')
        ->assertSet('name', 'Acme Retail')
        ->assertSet('industry', 'retail')
        ->assertSet('address_line1', 'Bağdat Caddesi No: 1')
        ->assertSet('city', 'İstanbul');
})->with(CompanyRole::cases());

test('owners and admins can edit, editors and viewers only read', function (CompanyRole $role, bool $canEdit) {
    $response = $this->actingAs(memberOf(profileCompany(), $role))->get(route('company.profile'));

    $canEdit
        ? $response->assertSee('data-test="save-company-button"', false)->assertDontSee('data-test="company-read-only"', false)
        : $response->assertSee('data-test="company-read-only"', false)->assertDontSee('data-test="save-company-button"', false);
})->with([
    'owner' => [CompanyRole::Owner, true],
    'admin' => [CompanyRole::Admin, true],
    'editor' => [CompanyRole::Editor, false],
    'viewer' => [CompanyRole::Viewer, false],
]);

test('an owner saves every company detail', function () {
    $company = profileCompany();
    Industry::factory()->create(['slug' => 'legal']);

    Livewire::actingAs(memberOf($company, CompanyRole::Owner))
        ->test('pages::company.profile')
        ->set(profileInput(['industry' => 'legal']))
        ->call('save')
        ->assertHasNoErrors();

    $company->refresh();

    expect($company->only('name', 'established_year', 'contact_email', 'phone', 'whatsapp_phone', 'legal_name', 'tax_number', 'tax_office', 'trade_registry_number'))->toBe([
        'name' => 'Acme Retail Ltd.',
        'established_year' => 1998,
        'contact_email' => 'info@acme.test',
        'phone' => '0212 555 00 00',
        'whatsapp_phone' => null,
        'legal_name' => 'Acme Perakende A.Ş.',
        'tax_number' => '1234567890',
        'tax_office' => 'Beyoğlu',
        'trade_registry_number' => null,
    ])
        ->and($company->industry->slug)->toBe('legal')
        ->and($company->locations()->count())->toBe(1)
        ->and($company->primaryLocation->only('address_line1', 'address_line2', 'region', 'postal_code'))->toBe([
            'address_line1' => 'İstiklal Caddesi No: 5',
            'address_line2' => null,
            'region' => 'Beyoğlu',
            'postal_code' => '34430',
        ]);
});

test('an admin can change the industry', function () {
    $company = profileCompany();
    Industry::factory()->create(['slug' => 'legal']);

    Livewire::actingAs(memberOf($company, CompanyRole::Admin))
        ->test('pages::company.profile')
        ->set(profileInput(['industry' => 'legal']))
        ->call('save')
        ->assertHasNoErrors();

    expect($company->refresh()->industry->slug)->toBe('legal');
});

test('editors and viewers cannot save', function (CompanyRole $role) {
    $company = profileCompany();

    Livewire::actingAs(memberOf($company, $role))
        ->test('pages::company.profile')
        ->set(profileInput())
        ->call('save')
        ->assertForbidden();

    expect($company->refresh()->name)->toBe('Acme Retail');
})->with([CompanyRole::Editor, CompanyRole::Viewer]);

test('invalid details are rejected and nothing is saved', function (array $input, string $field) {
    $company = profileCompany();
    Industry::factory()->inactive()->create(['slug' => 'closed']);

    Livewire::actingAs(memberOf($company, CompanyRole::Owner))
        ->test('pages::company.profile')
        ->set(profileInput($input))
        ->call('save')
        ->assertHasErrors($field);

    expect($company->refresh()->name)->toBe('Acme Retail');
})->with([
    'missing name' => [['name' => ''], 'name'],
    'inactive industry' => [['industry' => 'closed'], 'industry'],
    'year too early' => [['established_year' => '1799'], 'established_year'],
    'year in the future' => [['established_year' => (string) (now()->year + 1)], 'established_year'],
    'long tax number' => [['tax_number' => str_repeat('1', 51)], 'tax_number'],
    'invalid contact email' => [['contact_email' => 'nope'], 'contact_email'],
    'missing city' => [['city' => ''], 'city'],
]);

test('a company keeps an industry that is no longer offered', function () {
    $company = profileCompany();
    $company->industry->update(['is_active' => false]);

    Livewire::actingAs(memberOf($company, CompanyRole::Owner))
        ->test('pages::company.profile')
        ->set(profileInput())
        ->call('save')
        ->assertHasNoErrors();

    expect($company->refresh()->industry->slug)->toBe('retail')
        ->and($company->name)->toBe('Acme Retail Ltd.');
});

test('the sidebar links to the company page for members only', function () {
    $this->actingAs(companyOwner())
        ->get(route('dashboard'))
        ->assertSee(route('company.profile'));

    $this->actingAs(User::factory()->create())
        ->get(route('profile.edit'))
        ->assertDontSee(route('company.profile'));
});
