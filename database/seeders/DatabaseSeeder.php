<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // role is deliberately not mass-assignable, so it is set explicitly here.
        $admin = User::factory()->create([
            'name' => 'Admin Orderdesk',
            'email' => 'admin@orderdesk.test',
        ]);
        $admin->forceFill(['role' => 'admin'])->save();

        /** @var Collection<int, User> $staff */
        $staff = collect(['staff1@orderdesk.test', 'staff2@orderdesk.test'])
            ->map(fn (string $email) => User::factory()->create(['email' => $email]));

        $products = Product::factory()->count(8)->create();

        foreach (range(1, 20) as $i) {
            $order = Order::factory()->create([
                'user_id' => $staff->random()->id,
                'code' => sprintf('ORD-2026-%04d', $i),
                'status' => ['draft', 'confirmed', 'shipped', 'done'][$i % 4],
            ]);

            $total = 0.0;

            foreach ($products->random(rand(1, 3)) as $product) {
                $qty = rand(1, 4);

                $order->items()->create([
                    'product_id' => $product->id,
                    'qty' => $qty,
                    'unit_price' => $product->price,
                ]);

                $total += $qty * (float) $product->price;
            }

            $order->update(['total' => $total]);

            // Walk the order from draft to wherever the seed left it, so the
            // detail screen has a history that matches the status.
            $order->events()->create([
                'user_id' => $order->user_id,
                'to_status' => \App\Enums\OrderStatus::Draft,
            ]);

            $from = \App\Enums\OrderStatus::Draft;

            while ($from !== $order->status) {
                $next = $from->allowedNext()[0] ?? null;

                if ($next === null) {
                    break;
                }

                $order->events()->create([
                    'user_id' => $order->user_id,
                    'from_status' => $from,
                    'to_status' => $next,
                ]);

                $from = $next;
            }
        }
    }
}