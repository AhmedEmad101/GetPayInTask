<?php

namespace App\Actions\Product;
use App\Models\Product;
final class checkStockValidityAction
{
    public static function execute($id)
    {
        $product = Product::query()->with('holds')->find($id);
        if($product){
        return $product->current_stock>0?true:false;
        }
        return false;
    }
}