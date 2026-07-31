<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostalDispatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'to_title',
        'reference_no',
        'address',
        'note',
        'from_title',
        'date',
    ];
}
