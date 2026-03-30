<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'books';
    protected $primaryKey = 'id_books';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'book_name',
        'cover_image',
        'digital_file_path',
        'digital_file_original_name',
        'price',
        'discount_percent',
        'stock_quantity',
        'is_preorder',
        'publication_date',
        'number_of_pages',
        'average_rating',
        'description',
        'id_author',
        'id_publishers',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'publication_date' => 'date',
        'is_preorder' => 'boolean',
        'discount_percent' => 'integer',
    ];

    public function author()
    {
        return $this->belongsTo(Author::class, 'id_author', 'id_author');
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class, 'id_publishers', 'id_publishers');
    }

    public function genres()
    {
        return $this->belongsToMany(
            Genre::class,
            'book_genres',
            'id_books',
            'id_genre',
            'id_books',
            'id_genre'
        );
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'id_books', 'id_books');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'id_books', 'id_books');
    }

    public function toCartItem(): array
    {
        return [
            'id' => $this->id_books,
            'title' => $this->book_name,
            'author' => $this->author->author_name ?? 'Не указан',
            'price' => (float) $this->price,
            'image' => $this->cover_image_url,
            'quantity' => 1,
            'stock_quantity' => $this->stock_quantity,
        ];
    }

    public function getCoverImageUrlAttribute(): string
    {
        if ($this->cover_image) {
            return '/storage/' . ltrim($this->cover_image, '/');
        }

        return $this->getPlaceholderImageUrl();
    }

    public function getPlaceholderImageUrl(): string
    {
        return 'https://via.placeholder.com/500x700/667eea/ffffff?text=' . urlencode($this->book_name);
    }

    public function hasDigitalFile(): bool
    {
        return ! empty($this->digital_file_path);
    }

    public function getDigitalFileDownloadNameAttribute(): string
    {
        $extension = pathinfo($this->digital_file_original_name ?: $this->digital_file_path ?: '', PATHINFO_EXTENSION);
        $safeTitle = trim((string) preg_replace('/[^\pL\pN]+/u', '_', $this->book_name), '_') ?: 'book';

        return $safeTitle . ($extension ? '.' . mb_strtolower($extension) : '');
    }

    public function getOriginalPrice(): float
    {
        $discountPercent = max(0, min(95, (int) $this->discount_percent));
        $currentPrice = (float) $this->price;

        if ($discountPercent === 0) {
            return (float) (ceil(($currentPrice * 1.2) / 10) * 10);
        }

        return round($currentPrice / (1 - ($discountPercent / 100)), 2);
    }

    public function getDisplayDiscountPercent(): int
    {
        return max(0, min(95, (int) $this->discount_percent));
    }
}
