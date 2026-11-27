@extends('layouts.admin')

@section('content')
    <div class="w-full max-w-full overflow-x-hidden animate-in fade-in duration-500">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Orders</h1>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Action Toolbar --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm mb-6">
            <div class="flex flex-wrap items-center gap-2 mb-4">
                {{-- Export Button --}}
                <a href="{{ route('admin.orders.export', request()->all()) }}"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 rounded-xl font-bold text-[12px] flex items-center transition-all">
                    <i class="fas fa-file-export mr-2 text-slate-400"></i> Export CSV
                </a>

                @php
                    $orderStatuses = \App\Models\Order::statusChoices();
                @endphp

                <select id="bulkStatusSelect"
                    class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-[12px] font-bold text-slate-600 outline-none min-w-[180px]">
                    <option value="">Select New Status</option>
                    @foreach ($orderStatuses as $status => $label)
                        <option value="{{ $status }}">{{ $label }}</option>
                    @endforeach
                </select>

                <button id="bulkUpdateStatusBtn" type="button" disabled
                    class="bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white px-4 py-2.5 rounded-xl font-bold text-[12px] flex items-center border border-blue-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-blue-50 disabled:hover:text-blue-600">
                    <i class="fas fa-edit mr-2"></i> Update Selected
                </button>

                <button id="bulkDeleteBtn" type="button" disabled
                    class="bg-red-50 hover:bg-red-600 text-red-600 hover:text-white px-4 py-2.5 rounded-xl font-bold text-[12px] flex items-center border border-red-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-red-50 disabled:hover:text-red-600">
                    <i class="fas fa-trash-alt mr-2"></i>
                    Delete Selected (<span id="selectedOrderCount">0</span>)
                </button>

                <button
                    class="bg-[#2563eb] hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-[12px] flex items-center ml-auto shadow-lg shadow-blue-100">
                    <i class="fas fa-sync-alt mr-2"></i> Sync to Shopify
                </button>
            </div>

            {{-- Filters --}}
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-50">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" id="searchInput" value="{{ request('search') }}" placeholder="Search orders..."
                        class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500/10 outline-none">
                </div>

                <select id="statusFilter"
                    class="bg-slate-50 border-none rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 outline-none min-w-[180px]">
                    <option value="">All Statuses</option>
                    @foreach ($orderStatuses as $status => $label)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ $label }}</option>
                    @endforeach
                </select>

                <button onclick="window.location.href='{{ route('admin.orders.index') }}'"
                    class="p-2.5 bg-slate-100 text-slate-500 rounded-xl hover:bg-slate-200">
                    <i class="fas fa-redo-alt"></i>
                </button>
            </div>
        </div>

        <form id="bulkStatusForm" action="{{ route('admin.orders.bulk-status') }}" method="POST" class="hidden">
            @csrf
            @method('PUT')
            <input type="hidden" id="bulkStatusValue" name="status">
        </form>

        <form id="bulkDeleteForm" action="{{ route('admin.orders.bulk-delete') }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="w-full overflow-x-auto cursor-grab select-none global-drag-scroll">
                <table class="w-full text-left border-collapse table-auto">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr class="text-[11px] uppercase font-bold text-slate-600 tracking-wider">
                            <th class="px-4 py-4 text-center w-14">
                                <input id="selectAllOrders" type="checkbox"
                                    class="h-4 w-4 cursor-pointer rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                    aria-label="Select all orders on this page"
                                    title="Select all orders on this page"
                                    {{ $orders->isEmpty() ? 'disabled' : '' }}>
                            </th>
                            <th class="px-6 py-4 text-center w-16">Order #</th>
                            <th class="px-6 py-4">Customer</th>
                            <th class="px-6 py-4">Order Items</th>
                            <th class="px-6 py-4 text-center">Quantity</th>
                            <th class="px-6 py-4 text-center">Images</th>
                            <th class="px-6 py-4 text-right">Total</th>
                            <th class="px-6 py-4 text-center">Order Status</th>
                            <th class="px-6 py-4 text-center">Payment Status</th>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($orders as $order)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-4 text-center">
                                    <input type="checkbox" value="{{ $order->id }}"
                                        class="order-checkbox h-4 w-4 cursor-pointer rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                        aria-label="Select order {{ $order->order_number }}">
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-slate-600">
                                    #{{ $order->order_number }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-900 text-sm">{{ $order->customer_name }}</div>
                                    <div class="text-xs text-slate-500">{{ $order->customer_phone ?? 'N/A' }}</div>
                                    @if($order->customer_email)
                                        <div class="text-[11px] text-slate-400 truncate max-w-[170px]" title="{{ $order->customer_email }}">
                                            {{ $order->customer_email }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1.5 max-w-xs">
                                        @forelse($order->items as $item)
                                            @php
                                                $itemName = $item->product_name ?: ($item->device_type ?: 'Product Item');
                                            @endphp
                                            <div class="text-sm font-semibold text-slate-800 leading-snug" title="{{ $itemName }}">
                                                @if($order->items->count() > 1)
                                                    <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-blue-100 text-blue-700 text-[10px] font-bold mr-1 shrink-0">
                                                        {{ $loop->iteration }}
                                                    </span>
                                                @endif
                                                <span class="line-clamp-2 inline">{{ $itemName }}</span>
                                            </div>
                                        @empty
                                            <span class="text-xs text-slate-400 italic">No items</span>
                                        @endforelse
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-bold text-xs">
                                        {{ $order->items->sum('quantity') }}
                                    </span>
                                </td>

                                @php
                                    $productGroups = $order->getProductImagesGrouped();
                                    $totalPhotos = $order->getAllProductPhotoUrls();
                                    $totalPhotoCount = count($totalPhotos);
                                @endphp
                                <td class="px-6 py-4 text-center">
                                    @if($totalPhotoCount > 0)
                                        <button type="button"
                                            class="inline-flex items-center justify-center cursor-pointer group/order-img p-1 rounded-xl hover:bg-slate-100 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                            onclick="openOrderProductGallery({{ json_encode([
                                                'order_number' => $order->order_number,
                                                'customer_name' => $order->customer_name,
                                                'products' => $productGroups
                                            ]) }})"
                                            title="View {{ $totalPhotoCount }} product image{{ $totalPhotoCount > 1 ? 's' : '' }}">
                                            
                                            <div class="relative flex items-center justify-center">
                                                @if(count($productGroups) > 1)
                                                    {{-- Multiple Products: Stacked thumbnail avatars --}}
                                                    <div class="flex items-center -space-x-3 hover:space-x-1 transition-all duration-200">
                                                        @foreach(array_slice($productGroups, 0, 3) as $pIdx => $prod)
                                                            <div class="relative rounded-xl overflow-hidden ring-2 ring-white shadow-sm transition-transform duration-200 group-hover/order-img:scale-105" style="z-index: {{ 10 - $pIdx }};">
                                                                <img src="{{ $prod['first_photo'] }}" 
                                                                    alt="{{ $prod['product_name'] }}"
                                                                    class="h-9 w-9 object-cover bg-slate-100"
                                                                    onerror="this.src='{{ asset('images/placeholder.png') }}'">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    {{-- Single Product: clean thumbnail with hover zoom effect --}}
                                                    @php $singleProduct = $productGroups[0] ?? null; @endphp
                                                    <div class="relative rounded-xl overflow-hidden ring-1 ring-slate-200 group-hover/order-img:ring-2 group-hover/order-img:ring-blue-500 shadow-sm transition-all duration-200 group-hover/order-img:scale-105">
                                                        <img src="{{ $singleProduct['first_photo'] ?? asset('images/placeholder.png') }}"
                                                            alt="{{ $singleProduct['product_name'] ?? 'Product' }}"
                                                            class="h-9 w-9 object-cover bg-slate-100"
                                                            onerror="this.src='{{ asset('images/placeholder.png') }}'">
                                                    </div>
                                                @endif

                                                @if($totalPhotoCount > 1)
                                                    <span class="absolute -top-1.5 -right-2 bg-blue-600 text-white text-[9px] font-black rounded-full h-4 min-w-[16px] px-1 flex items-center justify-center shadow-md border border-white z-20">
                                                        +{{ $totalPhotoCount - 1 }}
                                                    </span>
                                                @endif
                                            </div>
                                        </button>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-300">
                                            <i class="far fa-image text-xs"></i>
                                            <span>No img</span>
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="font-bold text-emerald-600">${{ number_format($order->total, 2) }}</div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PUT')
                                        <select name="status"
                                            class="min-w-[140px] px-3 py-2 rounded-lg text-xs font-bold uppercase border outline-none transition-colors {{ \App\Models\Order::statusSelectClass($order->status) }}"
                                            onchange="this.form.submit()">
                                            @foreach ($orderStatuses as $status => $label)
                                                <option value="{{ $status }}" {{ ($order->status === $status || ($order->status === 'completed' && $status === \App\Models\Order::STATUS_SHIPPED)) ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center rounded-full px-3 py-1 text-xs font-black uppercase tracking-wide {{ \App\Models\Order::paymentStatusBadgeClass($order->payment_status) }}">
                                        {{ $order->payment_status_label }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $order->formatPlacedAtDenver('M d, Y') }}
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-3 items-center">
                                        <button type="button" title="Receipt"
                                            onclick="openReceiptModal('{{ route('admin.orders.receipt.preview', $order->id) }}', '{{ route('admin.orders.receipt', $order->id) }}')"
                                            class="text-emerald-600 hover:text-emerald-800 font-semibold">
                                            <i class="fas fa-file-invoice"></i>
                                        </button>
                                        <a href="{{ route('admin.orders.show', $order->id) }}" title="View"
                                            class="text-blue-600 hover:text-blue-800 font-semibold">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" title="Delete" class="text-red-600 hover:text-red-800 font-semibold"
                                                onclick="if (confirm('Are you sure you want to delete this order?')) this.closest('form').submit();">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-6 py-16 text-center text-slate-500">
                                    <i class="fas fa-inbox text-4xl mb-4 text-slate-300 block"></i>
                                    No orders found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="bg-slate-50 border-t border-slate-100 px-6 py-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>

    {{-- Receipt Preview Modal --}}
    <div id="receiptModal" class="hidden fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="closeReceiptModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-6xl h-[92vh] overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between bg-slate-50">
                <div>
                    <h3 class="text-lg font-black text-slate-800">Receipt Preview</h3>
                    <p class="text-xs text-slate-500">Professional order receipt</p>
                </div>
                <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                    <button id="receiptPrintButton" type="button" onclick="printReceiptPreview()"
                        class="px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                    <a id="receiptOpenInNewTab" href="#" target="_blank" class="px-4 py-2 rounded-lg bg-white hover:bg-slate-100 text-slate-700 text-sm font-bold border border-slate-200">
                        <i class="fas fa-external-link-alt mr-2"></i> Preview
                    </a>
                    <a id="receiptPdfLink" href="#" target="_blank" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">
                        <i class="fas fa-file-pdf mr-2"></i> PDF
                    </a>
                    <button type="button" onclick="closeReceiptModal()" class="h-9 w-9 rounded-full hover:bg-slate-100 text-slate-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="relative flex-1 bg-slate-100">
                <div id="receiptLoading" class="absolute inset-0 z-10 flex items-center justify-center bg-slate-100">
                    <div class="rounded-xl border border-slate-200 bg-white px-6 py-5 shadow-sm text-center">
                        <div class="mx-auto mb-3 h-10 w-10 rounded-full border-4 border-slate-200 border-t-emerald-600 animate-spin"></div>
                        <div class="text-sm font-black text-slate-800">Loading receipt</div>
                        <div class="text-xs text-slate-500 mt-1">Preparing preview...</div>
                    </div>
                </div>
                <iframe id="receiptIframe" src="about:blank" class="w-full h-full border-0 bg-white"></iframe>
            </div>
        </div>
    </div>

    {{-- Order Product Images Gallery Modal --}}
    <div id="orderProductImageModal" class="hidden fixed inset-0 z-[130] flex items-center justify-center p-2 sm:p-4 select-none animate-in fade-in duration-200">
        <div class="fixed inset-0 bg-slate-950/85 backdrop-blur-md" onclick="closeOrderProductGallery()"></div>
        <div class="relative bg-slate-900 rounded-2xl shadow-2xl w-full max-w-5xl h-[92vh] overflow-hidden flex flex-col border border-white/10 z-10" onclick="event.stopPropagation()">
            
            {{-- Modal Top Bar --}}
            <div class="px-5 py-3.5 border-b border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-900/90 backdrop-blur-md">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="px-2.5 py-1 rounded-lg bg-blue-500/20 text-blue-400 font-mono font-black text-xs border border-blue-500/30 shrink-0" id="orderGalleryOrderNumber">
                        #ORD
                    </span>
                    <div class="min-w-0">
                        <div class="text-white font-bold text-sm truncate flex items-center gap-2">
                            <span id="orderGalleryCustomerName">Customer</span>
                            <span class="text-slate-500 text-xs hidden sm:inline">&bull;</span>
                            <span id="orderGalleryCounter" class="text-xs font-semibold text-slate-400">1 / 1</span>
                        </div>
                        <div id="orderGalleryProductName" class="text-xs text-blue-300 font-medium truncate max-w-md sm:max-w-lg">
                            Product Name
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-end sm:self-auto shrink-0">
                    {{-- Zoom Toolbar --}}
                    <div class="flex items-center gap-1 bg-white/5 p-1 rounded-xl border border-white/10">
                        <button type="button" onclick="orderGalleryZoomOut()" title="Zoom Out (-)" class="h-8 w-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all active:scale-95">
                            <i class="fas fa-search-minus text-xs"></i>
                        </button>
                        <span id="orderGalleryZoomBadge" class="px-2 text-xs font-mono font-bold text-white min-w-[46px] text-center">100%</span>
                        <button type="button" onclick="orderGalleryZoomIn()" title="Zoom In (+)" class="h-8 w-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all active:scale-95">
                            <i class="fas fa-search-plus text-xs"></i>
                        </button>
                        <button type="button" onclick="orderGalleryResetZoom()" title="Reset Zoom" class="h-8 px-2.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-all active:scale-95">
                            <i class="fas fa-compress text-xs"></i>
                        </button>
                        <button type="button" onclick="orderGalleryRotate()" title="Rotate 90°" class="h-8 px-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-all active:scale-95">
                            <i class="fas fa-redo text-xs"></i>
                        </button>
                    </div>

                    <a id="orderGalleryDownloadBtn" href="#" target="_blank" download class="h-8 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1.5 transition-all shadow-md active:scale-95" title="Download Image">
                        <i class="fas fa-download text-xs"></i>
                        <span class="hidden md:inline">Save</span>
                    </a>

                    <button type="button" onclick="closeOrderProductGallery()" class="h-8 w-8 rounded-xl bg-white/10 hover:bg-white/20 text-white/80 hover:text-white flex items-center justify-center transition-all" title="Close (Esc)">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>

            {{-- Product Selector Filter Tabs (Visible if multiple products) --}}
            <div id="orderGalleryProductTabs" class="hidden px-5 py-2.5 border-b border-white/5 bg-slate-950/40 flex items-center gap-2 overflow-x-auto global-drag-scroll">
                <!-- Dynamically rendered tabs -->
            </div>

            {{-- Main Image Area --}}
            <div id="orderGalleryViewport" class="relative flex-1 bg-black/60 flex items-center justify-center overflow-hidden p-4">
                <img id="orderGalleryMainImg" src="" alt="Product Photo"
                    class="rounded-xl shadow-2xl transition-transform duration-150 ease-out select-none"
                    style="max-width: 90%; max-height: 90%; object-fit: contain; cursor: grab;"
                    onerror="this.src='{{ asset('images/placeholder.png') }}'">

                {{-- Prev / Next Navigation Arrows --}}
                <button id="orderGalleryPrevBtn" type="button" onclick="orderGalleryPrev(); event.stopPropagation();"
                    class="absolute left-4 top-1/2 -translate-y-1/2 bg-black/60 hover:bg-black/90 text-white w-10 h-10 rounded-full flex items-center justify-center shadow-lg transition-all border border-white/20 z-10 active:scale-95" title="Previous Image (Left Arrow)">
                    <i class="fas fa-chevron-left text-sm"></i>
                </button>
                <button id="orderGalleryNextBtn" type="button" onclick="orderGalleryNext(); event.stopPropagation();"
                    class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/60 hover:bg-black/90 text-white w-10 h-10 rounded-full flex items-center justify-center shadow-lg transition-all border border-white/20 z-10 active:scale-95" title="Next Image (Right Arrow)">
                    <i class="fas fa-chevron-right text-sm"></i>
                </button>
            </div>

            {{-- Bottom Thumbnail Strip --}}
            <div class="px-5 py-3 border-t border-white/10 bg-slate-900/90 backdrop-blur-md">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        Product Photos (<span id="orderGalleryTotalThumbCount">0</span>)
                    </div>
                    <div id="orderGalleryActiveProductBadge" class="text-[11px] font-semibold text-blue-400 truncate max-w-sm"></div>
                </div>
                <div id="orderGalleryThumbnails" class="flex items-center gap-2.5 overflow-x-auto pb-1 global-drag-scroll">
                    <!-- Dynamically rendered thumbnails -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Order Product Gallery State
        let orderGalleryData = null;
        let orderGalleryImages = []; // Array of { url, productName, productIndex, photoIndex, productQty, productPrice }
        let orderGalleryCurrentIndex = 0;
        let orderGalleryZoomScale = 1;
        let orderGalleryTranslateX = 0;
        let orderGalleryTranslateY = 0;
        let orderGalleryRotation = 0;
        let orderGalleryIsPanning = false;
        let orderGalleryPanStartX = 0;
        let orderGalleryPanStartY = 0;
        let orderGalleryFilterProductIdx = -1; // -1 for all products

        window.openOrderProductGallery = function(orderData, initialIndex = 0) {
            orderGalleryData = orderData;
            orderGalleryFilterProductIdx = -1;
            buildOrderGalleryImagesList();

            if (orderGalleryImages.length === 0) return;

            orderGalleryCurrentIndex = Math.min(Math.max(0, initialIndex), orderGalleryImages.length - 1);
            
            const orderNumEl = document.getElementById('orderGalleryOrderNumber');
            const custNameEl = document.getElementById('orderGalleryCustomerName');
            if (orderNumEl) orderNumEl.textContent = '#' + (orderData.order_number || '');
            if (custNameEl) custNameEl.textContent = orderData.customer_name || 'Customer';

            renderProductTabs();
            orderGalleryResetZoom();
            renderOrderGalleryActiveImage();
            renderOrderGalleryThumbnails();

            const modal = document.getElementById('orderProductImageModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        };

        window.closeOrderProductGallery = function() {
            const modal = document.getElementById('orderProductImageModal');
            if (!modal) return;
            modal.classList.add('hidden');
            document.body.style.overflow = '';
            orderGalleryResetZoom();
        };

        function buildOrderGalleryImagesList() {
            orderGalleryImages = [];
            if (!orderGalleryData || !Array.isArray(orderGalleryData.products)) return;

            orderGalleryData.products.forEach((prod, pIdx) => {
                if (orderGalleryFilterProductIdx !== -1 && orderGalleryFilterProductIdx !== pIdx) {
                    return;
                }
                const photos = Array.isArray(prod.photos) && prod.photos.length > 0 
                    ? prod.photos 
                    : (prod.first_photo ? [prod.first_photo] : []);
                
                photos.forEach((url, photoIdx) => {
                    orderGalleryImages.push({
                        url: url,
                        productName: prod.product_name || 'Product',
                        productIndex: pIdx,
                        photoIndex: photoIdx,
                        productQty: prod.quantity || 1,
                        productPrice: prod.unit_price || 0
                    });
                });
            });
        }

        function renderProductTabs() {
            const tabsContainer = document.getElementById('orderGalleryProductTabs');
            if (!tabsContainer || !orderGalleryData || !Array.isArray(orderGalleryData.products)) return;

            if (orderGalleryData.products.length <= 1) {
                tabsContainer.classList.add('hidden');
                return;
            }

            tabsContainer.classList.remove('hidden');
            tabsContainer.innerHTML = '';

            // "All Items" tab
            const totalPhotos = orderGalleryData.products.reduce((acc, p) => acc + (p.photos ? p.photos.length : 0), 0);
            const allBtn = document.createElement('button');
            allBtn.type = 'button';
            allBtn.className = `px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 ${orderGalleryFilterProductIdx === -1 ? 'bg-blue-600 text-white shadow-md shadow-blue-500/25 ring-2 ring-blue-400/30' : 'bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10'}`;
            allBtn.innerHTML = `<i class="fas fa-boxes text-[10px]"></i> <span>All Items</span> <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-black/30 font-mono">${totalPhotos}</span>`;
            allBtn.onclick = () => {
                orderGalleryFilterProductIdx = -1;
                buildOrderGalleryImagesList();
                orderGalleryCurrentIndex = 0;
                renderProductTabs();
                renderOrderGalleryActiveImage();
                renderOrderGalleryThumbnails();
            };
            tabsContainer.appendChild(allBtn);

            // Individual product tabs
            orderGalleryData.products.forEach((prod, pIdx) => {
                const prodPhotoCount = Array.isArray(prod.photos) ? prod.photos.length : 0;
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = `px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 ${orderGalleryFilterProductIdx === pIdx ? 'bg-blue-600 text-white shadow-md shadow-blue-500/25 ring-2 ring-blue-400/30' : 'bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10'}`;
                btn.innerHTML = `<span class="truncate max-w-[140px] sm:max-w-[220px]" title="${prod.product_name}">${prod.product_name}</span> <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-black/30 font-mono">${prodPhotoCount}</span>`;
                btn.onclick = () => {
                    orderGalleryFilterProductIdx = pIdx;
                    buildOrderGalleryImagesList();
                    orderGalleryCurrentIndex = 0;
                    renderProductTabs();
                    renderOrderGalleryActiveImage();
                    renderOrderGalleryThumbnails();
                };
                tabsContainer.appendChild(btn);
            });
        }

        function renderOrderGalleryActiveImage() {
            if (!orderGalleryImages || orderGalleryImages.length === 0) return;

            const current = orderGalleryImages[orderGalleryCurrentIndex];
            const mainImg = document.getElementById('orderGalleryMainImg');
            const counterEl = document.getElementById('orderGalleryCounter');
            const productNameEl = document.getElementById('orderGalleryProductName');
            const downloadBtn = document.getElementById('orderGalleryDownloadBtn');
            const activeProductBadge = document.getElementById('orderGalleryActiveProductBadge');

            if (mainImg) {
                mainImg.src = current.url;
            }
            if (counterEl) {
                counterEl.textContent = `${orderGalleryCurrentIndex + 1} of ${orderGalleryImages.length}`;
            }
            if (productNameEl) {
                productNameEl.textContent = current.productName;
            }
            if (activeProductBadge) {
                activeProductBadge.textContent = current.productName;
            }
            if (downloadBtn) {
                downloadBtn.href = current.url;
                const cleanName = (current.productName || 'product').replace(/[^a-zA-Z0-9_-]/g, '_');
                downloadBtn.setAttribute('download', `${cleanName}_photo_${orderGalleryCurrentIndex + 1}.jpg`);
            }

            const prevBtn = document.getElementById('orderGalleryPrevBtn');
            const nextBtn = document.getElementById('orderGalleryNextBtn');
            if (prevBtn) prevBtn.style.display = orderGalleryImages.length > 1 && orderGalleryCurrentIndex > 0 ? 'flex' : 'none';
            if (nextBtn) nextBtn.style.display = orderGalleryImages.length > 1 && orderGalleryCurrentIndex < orderGalleryImages.length - 1 ? 'flex' : 'none';

            updateThumbnailActiveState();
        }

        function renderOrderGalleryThumbnails() {
            const thumbsContainer = document.getElementById('orderGalleryThumbnails');
            const countBadge = document.getElementById('orderGalleryTotalThumbCount');
            if (!thumbsContainer) return;

            thumbsContainer.innerHTML = '';
            if (countBadge) countBadge.textContent = orderGalleryImages.length;

            orderGalleryImages.forEach((imgObj, idx) => {
                const thumbDiv = document.createElement('div');
                const isActive = idx === orderGalleryCurrentIndex;
                thumbDiv.className = `order-gallery-thumb relative shrink-0 w-14 h-14 rounded-xl overflow-hidden cursor-pointer transition-all border-2 ${isActive ? 'border-blue-500 ring-2 ring-blue-500/50 scale-105 opacity-100' : 'border-white/10 hover:border-white/40 opacity-70 hover:opacity-100'}`;
                thumbDiv.title = `${imgObj.productName} (#${idx + 1})`;
                thumbDiv.onclick = () => {
                    orderGalleryCurrentIndex = idx;
                    orderGalleryResetZoom();
                    renderOrderGalleryActiveImage();
                };

                const img = document.createElement('img');
                img.src = imgObj.url;
                img.className = 'w-full h-full object-cover bg-slate-800';
                img.onerror = function() { this.src = '/images/placeholder.png'; };
                thumbDiv.appendChild(img);

                if (orderGalleryData && orderGalleryData.products && orderGalleryData.products.length > 1) {
                    const badge = document.createElement('span');
                    badge.className = 'absolute bottom-0.5 right-0.5 bg-slate-900/90 text-blue-400 font-mono text-[9px] font-bold px-1 rounded';
                    badge.textContent = `P${imgObj.productIndex + 1}`;
                    thumbDiv.appendChild(badge);
                }

                thumbsContainer.appendChild(thumbDiv);
            });
        }

        function updateThumbnailActiveState() {
            const thumbs = document.querySelectorAll('.order-gallery-thumb');
            thumbs.forEach((th, idx) => {
                if (idx === orderGalleryCurrentIndex) {
                    th.className = 'order-gallery-thumb relative shrink-0 w-14 h-14 rounded-xl overflow-hidden cursor-pointer transition-all border-2 border-blue-500 ring-2 ring-blue-500/50 scale-105 opacity-100';
                    th.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                } else {
                    th.className = 'order-gallery-thumb relative shrink-0 w-14 h-14 rounded-xl overflow-hidden cursor-pointer transition-all border-2 border-white/10 hover:border-white/40 opacity-70 hover:opacity-100';
                }
            });
        }

        window.orderGalleryNext = function() {
            if (orderGalleryCurrentIndex < orderGalleryImages.length - 1) {
                orderGalleryCurrentIndex++;
                orderGalleryResetZoom();
                renderOrderGalleryActiveImage();
            }
        };

        window.orderGalleryPrev = function() {
            if (orderGalleryCurrentIndex > 0) {
                orderGalleryCurrentIndex--;
                orderGalleryResetZoom();
                renderOrderGalleryActiveImage();
            }
        };

        function applyOrderImgTransform() {
            const mainImg = document.getElementById("orderGalleryMainImg");
            const badge = document.getElementById("orderGalleryZoomBadge");
            if (!mainImg) return;

            mainImg.style.transform = `translate(${orderGalleryTranslateX}px, ${orderGalleryTranslateY}px) scale(${orderGalleryZoomScale}) rotate(${orderGalleryRotation}deg)`;
            mainImg.style.transition = orderGalleryIsPanning ? 'none' : 'transform 0.15s ease-out';

            if (badge) badge.textContent = Math.round(orderGalleryZoomScale * 100) + '%';
            mainImg.style.cursor = orderGalleryZoomScale > 1 ? (orderGalleryIsPanning ? 'grabbing' : 'grab') : 'default';
        }

        window.orderGalleryZoomIn = function() {
            orderGalleryZoomScale = Math.min(5.0, +(orderGalleryZoomScale + 0.25).toFixed(2));
            applyOrderImgTransform();
        };

        window.orderGalleryZoomOut = function() {
            orderGalleryZoomScale = Math.max(0.5, +(orderGalleryZoomScale - 0.25).toFixed(2));
            if (orderGalleryZoomScale <= 1) {
                orderGalleryTranslateX = 0;
                orderGalleryTranslateY = 0;
            }
            applyOrderImgTransform();
        };

        window.orderGalleryResetZoom = function() {
            orderGalleryZoomScale = 1;
            orderGalleryTranslateX = 0;
            orderGalleryTranslateY = 0;
            orderGalleryRotation = 0;
            applyOrderImgTransform();
        };

        window.orderGalleryRotate = function() {
            orderGalleryRotation = (orderGalleryRotation + 90) % 360;
            applyOrderImgTransform();
        };

        function setupOrderGalleryEvents() {
            const viewport = document.getElementById('orderGalleryViewport');
            if (!viewport) return;

            viewport.addEventListener('wheel', (e) => {
                const modal = document.getElementById('orderProductImageModal');
                if (!modal || modal.classList.contains('hidden')) return;

                e.preventDefault();
                if (e.deltaY < 0) {
                    window.orderGalleryZoomIn();
                } else {
                    window.orderGalleryZoomOut();
                }
            }, { passive: false });

            viewport.addEventListener('dblclick', () => {
                const modal = document.getElementById('orderProductImageModal');
                if (!modal || modal.classList.contains('hidden')) return;

                if (orderGalleryZoomScale > 1) {
                    window.orderGalleryResetZoom();
                } else {
                    orderGalleryZoomScale = 2.5;
                    applyOrderImgTransform();
                }
            });

            viewport.addEventListener('mousedown', (e) => {
                const modal = document.getElementById('orderProductImageModal');
                if (!modal || modal.classList.contains('hidden')) return;
                if (orderGalleryZoomScale <= 1) return;

                orderGalleryIsPanning = true;
                orderGalleryPanStartX = e.clientX - orderGalleryTranslateX;
                orderGalleryPanStartY = e.clientY - orderGalleryTranslateY;
                applyOrderImgTransform();
            });

            window.addEventListener('mousemove', (e) => {
                if (!orderGalleryIsPanning) return;
                orderGalleryTranslateX = e.clientX - orderGalleryPanStartX;
                orderGalleryTranslateY = e.clientY - orderGalleryPanStartY;
                applyOrderImgTransform();
            });

            window.addEventListener('mouseup', () => {
                if (orderGalleryIsPanning) {
                    orderGalleryIsPanning = false;
                    applyOrderImgTransform();
                }
            });
        }

        // Receipt Modal functions
        function openReceiptModal(previewUrl, pdfUrl) {
            const modal = document.getElementById('receiptModal');
            const iframe = document.getElementById('receiptIframe');
            const openTab = document.getElementById('receiptOpenInNewTab');
            const pdfLink = document.getElementById('receiptPdfLink');
            const loading = document.getElementById('receiptLoading');
            const printButton = document.getElementById('receiptPrintButton');

            modal.classList.remove('hidden');
            loading.classList.remove('hidden');
            printButton.disabled = true;
            openTab.href = previewUrl;
            pdfLink.href = pdfUrl || previewUrl;

            iframe.dataset.loadingReceipt = '0';
            iframe.src = 'about:blank';
            requestAnimationFrame(() => {
                iframe.dataset.loadingReceipt = '1';
                iframe.src = previewUrl;
            });

            document.body.style.overflow = 'hidden';
        }

        function closeReceiptModal() {
            const modal = document.getElementById('receiptModal');
            const iframe = document.getElementById('receiptIframe');
            const loading = document.getElementById('receiptLoading');
            const printButton = document.getElementById('receiptPrintButton');

            iframe.dataset.loadingReceipt = '0';
            iframe.src = 'about:blank';
            loading.classList.add('hidden');
            printButton.disabled = true;
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        function printReceiptPreview() {
            const iframe = document.getElementById('receiptIframe');

            if (iframe && iframe.contentWindow) {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const selectAllOrders = document.getElementById('selectAllOrders');
            const orderCheckboxes = Array.from(document.querySelectorAll('.order-checkbox'));
            const bulkStatusSelect = document.getElementById('bulkStatusSelect');
            const bulkUpdateStatusBtn = document.getElementById('bulkUpdateStatusBtn');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const selectedOrderCount = document.getElementById('selectedOrderCount');
            const bulkStatusForm = document.getElementById('bulkStatusForm');
            const bulkDeleteForm = document.getElementById('bulkDeleteForm');
            const bulkStatusValue = document.getElementById('bulkStatusValue');
            const receiptIframe = document.getElementById('receiptIframe');
            const receiptLoading = document.getElementById('receiptLoading');
            const receiptPrintButton = document.getElementById('receiptPrintButton');

            receiptIframe.addEventListener('load', function() {
                if (receiptIframe.dataset.loadingReceipt === '1') {
                    receiptLoading.classList.add('hidden');
                    receiptPrintButton.disabled = false;
                    receiptIframe.dataset.loadingReceipt = '0';
                }
            });

            document.addEventListener('keydown', function(e) {
                const orderImgModal = document.getElementById('orderProductImageModal');
                if (orderImgModal && !orderImgModal.classList.contains('hidden')) {
                    if (e.key === 'Escape') {
                        closeOrderProductGallery();
                    } else if (e.key === 'ArrowLeft') {
                        orderGalleryPrev();
                    } else if (e.key === 'ArrowRight') {
                        orderGalleryNext();
                    } else if (e.key === '+' || e.key === '=') {
                        orderGalleryZoomIn();
                    } else if (e.key === '-' || e.key === '_') {
                        orderGalleryZoomOut();
                    } else if (e.key === '0' || e.key === 'r' || e.key === 'R') {
                        orderGalleryResetZoom();
                    }
                    return;
                }

                if (e.key === 'Escape') {
                    closeReceiptModal();
                }
            });

            setupOrderGalleryEvents();

            function applyFilters() {
                const search = searchInput.value;
                const status = statusFilter.value;
                let url = new URL(window.location.href);

                if (search) url.searchParams.set('search', search);
                else url.searchParams.delete('search');

                if (status) url.searchParams.set('status', status);
                else url.searchParams.delete('status');

                url.searchParams.delete('page');
                window.location.href = url.href;
            }

            // Enter key press to search
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') applyFilters();
            });

            // Dropdown change to filter
            statusFilter.addEventListener('change', applyFilters);

            function getSelectedOrderIds() {
                return orderCheckboxes
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value);
            }

            function updateBulkActionState() {
                const selectedCount = getSelectedOrderIds().length;
                const allSelected = orderCheckboxes.length > 0 && selectedCount === orderCheckboxes.length;

                selectedOrderCount.textContent = selectedCount;
                bulkDeleteBtn.disabled = selectedCount === 0;
                bulkUpdateStatusBtn.disabled = selectedCount === 0 || !bulkStatusSelect.value;

                selectAllOrders.checked = allSelected;
                selectAllOrders.indeterminate = selectedCount > 0 && !allSelected;
            }

            function appendSelectedOrderIds(form, ids) {
                form.querySelectorAll('[data-bulk-order-id]').forEach(input => input.remove());

                ids.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    input.dataset.bulkOrderId = 'true';
                    form.appendChild(input);
                });
            }

            selectAllOrders.addEventListener('change', function() {
                orderCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllOrders.checked;
                });
                updateBulkActionState();
            });

            orderCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkActionState);
            });

            bulkStatusSelect.addEventListener('change', updateBulkActionState);

            bulkUpdateStatusBtn.addEventListener('click', function() {
                const selectedIds = getSelectedOrderIds();
                const selectedStatusLabel = bulkStatusSelect.options[bulkStatusSelect.selectedIndex]?.text;

                if (selectedIds.length === 0 || !bulkStatusSelect.value) return;
                if (!confirm(`Update ${selectedIds.length} selected order(s) to "${selectedStatusLabel}"?`)) return;

                appendSelectedOrderIds(bulkStatusForm, selectedIds);
                bulkStatusValue.value = bulkStatusSelect.value;
                bulkUpdateStatusBtn.disabled = true;
                bulkStatusForm.submit();
            });

            bulkDeleteBtn.addEventListener('click', function() {
                const selectedIds = getSelectedOrderIds();

                if (selectedIds.length === 0) return;
                if (!confirm(`Delete ${selectedIds.length} selected order(s)? This action cannot be undone.`)) return;

                appendSelectedOrderIds(bulkDeleteForm, selectedIds);
                bulkDeleteBtn.disabled = true;
                bulkDeleteForm.submit();
            });

            updateBulkActionState();
        });
    </script>
@endsection
