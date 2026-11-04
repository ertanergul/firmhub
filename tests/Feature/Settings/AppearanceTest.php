<?php

use App\Models\User;
use Livewire\Livewire;

test('the dashboard language can be changed in the appearance settings', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('pages::settings.appearance')
        ->set('locale', 'tr')
        ->assertHasNoErrors()
        ->assertRedirect(route('appearance.edit', ['locale' => 'tr']));

    expect($user->refresh()->locale)->toBe('tr');
});

test('unsupported dashboard languages are rejected', function () {
    $user = User::factory()->create(['locale' => 'en']);

    $this->actingAs($user);

    Livewire::test('pages::settings.appearance')
        ->set('locale', 'de')
        ->assertHasErrors('locale');

    expect($user->refresh()->locale)->toBe('en');
});
