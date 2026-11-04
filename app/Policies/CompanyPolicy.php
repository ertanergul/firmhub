<?php

namespace App\Policies;

use App\Enums\CompanyPermission;
use App\Models\Company;
use App\Models\CompanyMembership;
use App\Models\User;

/**
 * Decides what a user may do with a company, from their membership role (0.2 §7.1).
 *
 * The role is read from the database on every check and never cached in the session
 * (0.2 RM-7); users without a membership in the company are always denied.
 */
class CompanyPolicy
{
    /**
     * Determine whether the user can see the company dashboard.
     */
    public function view(User $user, Company $company): bool
    {
        return $this->grants($user, $company, CompanyPermission::ViewCompany);
    }

    /**
     * Determine whether the user can edit the company profile.
     */
    public function update(User $user, Company $company): bool
    {
        return $this->grants($user, $company, CompanyPermission::UpdateCompany);
    }

    /**
     * Determine whether the user can change the company's industry.
     */
    public function changeIndustry(User $user, Company $company): bool
    {
        return $this->grants($user, $company, CompanyPermission::ChangeIndustry);
    }

    /**
     * Determine whether the user can complete the onboarding steps.
     */
    public function completeOnboarding(User $user, Company $company): bool
    {
        return $this->grants($user, $company, CompanyPermission::CompleteOnboarding);
    }

    /**
     * Determine whether the user can delete the company.
     */
    public function delete(User $user, Company $company): bool
    {
        return $this->grants($user, $company, CompanyPermission::DeleteCompany);
    }

    /**
     * Determine whether the user's role in the company grants the permission.
     */
    private function grants(User $user, Company $company, CompanyPermission $permission): bool
    {
        $membership = CompanyMembership::query()->whereBelongsTo($user)->whereBelongsTo($company)->first();

        return $membership?->role->grants($permission) ?? false;
    }
}
