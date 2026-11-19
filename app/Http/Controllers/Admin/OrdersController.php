<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusUpdated;
use Illuminate\Validation\Rule;

class OrdersController extends Controller
{
    /**
     * Display a listing of all orders.
     */
    public function index(Request $request)
    {
        $query = Order::with('user', 'items');

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('order_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('customer_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('customer_email', 'like', '%' . $searchTerm . '%')
                    ->orWhere('customer_phone', 'like', '%' . $searchTerm . '%');
            });
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'All Statuses') {
            $query->where('status', $request->status);
        }

        $orders = $query->latest('created_at')->paginate(15)->appends($request->all());
        $totalCount = Order::count();

        return view('admin.orders.index', [
            'orders' => $orders,
            'totalCount' => $totalCount,
            'currentStatus' => $request->status ?? 'All Statuses'
        ]);
    }

    /**
     * Display the specified order.
     */
    public function show($id)
    {
        $order = Order::with('user', 'items.inventory')->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Generate and download a professional order receipt PDF.
     */
    public function receipt($id)
    {
        $order = Order::with('user', 'items.inventory')->findOrFail($id);

        $pdf = Pdf::loadView('admin.orders.receipt', [
            'order' => $order,
            'isPdf' => true,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('Order_Receipt_' . $order->order_number . '.pdf');
    }

    /**
     * Render a fast HTML receipt preview for the admin modal.
     */
    public function receiptPreview($id)
    {
        $order = Order::with('user', 'items.inventory')->findOrFail($id);

        return response()
            ->view('admin.orders.receipt', [
                'order' => $order,
                'isPdf' => false,
            ])
            ->header('Cache-Control', 'private, max-age=60');
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', Rule::in(Order::statusValues())],
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        $this->sendStatusUpdateEmail($order);

        return redirect()->back()
            ->with('success', 'Order status updated successfully!');
    }

    /**
     * Update the status of multiple orders.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1|max:500',
            'ids.*' => 'required|integer|distinct|exists:orders,id',
            'status' => ['required', Rule::in(Order::statusValues())],
        ]);

        $orders = DB::transaction(function () use ($validated) {
            $orders = Order::whereIn('id', $validated['ids'])->get();

            foreach ($orders as $order) {
                $order->update(['status' => $validated['status']]);
            }

            return $orders;
        });

        foreach ($orders as $order) {
            $this->sendStatusUpdateEmail($order);
        }

        return redirect()->back()
            ->with('success', "{$orders->count()} order(s) updated successfully!");
    }

    /**
     * Delete a single order.
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->back()
            ->with('success', 'Order deleted successfully!');
    }

    /**
     * Delete multiple orders.
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1|max:500',
            'ids.*' => 'required|integer|distinct|exists:orders,id',
        ]);

        $deletedCount = DB::transaction(function () use ($validated) {
            return Order::whereIn('id', $validated['ids'])->delete();
        });

        return redirect()->back()
            ->with('success', "{$deletedCount} order(s) deleted successfully!");
    }

    /**
     * Update order payment method.
     */
    public function updatePaymentMethod(Request $request, $id)
    {
        $request->validate([
            'payment_method' => ['required', Rule::in(['cash', 'cheque', 'square_card', 'card'])],
        ]);

        $order = Order::findOrFail($id);
        $order->update([
            'payment_method' => $request->payment_method,
            'payment_status' => Order::PAYMENT_STATUS_PAID,
        ]);

        return redirect()->back()
            ->with('success', 'Order payment method updated successfully!');
    }

    /**
     * Update order payment status.
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => ['required', Rule::in(Order::paymentStatusValues())],
        ]);

        $order = Order::findOrFail($id);
        $order->update(['payment_status' => $request->payment_status]);

        return redirect()->back()
            ->with('success', 'Order payment status updated successfully!');
    }

    /**
     * Export orders to CSV.
     */
    public function exportCsv(Request $request)
    {
        $query = Order::with('user', 'items');

        if ($request->filled('status'))
            $query->where('status', $request->status);
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(fn($q) => 
                $q->where('order_number', 'like', "%$searchTerm%")
                  ->orWhere('customer_name', 'like', "%$searchTerm%")
            );
        }

        $orders = $query->get();
        $fileName = 'Orders_Report_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Order #', 'Customer', 'Email', 'Phone', 'Total', 'Order Status', 'Payment Status', 'Date'];

        $callback = function () use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->customer_name,
                    $order->customer_email,
                    $order->customer_phone,
                    '$' . number_format($order->total, 2),
                    $order->status,
                    $order->payment_status_label,
                    $order->formatPlacedAtDenver('M d, Y H:i T')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function sendStatusUpdateEmail(Order $order): void
    {
        if (empty($order->customer_email)) {
            return;
        }

        try {
            Mail::to($order->customer_email)->send(new OrderStatusUpdated($order));
        } catch (\Exception $e) {
            \Log::warning('Failed to send order status update email', [
                'order_id' => $order->id,
                'email' => $order->customer_email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
