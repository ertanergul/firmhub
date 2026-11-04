<?php

use App\Enums\CompanyRole;
use App\Models\Industry;
use App\Models\User;
use Database\Seeders\IndustrySeeder;
use Livewire\Livewire;

test('guests are redirected to the login page', function () {
    $this->get(route('onboarding'))->assertRedirect(route('login'));
});

test('users with an unverified email are asked to verify it', function () {
    $this->actingAs(User::factory()->unverified()->create())
        ->get(route('onboarding'))
        ->assertRedirect(route('verification.notice'));
});

test('the page offers the active industries in the page language', function () {
    $this->seed(IndustrySeeder::class);
    Industry::query()->where('slug', 'other')->update(['is_active' => false]);

    $this->actingAs(User::factory()->create())
        ->get(route('onboarding', ['locale' => 'tr']))
        ->assertOk()
        ->assertSee('Şirketinizi oluşturun')
        ->assertSeeInOrder(['Perakende', 'Hukuk Hizmetleri'])
        ->assertDontSee('Diğer');
});

test('a user creates a company and becomes its owner', function () {
    $this->seed(IndustrySeeder::class);
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::onboarding.company')
        ->set('name', 'Acme Retail')
        ->set('industry', 'retail')
        ->call('createCompany')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    expect($user->refresh()->company->name)->toBe('Acme Retail')
        ->and($user->company->industry->slug)->toBe('retail')
        ->and($user->membership->role)->toBe(CompanyRole::Owner);
});

test('the company name and an offered industry are required', function (array $input, string $field) {
    Industry::factory()->inactive()->create(['slug' => 'closed']);
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::onboarding.company')
        ->set(['name' => 'Acme', 'industry' => 'closed', ...$input])
        ->call('createCompany')
        ->assertHasErrors($field);

    expect($user->refresh()->company)->toBeNull();
})->with([
    'missing name' => [['name' => ''], 'name'],
    'too long name' => [['name' => str_repeat('a', 256)], 'name'],
    'inactive industry' => [[], 'industry'],
    'unknown industry' => [['industry' => 'unknown'], 'industry'],
]);

test('users who already have a company go to their dashboard', function () {
    $owner = companyOwner();

    Livewire::actingAs($owner)
        ->test('pages::onboarding.company')
        ->assertRedirect(route('dashboard'));
});
