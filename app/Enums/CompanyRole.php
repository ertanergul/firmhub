<?php

namespace App\Enums;

/**
 * A member's role in a company (0.2 §6); one role per membership.
 */
enum CompanyRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Editor = 'editor';
    case Viewer = 'viewer';

    /**
     * The role's rank; nobody may assign a role ranked above their own (0.2 §6.1, RM-3).
     */
    public function rank(): int
    {
        return match ($this) {
            self::Owner => 4,
            self::Admin => 3,
            self::Editor => 2,
            self::Viewer => 1,
        };
    }

    /**
     * The permissions the role grants (0.2 §7).
     *
     * @return array<int, CompanyPermission>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::Owner => CompanyPermission::cases(),
            self::Admin => [
                CompanyPermission::ViewCompany,
                CompanyPermission::UpdateCompany,
                CompanyPermission::ChangeIndustry,
                CompanyPermission::CompleteOnboarding,
            ],
            self::Editor, self::Viewer => [
                CompanyPermission::ViewCompany,
            ],
        };
    }

    /**
     * Determine whether the role grants the permission.
     */
    public function grants(CompanyPermission $permission): bool
    {
        return in_array($permission, $this->permissions(), true);
    }
}
