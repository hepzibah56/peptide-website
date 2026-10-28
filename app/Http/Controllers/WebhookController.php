<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Services\BtcpayService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class WebhookController extends Controller
{
    public function btcpay(Request $request, BtcpayService $btcpay, OrderService $orderService): Response
    {
        $signature = (string) $request->header('BTCPay-Sig', '');
        $body = $request->getContent();

        if (! $btcpay->verifyWebhook($signature, $body)) {
            abort(403, 'invalid signature');
        }

        $payload = json_decode($body, true);
        if (! is_array($payload)) {
            return response('ok', 200);
        }

        $eventId = $payload['id'] ?? $payload['deliveryId'] ?? (string) Str::uuid();
        $invoiceId = $payload['data']['invoiceId'] ?? $payload['invoiceId'] ?? null;

        if (! $invoiceId) {
            return response('ok', 200);
        }

        $payment = Payment::where('invoice_id', $invoiceId)->first();
        if (! $payment) {
            return response('ok', 200);
        }

        $order = $payment->order;

        $status = $payload['data']['status'] ?? $payload['status'] ?? null;
        $payment->raw_last_event = $payload;

        match (true) {
            $btcpay->isInvoiceSettled($payload['data'] ?? []) => $this->handleSettled($payment, $order, $orderService),
            $btcpay->isInvoiceExpired($payload['data'] ?? []) => $this->handleExpired($payment, $order, $orderService),
            $btcpay->isInvoiceUnderpaid($payload['data'] ?? []) => $this->handleUnderpaid($payment, $order),
            default => null,
        };

        $payment->save();

        return response('ok', 200);
    }

    private function handleSettled(Payment $payment, Order $order, OrderService $orderService): void
    {
        $payment->status = 'settlement_received';
        $payment->paid_at = now();

        if ($order->status === 'pending_payment') {
            $orderService->markPaid($order);
        }
    }

    private function handleExpired(Payment $payment, Order $order, OrderService $orderService): void
    {
        $payment->status = 'expired';

        if ($order->status === 'pending_payment') {
            $orderService->markExpired($order);
        }
    }

    private function handleUnderpaid(Payment $payment, Order $order): void
    {
        $payment->status = 'underpaid';

        if ($order->status === 'pending_payment') {
            $order->status = 'underpaid';
        }
    }
}