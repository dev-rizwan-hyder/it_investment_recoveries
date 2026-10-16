<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\SalesInvoice;
use App\Services\SquareInvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserInvoiceController extends Controller
{
    /**
     * Display a listing of invoices belonging to the authenticated customer.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $clientIds = $this->resolveClientIds($user);

        $invoices = SalesInvoice::with(['client', 'items'])
            ->where(function ($q) use ($clientIds, $user) {
                if (!empty($clientIds)) {
                    $q->whereIn('client_id', $clientIds);
                }
                $q->orWhereRaw('TRIM(LOWER(customer_email)) = ?', [trim(strtolower($user->email))])
                  ->orWhere('customer_email', $user->email);
            })
            ->latest()
            ->get();

        $stats = [
            'total_count' => $invoices->count(),
            'paid_count' => $invoices->filter(fn ($i) => strtolower((string) $i->status) === SalesInvoice::STATUS_PAID)->count(),
            'unpaid_count' => $invoices->filter(fn ($i) => strtolower((string) $i->status) !== SalesInvoice::STATUS_PAID)->count(),
            'total_amount' => (float) $invoices->sum('total'),
            'unpaid_amount' => (float) $invoices->filter(fn ($i) => strtolower((string) $i->status) !== SalesInvoice::STATUS_PAID)->sum('total'),
            'paid_amount' => (float) $invoices->filter(fn ($i) => strtolower((string) $i->status) === SalesInvoice::STATUS_PAID)->sum('total'),
        ];

        $invoiceRows = $invoices->map(function (SalesInvoice $invoice) {
            $categories = $invoice->items
                ->pluck('invoice_category')
                ->filter()
                ->unique()
                ->values();

            $isPaid = strtolower((string) $invoice->status) === SalesInvoice::STATUS_PAID;

            $picturePaths = collect($invoice->picture_paths ?? [])->filter()->values();
            $pictureUrls = $picturePaths->map(fn($p) => $invoice->pictureUrl($p))->values()->all();
            $firstPicture = $pictureUrls[0] ?? null;
            $firstIsImage = $firstPicture ? SalesInvoice::isImageFile($picturePaths[0]) : false;
            $firstIcon = $firstPicture ? SalesInvoice::fileIconClass($picturePaths[0]) : null;
            $firstFileName = $firstPicture ? SalesInvoice::fileName($picturePaths[0]) : null;

            return [
                'id' => $invoice->id,
                'row_key' => 'invoice-' . $invoice->id,
                'num' => $invoice->invoice_number,
                'pictures' => $pictureUrls,
                'first_picture' => $firstPicture,
                'first_is_image' => $firstIsImage,
                'first_icon' => $firstIcon,
                'first_file_name' => $firstFileName,
                'picture_count' => count($pictureUrls),
                'client' => $invoice->customer_name ?: 'Customer',
                'email' => $invoice->customer_email,
                'phone' => $invoice->customer_phone,
                'company' => $invoice->customer_company,
                'address' => $invoice->customer_address ?: ($invoice->customer_company ?: 'No address specified'),
                'customer_address' => $invoice->customer_address,
                'val' => '$' . number_format((float) $invoice->total, 2),
                'total_raw' => (float) $invoice->total,
                'status' => $invoice->status_label,
                'is_paid' => $isPaid,
                'status_class' => $invoice->status_badge_class,
                'has_square' => $invoice->hasSquareInvoice(),
                'square_status' => $invoice->square_status,
                'square_badge_class' => $invoice->square_status_badge_class,
                'square_public_url' => $invoice->square_public_url,
                'service_date' => $invoice->service_date?->format('m/d/Y') ?? $invoice->created_at?->format('m/d/Y'),
                'service_date_value' => $invoice->service_date?->format('Y-m-d') ?? $invoice->created_at?->format('Y-m-d'),
                'due_date' => $invoice->due_date?->format('m/d/Y'),
                'due_date_value' => $invoice->due_date?->format('Y-m-d'),
                'payment_method' => $invoice->payment_method,
                'payment_method_label' => $invoice->payment_method_label,
                'payment_type' => $invoice->payment_type,
                'payment_type_label' => $invoice->payment_type_label,
                'categories' => $categories->all(),
                'category_label' => $categories->isNotEmpty() ? $categories->implode(', ') : 'Service Invoice',
                'view_url' => route('user.invoices.show', $invoice->id),
                'print_url' => route('user.invoices.print', $invoice->id) . '?embedded=1',
                'pay_url' => route('user.invoices.pay-square', $invoice->id),
                'sync_url' => route('user.invoices.sync-square', $invoice->id),
            ];
        });

        return view('user.invoices.index', compact('invoices', 'invoiceRows', 'stats'));
    }

    /**
     * Display a specific invoice with items and payment options.
     */
    public function show($id)
    {
        $user = Auth::user();
        $invoice = SalesInvoice::with(['client', 'items'])->findOrFail($id);

        $this->authorizeInvoiceAccess($invoice, $user);

        return view('user.invoices.show', compact('invoice'));
    }

    /**
     * Display printable receipt view for customer.
     */
    public function printInvoice($id)
    {
        $user = Auth::user();
        $invoice = SalesInvoice::with(['client', 'items'])->findOrFail($id);

        $this->authorizeInvoiceAccess($invoice, $user);

        return response()
            ->view('user.invoices.print', compact('invoice'))
            ->header('Cache-Control', 'private, max-age=60');
    }

    /**
     * Redirect customer to Square payment checkout or generate Square invoice link on demand.
     */
    public function paySquare($id, SquareInvoiceService $squareService)
    {
        $user = Auth::user();
        $invoice = SalesInvoice::with(['client', 'items'])->findOrFail($id);

        $this->authorizeInvoiceAccess($invoice, $user);

        if (strtolower((string) $invoice->status) === SalesInvoice::STATUS_PAID) {
            return redirect()
                ->route('user.invoices.show', $invoice->id)
                ->with('info', "Invoice {$invoice->invoice_number} is already paid.");
        }

        // If public Square payment checkout URL is already available, redirect directly
        if (!empty($invoice->square_public_url)) {
            return redirect()->away($invoice->square_public_url);
        }

        // Generate Square invoice & payment link if not yet generated
        $result = $squareService->createAndSendInvoice($invoice);

        if ($result['success']) {
            $refreshed = $invoice->fresh();
            if (!empty($refreshed->square_public_url)) {
                return redirect()->away($refreshed->square_public_url);
            }

            return redirect()
                ->route('user.invoices.show', $invoice->id)
                ->with('success', 'Square payment link generated. You can now complete your payment.');
        }

        return redirect()
            ->back()
            ->with('error', 'Unable to initialize Square payment: ' . ($result['message'] ?? 'Please contact support.'));
    }

    /**
     * Sync invoice payment status from Square (e.g. after customer completes payment).
     */
    public function syncSquare($id, SquareInvoiceService $squareService)
    {
        $user = Auth::user();
        $invoice = SalesInvoice::with(['client', 'items'])->findOrFail($id);

        $this->authorizeInvoiceAccess($invoice, $user);

        if (!$invoice->hasSquareInvoice()) {
            return redirect()
                ->back()
                ->with('warning', 'This invoice does not have an active Square payment link yet.');
        }

        $result = $squareService->syncInvoiceStatus($invoice);

        if ($result['success']) {
            return redirect()
                ->back()
                ->with('success', $result['message']);
        }

        return redirect()
            ->back()
            ->with('error', $result['message']);
    }

    /**
     * Helper: Resolve Client IDs matching the authenticated user.
     */
    private function resolveClientIds($user): array
    {
        return Client::whereRaw('TRIM(LOWER(email)) = ?', [trim(strtolower($user->email))])
            ->orWhere('email', $user->email)
            ->pluck('id')
            ->all();
    }

    /**
     * Helper: Verify user is authorized to view/pay this invoice.
     */
    private function authorizeInvoiceAccess(SalesInvoice $invoice, $user): void
    {
        $clientIds = $this->resolveClientIds($user);

        $belongsToClient = !empty($invoice->client_id) && in_array($invoice->client_id, $clientIds);
        $matchesEmail = !empty($invoice->customer_email) && (trim(strtolower($invoice->customer_email)) === trim(strtolower($user->email)));

        if (!$belongsToClient && !$matchesEmail) {
            abort(403, 'Unauthorized access to this invoice.');
        }
    }
}
