<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        if (app()->environment('testing')) {
            return;
        }

        $now = now();

        foreach (['admin', 'author', 'user'] as $roleName) {
            DB::table('roles')->updateOrInsert(
                ['role_name' => $roleName],
                ['role_name' => $roleName]
            );
        }

        $roleIds = DB::table('roles')
            ->whereIn('role_name', ['admin', 'author', 'user'])
            ->pluck('id_role', 'role_name');

        $users = [
            [
                'name' => 'Demo Admin',
                'email' => 'admin@bookstore.local',
                'phone_number' => '+10000000001',
                'balance' => 50000.00,
                'id_role' => $roleIds['admin'],
            ],
            [
                'name' => 'Demo Author User',
                'email' => 'author@bookstore.local',
                'phone_number' => '+10000000002',
                'balance' => 15000.00,
                'id_role' => $roleIds['author'],
            ],
            [
                'name' => 'Demo Reader',
                'email' => 'user@bookstore.local',
                'phone_number' => '+10000000003',
                'balance' => 3000.00,
                'id_role' => $roleIds['user'],
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                array_merge($user, [
                    'password' => Hash::make('Password123!'),
                    'email_verified_at' => $now,
                    'registration_date' => $now,
                    'remember_token' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }

        $authorUserId = (int) DB::table('users')
            ->where('email', 'author@bookstore.local')
            ->value('id_users');

        DB::table('authors')->updateOrInsert(
            ['id_users' => $authorUserId],
            [
                'author_name' => 'Demo House Author',
                'biography' => 'Demo author profile created by migration.',
            ]
        );

        $authorId = (int) DB::table('authors')
            ->where('id_users', $authorUserId)
            ->value('id_author');

        $publisherNames = [
            'Seeded Press Alpha',
            'Seeded Press Beta',
            'Seeded Press Gamma',
            'Seeded Press Delta',
        ];

        foreach ($publisherNames as $publisherName) {
            DB::table('publishers')->updateOrInsert(
                ['publisher_name' => $publisherName],
                ['publisher_name' => $publisherName]
            );
        }

        $publisherIds = DB::table('publishers')
            ->whereIn('publisher_name', $publisherNames)
            ->pluck('id_publishers', 'publisher_name');

        $genreNames = [
            'Seeded Fiction',
            'Seeded Mystery',
            'Seeded Science',
            'Seeded Fantasy',
            'Seeded History',
        ];

        foreach ($genreNames as $genreName) {
            DB::table('genres')->updateOrInsert(
                ['genre_name' => $genreName],
                ['genre_name' => $genreName]
            );
        }

        $genreIds = DB::table('genres')
            ->whereIn('genre_name', $genreNames)
            ->pluck('id_genre', 'genre_name');

        $books = [
            ['title' => 'Seeded Book 01', 'price' => 410.00, 'discount' => 5, 'stock' => 8, 'date' => '2026-01-05', 'pages' => 180, 'rating' => 4.10, 'publisher' => 'Seeded Press Alpha', 'genres' => ['Seeded Fiction']],
            ['title' => 'Seeded Book 02', 'price' => 430.00, 'discount' => 10, 'stock' => 11, 'date' => '2026-01-12', 'pages' => 192, 'rating' => 4.20, 'publisher' => 'Seeded Press Beta', 'genres' => ['Seeded Mystery']],
            ['title' => 'Seeded Book 03', 'price' => 450.00, 'discount' => 0, 'stock' => 7, 'date' => '2026-01-19', 'pages' => 205, 'rating' => 4.00, 'publisher' => 'Seeded Press Gamma', 'genres' => ['Seeded Science']],
            ['title' => 'Seeded Book 04', 'price' => 470.00, 'discount' => 15, 'stock' => 9, 'date' => '2026-01-26', 'pages' => 214, 'rating' => 4.35, 'publisher' => 'Seeded Press Delta', 'genres' => ['Seeded Fantasy']],
            ['title' => 'Seeded Book 05', 'price' => 490.00, 'discount' => 8, 'stock' => 10, 'date' => '2026-02-02', 'pages' => 228, 'rating' => 4.45, 'publisher' => 'Seeded Press Alpha', 'genres' => ['Seeded History']],
            ['title' => 'Seeded Book 06', 'price' => 510.00, 'discount' => 12, 'stock' => 6, 'date' => '2026-02-09', 'pages' => 240, 'rating' => 4.30, 'publisher' => 'Seeded Press Beta', 'genres' => ['Seeded Fiction', 'Seeded Mystery']],
            ['title' => 'Seeded Book 07', 'price' => 530.00, 'discount' => 20, 'stock' => 12, 'date' => '2026-02-16', 'pages' => 256, 'rating' => 4.55, 'publisher' => 'Seeded Press Gamma', 'genres' => ['Seeded Science', 'Seeded Fantasy']],
            ['title' => 'Seeded Book 08', 'price' => 550.00, 'discount' => 7, 'stock' => 5, 'date' => '2026-02-23', 'pages' => 268, 'rating' => 4.25, 'publisher' => 'Seeded Press Delta', 'genres' => ['Seeded Mystery', 'Seeded History']],
            ['title' => 'Seeded Book 09', 'price' => 570.00, 'discount' => 18, 'stock' => 13, 'date' => '2026-03-02', 'pages' => 284, 'rating' => 4.60, 'publisher' => 'Seeded Press Alpha', 'genres' => ['Seeded Fantasy']],
            ['title' => 'Seeded Book 10', 'price' => 590.00, 'discount' => 9, 'stock' => 8, 'date' => '2026-03-09', 'pages' => 298, 'rating' => 4.40, 'publisher' => 'Seeded Press Beta', 'genres' => ['Seeded Science']],
            ['title' => 'Seeded Book 11', 'price' => 610.00, 'discount' => 14, 'stock' => 9, 'date' => '2026-03-16', 'pages' => 310, 'rating' => 4.52, 'publisher' => 'Seeded Press Gamma', 'genres' => ['Seeded Fiction']],
            ['title' => 'Seeded Book 12', 'price' => 630.00, 'discount' => 6, 'stock' => 14, 'date' => '2026-03-23', 'pages' => 322, 'rating' => 4.33, 'publisher' => 'Seeded Press Delta', 'genres' => ['Seeded Mystery']],
            ['title' => 'Seeded Book 13', 'price' => 650.00, 'discount' => 0, 'stock' => 10, 'date' => '2026-03-30', 'pages' => 336, 'rating' => 4.18, 'publisher' => 'Seeded Press Alpha', 'genres' => ['Seeded History', 'Seeded Fiction']],
            ['title' => 'Seeded Book 14', 'price' => 670.00, 'discount' => 11, 'stock' => 7, 'date' => '2026-04-01', 'pages' => 348, 'rating' => 4.48, 'publisher' => 'Seeded Press Beta', 'genres' => ['Seeded Fantasy', 'Seeded Science']],
            ['title' => 'Seeded Book 15', 'price' => 690.00, 'discount' => 13, 'stock' => 6, 'date' => '2026-04-03', 'pages' => 360, 'rating' => 4.62, 'publisher' => 'Seeded Press Gamma', 'genres' => ['Seeded Science', 'Seeded History']],
            ['title' => 'Seeded Book 16', 'price' => 710.00, 'discount' => 17, 'stock' => 11, 'date' => '2026-04-05', 'pages' => 372, 'rating' => 4.70, 'publisher' => 'Seeded Press Delta', 'genres' => ['Seeded Mystery', 'Seeded Fantasy']],
            ['title' => 'Seeded Book 17', 'price' => 730.00, 'discount' => 4, 'stock' => 9, 'date' => '2026-04-07', 'pages' => 388, 'rating' => 4.21, 'publisher' => 'Seeded Press Alpha', 'genres' => ['Seeded Fiction']],
            ['title' => 'Seeded Book 18', 'price' => 750.00, 'discount' => 16, 'stock' => 8, 'date' => '2026-04-09', 'pages' => 402, 'rating' => 4.57, 'publisher' => 'Seeded Press Beta', 'genres' => ['Seeded History']],
            ['title' => 'Seeded Book 19', 'price' => 770.00, 'discount' => 19, 'stock' => 12, 'date' => '2026-04-11', 'pages' => 418, 'rating' => 4.74, 'publisher' => 'Seeded Press Gamma', 'genres' => ['Seeded Fantasy', 'Seeded Fiction']],
            ['title' => 'Seeded Book 20', 'price' => 790.00, 'discount' => 10, 'stock' => 15, 'date' => '2026-04-13', 'pages' => 430, 'rating' => 4.50, 'publisher' => 'Seeded Press Delta', 'genres' => ['Seeded Science', 'Seeded Mystery']],
        ];

        foreach ($books as $book) {
            DB::table('books')->updateOrInsert(
                ['book_name' => $book['title']],
                [
                    'cover_image' => null,
                    'digital_file_path' => null,
                    'digital_file_original_name' => null,
                    'price' => $book['price'],
                    'discount_percent' => $book['discount'],
                    'stock_quantity' => $book['stock'],
                    'is_preorder' => false,
                    'publication_date' => $book['date'],
                    'number_of_pages' => $book['pages'],
                    'average_rating' => $book['rating'],
                    'description' => 'Seeded demo book generated by migration.',
                    'id_author' => $authorId,
                    'id_publishers' => $publisherIds[$book['publisher']],
                ]
            );

            $bookId = (int) DB::table('books')
                ->where('book_name', $book['title'])
                ->value('id_books');

            foreach ($book['genres'] as $genreName) {
                DB::table('book_genres')->updateOrInsert(
                    [
                        'id_books' => $bookId,
                        'id_genre' => $genreIds[$genreName],
                    ],
                    [
                        'id_books' => $bookId,
                        'id_genre' => $genreIds[$genreName],
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        if (app()->environment('testing')) {
            return;
        }

        $bookTitles = [
            'Seeded Book 01', 'Seeded Book 02', 'Seeded Book 03', 'Seeded Book 04', 'Seeded Book 05',
            'Seeded Book 06', 'Seeded Book 07', 'Seeded Book 08', 'Seeded Book 09', 'Seeded Book 10',
            'Seeded Book 11', 'Seeded Book 12', 'Seeded Book 13', 'Seeded Book 14', 'Seeded Book 15',
            'Seeded Book 16', 'Seeded Book 17', 'Seeded Book 18', 'Seeded Book 19', 'Seeded Book 20',
        ];

        $bookIds = DB::table('books')
            ->whereIn('book_name', $bookTitles)
            ->pluck('id_books');

        if ($bookIds->isNotEmpty()) {
            DB::table('book_genres')->whereIn('id_books', $bookIds)->delete();
            DB::table('books')->whereIn('id_books', $bookIds)->delete();
        }

        $authorUserId = DB::table('users')
            ->where('email', 'author@bookstore.local')
            ->value('id_users');

        if ($authorUserId) {
            DB::table('authors')->where('id_users', $authorUserId)->delete();
        }

        DB::table('users')->whereIn('email', [
            'admin@bookstore.local',
            'author@bookstore.local',
            'user@bookstore.local',
        ])->delete();

        DB::table('genres')->whereIn('genre_name', [
            'Seeded Fiction',
            'Seeded Mystery',
            'Seeded Science',
            'Seeded Fantasy',
            'Seeded History',
        ])->delete();

        DB::table('publishers')->whereIn('publisher_name', [
            'Seeded Press Alpha',
            'Seeded Press Beta',
            'Seeded Press Gamma',
            'Seeded Press Delta',
        ])->delete();
    }
};
