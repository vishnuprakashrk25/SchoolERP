<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    protected $fillable = [
        'branch_id',
        'name',
        'email',
        'password',
        'is_active',
        'google_access_token',
        'google_refresh_token',
        'google_token_expires_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'google_access_token',
        'google_refresh_token',
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
