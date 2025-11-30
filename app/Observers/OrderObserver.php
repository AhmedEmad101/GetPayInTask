<?php

namespace App\Observers;

use App\Models\Order;
use App\Actions\Payment\processPaymentAction;
class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $pending = cache()->pull('pending_payment_' . $order->id);
        if ($pending) {
            processPaymentAction::execute($pending);
        }
    }

   
}
