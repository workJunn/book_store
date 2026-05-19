<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('reviews') || DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('ALTER TABLE reviews DROP CONSTRAINT IF EXISTS reviews_id_books_id_users_unique');
        DB::statement('ALTER TABLE reviews DROP CONSTRAINT IF EXISTS reviews_id_books_id_users_key');
    }

    public function down(): void
    {
        //
    }
};
