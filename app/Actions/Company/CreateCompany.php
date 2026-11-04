<?php

namespace App\Actions\Company;

use App\Enums\CompanyRole;
use App\Models\Company;
use App\Models\CompanyMembership;
use App\Models\Industry;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Creates a company and makes its creator the Owner, in one transaction (0.1 FR-COMP-01, 0.2 RM-9).
 *
 * Only verified users may create a company (FR-AUTH-02), and during the proof of concept
 * a user belongs to at most one company; the database enforces that rule even for
 * simultaneous requests (DECISIONS 2026-11-04).
 */
class CreateCompany
{
    /**
     * Create the company; its contact email starts as the owner's email.
     *
     * @throws AuthorizationException when the owner's email is not verified
     * @throws ValidationException when the industry is not offered or the owner already has a company
     */
    public function handle(User $owner, string $name, Industry $industry): Company
    {
        if (! $owner->hasVerifiedEmail()) {
            throw new AuthorizationException;
        }

        if (! $industry->is_active) {
            throw ValidationException::withMessages(['industry' => __('pages.company.errors.industry_unavailable')]);
        }

        if ($owner->membership()->exists()) {
            throw $this->alreadyInCompany();
        }

        try {
            return DB::transaction(function () use ($owner, $name, $industry): Company {
                $company = new Company(['name' => $name, 'contact_email' => $owner->email]);
                $company->industry()->associate($industry);
                $company->save();

                $membership = new CompanyMembership;
                $membership->role = CompanyRole::Owner;
                $membership->user()->associate($owner);
                $company->memberships()->save($membership);

                return $company;
            });
        } catch (UniqueConstraintViolationException) {
            throw $this->alreadyInCompany();
        }
    }

    /**
     * The error for a user who already belongs to a company.
     */
    private function alreadyInCompany(): ValidationException
    {
        return ValidationException::withMessages(['company' => __('pages.company.errors.already_in_company')]);
    }
}
