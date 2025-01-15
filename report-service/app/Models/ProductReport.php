<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReport extends Model
{
    protected $fillable = [
        'order_report_id',
        'product_id',
        'total_sold',
        'revenue',
        'cost',
        'profit'
    ];

    public function orderReport(): BelongsTo
    {
        return $this->belongsTo(OrderReport::class);
    }
} 