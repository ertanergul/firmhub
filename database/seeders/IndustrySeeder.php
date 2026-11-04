<?php

namespace Database\Seeders;

use App\Models\Industry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IndustrySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * The platform's industries in display order; their labels live in lang/{locale}/industries.php.
     *
     * @var array<int, string>
     */
    public const SLUGS = ['retail', 'legal', 'other'];

    /**
     * Add missing industries and keep the display order; safe to run on every deployment.
     *
     * Existing industries keep their active state, so a deactivated industry stays hidden.
     */
    public function run(): void
    {
        foreach (self::SLUGS as $position => $slug) {
            Industry::query()->updateOrCreate(['slug' => $slug], ['sort_order' => $position + 1]);
        }
    }
}
