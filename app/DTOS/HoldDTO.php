<?php

namespace App\DTOs;

class HoldDTO
{
    public int $product_id;
    public int $user_id;
    public int $qty;

    public function __construct(array $data)
    {
        $this->product_id = $data['product_id'];
        $this->user_id    = $data['user_id'];
        $this->qty        = $data['qty'];
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->product_id,
            'user_id'    => $this->user_id,
            'qty'        => $this->qty,
        ];
    }
}
