<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (! Schema::hasColumn('books', 'digital_file_path')) {
                $table->string('digital_file_path', 255)->nullable()->after('cover_image');
            }

            if (! Schema::hasColumn('books', 'digital_file_original_name')) {
                $table->string('digital_file_original_name', 255)->nullable()->after('digital_file_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'digital_file_original_name')) {
                $table->dropColumn('digital_file_original_name');
            }

            if (Schema::hasColumn('books', 'digital_file_path')) {
                $table->dropColumn('digital_file_path');
            }
        });
    }
};
