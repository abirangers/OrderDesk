<?php

namespace App\Services;

use App\Exceptions\InsufficientStock;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Reserve stock for an order line.
     *
     * The row lock only means anything inside a transaction, so this method opens
     * one when the caller has not. Called from an existing transaction it nests as
     * a savepoint and the lock is held until the outer commit.
     */
    public function reserve(Product $product, int $qty): void
    {
        DB::transaction(function () use ($product, $qty): void {
            $locked = Product::query()
                ->whereKey($product->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->stock < $qty) {
                throw InsufficientStock::forProduct($locked->sku, $qty, $locked->stock);
            }

            $locked->decrement('stock', $qty);
        });
    }
}