<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'order_status'   => $this->order_status,
            'payment_status' => $this->payment_status,
            'order_amount'   => $this->order_amount,
            'payment_method' => $this->payment_method,
            'hold_id'        => $this->hold_id,
        ];
    }
}
