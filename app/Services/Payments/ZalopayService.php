<?php

namespace App\Services\Payments;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

/**
 * ZaloPay sandbox integration (create-order API v2).
 * Docs: https://docs.zalopay.vn/v2/general/overview.html
 */
class ZalopayService
{
    /**
     * @return array{success: bool, order_url: ?string, app_trans_id: ?string, message: string}
     */
    public function createOrder(Order $order): array
    {
        $config = config('payment.zalopay');

        $appTransId = now()->format('ymd').'_'.$order->code.'-'.now()->format('His');
        $amount = (int) round((float) $order->total);
        $appTime = (int) (microtime(true) * 1000);
        $embedData = json_encode(['order_code' => $order->code]);
        $item = json_encode([]);

        $data = [
            'app_id' => $config['app_id'],
            'app_trans_id' => $appTransId,
            'app_user' => 'user_'.($order->user_id ?? 'guest'),
            'app_time' => $appTime,
            'amount' => $amount,
            'item' => $item,
            'embed_data' => $embedData,
            'description' => 'Thanh toan don hang '.$order->code,
            'callback_url' => $config['callback_url'] ?: route('payment.zalopay.callback'),
        ];

        $macInput = $config['app_id'].'|'.$data['app_trans_id'].'|'.$data['app_user'].'|'.$data['amount']
            .'|'.$data['app_time'].'|'.$data['embed_data'].'|'.$data['item'];
        $data['mac'] = hash_hmac('sha256', $macInput, $config['key1']);

        $response = Http::asForm()->timeout(15)->post($config['endpoint'], $data);
        $body = $response->json() ?? [];

        if ($response->successful() && ($body['return_code'] ?? 0) === 1 && ! empty($body['order_url'])) {
            return [
                'success' => true,
                'order_url' => $body['order_url'],
                'app_trans_id' => $appTransId,
                'message' => 'OK',
            ];
        }

        return [
            'success' => false,
            'order_url' => null,
            'app_trans_id' => $appTransId,
            'message' => $body['return_message'] ?? 'Không thể khởi tạo thanh toán ZaloPay.',
        ];
    }

    /**
     * Verify the `mac` ZaloPay attaches to its server-to-server callback.
     */
    public function verifyCallback(string $dataJson, string $receivedMac): bool
    {
        $config = config('payment.zalopay');
        $expected = hash_hmac('sha256', $dataJson, $config['key2']);

        return hash_equals($expected, $receivedMac);
    }
}
