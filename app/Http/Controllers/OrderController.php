<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Exceptions\InsufficientStock;
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
        $user = $request->user();
        $q = (string) $request->query('q', '');

        $orders = Order::query()
            ->with('user')
            ->when(! $user->isAdmin(), fn ($query) => $query->where('user_id', $user->id))
            ->when($q !== '', fn ($query) => $query->where('code', 'like', "%{$q}%"))
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
        try {
            $order = DB::transaction(function () use ($request, $stock): Order {
                $order = new Order([
                    'code' => 'TMP-'.Str::uuid(),
                    'user_id' => $request->user()->id,
                    'customer_name' => $request->validated('customer_name'),
                    'customer_phone' => $request->validated('customer_phone'),
                    'status' => OrderStatus::Draft,
                ]);
                $order->total = 0;
                $order->save();

                $order->code = sprintf('ORD-%s-%04d', now()->year, $order->id);
                $order->save();

                $total = 0.0;

                foreach ($request->validated('items') as $line) {
                    $product = Product::findOrFail($line['product_id']);
                    $qty = (int) $line['qty'];

                    $stock->reserve($product, $qty);

                    // unit_price is not fillable on purpose; it is copied from the
                    // catalogue, never from the request.
                    $order->items()->forceCreate([
                        'product_id' => $product->id,
                        'qty' => $qty,
                        'unit_price' => $product->price,
                    ]);

                    $total += $qty * (float) $product->price;
                }

                $order->total = $total;
                $order->save();

                return $order;
            });
        } catch (InsufficientStock $e) {
            return back()->withErrors(['items' => $e->getMessage()])->withInput();
        }

        return redirect()->route('orders.show', $order);
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        $order->load(['items.product', 'user']);

        return view('orders.show', ['order' => $order]);
    }

    public function transition(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

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