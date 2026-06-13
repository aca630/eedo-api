<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlaughterPublicTransaction extends Model
{
    use HasFactory;

    public const STATUS_CASHIER_ONLY = 'cashier_only';
    public const STATUS_SLAUGHTER_ONLY = 'slaughter_only';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'or_number',
        'agency',
        'payor',
        'cashier_user_id',
        'cashier_encoded_at',
        'small_heads',
        'goat_heads',
        'hog_heads',
        'large_heads',
        'cow_heads',
        'carabao_heads',
        'small_kilos',
        'large_kilos',
        'pmf_amount',
        'slaughter_user_id',
        'slaughter_encoded_at',
        'status',
    ];

    protected $casts = [
        'cashier_encoded_at' => 'datetime',
        'slaughter_encoded_at' => 'datetime',
        'small_heads' => 'integer',
        'goat_heads' => 'integer',
        'hog_heads' => 'integer',
        'large_heads' => 'integer',
        'cow_heads' => 'integer',
        'carabao_heads' => 'integer',
        'small_kilos' => 'decimal:2',
        'large_kilos' => 'decimal:2',
        'pmf_amount' => 'decimal:2',
    ];
}
