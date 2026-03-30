<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('books', 'cover_image')) {
            return;
        }

        Schema::table('books', function (Blueprint $table) {
            $table->string('cover_image', 255)->nullable()->after('book_name');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('books', 'cover_image')) {
            return;
        }

        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('cover_image');
        });
    }
};
