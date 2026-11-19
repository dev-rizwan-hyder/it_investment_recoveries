@extends('layouts.admin')

@section('title', 'Refurb/E-commerce Intake')

@push('styles')
    <style>
        @keyframes blinkRedFolder {
            0%, 100% {
                background-color: #dc2626 !important;
                border-color: #991b1b !important;
                box-shadow: 0 0 0 5px rgba(220, 38, 38, 0.6), 0 0 30px 10px rgba(220, 38, 38, 0.85) !important;
            }
            50% {
                background-color: #ef4444 !important;
                border-color: #7f1d1d !important;
                box-shadow: 0 0 0 7px rgba(239, 68, 68, 0.75), 0 0 40px 14px rgba(239, 68, 68, 0.95) !important;
            }
        }
        .blinking-red-card,
        div.product-card.blinking-red-card {
            animation: blinkRedFolder 0.5s infinite ease-in-out !important;
            background-color: #dc2626 !important;
            border-color: #991b1b !important;
        }
        .blinking-red-card h3,
        .blinking-red-card p,
        .blinking-red-card span.text-slate-900,
        .blinking-red-card span.text-slate-700,
        .blinking-red-card span.text-slate-500,
        .blinking-red-card span.text-slate-400,
        .blinking-red-card i.fa-folder,
        .blinking-red-card i.fa-folder-open,
        .blinking-red-card i.fa-clock {
            color: #ffffff !important;
        }
    </style>
@endpush

