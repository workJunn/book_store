<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->unsignedBigInteger('id_users')->nullable()->unique()->after('id_author');
            $table->foreign('id_users')->references('id_users')->on('users')->nullOnDelete();
        });

        Schema::table('books', function (Blueprint $table) {
            $table->unsignedTinyInteger('discount_percent')->default(0)->after('price');
        });

        Schema::create('partner_applications', function (Blueprint $table) {
            $table->id('id_partner_application');
            $table->unsignedBigInteger('id_users');
            $table->string('pen_name', 50);
            $table->text('biography');
            $table->text('experience_summary')->nullable();
            $table->string('portfolio_url', 255)->nullable();
            $table->string('payment_method', 20);
            $table->string('status', 20)->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('id_users')->references('id_users')->on('users')->cascadeOnDelete();
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_applications');

        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('discount_percent');
        });

        Schema::table('authors', function (Blueprint $table) {
            $table->dropForeign(['id_users']);
            $table->dropUnique(['id_users']);
            $table->dropColumn('id_users');
        });
    }
};
