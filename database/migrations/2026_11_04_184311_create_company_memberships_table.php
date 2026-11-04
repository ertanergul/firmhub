<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the user ↔ company role relation (0.3 §5.4).
     *
     * During the proof of concept a user belongs to at most one company, so user_id is
     * unique on its own (DECISIONS 2026-11-04), which also indexes the foreign key; replace
     * it with a plain index when users may join several companies. invited_by_user_id is
     * added with invitations (task 2.8).
     */
    public function up(): void
    {
        Schema::create('company_memberships', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('role');
            $table->timestampsTz();

            $table->unique(['company_id', 'user_id']);
            $table->unique('user_id', 'company_memberships_one_company_per_user');
        });

        DB::statement("ALTER TABLE company_memberships ADD CONSTRAINT company_memberships_role_valid CHECK (role IN ('owner', 'admin', 'editor', 'viewer'))");
        DB::statement("CREATE INDEX company_memberships_owners_index ON company_memberships (company_id) WHERE role = 'owner'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_memberships');
    }
};