@section('content')
        {{-- Flash Message --}}
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                class="max-w-8xl mx-auto mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl font-bold flex items-center shadow-sm text-sm">
                <i class="fas fa-check-circle mr-2 text-base"></i> {{ session('message') }}
            </div>
        @endif

        <!-- Header Section -->
        <div class="max-w-8xl mx-auto flex flex-col md:flex-row md:items-end justify-between mb-8 gap-6 no-print">
            <div>
                <nav class="flex mb-2 space-x-2 text-[10px] font-bold tracking-widest text-slate-400 uppercase">
                    <span>Admin</span>
                    <span class="text-slate-300">/</span>
                    <span class="text-indigo-600">Inventory</span>
                </nav>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Refurb/E-commerce Intake</h1>
                <p class="text-slate-500 mt-1 text-sm font-medium">Manage physical stock folders, coordinate location
                    storage, and upload item images.</p>
            </div>

            <a href="{{ route('admin.items-lot.intake', ['type' => 'refurbished']) }}"
                class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-lg hover:shadow-indigo-200 active:scale-95">
                <i class="fas fa-plus mr-2 text-[10px]"></i>
                Add Product Folder
            </a>
        </div>

        <!-- Quick Stats Cards Grid -->
        <div id="stats-container" class="max-w-8xl mx-auto grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8 no-print">
            <!-- Folders -->
            <div
                class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex items-center justify-between transition-all duration-300 hover:shadow-md">
                <div>
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider">Total Folders</span>
                    <span id="stat-folders"
                        class="text-3xl font-black text-slate-900 mt-1 block">{{ $stats['folders'] }}</span>
                </div>
                <div
                    class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 text-lg border border-blue-100">
                    <i class="fa-solid fa-folder-closed"></i>
                </div>
            </div>

            <!-- Images -->
            <div
                class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex items-center justify-between transition-all duration-300 hover:shadow-md">
                <div>
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider">Total Images</span>
                    <span id="stat-images"
                        class="text-3xl font-black text-indigo-600 mt-1 block">{{ $stats['images'] }}</span>
                </div>
                <div
                    class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-lg border border-indigo-100">
                    <i class="fa-solid fa-images"></i>
                </div>
            </div>

            <!-- Quantity -->
            <div
                class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex items-center justify-between transition-all duration-300 hover:shadow-md">
                <div>
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider">Total Quantity</span>
                    <span id="stat-quantity"
                        class="text-3xl font-black text-emerald-600 mt-1 block">{{ $stats['quantity'] }}</span>
                </div>
                <div
                    class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-lg border border-emerald-100">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>

            <!-- Marked (Done) -->
            <div
                class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex items-center justify-between transition-all duration-300 hover:shadow-md">
                <div>
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider">Done
                        (Completed)</span>
                    <span id="stat-marked"
                        class="text-3xl font-black text-purple-650 mt-1 block">{{ $stats['marked'] }}</span>
                </div>
                <div
                    class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 text-lg border border-purple-100">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>

        <!-- Filters & Search Toolbar -->
        <div class="max-w-8xl mx-auto mb-6 no-print">
            <form id="filters-form" onsubmit="event.preventDefault();" class="flex flex-wrap items-center gap-3">
                <!-- Search Input -->
                <div class="relative flex-grow max-w-xs">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="search" id="filter-search" value="{{ $search }}"
                        placeholder="Search by name or paste a link..."
                        class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-100 shadow-sm font-medium transition-all">
                </div>

                <!-- Search by Image Button -->
                <button type="button" onclick="openImageSearchModal()"
                    class="bg-white hover:bg-indigo-50 text-indigo-700 border border-slate-200 hover:border-indigo-300 px-3.5 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2 transition-all shadow-sm shrink-0">
                    <i class="fas fa-image text-indigo-600"></i>
                    <span class="hidden sm:inline">Search by Image</span>
                </button>

                <!-- Status Filter -->
                <div class="relative w-44">
                    <select name="status" id="filter-status"
                        class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 shadow-sm font-semibold text-slate-700 transition-all">
                        <option value="">All Statuses</option>
                        <option value="remaining" {{ $status === 'remaining' ? 'selected' : '' }}>Remaining</option>
                        <option value="done" {{ $status === 'done' ? 'selected' : '' }}>Done (Any)</option>
                        <option value="abid" {{ $status === 'abid' ? 'selected' : '' }}>Abid Done</option>
                        <option value="moses" {{ $status === 'moses' ? 'selected' : '' }}>Moses Done</option>
                        <option value="both" {{ $status === 'both' ? 'selected' : '' }}>Both Done</option>
                        <option value="sold" {{ $status === 'sold' ? 'selected' : '' }}>Sold</option>
                    </select>
                </div>

                <!-- Date Range Filter -->
                <div class="relative w-44">
                    <select name="date_range" id="filter-date-range" onchange="toggleCustomDateInputs(this.value)"
                        class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 shadow-sm font-semibold text-slate-700 transition-all">
                        <option value="" {{ ($dateRange ?? '') === '' || ($dateRange ?? '') === 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="90days" {{ ($dateRange ?? '') === '90days' ? 'selected' : '' }}>Last 90 days</option>
                        <option value="today" {{ ($dateRange ?? '') === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ ($dateRange ?? '') === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="this_week" {{ in_array(($dateRange ?? ''), ['this_week', 'week'], true) ? 'selected' : '' }}>This week</option>
                        <option value="last_week" {{ ($dateRange ?? '') === 'last_week' ? 'selected' : '' }}>Last week</option>
                        <option value="this_month" {{ in_array(($dateRange ?? ''), ['this_month', 'month'], true) ? 'selected' : '' }}>This month</option>
                        <option value="last_month" {{ ($dateRange ?? '') === 'last_month' ? 'selected' : '' }}>Last month</option>
                        <option value="60days" {{ ($dateRange ?? '') === '60days' ? 'selected' : '' }}>Last 60 days</option>
                        <option value="this_year" {{ ($dateRange ?? '') === 'this_year' ? 'selected' : '' }}>This year</option>
                        <option value="last_year" {{ ($dateRange ?? '') === 'last_year' ? 'selected' : '' }}>Last year</option>
                        <option value="custom" {{ ($dateRange ?? '') === 'custom' ? 'selected' : '' }}>Custom</option>
                    </select>
                </div>

                <!-- Custom Date Range Inputs -->
                <div id="custom-date-container" class="{{ ($dateRange ?? '') === 'custom' ? 'flex' : 'hidden' }} items-center gap-2">
                    <input type="date" name="start_date" id="filter-start-date" value="{{ $startDate ?? '' }}"
                        class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 outline-none focus:border-indigo-500 shadow-sm">
                    <span class="text-slate-400 text-xs font-bold">to</span>
                    <input type="date" name="end_date" id="filter-end-date" value="{{ $endDate ?? '' }}"
                        class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 outline-none focus:border-indigo-500 shadow-sm">
                </div>

                <!-- Folder Type Filter -->
                <div class="relative w-44">
                    <select name="folder_type" id="filter-folder-type"
                        class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 shadow-sm font-semibold text-slate-700 transition-all">
                        <option value="">All Types</option>
                        <option value="Ebay" {{ $folderTypeSearch === 'Ebay' ? 'selected' : '' }}>Ebay</option>
                        <option value="Denver" {{ $folderTypeSearch === 'Denver' ? 'selected' : '' }}>Denver</option>
                        <option value="Both" {{ $folderTypeSearch === 'Both' ? 'selected' : '' }}>Both</option>
                    </select>
                </div>

                <!-- User Filter -->
                <div class="relative w-44">
                    <select name="user" id="filter-user"
                        class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 shadow-sm font-semibold text-slate-700 transition-all">
                        <option value="">All Persons</option>
                        <option value="Abid" {{ ($userSearch ?? '') === 'Abid' ? 'selected' : '' }}>Abid</option>
                        <option value="Junaid" {{ ($userSearch ?? '') === 'Junaid' ? 'selected' : '' }}>Junaid</option>
                        <option value="Moses" {{ ($userSearch ?? '') === 'Moses' ? 'selected' : '' }}>Moses</option>
                    </select>
                </div>

                <!-- Reset Filters -->
                <button type="button" id="btn-clear-filters" onclick="clearSearchFilters()"
                    class="hidden bg-slate-100 hover:bg-slate-200 text-slate-650 px-4 py-2.5 rounded-xl font-bold text-xs flex items-center border border-slate-200 transition-all shadow-sm">
                    <i class="fas fa-times mr-2 opacity-70"></i> Clear Filter
                </button>
            </form>
        </div>

        <!-- Products Grid -->
        <div class="max-w-8xl mx-auto">
            <!-- Image Search Active Banner -->
            <div id="image-search-banner" class="{{ !empty($imageSearch) ? '' : 'hidden' }} mb-6 p-4 bg-gradient-to-r from-indigo-50 via-blue-50 to-indigo-50 border border-indigo-200 rounded-2xl flex items-center justify-between shadow-sm no-print">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-lg shadow-md">
                        <i class="fas fa-image"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-800">
                            Image Search Results <span id="image-search-count-badge" class="ml-2 bg-indigo-100 text-indigo-800 text-xs px-2.5 py-0.5 rounded-full font-black border border-indigo-200">{{ method_exists($products, 'total') ? $products->total() : count($products) }} Matched</span>
                        </h4>
                        <p class="text-xs text-slate-500 font-semibold" id="image-search-filename-text">
                            Showing product folders matching image: <strong>{{ $imageSearchName ?? '' }}</strong>
                        </p>
                    </div>
                </div>
                <button type="button" onclick="clearImageSearch()" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-xl text-xs font-extrabold transition-all shadow-sm flex items-center gap-1.5">
                    <i class="fas fa-times text-slate-400"></i> Clear Image Search
                </button>
            </div>

            <div id="product-grid" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @include('admin.ebay.partials.products-list', ['products' => $products])
            </div>

            <!-- Pagination Container -->
            <div id="pagination-container" class="mt-8">
                @include('admin.ebay.partials.pagination', ['products' => $products])
            </div>
        </div>
    </div>

    <!-- ================= ADD MODAL ================= -->
    <div id="addProductModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeAddProductModal()"></div>
        <div
            class="relative bg-white rounded-2xl shadow-xl w-full max-w-3xl mx-4 z-10 overflow-hidden transform transition-all border border-slate-100 max-h-[90vh] flex flex-col">
            <!-- Modal Header -->
            <div class="px-6 py-5 bg-slate-50 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
                <h3 class="text-lg font-bold text-slate-900">Add Ebay Product Folder</h3>
                <button onclick="closeAddProductModal()" class="text-slate-400 hover:text-slate-650 transition">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="addProductForm" onsubmit="submitAddProduct(event)" class="p-6 space-y-4 overflow-y-auto flex-1">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Folder Name -->
                    <div class="md:col-span-2">
                        <label for="add_folder_name"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Folder Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="add_folder_name" name="folder_name" required
                            placeholder="Enter folder/product name"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-100 transition-all font-semibold">
                    </div>

                    <!-- Condition -->
                    <div>
                        <label for="add_condition"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Condition <span
                                class="text-red-500">*</span></label>
                        <select id="add_condition" name="condition" required
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold text-slate-700">
                            <option value="">Select condition</option>
                            <option value="New">New</option>
                            <option value="Used" selected>Used</option>
                            <option value="Refurbished">Refurbished</option>
                            <option value="Open Box">Open Box</option>
                        </select>
                    </div>

                    <!-- Folder Type -->
                    <div>
                        <label for="add_folder_type"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Folder Type <span
                                class="text-red-500">*</span></label>
                        <select id="add_folder_type" name="folder_type" required
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold text-slate-700">
                            <option value="Ebay" selected>Ebay</option>
                            <option value="Denver">Denver</option>
                            <option value="Both">Both</option>
                        </select>
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="add_location"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Location</label>
                        <input type="text" id="add_location" name="location" placeholder="e.g. A-14"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold">
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label for="add_quantity"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Quantity <span
                                class="text-red-500">*</span></label>
                        <input type="number" id="add_quantity" name="quantity" min="1" value="1" required
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold">
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2">
                        <label for="add_notes"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Notes</label>
                        <textarea id="add_notes" name="notes" rows="3" placeholder="Enter special details or specifications..."
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-medium"></textarea>
                    </div>

                    <!-- Done Status Dropdown -->
                    <div>
                        <label for="add_marked"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Done Status</label>
                        <select id="add_marked" name="marked"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold text-slate-700">
                            <option value="remaining">Remaining</option>
                            <option value="abid">Abid Done</option>
                            <option value="moses">Moses Done</option>
                            <option value="both">Both Done</option>
                        </select>
                    </div>

                    <!-- Serial Number -->
                    <div>
                        <label for="add_serial_number"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Serial Number</label>
                        <input type="text" id="add_serial_number" name="serial_number" placeholder="Scan or enter serial number"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold">
                    </div>

                    <!-- Weight -->
                    <div>
                        <label for="add_weight"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Weight (lbs)</label>
                        <input type="number" step="0.01" id="add_weight" name="weight" placeholder="e.g. 5.5"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold">
                    </div>

                    <!-- Value ($) -->
                    <div>
                        <label for="add_value"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Value ($)</label>
                        <input type="number" step="0.01" id="add_value" name="value" placeholder="e.g. 99.99"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold">
                    </div>

                    <!-- Person Dropdown -->
                    <div>
                        <label for="add_user"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Person</label>
                        <select id="add_user" name="user"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold text-slate-700">
                            <option value="">Select Person</option>
                            <option value="Abid">Abid</option>
                            <option value="Junaid">Junaid</option>
                            <option value="Moses">Moses</option>
                        </select>
                    </div>

                    <!-- eBay Link -->
                    <div>
                        <label for="add_ebay_link"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">eBay Link</label>
                        <input type="text" id="add_ebay_link" name="ebay_link" placeholder="e.g. ebay.com/itm/..."
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold">
                    </div>

                    <!-- Website Link -->
                    <div>
                        <label for="add_website_link"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Website Link</label>
                        <input type="text" id="add_website_link" name="website_link" placeholder="e.g. yourstore.com/..."
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold">
                    </div>

                    <!-- Social Media Link 1 -->
                    <div>
                        <label for="add_social_media_1"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Social Media Link 1</label>
                        <input type="text" id="add_social_media_1" name="social_media_1" placeholder="e.g. facebook.com/..."
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold">
                    </div>

                    <!-- Social Media Link 2 -->
                    <div>
                        <label for="add_social_media_2"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Social Media Link 2</label>
                        <input type="text" id="add_social_media_2" name="social_media_2" placeholder="e.g. instagram.com/..."
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold">
                    </div>

                    <!-- Social Media Link 3 -->
                    <div>
                        <label for="add_social_media_3"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Social Media Link 3</label>
                        <input type="text" id="add_social_media_3" name="social_media_3" placeholder="e.g. twitter.com/..."
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold">
                    </div>
                </div>

                <!-- Images Upload Section -->
                <div class="border-t border-slate-150 pt-4 space-y-4">
                    <label class="block text-xs uppercase font-bold tracking-wider text-slate-600">Images Manager</label>

                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Camera Button -->
                        <button type="button" onclick="startWebcam('add')"
                            class="inline-flex items-center justify-center px-4 py-2.5 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 border border-slate-200 hover:border-indigo-150 text-slate-700 text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-sm">
                            <i class="fas fa-camera mr-2 text-[11px]"></i>
                            Camera (Webcam)
                        </button>

                        <!-- Select Images Button (Native picker, allows camera or gallery on mobile) -->
                        <button type="button" onclick="triggerFilePicker('add')"
                            class="inline-flex items-center justify-center px-4 py-2.5 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 border border-slate-200 hover:border-indigo-150 text-slate-700 text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-sm">
                            <i class="fa-regular fa-images mr-2 text-[11px]"></i>
                            Select Images (Multi-select)
                        </button>

                        <!-- Upload State Text -->
                        <span id="add-images-status-text"
                            class="text-xs font-bold uppercase tracking-wider text-slate-450">0 selected</span>
                    </div>

                    <!-- Hidden Input Picker (Force jpeg/png to convert HEIC on iOS) -->
                    <input type="file" id="add-file-picker" accept="image/jpeg, image/png, image/webp" multiple class="hidden"
                        onchange="handleFileSelection(this, 'add')">

                    <!-- HTML5 webcam capture panel -->
                    <div id="add-webcam-panel"
                        class="hidden w-full rounded-xl border border-slate-200 bg-white overflow-hidden">
                        <div class="relative bg-black flex justify-center">
                            <video id="add-webcam-video"
                                class="w-full h-[250px] sm:h-[350px] md:h-[420px] bg-black object-cover rounded-lg"
                                autoplay muted playsinline>
                            </video>
                            <div id="add-webcam-spinner"
                                class="absolute inset-0 bg-slate-950/70 flex items-center justify-center text-xs font-bold text-white">
                                Starting stream...</div>
                        </div>
                        <div class="bg-white px-5 py-4 flex items-center justify-between border-t border-slate-100">
                            <p id="add-webcam-count-text"
                                class="text-xs font-bold uppercase tracking-wider text-slate-400">0
                                captured this session
                            </p>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <button type="button" onclick="captureWebcamFrame('add')"
                                    class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold">
                                    <i class="fa-solid fa-camera mr-2"></i>
                                    Capture Photo
                                </button>

                                <button type="button" onclick="stopWebcam('add')"
                                    class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-3 bg-slate-100 hover:bg-slate-200 rounded-xl font-semibold">
                                    Close Camera
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Thumbnail Previews Grid -->
                    <div id="add-previews-grid" class="grid grid-cols-3 md:grid-cols-5 gap-3 pt-2"></div>
                </div>

                <!-- Modal Footer (Creating state managed in JS) -->
                <div class="sticky bottom-0 z-20 bg-white border-t border-slate-200 p-4">
                    <div class="flex flex-col sm:flex-row justify-end gap-3">
                        <button type="button" onclick="closeAddProductModal()"
                            class="w-full sm:w-auto px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl">
                            Cancel
                        </button>

                        <button type="submit" id="btn-add-product-submit"
                            class="w-full sm:w-auto px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl flex items-center justify-center gap-2">
                            <i class="fa-regular fa-folder-open"></i>
                            <span>Create Folder</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= EDIT MODAL ================= -->
    <div id="editProductModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeEditProductModal()"></div>
        <div
            class="relative bg-white rounded-2xl shadow-xl w-full max-w-3xl mx-4 z-10 overflow-hidden transform transition-all border border-slate-100 max-h-[90vh] flex flex-col">
            <!-- Modal Header -->
            <div class="px-6 py-5 bg-slate-50 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
                <h3 id="edit-modal-title" class="text-lg font-bold text-slate-900">Edit Product Folder</h3>
                <button onclick="closeEditProductModal()" class="text-slate-400 hover:text-slate-650 transition">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="editProductForm" onsubmit="submitEditProduct(event)" class="p-6 space-y-4 overflow-y-auto flex-1">
                @csrf
                @method('PATCH')

                <input type="hidden" name="folder" id="edit_folder">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Folder Name -->
                    <div class="md:col-span-2">
                        <label for="edit_folder_name"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Folder Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="edit_folder_name" name="folder_name" required
                            placeholder="Enter folder/product name"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-100 transition-all font-semibold">
                    </div>

                    <!-- Condition -->
                    <div>
                        <label for="edit_condition"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Condition <span
                                class="text-red-500">*</span></label>
                        <select id="edit_condition" name="condition" required
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold text-slate-700">
                            <option value="">Select condition</option>
                            <option value="New">New</option>
                            <option value="Used">Used</option>
                            <option value="Refurbished">Refurbished</option>
                            <option value="Open Box">Open Box</option>
                        </select>
                    </div>

                    <!-- Folder Type -->
                    <div>
                        <label for="edit_folder_type"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Folder Type <span
                                class="text-red-500">*</span></label>
                        <select id="edit_folder_type" name="folder_type" required
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold text-slate-700">
                            <option value="Ebay">Ebay</option>
                            <option value="Denver">Denver</option>
                            <option value="Both">Both</option>
                        </select>
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="edit_location"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Location</label>
                        <input type="text" id="edit_location" name="location" placeholder="e.g. A-14"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold">
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label for="edit_quantity"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Quantity <span
                                class="text-red-500">*</span></label>
                        <input type="number" id="edit_quantity" name="quantity" min="1" required
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold">
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2">
                        <label for="edit_notes"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Notes</label>
                        <textarea id="edit_notes" name="notes" rows="3" placeholder="Enter special details..."
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-medium"></textarea>
                    </div>

                    <!-- Done Status Dropdown -->
                    <div>
                        <label for="edit_marked"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Done Status</label>
                        <select id="edit_marked" name="marked"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold text-slate-700">
                            <option value="remaining">Remaining</option>
                            <option value="abid">Abid Done</option>
                            <option value="moses">Moses Done</option>
                            <option value="both">Both Done</option>
                        </select>
                    </div>

                    <!-- Serial Number -->
                    <div>
                        <label for="edit_serial_number"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Serial Number</label>
                        <input type="text" id="edit_serial_number" name="serial_number" placeholder="Scan or enter serial number"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold">
                    </div>

                    <!-- Weight -->
                    <div>
                        <label for="edit_weight"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Weight (lbs)</label>
                        <input type="number" step="0.01" id="edit_weight" name="weight" placeholder="e.g. 5.5"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold">
                    </div>

                    <!-- Value ($) -->
                    <div>
                        <label for="edit_value"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Value ($)</label>
                        <input type="number" step="0.01" id="edit_value" name="value" placeholder="e.g. 99.99"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold">
                    </div>

                    <!-- Person Dropdown -->
                    <div>
                        <label for="edit_user"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Person</label>
                        <select id="edit_user" name="user"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold text-slate-700">
                            <option value="">Select Person</option>
                            <option value="Abid">Abid</option>
                            <option value="Junaid">Junaid</option>
                            <option value="Moses">Moses</option>
                        </select>
                    </div>

                    <!-- eBay Link -->
                    <div>
                        <label for="edit_ebay_link"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">eBay Link</label>
                        <input type="text" id="edit_ebay_link" name="ebay_link" placeholder="e.g. ebay.com/itm/..."
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold">
                    </div>

                    <!-- Website Link -->
                    <div>
                        <label for="edit_website_link"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Website Link</label>
                        <input type="text" id="edit_website_link" name="website_link" placeholder="e.g. yourstore.com/..."
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold">
                    </div>

                    <!-- Social Media Link 1 -->
                    <div>
                        <label for="edit_social_media_1"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Social Media Link 1</label>
                        <input type="text" id="edit_social_media_1" name="social_media_1" placeholder="e.g. facebook.com/..."
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold">
                    </div>

                    <!-- Social Media Link 2 -->
                    <div>
                        <label for="edit_social_media_2"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Social Media Link 2</label>
                        <input type="text" id="edit_social_media_2" name="social_media_2" placeholder="e.g. instagram.com/..."
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold">
                    </div>

                    <!-- Social Media Link 3 -->
                    <div>
                        <label for="edit_social_media_3"
                            class="block text-xs uppercase font-bold tracking-wider text-slate-600 mb-2">Social Media Link 3</label>
                        <input type="text" id="edit_social_media_3" name="social_media_3" placeholder="e.g. twitter.com/..."
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 transition-all font-semibold">
                    </div>
                </div>

                <!-- Existing & New Images section -->
                <div class="border-t border-slate-150 pt-4 space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs uppercase font-bold tracking-wider text-slate-600">Folder Images
                            Gallery</label>
                        <span id="edit-images-count-text" class="text-xs font-extrabold text-indigo-600">0 images</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Camera Button -->
                        <button type="button" onclick="startWebcam('edit')"
                            class="inline-flex items-center justify-center px-4 py-2 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 border border-slate-200 hover:border-indigo-150 text-slate-700 text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-sm">
                            <i class="fas fa-camera mr-2 text-[10px]"></i> Camera
                        </button>

                        <!-- Select Images Button -->
                        <button type="button" onclick="triggerFilePicker('edit')"
                            class="inline-flex items-center justify-center px-4 py-2 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 border border-slate-200 hover:border-indigo-150 text-slate-700 text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-sm">
                            <i class="fa-regular fa-images mr-2 text-[10px]"></i> Add Images
                        </button>
                    </div>

                    <!-- Hidden Input Picker (Force jpeg/png to convert HEIC on iOS) -->
                    <input type="file" id="edit-file-picker" accept="image/jpeg, image/png, image/webp" multiple class="hidden"
                        onchange="handleFileSelection(this, 'edit')">

                    <!-- HTML5 webcam capture panel -->
                    <div id="edit-webcam-panel"
                        class="hidden overflow-hidden rounded-xl border border-indigo-100 bg-slate-950 transition-all duration-300">
                        <div class="relative bg-black flex justify-center">
                            <video id="edit-webcam-video" class="h-60 w-full max-w-lg bg-black object-contain" autoplay
                                muted playsinline></video>
                            <div id="edit-webcam-spinner"
                                class="absolute inset-0 bg-slate-950/70 flex items-center justify-center text-xs font-bold text-white">
                                Starting stream...</div>
                        </div>
                        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-slate-100">
                            <p id="edit-webcam-count-text"
                                class="text-xs font-bold uppercase tracking-wider text-slate-450">0 captured</p>
                            <div class="flex gap-2">
                                <button type="button" onclick="captureWebcamFrame('edit')"
                                    class="inline-flex items-center justify-center px-3.5 py-2 bg-indigo-650 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-sm">
                                    <i class="fa-solid fa-camera-retro mr-1.5"></i> Capture
                                </button>
                                <button type="button" onclick="stopWebcam('edit')"
                                    class="inline-flex items-center justify-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-650 text-xs font-bold uppercase tracking-wider rounded-xl transition-all border border-slate-200 shadow-sm">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Images Previews & Current Items List -->
                    <div id="edit-previews-grid" class="grid grid-cols-3 md:grid-cols-5 gap-3 pt-2"></div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100 flex-shrink-0">
                    <button type="button" onclick="closeEditProductModal()"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider rounded-xl transition-all">
                        Cancel
                    </button>
                    <button type="submit" id="btn-edit-product-submit"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition shadow-md flex items-center gap-2">
                        <i class="fa-regular fa-floppy-disk"></i>
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- ================= ADD TO INVENTORY MODAL ================= -->
    @include('admin.intake.modals.add-to-inventory-modal')
    @include('admin.intake.modals.partials.gallery-modal')

    <!-- ================= IMAGE SEARCH MODAL ================= -->
    <div id="imageSearchModal" class="fixed inset-0 z-[120] hidden overflow-y-auto bg-slate-900/60 backdrop-blur-md transition-opacity">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg overflow-hidden rounded-3xl bg-white p-6 shadow-2xl transition-all border border-slate-100">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm">
                            <i class="fa-solid fa-image text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Search by Image</h3>
                            <p class="text-xs font-semibold text-slate-500">Upload a product image to find visually similar items</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeImageSearchModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-800 flex items-center justify-center transition">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="py-5 space-y-4">
                    <!-- Upload Dropzone -->
                    <div id="image-dropzone" onclick="triggerSearchImagePicker()" ondragover="handleSearchDragOver(event)" ondragleave="handleSearchDragLeave(event)" ondrop="handleSearchDrop(event)"
                        class="group relative flex flex-col items-center justify-center border-2 border-dashed border-slate-200 hover:border-indigo-400 bg-slate-50/50 hover:bg-indigo-50/30 rounded-2xl p-8 cursor-pointer transition-all text-center">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-100/70 group-hover:bg-indigo-600 flex items-center justify-center text-indigo-600 group-hover:text-white transition-all shadow-sm mb-3">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-700">Click or drag image file here</p>
                        <p class="text-xs text-slate-450 mt-1 font-medium">Supports JPG, PNG, WEBP (Max 10MB)</p>
                        <input type="file" id="search-image-file-input" accept="image/jpeg, image/png, image/webp" class="hidden" onchange="handleSearchImageSelect(this)">
                    </div>

                    <!-- Selected Image Preview Area -->
                    <div id="search-image-preview-container" class="hidden border border-slate-200 rounded-2xl p-3 bg-slate-50 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <img id="search-image-preview-img" src="" class="w-14 h-14 rounded-xl object-cover border border-slate-200 flex-shrink-0 shadow-sm" alt="Preview">
                            <div class="overflow-hidden">
                                <p id="search-image-preview-name" class="text-xs font-bold text-slate-800 truncate">selected_image.jpg</p>
                                <span id="search-image-preview-size" class="text-[11px] font-semibold text-slate-500">Ready to search</span>
                            </div>
                        </div>
                        <button type="button" onclick="removeSearchImagePreview()" class="w-7 h-7 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition flex-shrink-0">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeImageSearchModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider rounded-xl transition">
                        Cancel
                    </button>
                    <button type="button" id="btn-submit-image-search" disabled onclick="submitImageSearch()" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span id="btn-submit-image-search-text">Search Matches</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Global Notification Toast fallback helper
        window.showNotification = window.showNotification || function(message, type = 'info') {
            const isError = type === 'error';
            const bgClass = isError ? 'bg-red-600' : 'bg-emerald-600';
            const notification = document.createElement('div');
            notification.className = `fixed bottom-5 right-5 z-[200] px-5 py-3 rounded-xl text-white font-semibold text-sm shadow-xl transition-all duration-300 transform translate-y-2 opacity-0 ${bgClass}`;
            notification.textContent = message;
            document.body.appendChild(notification);
            requestAnimationFrame(() => {
                notification.classList.remove('translate-y-2', 'opacity-0');
            });
            setTimeout(() => {
                notification.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        };

        // Set baseUrl for barcode generation fallback if needed
        window.EbayProducts = window.EbayProducts || { baseUrl: '/admin/ebay-products' };

        // Blinking Red Card Alert Toggle & Persistence to Server
        window.toggleBlinkingRedCard = async function(btnOrFolder, folderId) {
            let card = null;
            let folder = folderId;

            if (btnOrFolder && btnOrFolder.closest) {
                card = btnOrFolder.closest('.product-card');
                if (!folder && card) {
                    folder = card.getAttribute('data-folder');
                }
            }
            if (!card && (typeof btnOrFolder === 'string')) {
                folder = btnOrFolder;
                card = document.getElementById('product-card-' + folder) || document.querySelector(`.product-card[data-folder="${folder}"]`);
            }
            if (!card && folder) {
                card = document.getElementById('product-card-' + folder) || document.querySelector(`.product-card[data-folder="${folder}"]`);
            }

            if (!folder) {
                console.error('Card element not found for folder:', folder);
                return;
            }

            const btn = (btnOrFolder && btnOrFolder.tagName === 'BUTTON') ? btnOrFolder : (card ? card.querySelector('.btn-blink-toggle') : null);
            if (btn) btn.disabled = true;

            const wasBlinking = card ? card.classList.contains('blinking-red-card') : false;
            const willBlink = !wasBlinking;

            // Optimistic UI update
            if (card) {
                if (willBlink) {
                    card.classList.add('blinking-red-card');
                    card.setAttribute('style', 'animation: blinkRedFolder 0.5s infinite ease-in-out !important; background-color: #dc2626 !important; border-color: #991b1b !important;');
                } else {
                    card.classList.remove('blinking-red-card');
                    card.removeAttribute('style');
                }
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            try {
                const response = await fetch(`/admin/ebay-products/${encodeURIComponent(folder)}/toggle-blink`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'X-HTTP-Method-Override': 'PATCH',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    const currentCard = document.getElementById(`product-card-${folder}`);
                    if (currentCard && data.html) {
                        currentCard.outerHTML = data.html;
                    }
                    if (data.blinking) {
                        if (window.showNotification) showNotification('Folder marked with Blinking Red Alert!', 'error');
                    } else {
                        if (window.showNotification) showNotification('Blinking Red Alert removed.', 'info');
                    }
                } else {
                    throw new Error(data.message || 'Failed to update alert state.');
                }
            } catch (err) {
                console.error('Toggle blink error:', err);
                // Rollback optimistic update
                if (card) {
                    if (wasBlinking) {
                        card.classList.add('blinking-red-card');
                        card.setAttribute('style', 'animation: blinkRedFolder 0.5s infinite ease-in-out !important; background-color: #dc2626 !important; border-color: #991b1b !important;');
                    } else {
                        card.classList.remove('blinking-red-card');
                        card.removeAttribute('style');
                    }
                }
                if (window.showNotification) {
                    showNotification('Could not save blinking alert to server.', 'warning');
                }
            } finally {
                const currentCard = document.getElementById(`product-card-${folder}`);
                const finalBtn = currentCard ? currentCard.querySelector('.btn-blink-toggle') : btn;
                if (finalBtn) finalBtn.disabled = false;
            }
        };

        window.restoreBlinkingCards = function() {};

        $(document).ready(function() {
            // One-time sync: migrate any previously saved local storage alerts to the server
            try {
                const legacyBlinking = JSON.parse(localStorage.getItem('ebay_blinking_folders') || '[]');
                if (Array.isArray(legacyBlinking) && legacyBlinking.length > 0) {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                    legacyBlinking.forEach(async (folder) => {
                        const card = document.getElementById('product-card-' + folder);
                        if (card && !card.classList.contains('blinking-red-card')) {
                            try {
                                const res = await fetch(`/admin/ebay-products/${encodeURIComponent(folder)}/toggle-blink`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': token,
                                        'X-HTTP-Method-Override': 'PATCH',
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({ blinking: true })
                                });
                                const d = await res.json();
                                if (d.success && d.html) {
                                    const c = document.getElementById('product-card-' + folder);
                                    if (c) c.outerHTML = d.html;
                                }
                            } catch (e) {}
                        }
                    });
                    localStorage.removeItem('ebay_blinking_folders');
                }
            } catch (e) {}
        });

        window.closeAddToInventoryModal = function() {
            $('#addToInventoryModal').addClass('hidden');
        };

        window.openEbayToInventoryModal = function(btn) {
            const folder = typeof btn === 'string' ? btn : btn.getAttribute('data-folder');
            
            $.ajax({
                url: `/admin/ebay-products/${encodeURIComponent(folder)}/prepare-inventory`,
                type: 'GET',
                success: (response) => {
                    const item = response.item;
                    const invData = response.inventoryData;

                    $('#inventory-item-id').val(item.id);

                    // Initialize category dropdown manager
                    if (window.categoryHierarchyManager) {
                        window.categoryHierarchyManager.initialize();
                    }

                    // Pre-fill fields (Brand & Model, Location, Quantity, Notes)
                    if (invData.brand_category || item.product_name) {
                        $('#inv-brand-category').val(invData.brand_category || item.product_name || '');
                    }
                    $('#inv-location').val(invData.put_away_location || '');
                    $('#inv-quantity').val(invData.quantity);
                    $('#inv-quantity').data('available', invData.quantity);
                    $('#inv-quantity').attr('max', invData.quantity);
                    $('#remaining-qty').text(`Total: ${invData.quantity} items`);
                    $('#inv-notes').val(invData.notes || '');

                    // Pre-fill eBay folder images into the modal preview
                    if (window.inventoryPhotoManager && Array.isArray(invData.images)) {
                        window.inventoryPhotoManager.accumulatedFiles = [];
                        invData.images.forEach(img => {
                            window.inventoryPhotoManager.accumulatedFiles.push({
                                id: window.inventoryPhotoManager.generateId(),
                                file: null,
                                dataUrl: img.url,
                                filename: img.filename
                            });
                        });
                    }

                    $('#quantity-error').hide();
                    $('#inv-submit-btn').prop('disabled', false);

                    // Reveal modal and refresh photo preview grid
                    $('#addToInventoryModal').removeClass('hidden');

                    if (window.inventoryPhotoManager) {
                        window.inventoryPhotoManager.refreshPhotoPreview();
                    }

                    $(document).off('submit', '#saveToInventoryForm').on('submit', '#saveToInventoryForm', async (e) => {
                        e.preventDefault();
                        const submitBtn = $('#inv-submit-btn');
                        const submitSpinner = $('#inv-submit-spinner');
                        const submitText = $('#inv-submit-text');

                        submitBtn.prop('disabled', true);
                        submitSpinner.removeClass('hidden');
                        submitText.text('Saving...');

                        const formData = new FormData(e.target);
                        const csrfToken = $('meta[name="csrf-token"]').attr('content');
                        if (csrfToken) {
                            formData.append('_token', csrfToken);
                        }
                        formData.append('source_type', 'ebay_products');

                        // Include accumulated local photos (rotated/customized)
                        if (window.inventoryPhotoManager && window.inventoryPhotoManager.getFiles) {
                            const files = await window.inventoryPhotoManager.getFiles();
                            files.forEach(file => {
                                formData.append('repair_photos[]', file);
                            });
                        }

                        $.ajax({
                            url: `/admin/ebay-products/${encodeURIComponent(folder)}/save-inventory`,
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            },
                            success: (response) => {
                                window.showNotification(response.message || 'Saved to inventory successfully!', 'success');
                                setTimeout(() => {
                                    window.closeAddToInventoryModal();
                                    location.reload(); // Quickest way to update the grid and stats
                                }, 1000);
                            },
                            error: (xhr) => {
                                const message = xhr.responseJSON?.error || xhr.responseJSON?.message || 'Failed to save to inventory';
                                window.showNotification(message, 'error');
                                submitBtn.prop('disabled', false);
                                submitSpinner.addClass('hidden');
                                submitText.text('Save to Inventory');
                            },
                        });
                    });
                },
                error: () => {
                    window.showNotification('Failed to prepare item for inventory', 'error');
                }
            });
        };

        // Delegated click handler fallback
        $(document).on('click', '.btn-add-inventory', function(e) {
            e.preventDefault();
            if (typeof window.openEbayToInventoryModal === 'function') {
                window.openEbayToInventoryModal(this);
            }
        });
    </script>

    <script>
        (() => {
            // --- Globals ---
            const urls = {
                store: "{{ route('admin.ebay-products.store') }}",
                tempUpload: "{{ route('admin.ebay-products.temp-upload') }}",
                index: "{{ route('admin.ebay-products.index') }}"
            };

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            // Active upload queues tracking
            let activeUploads = {
                add: 0,
                edit: 0
            };

            // Arrays containing temporary UUIDs returned from backend
            let tempImageIds = {
                add: [],
                edit: []
            };

            // Webcam stream handlers
            let webcamStreams = {
                add: null,
                edit: null
            };
            let webcamCaptureCount = {
                add: 0,
                edit: 0
            };

            // Live filtration search debouncer
            let debounceTimer = null;

            // ==========================================
            //         MODAL ACTIONS
            // ==========================================
            window.openAddProductModal = () => {
                document.getElementById('addProductForm').reset();
                document.getElementById('add-previews-grid').innerHTML = '';
                document.getElementById('add-images-status-text').textContent = '0 selected';
                document.getElementById('add-images-status-text').className =
                    'text-xs font-bold uppercase tracking-wider text-slate-450';
                tempImageIds.add = [];
                activeUploads.add = 0;
                updateSubmitButtonState('add');

                const modal = document.getElementById('addProductModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');

                setTimeout(() => document.getElementById('add_folder_name').focus(), 100);
            };

            window.closeAddProductModal = () => {
                stopWebcam('add');
                const modal = document.getElementById('addProductModal');
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            };

            window.openEditProductModal = (button) => {
                const encodedData = button.getAttribute('data-product-json');
                const product = JSON.parse(atob(encodedData));

                document.getElementById('edit_folder').value = product.folder;
                document.getElementById('edit_folder_name').value = product.folder_name;
                document.getElementById('edit_condition').value = product.condition;
                document.getElementById('edit_folder_type').value = product.folder_type;
                document.getElementById('edit_location').value = product.location;
                document.getElementById('edit_quantity').value = product.quantity;
                document.getElementById('edit_notes').value = product.notes;
                document.getElementById('edit_serial_number').value = product.serial_number || '';
                document.getElementById('edit_weight').value = product.weight || '';
                document.getElementById('edit_value').value = product.value || product.estimated_value || '';
                document.getElementById('edit_ebay_link').value = product.ebay_link || '';
                document.getElementById('edit_website_link').value = product.website_link || '';
                document.getElementById('edit_social_media_1').value = product.social_media_1 || '';
                document.getElementById('edit_social_media_2').value = product.social_media_2 || '';
                document.getElementById('edit_social_media_3').value = product.social_media_3 || '';

                // Done status mapping
                let markedVal = product.marked;
                let markedSelectValue = 'remaining';
                if (markedVal === true || markedVal === 'both') {
                    markedSelectValue = 'both';
                } else if (markedVal === 'ebay' || markedVal === 'abid') {
                    markedSelectValue = 'abid';
                } else if (markedVal === 'denver' || markedVal === 'moses') {
                    markedSelectValue = 'moses';
                }
                document.getElementById('edit_marked').value = markedSelectValue;

                // User select mapping
                document.getElementById('edit_user').value = product.user || '';

                document.getElementById('edit-modal-title').textContent = `Edit Folder: ${product.folder_name}`;

                // Clear past local edits
                tempImageIds.edit = [];
                activeUploads.edit = 0;
                updateSubmitButtonState('edit');

                // Render existing images grid
                renderEditImages(product.images, product.folder);

                const modal = document.getElementById('editProductModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
            };

            window.closeEditProductModal = () => {
                stopWebcam('edit');
                const modal = document.getElementById('editProductModal');
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            };

            // ==========================================
            //         BACKGROUND UPLOADING LOGIC
            // ==========================================
            window.triggerFilePicker = (type) => {
                document.getElementById(`${type}-file-picker`).click();
            };

            window.handleFileSelection = async (input, type) => {
                const files = Array.from(input.files || []);
                input.value = ''; // Reset input to let them select same files again
                if (files.length === 0) return;

                const btn = document.getElementById(`btn-${type}-product-submit`);
                const originalText = btn ? btn.querySelector('span').textContent : '';
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                    btn.querySelector('span').textContent = 'Processing images...';
                }

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    // Skip compression for non-image or small files (< 500KB)
                    if (!file.type.startsWith('image/') || file.size < 500 * 1024) {
                        uploadSingleFile(file, type);
                        continue;
                    }
                    try {
                        const compressedFile = await compressImage(file);
                        uploadSingleFile(compressedFile, type);
                    } catch (e) {
                        console.error('Compression failed, using original', e);
                        uploadSingleFile(file, type);
                    }
                }

                if (btn) {
                    btn.disabled = false;
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    btn.querySelector('span').textContent = originalText;
                }
            };

            const compressImage = (file) => {
                return new Promise((resolve, reject) => {
                    const img = new Image();
                    const objectUrl = URL.createObjectURL(file);
                    
                    img.onload = () => {
                        URL.revokeObjectURL(objectUrl);
                        
                        const canvas = document.createElement('canvas');
                        let width = img.width;
                        let height = img.height;
                        const max_dim = 800; // lower dimension for faster uploads (eBay recommends 500-1600px, 800 is a good fast middle ground)

                        if (width > height && width > max_dim) {
                            height = Math.round((height *= max_dim / width));
                            width = max_dim;
                        } else if (height > max_dim) {
                            width = Math.round((width *= max_dim / height));
                            height = max_dim;
                        }

                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob((blob) => {
                            if (blob) {
                                // Try to preserve the original extension if it's not converted to jpeg
                                let ext = 'jpg';
                                let newName = file.name;
                                if (newName.lastIndexOf('.') > 0) {
                                    newName = newName.substring(0, newName.lastIndexOf('.')) + '.' + ext;
                                }
                                resolve(new File([blob], newName, { type: 'image/jpeg', lastModified: Date.now() }));
                            } else {
                                reject(new Error('Canvas toBlob failed'));
                            }
                        }, 'image/jpeg', 0.8);
                    };
                    
                    img.onerror = (e) => {
                        URL.revokeObjectURL(objectUrl);
                        reject(e);
                    };
                    
                    img.src = objectUrl;
                });
            };

            const uploadSingleFile = async (file, type) => {
                // Instantly show as successful and queue the file in JS memory
                // to be uploaded all at once when clicking "Create Folder"
                
                const previewId = `preview-${type}-${Math.random().toString(36).substr(2, 9)}`;
                const objectUrl = URL.createObjectURL(file);
                
                // Store the file in memory
                tempImageIds[type].push({ id: previewId, file: file, url: objectUrl });
                
                const grid = document.getElementById(`${type}-previews-grid`);
                const countText = document.getElementById(`${type}-images-status-text`);

                const card = document.createElement('div');
                card.id = previewId;
                card.className = 'relative aspect-square overflow-hidden rounded-xl border border-slate-250 bg-slate-100 group';
                card.innerHTML = `
                <img src="${objectUrl}" class="h-full w-full object-cover">
                <!-- Delete Button -->
                <button type="button" onclick="removeTempPreviewCard('${previewId}', '${type}')"
                        class="absolute right-1.5 top-1.5 w-7 h-7 bg-white/95 rounded-full flex items-center justify-center text-red-650 hover:bg-red-50 shadow-sm transition opacity-0 group-hover:opacity-100 focus:opacity-100 duration-150">
                    <i class="fa-solid fa-xmark text-[11px]"></i>
                </button>
                <div class="absolute left-1.5 bottom-1.5 w-5 h-5 bg-emerald-500 rounded-full flex items-center justify-center text-white text-[10px] shadow-sm">
                    <i class="fa-solid fa-check"></i>
                </div>
            `;
                
                grid.appendChild(card);

                if (type === 'add') {
                    const totalCount = tempImageIds.add.length;
                    countText.textContent = `${totalCount} item(s) selected`;
                    countText.className = 'text-xs font-bold uppercase tracking-wider text-indigo-600';
                } else if (type === 'edit') {
                    const editCountText = document.getElementById('edit-images-count-text');
                    const totalCurrent = document.querySelectorAll('#edit-previews-grid > div').length;
                    editCountText.textContent = `${totalCurrent} images`;
                }
            };

            window.removeTempPreviewCard = (previewId, type) => {
                const card = document.getElementById(previewId);
                if (card) card.remove();

                const item = tempImageIds[type].find(i => i.id === previewId);
                if (item) URL.revokeObjectURL(item.url);

                tempImageIds[type] = tempImageIds[type].filter(i => i.id !== previewId);

                if (type === 'add') {
                    const countText = document.getElementById('add-images-status-text');
                    const totalCount = tempImageIds.add.length;
                    countText.textContent = `${totalCount} item(s) selected`;
                    if (totalCount === 0) {
                        countText.className = 'text-xs font-bold uppercase tracking-wider text-slate-450';
                    }
                } else if (type === 'edit') {
                    const countText = document.getElementById('edit-images-count-text');
                    const totalCurrent = document.querySelectorAll('#edit-previews-grid > div').length;
                    countText.textContent = `${totalCurrent} images`;
                }
            };

            // Manage button state based on uploading queues
            const updateSubmitButtonState = (type) => {
                const btn = document.getElementById(`btn-${type}-product-submit`);
                if (!btn) return;

                if (activeUploads[type] > 0) {
                    btn.disabled = true;
                    btn.classList.add('opacity-70', 'cursor-wait');
                    btn.querySelector('span').textContent = 'Uploading images...';
                } else {
                    btn.disabled = false;
                    btn.classList.remove('opacity-70', 'cursor-wait');
                    btn.querySelector('span').textContent = type === 'add' ? 'Create Folder' : 'Save Changes';
                }
            };

            // ==========================================
            //         WEBCAM CAMERA CAPTURING
            // ==========================================
            window.startWebcam = async (type) => {
                const video = document.getElementById(`${type}-webcam-video`);
                const panel = document.getElementById(`${type}-webcam-panel`);
                const spinner = document.getElementById(`${type}-webcam-spinner`);

                panel.classList.remove('hidden');
                spinner.classList.remove('hidden');
                webcamCaptureCount[type] = 0;
                updateWebcamCaptureCountText(type);

                try {
                    if (webcamStreams[type]) {
                        stopWebcam(type);
                    }

                    const stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: {
                                ideal: 'environment'
                            },
                            width: {
                                ideal: 1280
                            },
                            height: {
                                ideal: 720
                            }
                        },
                        audio: false
                    });

                    webcamStreams[type] = stream;
                    video.srcObject = stream;

                    video.onloadedmetadata = () => {
                        spinner.classList.add('hidden');
                    };

                } catch (err) {
                    console.error(err);
                    spinner.textContent = "Unable to start webcam. Fallback to file selection.";
                    setTimeout(() => {
                        panel.classList.add('hidden');
                        triggerFilePicker(type);
                    }, 1500);
                }
            };

            window.stopWebcam = (type) => {
                const video = document.getElementById(`${type}-webcam-video`);
                const panel = document.getElementById(`${type}-webcam-panel`);
                const stream = webcamStreams[type];

                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    webcamStreams[type] = null;
                }

                if (video) {
                    video.pause();
                    video.srcObject = null;
                }

                panel.classList.add('hidden');
            };

            window.captureWebcamFrame = async (type) => {
                const video = document.getElementById(`${type}-webcam-video`);
                if (!video || !webcamStreams[type] || video.readyState < 2) return;

                const width = video.videoWidth || 640;
                const height = video.videoHeight || 480;

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;

                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, width, height);

                // Convert to blob and process
                canvas.toBlob(blob => {
                    if (!blob) return;

                    const fileName = `webcam-${new Date().getTime()}.jpg`;
                    const file = new File([blob], fileName, {
                        type: 'image/jpeg'
                    });

                    uploadSingleFile(file, type);

                    webcamCaptureCount[type]++;
                    updateWebcamCaptureCountText(type);
                }, 'image/jpeg', 0.85);
            };

            const updateWebcamCaptureCountText = (type) => {
                const text = document.getElementById(`${type}-webcam-count-text`);
                if (text) text.textContent = `${webcamCaptureCount[type]} captured this session`;
            };

            // ==========================================
            //         EDIT MODAL IMAGES LIST
            // ==========================================
            const renderEditImages = (images, folder) => {
                const grid = document.getElementById('edit-previews-grid');
                const countText = document.getElementById('edit-images-count-text');

                grid.innerHTML = '';
                countText.textContent = `${images.length} images`;

                images.forEach(img => {
                    const card = document.createElement('div');
                    card.className =
                        'relative aspect-square overflow-hidden rounded-xl border border-slate-200 bg-slate-100 group';

                    if (img.can_preview) {
                        card.innerHTML = `<img src="${img.url}" class="h-full w-full object-cover">`;
                    } else {
                        card.innerHTML = `
                        <div class="flex h-full w-full flex-col items-center justify-center gap-1 px-1 text-center text-slate-400">
                            <i class="fa-regular fa-file-image text-lg"></i>
                            <span class="text-[8px] font-bold uppercase tracking-wide">${img.extension}</span>
                        </div>
                    `;
                    }

                    // Add direct delete button calling AJAX immediately
                    const delBtn = document.createElement('button');
                    delBtn.type = 'button';
                    delBtn.className =
                        'absolute right-1.5 top-1.5 w-7 h-7 bg-white/95 rounded-full flex items-center justify-center text-red-650 hover:bg-red-50 shadow-sm transition opacity-0 group-hover:opacity-100 focus:opacity-100 duration-150';
                    delBtn.innerHTML = '<i class="fa-solid fa-trash-can text-[10px]"></i>';
                    delBtn.onclick = () => deleteServerImage(folder, img.filename, card);

                    card.appendChild(delBtn);
                    grid.appendChild(card);
                });
            };

            const deleteServerImage = async (folder, filename, cardElement) => {
                if (!confirm('Are you sure you want to delete this image permanently?')) return;

                try {
                    const response = await fetch(
                        `/admin/ebay-products/${folder}/images/${encodeURIComponent(filename)}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        // Remove card from UI
                        cardElement.remove();

                        // Update stats
                        updateStatsDOM(data.stats);

                        // Update main list card
                        const originalCard = document.getElementById(`product-card-${folder}`);
                        if (originalCard && data.html) {
                            originalCard.outerHTML = data.html;
                        }

                        // Update count label in modal
                        const countText = document.getElementById('edit-images-count-text');
                        const totalCurrent = document.querySelectorAll('#edit-previews-grid > div').length;
                        countText.textContent = `${totalCurrent} images`;
                    } else {
                        alert(data.message || 'Failed to delete image.');
                    }
                } catch (err) {
                    alert('An error occurred while deleting the image.');
                }
            };

            // ==========================================
            //         CRUD AJAX OPERATIONS
            // ==========================================
            window.submitAddProduct = async (event) => {
                event.preventDefault();

                const btn = document.getElementById('btn-add-product-submit');
                btn.disabled = true;
                btn.querySelector('span').textContent = 'Creating folder...';

                const form = event.target;
                const formData = new FormData(form);

                // Append all queued images as files
                tempImageIds.add.forEach(item => {
                    formData.append('images[]', item.file);
                });

                try {
                    const response = await fetch(urls.store, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        closeAddProductModal();

                        // Prepends new card to grid list instantly!
                        const grid = document.getElementById('product-grid');
                        const emptyState = grid.querySelector('.col-span-1.md\\:col-span-2');
                        if (emptyState) emptyState.remove();

                        grid.insertAdjacentHTML('afterbegin', data.html);

                        // Update page stats
                        updateStatsDOM(data.stats);
                    } else {
                        alert(data.message || 'Failed to create product folder.');
                    }
                } catch (err) {
                    alert('An error occurred during product folder creation.');
                } finally {
                    btn.disabled = false;
                    btn.querySelector('span').textContent = 'Create Folder';
                }
            };

            window.submitEditProduct = async (event) => {
                event.preventDefault();

                const btn = document.getElementById('btn-edit-product-submit');
                btn.disabled = true;
                btn.querySelector('span').textContent = 'Saving changes...';

                const form = event.target;
                const folder = document.getElementById('edit_folder').value;
                const formData = new FormData(form);

                try {
                    // If there are background uploaded images, send them along to append
                    if (tempImageIds.edit.length > 0) {
                        const appendFormData = new FormData();
                        tempImageIds.edit.forEach(item => {
                            appendFormData.append('images[]', item.file);
                        });

                        // Send to the images append endpoint first
                        const appendResponse = await fetch(`/admin/ebay-products/${folder}/images`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: appendFormData
                        });

                        if (!appendResponse.ok) {
                            throw new Error('Failed to attach new images.');
                        }
                    }

                    // Update fields
                    const response = await fetch(`/admin/ebay-products/${folder}`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        closeEditProductModal();

                        // Replace the card in the list instantly!
                        const card = document.getElementById(`product-card-${folder}`);
                        if (card && data.html) {
                            card.outerHTML = data.html;
                        }

                        // Update stats
                        updateStatsDOM(data.stats);
                    } else {
                        alert(data.message || 'Failed to update folder details.');
                    }
                } catch (err) {
                    alert(err.message || 'An error occurred during updating.');
                } finally {
                    btn.disabled = false;
                    btn.querySelector('span').textContent = 'Save Changes';
                }
            };

            window.toggleProductMark = async (folder) => {
                try {
                    const response = await fetch(`/admin/ebay-products/${folder}/toggle-mark`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-HTTP-Method-Override': 'PATCH',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        // Replace updated card instantly!
                        const card = document.getElementById(`product-card-${folder}`);
                        if (card && data.html) {
                            card.outerHTML = data.html;
                        }

                        // Update stats
                        updateStatsDOM(data.stats);
                    } else {
                        alert('Failed to update product status.');
                    }
                } catch (err) {
                    alert('An error occurred while toggling the Done state.');
                }
            };

            window.deleteProductFolder = async (folder) => {
                if (!confirm(
                        'Are you sure you want to delete this folder and all images permanently? This action is irreversible.'
                    )) return;

                try {
                    const response = await fetch(`/admin/ebay-products/${folder}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-HTTP-Method-Override': 'DELETE',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        // Remove card from DOM
                        const card = document.getElementById(`product-card-${folder}`);
                        if (card) card.remove();

                        // Update stats
                        updateStatsDOM(data.stats);

                        // Show empty state if no folders left
                        const grid = document.getElementById('product-grid');
                        if (grid.querySelectorAll('.product-card').length === 0) {
                            grid.innerHTML = `
                            <div class="col-span-1 md:col-span-2 rounded-2xl border border-dashed border-slate-200 bg-white px-6 py-16 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-55 shadow-sm text-slate-450 border border-slate-100">
                                    <i class="fa-regular fa-folder-open text-2xl"></i>
                                </div>
                                <p class="mt-4 text-sm font-bold text-slate-800">No product folders found</p>
                                <p class="mt-1 text-xs text-slate-400 font-medium">Create a new Ebay Product folder or adjust your search filters.</p>
                            </div>
                        `;
                        }
                    } else {
                        alert(data.message || 'Failed to delete product folder.');
                    }
                } catch (err) {
                    alert('An error occurred during deletion.');
                }
            };

            window.markProductSold = async (folder) => {
                if (!confirm('Mark this item as sold? The inventory quantity will be set to 0.')) return;

                try {
                    const response = await fetch(`/admin/ebay-products/${encodeURIComponent(folder)}/mark-sold`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        const card = document.getElementById(`product-card-${folder}`);
                        if (card && data.html) {
                            card.outerHTML = data.html;
                        }
                        updateStatsDOM(data.stats);
                        window.showNotification(data.message || 'Item marked as sold.', 'success');
                    } else {
                        window.showNotification(data.message || 'Failed to mark as sold.', 'error');
                    }
                } catch (err) {
                    window.showNotification('An error occurred while marking as sold.', 'error');
                }
            };

            window.deleteInventoryRecord = async (folder) => {
                if (!confirm('Delete the linked inventory record? This cannot be undone. The product folder will remain.')) return;

                try {
                    const response = await fetch(`/admin/ebay-products/${encodeURIComponent(folder)}/inventory`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-HTTP-Method-Override': 'DELETE',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        const card = document.getElementById(`product-card-${folder}`);
                        if (card && data.html) {
                            card.outerHTML = data.html;
                        }
                        updateStatsDOM(data.stats);
                        window.showNotification(data.message || 'Inventory record deleted.', 'success');
                    } else {
                        window.showNotification(data.message || 'Failed to delete inventory record.', 'error');
                    }
                } catch (err) {
                    window.showNotification('An error occurred during deletion.', 'error');
                }
            };

            window.downloadProductImages = (encodedImages) => {
                try {
                    const list = JSON.parse(atob(encodedImages));
                    list.forEach((img, index) => {
                        setTimeout(() => {
                            const a = document.createElement('a');
                            a.href = img.url;
                            a.download = img.filename;
                            document.body.appendChild(a);
                            a.click();
                            a.remove();
                        }, index * 180);
                    });
                } catch (e) {
                    console.error(e);
                }
            };

            // DOM helper to update stats layout
            const updateStatsDOM = (stats) => {
                if (!stats) return;
                document.getElementById('stat-folders').textContent = stats.folders;
                document.getElementById('stat-images').textContent = stats.images;
                document.getElementById('stat-quantity').textContent = stats.quantity;
                document.getElementById('stat-marked').textContent = stats.marked;
            };

            // ==========================================
            //         LIVE FILTRATION SEARCH
            // ==========================================
            const triggerSearchFilters = () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(performLiveSearch, 220);
            };

            window.toggleCustomDateInputs = (val) => {
                const container = document.getElementById('custom-date-container');
                if (container) {
                    if (val === 'custom') {
                        container.classList.remove('hidden');
                        container.classList.add('flex');
                    } else {
                        container.classList.add('hidden');
                        container.classList.remove('flex');
                        const sInput = document.getElementById('filter-start-date');
                        const eInput = document.getElementById('filter-end-date');
                        if (sInput) sInput.value = '';
                        if (eInput) eInput.value = '';
                    }
                }
            };

            window.loadProductsFromUrl = async (requestUrl, pushState = true) => {
                if (pushState) {
                    window.history.pushState({}, '', requestUrl);
                } else {
                    window.history.replaceState({}, '', requestUrl);
                }

                try {
                    const response = await fetch(requestUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (response.ok && data.html) {
                        document.getElementById('product-grid').innerHTML = data.html;
                        const pagContainer = document.getElementById('pagination-container');
                        if (pagContainer) {
                            pagContainer.innerHTML = data.pagination_html || '';
                        }
                        updateStatsDOM(data.stats);
                        if (window.restoreBlinkingCards) window.restoreBlinkingCards();
                    }
                } catch (err) {
                    console.error('Request failed', err);
                }
            };

            const performLiveSearch = async () => {
                const form = document.getElementById('filters-form');
                const search = document.getElementById('filter-search').value;
                const status = document.getElementById('filter-status').value;
                const folderType = document.getElementById('filter-folder-type').value;
                const user = document.getElementById('filter-user')?.value || '';
                const dateRange = document.getElementById('filter-date-range')?.value || '';
                const startDate = document.getElementById('filter-start-date')?.value || '';
                const endDate = document.getElementById('filter-end-date')?.value || '';

                // Manage Clear Button Visibility
                const hasActiveFilters = (search.trim() !== '' || status !== '' || folderType !== '' || user !== '' || dateRange !== '' || startDate !== '' || endDate !== '');
                document.getElementById('btn-clear-filters').classList.toggle('hidden', !hasActiveFilters);

                // Construct queries URL (reset page on filter changes)
                const formData = new FormData(form);
                formData.delete('page');
                const queryParams = new URLSearchParams(formData).toString();
                const requestUrl = `${urls.index}?${queryParams}`;

                await window.loadProductsFromUrl(requestUrl, false);
            };

            // AJAX Pagination link handler
            document.addEventListener('click', (e) => {
                const paginationLink = e.target.closest('#pagination-container a');
                if (paginationLink && paginationLink.href) {
                    e.preventDefault();
                    window.loadProductsFromUrl(paginationLink.href, true);
                    document.getElementById('product-grid')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });

            // Browser back/forward navigation support
            window.addEventListener('popstate', () => {
                window.loadProductsFromUrl(window.location.href, false);
            });

            window.clearSearchFilters = () => {
                document.getElementById('filter-search').value = '';
                document.getElementById('filter-status').value = '';
                document.getElementById('filter-folder-type').value = '';
                if (document.getElementById('filter-user')) {
                    document.getElementById('filter-user').value = '';
                }
                if (document.getElementById('filter-date-range')) {
                    document.getElementById('filter-date-range').value = '';
                }
                if (document.getElementById('filter-start-date')) {
                    document.getElementById('filter-start-date').value = '';
                }
                if (document.getElementById('filter-end-date')) {
                    document.getElementById('filter-end-date').value = '';
                }
                window.toggleCustomDateInputs('');
                performLiveSearch();
            };

            // Bind filter event listeners
            document.getElementById('filter-search')?.addEventListener('input', triggerSearchFilters);
            document.getElementById('filter-status')?.addEventListener('change', performLiveSearch);
            document.getElementById('filter-folder-type')?.addEventListener('change', performLiveSearch);
            document.getElementById('filter-user')?.addEventListener('change', performLiveSearch);
            document.getElementById('filter-date-range')?.addEventListener('change', performLiveSearch);
            document.getElementById('filter-start-date')?.addEventListener('change', performLiveSearch);
            document.getElementById('filter-end-date')?.addEventListener('change', performLiveSearch);

            // ==========================================
            //         IMAGE SEARCH MODAL LOGIC
            // ==========================================
            let searchSelectedFile = null;

            window.openImageSearchModal = () => {
                document.getElementById('imageSearchModal')?.classList.remove('hidden');
            };

            window.closeImageSearchModal = () => {
                document.getElementById('imageSearchModal')?.classList.add('hidden');
            };

            window.triggerSearchImagePicker = () => {
                document.getElementById('search-image-file-input')?.click();
            };

            window.handleSearchImageSelect = (input) => {
                if (input.files && input.files[0]) {
                    setSearchFile(input.files[0]);
                }
            };

            window.handleSearchDragOver = (e) => {
                e.preventDefault();
                e.stopPropagation();
                document.getElementById('image-dropzone')?.classList.add('border-indigo-500', 'bg-indigo-50/50');
            };

            window.handleSearchDragLeave = (e) => {
                e.preventDefault();
                e.stopPropagation();
                document.getElementById('image-dropzone')?.classList.remove('border-indigo-500', 'bg-indigo-50/50');
            };

            window.handleSearchDrop = (e) => {
                e.preventDefault();
                e.stopPropagation();
                document.getElementById('image-dropzone')?.classList.remove('border-indigo-500', 'bg-indigo-50/50');
                if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                    setSearchFile(e.dataTransfer.files[0]);
                }
            };

            const setSearchFile = (file) => {
                if (!file.type.startsWith('image/')) {
                    if (window.showNotification) window.showNotification('Please select a valid image file', 'error');
                    return;
                }
                searchSelectedFile = file;

                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = document.getElementById('search-image-preview-img');
                    const name = document.getElementById('search-image-preview-name');
                    const size = document.getElementById('search-image-preview-size');
                    const container = document.getElementById('search-image-preview-container');
                    const btn = document.getElementById('btn-submit-image-search');

                    if (img) img.src = e.target.result;
                    if (name) name.textContent = file.name;
                    if (size) size.textContent = (file.size / 1024).toFixed(1) + ' KB';
                    if (container) container.classList.remove('hidden');
                    if (btn) btn.disabled = false;
                };
                reader.readAsDataURL(file);
            };

            window.removeSearchImagePreview = () => {
                searchSelectedFile = null;
                const fileInput = document.getElementById('search-image-file-input');
                const img = document.getElementById('search-image-preview-img');
                const container = document.getElementById('search-image-preview-container');
                const btn = document.getElementById('btn-submit-image-search');

                if (fileInput) fileInput.value = '';
                if (img) img.src = '';
                if (container) container.classList.add('hidden');
                if (btn) btn.disabled = true;
            };

            window.submitImageSearch = async () => {
                if (!searchSelectedFile) return;

                const submitBtn = document.getElementById('btn-submit-image-search');
                const submitBtnText = document.getElementById('btn-submit-image-search-text');
                if (submitBtn) submitBtn.disabled = true;
                if (submitBtnText) submitBtnText.textContent = 'Searching...';

                const formData = new FormData();
                formData.append('image', searchSelectedFile);

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                try {
                    const response = await fetch('{{ route("admin.ebay-products.image-search") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok && data.status === 'success') {
                        window.closeImageSearchModal();

                        // Update Grid & Stats & Pagination
                        const grid = document.getElementById('product-grid');
                        if (grid) grid.innerHTML = data.html;
                        const pagContainer = document.getElementById('pagination-container');
                        if (pagContainer) pagContainer.innerHTML = data.pagination_html || '';
                        if (data.stats && window.updateStatsDOM) {
                            window.updateStatsDOM(data.stats);
                        }

                        // Show search banner
                        const banner = document.getElementById('image-search-banner');
                        const previewImg = document.getElementById('banner-preview-img');
                        const matchCount = document.getElementById('image-search-match-count');

                        if (banner && previewImg && matchCount) {
                            previewImg.src = URL.createObjectURL(searchSelectedFile);
                            matchCount.textContent = `${data.count} matching product(s) found`;
                            banner.classList.remove('hidden');
                        }

                        if (window.showNotification) {
                            window.showNotification(`Image search completed: ${data.count} match(es) found`, 'success');
                        }
                    } else {
                        if (window.showNotification) {
                            window.showNotification(data.message || 'Image search failed', 'error');
                        }
                    }
                } catch (err) {
                    console.error("Image search request error:", err);
                    if (window.showNotification) window.showNotification('Error performing image search', 'error');
                } finally {
                    if (submitBtn) submitBtn.disabled = false;
                    if (submitBtnText) submitBtnText.textContent = 'Search Matches';
                }
            };

            window.clearImageSearch = () => {
                const banner = document.getElementById('image-search-banner');
                if (banner) banner.classList.add('hidden');
                window.removeSearchImagePreview();

                if (window.clearSearchFilters) {
                    window.clearSearchFilters();
                } else {
                    window.location.reload();
                }
            };

        })();
    </script>
@endpush
