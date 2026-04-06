<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Publisher;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $publishers = collect([
            'Эксмо' => Publisher::query()->firstOrCreate(['publisher_name' => 'Эксмо']),
            'АСТ' => Publisher::query()->firstOrCreate(['publisher_name' => 'АСТ']),
            'Азбука' => Publisher::query()->firstOrCreate(['publisher_name' => 'Азбука']),
        ]);

        $genres = collect([
            'Фантастика' => Genre::query()->firstOrCreate(['genre_name' => 'Фантастика']),
            'Роман' => Genre::query()->firstOrCreate(['genre_name' => 'Роман']),
            'Детектив' => Genre::query()->firstOrCreate(['genre_name' => 'Детектив']),
            'Научпоп' => Genre::query()->firstOrCreate(['genre_name' => 'Научпоп']),
        ]);

        $authors = collect([
            'Анна Лаврова' => Author::query()->firstOrCreate(
                ['author_name' => 'Анна Лаврова'],
                ['biography' => 'Пишет современные городские романы и короткую прозу.']
            ),
            'Илья Ветров' => Author::query()->firstOrCreate(
                ['author_name' => 'Илья Ветров'],
                ['biography' => 'Автор приключенческой фантастики и историй о будущем.']
            ),
            'Марина Соколова' => Author::query()->firstOrCreate(
                ['author_name' => 'Марина Соколова'],
                ['biography' => 'Работает на стыке детектива, драмы и психологической прозы.']
            ),
        ]);

        $books = [
            [
                'book_name' => 'Семь дней до рассвета',
                'price' => 790.00,
                'discount_percent' => 10,
                'stock_quantity' => 14,
                'publication_date' => now()->subDays(1)->toDateString(),
                'number_of_pages' => 320,
                'average_rating' => 4.70,
                'description' => 'Свежий роман о выборе, переменах и новой точке опоры.',
                'id_author' => $authors['Анна Лаврова']->getKey(),
                'id_publishers' => $publishers['Эксмо']->getKey(),
                'genres' => [$genres['Роман']->getKey()],
            ],
            [
                'book_name' => 'Код северного ветра',
                'price' => 860.00,
                'discount_percent' => 15,
                'stock_quantity' => 9,
                'publication_date' => now()->subDays(3)->toDateString(),
                'number_of_pages' => 368,
                'average_rating' => 4.85,
                'description' => 'Новая фантастическая история о сигналах, которые меняют город.',
                'id_author' => $authors['Илья Ветров']->getKey(),
                'id_publishers' => $publishers['АСТ']->getKey(),
                'genres' => [$genres['Фантастика']->getKey()],
            ],
            [
                'book_name' => 'Последний этаж тишины',
                'price' => 720.00,
                'discount_percent' => 0,
                'stock_quantity' => 11,
                'publication_date' => now()->subDays(5)->toDateString(),
                'number_of_pages' => 288,
                'average_rating' => 4.40,
                'description' => 'Свежий психологический детектив о доме, в котором никто ничего не слышал.',
                'id_author' => $authors['Марина Соколова']->getKey(),
                'id_publishers' => $publishers['Азбука']->getKey(),
                'genres' => [$genres['Детектив']->getKey()],
            ],
            [
                'book_name' => 'Архив лунных станций',
                'price' => 910.00,
                'discount_percent' => 20,
                'stock_quantity' => 7,
                'publication_date' => now()->subDays(6)->toDateString(),
                'number_of_pages' => 404,
                'average_rating' => 4.90,
                'description' => 'Фантастический роман о данных, памяти и дальних экспедициях.',
                'id_author' => $authors['Илья Ветров']->getKey(),
                'id_publishers' => $publishers['Эксмо']->getKey(),
                'genres' => [$genres['Фантастика']->getKey(), $genres['Научпоп']->getKey()],
            ],
            [
                'book_name' => 'Город, который ждет снег',
                'price' => 680.00,
                'discount_percent' => 5,
                'stock_quantity' => 13,
                'publication_date' => now()->subDays(12)->toDateString(),
                'number_of_pages' => 256,
                'average_rating' => 4.10,
                'description' => 'Более ранний роман для каталога и других разделов.',
                'id_author' => $authors['Анна Лаврова']->getKey(),
                'id_publishers' => $publishers['АСТ']->getKey(),
                'genres' => [$genres['Роман']->getKey()],
            ],
        ];

        foreach ($books as $bookData) {
            $genreIds = $bookData['genres'];
            unset($bookData['genres']);

            $book = Book::query()->updateOrCreate(
                ['book_name' => $bookData['book_name']],
                $bookData
            );

            $book->genres()->sync($genreIds);
        }
    }
}
