<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\CompanyLocationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * A company address (0.3 §5.6); the MVP uses one primary location per company.
 *
 * The company is set through the relationship, never mass-assigned (0.2 OR-5).
 *
 * @property string $id
 * @property string $company_id
 * @property string|null $label
 * @property string $address_line1
 * @property string|null $address_line2
 * @property string|null $postal_code
 * @property string $city
 * @property string|null $region
 * @property string $country_code
 * @property string|null $phone
 * @property string|null $email
 * @property bool $is_primary
 * @property int $sort_order
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
#[Fillable(['label', 'address_line1', 'address_line2', 'postal_code', 'city', 'region', 'country_code', 'phone', 'email', 'is_primary', 'sort_order'])]
class CompanyLocation extends Model
{
    /** @use HasFactory<CompanyLocationFactory> */
    use HasFactory, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Store the location's email in lower case, as the database requires (0.3 C-8).
     *
     * @return Attribute<string|null, string|null>
     */
    protected function email(): Attribute
    {
        return Attribute::make(set: fn (?string $value): ?string => $value === null ? null : Str::lower($value));
    }

    /**
     * Get the company the location belongs to.
     *
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
