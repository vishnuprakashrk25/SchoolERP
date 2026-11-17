<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'role',
        'activity_type',
        'description',
        'method',
        'url',
        'ip_address',
        'user_agent',
        'status_code',
        'request_payload',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'status_code'     => 'integer',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
