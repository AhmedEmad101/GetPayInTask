<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
class Hold extends Model
{use prunable,HasFactory;
  protected $fillable = [
        'product_id',
        'user_id',
        'qty',
        'expires_at',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function order()
    {
        return $this->hasOne(Order::class, 'hold_id');
    }
    //********************************************* */
    public function prunable()
{
    return static::where('expires_at', '<=', now());
}


}