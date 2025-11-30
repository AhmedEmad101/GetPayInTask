<?php

namespace App\Actions\Hold;
use App\Models\Hold;
use App\Models\Product;
use App\DTOs\HoldDTO;
use Exception;
use Illuminate\Support\Facades\DB;
final class createHoldAction
{
    public static function execute(HoldDTO $dto)
    {
        return DB::transaction(function () use ($dto) {
            $product = Product::lockForUpdate()->findOrFail($dto->product_id);
          if ($dto->qty > $product->current_stock) {
                throw new Exception("Not enough stock available.");
            }
            $product->decrement('current_stock', $dto->qty);
            return Hold::create([
                'product_id' => $dto->product_id,
                'user_id'    => $dto->user_id,
                'qty'        => $dto->qty,
                'expires_at' => now()->addMinutes(2),
            ]);
        });
    }
}