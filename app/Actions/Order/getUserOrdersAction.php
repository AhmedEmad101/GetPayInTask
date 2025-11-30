<?php

namespace App\Actions\Order;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

final class getUserOrdersAction
{
   
    public static function execute(array $with = []): Collection
    {
        $user = auth()->user();
        if (!$user) {
            throw new \Exception('No authenticated user found.');
        }

        return Order::query()
            ->where('user_id', $user->id)
            ->with($with)
            ->latest()
            ->get();
    }
}
