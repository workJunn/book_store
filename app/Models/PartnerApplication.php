<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerApplication extends Model
{
    protected $table = 'partner_applications';
    protected $primaryKey = 'id_partner_application';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_users',
        'pen_name',
        'biography',
        'experience_summary',
        'portfolio_url',
        'payment_method',
        'status',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_users', 'id_users');
    }
}
