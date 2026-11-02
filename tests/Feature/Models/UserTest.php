<?php

use App\Models\User;
use Illuminate\Support\Facades\Gate;

test('new users are not platform admins', function () {
    $user = User::factory()->create();

    expect($user->is_platform_admin)->toBeFalse()
        ->and($user->refresh()->is_platform_admin)->toBeFalse();
});

test('the platform admin flag cannot be mass assigned', function () {
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'is_platform_admin' => true,
    ]);

    $user->update(['is_platform_admin' => true]);

    expect($user->refresh()->is_platform_admin)->toBeFalse();
});

test('the platform admin gate allows platform admins only', function () {
    expect(Gate::forUser(User::factory()->platformAdmin()->create())->allows('platform.admin'))->toBeTrue()
        ->and(Gate::forUser(User::factory()->create())->allows('platform.admin'))->toBeFalse();
});

test('platform admins receive no other abilities implicitly', function () {
    Gate::define('manage-company', fn (User $user): bool => false);

    expect(Gate::forUser(User::factory()->platformAdmin()->create())->allows('manage-company'))->toBeFalse();
});
