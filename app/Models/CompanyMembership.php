<?php

namespace App\Models;

use App\Enums\CompanyRole;
use Database\Factories\CompanyMembershipFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * A user's role in a company (0.2 §3, 0.3 §5.4).
 *
 * Nothing is mass-assignable: the company, the user and the role are always set
 * explicitly by server-side code (0.2 OR-5, AR-8).
 *
 * @property string $id
 * @property string $company_id
 * @property int $user_id
 * @property CompanyRole $role
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class CompanyMembership extends Model
{
    /** @use HasFactory<CompanyMembershipFactory> */
    use HasFactory, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => CompanyRole::class,
        ];
    }

    /**
     * Get the company the membership belongs to.
     *
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the member.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
