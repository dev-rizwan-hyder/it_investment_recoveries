@php
    $imageUrl = fn ($filename) => asset('ebay-product/' . rawurlencode($product['folder']) . '/' . rawurlencode($filename));
    $previewableExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    $formatUrl = function ($url) {
        if (empty($url)) return '';
        $url = trim($url);
        if (!preg_match('/^https?:\/\//i', $url)) {
            return 'https://' . $url;
        }
        return $url;
    };
    
    $downloadImages = collect($product['images'])
        ->map(fn ($image) => [
            'url' => $imageUrl($image['filename']),
            'filename' => $image['filename'],
        ])
        ->values();

    $editProductData = collect([
        'folder' => $product['folder'],
        'folder_name' => $product['folder_name'],
        'condition' => $product['condition'],
        'location' => $product['location'],
        'quantity' => $product['quantity'],
        'notes' => $product['notes'],
        'folder_type' => $product['folder_type'],
        'marked' => $product['marked'],
        'user' => $product['user'] ?? '',
        'serial_number' => $product['serial_number'] ?? '',
        'weight' => $product['weight'] ?? '',
        'value' => $product['value'] ?? $product['estimated_value'] ?? '',
        'estimated_value' => $product['value'] ?? $product['estimated_value'] ?? '',
        'ebay_link' => $product['ebay_link'] ?? '',
        'website_link' => $product['website_link'] ?? '',
        'social_media_1' => $product['social_media_1'] ?? '',
        'social_media_2' => $product['social_media_2'] ?? '',
        'social_media_3' => $product['social_media_3'] ?? '',
        'images' => collect($product['images'])
            ->map(function ($image) use ($imageUrl, $previewableExtensions) {
                $filename = $image['filename'];
                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                return [
                    'filename' => $filename,
                    'original_name' => $image['original_name'] ?? $filename,
                    'url' => $imageUrl($filename),
                    'can_preview' => in_array($extension, $previewableExtensions, true),
                    'extension' => $extension ?: 'file',
                ];
            })
            ->values()
            ->all(),
    ])->toJson();

    $isBlinking     = !empty($product['blinking']);
    $markedStatus   = $product['marked'] ?? 'remaining';
    $inventoryId    = $product['inventory_id'] ?? null;
    $qty            = (int) ($product['quantity'] ?? 0);
    $allTransferred = $inventoryId && $qty === 0;

    $cardStyleClass = 'border-slate-200 bg-white hover:border-slate-300';
    if ($markedStatus === 'ebay' || $markedStatus === 'abid') {
        $cardStyleClass = 'border-amber-300 bg-amber-100/55 ring-1 ring-amber-100/30';
    } elseif ($markedStatus === 'denver' || $markedStatus === 'moses') {
        $cardStyleClass = 'border-blue-300 bg-blue-100/55 ring-1 ring-blue-100/30';
    } elseif ($markedStatus === 'both' || $markedStatus === true) {
        $cardStyleClass = 'border-emerald-350 bg-emerald-100/55 ring-1 ring-emerald-100/30';
    }
    if ($isBlinking) {
        $cardStyleClass .= ' blinking-red-card';
    }
@endphp

