@extends('ebay.layout')

@section('title', 'eBay API Products Fetch')
@section('page-title', 'eBay Live API Products')

@section('content')
<div class="space-y-6">

    <!-- Navigation Tabs & Header -->
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    @if(!empty($accountConnected))
                        @if(!empty($authenticatedUser))
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">
                                <i class="fa-solid fa-circle-check mr-1.5"></i> Connected: {{ $authenticatedUser }}
                            </span>
                            @if(!empty($expectedSeller) && strtolower($authenticatedUser) !== strtolower($expectedSeller))
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-900 border border-amber-300">
                                    <i class="fa-solid fa-triangle-exclamation mr-1.5 text-amber-600"></i> Mismatch: Expected "{{ $expectedSeller }}"
                                </span>
                            @endif
                        @else
                            <span class="inline-flex items-center rounded-full bg-slate-200 px-3 py-1 text-xs font-bold text-slate-800">
                                <i class="fa-solid fa-circle-question mr-1.5 text-slate-500"></i> Connected (Identity Unverified)
                            </span>
                        @endif
                    @else
                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">
                            <i class="fa-solid fa-triangle-exclamation mr-1.5"></i> Account Not Connected
                        </span>
                    @endif
                    <span class="text-xs font-semibold text-slate-500">OAuth Grant: <code class="rounded bg-slate-100 px-1.5 py-0.5 text-slate-800">{{ $tokenType ?? 'client_credentials' }}</code></span>
                </div>
                <h2 class="mt-1 text-2xl font-extrabold text-slate-900 sm:text-3xl">eBay API Fetched Listings</h2>
                <p class="mt-1 text-sm text-slate-600">Browse real-time product data, prices, images, and inventory fetched directly from your eBay seller account.</p>
            </div>

            <!-- Header Action Buttons -->
            <div class="flex flex-wrap items-center gap-2 self-start md:self-auto">
                <a href="{{ url('/ebay/login') }}" class="rounded-xl bg-blue-600 px-4 py-2 text-xs font-extrabold text-white shadow-sm hover:bg-blue-700 transition-all flex items-center gap-1.5" title="Sign in with seller account: {{ $expectedSeller ?? 'systematictrade' }}">
                    <i class="fa-solid fa-plug"></i> {{ !empty($accountConnected) ? 'Reconnect (' . ($expectedSeller ?? 'systematictrade') . ')' : 'Connect ' . ($expectedSeller ?? 'systematictrade') . ' Account' }}
                </a>

                <div class="flex items-center gap-2 rounded-xl bg-slate-100 p-1.5">
                    <a href="{{ url('/ebay/products-detail-hidden') }}" class="rounded-lg px-3.5 py-2 text-xs font-bold text-slate-600 hover:bg-white hover:text-slate-900 transition-all">
                        <i class="fa-solid fa-folder-open mr-1.5"></i> Local Disk Products
                    </a>
                    <a href="{{ url('/ebay/products') }}" class="rounded-lg bg-white px-3.5 py-2 text-xs font-extrabold text-blue-700 shadow-sm">
                        <i class="fa-solid fa-api mr-1.5"></i> Connected API Products
                    </a>
                </div>
            </div>
        </div>

        @if(!empty($accountConnected) && !empty($authenticatedUser) && !empty($expectedSeller) && strtolower($authenticatedUser) !== strtolower($expectedSeller))
            <div class="mt-5 rounded-2xl border border-amber-300 bg-amber-50/95 p-4 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-start gap-2.5">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-extrabold text-amber-950">eBay Account Mismatch Detected</h4>
                        <p class="mt-0.5 text-xs text-amber-900 leading-relaxed">
                            You are authenticated as <strong>{{ $authenticatedUser }}</strong>, but store listings belong to <strong>{{ $expectedSeller }}</strong>. Active listings will not load until you sign in with <strong>{{ $expectedSeller }}</strong>.
                        </p>
                    </div>
                </div>
                <a href="{{ url('/ebay/login') }}" class="rounded-xl bg-amber-600 px-4 py-2 text-xs font-extrabold text-white shadow-sm hover:bg-amber-700 whitespace-nowrap transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i> Sign In as {{ $expectedSeller }}
                </a>
            </div>
        @endif

        @if(empty($accountConnected))
            <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50/90 p-4 shadow-sm">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="space-y-1">
                        <h4 class="text-sm font-extrabold text-amber-950 flex items-center gap-2">
                            <i class="fa-solid fa-plug-circle-exclamation text-amber-600 text-base"></i>
                            Connect Your eBay Seller Account ({{ $expectedSeller ?? 'systematictrade' }})
                        </h4>
                        <p class="text-xs text-amber-900">
                            Sign in with your <strong>{{ $expectedSeller ?? 'systematictrade' }}</strong> eBay account to display live inventory products.
                        </p>
                    </div>
                    <a href="{{ url('/ebay/login') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-amber-600 px-4 py-2 text-xs font-extrabold text-white shadow-sm hover:bg-amber-700 transition-all whitespace-nowrap">
                        <i class="fa-solid fa-right-to-bracket"></i> Sign In to eBay
                    </a>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50/90 p-3.5 text-xs font-semibold text-emerald-900 flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('warning'))
            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50/90 p-3.5 text-xs font-semibold text-amber-900 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-amber-600 text-sm"></i>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        @if($notice)
            <div class="mt-4 rounded-xl border border-blue-200 bg-blue-50/80 p-3.5 text-xs font-semibold text-blue-900 flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-blue-600 text-sm"></i>
                <span>{{ $notice }}</span>
            </div>
        @endif

        @if($error || session('error'))
            <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3.5 text-xs font-semibold text-red-900 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-red-600 text-sm"></i>
                <span>{{ $error ?: session('error') }}</span>
            </div>
        @endif
    </div>

    <!-- API Live Search Bar -->
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <form method="GET" action="{{ url('/ebay/products') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative flex-1">
                <input type="text" name="q" value="{{ $query }}" placeholder="Search live eBay items (e.g. laptop, monitor, thinkpad)..." 
                    class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-9 pr-3 text-sm text-slate-900 placeholder-slate-400 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-sm text-slate-400"></i>
            </div>

            <div class="w-full sm:w-36">
                <select name="limit" class="w-full rounded-xl border border-slate-300 bg-white py-2.5 px-3 text-sm text-slate-900 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    <option value="12" {{ $limit == 12 ? 'selected' : '' }}>12 Items</option>
                    <option value="24" {{ $limit == 24 ? 'selected' : '' }}>24 Items</option>
                    <option value="48" {{ $limit == 48 ? 'selected' : '' }}>48 Items</option>
                    <option value="96" {{ $limit == 96 ? 'selected' : '' }}>96 Items</option>
                </select>
            </div>

            <button type="submit" class="rounded-xl bg-blue-600 py-2.5 px-5 text-sm font-bold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-bolt"></i> Fetch from API
            </button>
        </form>
    </div>

    <!-- Items Counter -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 px-1">
        <p class="text-sm font-bold text-slate-700">
            Fetched <span class="text-blue-600 font-extrabold">{{ count($items) }}</span> eBay {{ Str::plural('item', count($items)) }}
            @if($query !== '')
                matching <span class="rounded bg-slate-200/70 px-1.5 py-0.5 font-mono text-xs text-slate-900">"{{ $query }}"</span>
            @endif
        </p>
        <a href="{{ url('/ebay/products?q=' . urlencode($query) . '&format=json') }}" target="_blank" class="text-xs font-bold text-blue-600 hover:text-blue-800 inline-flex items-center gap-1">
            <i class="fa-solid fa-code"></i> View Raw JSON Payload
        </a>
    </div>

    <!-- Items Grid -->
    @if(empty($items))
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                <i class="fa-solid fa-cloud-slash text-2xl"></i>
            </div>
            <h3 class="mt-4 text-lg font-bold text-slate-900">No Items Found</h3>
            <p class="mt-1 text-sm text-slate-500">eBay API returned zero items matching search term "{{ $query }}".</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($items as $idx => $item)
                @php
                    $thumb = $item['thumbnail'] ?? (!empty($item['images']) ? $item['images'][0]['imageUrl'] : null);
                    $priceVal = is_array($item['price']) ? ($item['price']['value'] ?? 'N/A') : $item['price'];
                    $currency = is_array($item['price']) ? ($item['price']['currency'] ?? 'USD') : 'USD';
                @endphp

                <div class="flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md">
                    
                    <!-- Image Display Box -->
                    <div class="relative aspect-4/3 w-full overflow-hidden bg-slate-900/5 p-3 flex items-center justify-center">
                        @if($thumb)
                            <img src="{{ $thumb }}" alt="{{ $item['title'] }}" 
                                onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=400&q=80';"
                                class="h-full w-full object-contain transition-transform duration-300 hover:scale-105 cursor-pointer"
                                onclick="openApiLightbox(this.src, '{{ addslashes($item['title']) }}')">
                        @else
                            <div class="flex flex-col items-center justify-center text-slate-400 p-4 text-center">
                                <i class="fa-solid fa-image text-3xl mb-1 text-slate-300"></i>
                                <span class="text-xs font-semibold text-slate-500">No Image in Sandbox</span>
                            </div>
                        @endif

                        <!-- Price Tag Badge -->
                        <div class="absolute top-2.5 left-2.5 rounded-lg bg-slate-900/90 px-2.5 py-1 text-xs font-extrabold text-emerald-400 shadow backdrop-blur-sm">
                            @if(is_numeric($priceVal))
                                ${{ number_format((float)$priceVal, 2) }} {{ $currency }}
                            @else
                                {{ $priceVal }}
                            @endif
                        </div>

                        <!-- Condition Badge -->
                        @if(!empty($item['condition']))
                            <div class="absolute top-2.5 right-2.5 rounded-lg bg-white/90 px-2 py-0.5 text-[11px] font-bold text-slate-800 shadow border border-slate-200">
                                {{ $item['condition'] }}
                            </div>
                        @endif
                    </div>

                    <!-- Details Box -->
                    <div class="flex flex-1 flex-col justify-between p-4">
                        <div>
                            <!-- Title -->
                            <h3 class="line-clamp-2 text-sm font-bold text-slate-900 leading-snug hover:text-blue-600 transition-colors">
                                {{ $item['title'] }}
                            </h3>

                            <!-- Category badges -->
                            @if(!empty($item['categories']))
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @foreach(array_slice($item['categories'], 0, 2) as $cat)
                                        <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700">
                                            <i class="fa-solid fa-tag text-[9px] mr-1 text-slate-400"></i>{{ $cat['categoryName'] ?? 'Category' }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Meta Info Grid -->
                            <div class="mt-3 space-y-1.5 text-xs text-slate-600 border-t border-slate-100 pt-2.5">
                                @if(!empty($item['itemLocation']['country']))
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-400 text-[11px]">Location:</span>
                                        <span class="font-semibold text-slate-800">{{ $item['itemLocation']['country'] }}</span>
                                    </div>
                                @endif

                                @if(!empty($item['seller']['feedbackScore']))
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-400 text-[11px]">Seller Rating:</span>
                                        <span class="font-semibold text-slate-800">
                                            <i class="fa-solid fa-star text-amber-400 mr-1"></i>{{ number_format($item['seller']['feedbackScore']) }}
                                        </span>
                                    </div>
                                @endif

                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400 text-[11px]">Item ID:</span>
                                    <code class="text-[10px] font-mono text-slate-700 bg-slate-100 px-1 rounded truncate max-w-[120px]">{{ $item['itemId'] }}</code>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-2">
                            @if(!empty($item['itemWebUrl']))
                                <a href="{{ $item['itemWebUrl'] }}" target="_blank" rel="noopener noreferrer" 
                                    class="flex-1 rounded-xl bg-amber-50 py-2 px-3 text-center text-xs font-bold text-amber-900 border border-amber-200 hover:bg-amber-100 transition-colors">
                                    View on eBay <i class="fa-solid fa-arrow-up-right-from-square ml-1 text-[10px]"></i>
                                </a>
                            @endif

                            <button type="button" onclick="inspectItemJson({{ $idx }})" 
                                class="rounded-xl border border-slate-300 bg-slate-50 py-2 px-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-100" title="Inspect Raw API Object">
                                <i class="fa-solid fa-code"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

<!-- Fullscreen Image Lightbox Modal -->
<div id="api-image-lightbox-modal" class="fixed inset-0 z-50 hidden flex-col items-center justify-center bg-slate-950/90 p-4 backdrop-blur-md transition-all">
    <button type="button" onclick="closeApiLightbox()" class="absolute right-4 top-4 z-10 rounded-full bg-white/10 p-3 text-white hover:bg-white/20 focus:outline-none transition-colors">
        <i class="fa-solid fa-xmark text-xl"></i>
    </button>
    <div class="relative flex max-h-[85vh] max-w-[90vw] flex-col items-center justify-center">
        <img id="api-lightbox-image" src="" alt="Preview" class="max-h-[80vh] max-w-[90vw] rounded-xl object-contain shadow-2xl">
        <p id="api-lightbox-caption" class="mt-3 text-center text-sm font-semibold text-slate-200"></p>
    </div>
</div>

<!-- Raw JSON Inspector Modal -->
<div id="json-inspector-modal" class="fixed inset-0 z-50 hidden flex-col items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm transition-all">
    <div class="relative flex h-[80vh] w-full max-w-3xl flex-col rounded-2xl border border-slate-700 bg-slate-900 text-slate-100 shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-800 px-5 py-3.5 bg-slate-950">
            <h3 class="text-sm font-bold text-slate-200"><i class="fa-solid fa-code text-blue-400 mr-2"></i> eBay API Item JSON Inspection</h3>
            <button type="button" onclick="closeJsonInspector()" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <div class="flex-1 overflow-auto p-4 font-mono text-xs leading-relaxed text-emerald-400 bg-slate-950">
            <pre id="json-inspector-code"></pre>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const apiItemsData = @json($items);

    function openApiLightbox(imgUrl, caption) {
        const modal = document.getElementById('api-image-lightbox-modal');
        document.getElementById('api-lightbox-image').src = imgUrl;
        document.getElementById('api-lightbox-caption').textContent = caption;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeApiLightbox() {
        const modal = document.getElementById('api-image-lightbox-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function inspectItemJson(idx) {
        if (apiItemsData[idx]) {
            const codeElem = document.getElementById('json-inspector-code');
            codeElem.textContent = JSON.stringify(apiItemsData[idx], null, 2);
            const modal = document.getElementById('json-inspector-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeJsonInspector() {
        const modal = document.getElementById('json-inspector-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeApiLightbox();
            closeJsonInspector();
        }
    });
</script>
@endsection
