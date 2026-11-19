<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SquareWebhookController extends Controller
{
    /**
     * Handle incoming webhooks from Square.
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        $eventType = $payload['type'] ?? null;
        $eventData = $payload['data']['object']['invoice'] ?? null;

        Log::info('Square Webhook received', [
            'type' => $eventType,
            'invoice_id' => $eventData['id'] ?? null,
        ]);

        if (!$eventData || empty($eventData['id'])) {
            return response()->json(['status' => 'ignored', 'reason' => 'No invoice data found'], 200);
        }

        $squareInvoiceId = $eventData['id'];
        $invoiceStatus = strtoupper((string) ($eventData['status'] ?? ''));
        $publicUrl = $eventData['public_url'] ?? null;

        $invoice = SalesInvoice::where('square_invoice_id', $squareInvoiceId)->first();
        if (!$invoice) {
            Log::warning('Square Webhook: No matching Laravel invoice found for Square ID', ['square_invoice_id' => $squareInvoiceId]);
            return response()->json(['status' => 'not_found'], 200);
        }

        $updateData = [
            'square_status' => $invoiceStatus ?: $invoice->square_status,
        ];

        if (!empty($publicUrl)) {
            $updateData['square_public_url'] = $publicUrl;
        }

        if ($eventType === 'invoice.payment_made' || $invoiceStatus === 'PAID') {
            $updateData['status'] = SalesInvoice::STATUS_PAID;
            $updateData['square_status'] = 'PAID';
            if (!$invoice->paid_at) {
                $updateData['paid_at'] = now();
            }
            Log::info("Sales Invoice {$invoice->invoice_number} marked as PAID via Square Webhook");
        } elseif ($eventType === 'invoice.canceled' || $invoiceStatus === 'CANCELED') {
            $updateData['status'] = SalesInvoice::STATUS_UNPAID;
            $updateData['square_status'] = 'CANCELED';
            Log::info("Sales Invoice {$invoice->invoice_number} marked as CANCELED via Square Webhook");
        } elseif ($invoiceStatus === 'UNPAID' || $invoiceStatus === 'SCHEDULED') {
            $updateData['status'] = SalesInvoice::STATUS_UNPAID;
        }

        $invoice->update($updateData);

        return response()->json(['status' => 'success', 'invoice_number' => $invoice->invoice_number], 200);
    }
}
