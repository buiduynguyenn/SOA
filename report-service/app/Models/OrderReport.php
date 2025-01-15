<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderReport extends Model
{
    protected $fillable = [
        'order_id',
        'total_revenue',
        'total_cost',
        'total_profit'
    ];

    public function productReports(): HasMany
    {
        return $this->hasMany(ProductReport::class);
    }
} 