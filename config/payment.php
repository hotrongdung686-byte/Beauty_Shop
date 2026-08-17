<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Payment gateways
    |--------------------------------------------------------------------------
    |
    | Each gateway is considered "demo" (simulated locally) unless the
    | merchant actually configured real credentials in .env. This project
    | ships with no live merchant accounts, so out of the box every gateway
    | runs through the built-in simulator at /thanh-toan/cong/{order} instead
    | of silently failing against fake/expired sandbox credentials.
    |
    | To go live with a real gateway, set the corresponding *_TMN_CODE /
    | *_PARTNER_CODE / *_APP_ID / *_ACCOUNT_NUMBER env var — that alone
    | flips that gateway out of demo mode.
    */

    'vnpay' => [
        'demo' => env('VNPAY_TMN_CODE') === null,
        'tmn_code' => env('VNPAY_TMN_CODE'),
        'hash_secret' => env('VNPAY_HASH_SECRET'),
        'url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
        'return_url' => env('VNPAY_RETURN_URL'),
        'version' => '2.1.0',
        'locale' => 'vn',
        'currency' => 'VND',
    ],

    'momo' => [
        'demo' => env('MOMO_PARTNER_CODE') === null,
        'partner_code' => env('MOMO_PARTNER_CODE'),
        'access_key' => env('MOMO_ACCESS_KEY'),
        'secret_key' => env('MOMO_SECRET_KEY'),
        'endpoint' => env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create'),
        'redirect_url' => env('MOMO_REDIRECT_URL'),
        'ipn_url' => env('MOMO_IPN_URL'),
    ],

    'zalopay' => [
        'demo' => env('ZALOPAY_APP_ID') === null,
        'app_id' => env('ZALOPAY_APP_ID'),
        'key1' => env('ZALOPAY_KEY1'),
        'key2' => env('ZALOPAY_KEY2'),
        'endpoint' => env('ZALOPAY_ENDPOINT', 'https://sb-openapi.zalopay.vn/v2/create'),
        'callback_url' => env('ZALOPAY_CALLBACK_URL'),
        'redirect_url' => env('ZALOPAY_REDIRECT_URL'),
    ],

    'sepay' => [
        // SePay confirms payment via bank-transfer webhook, not a redirect
        // gateway — it needs the shop's real bank account linked in the
        // SePay dashboard (https://my.sepay.vn) before webhooks will fire.
        // There is no gateway sandbox, so this is "demo" until an account
        // number is configured.
        'demo' => env('SEPAY_ACCOUNT_NUMBER') === null,
        'account_number' => env('SEPAY_ACCOUNT_NUMBER'),
        'bank_code' => env('SEPAY_BANK_CODE', 'MBBank'),
        'account_name' => env('SEPAY_ACCOUNT_NAME'),
        // Token expected in the "Authorization: Apikey {token}" header SePay
        // sends with every webhook call, to verify the request is genuine.
        'webhook_token' => env('SEPAY_WEBHOOK_TOKEN'),
        'qr_template' => env('SEPAY_QR_TEMPLATE', 'compact'),
    ],

];
