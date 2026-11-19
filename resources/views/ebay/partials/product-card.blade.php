@php
    $imageUrl = fn ($filename) => asset('ebay/' . rawurlencode($product['folder']) . '/' . rawurlencode($filename));
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
    $uploadInputId = 'upload-images-' . md5($product['folder']);
    $editProduct = collect([
        'folder_name' => $product['folder_name'],
        'condition' => $product['condition'],
        'location' => $product['location'],
        'quantity' => $product['quantity'],
        'notes' => $product['notes'],
        'folder_type' => $product['folder_type'] ?? 'Ebay',
        'marked' => $product['marked'],
        'user' => $product['user'] ?? '',
        'ebay_link' => $product['ebay_link'] ?? '',
        'website_link' => $product['website_link'] ?? '',
        'social_media_1' => $product['social_media_1'] ?? '',
        'social_media_2' => $product['social_media_2'] ?? '',
        'social_media_3' => $product['social_media_3'] ?? '',
        'upload_url' => route('summary.ebay.images.store', $product['folder']),
        'images' => collect($product['images'])
            ->map(function ($image) use ($imageUrl, $previewableExtensions, $product) {
                $filename = $image['filename'];
                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                return [
                    'filename' => $filename,
                    'original_name' => $image['original_name'] ?? $filename,
                    'url' => $imageUrl($filename),
                    'can_preview' => in_array($extension, $previewableExtensions, true),
                    'extension' => $extension ?: 'file',
                    'delete_url' => route('summary.ebay.images.destroy', [
                        'folder' => $product['folder'],
                        'filename' => $filename,
                    ]),
                ];
            })
            ->values()
            ->all(),
    ])->toJson();
@endphp
@php
    $markedStatus = $product['marked'] ?? 'remaining';
    
    $cardStyleClass = 'border-slate-200 bg-white';
    if ($markedStatus === 'ebay' || $markedStatus === 'abid') {
        $cardStyleClass = 'border-amber-300 bg-amber-100/55 ring-1 ring-amber-100/30';
    } elseif ($markedStatus === 'denver' || $markedStatus === 'moses') {
        $cardStyleClass = 'border-blue-300 bg-blue-100/55 ring-1 ring-blue-100/30';
    } elseif ($markedStatus === 'both' || $markedStatus === true) {
        $cardStyleClass = 'border-emerald-355 bg-emerald-100/55 ring-1 ring-emerald-100/30';
    }
