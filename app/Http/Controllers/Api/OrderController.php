<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DTOs\OrderDTO;
use App\DTOs\HoldDTO;
use App\Actions\Order\createOrderAction;
use App\Actions\Hold\createHoldAction;
use App\Actions\Order\getUserOrdersAction;
use App\Actions\Payment\ProcessPaymentAction;
use App\Http\Requests\CreateOrderRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\HoldResource;
use App\Http\Resources\OrderResource;
class OrderController extends Controller
{use ApiResponseTrait;
   public function generate_order(CreateOrderRequest $request)
   {
       try {
     $data = $request->validated();
    $holdDTO = new HoldDTO([
            'product_id' => $data['product_id'],
            'user_id'    => auth()->id(),
            'qty'        => $data['qty'],
        ]);
        $hold = createHoldAction::execute($holdDTO);
        $orderDTO = new OrderDTO(array_merge($data, ['hold_id' => $hold->id]));
        $order = createOrderAction::execute($orderDTO);
        return $this->successResponse([
            'order' => new OrderResource($order),
    ], 'success');
       }
      catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                422
            );
        }
   }
 public function get_user_orders()
{
    $orders = getUserOrdersAction::execute(['product', 'hold']);
    if($orders){
    return $this->successResponse([
        'orders' => OrderResource::collection($orders)
    ]);
}
return $this->errorResponse('No Orders found',404);
}  
}
