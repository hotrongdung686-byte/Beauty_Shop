<?php

namespace App\Services\Payments;

use App\Models\Order;

/**
 * SePay bank-transfer confirmation: the customer scans a VietQR code and
 * pays via their own banking app, SePay watches the shop's real linked bank
 * account and fires a webhook to us once the transfer lands. Requires that
 * account to be connected in the SePay dashboard (https://my.sepay.vn) —
 * there is no gateway "sandbox" for this product, it moves real money.
 *
 * Docs: https://docs.sepay.vn
 */
class SepayService
{
    /**
     * Prefix SePay is configured (in its dashboard, under webhook filters)
     * to recognize as a payment code in transfer content, e.g. "ORDABC123".
     * Must match what's set up there — see qrImageUrl()'s transfer content.
     */
    protected const ORDER_CODE_PATTERN = '/ORD[A-Z0-9-]+/i';

    public function isConfigured(): bool
    {
        return filled(config('payment.sepay.account_number'));
    }

    /**
     * Build a VietQR image URL pre-filled with the order's amount and a
     * transfer note containing the order code, so SePay's webhook can match
     * the incoming transfer back to this order.
     */
    public function qrImageUrl(Order $order): string
    {
        $config = config('payment.sepay');

        $params = [
            'bank' => $config['bank_code'],
            'acc' => $config['account_number'],
            'template' => $config['qr_template'],
            'amount' => (int) round((float) $order->total),
            'des' => $this->transferContent($order),
        ];

        if ($config['account_name']) {
            $params['accName'] = $config['account_name'];
        }

        return 'https://qr.sepay.vn/img?'.http_build_query($params);
    }

    public function transferContent(Order $order): string
    {
        return 'DH '.$order->code;
    }

    public function verifyToken(?string $authorizationHeader): bool
    {
        $expected = config('payment.sepay.webhook_token');

        if (blank($expected)) {
            return false;
        }

        return hash_equals('Apikey '.$expected, (string) $authorizationHeader)
            || hash_equals($expected, (string) $authorizationHeader);
    }

    /**
     * Match an incoming webhook payload back to one of our orders.
     *
     * Prefers SePay's own `code` field — auto-extracted by SePay from the
     * transfer content server-side according to the "payment code prefix"
     * configured in its dashboard, and far more reliable than us re-parsing
     * `content`/`description` ourselves (some banks mangle punctuation/case
     * in the free-text content). Falls back to regex-matching the raw
     * content in case the prefix filter isn't configured on SePay's side.
     */
    public function findOrderFromPayload(array $payload): ?Order
    {
        $code = trim((string) ($payload['code'] ?? ''));

        if ($code !== '' && preg_match(self::ORDER_CODE_PATTERN, $code, $matches)) {
            $order = Order::where('code', strtoupper($matches[0]))->first();
            if ($order) {
                return $order;
            }
        }

        $content = ($payload['content'] ?? '').' '.($payload['description'] ?? '');

        return $this->findOrderFromContent($content);
    }

    /**
     * Find the order referenced by a raw transfer content/description
     * string (e.g. "DH ORD260812ABCDE ND ...").
     */
    public function findOrderFromContent(string $content): ?Order
    {
        if (! preg_match(self::ORDER_CODE_PATTERN, $content, $matches)) {
            return null;
        }

        return Order::where('code', strtoupper($matches[0]))->first();
    }
}
