<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements CanResetPasswordContract
{
    use CanResetPassword;
    use HasFactory;
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_users';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'password',
        'email',
        'phone_number',
        'balance',
        'id_role',
        'registration_date',
        'email_verified_at',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'registration_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'id_users', 'id_users');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    public function authorProfile()
    {
        return $this->hasOne(Author::class, 'id_users', 'id_users');
    }

    public function partnerApplications()
    {
        return $this->hasMany(PartnerApplication::class, 'id_users', 'id_users');
    }

    public function isAdmin(): bool
    {
        if ($this->relationLoaded('role')) {
            return $this->role?->role_name === 'admin';
        }

        return $this->role()->where('role_name', 'admin')->exists();
    }

    public function isAuthor(): bool
    {
        if ($this->relationLoaded('role')) {
            return $this->role?->role_name === 'author';
        }

        return $this->role()->where('role_name', 'author')->exists();
    }
}
