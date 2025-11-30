<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

     public function rules(): array
    {
        return [
            'user_id'        => 'required|integer|exists:users,id',
            'payment_status' => 'required|string|in:pending,paid,failed,cancelled',
            'order_status'   => 'required|integer|min:0',
            'payment_method' => 'required|string|in:cash,card,wallet,bank_transfer',
            'product_id'   => 'required|numeric|exists:products,id',
            'qty'=> 'required|numeric|min:1',
            
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'The user ID is required.',
            'user_id.integer'  => 'The user ID must be a valid number.',
            'user_id.exists'   => 'The selected user does not exist.',
            'payment_status.required' => 'Payment status is required.',
            'payment_status.string'   => 'Payment status must be text.',
            'payment_status.in'       => 'Payment status must be one of: pending, paid, failed, cancelled.',
            'order_status.required' => 'Order status is required.',
            'order_status.integer'  => 'Order status must be a number.',
            'order_status.min'      => 'Order status cannot be negative.',
            'payment_method.required' => 'Payment method is required.',
            'payment_method.string'   => 'Payment method must be text.',
            'payment_method.in'       => 'Payment method must be one of: cash, card, wallet, bank_transfer.',
            'product_id.exists'   => 'The selected product does not exist.',
             'qty.min'=> 'quantity should be at least 1'
        ];
    }
}
