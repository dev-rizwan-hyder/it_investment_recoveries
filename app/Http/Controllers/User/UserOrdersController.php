<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class UserOrdersController extends Controller
{
    /**
     * Display user orders listing
     */
    public function index()
    {
        $user = auth()->user();
        
        $orders = Order::where('user_id', $user->id)
            ->with('items')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('user.orders.index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Show order details
     */
    public function show($id)
    {
        $user = auth()->user();
        
        $order = Order::where('user_id', $user->id)
            ->with('items.inventory')
            ->findOrFail($id);

        return view('user.orders.show', [
            'order' => $order,
        ]);
    }

    /**
     * Cancel order
     */
    public function cancel($id)
    {
        $user = auth()->user();
        
        $order = Order::where('user_id', $user->id)
            ->findOrFail($id);

        // Only pending orders can be cancelled
        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be cancelled');
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Order cancelled successfully');
    }
}
