<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id('id_role');
            $table->string('role_name', 50)->unique();
        });

        // users
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_users');
            $table->string('name', 50);
            $table->string('password', 100);
            $table->decimal('balance', 10, 2)->default(0.00);
            $table->timestamp('registration_date')->useCurrent();
            $table->string('email', 50)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->unsignedBigInteger('id_role')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('id_role')->references('id_role')->on('roles');
        });

        // authors
        Schema::create('authors', function (Blueprint $table) {
            $table->id('id_author');
            $table->string('author_name', 50);
            $table->text('biography')->nullable();
        });

        // publishers
        Schema::create('publishers', function (Blueprint $table) {
            $table->id('id_publishers');
            $table->string('publisher_name', 50)->unique();
        });

        // books
        Schema::create('books', function (Blueprint $table) {
            $table->id('id_books');
            $table->string('book_name', 200);
            $table->decimal('price', 10, 2);
            $table->integer('stock_quantity')->default(0);
            $table->date('publication_date')->nullable();
            $table->integer('number_of_pages');
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('id_author')->nullable();
            $table->unsignedBigInteger('id_publishers')->nullable();

            $table->foreign('id_author')->references('id_author')->on('authors');
            $table->foreign('id_publishers')->references('id_publishers')->on('publishers');
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE books ADD CONSTRAINT books_price_check CHECK (price >= 0.00)');
            DB::statement('ALTER TABLE books ADD CONSTRAINT books_stock_quantity_check CHECK (stock_quantity >= 0)');
            DB::statement('ALTER TABLE books ADD CONSTRAINT books_pages_check CHECK (number_of_pages > 0)');
        }

        // genres
        Schema::create('genres', function (Blueprint $table) {
            $table->id('id_genre');
            $table->string('genre_name', 50)->unique();
        });

        // book_genres
        Schema::create('book_genres', function (Blueprint $table) {
            $table->unsignedBigInteger('id_books');
            $table->unsignedBigInteger('id_genre');

            $table->primary(['id_books', 'id_genre']);

            $table->foreign('id_books')->references('id_books')->on('books');
            $table->foreign('id_genre')->references('id_genre')->on('genres');
        });

        // reviews
        Schema::create('reviews', function (Blueprint $table) {
            $table->id('id_reviews');
            $table->unsignedBigInteger('id_books')->nullable();
            $table->unsignedBigInteger('id_users')->nullable();
            $table->integer('rating');
            $table->text('review_text')->nullable();
            $table->timestamp('review_date')->useCurrent();

            $table->unique(['id_books', 'id_users']);

            $table->foreign('id_books')->references('id_books')->on('books');
            $table->foreign('id_users')->references('id_users')->on('users');
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE reviews ADD CONSTRAINT reviews_rating_check CHECK (rating >= 1 AND rating <= 5)');
        }

        // orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id('id_orders');
            $table->unsignedBigInteger('id_users');
            $table->timestamp('order_date')->useCurrent();
            $table->string('status', 50);
            $table->decimal('total_amount', 10, 2);

            $table->foreign('id_users')->references('id_users')->on('users');
        });

        // orders_details
        Schema::create('orders_details', function (Blueprint $table) {
            $table->id('id_orders_details');
            $table->unsignedBigInteger('id_orders');
            $table->unsignedBigInteger('id_books');
            $table->integer('quantity');
            $table->decimal('price_per_item', 10, 2);

            $table->foreign('id_orders')->references('id_orders')->on('orders');
            $table->foreign('id_books')->references('id_books')->on('books');
        });

        // sessions
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id', 255)->primary();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('payload');
            $table->integer('last_activity');

            $table->foreign('user_id')->references('id_users')->on('users')->nullOnDelete();

            $table->index('user_id', 'idx_sessions_user_id');
            $table->index('last_activity', 'idx_sessions_last_activity');
        });

        // cache
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key', 255)->primary();
            $table->text('value');
            $table->integer('expiration');

            $table->index('expiration', 'idx_cache_expiration');
        });

        // cache_locks
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key', 255)->primary();
            $table->string('owner', 255);
            $table->integer('expiration');

            $table->index('expiration', 'idx_cache_locks_expiration');
        });

        // password_reset_tokens
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 255)->primary();
            $table->string('token', 255);
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('orders_details');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('book_genres');
        Schema::dropIfExists('genres');
        Schema::dropIfExists('books');
        Schema::dropIfExists('publishers');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};
