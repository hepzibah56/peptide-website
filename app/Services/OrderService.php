<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Database\Connection;

class OrderService
{
    public function __construct(private Connection $db) {}

    public function transition(Order $order, string $newStatus, ?string $note = null): Order
    {
        return $this->db->transaction(function () use ($order, $newStatus, $note) {
            $order->status = $newStatus;
            if ($note) {
                $existing = $order->notes_internal ? $order->notes_internal."\n" : '';
                $order->notes_internal = $existing.'['.now()->format('Y-m-d H:i:s').'] '.$note;
            }
            $order->save();

            return $order->fresh();
        });
    }

    public function markPaid(Order $order): Order
    {
        return $this->transition($order, 'paid', 'Payment confirmed.');
    }

    public function markProcessing(Order $order): Order
    {
        return $this->transition($order, 'processing', 'Order being prepared.');
    }

    public function markShipped(Order $order, ?string $tracking = null, ?string $carrier = null): Order
    {
        if ($tracking) {
            $order->tracking_number = $tracking;
        }
        if ($carrier) {
            $order->carrier = $carrier;
        }

        return $this->transition($order, 'shipped', 'Shipped via '.($carrier ?? 'carrier').'.');
    }

    public function markCompleted(Order $order): Order
    {
        return $this->transition($order, 'completed', 'Order completed.');
    }

    public function markExpired(Order $order): Order
    {
        return $this->transition($order, 'expired', 'Payment window expired.');
    }

    public function cancel(Order $order, ?string $note = null): Order
    {
        return $this->transition($order, 'cancelled', $note ?? 'Order cancelled.');
    }
}