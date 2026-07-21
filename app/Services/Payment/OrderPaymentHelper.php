<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\AtexAuditLog;
use Illuminate\Support\Facades\Log;
use App\Services\Payment\DTOs\PaymentVerifyResult;

class OrderPaymentHelper
{
    /**
     * Synchronize and process order payment details after verification
     */
    public static function processSuccessfulPayment(Order $order, PaymentVerifyResult $result)
    {
        try {
            // 1. Update order status and payment status
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
                'settlement_status' => 'pending_escrow',
            ]);

            // 2. Update settlement record if existing
            if ($order->settlement) {
                $order->settlement->update([
                    'status' => 'held_in_escrow',
                ]);
            }

            // 2b. Update or create the payment record in order_payments table
            \App\Models\OrderPayment::updateOrCreate(
                ['reference' => $result->reference, 'order_id' => $order->id],
                [
                    'gateway' => $result->gateway,
                    'amount' => $result->amountPaid,
                    'status' => 'successful',
                    'raw_details' => json_decode($result->rawResponse, true),
                    'paid_at' => $result->paymentDate ? date('Y-m-d H:i:s', strtotime($result->paymentDate)) : date('Y-m-d H:i:s'),
                ]
            );

            // 3. Log activity
            AtexAuditLog::create([
                'actor_id' => $order->buyerProfile->user_id ?? auth()->id(),
                'action' => 'payment_verified',
                'auditable_type' => 'order',
                'auditable_id' => $order->id,
                'new_values' => json_encode([
                    'reference' => $result->reference,
                    'gateway' => $result->gateway,
                    'amount' => $result->amountPaid,
                    'paid_at' => $result->paymentDate,
                ]),
                'ip_address' => request()->ip(),
            ]);

            Log::info("Order #{$order->order_number} payment of {$result->amountPaid} verified via " . ucfirst($result->gateway) . ". Ref: {$result->reference}");

        } catch (\Throwable $e) {
            Log::error('Error processing successful order payment: ' . $e->getMessage());
            throw $e;
        }
    }
}
