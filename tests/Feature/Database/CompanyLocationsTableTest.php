<?php

use App\Models\Company;
use App\Models\CompanyLocation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

test('a company has at most one primary location', function () {
    $company = Company::factory()->create();
    CompanyLocation::factory()->for($company)->primary()->create();
    CompanyLocation::factory()->for($company)->create();

    // The savepoint keeps the test's transaction usable after the expected failure.
    expect(fn () => DB::transaction(fn () => CompanyLocation::factory()->for($company)->primary()->create()))
        ->toThrow(fn (QueryException $exception) => expect($exception->getCode())->toBe('23505'));

    expect(CompanyLocation::factory()->primary()->create()->is_primary)->toBeTrue();
});

test('country codes are two upper-case letters', function (string $countryCode) {
    expect(fn () => CompanyLocation::factory()->create(['country_code' => $countryCode]))
        ->toThrow(fn (QueryException $exception) => expect($exception->getCode())->toBe('23514'));
})->with(['tr', 'T1']);

test('location emails are stored in lower case', function () {
    $location = CompanyLocation::factory()->create(['email' => 'Branch@Acme.TEST']);

    expect($location->refresh()->email)->toBe('branch@acme.test');
});
