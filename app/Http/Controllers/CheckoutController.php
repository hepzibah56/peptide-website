<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Services\BtcpayService;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cart,
        private CheckoutService $checkout,
        private BtcpayService $btcpay
    ) {}

    public function index(Request $request): Response
    {
        $items = $this->cart->items();
        if (empty($items)) {
            return redirect()->route('cart.index');
        }

        $subtotal = $this->cart->subtotal();
        $vat = round($subtotal * 0.25, 2);
        $total = $subtotal + $vat;

        return response()->view('checkout.index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'vat' => $vat,
            'total' => $total,
            'btcpayEnabled' => $this->btcpay->enabled(),
        ])->header('Cache-Control', 'no-store');
    }

    public function store(CheckoutRequest $request): Response
    {
        $items = $this->cart->items();
        if (empty($items)) {
            return redirect()->route('cart.index');
        }

        $order = $this->checkout->create($request->validated(), $items);

        if (! $this->btcpay->enabled()) {
            return response()->view('checkout.offline', [
                'order' => $order,
                'message' => 'Betalingssystemet er ikke konfigurert. Vennligst kontakt kundestøtte.',
            ])->header('Cache-Control', 'no-store');
        }

        $redirectUrl = route('order.show', ['locale' => $order->locale, 'token' => $order->public_token], false);
        $webhookUrl = route('webhooks.btcpay', [], false);

        $invoice = $this->btcpay->createInvoice(
            $order->total_ore,
            $order->number,
            $redirectUrl,
            $webhookUrl
        );

        if (! $invoice) {
            return response()->view('checkout.offline', [
                'order' => $order,
                'message' => 'Kunne ikke opprette betalingsforsøk. Vennligst prøv igjen eller kontakt kundestøtte.',
            ])->header('Cache-Control', 'no-store');
        }

        $payment = Payment::create([
            'order_id' => $order->id,
            'provider' => 'btcpay',
            'invoice_id' => $invoice['id'],
            'status' => 'pending',
            'amount_ore' => $order->total_ore,
            'btc_amount' => $invoice['amount'] ?? null,
            'rate' => $invoice['rate'] ?? null,
            'expires_at' => $invoice['expiresAt'] ?? null,
        ]);

        $this->cart->clear();

        return response()->view('checkout.payment', [
            'order' => $order,
            'payment' => $payment,
            'invoice' => $invoice,
            'btcpayUrl' => $this->btcpay->baseUrl,
        ])->header('Cache-Control', 'no-store');
    }
}