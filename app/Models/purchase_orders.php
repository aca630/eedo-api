<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class purchase_orders extends Model
{
    use HasFactory;
        protected $fillable = [
            'purchase_order_id',
            'cashier_id',
            'product_id',
            'quantity',
            'cost_price',
            'retail_price',
            'isvoided',
            'status',
        ];
}
