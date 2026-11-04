<?php

use App\Enums\CompanyRole;
use App\Models\Company;
use App\Models\CompanyMembership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

/**
 * The fetch metadata a browser sends when the user opens a page.
 *
 * @return array<string, string>
 */
function pageOpenedFrom(string $fetchSite): array
{
    return [
        'Sec-Fetch-Site' => $fetchSite,
        'Sec-Fetch-Mode' => 'navigate',
        'Sec-Fetch-Dest' => 'document',
    ];
}

/**
 * Create a user who is a member of the company with the given role (0.9 §4.3).
 *
 * @param  array<string, mixed>  $attributes
 */
function memberOf(Company $company, CompanyRole $role, array $attributes = []): User
{
    $user = User::factory()->create($attributes);

    CompanyMembership::factory()->role($role)->for($company)->for($user)->create();

    return $user;
}

/**
 * Create a user who owns a new company.
 *
 * @param  array<string, mixed>  $attributes
 */
function companyOwner(array $attributes = []): User
{
    return memberOf(Company::factory()->create(), CompanyRole::Owner, $attributes);
}
