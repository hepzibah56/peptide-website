<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Session\Store;

class CartService
{
    public function __construct(private Store $session) {}

    public function key(): string
    {
        return 'cart.'.app()->getLocale();
    }

    public function all(): array
    {
        return $this->session->get($this->key(), []);
    }

    public function add(int $productId, int $quantity = 1): array
    {
        $product = Product::active()->find($productId);
        if (! $product) {
            return ['ok' => false, 'error' => 'not_found'];
        }

        $cart = $this->all();
        $current = $cart[$productId]['quantity'] ?? 0;
        $newQty = $current + $quantity;

        if ($product->track_stock && $newQty > $product->stock) {
            return ['ok' => false, 'error' => 'out_of_stock', 'available' => $product->stock];
        }

        $cart[$productId] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => (float) $product->price,
            'quantity' => $newQty,
        ];

        $this->session->put($this->key(), $cart);

        return ['ok' => true, 'count' => $this->count(), 'subtotal' => $this->subtotal()];
    }

    public function update(int $productId, int $quantity): array
    {
        $product = Product::active()->find($productId);
        if (! $product) {
            return ['ok' => false, 'error' => 'not_found'];
        }

        $cart = $this->all();
        if ($quantity <= 0) {
            unset($cart[$productId]);
            $this->session->put($this->key(), $cart);

            return ['ok' => true, 'count' => $this->count(), 'subtotal' => $this->subtotal()];
        }

        if ($product->track_stock && $quantity > $product->stock) {
            return ['ok' => false, 'error' => 'out_of_stock', 'available' => $product->stock];
        }

        $cart[$productId]['quantity'] = $quantity;
        $this->session->put($this->key(), $cart);

        return ['ok' => true, 'count' => $this->count(), 'subtotal' => $this->subtotal()];
    }

    public function remove(int $productId): array
    {
        $cart = $this->all();
        unset($cart[$productId]);
        $this->session->put($this->key(), $cart);

        return ['ok' => true, 'count' => $this->count(), 'subtotal' => $this->subtotal()];
    }

    public function count(): int
    {
        return collect($this->all())->sum('quantity');
    }

    public function items(): array
    {
        $cart = $this->all();
        $ids = array_keys($cart);
        if (! $ids) {
            return [];
        }

        $products = Product::active()->whereIn('id', $ids)->get()->keyBy('id');

        return collect($cart)->map(function ($item, $id) use ($products) {
            $product = $products->get($id);
            $item['product'] = $product;
            $item['line_total'] = $product ? $item['quantity'] * (float) $product->price : 0;

            return $item;
        })->values()->all();
    }

    public function subtotal(): float
    {
        return collect($this->items())->sum('line_total');
    }

    public function clear(): void
    {
        $this->session->forget($this->key());
    }
}