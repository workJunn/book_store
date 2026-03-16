<?php

namespace App\Http\Controllers;

class BookController extends Controller
{
    public static function getBooks(): array
    {
        return [
            1 => [
                'id' => 1,
                'title' => 'Мастер и Маргарита',
                'author' => 'Михаил Булгаков',
                'category' => 'Фантастика',
                'price' => 890,
                'old_price' => 1200,
                'rating' => 5,
                'reviews' => 124,
                'badge' => 'Новинка',
                'badge_type' => 'new',
                'image' => 'https://via.placeholder.com/500x700/667eea/ffffff?text=Мастер+и+Маргарита',
                'description' => 'Роман о дьяволе, посетившем Москву 1930-х годов.',
                'full_description' => 'Один из самых известных романов русской литературы XX века. История переплетает сатиру, мистику, философию и трагическую любовь Мастера и Маргариты. На фоне московской реальности разворачиваются события, связанные с появлением Воланда и его свиты.',
            ],
            2 => [
                'id' => 2,
                'title' => 'Преступление и наказание',
                'author' => 'Фёдор Достоевский',
                'category' => 'Классика',
                'price' => 750,
                'old_price' => 1000,
                'rating' => 4,
                'reviews' => 89,
                'badge' => '-25%',
                'badge_type' => 'sale',
                'image' => 'https://via.placeholder.com/500x700/764ba2/ffffff?text=Преступление+и+наказание',
                'description' => 'Психологический роман о студенте Раскольникове.',
                'full_description' => 'Глубокий психологический роман о вине, совести, морали и наказании. История Раскольникова раскрывает внутреннюю борьбу человека, пытающегося оправдать преступление идеей собственного превосходства.',
            ],
            3 => [
                'id' => 3,
                'title' => 'Убийство в Восточном экспрессе',
                'author' => 'Агата Кристи',
                'category' => 'Детектив',
                'price' => 650,
                'old_price' => null,
                'rating' => 5,
                'reviews' => 256,
                'badge' => null,
                'badge_type' => null,
                'image' => 'https://via.placeholder.com/500x700/667eea/ffffff?text=Восточный+экспресс',
                'description' => 'Эркюль Пуаро расследует загадочное убийство.',
                'full_description' => 'Классический детектив, где великий сыщик Эркюль Пуаро сталкивается с одним из самых необычных дел в своей карьере. Захватывающий сюжет, напряжённая атмосфера и неожиданная развязка.',
            ],
            4 => [
                'id' => 4,
                'title' => 'Атомные привычки',
                'author' => 'Джеймс Клир',
                'category' => 'Саморазвитие',
                'price' => 1200,
                'old_price' => null,
                'rating' => 5,
                'reviews' => 432,
                'badge' => 'Новинка',
                'badge_type' => 'new',
                'image' => 'https://via.placeholder.com/500x700/764ba2/ffffff?text=Атомные+привычки',
                'description' => 'Практическое руководство по формированию привычек.',
                'full_description' => 'Книга о том, как маленькие ежедневные изменения приводят к большим результатам. Автор объясняет, как устроены привычки и как системно выстраивать поведение для достижения целей.',
            ],
        ];
    }

    public function index()
    {
        $books = array_values(self::getBooks());
        return view('welcome', compact('books'));
    }

    public function show($id)
    {
        $books = self::getBooks();
        $book = $books[$id] ?? null;

        if (!$book) {
            abort(404);
        }

        return view('books.show', compact('book'));
    }
}