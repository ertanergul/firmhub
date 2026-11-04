<?php

namespace App\Enums;

/**
 * What a company role allows (0.2 §7); the mapping lives in CompanyRole::permissions().
 *
 * Phase 2 covers the company itself (0.2 §7.1); site, content and message permissions
 * are added with Phase 3.
 */
enum CompanyPermission: string
{
    case ViewCompany = 'company.view';
    case UpdateCompany = 'company.update';
    case ChangeIndustry = 'company.change-industry';
    case CompleteOnboarding = 'company.complete-onboarding';
    case DeleteCompany = 'company.delete';
}
