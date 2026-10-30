<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Normalize stored emails to lowercase and enforce case-insensitive uniqueness (0.3 D-6).
     *
     * Fails with SQLSTATE 23505 if case-variant duplicates exist; the migration runs in a
     * transaction, so no email is changed in that case.
     */
    public function up(): void
    {
        DB::statement('UPDATE users SET email = lower(email) WHERE email <> lower(email)');
        DB::statement('CREATE UNIQUE INDEX users_email_lower_unique ON users (lower(email))');
    }

    /**
     * Reverse the migrations.
     *
     * The original letter case of normalized emails is not restored.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS users_email_lower_unique');
    }
};
