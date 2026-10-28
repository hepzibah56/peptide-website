<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'sku',
        'name',
        'unit_price_ore',
        'vat_rate',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'unit_price_ore' => 'integer',
            'quantity' => 'integer',
            'vat_rate' => 'decimal:4',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function lineTotalNok(): float
    {
        return (($this->unit_price_ore / 100) * $this->quantity);
    }
}