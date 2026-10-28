<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\User;
use App\Policies\OrderPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_cannot_view_another_users_order(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $owner->id]);

        $this->assertFalse((new OrderPolicy)->view($other, $order));
    }

    public function test_staff_can_view_their_own_order(): void
    {
        $owner = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $owner->id]);

        $this->assertTrue((new OrderPolicy)->view($owner, $order));
    }

    public function test_admin_can_view_any_order(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $order = Order::factory()->create(['user_id' => $owner->id]);

        $this->assertTrue((new OrderPolicy)->view($admin, $order));
    }
}