<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Signature('platform-admin:revoke {email : The email address of the user} {--reason= : Why the platform admin flag is removed} {--actor= : The email address of the user who requested the change}')]
#[Description('Revoke the platform admin flag from a user')]
class RevokePlatformAdmin extends PlatformAdminCommand
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        return $this->changePlatformAdminStatus(false);
    }
}
