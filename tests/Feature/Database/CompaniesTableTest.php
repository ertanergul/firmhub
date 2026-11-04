<?php

use App\Models\Company;
use App\Models\CompanyMembership;
use App\Models\Industry;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Insert a company row directly, bypassing the model, so database constraints are tested on their own.
 *
 * @param  array<string, mixed>  $attributes
 */
function insertCompany(array $attributes = []): string
{
    $id = (string) Str::uuid7();

    DB::table('companies')->insert([
        'id' => $id,
        'name' => 'Acme',
        'industry_id' => Industry::factory()->create()->id,
        'contact_email' => 'hello@acme.test',
        ...$attributes,
    ]);

    return $id;
}

/**
 * Insert a membership row directly, bypassing the model.
 */
function insertMembership(string $companyId, int $userId, string $role = 'viewer'): void
{
    DB::table('company_memberships')->insert([
        'id' => (string) Str::uuid7(),
        'company_id' => $companyId,
        'user_id' => $userId,
        'role' => $role,
    ]);
}

/**
 * Assert that the callback fails with the given PostgreSQL error code.
 */
function expectSqlState(Closure $callback, string $sqlState): void
{
    expect($callback)->toThrow(fn (QueryException $exception) => expect($exception->getCode())->toBe($sqlState));
}

test('the database requires a lower-case contact email', function () {
    expectSqlState(fn () => insertCompany(['contact_email' => 'Hello@Acme.test']), '23514');
});

test('the database rejects implausible founding years', function (int $year) {
    expectSqlState(fn () => insertCompany(['established_year' => $year]), '23514');
})->with([1799, 2101]);

test('an industry used by a company cannot be deleted', function () {
    $industry = Industry::factory()->create();
    insertCompany(['industry_id' => $industry->id]);

    expectSqlState(fn () => DB::table('industries')->delete($industry->id), '23001');
});

test('the database only accepts the four company roles', function () {
    $companyId = insertCompany();

    expectSqlState(fn () => insertMembership($companyId, User::factory()->create()->id, 'superadmin'), '23514');
});

test('a user belongs to at most one company', function () {
    $user = User::factory()->create();
    insertMembership(insertCompany(), $user->id);

    expectSqlState(fn () => insertMembership(insertCompany(['contact_email' => 'other@acme.test']), $user->id), '23505');
});

test('memberships are removed with their company or user', function () {
    $company = Company::factory()->withOwner()->create();
    $member = CompanyMembership::factory()->for($company)->create();

    $member->user->delete();
    expect(CompanyMembership::query()->count())->toBe(1);

    $company->forceDelete();
    expect(CompanyMembership::query()->count())->toBe(0);
});
