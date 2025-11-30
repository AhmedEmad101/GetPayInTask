<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponseTrait;
use App\Actions\Payment\processPaymentAction;
use App\DTOs\PaymentWebhookDTO;
use Illuminate\Support\Facades\Cache;
class PaymentWebhookController extends Controller
{use ApiResponseTrait;
    public function handle(Request $request)
    {
        $dto = PaymentWebhookDTO::fromRequest($request);
        $orderExists = Order::find($dto->order_id);
        if (!$orderExists) {
            Cache::put("pending_webhook_order_{$dto->order_id}", $dto->toArray(), now()->addMinutes(5));
            return $this->successResponse(['message' => 'Webhook saved, order not yet created'], 202);
        }
        $result = ProcessPaymentAction::execute($dto->toArray());

        return $this->successResponse(['message' => 'Webhook processed', 'result' => $result], 200);
    }
}
