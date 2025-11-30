<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{use HasFactory;
    protected $fillable = [
        'user_id',
        'product_id',
        'payment_status',
        'order_status',
        'payment_method',
        'order_amount',
        'qty',
        'order_group_id',
        'hold_id'
    ];
     public function user()
    {
        return $this->belongsTo(User::class);
    }
     public function product()
    {
        return $this->belongsTo(Product::class);
    }

     public function hold()
    {
        return $this->belongsTo(Hold::class, 'hold_id');
    }
}
