<?php

namespace App\Actions\Payment;

use App\Models\Order;

final class handleFailedPaymentAction
{
    public static function execute(Order $order)
    {
        $order->update([
            'payment_status' => 'failed',
            'order_status' => 'cancelled',
        ]);

        if ($order->hold) {
            $order->hold->product->increment('current_stock', $order->hold->qty);
            $order->hold->delete(); 
        }
    }
}
