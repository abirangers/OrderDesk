<?php

namespace App\Services;

use App\Models\Product;

class StockService
{
    /**
     * Reserve stock for an order line.
     */
    public function reserve(Product $product, int $qty): void
    {
        $product->decrement('stock', $qty);
    }
}