@endphp
<div class="rounded-lg border p-3 shadow-sm transition sm:p-4 {{ $cardStyleClass }}">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <h3 class="min-w-0 break-words text-base font-extrabold text-slate-950 sm:text-lg">{{ $product['product_name'] }}</h3>
                @php
                    $folderType = $product['folder_type'] ?? 'Ebay';
                    $badgeColorClass = match($folderType) {
                        'Denver' => 'border-purple-200 bg-purple-50 text-purple-700',
                        'Both' => 'border-indigo-200 bg-indigo-50 text-indigo-700',
                        default => 'border-blue-200 bg-blue-50 text-blue-700',
                    };
                @endphp
                <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-bold {{ $badgeColorClass }}">
                    <i class="fa-regular fa-folder text-[10px]"></i>
                    {{ $folderType }}
                </span>
                @if ($markedStatus === 'ebay' || $markedStatus === 'abid')
                    <span class="inline-flex items-center gap-1 rounded-full border border-amber-250 bg-amber-100/70 px-2.5 py-1 text-xs font-bold text-amber-700 shadow-sm">
                        <i class="fa-solid fa-check-double"></i>
                        Abid Done
                    </span>
                @elseif ($markedStatus === 'denver' || $markedStatus === 'moses')
                    <span class="inline-flex items-center gap-1 rounded-full border border-blue-200 bg-blue-100/70 px-2.5 py-1 text-xs font-bold text-blue-700 shadow-sm">
                        <i class="fa-solid fa-check-double"></i>
                        Moses Done
                    </span>
                @elseif ($markedStatus === 'both' || $markedStatus === true)
                    <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-white px-2.5 py-1 text-xs font-bold text-emerald-700 shadow-sm">
                        <i class="fa-solid fa-check-double"></i>
                        Both Done
                    </span>
                @endif

                @if ($product['user'] ?? '')
                    <span class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700 shadow-sm">
                        <i class="fa-solid fa-user"></i>
                        {{ $product['user'] }}
                    </span>
                @endif
                @if (count($product['images']) === 0)
                    <span class="inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700">
                        <i class="fa-solid fa-clock"></i>
                        Upload pending
                    </span>
                @endif
                @if (isset($product['similarity_match']))
                    <span class="inline-flex items-center gap-1 rounded-full border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700">
                        <i class="fa-regular fa-image"></i>
                        {{ $product['similarity_match']['score'] }}% match
                    </span>
                @endif
            </div>
            @if (isset($product['similarity_match']))
                <p class="mt-1 truncate text-xs font-semibold text-blue-600">Matched: {{ $product['similarity_match']['original_name'] }}</p>
            @endif
            <p class="mt-1 flex flex-wrap items-center gap-1.5 text-xs font-bold uppercase tracking-wide text-slate-500">
                <i class="fa-regular fa-clock"></i>
                Added {{ $product['created_at_denver'] }} 
            </p>
        </div>

        <div class="grid w-full flex-shrink-0 grid-cols-5 gap-2 sm:flex sm:w-auto">
            <button type="button" title="Edit details" aria-label="Edit details" data-folder="{{ $product['folder'] }}" data-update-url="{{ route('summary.ebay.update', $product['folder']) }}" data-edit-product="{{ base64_encode($editProduct) }}" class="edit-product-button inline-flex h-11 w-full items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700 sm:h-10 sm:w-10">
                <i class="fa-regular fa-pen-to-square"></i>
            </button>
            <button type="button" title="Download images" aria-label="Download images" data-download-images="{{ base64_encode($downloadImages->toJson()) }}" class="download-images-button inline-flex h-11 w-full items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 disabled:cursor-not-allowed disabled:opacity-40 sm:h-10 sm:w-10" {{ $downloadImages->isEmpty() ? 'disabled' : '' }}>
                <i class="fa-solid fa-download"></i>
            </button>
            <form method="POST" action="{{ route('summary.ebay.images.store', $product['folder']) }}" enctype="multipart/form-data" class="w-full sm:w-auto">
                @csrf
                <input id="{{ $uploadInputId }}" type="file" name="images[]" accept="image/*" multiple class="existing-images-input hidden">
                <button type="button" title="Add images" aria-label="Add images" data-upload-input="{{ $uploadInputId }}" class="upload-images-button inline-flex h-11 w-full items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 sm:h-10 sm:w-10">
                    <i class="fa-regular fa-images"></i>
                </button>
            </form>
            <form method="POST" action="{{ route('summary.ebay.toggle-mark', $product['folder']) }}" class="w-full sm:w-auto">
                @csrf
                @method('PATCH')
                @php
                    $toggleBtnTitle = 'Mark as Done';
                    $toggleBtnClass = 'border-slate-200 text-slate-600 hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700';
                    
                    if ($markedStatus === 'ebay' || $markedStatus === 'abid') {
                        $toggleBtnTitle = 'Mark as Moses Done';
                        $toggleBtnClass = 'border-amber-250 text-amber-700 hover:bg-amber-100';
                    } elseif ($markedStatus === 'denver' || $markedStatus === 'moses') {
                        $toggleBtnTitle = 'Mark as Both Done';
                        $toggleBtnClass = 'border-blue-250 text-blue-700 hover:bg-blue-100';
                    } elseif ($markedStatus === 'both' || $markedStatus === true) {
                        $toggleBtnTitle = 'Mark as Remaining';
                        $toggleBtnClass = 'border-emerald-200 text-emerald-750 hover:bg-emerald-100';
                    }
                @endphp
                <button type="submit" title="{{ $toggleBtnTitle }}" aria-label="{{ $toggleBtnTitle }}" class="inline-flex h-11 w-full items-center justify-center rounded-lg border bg-white sm:h-10 sm:w-10 {{ $toggleBtnClass }}">
                    <i class="fa-solid fa-check-double"></i>
                </button>
            </form>
            <form method="POST" action="{{ route('summary.ebay.destroy', $product['folder']) }}" class="w-full sm:w-auto" onsubmit="return confirm('Delete this product folder and all images?')">
                @csrf
                @method('DELETE')
                <button type="submit" title="Delete folder" aria-label="Delete folder" class="inline-flex h-11 w-full items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:border-red-200 hover:bg-red-50 hover:text-red-700 sm:h-10 sm:w-10">
                    <i class="fa-regular fa-trash-can"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-4">
        @forelse (array_slice($product['images'], 0, 4) as $image)
            @php
                $url = $imageUrl($image['filename']);
                $extension = strtolower(pathinfo($image['filename'], PATHINFO_EXTENSION));
                $canPreview = in_array($extension, $previewableExtensions, true);
            @endphp
            <a href="{{ $url }}" target="_blank" class="relative block aspect-square overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                @if ($canPreview)
                    <img src="{{ $url }}" alt="{{ $image['original_name'] }}" class="h-full w-full object-cover" onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');">
                    <div class="absolute inset-0 hidden items-center justify-center bg-slate-100 text-slate-400">
                        <i class="fa-regular fa-image text-2xl"></i>
                    </div>
                @else
                    <div class="flex h-full w-full flex-col items-center justify-center gap-1 px-2 text-center text-slate-500">
                        <i class="fa-regular fa-file-image text-2xl"></i>
                        <span class="text-[10px] font-extrabold uppercase tracking-wide">{{ $extension ?: 'file' }}</span>
                    </div>
                @endif
            </a>
        @empty
            <div class="col-span-2 flex h-28 items-center justify-center rounded-lg bg-slate-100 text-slate-400 sm:col-span-4">
                <i class="fa-regular fa-image text-2xl"></i>
            </div>
        @endforelse
    </div>

    <div class="mt-4 grid gap-3 text-sm sm:grid-cols-3">
        @if ($product['condition'])
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Condition</p>
                <p class="mt-1 font-bold text-slate-700">{{ $product['condition'] }}</p>
            </div>
        @endif
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Location</p>
            <p class="mt-1 font-bold text-slate-700">{{ $product['location'] ?: '-' }}</p>
        </div>
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Quantity</p>
            <p class="mt-1 font-bold text-slate-700">{{ $product['quantity'] }}</p>
        </div>
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Images</p>
            <p class="mt-1 font-bold text-slate-700">{{ count($product['images']) }}</p>
        </div>
    </div>

    @if ($product['notes'])
        @php
            $notesBgClass = 'border-slate-100 bg-slate-50';
            if ($markedStatus === 'ebay' || $markedStatus === 'abid') {
                $notesBgClass = 'border-amber-100 bg-white/70';
            } elseif ($markedStatus === 'denver' || $markedStatus === 'moses') {
                $notesBgClass = 'border-blue-100 bg-white/70';
            } elseif ($markedStatus === 'both' || $markedStatus === true) {
                $notesBgClass = 'border-emerald-100 bg-white/70';
            }
        @endphp
        <div class="mt-4 rounded-lg border {{ $notesBgClass }} p-3.5 text-sm font-medium leading-6 text-slate-700 flex items-start gap-2.5">
            <i class="fa-solid fa-circle-info mt-0.5 text-indigo-500/80 flex-shrink-0 text-base"></i>
            <div>
                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Notes</span>
                <p class="text-slate-700 font-medium leading-relaxed">{{ $product['notes'] }}</p>
            </div>
        </div>
    @endif

    <!-- Links -->
    @if (!empty($product['ebay_link']) || !empty($product['website_link']) || !empty($product['social_media_1']) || !empty($product['social_media_2']) || !empty($product['social_media_3']))
        @php
            $linksBorderClass = 'border-slate-200';
            $itemBgClass = 'bg-slate-50/60 hover:bg-slate-100/80 hover:border-slate-300';
            if ($markedStatus === 'ebay' || $markedStatus === 'abid') {
                $linksBorderClass = 'border-amber-250';
                $itemBgClass = 'bg-amber-50/40 hover:bg-amber-100/40 hover:border-amber-200';
            } elseif ($markedStatus === 'denver' || $markedStatus === 'moses') {
                $linksBorderClass = 'border-blue-200';
                $itemBgClass = 'bg-blue-50/40 hover:bg-blue-100/40 hover:border-blue-200';
            } elseif ($markedStatus === 'both' || $markedStatus === true) {
                $linksBorderClass = 'border-emerald-200';
                $itemBgClass = 'bg-emerald-50/40 hover:bg-emerald-100/40 hover:border-emerald-200';
            }
        @endphp
        <div class="mt-4 border-t border-dashed {{ $linksBorderClass }} pt-4">
            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2.5">Folder Links</span>
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
                            <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-650 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform duration-200">
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
                            <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-650 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform duration-200">
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
                            <div class="w-7 h-7 rounded-lg bg-purple-50 text-purple-650 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform duration-200">
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
</div>
