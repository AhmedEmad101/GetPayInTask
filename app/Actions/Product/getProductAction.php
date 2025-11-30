<?php

namespace App\Actions\Product;
use App\Models\Product;

final class getProductAction
{
    public static function execute(Product $product)
    {
        $product = Product::find($product->id);
        return $product?$product:null;
    }
}