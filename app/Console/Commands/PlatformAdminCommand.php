<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Shared flow of the commands that grant or revoke the platform admin flag (0.2 §10, 0.7 SEC-ADMIN-2).
 */
abstract class PlatformAdminCommand extends Command
{
    /**
     * Grant or revoke the platform admin flag of the user given by the email argument.
     */
    protected function changePlatformAdminStatus(bool $isPlatformAdmin): int
    {
        $reason = $this->textInput($this->option('reason'));

        if ($reason === '') {
            $this->components->error('The --reason option is required.');

            return self::FAILURE;
        }

        $user = $this->findUserByEmail($this->textInput($this->argument('email')));

        if ($user === null) {
            $this->components->error('No user exists with the given email address.');

            return self::FAILURE;
        }

        $actorEmail = $this->textInput($this->option('actor'));
        $actor = $actorEmail === '' ? null : $this->findUserByEmail($actorEmail);

        if ($actorEmail !== '' && $actor === null) {
            $this->components->error('No user exists with the given --actor email address.');

            return self::FAILURE;
        }

        if ($user->is_platform_admin === $isPlatformAdmin) {
            $this->components->info($isPlatformAdmin ? 'The user is already a platform admin.' : 'The user is not a platform admin.');

            return self::SUCCESS;
        }

        $user->forceFill(['is_platform_admin' => $isPlatformAdmin])->save();

        Log::info($isPlatformAdmin ? 'Platform admin granted.' : 'Platform admin revoked.', [
            'event' => $isPlatformAdmin ? 'PlatformAdminGranted' : 'PlatformAdminRevoked',
            'actor' => $this->operatingSystemUser(),
            'actor_user_id' => $actor?->id,
            'user_id' => $user->id,
            'reason' => $reason,
        ]);

        $this->components->info($isPlatformAdmin ? 'Platform admin granted.' : 'Platform admin revoked.');

        return self::SUCCESS;
    }

    /**
     * Find a user by email; emails are stored in lowercase (0.3 D-6).
     */
    private function findUserByEmail(string $email): ?User
    {
        return User::query()->where('email', Str::lower($email))->first();
    }

    /**
     * Normalize a console argument or option to trimmed text; a missing value becomes an empty string.
     */
    private function textInput(mixed $value): string
    {
        return is_string($value) ? trim($value) : '';
    }

    /**
     * Resolve the operating system user running the command.
     */
    private function operatingSystemUser(): string
    {
        $user = function_exists('posix_geteuid') ? posix_getpwuid(posix_geteuid()) : false;

        return $user === false ? get_current_user() : $user['name'];
    }
}
