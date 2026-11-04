<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\IndustryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A business sector chosen during onboarding (0.3 §5.2).
 *
 * The list is platform-managed (IndustrySeeder) and its labels live in
 * lang/{locale}/industries.php, keyed by slug (0.3 D-8). Industries that are no
 * longer offered are deactivated, never deleted.
 *
 * @property int $id
 * @property string $slug
 * @property bool $is_active
 * @property int $sort_order
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
#[Fillable(['slug', 'is_active', 'sort_order'])]
class Industry extends Model
{
    /** @use HasFactory<IndustryFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Limit the query to the industries offered during onboarding, in display order.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function offered(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get the industry's name in the current language.
     */
    public function label(): string
    {
        return __("industries.{$this->slug}");
    }
}
