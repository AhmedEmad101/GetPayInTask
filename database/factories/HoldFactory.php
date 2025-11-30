<?php
// database/factories/HoldFactory.php
namespace Database\Factories;

use App\Models\Hold;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class HoldFactory extends Factory
{
    protected $model = Hold::class;

    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'qty' => $this->faker->numberBetween(1, 5),
            'expires_at' => now()->addMinutes(2),
        ];
    }
}
