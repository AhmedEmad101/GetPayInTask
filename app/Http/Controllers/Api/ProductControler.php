<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Traits\ApiResponseTrait;
use App\Actions\Product\getProductAction;
class ProductControler extends Controller
{
    use ApiResponseTrait;
   public function get_product(Product $product)
   {
    $product = getProductAction::execute($product);
    if($product)
    {
         return $this->successResponse($product,'Product retrieved successfully');
    }
     return $this->errorResponse('no product found', 404);
   }
}
