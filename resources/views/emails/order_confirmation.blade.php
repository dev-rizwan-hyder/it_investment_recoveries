<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<style>
		body { font-family: Arial, Helvetica, sans-serif; color: #0f172a; background: #ffffff; margin: 0; padding: 24px; }
		.container { max-width: 600px; margin: 0 auto; }
		.meta { font-size: 14px; color: #374151; margin-bottom: 18px; }
		.meta p { margin: 6px 0; }
		.message { font-size: 16px; color: #0f172a; line-height: 1.6; }
	</style>
</head>
<body>
	@php
		$emailOrderDate = isset($order) && $order instanceof \App\Models\Order
			? $order->formatPlacedAtDenver('F j, Y g:i A T')
			: ($orderDate ?? now(\App\Models\Order::DENVER_TIMEZONE)->format('F j, Y g:i A T'));
	@endphp
	<div class="container">
		<div class="meta">
			<p><strong>Order No.:</strong> {{ $order->order_number ?? ($orderNumber ?? 'N/A') }}</p>
			<p><strong>Date:</strong> {{ $emailOrderDate }}</p>
			<p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $order->status ?? ($orderStatus ?? 'processing'))) }}</p>
		</div>

		<div class="message">
			<p>Thank you for shopping with Denver Discount Computers. Your order is currently being processed. We will notify you as soon as it is ready for pickup or shipment.</p>
			<p>Thank you.</p>
			<p>Visit our website to see exciting offers and Order Updates.</p>
			<p style="margin: 24px 0;">
				<a href="{{ \Illuminate\Support\Facades\Route::has('store.index') ? route('store.index') : url('/') }}"
					style="display: inline-block; background-color: #0071e3; color: #ffffff; padding: 12px 24px; border-radius: 8px; font-weight: 700; text-decoration: none;">
					Visit Our Website
				</a>
			</p>
		</div>
	</div>
</body>
</html>
