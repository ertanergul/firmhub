<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the tenant root (0.3 §5.3): a UUIDv7 key used as the route key, a lower-case
     * contact email, optional legal details and the platform-managed suspension.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('name');
            $table->foreignId('industry_id')->index()->constrained()->restrictOnDelete();
            $table->text('contact_email');
            $table->text('phone')->nullable();
            $table->text('whatsapp_phone')->nullable();
            $table->text('legal_name')->nullable();
            $table->text('tax_number')->nullable();
            $table->text('tax_office')->nullable();
            $table->text('trade_registry_number')->nullable();
            $table->smallInteger('established_year')->nullable();
            $table->timestampTz('onboarding_completed_at')->nullable();
            $table->timestampTz('suspended_at')->nullable();
            $table->text('suspension_reason')->nullable();
            $table->softDeletesTz();
            $table->timestampsTz();
        });

        DB::statement('ALTER TABLE companies ADD CONSTRAINT companies_contact_email_lowercase CHECK (contact_email = lower(contact_email))');
        DB::statement('ALTER TABLE companies ADD CONSTRAINT companies_established_year_range CHECK (established_year BETWEEN 1800 AND 2100)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
