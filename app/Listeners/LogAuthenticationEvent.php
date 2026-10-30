<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Events\RecoveryCodeReplaced;
use Laravel\Fortify\Events\RecoveryCodesGenerated;
use Laravel\Fortify\Events\TwoFactorAuthenticationChallenged;
use Laravel\Fortify\Events\TwoFactorAuthenticationConfirmed;
use Laravel\Fortify\Events\TwoFactorAuthenticationDisabled;
use Laravel\Fortify\Events\TwoFactorAuthenticationEnabled;
use Laravel\Fortify\Events\TwoFactorAuthenticationFailed;

/**
 * Records authentication events without credentials, codes, tokens, email addresses or IP addresses (0.7 SEC-AUTH-8, SEC-LOG-1).
 */
class LogAuthenticationEvent
{
    /**
     * Handle the event.
     */
    public function handle(
        Login|Logout|Failed|Lockout|TwoFactorAuthenticationChallenged|TwoFactorAuthenticationFailed|TwoFactorAuthenticationEnabled|TwoFactorAuthenticationConfirmed|TwoFactorAuthenticationDisabled|RecoveryCodesGenerated|RecoveryCodeReplaced $event,
    ): void {
        Log::log($this->level($event), 'Authentication event.', [
            'event' => class_basename($event),
            'guard' => $event instanceof Login || $event instanceof Logout || $event instanceof Failed ? $event->guard : null,
            'user_id' => $this->userId($event),
        ]);
    }

    /**
     * Determine the log level for the event.
     */
    private function level(object $event): string
    {
        return $event instanceof Failed || $event instanceof Lockout || $event instanceof TwoFactorAuthenticationFailed
            ? 'warning'
            : 'info';
    }

    /**
     * Resolve the identifier of the user the event refers to, if any.
     */
    private function userId(object $event): mixed
    {
        $user = $event instanceof Lockout ? null : ($event->user ?? null);

        return $user instanceof Authenticatable ? $user->getAuthIdentifier() : null;
    }
}
