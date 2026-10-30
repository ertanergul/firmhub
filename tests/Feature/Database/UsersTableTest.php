<?php

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

test('the database rejects emails that differ only in letter case', function () {
    $user = [
        'name' => 'Existing User',
        'password' => 'hashed-password',
        'created_at' => now(),
        'updated_at' => now(),
    ];

    DB::table('users')->insert([...$user, 'email' => 'user@example.com']);

    expect(fn () => DB::table('users')->insert([...$user, 'email' => 'User@Example.com']))
        ->toThrow(fn (QueryException $exception) => expect($exception->getCode())->toBe('23505'));
});
