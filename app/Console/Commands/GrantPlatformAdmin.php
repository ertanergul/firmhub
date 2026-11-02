<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Signature('platform-admin:grant {email : The email address of the user} {--reason= : Why the user becomes a platform admin} {--actor= : The email address of the user who requested the change}')]
#[Description('Grant the platform admin flag to a user')]
class GrantPlatformAdmin extends PlatformAdminCommand
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        return $this->changePlatformAdminStatus(true);
    }
}
