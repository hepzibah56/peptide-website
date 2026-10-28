<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Database\Connection;

class CheckoutService
{
    public function __construct(private Connection $db) {}

    public function create(array $data, array $items): Order
    {
        return $this->db->transaction(function () use ($data, $items) {
            $subtotal = collect($items)->sum('line_total');
            $vatRate = 0.25;
            $vat = round($subtotal * $vatRate, 2);
            $total = $subtotal + $vat;

            $order = Order::create([
                'public_token' => Order::generateToken(),
                'number' => Order::generateNumber(),
                'status' => 'pending_payment',
                'customer_email' => $data['email'],
                'customer_name' => $data['name'] ?? null,
                'customer_phone' => $data['phone'] ?? null,
                'customer_company' => $data['company'] ?? null,
                'customer_org_number' => $data['org_number'] ?? null,
                'billing_name' => $data['name'],
                'billing_line1' => $data['address'],
                'billing_postcode' => $data['postcode'],
                'billing_city' => $data['city'],
                'billing_country' => 'NO',
                'shipping_name' => $data['name'],
                'shipping_line1' => $data['address'],
                'shipping_postcode' => $data['postcode'],
                'shipping_city' => $data['city'],
                'shipping_country' => 'NO',
                'subtotal_ore' => (int) round($subtotal * 100),
                'discount_ore' => 0,
                'shipping_ore' => 0,
                'vat_ore' => (int) round($vat * 100),
                'total_ore' => (int) round($total * 100),
                'vat_rate' => $vatRate,
                'locale' => app()->getLocale(),
                'placed_at' => now(),
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'sku' => $item['product']->sku ?? null,
                    'name' => $item['name'],
                    'unit_price_ore' => (int) round($item['price'] * 100),
                    'vat_rate' => $vatRate,
                    'quantity' => $item['quantity'],
                ]);

                if ($item['product']->track_stock) {
                    $item['product']->where('id', $item['product_id'])
                        ->where('stock', '>', 0)
                        ->decrement('stock', $item['quantity']);
                }
            }

            return $order;
        });
    }
}