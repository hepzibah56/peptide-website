<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'provider',
        'invoice_id',
        'status',
        'amount_ore',
        'btc_amount',
        'rate',
        'expires_at',
        'paid_at',
        'raw_last_event',
    ];

    protected function casts(): array
    {
        return [
            'amount_ore' => 'integer',
            'expires_at' => 'datetime',
            'paid_at' => 'datetime',
            'raw_last_event' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}