<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
class ProductFactory extends Factory
{ protected $model = Product::class;
    public function definition(): array
    {
        return [
            'name'         => 'iPhone 16 Pro Max',
            'description'  => 'Brand new iPhone 16 Pro Max – 256GB',
            'price'        => 49999.00,
            'current_stock' => 20,  
            'shipping_cost' => 50.00,
        ];
    }
}
