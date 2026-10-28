<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'price',
        'currency',
        'image',
        'is_active',
        'is_published',
        'is_featured',
        'sku',
        'summary',
        'stock',
        'track_stock',
        'lot',
        'storage',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'track_stock' => 'boolean',
            'stock' => 'integer',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_published', true);
    }
}