<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'phone',
        'address',
        'birthdate',
        'locale',
        'currency',
        'status',
        'created_by',
        'updated_by',
        'last_login_at',
        'two_factor_secret',
        'two_factor_enabled',
        'last_action',
        'preferences',
        'is_admin',
        'module_access',
        'notifications_enabled',
        'email_verified_at',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'birthdate' => 'date',
            'two_factor_enabled' => 'boolean',
            'notifications_enabled' => 'boolean',
            'is_admin' => 'boolean',
            'module_access' => 'array',
            'preferences' => 'array',
            'status' => 'boolean',
        ];
    }

    public function getPhotoUrlAttribute(): string
    {
        if (empty($this->photo)) {
            return asset('assets/img/profile.jpg');
        }

        return url('media/public/' . ltrim($this->photo, '/'));
    }
}

