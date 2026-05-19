<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_votes', function (Blueprint $table) {
            $table->id('id_review_vote');
            $table->unsignedBigInteger('id_reviews');
            $table->unsignedBigInteger('id_users');
            $table->boolean('is_helpful');
            $table->timestamps();

            $table->unique(['id_reviews', 'id_users']);
            $table->foreign('id_reviews')->references('id_reviews')->on('reviews')->cascadeOnDelete();
            $table->foreign('id_users')->references('id_users')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_votes');
    }
};
