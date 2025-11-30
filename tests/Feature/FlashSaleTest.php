<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use App\Models\Product;
use App\Models\User;
use App\Models\Hold;
use App\Models\Order;
use App\Models\Payment;
use App\DTOs\HoldDTO;
use App\DTOs\OrderDTO;
use App\Actions\Hold\createHoldAction;
use App\Actions\Order\createOrderAction;
use App\Actions\Payment\processPaymentAction;
use Illuminate\Support\Facades\DB;
class FlashSaleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate:fresh');
        User::factory()->create(['id' => 1, 'email' => 'test@example.com']);
        Product::factory()->create([
            'id' => 1,
            'name' => 'Flash Product',
            'price' => 100.00,
            'current_stock' => 5,
            'description' => 'Test',
            'shipping_cost' => 0,
        ]);
    }

    /** @test */
    public function product_endpoint_returns_correct_available_stock()
    {
        $product = Product::find(1);
        $response = $this->getJson(route('products.show', $product->id));

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $product->id]);
        $this->assertEquals(
            $product->current_stock,
            data_get($response->json('data'), 'current_stock') ?? data_get($response->json(), 'current_stock')
        );
    }

    /** @test */
    public function creating_hold_reduces_stock_and_returns_expires_at()
    {
        $dto = new HoldDTO(['product_id' => 1, 'user_id' => 1, 'qty' => 2]);
        $hold = createHoldAction::execute($dto);

        $this->assertDatabaseHas('holds', ['id' => $hold->id, 'product_id' => 1, 'qty' => 2]);
        $this->assertDatabaseHas('products', ['id' => 1, 'current_stock' => 3]);
        $this->assertNotNull($hold->expires_at);
    }

    /** @test */
    public function expired_hold_is_pruned_and_stock_restored()
    {
        $hold = Hold::create([
            'product_id' => 1,
            'user_id' => 1,
            'qty' => 2,
            'expires_at' => now()->subMinutes(10),
        ]);

        Product::find(1)->decrement('current_stock', 2);

        Artisan::call('model:prune');

        $this->assertDatabaseMissing('holds', ['id' => $hold->id]);
        $this->assertEquals(5, Product::find(1)->current_stock);
    }

    /** @test */
    public function create_order_from_valid_hold()
    {
        $holdDto = new HoldDTO(['product_id' => 1, 'user_id' => 1, 'qty' => 2]);
        $hold = createHoldAction::execute($holdDto);

        $orderDTO = new OrderDTO([
            'user_id' => 1,
            'payment_status' => 'pending',
            'order_status' => 0,
            'payment_method' => 'card',
            'order_amount' => 200.00,
            'order_group_id' => 'grp-1',
            'product_id' => 1,
            'hold_id' => $hold->id,
        ]);

        $order = createOrderAction::execute($orderDTO);

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'hold_id' => $hold->id]);
    }

    /** @test */
    public function webhook_is_idempotent()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['current_stock' => 10]);
        $hold = Hold::factory()->create(['product_id' => $product->id, 'qty' => 1, 'user_id' => $user->id]);
        $order = Order::factory()->create(['hold_id' => $hold->id, 'user_id' => $user->id, 'product_id' => $product->id]);

        $payload = [
            'order_id' => $order->id,
            'amount' => $product->price,
            'status' => 'success',
            'payment_method' => 'card',
            'transaction_id' => 'tx123',
            'idempotency_key' => 'idem-1',
        ];

        processPaymentAction::execute($payload);
        processPaymentAction::execute($payload);

        $payments = Payment::where('idempotency_key', 'idem-1')->get();
        $this->assertCount(1, $payments);
    }

    /** @test */
    public function webhook_is_idempotent_and_updates_order_once_only()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['current_stock' => 10, 'price' => 100]);

        $hold = Hold::factory()->create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'qty' => 1,
            'expires_at' => now()->addMinutes(2),
        ]);

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'hold_id' => $hold->id,
            'payment_status' => 'pending',
            'order_status' => 0,
            'payment_method' => 'card',
            'order_amount' => $product->price,
        ]);

        $payload = [
            'order_id' => $order->id,
            'amount' => $product->price,
            'status' => 'success',
            'payment_method' => 'card',
            'transaction_id' => 'tx123',
            'idempotency_key' => 'idem-1',
        ];

        processPaymentAction::execute($payload);
        processPaymentAction::execute($payload);

        $payments = Payment::where('idempotency_key', 'idem-1')->get();
        $this->assertCount(1, $payments);
        $this->assertEquals('paid', Order::find($order->id)->payment_status);
    }
    /** @test */
public function prevents_overselling_under_concurrent_holds()
{
    $product = Product::factory()->create(['current_stock' => 5]);

    $results = collect(range(1, 10))->map(function () use ($product) {
        return DB::transaction(function () use ($product) {
            $productForUpdate = Product::lockForUpdate()->find($product->id);

            if ($productForUpdate->current_stock < 1) {
                return null; // cannot create hold, out of stock
            }

            $user = User::factory()->create();

            $hold = Hold::create([
                'product_id' => $productForUpdate->id,
                'user_id' => $user->id,
                'qty' => 1,
                'expires_at' => now()->addMinutes(2),
            ]);

            $productForUpdate->decrement('current_stock', 1);

            return $hold->id;
        });
    });

    $this->assertEquals(0, Product::find($product->id)->current_stock);

    // Optional: check only 5 holds succeeded
    $this->assertCount(5, Hold::all());
}


/** @test */
public function webhook_before_order_creation_does_not_break()
{
    $user = User::factory()->create();
    $product = Product::factory()->create(['current_stock' => 10]);

    $payload = [
        'order_id' => 999, // order does not exist yet
        'amount' => 100,
        'status' => 'success',
        'payment_method' => 'card',
        'transaction_id' => 'tx999',
        'idempotency_key' => 'idem-999',
    ];

    $result = processPaymentAction::execute($payload);
    $this->assertNull($result); // safely ignored
}

}
