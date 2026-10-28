<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_an_order_records_an_event(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('orders.store'), [
            'customer_name' => 'Walk-in',
            'customer_phone' => '081200000000',
            'items' => [
                ['product_id' => \App\Models\Product::factory()->create(['stock' => 5])->id, 'qty' => 1],
            ],
        ])->assertRedirect();

        $event = OrderEvent::sole();

        $this->assertNull($event->from_status);
        $this->assertSame(OrderStatus::Draft, $event->to_status);
        $this->assertSame($user->id, $event->user_id);
    }

    public function test_a_status_change_is_recorded_with_both_ends(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => OrderStatus::Draft]);
        $order->events()->create(['user_id' => $user->id, 'to_status' => OrderStatus::Draft]);

        $this->actingAs($user)
            ->post(route('orders.transition', $order), ['status' => 'confirmed'])
            ->assertRedirect();

        $event = $order->events()->latest('id')->first();

        $this->assertSame(OrderStatus::Draft, $event->from_status);
        $this->assertSame(OrderStatus::Confirmed, $event->to_status);
    }
}