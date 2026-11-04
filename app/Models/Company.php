<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * The tenant: a business and its information (0.2 §2, 0.3 §5.3).
 *
 * The industry, onboarding and suspension fields are not mass-assignable; memberships
 * are created through relationships, never from request data (0.2 OR-5).
 *
 * @property string $id
 * @property string $name
 * @property int $industry_id
 * @property string $contact_email
 * @property string|null $phone
 * @property string|null $whatsapp_phone
 * @property string|null $legal_name
 * @property string|null $tax_number
 * @property string|null $tax_office
 * @property string|null $trade_registry_number
 * @property int|null $established_year
 * @property CarbonImmutable|null $onboarding_completed_at
 * @property CarbonImmutable|null $suspended_at
 * @property string|null $suspension_reason
 * @property CarbonImmutable|null $deleted_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
#[Fillable(['name', 'contact_email', 'phone', 'whatsapp_phone', 'legal_name', 'tax_number', 'tax_office', 'trade_registry_number', 'established_year'])]
class Company extends Model
{
    /** @use HasFactory<CompanyFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'established_year' => 'integer',
            'onboarding_completed_at' => 'datetime',
            'suspended_at' => 'datetime',
        ];
    }

    /**
     * Store the contact email in lower case, as the database requires (0.3 C-8).
     *
     * @return Attribute<string, string>
     */
    protected function contactEmail(): Attribute
    {
        return Attribute::make(set: fn (string $value): string => Str::lower($value));
    }

    /**
     * Get the company's industry.
     *
     * @return BelongsTo<Industry, $this>
     */
    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    /**
     * Get the company's locations.
     *
     * @return HasMany<CompanyLocation, $this>
     */
    public function locations(): HasMany
    {
        return $this->hasMany(CompanyLocation::class);
    }

    /**
     * Get the company's primary location, shown on its site (0.5 §11).
     *
     * @return HasOne<CompanyLocation, $this>
     */
    public function primaryLocation(): HasOne
    {
        return $this->hasOne(CompanyLocation::class)->where('is_primary', true);
    }

    /**
     * Get the company's memberships.
     *
     * @return HasMany<CompanyMembership, $this>
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(CompanyMembership::class);
    }
}
