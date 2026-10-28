<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $q = (string) $request->query('q', '');

        $orders = Order::query()
            ->with('user')
            ->when($q !== '', fn ($query) => $query->whereRaw("code LIKE '%{$q}%'"))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('orders.index', ['orders' => $orders, 'q' => $q]);
    }

    public function create(): View
    {
        return view('orders.create', [
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function store(StoreOrderRequest $request, StockService $stock): RedirectResponse
    {
        $order = DB::transaction(function () use ($request, $stock): Order {
            $order = Order::create([
                'code' => 'TMP-'.Str::uuid(),
                'user_id' => $request->user()->id,
                'customer_name' => $request->validated('customer_name'),
                'customer_phone' => $request->validated('customer_phone'),
                'status' => OrderStatus::Draft,
                'total' => $request->validated('total'),
            ]);

            $order->update([
                'code' => sprintf('ORD-%s-%04d', now()->year, $order->id),
            ]);

            foreach ($request->validated('items') as $line) {
                $product = Product::findOrFail($line['product_id']);

                $stock->reserve($product, (int) $line['qty']);

                $order->items()->create([
                    'product_id' => $product->id,
                    'qty' => (int) $line['qty'],
                    'unit_price' => $line['unit_price'],
                ]);
            }

            return $order;
        });

        return redirect()->route('orders.show', $order);
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'user']);

        return view('orders.show', ['order' => $order]);
    }

    public function transition(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:draft,confirmed,shipped,done'],
        ]);

        $next = OrderStatus::from($validated['status']);

        if (! $order->status->canTransitionTo($next)) {
            return back()->withErrors([
                'status' => "Cannot move an order from {$order->status->value} to {$next->value}.",
            ]);
        }

        $order->update(['status' => $next]);

        return back()->with('status', "Order moved to {$next->value}.");
    }
}