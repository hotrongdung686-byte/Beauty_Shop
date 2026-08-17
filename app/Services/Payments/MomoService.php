<?php

namespace App\Services\Payments;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

/**
 * MoMo sandbox integration ("payWithMethod" flow, API v2).
 * Docs: https://developers.momo.vn/v3/docs/payment/api/wallet/onetime
 */
class MomoService
{
    /**
     * Ask MoMo to open a payment session for this order.
     *
     * @return array{success: bool, pay_url: ?string, message: string}
     */
    public function createPayment(Order $order): array
    {
        $config = config('payment.momo');

        $requestId = $config['partner_code'].time();
        $orderId = $order->code.'-'.now()->format('His');
        $amount = (string) (int) round((float) $order->total);
        $orderInfo = 'Thanh toan don hang '.$order->code;
        $redirectUrl = $config['redirect_url'] ?: route('payment.momo.return');
        $ipnUrl = $config['ipn_url'] ?: route('payment.momo.ipn');
        $extraData = '';
        // 'payWithMethod' is what MoMo's shared test partner code (MOMO) expects;
        // 'captureWallet' is the older flow name and gets rejected by this account.
        $requestType = 'payWithMethod';

        // MoMo's "create payment" signature — field order is fixed by their docs.
        // Only these fields are signed; extra body fields below (partnerName,
        // storeId, autoCapture, lang) are NOT part of the signature.
        $rawSignature = 'accessKey='.$config['access_key']
            .'&amount='.$amount
            .'&extraData='.$extraData
            .'&ipnUrl='.$ipnUrl
            .'&orderId='.$orderId
            .'&orderInfo='.$orderInfo
            .'&partnerCode='.$config['partner_code']
            .'&redirectUrl='.$redirectUrl
            .'&requestId='.$requestId
            .'&requestType='.$requestType;

        $signature = hash_hmac('sha256', $rawSignature, $config['secret_key']);

        $response = Http::timeout(15)->post($config['endpoint'], [
            'partnerCode' => $config['partner_code'],
            'partnerName' => config('app.name'),
            'storeId' => 'BeautyShopStore',
            'accessKey' => $config['access_key'],
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'autoCapture' => true,
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature,
        ]);

        $body = $response->json() ?? [];

        if ($response->successful() && ($body['resultCode'] ?? 1) === 0 && ! empty($body['payUrl'])) {
            return ['success' => true, 'pay_url' => $body['payUrl'], 'message' => 'OK'];
        }

        return [
            'success' => false,
            'pay_url' => null,
            'message' => $body['message'] ?? 'Không thể khởi tạo thanh toán MoMo.',
        ];
    }

    /**
     * Verify the signature on MoMo's server-to-server IPN callback.
     *
     * MoMo urlencode()'s `message` and `orderInfo` specifically when signing
     * this callback (they can contain spaces/Vietnamese text) but leaves the
     * other fields raw — mismatching this per-field treatment is a common
     * cause of "signature always fails" bugs.
     */
    public function verifyIpnSignature(array $data): bool
    {
        $config = config('payment.momo');

        $rawSignature = 'accessKey='.$config['access_key']
            .'&amount='.($data['amount'] ?? '')
            .'&extraData='.($data['extraData'] ?? '')
            .'&message='.urlencode($data['message'] ?? '')
            .'&orderId='.($data['orderId'] ?? '')
            .'&orderInfo='.urlencode($data['orderInfo'] ?? '')
            .'&orderType='.($data['orderType'] ?? '')
            .'&partnerCode='.($data['partnerCode'] ?? '')
            .'&payType='.($data['payType'] ?? '')
            .'&requestId='.($data['requestId'] ?? '')
            .'&responseTime='.($data['responseTime'] ?? '')
            .'&resultCode='.($data['resultCode'] ?? '')
            .'&transId='.($data['transId'] ?? '');

        $expected = hash_hmac('sha256', $rawSignature, $config['secret_key']);

        return hash_equals($expected, (string) ($data['signature'] ?? ''));
    }
}
