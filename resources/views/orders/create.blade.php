@extends('layouts.app')

@section('title', 'New order')

@section('content')
    @php
        $payload = $products->map(fn ($p) => [
            'id' => $p->id,
            'sku' => $p->sku,
            'name' => $p->name,
            'price' => (float) $p->price,
            'stock' => $p->stock,
        ])->values();
    @endphp

    <div class="mb-3">
        <h1 class="text-[20px] font-semibold leading-tight">New order</h1>
        <p class="mt-1 text-ink-muted">Stock is reserved when the order is created.</p>
    </div>

    <form method="POST" action="{{ route('orders.store') }}" class="max-w-3xl"
          x-data="orderForm({{ Js::from($payload) }})">
        @csrf

        <div class="border border-rule bg-surface p-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block">
                    <span class="mb-1 block text-ink-muted">Customer name</span>
                    <input name="customer_name" value="{{ old('customer_name') }}" required
                           class="w-full border border-rule px-2 py-1.5 @error('customer_name') border-oxide @enderror">
                    @error('customer_name')
                        <span class="mt-1 block text-oxide">{{ $message }}</span>
                    @enderror
                </label>

                <label class="block">
                    <span class="mb-1 block text-ink-muted">Phone</span>
                    <input name="customer_phone" value="{{ old('customer_phone') }}" required
                           inputmode="tel" class="num w-full border border-rule px-2 py-1.5 @error('customer_phone') border-oxide @enderror">
                    @error('customer_phone')
                        <span class="mt-1 block text-oxide">{{ $message }}</span>
                    @enderror
                </label>
            </div>
        </div>

        <div class="mt-6 border border-rule bg-surface">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px] border-collapse">
                    <thead>
                        <tr class="border-b border-rule text-left text-ink-muted">
                            <th class="px-3 py-2 font-medium">Product</th>
                            <th class="px-3 py-2 font-medium">Qty</th>
                            <th class="px-3 py-2 font-medium">Unit price</th>
                            <th class="px-3 py-2 text-right font-medium">Subtotal</th>
                            <th class="px-3 py-2"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(line, index) in lines" :key="index">
                            <tr class="border-b border-rule last:border-0">
                                <td class="px-3 py-2">
                                    <select x-model="line.product_id" x-on:change="syncPrice(index)"
                                            :name="`items[${index}][product_id]`" required
                                            class="w-full border border-rule px-2 py-1.5">
                                        <option value="">Select product</option>
                                        <template x-for="product in products" :key="product.id">
                                            <option :value="product.id"
                                                    x-text="`${product.sku} · ${product.name} (${product.stock} in stock)`"></option>
                                        </template>
                                    </select>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" min="1" x-model="line.qty" :name="`items[${index}][qty]`" required
                                           class="num w-20 border border-rule px-2 py-1.5">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" step="0.01" min="0" x-model="line.unit_price"
                                           :name="`items[${index}][unit_price]`" required
                                           class="num w-32 border border-rule px-2 py-1.5">
                                </td>
                                <td class="num px-3 py-2 text-right"
                                    x-text="((Number(line.qty) || 0) * (Number(line.unit_price) || 0)).toFixed(2)"></td>
                                <td class="px-3 py-2 text-right">
                                    <button type="button" x-on:click="removeLine(index)"
                                            class="text-ink-muted underline underline-offset-2 hover:text-oxide"
                                            x-show="lines.length > 1">Remove</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="border-t border-rule px-3 py-2">
                <button type="button" x-on:click="addLine()"
                        class="text-ink-muted underline underline-offset-2 hover:text-ink">Add line</button>
            </div>
        </div>

        @error('items')
            <p class="mt-2 text-oxide">{{ $message }}</p>
        @enderror

        <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-baseline gap-3">
                <span class="text-ink-muted">Total</span>
                <span class="num text-[18px]" x-text="total.toFixed(2)"></span>
                <input type="hidden" name="total" :value="total.toFixed(2)" value="0">
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('orders.index') }}" class="text-ink-muted underline underline-offset-2 hover:text-ink">Cancel</a>
                <button type="submit" class="bg-oxide px-3 py-1.5 font-medium text-white hover:bg-[#9c3c24]">
                    Create order
                </button>
            </div>
        </div>
    </form>
@endsection