<?php

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Events\RecoveryCodeReplaced;
use Laravel\Fortify\Events\TwoFactorAuthenticationFailed;

test('successful logins are logged with the user id only', function () {
    Log::spy();

    $user = User::factory()->create();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    Log::shouldHaveReceived('log')->withArgs(fn (string $level, string $message, array $context): bool => $level === 'info'
        && $context === ['event' => 'Login', 'guard' => 'web', 'user_id' => $user->id])
        ->once();
});

test('failed logins are logged as warnings without credentials', function () {
    Log::spy();

    $user = User::factory()->create();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    Log::shouldHaveReceived('log')->withArgs(fn (string $level, string $message, array $context): bool => $level === 'warning'
        && $context === ['event' => 'Failed', 'guard' => 'web', 'user_id' => $user->id])
        ->once();
});

test('failed two factor attempts are logged as warnings', function () {
    Log::spy();

    $user = User::factory()->create();

    event(new TwoFactorAuthenticationFailed($user));

    Log::shouldHaveReceived('log')->withArgs(fn (string $level, string $message, array $context): bool => $level === 'warning'
        && $context === ['event' => 'TwoFactorAuthenticationFailed', 'guard' => null, 'user_id' => $user->id])
        ->once();
});

test('replaced recovery codes are never logged', function () {
    Log::spy();

    $user = User::factory()->create();

    event(new RecoveryCodeReplaced($user, 'secret-recovery-code'));

    Log::shouldHaveReceived('log')->withArgs(fn (string $level, string $message, array $context): bool => $context === ['event' => 'RecoveryCodeReplaced', 'guard' => null, 'user_id' => $user->id]
        && ! str_contains(json_encode($context), 'secret-recovery-code'))
        ->once();
});
