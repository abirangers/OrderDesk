<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_term_is_not_interpreted_as_sql(): void
    {
        $user = User::factory()->create();
        Order::factory()->count(3)->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('orders.index', ['q' => "' OR '1'='1"]))
            ->assertOk()
            ->assertSee('No orders match this search.');
    }

    public function test_search_still_finds_a_matching_code(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'code' => 'ORD-2026-0099']);
        Order::factory()->count(2)->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('orders.index', ['q' => 'ORD-2026-0099']))
            ->assertOk()
            ->assertSee('ORD-2026-0099');
    }
}