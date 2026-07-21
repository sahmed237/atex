<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use App\Services\Payment\Gateways\PaymentGatewayFactory;
use App\Services\Payment\OrderPaymentHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Initiate payment for an existing order
     */
    public function pay(Request $request, Order $order)
    {
        $request->validate([
            'gateway' => 'required|string',
        ]);

        $gatewayKey = strtolower($request->gateway);
        $isActive = Setting::where('key', "{$gatewayKey}_active")->value('value') == '1';

        if (!$isActive) {
            return back()->with('error', ucfirst($gatewayKey) . ' payment gateway is currently inactive.');
        }

        $user = auth()->user();

        // Format order info to meet payment gateway requirements
        $paymentData = (object) [
            'id' => $order->id,
            'payment_amount' => (float) $order->total_amount,
            'prefix' => 'AEM-ORD',
            'author_name' => $user->name ?? 'Buyer',
        ];

        $email = $user->email ?? 'buyer@atex.adamawastate.gov.ng';
        $phone = $user->phone ?? '08000000000';
        $callbackUrl = route('payment.callback', ['gateway' => $gatewayKey]);

        try {
            $gatewayInstance = PaymentGatewayFactory::create($gatewayKey);
            $initResult = $gatewayInstance->initialize($paymentData, $email, $phone, $callbackUrl);

            \App\Models\OrderPayment::create([
                'order_id' => $order->id,
                'gateway' => $gatewayKey,
                'reference' => $initResult->reference,
                'amount' => (float) $order->total_amount,
                'status' => 'pending',
                'raw_details' => json_decode($initResult->rawResponse, true),
            ]);

            return redirect()->away($initResult->redirectUrl);
        } catch (\Throwable $e) {
            Log::error("Payment initialization failed for Order #{$order->order_number}: " . $e->getMessage());
            return back()->with('error', 'Unable to initiate payment: ' . $e->getMessage());
        }
    }

    /**
     * Handle return callback after gateway payment completion
     */
    public function callback(Request $request, string $gateway)
    {
        $gatewayKey = strtolower($gateway);

        $reference = $request->get('reference')
            ?? $request->get('trxref')
            ?? $request->get('paymentReference')
            ?? $request->get('txnRef');

        if (!$reference) {
            return redirect()->route('buyer.orders.index')->with('error', 'No payment reference provided by gateway.');
        }

        // Expected reference format: AEM-ORD-{order_id}-{timestamp}
        $parts = explode('-', $reference);
        $orderId = $parts[2] ?? null;

        $order = Order::find($orderId);

        try {
            $gatewayInstance = PaymentGatewayFactory::create($gatewayKey);
            $verifyResult = $gatewayInstance->verify($reference);

            if ($verifyResult->amountPaid > 0 && $order) {
                OrderPaymentHelper::processSuccessfulPayment($order, $verifyResult);
                return redirect()->route('buyer.orders.show', $order->id)->with('success', 'Payment verified and placed in escrow hold successfully!');
            }

            if ($order) {
                return redirect()->route('buyer.orders.show', $order->id)->with('error', 'Payment verification was pending or incomplete.');
            }

            return redirect()->route('buyer.orders.index')->with('error', 'Order not found for payment reference.');
        } catch (\Throwable $e) {
            Log::error("Payment callback verification error for {$reference}: " . $e->getMessage());
            return redirect()->route('buyer.orders.index')->with('error', 'Payment verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle asynchronous webhooks from payment gateways
     */
    public function webhook(Request $request, string $gateway)
    {
        $gatewayKey = strtolower($gateway);
        $payload = $request->all();
        $headers = $request->headers->all();
        $rawBody = $request->getContent();

        try {
            $gatewayInstance = PaymentGatewayFactory::create($gatewayKey);
            $verifyResult = $gatewayInstance->webhook($payload, $headers, $rawBody);

            if ($verifyResult && $verifyResult->amountPaid > 0) {
                $parts = explode('-', $verifyResult->reference);
                $orderId = $parts[2] ?? null;
                $order = Order::find($orderId);

                if ($order && $order->payment_status !== 'paid') {
                    OrderPaymentHelper::processSuccessfulPayment($order, $verifyResult);
                }
            }

            return response()->json(['status' => 'success']);
        } catch (\Throwable $e) {
            Log::error("Webhook error for {$gatewayKey}: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}
