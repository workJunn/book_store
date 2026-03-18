<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
    protected $table = 'publishers';
    protected $primaryKey = 'id_publishers';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'publisher_name',
    ];

    public function books()
    {
        return $this->hasMany(Book::class, 'id_publishers', 'id_publishers');
    }
}