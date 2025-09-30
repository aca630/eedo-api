<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlaughterPrivate extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'or_no',
        'agency',
        'owner',
        'small_heads',
        'large_heads',
    ];
}
