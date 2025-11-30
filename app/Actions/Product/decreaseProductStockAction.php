<?php

namespace App\Actions\Product;
use App\Models\Product;
final class decreaseProductStockAction
{
    public static function execute(Product $product , $qty)
    {
        $product = Product::query()->with('holds')->find($product->id);
        $product->update(['qty'=>$product->current_stock - $qty]);
        return $product?product:null;
    }
}