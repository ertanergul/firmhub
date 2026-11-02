<?php

use App\Models\User;
use Illuminate\Support\Facades\Log;

test('the platform admin flag is granted and the change is logged without email addresses', function () {
    Log::spy();

    $user = User::factory()->create();

    $this->artisan('platform-admin:grant', ['email' => $user->email, '--reason' => 'Operations on-call'])
        ->expectsOutputToContain('Platform admin granted.')
        ->assertSuccessful();

    expect($user->refresh()->is_platform_admin)->toBeTrue();

    Log::shouldHaveReceived('info')->withArgs(fn (string $message, array $context): bool => $context['event'] === 'PlatformAdminGranted'
        && is_string($context['actor']) && $context['actor'] !== ''
        && $context['actor_user_id'] === null
        && $context['user_id'] === $user->id
        && $context['reason'] === 'Operations on-call'
        && ! str_contains(json_encode($context), $user->email))
        ->once();
});

test('the user is found regardless of the letter case of the given email', function () {
    $user = User::factory()->create(['email' => 'admin@example.com']);

    $this->artisan('platform-admin:grant', ['email' => 'Admin@Example.com', '--reason' => 'Operations on-call'])
        ->assertSuccessful();

    expect($user->refresh()->is_platform_admin)->toBeTrue();
});

test('the requesting user is logged by id when an actor is given', function () {
    Log::spy();

    $actor = User::factory()->platformAdmin()->create();
    $user = User::factory()->create();

    $this->artisan('platform-admin:grant', ['email' => $user->email, '--reason' => 'Operations on-call', '--actor' => $actor->email])
        ->assertSuccessful();

    Log::shouldHaveReceived('info')->withArgs(fn (string $message, array $context): bool => $context['actor_user_id'] === $actor->id
        && $context['user_id'] === $user->id)
        ->once();
});

test('the flag is not granted without a reason', function (?string $reason) {
    $user = User::factory()->create();

    $this->artisan('platform-admin:grant', ['email' => $user->email, '--reason' => $reason])
        ->expectsOutputToContain('The --reason option is required.')
        ->assertFailed();

    expect($user->refresh()->is_platform_admin)->toBeFalse();
})->with([
    'missing' => null,
    'blank' => '   ',
]);

test('the command fails for an unknown email address', function () {
    $this->artisan('platform-admin:grant', ['email' => 'nobody@example.com', '--reason' => 'Operations on-call'])
        ->expectsOutputToContain('No user exists with the given email address.')
        ->assertFailed();
});

test('the flag is not granted when the actor is unknown', function () {
    $user = User::factory()->create();

    $this->artisan('platform-admin:grant', ['email' => $user->email, '--reason' => 'Operations on-call', '--actor' => 'nobody@example.com'])
        ->expectsOutputToContain('No user exists with the given --actor email address.')
        ->assertFailed();

    expect($user->refresh()->is_platform_admin)->toBeFalse();
});

test('granting an existing platform admin changes and logs nothing', function () {
    Log::spy();

    $user = User::factory()->platformAdmin()->create();
    $updatedAt = $user->updated_at;

    $this->travel(1)->minutes();

    $this->artisan('platform-admin:grant', ['email' => $user->email, '--reason' => 'Operations on-call'])
        ->expectsOutputToContain('The user is already a platform admin.')
        ->assertSuccessful();

    expect($user->refresh()->updated_at)->toEqual($updatedAt);

    Log::shouldNotHaveReceived('info');
});
