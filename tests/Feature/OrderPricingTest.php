<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPricingTest extends TestCase
{
    use RefreshDatabase;

    public function test_total_and_unit_price_come_from_the_catalogue(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100_000, 'stock' => 10]);

        $this->actingAs($user)->post(route('orders.store'), [
            'customer_name' => 'Walk-in',
            'customer_phone' => '081200000000',
            'total' => '1.00',
            'items' => [
                ['product_id' => $product->id, 'qty' => 2, 'unit_price' => '1.00'],
            ],
        ])->assertRedirect();

        $order = Order::sole();

        $this->assertSame('200000.00', $order->total);
        $this->assertSame('100000.00', $order->items->first()->unit_price);
    }
}