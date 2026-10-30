<?php

use Illuminate\Support\Facades\Notification;
use Laravel\Fortify\Features;

test('registration is throttled per ip address', function () {
    $this->skipUnlessFortifyHas(Features::registration());

    for ($attempt = 1; $attempt <= 10; $attempt++) {
        $this->post(route('register.store'), [])->assertSessionHasErrors();
    }

    $this->post(route('register.store'), [])->assertTooManyRequests();

    $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.20'])
        ->post(route('register.store'), [])
        ->assertSessionHasErrors();
});

test('password reset link requests are throttled per ip address', function () {
    $this->skipUnlessFortifyHas(Features::resetPasswords());

    Notification::fake();

    for ($attempt = 1; $attempt <= 5; $attempt++) {
        $this->post(route('password.email'), ['email' => "person{$attempt}@example.com"])->assertRedirect();
    }

    $this->post(route('password.email'), ['email' => 'person6@example.com'])->assertTooManyRequests();
});

test('password reset link requests are throttled per email address across ip addresses', function () {
    $this->skipUnlessFortifyHas(Features::resetPasswords());

    Notification::fake();

    $emails = ['victim@example.com', 'Victim@example.com', 'VICTIM@example.com'];

    foreach ($emails as $index => $email) {
        $this->withServerVariables(['REMOTE_ADDR' => "203.0.113.{$index}"])
            ->post(route('password.email'), ['email' => $email])
            ->assertRedirect();
    }

    $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.99'])
        ->post(route('password.email'), ['email' => 'victim@example.com'])
        ->assertTooManyRequests();
});
