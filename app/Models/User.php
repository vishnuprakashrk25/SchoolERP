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
        'full_name',
        'email',
        'password',
        'is_active',
        'google_access_token',
        'google_refresh_token',
        'google_token_expires_at'
    ];

    protected $appends = [
        'full_name'
    ];

    public function getFullNameAttribute($value)
    {
        return $value ?? $this->attributes['name'] ?? null;
    }

    public function setFullNameAttribute($value)
    {
        $this->attributes['full_name'] = $value;
        if (empty($this->attributes['name'])) {
            $this->attributes['name'] = $value;
        }
    }

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
