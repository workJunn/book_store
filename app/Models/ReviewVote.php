<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewVote extends Model
{
    protected $table = 'review_votes';
    protected $primaryKey = 'id_review_vote';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_reviews',
        'id_users',
        'is_helpful',
    ];

    protected $casts = [
        'is_helpful' => 'boolean',
    ];

    public function review()
    {
        return $this->belongsTo(Review::class, 'id_reviews', 'id_reviews');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_users', 'id_users');
    }
}
