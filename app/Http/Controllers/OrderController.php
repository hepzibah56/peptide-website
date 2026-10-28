<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\BtcpayService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    public function __construct(private BtcpayService $btcpay) {}

    public function show(Request $request, string $locale, string $token): Response
    {
        $order = Order::where('public_token', $token)->firstOrFail();

        $invoice = null;
        $payment = $order->payments()->latest()->first();
        if ($payment && $payment->invoice_id) {
            $invoice = $this->btcpay->getInvoice($payment->invoice_id);
            if ($invoice && $this->btcpay->isInvoiceSettled($invoice) && $order->status === 'pending_payment') {
                $order->status = 'paid';
                $order->save();
            }
        }

        return response()->view('orders.show', [
            'order' => $order,
            'payment' => $payment,
            'invoice' => $invoice,
        ])->header('Cache-Control', 'no-store');
    }
}