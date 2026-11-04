<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the company addresses (0.3 §5.6).
     *
     * The MVP manages one primary location per company; the table already allows the
     * multiple locations of Phase 4 (0.3 D-2).
     */
    public function up(): void
    {
        Schema::create('company_locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained()->cascadeOnDelete();
            $table->text('label')->nullable();
            $table->text('address_line1');
            $table->text('address_line2')->nullable();
            $table->text('postal_code')->nullable();
            $table->text('city');
            $table->text('region')->nullable();
            $table->char('country_code', 2);
            $table->text('phone')->nullable();
            $table->text('email')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestampsTz();

            $table->index(['company_id', 'sort_order']);
        });

        DB::statement("ALTER TABLE company_locations ADD CONSTRAINT company_locations_country_code_format CHECK (country_code ~ '^[A-Z]{2}$')");
        DB::statement('ALTER TABLE company_locations ADD CONSTRAINT company_locations_email_lowercase CHECK (email = lower(email))');
        DB::statement('CREATE UNIQUE INDEX company_locations_one_primary ON company_locations (company_id) WHERE is_primary');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_locations');
    }
};
