<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;
     protected $fillable = [
        'product_code',
        'barcode',
        'name',
        'description',
        'category_id',
        'cost_price',
        'whole_price',
        'retail_price',
        'stock',
        'unit',
        'is_active',
    ];
}
