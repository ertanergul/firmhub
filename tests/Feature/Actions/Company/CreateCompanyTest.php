<?php

use App\Actions\Company\CreateCompany;
use App\Enums\CompanyRole;
use App\Models\Company;
use App\Models\CompanyMembership;
use App\Models\Industry;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

test('a verified user creates a company and becomes its owner', function () {
    $owner = User::factory()->create(['email' => 'owner@example.com']);
    $industry = Industry::factory()->create();

    $company = app(CreateCompany::class)->handle($owner, 'Acme Retail', $industry);

    expect($company->only('name', 'contact_email', 'industry_id'))->toBe([
        'name' => 'Acme Retail',
        'contact_email' => 'owner@example.com',
        'industry_id' => $industry->id,
    ])
        ->and(Str::isUuid($company->id, 7))->toBeTrue()
        ->and($owner->membership->company_id)->toBe($company->id)
        ->and($owner->membership->role)->toBe(CompanyRole::Owner);
});

test('unverified users cannot create a company', function () {
    $owner = User::factory()->unverified()->create();

    expect(fn () => app(CreateCompany::class)->handle($owner, 'Acme', Industry::factory()->create()))
        ->toThrow(AuthorizationException::class);

    expect(Company::query()->count())->toBe(0);
});

test('a company cannot be created in an industry that is no longer offered', function () {
    $owner = User::factory()->create();

    expect(fn () => app(CreateCompany::class)->handle($owner, 'Acme', Industry::factory()->inactive()->create()))
        ->toThrow(ValidationException::class, __('pages.company.errors.industry_unavailable'));

    expect(Company::query()->count())->toBe(0);
});

test('a user who already belongs to a company cannot create another one', function () {
    $owner = User::factory()->create();
    Company::factory()->withOwner($owner)->create();

    expect(fn () => app(CreateCompany::class)->handle($owner, 'Second', Industry::factory()->create()))
        ->toThrow(ValidationException::class, __('pages.company.errors.already_in_company'));

    expect(Company::query()->count())->toBe(1);
});

test('a membership created by a simultaneous request rolls the new company back', function () {
    $owner = User::factory()->create();
    $otherCompany = Company::factory()->withOwner()->create();

    // Simulates another request that adds the user to a company after the action's own check.
    CompanyMembership::creating(function (CompanyMembership $membership) use ($owner, $otherCompany): void {
        if ($membership->user_id === $owner->id) {
            DB::table('company_memberships')->insert([
                'id' => (string) Str::uuid7(),
                'company_id' => $otherCompany->id,
                'user_id' => $owner->id,
                'role' => 'viewer',
            ]);
        }
    });

    expect(fn () => app(CreateCompany::class)->handle($owner, 'Acme', Industry::factory()->create()))
        ->toThrow(ValidationException::class, __('pages.company.errors.already_in_company'));

    expect(Company::query()->pluck('id')->all())->toBe([$otherCompany->id]);
});

test('ownership fields are not mass-assignable', function () {
    $company = new Company(['name' => 'Acme', 'industry_id' => 1, 'suspended_at' => now(), 'onboarding_completed_at' => now()]);

    expect($company->getAttributes())->toBe(['name' => 'Acme'])
        ->and(fn () => new CompanyMembership(['company_id' => 'x', 'user_id' => 1, 'role' => 'owner']))
        ->toThrow(MassAssignmentException::class);
});
