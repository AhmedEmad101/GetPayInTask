<?php

namespace App\DTOs;

class PaymentWebhookDTO
{
    public int $order_id;
    public string $status;
    public float $amount;
    public string $payment_method;
    public ?string $transaction_id;
    public string $idempotency_key;

    public function __construct(array $data)
    {
        $this->order_id = $data['order_id'];
        $this->status = $data['status'];
        $this->amount = $data['amount'];
        $this->payment_method = $data['payment_method'];
        $this->transaction_id = $data['transaction_id'] ?? null;
        $this->idempotency_key = $data['idempotency_key'];
    }

    public static function fromRequest($request): self
    {
        return new self([
            'order_id' => $request->input('order_id'),
            'status' => $request->input('status'),
            'amount' => $request->input('amount'),
            'payment_method' => $request->input('payment_method'),
            'transaction_id' => $request->input('transaction_id'),
            'idempotency_key' => $request->header('Idempotency-Key'),
        ]);
    }

    public function toArray(): array
    {
        return [
            'order_id' => $this->order_id,
            'status' => $this->status,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'transaction_id' => $this->transaction_id,
            'idempotency_key' => $this->idempotency_key,
        ];
    }
}
