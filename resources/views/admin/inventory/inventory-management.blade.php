@extends('layouts.admin')

@section('content')
    <style>
        #table-scroll-container.cursor-grab * {
            cursor: grab;
        }
        #table-scroll-container.active-grabbing * {
            cursor: grabbing !important;
        }
        #table-scroll-container.cursor-grab input,
        #table-scroll-container.cursor-grab select,
        #table-scroll-container.cursor-grab button,
        #table-scroll-container.cursor-grab a,
        #table-scroll-container.cursor-grab label,
        #table-scroll-container.cursor-grab .cursor-pointer,
        #table-scroll-container.active-grabbing input,
        #table-scroll-container.active-grabbing select,
        #table-scroll-container.active-grabbing button,
        #table-scroll-container.active-grabbing a,
        #table-scroll-container.active-grabbing label,
        #table-scroll-container.active-grabbing .cursor-pointer {
            cursor: auto;
        }
        #table-scroll-container.cursor-grab [data-action],
        #table-scroll-container.cursor-grab .inventory-item-checkbox {
            cursor: pointer;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        // Helper function to construct image URLs based on storage location
        function getImageUrl(photoPath) {
            if (!photoPath) return '/images/placeholder.png';
            const strPath = String(photoPath).trim();
            if (strPath.startsWith('http://') || strPath.startsWith('https://')) {
                return strPath;
            }
            const cleanPath = strPath.replace(/^\/+/, '');
            const rootServedPrefixes = ['pallets/', 'refurbishing/', 'repair/', 'images/'];

            // These folders are served directly from /public
            if (rootServedPrefixes.some(prefix => cleanPath.startsWith(prefix))) {
                return `/${cleanPath}`;
            }

            // Already points to storage path
            if (cleanPath.startsWith('storage/')) {
                return `/${cleanPath}`;
            }

            // Fallback: serve from /storage
            return `/storage/${cleanPath}`;
        }
    </script>
    <div class="w-full animate-in fade-in duration-500 overflow-hidden relative p-4 md:p-6 bg-slate-50 min-h-screen">
        {{-- Page Heading --}}
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-slate-900 flex items-center gap-3">
                <i class="fas fa-boxes text-blue-600"></i> Inventory Management
            </h1>
            <p class="text-slate-500 mt-2 text-sm">Track and manage your complete inventory across all locations</p>
        </div>

        {{-- 1. Stats Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 md:gap-4 mb-8" id="stats-container"></div>

        {{-- 2. Action Toolbar --}}
        <div class="flex flex-wrap items-center gap-2 mb-6">
            <a href="{{ route('admin.items-lot.intake') }}"
                class="bg-[#2563eb] hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg font-bold text-[12px] flex items-center shadow-md transition-all active:scale-95">
                + Add Items/Lot Entries
            </a>
            <button
                class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 rounded-lg font-bold text-[11px] flex items-center border border-slate-200 transition-all">
                <i class="fas fa-copy mr-2 opacity-50"></i> Duplicate (0)
            </button>
            <button
                class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 rounded-lg font-bold text-[11px] flex items-center border border-slate-200 transition-all">
                <i class="fas fa-print mr-2 opacity-50"></i> Print Labels (0)
            </button>
            <button id="bulk-delete-btn" type="button" disabled
                class="bg-red-50 hover:bg-red-600 text-red-600 hover:text-white px-4 py-2.5 rounded-lg font-bold text-[11px] flex items-center border border-red-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-red-50 disabled:hover:text-red-600">
                <i class="fas fa-trash-alt mr-2"></i>
                Delete Selected (<span id="bulk-delete-count">0</span>)
            </button>
            <button id="delete-zero-qty-btn" type="button" onclick="deleteZeroQuantityItems()"
                class="bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white px-4 py-2.5 rounded-lg font-bold text-[11px] flex items-center border border-rose-200 transition-all active:scale-95"
                title="Delete all inventory items with quantity 0">
                <i class="fas fa-trash-alt mr-2"></i>
                Delete 0 Qty (<span id="zero-qty-count">{{ \App\Models\Inventory::where('quantity', '<=', 0)->orWhereNull('quantity')->count() }}</span>)
            </button>
            <button id="export-csv-btn"
                class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2.5 rounded-lg font-bold text-[11px] flex items-center border border-slate-200 transition-all">
                <i class="fas fa-download mr-2 opacity-50"></i> Export CSV
            </button>
            <div class="ml-auto flex items-center gap-2">
                <button id="fetch-ebay-btn" type="button"
                    class="bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white px-5 py-2.5 rounded-lg font-bold text-[12px] flex items-center gap-2 shadow-md transition-all active:scale-95">
                    <i class="fas fa-store"></i> Fetch from eBay
                </button>
                <button id="match-ebay-qty-btn" type="button" onclick="matchAllEbayQuantities()"
                    class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-5 py-2.5 rounded-lg font-bold text-[12px] flex items-center gap-2 shadow-md transition-all active:scale-95"
                    title="Match and update all synced products quantity with current eBay quantity">
                    <i class="fas fa-sync-alt"></i> Sync Latest eBay Quantities
                </button>
            </div>
        </div>

        {{-- 3. Filters Section (Professionally Aligned) --}}
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-sm mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-12 gap-3 items-end">
                {{-- 1. Search field --}}
                <div class="xl:col-span-2 sm:col-span-2">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5 flex items-center gap-1.5">
                        <i class="fas fa-search text-blue-500 text-[10px]"></i> Search Item
                    </label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input id="search-input" type="text" placeholder="Name, barcode, serial, link..."
                            class="w-full h-[40px] pl-8 pr-3 bg-slate-50/70 hover:bg-white focus:bg-white border border-slate-200 rounded-lg text-xs font-semibold outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all shadow-xs text-slate-700">
                    </div>
                </div>

                {{-- 2. Category --}}
                <div class="xl:col-span-2">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-layer-group text-blue-500 text-[10px]"></i> Category
                    </label>
                    <select id="category-filter"
                        class="w-full h-[40px] bg-slate-50/70 hover:bg-white focus:bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 rounded-lg px-3 text-xs font-semibold text-slate-700 outline-none transition-all shadow-xs truncate">
                        <option value="">All Categories</option>
                    </select>
                </div>

                {{-- 3. Sub Category --}}
                <div class="xl:col-span-2">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-sitemap text-blue-500 text-[10px]"></i> Sub Category
                    </label>
                    <select id="sub-category-filter" disabled
                        class="w-full h-[40px] bg-slate-50/70 hover:bg-white focus:bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 rounded-lg px-3 text-xs font-semibold text-slate-700 outline-none transition-all shadow-xs disabled:bg-slate-100/70 disabled:text-slate-400 disabled:cursor-not-allowed truncate">
                        <option value="">All Sub Categories</option>
                    </select>
                </div>

                {{-- 4. Sub Sub Category --}}
                <div class="xl:col-span-2">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-folder-tree text-blue-500 text-[10px]"></i> Sub Sub Category
                    </label>
                    <select id="sub-sub-category-filter" disabled
                        class="w-full h-[40px] bg-slate-50/70 hover:bg-white focus:bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 rounded-lg px-3 text-xs font-semibold text-slate-700 outline-none transition-all shadow-xs disabled:bg-slate-100/70 disabled:text-slate-400 disabled:cursor-not-allowed truncate">
                        <option value="">All Sub Sub Categories</option>
                    </select>
                </div>

                {{-- 5. Intake Type (Both, eBay, Denver only) --}}
                <div class="xl:col-span-1">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-box-open text-blue-500 text-[10px]"></i> Intake
                    </label>
                    <select id="intake-filter"
                        class="w-full h-[40px] bg-slate-50/70 hover:bg-white focus:bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 rounded-lg px-2 text-xs font-semibold text-slate-700 outline-none transition-all shadow-xs">
                        <option value="">Both</option>
                        <option value="eBay">eBay</option>
                        <option value="Denver">Denver</option>
                    </select>
                </div>

                {{-- 6. Statuses --}}
                <div class="xl:col-span-1">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-tag text-blue-500 text-[10px]"></i> Status
                    </label>
                    <select id="status-filter"
                        class="w-full h-[40px] bg-slate-50/70 hover:bg-white focus:bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 rounded-lg px-2 text-xs font-semibold text-slate-700 outline-none transition-all shadow-xs">
                        <option value="">All Statuses</option>
                        <option value="In Stock">IN STOCK</option>
                        <option value="LIVE ONLINE">LIVE ONLINE</option>
                        <option value="Recieved">RECEIVED</option>
                        <option value="Sold">SOLD</option>
                        <option value="COMPLETED">COMPLETED</option>
                    </select>
                </div>

                {{-- 7. Search by Date + Reset Button --}}
                <div class="xl:col-span-2 flex items-end gap-2">
                    <div class="flex-1">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5 flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar text-blue-500 text-[10px]"></i> Date
                        </label>
                        <input id="date-filter" type="date"
                            class="w-full h-[40px] bg-slate-50/70 hover:bg-white focus:bg-white border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 rounded-lg px-2.5 text-xs font-semibold text-slate-700 outline-none transition-all shadow-xs">
                    </div>
                    <button id="refresh-btn" type="button" title="Reset all filters"
                        class="h-[40px] w-[40px] shrink-0 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-600 rounded-lg flex items-center justify-center transition-all border border-slate-200">
                        <i class="fas fa-redo-alt text-xs"></i>
                    </button>
                </div>

                {{-- Hidden Pallet Filter kept for DOM compatibility --}}
                <select id="pallet-filter" class="hidden">
                    <option value="">All Pallets</option>
                    @foreach ($pallets_list as $pallet)
                        <option value="{{ $pallet->barcode_number }}">{{ $pallet->barcode_number }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- 4. Main Table --}}
        <div class="bg-white rounded-[1.5rem] border border-slate-100 shadow-sm">
            <div class="overflow-x-auto cursor-grab select-none active:cursor-grabbing" id="table-scroll-container">
                <table class="w-full text-left border-collapse min-w-[1360px] table-fixed">
                    <thead class="bg-slate-50/50 border-b border-slate-100">
                        <tr class="text-[10px] uppercase font-black text-slate-400 tracking-[0.1em]">
                            <th class="px-3 py-5 w-14 text-center">
                                <input id="select-all-items" type="checkbox"
                                    class="h-4 w-4 cursor-pointer rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                    aria-label="Select all inventory items on this page"
                                    title="Select all items on this page">
                            </th>
                            <th class="px-4 py-5 w-40">Barcode</th>
                            <th class="px-4 py-5 w-40">Name/Model</th>
                            <th class="px-3 py-5 w-64 text-center">Category</th>
                            <th class="px-4 py-5 w-32 text-center">Intake</th>
                            <th class="px-4 py-5 w-24 text-center">Photos</th>
                            <th class="px-4 py-5 w-24 text-center">Qty</th>
                            <th class="px-4 py-5 w-32 text-center">Status</th>
                            <th class="px-4 py-5 w-28">Price</th>
                            <th class="px-4 py-5 w-28 text-center">Date</th>
                            <th class="px-4 py-5 w-32 text-center">Sell</th>
                            <th class="px-3 py-5 w-[215px] text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50" id="items-table-body">
                        <tr>
                            <td colspan="12" class="p-16 text-center text-slate-300 font-bold uppercase text-xs">
                                Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 5. Pagination --}}
        <div class="mt-6" id="pagination-container"></div>
    </div>

    {{-- MODALS --}}
    @include('admin.intake.modals.partials.gallery-modal')
    @include('admin.inventory.modals.item-details-modal')
    @include('admin.inventory.modals.edit-item-modal')

    {{-- Sync Success Modal --}}
    <div id="syncSuccessModal" class="hidden fixed inset-0 z-[150] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
            onclick="closeSyncSuccessModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 text-center animate-in zoom-in duration-300">
            {{-- Close (Cross) button --}}
            <button type="button" class="absolute top-4 right-4 h-8 w-8 rounded-full hover:bg-slate-105 text-slate-500 flex items-center justify-center transition-colors"
                onclick="closeSyncSuccessModal()">
                <i class="fas fa-times"></i>
            </button>

            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100 text-green-600 mb-4">
                <i class="fas fa-check text-xl"></i>
            </div>

            <h3 class="text-lg font-black text-slate-800 mb-2">Product Synced Successfully!</h3>
            <p class="text-slate-500 text-sm mb-4">The item is now live on the store front.</p>

            <div class="bg-slate-50 rounded-xl border border-slate-100 p-3 mb-6 flex items-center justify-between gap-2 text-left">
                <a id="sync-store-link" href="#" target="_blank" class="text-xs font-bold text-blue-600 break-all hover:text-blue-800 hover:underline">http://127.0.0.1:8000/product/128</a>
                <button type="button" id="copy-sync-link-btn" onclick="copySyncLink()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors flex items-center gap-1 shrink-0">
                    <i class="fas fa-copy"></i> Copy
                </button>
            </div>

            <div class="text-[10px] text-slate-400 font-semibold flex items-center justify-center gap-1.5">
                <i class="fas fa-clock"></i> Disappearing in <span id="sync-timer-counter">10</span>s...
            </div>
        </div>
    </div>

    {{-- Direct Sell Modal --}}
    <div id="directSellModal" class="hidden  fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm"
            onclick="document.getElementById('directSellModal').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden">
            <div
                class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-gradient-to-r from-green-50 to-emerald-50">
                <h3 class="text-lg font-black text-slate-800">
                    <i class="fas fa-shopping-cart text-green-600 mr-2"></i> Direct Store Pickup Sale
                </h3>
                <button type="button" class="h-8 w-8 rounded-full hover:bg-white text-slate-500"
                    onclick="document.getElementById('directSellModal').classList.add('hidden')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                <input type="hidden" id="sell-item-id">
                <input type="hidden" id="sell-quantity-max">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                    <div class="space-y-3 bg-slate-50 rounded-2xl border border-slate-200 p-4">
                        <div>
                            <p class="text-[10px] uppercase tracking-[0.18em] text-slate-400 mb-2">Barcode</p>
                            <p id="sell-barcode" class="text-sm font-semibold text-slate-800">N/A</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-[0.18em] text-slate-400 mb-2">Status</p>
                            <p id="sell-status" class="text-sm font-semibold text-slate-800">N/A</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-[0.18em] text-slate-400 mb-2">Category</p>
                            <p id="sell-category" class="text-sm font-semibold text-slate-800">N/A</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-[0.18em] text-slate-400 mb-2">Available Quantity</p>
                            <p id="sell-available-quantity" class="text-sm font-semibold text-slate-800">0</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-[0.18em] text-slate-400 mb-2">Location</p>
                            <p id="sell-location" class="text-sm font-semibold text-slate-800">N/A</p>
                        </div>
                    </div>
                    <div class="space-y-3 bg-slate-50 rounded-2xl border border-slate-200 p-4">
                        <div>
                            <p class="text-[10px] uppercase tracking-[0.18em] text-slate-400 mb-2">Brand & Model</p>
                            <p id="sell-brand-model" class="text-sm font-semibold text-slate-800">N/A</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-[0.18em] text-slate-400 mb-2">Device Type</p>
                            <p id="sell-device-type" class="text-sm font-semibold text-slate-800">N/A</p>
                        </div>
                        <div class="space-y-3">
                            <p class="text-[10px] uppercase tracking-[0.18em] text-slate-400 mb-2">Device Specs</p>
                            <p id="sell-device-specs" class="text-sm font-semibold text-slate-800">N/A</p>
                        </div>
                        <div class="space-y-3">
                            <p class="text-[10px] uppercase tracking-[0.18em] text-slate-400 mb-2">Operating System</p>
                            <p id="sell-operating-system" class="text-sm font-semibold text-slate-800">N/A</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-[0.18em] text-slate-400 mb-2">Technician</p>
                            <p id="sell-technician" class="text-sm font-semibold text-slate-800">N/A</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                    {{-- Quantity --}}
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Quantity</label>
                        <input type="number" id="sell-quantity" min="1" value="1"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg font-bold text-slate-800 focus:border-green-400 focus:outline-none">
                        <p id="sell-quantity-remaining" class="mt-2 text-xs font-semibold text-emerald-600"></p>
                        <p id="sell-stock-warning" class="mt-1 text-xs font-semibold text-red-600 hidden"></p>
                        <div class="mt-3 rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm">
                            <div class="flex items-center justify-between text-slate-600">
                                <span class="font-semibold">Tax (8%)</span>
                                <span id="sell-tax-amount" class="font-bold">$0.00</span>
                            </div>
                        </div>
                    </div>
                    {{-- Price --}}
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Subtotal</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 font-bold">$</span>
                            <input id="sell-item-price" readonly
                                class="w-full pl-7 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg font-bold text-slate-800"
                                step="0.01">
                        </div>
                        <p id="sell-unit-price" class="mt-2 text-xs font-semibold text-slate-500"></p>
                        <div class="mt-3 rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="font-black text-slate-800">Total Price</span>
                                <span id="sell-total-charge" class="font-black text-emerald-700">$0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Notes</label>
                    <p id="sell-notes"
                        class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-sm leading-6 text-slate-700">
                        No notes provided.</p>
                </div>

                {{-- Customer Name --}}
                <div class="mb-4">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Customer Name *</label>
                    <input type="text" id="sell-customer-name" placeholder="Enter customer name"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-slate-700 focus:border-green-400 focus:outline-none">
                </div>

                {{-- Customer Email --}}
                <div class="mb-4">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                    <input type="email" id="sell-customer-email" placeholder="customer@example.com"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-slate-700 focus:border-green-400 focus:outline-none">
                </div>

                {{-- Customer Phone --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Phone</label>
                    <input type="tel" id="sell-customer-phone" placeholder="(123) 456-7890"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-slate-700 focus:border-green-400 focus:outline-none">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Company (Optional)</label>
                    <input type="text" id="sell-customer-company" placeholder="Enter company name"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-slate-700 focus:border-green-400 focus:outline-none">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Order Status</label>
                    <select id="sell-order-status"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-slate-700 focus:border-green-400 focus:outline-none bg-white">
                        @foreach (\App\Models\Order::statusChoices() as $status => $label)
                            <option value="{{ $status }}" {{ $status === \App\Models\Order::STATUS_IN_STORE_PURCHASED ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Purchase Type</label>
                    <select id="sell-payment-method"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-slate-700 focus:border-green-400 focus:outline-none bg-white">
                        <option value="cash">Cash</option>
                        <option value="cheque">Cheque</option>
                        <option value="square_card">Square Card</option>
                    </select>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-6 text-[12px] text-blue-700 font-semibold">
                    <i class="fas fa-info-circle mr-2"></i> The selected payment type will be saved with this sale.
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-slate-200 flex gap-3 bg-slate-50">
                <button type="button" onclick="document.getElementById('directSellModal').classList.add('hidden')"
                    class="flex-1 px-4 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold rounded-lg transition-all">
                    Cancel
                </button>
                <button type="button" id="confirm-sell-btn" onclick="inventoryManager.processSell()"
                    class="flex-1 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition-all">
                    <i class="fas fa-check mr-2"></i> Confirm Sale
                </button>
            </div>
        </div>
    </div>

    <div id="inventoryShippingRatesModal" class="hidden fixed inset-0 z-[120] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm"
            onclick="document.getElementById('inventoryShippingRatesModal').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] overflow-hidden">
            <div class="px-5 py-4 border-b flex items-center justify-between">
                <h3 class="text-base font-black text-slate-800">Inventory Shipping Rates</h3>
                <button type="button" class="h-8 w-8 rounded-full hover:bg-slate-100 text-slate-500"
                    onclick="document.getElementById('inventoryShippingRatesModal').classList.add('hidden')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="inventoryShippingRatesList" class="p-5 space-y-3 overflow-y-auto max-h-[70vh]"></div>
        </div>
    </div>

    {{-- eBay Listings Popup Modal --}}
    <div id="ebayListingsModal" class="hidden fixed inset-0 z-[150] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity" onclick="closeEbayListingsModal()"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl max-w-5xl w-full max-h-[92vh] flex flex-col overflow-hidden animate-in zoom-in duration-200">
            
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-orange-50/80 via-white to-amber-50/80 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-orange-500/10 border border-orange-200 flex items-center justify-center text-orange-600">
                        <i class="fas fa-store text-lg"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-black text-slate-800 tracking-tight">eBay Listings</h3>
                            <span id="ebay-total-badge" class="bg-orange-100 text-orange-800 text-[11px] font-extrabold px-2.5 py-0.5 rounded-full border border-orange-200">0 Listings</span>
                            <span id="ebay-seller-badge" class="bg-slate-100 text-slate-700 text-[11px] font-extrabold px-2.5 py-0.5 rounded-full border border-slate-200 inline-flex items-center gap-1.5">
                                <i class="fa-brands fa-ebay text-orange-500 text-xs"></i> Seller: <span id="ebay-seller-name" class="text-slate-900 font-black">{{ env('EBAY_SELLER_USERNAME', 'systematictrade') }}</span>
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium">Browse active eBay seller listings, search by title, and sync items into inventory</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="modal-match-ebay-qty-btn" onclick="matchAllEbayQuantities()" title="Match and update all synced inventory quantities with current eBay quantities"
                        class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold px-3.5 py-2 rounded-xl text-xs flex items-center gap-1.5 shadow-md shadow-emerald-200 transition-all active:scale-95">
                        <i class="fas fa-sync-alt"></i> Match Quantities
                    </button>
                    <button type="button" id="sync-all-ebay-btn" onclick="syncAllEbayItems()"
                        class="bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-extrabold px-4 py-2 rounded-xl text-xs flex items-center gap-2 shadow-md shadow-orange-200 transition-all active:scale-95">
                        <i class="fas fa-cloud-download-alt"></i> Sync All Unsynced
                    </button>
                    <button type="button" onclick="loadEbayListingsModal(true)" title="Reload listings from eBay API"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold p-2 rounded-xl text-xs transition-colors">
                        <i class="fas fa-redo-alt"></i>
                    </button>
                    <button type="button" class="h-9 w-9 rounded-xl hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors ml-1"
                        onclick="closeEbayListingsModal()">
                        <i class="fas fa-times text-base"></i>
                    </button>
                </div>
            </div>

            {{-- Filter & Stats Toolbar --}}
            <div class="px-6 py-3.5 bg-slate-50/80 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 shrink-0">
                {{-- Live Search Input --}}
                <div class="relative flex-grow max-w-md">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input id="ebay-popup-search" type="text" placeholder="Search eBay listings by title or Item ID..."
                        oninput="filterEbayPopupListings()"
                        class="w-full pl-9 pr-8 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100 shadow-sm transition-all">
                    <button id="clear-popup-search" type="button" onclick="clearPopupSearch()" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>

                {{-- Stats Badges & Sorting Indicator --}}
                <div class="flex items-center gap-3 text-xs font-bold text-slate-600">
                    <div class="flex items-center gap-1.5 bg-white border border-slate-200 rounded-xl px-3 py-1.5 shadow-sm">
                        <span class="text-slate-400 uppercase text-[10px] tracking-wider font-extrabold">Total:</span>
                        <span id="ebay-stat-total" class="text-slate-800">0</span>
                    </div>
                    <div class="flex items-center gap-1.5 bg-emerald-50 border border-emerald-200 rounded-xl px-3 py-1.5 text-emerald-700 shadow-sm">
                        <span class="text-emerald-600/70 uppercase text-[10px] tracking-wider font-extrabold">Synced:</span>
                        <span id="ebay-stat-synced" class="font-extrabold">0</span>
                    </div>
                    <div class="flex items-center gap-1.5 bg-amber-50 border border-amber-200 rounded-xl px-3 py-1.5 text-amber-700 shadow-sm">
                        <span class="text-amber-600/70 uppercase text-[10px] tracking-wider font-extrabold">Unsynced:</span>
                        <span id="ebay-stat-unsynced" class="font-extrabold">0</span>
                    </div>
                    <div class="flex items-center gap-1.5 bg-orange-50 border border-orange-200 text-orange-700 rounded-xl px-3 py-1.5">
                        <i class="fas fa-sort-amount-down text-orange-500"></i>
                        <span>Newest First</span>
                    </div>
                </div>
            </div>

            {{-- Modal Body / Content Area --}}
            <div class="p-6 overflow-y-auto flex-grow max-h-[62vh] min-h-[350px]" id="ebay-modal-scroll-area">
                
                {{-- Loading State --}}
                <div id="ebay-popup-loading" class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-orange-100 mb-4 animate-bounce">
                        <i class="fas fa-spinner fa-spin text-2xl text-orange-600"></i>
                    </div>
                    <h4 class="text-base font-bold text-slate-800 mb-1">Fetching eBay Listings...</h4>
                    <p class="text-xs text-slate-500">Connecting to eBay API and retrieving your active listings sorted newest first.</p>
                </div>

                {{-- Error State --}}
                <div id="ebay-popup-error" class="hidden text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-exclamation-circle text-2xl text-red-600"></i>
                    </div>
                    <h4 class="text-base font-bold text-slate-800 mb-1">Failed to Load eBay Listings</h4>
                    <p class="text-sm text-red-600 font-semibold mb-4" id="ebay-popup-error-message">An error occurred while fetching eBay listings.</p>
                    <button type="button" onclick="loadEbayListingsModal(true)" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl text-xs transition-all">
                        <i class="fas fa-redo mr-1"></i> Try Again
                    </button>
                </div>

                {{-- Empty Search Results / Notice State --}}
                <div id="ebay-popup-empty" class="hidden text-center py-12 px-4 max-w-lg mx-auto">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-slate-100 text-slate-400 mb-3">
                        <i class="fas fa-search text-xl"></i>
                    </div>
                    <h4 class="text-sm font-bold text-slate-700 mb-1">No Active Listings Found</h4>
                    <p id="ebay-popup-empty-desc" class="text-xs text-slate-500 mb-4 leading-relaxed">No active eBay listings found for this seller account.</p>
                    <div id="ebay-popup-connect-box" class="hidden mt-3 p-4 bg-orange-50 border border-orange-200 rounded-2xl text-center">
                        <p class="text-xs text-orange-800 font-semibold mb-3">Your eBay seller session may have expired or is not connected.</p>
                        <a href="{{ url('/ebay/login') }}" target="_blank"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-extrabold px-5 py-2.5 rounded-xl text-xs shadow-md transition-all">
                            <i class="fa-brands fa-ebay text-base"></i> Connect / Re-authorize eBay Account
                        </a>
                    </div>
                </div>

                {{-- Listings List Grid --}}
                <div id="ebay-popup-listings-list" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Rendered dynamically via JavaScript --}}
                </div>
            </div>

            {{-- Modal Pagination Bar --}}
            <div id="ebay-popup-pagination" class="hidden px-6 py-3 bg-white border-t border-slate-200/80 flex flex-wrap items-center justify-between gap-3 shrink-0 text-xs">
                <div class="flex items-center gap-3">
                    <span id="ebay-pagination-info" class="text-slate-600 font-bold text-xs">
                        Showing <span class="text-slate-900 font-black">1–20</span> of <span class="text-slate-900 font-black">0</span> listings
                    </span>
                    <div class="flex items-center gap-1.5 text-slate-500 font-semibold text-xs border-l border-slate-200 pl-3">
                        <label for="ebay-per-page-select">Per page:</label>
                        <select id="ebay-per-page-select" onchange="changeEbayPageSize(this.value)"
                            class="bg-slate-50 hover:bg-white border border-slate-200 rounded-lg px-2.5 py-1 text-xs font-bold text-slate-700 outline-none focus:border-orange-400 focus:ring-1 focus:ring-orange-200 transition-all cursor-pointer shadow-xs">
                            <option value="10">10</option>
                            <option value="20" selected>20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-1" id="ebay-pagination-controls">
                    {{-- Populated dynamically via renderEbayCurrentPage --}}
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-3.5 border-t border-slate-100 flex items-center justify-between bg-slate-50 shrink-0 text-xs">
                <div class="text-slate-500 font-semibold flex items-center gap-2">
                    <i class="fas fa-info-circle text-slate-400"></i>
                    <span>Listings auto-sorted by newest start date. Click <strong>Sync to Inventory</strong> to add any item.</span>
                </div>
                <button type="button" onclick="closeEbayListingsModal()"
                    class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-xl transition-all">
                    Close
                </button>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            const baseUrl = "{{ route('admin.inventory.index') }}";
            const csrfToken = "{{ csrf_token() }}";
            const storageUrl = "{{ asset('storage/') }}";
            const palletsList = [];
            const ebayFetchUrl = "{{ route('admin.inventory.fetch-ebay') }}";
            const ebayListingsUrl = "{{ route('admin.inventory.ebay-listings') }}";
            const syncSingleEbayUrl = "{{ route('admin.inventory.sync-single-ebay') }}";
            const matchEbayQuantitiesUrl = "{{ route('admin.inventory.match-ebay-quantities') }}";

            // eBay Modal State & Pagination
            window.ebayModalItems = [];
            window.ebayPagination = {
                currentPage: 1,
                pageSize: 20,
                filteredItems: [],
            };

            // Bind top action buttons
            document.getElementById('fetch-ebay-btn').addEventListener('click', openEbayListingsModal);

            function openEbayListingsModal() {
                const modal = document.getElementById('ebayListingsModal');
                modal.classList.remove('hidden');
                if (window.ebayModalItems && window.ebayModalItems.length > 0) {
                    renderEbayCurrentPage();
                } else {
                    loadEbayListingsModal();
                }
            }

            function closeEbayListingsModal() {
                document.getElementById('ebayListingsModal').classList.add('hidden');
            }

            function loadEbayListingsModal(forceReload = false) {
                const loading = document.getElementById('ebay-popup-loading');
                const error = document.getElementById('ebay-popup-error');
                const empty = document.getElementById('ebay-popup-empty');
                const list = document.getElementById('ebay-popup-listings-list');
                const searchInput = document.getElementById('ebay-popup-search');
                const paginationBar = document.getElementById('ebay-popup-pagination');

                loading.classList.remove('hidden');
                error.classList.add('hidden');
                empty.classList.add('hidden');
                if (paginationBar) paginationBar.classList.add('hidden');
                list.innerHTML = '';
                if (searchInput) searchInput.value = '';
                document.getElementById('clear-popup-search').classList.add('hidden');

                const urlParams = new URLSearchParams(window.location.search);
                const sellerParam = urlParams.get('seller');
                let fetchUrl = sellerParam ? `${ebayListingsUrl}?seller=${encodeURIComponent(sellerParam)}` : ebayListingsUrl;
                if (forceReload) {
                    fetchUrl += (fetchUrl.includes('?') ? '&' : '?') + 'refresh=1';
                }

                fetch(fetchUrl, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                })
                .then(async res => {
                    const text = await res.text();
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error(res.ok ? 'Invalid response format received from server.' : `Server error (${res.status}). Please try again in a moment.`);
                    }
                })
                .then(data => {
                    loading.classList.add('hidden');

                    if (data.success && Array.isArray(data.items)) {
                        window.ebayModalItems = data.items;
                        window.ebayPagination.filteredItems = data.items;
                        window.ebayPagination.currentPage = 1;
                        window.ebayModalNotice = data.notice || '';
                        window.ebayModalReasons = data.reasons || [];
                        if (data.seller && document.getElementById('ebay-seller-name')) {
                            document.getElementById('ebay-seller-name').textContent = data.seller;
                        }
                        updateEbayPopupStats(data.total_count, data.synced_count, data.unsynced_count);
                        renderEbayCurrentPage();
                    } else {
                        error.classList.remove('hidden');
                        const reasonList = (data.reasons && data.reasons.length > 0)
                            ? '<ul class="mt-2 text-left list-disc list-inside text-xs font-normal space-y-1">' + data.reasons.map(r => `<li>${r}</li>`).join('') + '</ul>'
                            : '';
                        document.getElementById('ebay-popup-error-message').innerHTML = (data.message || 'Failed to retrieve listings.') + reasonList;
                    }
                })
                .catch(err => {
                    loading.classList.add('hidden');
                    error.classList.remove('hidden');
                    document.getElementById('ebay-popup-error-message').textContent = err.message || 'Network error fetching eBay listings.';
                });
            }

            function updateEbayPopupStats(total, synced, unsynced) {
                if (total === undefined) {
                    total = window.ebayModalItems.length;
                    synced = window.ebayModalItems.filter(i => i.is_synced).length;
                    unsynced = total - synced;
                }
                document.getElementById('ebay-total-badge').textContent = `${total} Listings`;
                document.getElementById('ebay-stat-total').textContent = total;
                document.getElementById('ebay-stat-synced').textContent = synced;
                document.getElementById('ebay-stat-unsynced').textContent = unsynced;

                const syncAllBtn = document.getElementById('sync-all-ebay-btn');
                if (syncAllBtn) {
                    syncAllBtn.disabled = false;
                    syncAllBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    if (unsynced === 0) {
                        syncAllBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Re-sync All Quantities';
                        syncAllBtn.title = 'All listings synced. Click to update quantities for all items in inventory.';
                    } else {
                        syncAllBtn.innerHTML = `<i class="fas fa-cloud-download-alt"></i> Sync All (${unsynced} Unsynced)`;
                        syncAllBtn.title = 'Sync new items and update existing quantities in inventory.';
                    }
                }
            }

            function filterEbayPopupListings() {
                const query = (document.getElementById('ebay-popup-search').value || '').trim().toLowerCase();
                const clearBtn = document.getElementById('clear-popup-search');

                if (query.length > 0) {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                }

                if (!window.ebayModalItems) return;

                if (!query) {
                    window.ebayPagination.filteredItems = window.ebayModalItems;
                } else {
                    window.ebayPagination.filteredItems = window.ebayModalItems.filter(item => {
                        const title = (item.title || '').toLowerCase();
                        const itemId = (item.itemId || '').toLowerCase();
                        const category = (item.category || '').toLowerCase();
                        const loc = (item.put_away_location || '').toLowerCase();
                        return title.includes(query) || itemId.includes(query) || category.includes(query) || loc.includes(query);
                    });
                }

                window.ebayPagination.currentPage = 1;
                renderEbayCurrentPage();
            }

            function clearPopupSearch() {
                const searchInput = document.getElementById('ebay-popup-search');
                if (searchInput) searchInput.value = '';
                filterEbayPopupListings();
            }

            function changeEbayPageSize(size) {
                window.ebayPagination.pageSize = parseInt(size, 10) || 20;
                window.ebayPagination.currentPage = 1;
                renderEbayCurrentPage();
            }

            function goToEbayPage(page) {
                const totalItems = (window.ebayPagination.filteredItems || []).length;
                const pageSize = window.ebayPagination.pageSize || 20;
                const totalPages = Math.max(1, Math.ceil(totalItems / pageSize));
                const target = Math.max(1, Math.min(parseInt(page, 10) || 1, totalPages));

                window.ebayPagination.currentPage = target;
                renderEbayCurrentPage(true);
            }

            function renderEbayCurrentPage(scrollToTop = false) {
                const allFiltered = window.ebayPagination.filteredItems || [];
                const totalItems = allFiltered.length;
                const pageSize = window.ebayPagination.pageSize || 20;
                const totalPages = Math.max(1, Math.ceil(totalItems / pageSize));

                if (window.ebayPagination.currentPage > totalPages) {
                    window.ebayPagination.currentPage = totalPages;
                }
                if (window.ebayPagination.currentPage < 1) {
                    window.ebayPagination.currentPage = 1;
                }

                const currentPage = window.ebayPagination.currentPage;
                const paginationBar = document.getElementById('ebay-popup-pagination');
                const infoEl = document.getElementById('ebay-pagination-info');
                const controlsEl = document.getElementById('ebay-pagination-controls');

                if (totalItems === 0) {
                    if (paginationBar) paginationBar.classList.add('hidden');
                    renderEbayPopupListings([]);
                    return;
                }

                if (paginationBar) paginationBar.classList.remove('hidden');

                const startIndex = (currentPage - 1) * pageSize;
                const endIndex = Math.min(startIndex + pageSize, totalItems);
                const pageItems = allFiltered.slice(startIndex, endIndex);

                if (infoEl) {
                    infoEl.innerHTML = `Showing <span class="text-slate-900 font-black">${startIndex + 1}–${endIndex}</span> of <span class="text-slate-900 font-black">${totalItems}</span> listings <span class="text-slate-400 font-normal">(Page ${currentPage} of ${totalPages})</span>`;
                }

                if (controlsEl) {
                    let html = '';

                    // First page button
                    if (currentPage > 2 && totalPages > 5) {
                        html += `
                            <button type="button" onclick="goToEbayPage(1)" title="First Page"
                                class="px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-bold text-xs transition-all shadow-xs">
                                <i class="fas fa-angle-double-left"></i>
                            </button>
                        `;
                    }

                    // Prev button
                    if (currentPage > 1) {
                        html += `
                            <button type="button" onclick="goToEbayPage(${currentPage - 1})"
                                class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition-all shadow-xs flex items-center gap-1.5">
                                <i class="fas fa-chevron-left text-[10px]"></i> Prev
                            </button>
                        `;
                    } else {
                        html += `
                            <button type="button" disabled
                                class="px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-100 text-slate-300 font-bold text-xs cursor-not-allowed flex items-center gap-1.5">
                                <i class="fas fa-chevron-left text-[10px]"></i> Prev
                            </button>
                        `;
                    }

                    // Numeric buttons
                    const maxButtons = 5;
                    let startPage = Math.max(1, currentPage - 2);
                    let endPage = Math.min(totalPages, startPage + maxButtons - 1);
                    if (endPage - startPage < maxButtons - 1) {
                        startPage = Math.max(1, endPage - maxButtons + 1);
                    }

                    if (startPage > 1) {
                        html += `
                            <button type="button" onclick="goToEbayPage(1)"
                                class="w-8 h-8 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition-all shadow-xs">
                                1
                            </button>
                        `;
                        if (startPage > 2) {
                            html += `<span class="px-1 text-slate-400 font-bold text-xs select-none">...</span>`;
                        }
                    }

                    for (let p = startPage; p <= endPage; p++) {
                        if (p === currentPage) {
                            html += `
                                <button type="button"
                                    class="w-8 h-8 rounded-lg border border-orange-500 bg-orange-500 text-white font-black text-xs shadow-sm shadow-orange-200">
                                    ${p}
                                </button>
                            `;
                        } else {
                            html += `
                                <button type="button" onclick="goToEbayPage(${p})"
                                    class="w-8 h-8 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition-all shadow-xs">
                                    ${p}
                                </button>
                            `;
                        }
                    }

                    if (endPage < totalPages) {
                        if (endPage < totalPages - 1) {
                            html += `<span class="px-1 text-slate-400 font-bold text-xs select-none">...</span>`;
                        }
                        html += `
                            <button type="button" onclick="goToEbayPage(${totalPages})"
                                class="w-8 h-8 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition-all shadow-xs">
                                ${totalPages}
                            </button>
                        `;
                    }

                    // Next button
                    if (currentPage < totalPages) {
                        html += `
                            <button type="button" onclick="goToEbayPage(${currentPage + 1})"
                                class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition-all shadow-xs flex items-center gap-1.5">
                                Next <i class="fas fa-chevron-right text-[10px]"></i>
                            </button>
                        `;
                    } else {
                        html += `
                            <button type="button" disabled
                                class="px-3 py-1.5 rounded-lg border border-slate-200 bg-slate-100 text-slate-300 font-bold text-xs cursor-not-allowed flex items-center gap-1.5">
                                Next <i class="fas fa-chevron-right text-[10px]"></i>
                            </button>
                        `;
                    }

                    // Last page button
                    if (currentPage < totalPages - 1 && totalPages > 5) {
                        html += `
                            <button type="button" onclick="goToEbayPage(${totalPages})" title="Last Page"
                                class="px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-bold text-xs transition-all shadow-xs">
                                <i class="fas fa-angle-double-right"></i>
                            </button>
                        `;
                    }

                    controlsEl.innerHTML = html;
                }

                // Render page slice
                renderEbayPopupListings(pageItems);

                if (scrollToTop) {
                    const scrollArea = document.getElementById('ebay-modal-scroll-area');
                    if (scrollArea) scrollArea.scrollTop = 0;
                }
            }

            function renderEbayPopupListings(items) {
                const list = document.getElementById('ebay-popup-listings-list');
                const empty = document.getElementById('ebay-popup-empty');
                const connectBox = document.getElementById('ebay-popup-connect-box');

                if (!items || items.length === 0) {
                    list.innerHTML = '';
                    const searchVal = (document.getElementById('ebay-popup-search')?.value || '').trim();
                    const emptyTitle = document.querySelector('#ebay-popup-empty h4');
                    const emptyDesc = document.getElementById('ebay-popup-empty-desc') || document.querySelector('#ebay-popup-empty p');
                    
                    if (searchVal) {
                        if (emptyTitle) emptyTitle.textContent = 'No Matching eBay Listings';
                        if (emptyDesc) emptyDesc.textContent = `No items match "${searchVal}". Try clearing or changing search keywords.`;
                        if (connectBox) connectBox.classList.add('hidden');
                    } else {
                        const sellerName = document.getElementById('ebay-seller-name')?.textContent || 'this seller';
                        if (emptyTitle) emptyTitle.textContent = 'No Active Listings Found';
                        
                        let reasonsHtml = '';
                        if (window.ebayModalReasons && window.ebayModalReasons.length > 0) {
                            reasonsHtml = `
                                <div class="mt-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-left">
                                    <div class="flex items-center gap-2 text-amber-900 font-bold text-xs mb-2">
                                        <i class="fas fa-exclamation-triangle text-amber-600"></i>
                                        <span>Actual Reason Data Was Not Fetched:</span>
                                    </div>
                                    <ul class="list-disc list-inside space-y-1.5 text-xs text-amber-900 leading-relaxed font-medium">
                                        ${window.ebayModalReasons.map(r => `<li>${r}</li>`).join('')}
                                    </ul>
                                </div>
                            `;
                        } else if (window.ebayModalNotice) {
                            reasonsHtml = `<div class="mt-2 text-xs text-slate-500 font-medium">${window.ebayModalNotice}</div>`;
                        }

                        if (emptyDesc) {
                            emptyDesc.innerHTML = `<span class="font-bold text-slate-700">eBay returned 0 active listings for ${sellerName}.</span>` + reasonsHtml;
                        }
                        if (connectBox) connectBox.classList.remove('hidden');
                    }
                    empty.classList.remove('hidden');
                    return;
                }

                empty.classList.add('hidden');
                if (connectBox) connectBox.classList.add('hidden');

                list.innerHTML = items.map(item => {
                    const imgSrc = item.image_url || '/images/placeholder.png';
                    const isSynced = !!item.is_synced;
                    const formattedPrice = (typeof item.price === 'number') ? item.price.toFixed(2) : '0.00';

                    const locationBadge = item.put_away_location 
                        ? `<span class="bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-extrabold px-2 py-0.5 rounded-lg inline-flex items-center gap-1" title="Extracted Put Away Location"><i class="fas fa-map-marker-alt text-blue-500"></i> Location: ${item.put_away_location}</span>` 
                        : '';

                    let dimsBadge = '';
                    if (item.dimensions) {
                        const d = item.dimensions;
                        const parts = [];
                        if (d.length || d.width || d.height) {
                            parts.push(`${d.length || 0}" × ${d.width || 0}" × ${d.height || 0}"`);
                        }
                        if (d.weight) {
                            parts.push(`${d.weight} lbs`);
                        }
                        if (parts.length > 0) {
                            dimsBadge = `<span class="bg-slate-100 text-slate-600 border border-slate-200 text-[10px] font-bold px-2 py-0.5 rounded-lg inline-flex items-center gap-1" title="Package Dimensions & Weight"><i class="fas fa-box text-slate-400"></i> ${parts.join(' | ')}</span>`;
                        }
                    }

                    return `
                        <div id="ebay-card-${item.itemId}" class="bg-white border ${isSynced ? 'border-emerald-200 bg-emerald-50/20' : 'border-slate-200'} rounded-2xl p-4 flex gap-4 hover:shadow-md transition-all relative group">
                            
                            {{-- Image Preview --}}
                            <div class="w-24 h-24 rounded-xl bg-slate-100 overflow-hidden shrink-0 border border-slate-100 relative group/img cursor-pointer" onclick='openGallery(${JSON.stringify(item.photo_urls && item.photo_urls.length > 0 ? item.photo_urls : [imgSrc])})' title="Click to view photos">
                                <img src="${imgSrc}" alt="${item.title}" class="w-full h-full object-cover" loading="lazy" onerror="this.src='/images/placeholder.png'">
                                ${isSynced ? '<span class="absolute top-1 left-1 bg-emerald-600 text-white text-[9px] font-black px-1.5 py-0.5 rounded shadow"><i class="fas fa-check"></i> SYNCED</span>' : ''}
                                ${(item.photo_urls && item.photo_urls.length > 1) ? `<span class="absolute bottom-1 right-1 bg-slate-900/80 text-white text-[9px] font-black px-1.5 py-0.5 rounded shadow backdrop-blur-sm"><i class="fas fa-images mr-0.5"></i> ${item.photo_urls.length}</span>` : ''}
                            </div>

                            {{-- Item Details --}}
                            <div class="flex-grow flex flex-col justify-between min-w-0">
                                <div>
                                    <div class="flex items-start justify-between gap-2 mb-1">
                                        <h4 class="text-xs font-bold text-slate-800 line-clamp-2 leading-snug hover:text-orange-600 transition-colors" title="${item.title}">
                                            ${item.title}
                                        </h4>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 text-[10px] text-slate-400 font-semibold mb-2">
                                        <span>ID: <a href="${item.itemWebUrl}" target="_blank" class="text-blue-600 font-bold hover:underline">#${item.itemId}</a></span>
                                        <span>•</span>
                                        <span><i class="far fa-clock"></i> ${item.formatted_date}</span>
                                        <span>•</span>
                                        <span class="text-slate-600 font-bold"><i class="fa-brands fa-ebay text-orange-500 mr-0.5"></i> ${item.seller || 'Seller'}</span>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5 mb-2">
                                        ${locationBadge}
                                        ${dimsBadge}
                                    </div>
                                </div>

                                {{-- Price, Qty & Action --}}
                                <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100">
                                    <div>
                                        <div class="text-sm font-black text-slate-900">$${formattedPrice}</div>
                                        <div class="text-[10px] font-bold text-slate-500">
                                            Qty: ${item.quantity === 0 ? '<span class="text-rose-600 font-extrabold">0 (Out of stock)</span>' : `<span class="text-slate-800 font-black">${item.quantity}</span>`} | ${item.condition}
                                        </div>
                                    </div>

                                    <button id="sync-btn-${item.itemId}" type="button" onclick="syncSingleEbayItem('${item.itemId}')"
                                        class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all shadow-sm flex items-center gap-1.5 ${
                                            isSynced 
                                            ? 'bg-slate-100 hover:bg-slate-200 text-emerald-700 border border-emerald-200' 
                                            : 'bg-orange-500 hover:bg-orange-600 text-white active:scale-95 shadow-orange-200'
                                        }">
                                        ${
                                            isSynced 
                                            ? '<i class="fas fa-check-circle text-emerald-600"></i> Re-sync' 
                                            : '<i class="fas fa-sync-alt"></i> Sync to Inventory'
                                        }
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            }

            function syncSingleEbayItem(itemId) {
                const item = window.ebayModalItems.find(i => String(i.itemId) === String(itemId));
                if (!item) return;

                const btn = document.getElementById(`sync-btn-${itemId}`);
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Syncing...';
                }

                fetch(syncSingleEbayUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        itemId: item.itemId,
                        title: item.title,
                        price: item.price,
                        condition: item.condition,
                        category: item.category,
                        itemWebUrl: item.itemWebUrl,
                        image_url: item.image_url,
                        photo_urls: item.photo_urls,
                        quantity: item.quantity,
                        put_away_location: item.put_away_location,
                        dimensions: item.dimensions,
                        seller: item.seller || document.getElementById('ebay-seller-name')?.textContent || '',
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        item.is_synced = true;
                        item.inventory_status = 'In Stock';
                        if (data.quantity !== undefined) {
                            item.quantity = data.quantity;
                        }

                        const card = document.getElementById(`ebay-card-${itemId}`);
                        if (card) {
                            card.classList.add('border-emerald-200', 'bg-emerald-50/20');
                            const qtyEl = card.querySelector('.text-\\[10px\\].font-bold');
                            if (qtyEl && data.quantity !== undefined) {
                                const qVal = data.quantity;
                                const qHtml = qVal === 0 ? '<span class="text-rose-600 font-extrabold">0 (Out of stock)</span>' : `<span class="text-slate-800 font-black">${qVal}</span>`;
                                qtyEl.innerHTML = `Qty: ${qHtml} | ${item.condition}`;
                            }
                        }

                        if (btn) {
                            btn.disabled = false;
                            btn.className = 'px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all shadow-sm flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-emerald-700 border border-emerald-200';
                            btn.innerHTML = '<i class="fas fa-check-circle text-emerald-600"></i> Re-sync';
                        }

                        updateEbayPopupStats();

                        // Refresh background inventory table & stats
                        if (typeof inventoryManager !== 'undefined') {
                            if (inventoryManager.loadData) inventoryManager.loadData();
                            if (inventoryManager.loadStats) inventoryManager.loadStats();
                        }
                    } else {
                        alert('Sync failed: ' + (data.message || 'Unknown error'));
                        if (btn) {
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fas fa-sync-alt"></i> Sync to Inventory';
                        }
                    }
                })
                .catch(err => {
                    alert('Sync error: ' + (err.message || 'Network error'));
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-sync-alt"></i> Sync to Inventory';
                    }
                });
            }

            function syncAllEbayItems() {
                const unsyncedItems = (window.ebayModalItems || []).filter(i => !i.is_synced);
                const isAllSynced = unsyncedItems.length === 0;

                const btn = document.getElementById('sync-all-ebay-btn');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> ' + (isAllSynced ? 'Updating All Quantities...' : 'Syncing All...');
                }

                fetch(ebayFetchUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        force_resync: isAllSynced,
                        seller: document.getElementById('ebay-seller-name')?.textContent || '',
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-cloud-download-alt mr-1"></i> Sync All Unsynced';
                    }

                    if (data.success) {
                        alert(data.message || 'All eBay listings synced successfully!');
                        loadEbayListingsModal();

                        // Refresh background inventory table & stats
                        if (typeof inventoryManager !== 'undefined') {
                            if (inventoryManager.loadData) inventoryManager.loadData();
                            if (inventoryManager.loadStats) inventoryManager.loadStats();
                        }
                    } else {
                        alert('Batch sync failed: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(err => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-cloud-download-alt mr-1"></i> Sync All Unsynced';
                    }
                    alert('Batch sync error: ' + (err.message || 'Network error'));
                });
            }

            async function matchAllEbayQuantities() {
                const btn = document.getElementById('match-ebay-qty-btn');
                const modalBtn = document.getElementById('modal-match-ebay-qty-btn');
                const setBtns = (disabled, html) => {
                    if (btn) {
                        btn.disabled = disabled;
                        if (html) btn.innerHTML = html;
                    }
                    if (modalBtn) {
                        modalBtn.disabled = disabled;
                        if (html) modalBtn.innerHTML = html;
                    }
                };

                if (!confirm('Match and update all synced products in inventory with their current quantity available on eBay (as shown in the Fetch from eBay modal)?')) {
                    return;
                }

                setBtns(true, '<i class="fas fa-spinner fa-spin mr-1.5"></i> Checking listings...');

                try {
                    let items = window.ebayModalItems;

                    // If modal has not been opened yet or is empty, fetch active listings data first
                    if (!items || !Array.isArray(items) || items.length === 0) {
                        setBtns(true, '<i class="fas fa-spinner fa-spin mr-1.5"></i> Loading eBay listings...');
                        const listingsRes = await fetch(ebayListingsUrl, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                        });
                        const listingsText = await listingsRes.text();
                        let listingsData;
                        try {
                            listingsData = JSON.parse(listingsText);
                        } catch (e) {
                            throw new Error(listingsRes.ok ? 'Invalid response format from eBay listings.' : `Server error (${listingsRes.status}) while retrieving eBay listings.`);
                        }

                        if (listingsData && listingsData.success && Array.isArray(listingsData.items)) {
                            items = listingsData.items;
                            window.ebayModalItems = listingsData.items;
                            if (listingsData.seller && document.getElementById('ebay-seller-name')) {
                                document.getElementById('ebay-seller-name').textContent = listingsData.seller;
                            }
                        }
                    }

                    setBtns(true, '<i class="fas fa-spinner fa-spin mr-1.5"></i> Matching Quantities...');

                    const payload = {
                        seller: document.getElementById('ebay-seller-name')?.textContent || '',
                        items: (items || []).map(i => ({
                            itemId: i.itemId,
                            quantity: i.quantity,
                            inventory_id: i.inventory_id,
                            price: i.price,
                            title: i.title,
                        })),
                    };

                    const matchRes = await fetch(matchEbayQuantitiesUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });

                    const matchText = await matchRes.text();
                    let data;
                    try {
                        data = JSON.parse(matchText);
                    } catch (e) {
                        throw new Error(matchRes.ok ? 'Invalid response from server.' : `Server returned an error (${matchRes.status}).`);
                    }

                    setBtns(false, '<i class="fas fa-check mr-1.5"></i> Quantities Matched!');
                    setTimeout(() => {
                        if (btn) btn.innerHTML = '<i class="fas fa-sync-alt mr-1.5"></i> Match eBay Quantities';
                        if (modalBtn) modalBtn.innerHTML = '<i class="fas fa-sync-alt mr-1.5"></i> Match Quantities';
                    }, 3500);

                    if (data.success) {
                        alert(data.message || 'All synced product quantities have been updated to match eBay!');
                        if (typeof inventoryManager !== 'undefined') {
                            if (inventoryManager.loadData) inventoryManager.loadData();
                            if (inventoryManager.loadStats) inventoryManager.loadStats();
                        }
                        if (!document.getElementById('ebayListingsModal').classList.contains('hidden')) {
                            loadEbayListingsModal();
                        }
                    } else {
                        alert('Error matching eBay quantities: ' + (data.message || 'Unknown error'));
                    }
                } catch (err) {
                    setBtns(false, null);
                    if (btn) btn.innerHTML = '<i class="fas fa-sync-alt mr-1.5"></i> Match eBay Quantities';
                    if (modalBtn) modalBtn.innerHTML = '<i class="fas fa-sync-alt mr-1.5"></i> Match Quantities';
                    alert('Error matching eBay quantities: ' + (err.message || 'Network error'));
                }
            }

            const defaultDirectSellOrderStatus = "{{ \App\Models\Order::STATUS_IN_STORE_PURCHASED }}";

            async function deleteZeroQuantityItems() {
                const countBadge = document.getElementById('zero-qty-count');
                const countText = countBadge ? countBadge.textContent.trim() : '0';
                const currentCount = parseInt(countText) || 0;

                if (currentCount === 0) {
                    alert('No items with 0 quantity found to delete.');
                    return;
                }

                if (!confirm(`Are you sure you want to delete all ${currentCount} items with quantity 0 from inventory?\n\nThey will be removed from inventory and moved to Trash.`)) {
                    return;
                }

                const btn = document.getElementById('delete-zero-qty-btn');
                const originalHtml = btn ? btn.innerHTML : '';
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i> Deleting...';
                }

                try {
                    const response = await fetch("{{ route('admin.inventory.delete-zero-quantity') }}", {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Accept': 'application/json',
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert(data.message || 'Zero quantity items deleted successfully!');
                        if (countBadge) countBadge.textContent = '0';
                        if (typeof inventoryManager !== 'undefined') {
                            if (inventoryManager.loadData) inventoryManager.loadData();
                            if (inventoryManager.loadStats) inventoryManager.loadStats();
                        }
                    } else {
                        alert('Error: ' + (data.message || 'Failed to delete items.'));
                    }
                } catch (err) {
                    alert('Network error: ' + (err.message || 'Failed to communicate with server.'));
                } finally {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                        const updatedBadge = document.getElementById('zero-qty-count');
                        if (updatedBadge) updatedBadge.textContent = '0';
                    }
                }
            }
        </script>
        <script
            src="{{ asset('js/admin/inventory/inventory-management.js') }}?v={{ filemtime(public_path('js/admin/inventory/inventory-management.js')) }}">
        </script>
    @endpush
@endsection
