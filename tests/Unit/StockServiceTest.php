<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_reserving_more_than_available_is_rejected(): void
    {
        $product = Product::factory()->create(['stock' => 1]);

        $this->expectException(RuntimeException::class);

        app(StockService::class)->reserve($product, 2);
    }

    public function test_two_sequential_reservations_cannot_drive_stock_below_zero(): void
    {
        $product = Product::factory()->create(['stock' => 1]);
        $service = app(StockService::class);

        $service->reserve($product, 1);

        try {
            $service->reserve($product, 1);
            $this->fail('The second reservation should have been rejected.');
        } catch (RuntimeException) {
            // expected
        }

        $this->assertSame(0, $product->fresh()->stock);
    }
}