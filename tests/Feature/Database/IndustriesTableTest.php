<?php

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

test('the database rejects duplicate industry slugs', function () {
    DB::table('industries')->insert(['slug' => 'retail', 'sort_order' => 1]);

    expect(fn () => DB::table('industries')->insert(['slug' => 'retail', 'sort_order' => 2]))
        ->toThrow(fn (QueryException $exception) => expect($exception->getCode())->toBe('23505'));
});

test('the database only accepts slugs that can be used as language keys', function (string $slug) {
    expect(fn () => DB::table('industries')->insert(['slug' => $slug, 'sort_order' => 1]))
        ->toThrow(fn (QueryException $exception) => expect($exception->getCode())->toBe('23514'));
})->with([
    'upper case' => 'Retail',
    'space' => 'legal services',
    'dot' => 'legal.services',
    'leading hyphen' => '-retail',
]);
