<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'public_token',
        'number',
        'status',
        'customer_email',
        'customer_name',
        'customer_phone',
        'customer_company',
        'customer_org_number',
        'billing_name',
        'billing_line1',
        'billing_line2',
        'billing_postcode',
        'billing_city',
        'billing_country',
        'shipping_name',
        'shipping_line1',
        'shipping_line2',
        'shipping_postcode',
        'shipping_city',
        'shipping_country',
        'subtotal_ore',
        'discount_ore',
        'shipping_ore',
        'vat_ore',
        'total_ore',
        'vat_rate',
        'locale',
        'tracking_number',
        'carrier',
        'notes_internal',
        'placed_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal_ore' => 'integer',
            'discount_ore' => 'integer',
            'shipping_ore' => 'integer',
            'vat_ore' => 'integer',
            'total_ore' => 'integer',
            'vat_rate' => 'decimal:4',
            'placed_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public static function generateToken(): string
    {
        return Str::random(40);
    }

    public static function generateNumber(): string
    {
        return 'NP-'.date('ym').'-'.Str::random(4);
    }

    public function subtotalNok(): float
    {
        return $this->subtotal_ore / 100;
    }

    public function vatNok(): float
    {
        return $this->vat_ore / 100;
    }

    public function totalNok(): float
    {
        return $this->total_ore / 100;
    }
}