<div id="product-card-{{ $product['folder'] }}"
     data-folder="{{ $product['folder'] }}"
     class="product-card rounded-2xl border p-5 shadow-sm transition-all duration-300 hover:shadow-md {{ $cardStyleClass }}"
     @if($isBlinking) style="animation: blinkRedFolder 0.5s infinite ease-in-out !important; background-color: #dc2626 !important; border-color: #991b1b !important;" @endif>

    {{-- ── Header: title + badges only ── --}}
    <div class="min-w-0">
        <div class="flex flex-wrap items-center gap-2">
            <h3 class="min-w-0 break-words text-base font-extrabold text-slate-900 sm:text-lg tracking-tight">
                {{ $product['product_name'] }}
            </h3>

            @php
                $folderType = $product['folder_type'];
                $badgeColorClass = match($folderType) {
                    'Denver' => 'border-purple-200 bg-purple-50 text-purple-700',
                    'Both'   => 'border-indigo-200 bg-indigo-50 text-indigo-700',
                    default  => 'border-blue-200 bg-blue-50 text-blue-700',
                };
            @endphp
            <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-xs font-bold {{ $badgeColorClass }}">
                <i class="fa-regular fa-folder text-[10px]"></i>
                {{ $folderType }}
            </span>

            @if ($markedStatus === 'ebay' || $markedStatus === 'abid')
                <span class="inline-flex items-center gap-1 rounded-full border border-amber-250 bg-amber-100/70 px-2.5 py-0.5 text-xs font-bold text-amber-700 shadow-sm">
                    <i class="fa-solid fa-circle-check"></i> Abid Done
                </span>
            @elseif ($markedStatus === 'denver' || $markedStatus === 'moses')
                <span class="inline-flex items-center gap-1 rounded-full border border-blue-200 bg-blue-100/70 px-2.5 py-0.5 text-xs font-bold text-blue-700 shadow-sm">
                    <i class="fa-solid fa-circle-check"></i> Moses Done
                </span>
            @elseif ($markedStatus === 'both' || $markedStatus === true)
                <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-100/60 px-2.5 py-0.5 text-xs font-bold text-emerald-700 shadow-sm">
                    <i class="fa-solid fa-circle-check"></i> Both Done
                </span>
            @else
                <span class="inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-2.5 py-0.5 text-xs font-bold text-amber-700">
                    <i class="fa-solid fa-clock"></i> Remaining
                </span>
            @endif

            @if ($product['user'] ?? '')
                <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-700">
                    <i class="fa-solid fa-user"></i> {{ $product['user'] }}
                </span>
            @endif

            @if (isset($product['similarity_match']))
                <span class="inline-flex items-center gap-1 rounded-full border border-indigo-300 bg-indigo-100 px-2.5 py-0.5 text-xs font-black text-indigo-800 shadow-sm" title="Matched image: {{ $product['similarity_match']['filename'] }}">
                    <i class="fa-solid fa-camera text-indigo-600"></i> {{ $product['similarity_match']['score'] }}% Match
                </span>
            @endif

            @if (count($product['images']) === 0)
                <span class="inline-flex items-center gap-1 rounded-full border border-rose-200 bg-rose-50 px-2.5 py-0.5 text-xs font-bold text-rose-700">
                    <i class="fa-solid fa-image-slash"></i> No Images
                </span>
            @endif
        </div>

        <p class="mt-2 flex flex-wrap items-center gap-1.5 text-xs font-semibold text-slate-400">
            <i class="fa-regular fa-clock text-[10px]"></i>
            Added {{ $product['created_at_denver'] }}
        </p>
    </div>

    {{-- ── Thumbnail Gallery ── --}}
    <div class="mt-4 grid grid-cols-4 gap-2">
        @forelse (array_slice($product['images'], 0, 4) as $image)
            @php
                $url       = $imageUrl($image['filename']);
                $extension = strtolower(pathinfo($image['filename'], PATHINFO_EXTENSION));
                $canPreview = in_array($extension, $previewableExtensions, true);
            @endphp
            <a href="javascript:void(0)"
               onclick="openGallery({{ json_encode($downloadImages->pluck('url')->all()) }}, {{ $loop->index }})"
               class="relative block aspect-square overflow-hidden rounded-xl border border-slate-100 bg-slate-50 transition duration-200 hover:scale-[1.02] hover:shadow-sm cursor-pointer">
                @if ($canPreview)
                    <img src="{{ $url }}" alt="{{ $image['original_name'] }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full w-full flex-col items-center justify-center gap-1 px-1 text-center text-slate-400">
                        <i class="fa-regular fa-file-image text-lg"></i>
                        <span class="text-[8px] font-bold uppercase tracking-wide">{{ $extension ?: 'file' }}</span>
                    </div>
                @endif
            </a>
        @empty
            <div class="col-span-4 flex h-24 items-center justify-center rounded-xl border border-dashed border-slate-200 bg-slate-50/50 text-slate-400">
                <div class="text-center">
                    <i class="fa-regular fa-image text-xl mb-1 block"></i>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">No Images Uploaded</span>
                </div>
            </div>
        @endforelse
    </div>

    {{-- ── Metadata Grid ── --}}
    <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 border-t border-slate-100 pt-4 text-xs font-semibold text-slate-500">
        <div>
            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Condition</span>
            <span class="mt-1 block font-bold text-slate-700">{{ $product['condition'] }}</span>
        </div>
        <div>
            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Location</span>
            <span class="mt-1 block font-bold text-slate-700 truncate" title="{{ $product['location'] }}">{{ $product['location'] ?: '-' }}</span>
        </div>
        <div>
            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Quantity</span>
            <span class="mt-1 block font-bold text-slate-700">{{ $product['quantity'] }}</span>
        </div>
        <div>
            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Serial Number</span>
            <span class="mt-1 block font-bold text-slate-700 truncate" title="{{ $product['serial_number'] ?? '-' }}">{{ !empty($product['serial_number']) ? $product['serial_number'] : '-' }}</span>
        </div>
        <div>
            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Weight</span>
            <span class="mt-1 block font-bold text-slate-700">{{ !empty($product['weight']) ? $product['weight'] . ' lbs' : '-' }}</span>
        </div>
        <div>
            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Value ($)</span>
            <span class="mt-1 block font-bold text-slate-700">{{ (isset($product['value']) && $product['value'] !== '' && $product['value'] !== null) ? '$' . number_format((float)$product['value'], 2) : (isset($product['estimated_value']) && $product['estimated_value'] !== '' && $product['estimated_value'] !== null ? '$' . number_format((float)$product['estimated_value'], 2) : '-') }}</span>
        </div>
    </div>

    {{-- ── Notes ── --}}
    @if ($product['notes'])
        @php
            $notesBgClass = 'border-slate-100 bg-slate-50/70';
            if ($markedStatus === 'ebay' || $markedStatus === 'abid')         $notesBgClass = 'border-amber-100/50 bg-white/60';
            elseif ($markedStatus === 'denver' || $markedStatus === 'moses')  $notesBgClass = 'border-blue-100/50 bg-white/60';
            elseif ($markedStatus === 'both' || $markedStatus === true)       $notesBgClass = 'border-emerald-100/50 bg-white/60';
        @endphp
        <div class="mt-4 rounded-xl border p-3.5 text-xs font-medium leading-relaxed text-slate-600 flex items-start gap-2.5 {{ $notesBgClass }}">
            <i class="fa-solid fa-circle-info mt-0.5 text-indigo-500/80 flex-shrink-0 text-sm"></i>
            <div>
                <span class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Notes</span>
                <p class="font-medium leading-relaxed">{{ $product['notes'] }}</p>
            </div>
        </div>
    @endif

    {{-- ── Folder Links ── --}}
    @if (!empty($product['ebay_link']) || !empty($product['website_link']) || !empty($product['social_media_1']) || !empty($product['social_media_2']) || !empty($product['social_media_3']))
        @php
            $linksBorderClass = 'border-slate-200';
            $itemBgClass      = 'bg-slate-50/60 hover:bg-slate-100/80 hover:border-slate-300';
            if ($markedStatus === 'ebay' || $markedStatus === 'abid') {
                $linksBorderClass = 'border-amber-250';
                $itemBgClass      = 'bg-amber-50/40 hover:bg-amber-100/40 hover:border-amber-200';
            } elseif ($markedStatus === 'denver' || $markedStatus === 'moses') {
                $linksBorderClass = 'border-blue-200';
                $itemBgClass      = 'bg-blue-50/40 hover:bg-blue-100/40 hover:border-blue-200';
            } elseif ($markedStatus === 'both' || $markedStatus === true) {
                $linksBorderClass = 'border-emerald-200';
                $itemBgClass      = 'bg-emerald-50/40 hover:bg-emerald-100/40 hover:border-emerald-200';
            }
        @endphp
        <div class="mt-4 border-t border-dashed {{ $linksBorderClass }} pt-4">
            <span class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2.5">Folder Links</span>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @if (!empty($product['ebay_link']))
                    <a href="{{ $formatUrl($product['ebay_link']) }}" target="_blank"
                       class="group flex items-center justify-between p-2.5 px-3 rounded-xl border border-slate-200/80 {{ $itemBgClass }} transition-all duration-200 hover:shadow-sm">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform duration-200">
                                <i class="fa-brands fa-ebay text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[8px] font-black uppercase tracking-wider text-slate-400">eBay Store</span>
                                <span class="block text-xs font-bold text-slate-700 truncate max-w-[170px] sm:max-w-[200px]">{{ preg_replace('/^https?:\/\/(www\.)?/', '', $product['ebay_link']) }}</span>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-slate-400 group-hover:text-indigo-600 transition-colors"></i>
                    </a>
                @endif
                @if (!empty($product['website_link']))
                    <a href="{{ $formatUrl($product['website_link']) }}" target="_blank"
                       class="group flex items-center justify-between p-2.5 px-3 rounded-xl border border-slate-200/80 {{ $itemBgClass }} transition-all duration-200 hover:shadow-sm">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform duration-200">
                                <i class="fa-solid fa-globe text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[8px] font-black uppercase tracking-wider text-slate-400">Website URL</span>
                                <span class="block text-xs font-bold text-slate-700 truncate max-w-[170px] sm:max-w-[200px]">{{ preg_replace('/^https?:\/\/(www\.)?/', '', $product['website_link']) }}</span>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-slate-400 group-hover:text-indigo-600 transition-colors"></i>
                    </a>
                @endif
                @if (!empty($product['social_media_1']))
                    <a href="{{ $formatUrl($product['social_media_1']) }}" target="_blank"
                       class="group flex items-center justify-between p-2.5 px-3 rounded-xl border border-slate-200/80 {{ $itemBgClass }} transition-all duration-200 hover:shadow-sm">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform duration-200">
                                <i class="fa-solid fa-share-nodes text-xs"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[8px] font-black uppercase tracking-wider text-slate-400">Social Media 1</span>
                                <span class="block text-xs font-bold text-slate-700 truncate max-w-[170px] sm:max-w-[200px]">{{ preg_replace('/^https?:\/\/(www\.)?/', '', $product['social_media_1']) }}</span>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-slate-400 group-hover:text-indigo-600 transition-colors"></i>
                    </a>
                @endif
                @if (!empty($product['social_media_2']))
                    <a href="{{ $formatUrl($product['social_media_2']) }}" target="_blank"
                       class="group flex items-center justify-between p-2.5 px-3 rounded-xl border border-slate-200/80 {{ $itemBgClass }} transition-all duration-200 hover:shadow-sm">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-7 h-7 rounded-lg bg-pink-50 text-pink-600 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform duration-200">
                                <i class="fa-solid fa-share-nodes text-xs"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[8px] font-black uppercase tracking-wider text-slate-400">Social Media 2</span>
                                <span class="block text-xs font-bold text-slate-700 truncate max-w-[170px] sm:max-w-[200px]">{{ preg_replace('/^https?:\/\/(www\.)?/', '', $product['social_media_2']) }}</span>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-slate-400 group-hover:text-indigo-600 transition-colors"></i>
                    </a>
                @endif
                @if (!empty($product['social_media_3']))
                    <a href="{{ $formatUrl($product['social_media_3']) }}" target="_blank"
                       class="group flex items-center justify-between p-2.5 px-3 rounded-xl border border-slate-200/80 {{ $itemBgClass }} transition-all duration-200 hover:shadow-sm">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform duration-200">
                                <i class="fa-solid fa-share-nodes text-xs"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[8px] font-black uppercase tracking-wider text-slate-400">Social Media 3</span>
                                <span class="block text-xs font-bold text-slate-700 truncate max-w-[170px] sm:max-w-[200px]">{{ preg_replace('/^https?:\/\/(www\.)?/', '', $product['social_media_3']) }}</span>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-slate-400 group-hover:text-indigo-600 transition-colors"></i>
                    </a>
                @endif
            </div>
        </div>
    @endif

    {{-- ── Action Buttons (bottom of card) ── --}}
    @php
        $toggleBtnLabel = 'Mark as Done';
        $toggleBtnClass = 'border-slate-200 bg-white text-slate-500 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200';
        if ($markedStatus === 'ebay' || $markedStatus === 'abid') {
            $toggleBtnLabel = 'Mark Moses Done';
            $toggleBtnClass = 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100';
        } elseif ($markedStatus === 'denver' || $markedStatus === 'moses') {
            $toggleBtnLabel = 'Mark Both Done';
            $toggleBtnClass = 'border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100';
        } elseif ($markedStatus === 'both' || $markedStatus === true) {
            $toggleBtnLabel = 'Mark Remaining';
            $toggleBtnClass = 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100';
        }
    @endphp

    <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">

        {{-- Add to Inventory / All Transferred --}}
        @if ($allTransferred)
            <button type="button" disabled
                    class="h-8 px-3 inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-black uppercase tracking-wide cursor-not-allowed">
                <i class="fas fa-check-double text-[10px]"></i> All Transferred
            </button>
        @else
            <button type="button"
                    data-folder="{{ $product['folder'] }}"
                    onclick="openEbayToInventoryModal(this)"
                    class="h-8 px-3 inline-flex items-center gap-1.5 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 text-[11px] font-black uppercase tracking-wide hover:bg-blue-600 hover:text-white transition-all btn-add-inventory">
                <i class="fas fa-plus-circle text-[10px]"></i> + Inventory
            </button>
        @endif

        {{-- Sold --}}
        @if ($product['sold'] ?? false)
            <button type="button" disabled
                    class="h-8 px-3 inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-black uppercase tracking-wide cursor-not-allowed">
                <i class="fas fa-check-circle text-[10px]"></i> Sold
            </button>
        @else
            <button type="button"
                    title="{{ $inventoryId ? 'Mark as Sold' : 'Add to inventory first' }}"
                    onclick="{{ $inventoryId ? "markProductSold('{$product['folder']}')" : '' }}"
                    @if(!$inventoryId) disabled @endif
                    class="h-8 px-3 inline-flex items-center gap-1.5 rounded-lg border text-[11px] font-black uppercase tracking-wide transition-all
                        {{ $inventoryId
                            ? 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-500 hover:text-white'
                            : 'bg-slate-50 text-slate-300 border-slate-200 cursor-not-allowed' }}">
                <i class="fas fa-tag text-[10px]"></i> Sold
            </button>
        @endif

        {{-- spacer --}}
        <span class="flex-1"></span>

        {{-- Toggle Done --}}
        <button type="button"
                title="{{ $toggleBtnLabel }}"
                onclick="toggleProductMark('{{ $product['folder'] }}')"
                class="h-8 px-3 inline-flex items-center gap-1.5 rounded-lg border text-[11px] font-black uppercase tracking-wide transition-all {{ $toggleBtnClass }}">
            <i class="fa-solid fa-check-double text-[10px]"></i> {{ $toggleBtnLabel }}
        </button>

        {{-- Toggle Blinking Red Alert --}}
        <button type="button"
                title="{{ $isBlinking ? 'Remove Blinking Red Alert Style' : 'Toggle Blinking Red Alert Style' }}"
                onclick="toggleBlinkingRedCard(this, '{{ $product['folder'] }}')"
                class="btn-blink-toggle w-8 h-8 inline-flex items-center justify-center rounded-lg border transition-all shadow-sm {{ $isBlinking ? 'border-red-700 bg-red-600 text-white hover:bg-red-700 ring-2 ring-red-400' : 'border-red-200 text-red-500 bg-red-50/60 hover:bg-red-600 hover:text-white hover:border-red-600' }}">
            <i class="fa-solid fa-bell text-[11px] {{ $isBlinking ? 'animate-bounce' : '' }}"></i>
        </button>

        {{-- Edit --}}
        <button type="button" title="Edit"
                data-folder="{{ $product['folder'] }}"
                data-product-json="{{ base64_encode($editProductData) }}"
                onclick="openEditProductModal(this)"
                class="w-8 h-8 inline-flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all">
            <i class="fa-solid fa-pen-to-square text-[11px]"></i>
        </button>

        {{-- Download --}}
        <button type="button" title="Download images"
                onclick="downloadProductImages('{{ base64_encode($downloadImages->toJson()) }}')"
                class="w-8 h-8 inline-flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all disabled:opacity-40 disabled:pointer-events-none"
                {{ $downloadImages->isEmpty() ? 'disabled' : '' }}>
            <i class="fa-solid fa-download text-[11px]"></i>
        </button>

        {{-- Delete Inventory Record --}}
        @if ($inventoryId)
            <button type="button" title="Delete inventory record"
                    onclick="deleteInventoryRecord('{{ $product['folder'] }}')"
                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg border bg-rose-50 text-rose-500 border-rose-200 hover:bg-rose-500 hover:text-white transition-all">
                <i class="fa-solid fa-box-archive text-[11px]"></i>
            </button>
        @endif

        {{-- Delete Folder --}}
        <button type="button" title="Delete folder"
                onclick="deleteProductFolder('{{ $product['folder'] }}')"
                class="w-8 h-8 inline-flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all">
            <i class="fa-solid fa-trash-can text-[11px]"></i>
        </button>
    </div>

</div>
