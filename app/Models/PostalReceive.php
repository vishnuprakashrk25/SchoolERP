<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostalReceive extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_title',
        'reference_no',
        'address',
        'note',
        'to_title',
        'date',
    ];
}
