<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';
    protected $primaryKey = 'id_reviews';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_books',
        'id_users',
        'rating',
        'review_text',
        'review_date',
    ];

    protected $casts = [
        'review_date' => 'datetime',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class, 'id_books', 'id_books');
    }
}