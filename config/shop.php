<?php

return [
    'name' => env('SHOP_NAME', 'Norges Pure Peps'),
    'email' => env('SHOP_EMAIL', 'norgespurepeps@pm.me'),
    'phone' => env('SHOP_PHONE', ''),
    'org_number' => env('SHOP_ORG_NUMBER', ''),
    'address' => env('SHOP_ADDRESS', ''),
    'currency' => 'NOK',
    'vat_inclusive' => true,
    'shipping_posten_link' => 'https://sending.posten.no/bestill/pakke/under5kg',
    'domestic_only' => true,
    'payment' => [
        'provider' => 'btcpay',
        'btcpay_url' => env('BTCPAY_URL', ''),
        'btcpay_store_id' => env('BTCPAY_STORE_ID', ''),
        'btcpay_api_key' => env('BTCPAY_API_KEY', ''),
        'btcpay_webhook_secret' => env('BTCPAY_WEBHOOK_SECRET', ''),
    ],
    'compliance' => [
        'enabled' => false,
        'sterile_water_cleared' => false,
    ],
];