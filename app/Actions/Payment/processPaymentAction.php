<?php

namespace App\Actions\Payment;
use App\Actions\Payment\handleFailedPaymentAction;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

final class processPaymentAction
{

    public static function execute(array $data)
    {
        $idempotencyKey = $data['idempotency_key'];

        // Check if already processed
        $existing = Payment::where('idempotency_key', $idempotencyKey)->first();
        if ($existing) {
            return ['message' => 'Already processed'];
        }

        return DB::transaction(function () use ($data) {

            $order = Order::lockForUpdate()->find($data['order_id']);
            if (!$order) return null;

            $payment = Payment::create([
                'order_id' => $order->id,
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'],
                'payment_status' => $data['status'],
                'transaction_id' => $data['transaction_id'],
                'idempotency_key' => $data['idempotency_key'],
            ]);

            if ($data['status'] === 'success') {
                $order->update([
                    'payment_status' => 'paid',
                    'order_status' => 'confirmed',
                ]);
            } else {
                HandleFailedPaymentAction::execute($order);
            }

            return $payment;
        });
    }
}
