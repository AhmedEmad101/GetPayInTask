<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HoldResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'expires_at' => $this->expires_at,
            'qty'        => $this->qty,
            'product_id' => $this->product_id,
        ];
    }
}
