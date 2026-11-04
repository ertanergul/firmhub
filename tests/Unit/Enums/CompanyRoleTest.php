<?php

use App\Enums\CompanyPermission;
use App\Enums\CompanyRole;

test('roles grant the company permissions of the permission matrix', function (CompanyRole $role, array $granted) {
    foreach (CompanyPermission::cases() as $permission) {
        expect($role->grants($permission))->toBe(in_array($permission, $granted, true), "{$role->name} / {$permission->name}");
    }
})->with([
    'owner' => [CompanyRole::Owner, [
        CompanyPermission::ViewCompany,
        CompanyPermission::UpdateCompany,
        CompanyPermission::ChangeIndustry,
        CompanyPermission::CompleteOnboarding,
        CompanyPermission::DeleteCompany,
    ]],
    'admin' => [CompanyRole::Admin, [
        CompanyPermission::ViewCompany,
        CompanyPermission::UpdateCompany,
        CompanyPermission::ChangeIndustry,
        CompanyPermission::CompleteOnboarding,
    ]],
    'editor' => [CompanyRole::Editor, [CompanyPermission::ViewCompany]],
    'viewer' => [CompanyRole::Viewer, [CompanyPermission::ViewCompany]],
]);

test('roles are ranked owner, admin, editor, viewer', function () {
    $ranked = collect(CompanyRole::cases())->sortByDesc(fn (CompanyRole $role): int => $role->rank())->values()->all();

    expect($ranked)->toBe([CompanyRole::Owner, CompanyRole::Admin, CompanyRole::Editor, CompanyRole::Viewer]);
});
