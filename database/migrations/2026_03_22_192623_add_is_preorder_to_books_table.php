<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('books', 'is_preorder')) {
            return;
        }

        Schema::table('books', function (Blueprint $table) {
            $table->boolean('is_preorder')->default(false)->after('stock_quantity');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('books', 'is_preorder')) {
            return;
        }

        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('is_preorder');
        });
    }
};
