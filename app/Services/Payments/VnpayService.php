<?php

namespace App\Services\Payments;

use App\Models\Order;
use Illuminate\Http\Request;

/**
 * VNPay sandbox integration (payment gateway v2.1.0).
 * Docs: https://sandbox.vnpayment.vn/apis/docs/thanh-toan-pay/pay.html
 */
class VnpayService
{
    public function buildPaymentUrl(Order $order, Request $request): string
    {
        $config = config('payment.vnpay');

        $data = [
            'vnp_Version' => $config['version'],
            'vnp_Command' => 'pay',
            'vnp_TmnCode' => $config['tmn_code'],
            'vnp_Amount' => (int) round((float) $order->total * 100),
            'vnp_CurrCode' => $config['currency'],
            'vnp_TxnRef' => $order->code.'-'.now()->format('His'),
            'vnp_OrderInfo' => 'Thanh toan don hang '.$order->code,
            'vnp_OrderType' => 'other',
            'vnp_Locale' => $config['locale'],
            'vnp_ReturnUrl' => $config['return_url'] ?: route('payment.vnpay.return'),
            'vnp_IpAddr' => $request->ip(),
            'vnp_CreateDate' => now()->format('YmdHis'),
            'vnp_ExpireDate' => now()->addMinutes(15)->format('YmdHis'),
        ];

        ksort($data);

        // VNPay's official algorithm signs (and builds the query string from)
        // urlencode()'d key=value pairs joined with '&' — NOT
        // http_build_query()/urldecode(), which produces a different string
        // and causes "Sai chữ ký" (invalid signature) on VNPay's side.
        [$hashData, $query] = $this->buildQueryAndHashData($data);

        $secureHash = hash_hmac('sha512', $hashData, $config['hash_secret']);

        return $config['url'].'?'.$query.'&vnp_SecureHash='.$secureHash;
    }

    /**
     * @return array{valid: bool, success: bool, txn_ref: ?string, message: string}
     */
    public function verifyReturn(array $query): array
    {
        $config = config('payment.vnpay');
        $receivedHash = $query['vnp_SecureHash'] ?? '';

        unset($query['vnp_SecureHash'], $query['vnp_SecureHashType']);
        ksort($query);

        [$hashData] = $this->buildQueryAndHashData($query);
        $expectedHash = hash_hmac('sha512', $hashData, $config['hash_secret']);

        $valid = hash_equals($expectedHash, (string) $receivedHash);
        $success = $valid
            && ($query['vnp_ResponseCode'] ?? null) === '00'
            && ($query['vnp_TransactionStatus'] ?? '00') === '00';

        return [
            'valid' => $valid,
            'success' => $success,
            'txn_ref' => $query['vnp_TxnRef'] ?? null,
            'transaction_no' => $query['vnp_TransactionNo'] ?? null,
            'message' => $valid
                ? ($success ? 'Giao dịch thành công.' : 'Giao dịch không thành công (mã lỗi: '.($query['vnp_ResponseCode'] ?? '?').').')
                : 'Chữ ký không hợp lệ.',
        ];
    }

    /**
     * Build both the hash-signing string and the URL query string using
     * VNPay's exact convention: urlencode() each key/value, joined with '&',
     * skipping empty values (VNPay's sample does the same).
     *
     * @return array{0: string, 1: string} [$hashData, $query]
     */
    protected function buildQueryAndHashData(array $data): array
    {
        $hashData = '';
        $query = '';
        $first = true;

        foreach ($data as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $encoded = urlencode((string) $key).'='.urlencode((string) $value);

            if ($first) {
                $hashData .= $encoded;
                $query .= $encoded;
                $first = false;
            } else {
                $hashData .= '&'.$encoded;
                $query .= '&'.$encoded;
            }
        }

        return [$hashData, $query];
    }
}
