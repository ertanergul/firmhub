<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the platform-managed industry catalog (0.3 §5.2).
     *
     * Labels come from lang/{locale}/industries.php keyed by slug (0.3 D-8), so the slug
     * is restricted to lowercase words joined by hyphens.
     */
    public function up(): void
    {
        Schema::create('industries', function (Blueprint $table) {
            $table->id();
            $table->text('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order');
            $table->timestampsTz();
        });

        DB::statement("ALTER TABLE industries ADD CONSTRAINT industries_slug_format CHECK (slug ~ '^[a-z0-9]+(-[a-z0-9]+)*$')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('industries');
    }
};
