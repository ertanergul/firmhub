<?php

use App\Enums\CompanyRole;
use App\Models\Company;
use App\Models\CompanyMembership;
use App\Models\User;
use Livewire\Livewire;

test('the last owner of a company sees why the account cannot be deleted', function () {
    $this->actingAs(companyOwner())
        ->get(route('profile.edit', ['locale' => 'tr']))
        ->assertSee('Bir şirketin tek sahibisiniz.')
        ->assertSee('data-test="delete-user-last-owner"', false)
        ->assertDontSee('data-test="delete-user-button"', false);
});

test('the last owner of a company cannot delete the account', function () {
    $owner = companyOwner();

    Livewire::actingAs($owner)
        ->test('pages::settings.delete-user-modal')
        ->set('password', 'password')
        ->call('deleteUser')
        ->assertHasErrors(['password' => __('pages.settings.delete_account.last_owner')]);

    expect($owner->fresh())->not->toBeNull()
        ->and($owner->membership()->exists())->toBeTrue()
        ->and(auth()->check())->toBeTrue();
});

test('members who are not the last owner can delete their account', function (CompanyRole $role, bool $anotherOwner) {
    $company = Company::factory()->create();
    $member = memberOf($company, $role);

    if ($anotherOwner) {
        memberOf($company, CompanyRole::Owner);
    }

    $this->actingAs($member)
        ->get(route('profile.edit'))
        ->assertSee('data-test="delete-user-button"', false);

    Livewire::actingAs($member)
        ->test('pages::settings.delete-user-modal')
        ->set('password', 'password')
        ->call('deleteUser')
        ->assertHasNoErrors()
        ->assertRedirect('/');

    expect($member->fresh())->toBeNull()
        ->and($company->fresh())->not->toBeNull();
})->with([
    'owner with another owner' => [CompanyRole::Owner, true],
    'admin' => [CompanyRole::Admin, true],
    'editor' => [CompanyRole::Editor, true],
    'viewer' => [CompanyRole::Viewer, true],
]);

test('the last owner cannot be deleted by any other code either', function () {
    $owner = companyOwner();

    expect(fn () => $owner->delete())->toThrow(LogicException::class);

    expect($owner->fresh())->not->toBeNull()
        ->and(CompanyMembership::query()->whereBelongsTo($owner)->exists())->toBeTrue();
});

test('an owner is only the last owner of companies without another owner', function () {
    $owner = companyOwner();
    $coOwnedCompany = Company::factory()->create();
    $coOwner = memberOf($coOwnedCompany, CompanyRole::Owner);
    memberOf($coOwnedCompany, CompanyRole::Owner);

    expect($owner->isLastOwnerOfACompany())->toBeTrue()
        ->and($coOwner->isLastOwnerOfACompany())->toBeFalse()
        ->and(User::factory()->create()->isLastOwnerOfACompany())->toBeFalse()
        ->and(memberOf(Company::factory()->withOwner()->create(), CompanyRole::Admin)->isLastOwnerOfACompany())->toBeFalse();
});
