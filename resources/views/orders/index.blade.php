@extends('layouts.app')

@section('title', 'Orders')

@section('content')
    <div class="mb-3 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-[20px] font-semibold leading-tight">Orders</h1>
            <p class="mt-1 text-ink-muted">{{ $orders->total() }} orders</p>
        </div>

        <a href="{{ route('orders.create') }}"
           class="btn bg-oxide px-3 py-1.5 font-medium text-white hover:bg-[#9c3c24]">New order</a>
    </div>

    <form method="GET" action="{{ route('orders.index') }}" class="mb-4 flex flex-wrap items-center gap-2">
        <label for="q" class="sr-only">Search by order code</label>
        <input id="q" name="q" value="{{ $q }}" placeholder="Search order code"
               class="w-full max-w-xs border border-rule bg-surface px-2 py-1.5">
        <button type="submit" class="border border-rule bg-surface px-3 py-1.5 hover:border-ink">Search</button>
        @if ($q !== '')
            <a href="{{ route('orders.index') }}" class="text-ink-muted underline underline-offset-2 hover:text-ink">Clear</a>
        @endif
    </form>

    <div class="border border-rule bg-surface">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] border-collapse">
                <thead>
                    <tr class="border-b border-rule text-left text-ink-muted">
                        <th class="px-3 py-2 font-medium">Code</th>
                        <th class="px-3 py-2 font-medium">Customer</th>
                        <th class="px-3 py-2 font-medium">Owner</th>
                        <th class="px-3 py-2 font-medium">Status</th>
                        <th class="px-3 py-2 text-right font-medium">Total</th>
                        <th class="px-3 py-2 font-medium">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr class="h-10 border-b border-rule last:border-0 hover:bg-paper">
                            <td class="px-3 py-2 whitespace-nowrap">
                                <a href="{{ route('orders.show', $order) }}"
                                   class="num hover:underline hover:underline-offset-2">{{ $order->code }}</a>
                            </td>
                            <td class="px-3 py-2">{{ $order->customer_name }}</td>
                            <td class="px-3 py-2 text-ink-muted">{{ $order->user->email }}</td>
                            <td class="px-3 py-2">@include('orders.partials.status', ['status' => $order->status])</td>
                            <td class="num px-3 py-2 text-right">{{ number_format((float) $order->total, 2) }}</td>
                            <td class="num px-3 py-2 whitespace-nowrap text-ink-muted">{{ $order->created_at->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-8 text-center text-ink-muted">
                                No orders match this search.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
@endsection