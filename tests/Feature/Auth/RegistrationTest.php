<?php

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('registration rejects an email that differs from an existing one only in letter case', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'Taken@Example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('new users are created with a lowercase email', function () {
    $user = app(CreateNewUser::class)->create([
        'name' => 'Jane Doe',
        'email' => 'Jane@Example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    expect($user->email)->toBe('jane@example.com');
});
