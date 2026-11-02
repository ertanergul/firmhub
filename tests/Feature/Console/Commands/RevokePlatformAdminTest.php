<?php

use App\Models\User;
use Illuminate\Support\Facades\Log;

test('the platform admin flag is revoked and the change is logged', function () {
    Log::spy();

    $user = User::factory()->platformAdmin()->create();

    $this->artisan('platform-admin:revoke', ['email' => $user->email, '--reason' => 'Left the operations team'])
        ->expectsOutputToContain('Platform admin revoked.')
        ->assertSuccessful();

    expect($user->refresh()->is_platform_admin)->toBeFalse();

    Log::shouldHaveReceived('info')->withArgs(fn (string $message, array $context): bool => $context['event'] === 'PlatformAdminRevoked'
        && $context['user_id'] === $user->id
        && $context['reason'] === 'Left the operations team')
        ->once();
});

test('the flag is not revoked without a reason', function () {
    $user = User::factory()->platformAdmin()->create();

    $this->artisan('platform-admin:revoke', ['email' => $user->email])
        ->assertFailed();

    expect($user->refresh()->is_platform_admin)->toBeTrue();
});

test('revoking a user who is not a platform admin logs nothing', function () {
    Log::spy();

    $user = User::factory()->create();

    $this->artisan('platform-admin:revoke', ['email' => $user->email, '--reason' => 'Left the operations team'])
        ->expectsOutputToContain('The user is not a platform admin.')
        ->assertSuccessful();

    Log::shouldNotHaveReceived('info');
});
