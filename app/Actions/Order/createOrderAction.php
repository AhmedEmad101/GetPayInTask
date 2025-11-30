<?php

namespace App\Actions\Order;
use App\Models\Order;
use App\Models\Hold;
use App\DTOs\OrderDTO;
use Illuminate\Support\Facades\DB;
final class createOrderAction
{
    public static function execute(OrderDTO $dto): ?Order
    {
    {
        return DB::transaction(function () use ($dto) {
            $hold = Hold::lockForUpdate()->find($dto->hold_id);

            if (!$hold) {
                throw new \Exception("Hold not found or expired");
            }
            $product = $hold->product;
            if ($hold->qty > $product->current_stock) {
                throw new \Exception("Insufficient stock for the hold");
            }
            $order = Order::create($dto->toArray());
            return $order;
        });
    }
}
}