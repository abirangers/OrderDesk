<?php

namespace Tests\Unit;

use App\Enums\OrderStatus;
use PHPUnit\Framework\TestCase;

class OrderStatusTest extends TestCase
{
    public function test_draft_moves_only_to_confirmed(): void
    {
        $this->assertTrue(OrderStatus::Draft->canTransitionTo(OrderStatus::Confirmed));
        $this->assertFalse(OrderStatus::Draft->canTransitionTo(OrderStatus::Shipped));
        $this->assertFalse(OrderStatus::Draft->canTransitionTo(OrderStatus::Done));
    }

    public function test_done_is_terminal(): void
    {
        $this->assertSame([], OrderStatus::Done->allowedNext());
    }
}