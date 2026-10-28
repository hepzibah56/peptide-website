<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class BtcpayService
{
    private string $baseUrl;
    private string $apiKey;
    private string $storeId;
    private string $webhookSecret;

    public function __construct()
    {
        $cfg = config('shop.payment');
        $this->baseUrl = rtrim((string) $cfg['btcpay_url'], '/');
        $this->apiKey = (string) $cfg['btcpay_api_key'];
        $this->storeId = (string) $cfg['btcpay_store_id'];
        $this->webhookSecret = (string) $cfg['btcpay_webhook_secret'];
    }

    public function enabled(): bool
    {
        return $this->baseUrl !== '' && $this->apiKey !== '' && $this->storeId !== '';
    }

    private function headers(): array
    {
        return [
            'Authorization' => 'token '.$this->apiKey,
            'Accept' => 'application/json',
        ];
    }

    public function createInvoice(int $amountOre, string $orderNumber, string $redirectUrl, string $webhookUrl, int $expirationMinutes = 30): ?array
    {
        if (! $this->enabled()) {
            return null;
        }

        $payload = [
            'amount' => (string) $amountOre,
            'currency' => 'NOK',
            'orderId' => $orderNumber,
            'redirectUrl' => $redirectUrl,
            'webhookUrl' => $webhookUrl,
            'checkout' => [
                'redirectUrl' => $redirectUrl,
                'expirationMinutes' => $expirationMinutes,
                'requiresPayment' => true,
            ],
            'metadata' => [
                'orderNumber' => $orderNumber,
            ],
        ];

        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(20)
                ->post("{$this->baseUrl}/api/v1/stores/{$this->storeId}/invoices", $payload);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (ConnectionException) {
            // fall through
        }

        return null;
    }

    public function getInvoice(string $invoiceId): ?array
    {
        if (! $this->enabled()) {
            return null;
        }

        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(20)
                ->get("{$this->baseUrl}/api/v1/invoices/{$invoiceId}");

            if ($response->successful()) {
                return $response->json();
            }
        } catch (ConnectionException) {
            // fall through
        }

        return null;
    }

    public function verifyWebhook(string $signature, string $body): bool
    {
        if ($this->webhookSecret === '') {
            return false;
        }

        $expected = hash_hmac('sha256', $body, $this->webhookSecret);

        return hash_equals($expected, $signature);
    }

    public function isInvoiceSettled(array $invoice): bool
    {
        $status = $invoice['status'] ?? '';

        return in_array($status, ['settled', 'completed', 'confirmed'], true);
    }

    public function isInvoiceExpired(array $invoice): bool
    {
        return ($invoice['status'] ?? '') === 'expired';
    }

    public function isInvoiceUnderpaid(array $invoice): bool
    {
        return ($invoice['status'] ?? '') === 'underpaid';
    }
}