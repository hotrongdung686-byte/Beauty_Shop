<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Payments\MomoService;
use App\Services\Payments\SepayService;
use App\Services\Payments\VnpayService;
use App\Services\Payments\ZalopayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | VNPay
    |--------------------------------------------------------------------------
    */

    /**
     * VNPay's browser redirect back after payment. UX-only, matching the
     * MoMo pattern above: we trust `vnp_ResponseCode` here to give the
     * shopper an immediate result, while vnpayIpn() below (VNPay's
     * server-to-server notify, fully signature-verified) is the actual
     * source of truth — configure its URL as the "IPN URL" for this
     * terminal in the VNPay merchant portal.
     */
    public function vnpayReturn(Request $request)
    {
        $order = $this->findOrderFromTxnRef($request->query('vnp_TxnRef'));

        if (! $order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng.');
        }

        $success = $request->query('vnp_ResponseCode') === '00'
            && $request->query('vnp_TransactionStatus', '00') === '00';

        return $this->finalizeGatewayPayment(
            $order,
            'vnpay',
            $success,
            $success ? 'Giao dịch thành công.' : 'Giao dịch không thành công (mã lỗi: '.$request->query('vnp_ResponseCode', '?').').',
            $request->query('vnp_TransactionNo')
        );
    }

    /**
     * Server-to-server IPN from VNPay — the authoritative confirmation,
     * fully signature-verified. Must be registered as this terminal's "IPN
     * URL" in the VNPay merchant portal; VNPay calls it via GET.
     */
    public function vnpayIpn(Request $request, VnpayService $vnpay)
    {
        $result = $vnpay->verifyReturn($request->query());

        if (! $result['valid']) {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
        }

        $order = $this->findOrderFromTxnRef($result['txn_ref']);

        if (! $order) {
            return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
        }

        $this->markPayment($order, 'vnpay', $result['success'], $result['transaction_no'] ?? null);

        return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
    }

    /*
    |--------------------------------------------------------------------------
    | MoMo
    |--------------------------------------------------------------------------
    */

    /**
     * MoMo's browser redirect back after payment. MoMo doesn't sign this
     * callback with a documented/verifiable field set, so — same as MoMo's
     * own reference client code — we trust `resultCode` directly here rather
     * than waiting on the IPN, which never arrives at all on a plain
     * localhost setup. markPayment() is idempotent, so if the IPN also
     * arrives later (e.g. tunnelled through ngrok) it's a safe no-op.
     */
    public function momoReturn(Request $request)
    {
        $order = $this->findOrderFromTxnRef($request->query('orderId'));

        if (! $order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng.');
        }

        $success = (string) $request->query('resultCode', '1') === '0';

        return $this->finalizeGatewayPayment(
            $order,
            'momo',
            $success,
            $success ? 'Giao dịch thành công.' : ((string) $request->query('message') ?: 'Giao dịch không thành công.'),
            $request->query('transId')
        );
    }

    /**
     * Server-to-server notification from MoMo — the source of truth for
     * marking a MoMo payment as paid. Must respond quickly.
     */
    public function momoIpn(Request $request, MomoService $momo)
    {
        $data = $request->all();
        Log::info('MoMo IPN', $data);

        if (! $momo->verifyIpnSignature($data)) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $order = $this->findOrderFromTxnRef($data['orderId'] ?? null);
        $success = (string) ($data['resultCode'] ?? '1') === '0';

        if ($order) {
            $this->markPayment($order, 'momo', $success, $data['transId'] ?? null);
        }

        return response()->json(['message' => 'OK'], 204);
    }

    /*
    |--------------------------------------------------------------------------
    | ZaloPay
    |--------------------------------------------------------------------------
    */

    public function zalopayReturn(Request $request)
    {
        // ZaloPay's redirect only tells us the user came back; the actual
        // confirmation is the server-to-server callback below. We look the
        // order up from the app_trans_id we embedded ("ymd_CODE-His").
        $appTransId = $request->query('apptransid', '');
        $code = preg_replace('/^\d{6}_/', '', $appTransId);
        $order = $this->findOrderFromTxnRef($code);

        if (! $order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng.');
        }

        $order->refresh();
        $paid = $order->payments()->where('method', 'zalopay')->where('status', Payment::STATUS_PAID)->exists();

        return redirect()->route('checkout.success', $order)
            ->with($paid ? 'success' : 'error', $paid ? 'Thanh toán ZaloPay thành công!' : 'Đang chờ xác nhận thanh toán từ ZaloPay.');
    }

    /**
     * Server-to-server callback from ZaloPay confirming payment.
     */
    public function zalopayCallback(Request $request, ZalopayService $zalopay)
    {
        $dataJson = $request->input('data', '');
        $mac = $request->input('mac', '');

        if (! $zalopay->verifyCallback($dataJson, $mac)) {
            return response()->json(['return_code' => 2, 'return_message' => 'Invalid']);
        }

        $payload = json_decode($dataJson, true) ?? [];
        $embedData = json_decode($payload['embed_data'] ?? '{}', true) ?? [];
        // embed_data.order_code is already our clean Order::code (no txn-ref
        // suffix to strip) — routing it through findOrderFromTxnRef() would
        // wrongly chop off its trailing digits (e.g. "ORD-000001" looks like
        // a "-XXXXXX" suffix to that helper), so look it up directly.
        $order = isset($embedData['order_code']) ? Order::where('code', $embedData['order_code'])->first() : null;

        if (! $order) {
            Log::warning('ZaloPay callback: no matching order', $payload);

            return response()->json(['return_code' => 2, 'return_message' => 'Order not found']);
        }

        $this->markPayment($order, 'zalopay', true, $payload['zp_trans_id'] ?? null);

        return response()->json(['return_code' => 1, 'return_message' => 'Success']);
    }

    /*
    |--------------------------------------------------------------------------
    | SePay (bank-transfer webhook)
    |--------------------------------------------------------------------------
    */

    public function sepayWebhook(Request $request, SepayService $sepay)
    {
        if (! $sepay->verifyToken($request->header('Authorization'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $request->all();
        Log::info('SePay webhook', $payload);

        // SePay may redeliver the same transaction more than once — the
        // idempotency guard in markPayment() (skip if already paid) is what
        // actually prevents double-processing; this ref is just for the
        // audit trail on the payment record.
        $transactionRef = (string) ($payload['referenceCode'] ?? $payload['id'] ?? '');
        $amount = (float) ($payload['transferAmount'] ?? 0);

        $order = $sepay->findOrderFromPayload($payload);

        if (! $order) {
            Log::info('SePay webhook: no matching order', $payload);

            return response()->json(['success' => true]); // ack anyway so SePay doesn't retry forever
        }

        $success = $amount >= (float) $order->total;
        $this->markPayment($order, 'sepay', $success, $transactionRef ?: null);

        return response()->json(['success' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | Shared helpers
    |--------------------------------------------------------------------------
    */

    protected function findOrderFromTxnRef(?string $txnRef): ?Order
    {
        if (! $txnRef) {
            return null;
        }

        // Our txn refs are built as "ORD250812ABCDE-His" — strip the suffix.
        $code = preg_replace('/-\d{6}$/', '', $txnRef);

        return Order::where('code', $code)->first();
    }

    protected function finalizeGatewayPayment(Order $order, string $method, bool $success, string $message, ?string $transactionRef = null)
    {
        $this->markPayment($order, $method, $success, $transactionRef);

        return redirect()->route('checkout.success', $order)->with($success ? 'success' : 'error', $message);
    }

    protected function markPayment(Order $order, string $method, bool $success, ?string $transactionRef = null): void
    {
        $payment = $order->payments()->where('method', $method)->latest()->first();

        if (! $payment) {
            return;
        }

        if ($payment->status === Payment::STATUS_PAID) {
            return; // already confirmed, avoid double-processing
        }

        $payment->update([
            'status' => $success ? Payment::STATUS_PAID : Payment::STATUS_FAILED,
            'transaction_ref' => $transactionRef,
            'paid_at' => $success ? now() : null,
        ]);

        if ($success && $order->status === Order::STATUS_PENDING) {
            $order->update(['status' => Order::STATUS_CONFIRMED]);
        }
    }
}
