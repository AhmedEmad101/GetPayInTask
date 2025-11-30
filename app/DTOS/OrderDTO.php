<?php

namespace App\DTOs;

class OrderDTO
{
    public int $user_id;
    public int $product_id;
    public string $payment_status;
    public string $order_status;
    public string $payment_method;
    public float $order_amount;
    public string $order_group_id;
     public int $hold_id;
    public function __construct(array $data)
    {
        $this->user_id        = $data['user_id'];
        $this->payment_status = $data['payment_status'];
        $this->order_status   = $data['order_status'];
        $this->payment_method = $data['payment_method'];
        $this->order_group_id = $data['order_group_id'];
        $this->hold_id        = $data['hold_id'];
        $this->product_id = $data['product_id'];
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function toArray(): array
    {
        return [
            'user_id'        => $this->user_id,
            'product_id'     => $this->product_id,
            'payment_status' => $this->payment_status,
            'order_status'   => $this->order_status,
            'payment_method' => $this->payment_method,
            'order_group_id' => $this->order_group_id,
            'hold_id'        => $this->hold_id,
        ];
    }
}
