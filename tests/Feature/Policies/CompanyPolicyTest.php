<?php

use App\Enums\CompanyRole;
use App\Models\Company;
use App\Models\User;

test('company abilities follow the permission matrix', function (CompanyRole $role, array $allowed) {
    $company = Company::factory()->create();
    $member = memberOf($company, $role);

    foreach (['view', 'update', 'changeIndustry', 'completeOnboarding', 'delete'] as $ability) {
        expect($member->can($ability, $company))->toBe(in_array($ability, $allowed, true), "{$role->name} / {$ability}");
    }
})->with([
    'owner' => [CompanyRole::Owner, ['view', 'update', 'changeIndustry', 'completeOnboarding', 'delete']],
    'admin' => [CompanyRole::Admin, ['view', 'update', 'changeIndustry', 'completeOnboarding']],
    'editor' => [CompanyRole::Editor, ['view']],
    'viewer' => [CompanyRole::Viewer, ['view']],
]);

test('users have no abilities in companies they do not belong to', function (?CompanyRole $roleElsewhere) {
    $company = Company::factory()->withOwner()->create();
    $user = $roleElsewhere === null
        ? User::factory()->create()
        : memberOf(Company::factory()->create(), $roleElsewhere);

    foreach (['view', 'update', 'changeIndustry', 'completeOnboarding', 'delete'] as $ability) {
        expect($user->can($ability, $company))->toBeFalse($ability);
    }
})->with([
    'without a company' => null,
    'owner of another company' => CompanyRole::Owner,
]);

test('a changed role takes effect on the next check', function () {
    $company = Company::factory()->create();
    $member = memberOf($company, CompanyRole::Admin);

    expect($member->can('update', $company))->toBeTrue();

    $member->membership->forceFill(['role' => CompanyRole::Viewer])->save();

    expect($member->can('update', $company))->toBeFalse();
});
