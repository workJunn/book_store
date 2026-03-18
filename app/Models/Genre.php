<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $table = 'genres';
    protected $primaryKey = 'id_genre';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'genre_name',
    ];

    public function books()
    {
        return $this->belongsToMany(
            Book::class,
            'book_genres',
            'id_genre',
            'id_books',
            'id_genre',
            'id_books'
        );
    }
}
