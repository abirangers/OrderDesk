@extends('layouts.app')

@section('title', $order->code)

@section('content')
    <div class="mb-3 flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('orders.index') }}" class="text-ink-muted underline underline-offset-2 hover:text-ink">Orders</a>
            <h1 class="num mt-2 text-[20px] font-semibold leading-tight">{{ $order->code }}</h1>
            <p class="mt-1">@include('orders.partials.status', ['status' => $order->status])</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @foreach ($order->status->allowedNext() as $next)
                <form method="POST" action="{{ route('orders.transition', $order) }}">
                    @csrf
                    <input type="hidden" name="status" value="{{ $next->value }}">
                    <button type="submit" class="border border-rule bg-surface px-3 py-1.5 hover:border-ink">
                        Mark {{ $next->label() }}
                    </button>
                </form>
            @endforeach

            @if ($order->status->allowedNext() === [])
                <span class="text-ink-muted">This order is closed.</span>
            @endif
        </div>
    </div>

    @error('status')
        <div class="mb-4 border-l-2 border-oxide bg-surface px-3 py-2">{{ $message }}</div>
    @enderror

    <div class="grid gap-6 sm:grid-cols-2">
        <div class="border border-rule bg-surface p-4">
            <h2 class="mb-2 text-ink-muted">Customer</h2>
            <p class="font-medium">{{ $order->customer_name }}</p>
            <p class="num mt-1 text-ink-muted">{{ $order->customer_phone }}</p>
        </div>

        <div class="border border-rule bg-surface p-4">
            <h2 class="mb-2 text-ink-muted">Record</h2>
            <dl class="grid grid-cols-[7rem_1fr] gap-y-1">
                <dt class="text-ink-muted">Owner</dt>
                <dd>{{ $order->user->email }}</dd>
                <dt class="text-ink-muted">Created</dt>
                <dd class="num">{{ $order->created_at->format('Y-m-d H:i') }}</dd>
                <dt class="text-ink-muted">Updated</dt>
                <dd class="num">{{ $order->updated_at->format('Y-m-d H:i') }}</dd>
            </dl>
        </div>
    </div>

    <div class="mt-6 border border-rule bg-surface">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[560px] border-collapse">
                <thead>
                    <tr class="border-b border-rule text-left text-ink-muted">
                        <th class="px-3 py-2 font-medium">Product</th>
                        <th class="px-3 py-2 font-medium">SKU</th>
                        <th class="px-3 py-2 text-right font-medium">Qty</th>
                        <th class="px-3 py-2 text-right font-medium">Unit price</th>
                        <th class="px-3 py-2 text-right font-medium">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr class="h-10 border-b border-rule last:border-0">
                            <td class="px-3 py-2">{{ $item->product->name }}</td>
                            <td class="num px-3 py-2 text-ink-muted">{{ $item->product->sku }}</td>
                            <td class="num px-3 py-2 text-right">{{ $item->qty }}</td>
                            <td class="num px-3 py-2 text-right">{{ number_format((float) $item->unit_price, 2) }}</td>
                            <td class="num px-3 py-2 text-right">
                                {{ number_format((float) $item->unit_price * $item->qty, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-ink">
                        <td colspan="4" class="px-3 py-2 text-right text-ink-muted">Total</td>
                        <td class="num px-3 py-2 text-right font-semibold">{{ number_format((float) $order->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection