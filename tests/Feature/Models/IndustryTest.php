<?php

use App\Actions\Localization\UpdateLocalePreference;
use App\Models\Industry;
use Database\Seeders\IndustrySeeder;
use Illuminate\Support\Facades\Lang;

test('the seeder adds the platform industries in display order and can run again', function () {
    $this->seed(IndustrySeeder::class);
    $this->seed(IndustrySeeder::class);

    expect(Industry::query()->offered()->pluck('slug')->all())->toBe(['retail', 'legal', 'other']);
});

test('the seeder keeps deactivated industries hidden', function () {
    Industry::factory()->inactive()->create(['slug' => 'legal']);

    $this->seed(IndustrySeeder::class);

    expect(Industry::query()->offered()->pluck('slug')->all())->toBe(['retail', 'other'])
        ->and(Industry::query()->count())->toBe(3);
});

test('only active industries are offered, in display order', function () {
    Industry::factory()->create(['slug' => 'second', 'sort_order' => 2]);
    Industry::factory()->create(['slug' => 'first', 'sort_order' => 1]);
    Industry::factory()->inactive()->create(['slug' => 'hidden', 'sort_order' => 0]);

    expect(Industry::query()->offered()->pluck('slug')->all())->toBe(['first', 'second']);
});

test('industries are labelled in the current language', function () {
    $industry = Industry::factory()->create(['slug' => 'legal']);

    app()->setLocale('tr');

    expect($industry->label())->toBe('Hukuk Hizmetleri');
});

test('every platform industry has a label in every language', function () {
    foreach (UpdateLocalePreference::supportedLocales() as $locale) {
        $missing = array_filter(IndustrySeeder::SLUGS, fn (string $slug): bool => ! Lang::has("industries.{$slug}", $locale, false));

        expect(array_values($missing))->toBe([], "lang/{$locale}/industries.php is missing industries");
    }
});
