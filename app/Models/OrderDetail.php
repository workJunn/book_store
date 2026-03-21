<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'orders_details';
    protected $primaryKey = 'id_orders_details';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_orders',
        'id_books',
        'quantity',
        'price_per_item',
    ];

    protected $casts = [
        'price_per_item' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_orders', 'id_orders');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'id_books', 'id_books');
    }
}
