<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $table = 'authors';
    protected $primaryKey = 'id_author';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_users',
        'author_name',
        'biography',
    ];

    public function books()
    {
        return $this->hasMany(Book::class, 'id_author', 'id_author');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_users', 'id_users');
    }
}
