<?php

namespace App\Observers;

use App\Models\Hold;
use Illuminate\Support\Facades\DB;
class HoldObserver
{
    
    public function deleting(Hold $hold): void
    {
         DB::transaction(function () use ($hold) {
            if (!$hold->order()->exists()) {
                $hold->product->increment('current_stock', $hold->qty);
            }
        });
    }
}


