<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

beforeEach(function () {
    app()['env'] = 'production';
});

test('production accepts an eight character password that meets the composition rules', function () {
    Http::preventStrayRequests();
    Http::fake();

    $validator = Validator::make(['password' => 'Abcdef1!'], ['password' => Password::defaults()]);

    expect($validator->passes())->toBeTrue();
    Http::assertNothingSent();
});

test('production rejects passwords shorter than eight characters', function () {
    $validator = Validator::make(['password' => 'Abcde1!'], ['password' => Password::defaults()]);

    expect($validator->fails())->toBeTrue();
});

test('production rejects passwords that do not meet the composition rules', function (string $password) {
    $validator = Validator::make(['password' => $password], ['password' => Password::defaults()]);

    expect($validator->fails())->toBeTrue();
})->with([
    'no uppercase letter' => 'abcdef1!',
    'no lowercase letter' => 'ABCDEF1!',
    'no number' => 'Abcdefg!',
    'no symbol' => 'Abcdefg1',
]);
