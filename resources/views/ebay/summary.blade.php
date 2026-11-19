@extends('ebay.layout')

@section('title', 'eBay Summary')
@section('page-title', 'eBay Summary')

@section('header-action')
    <button type="button" id="open-product-modal" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-700 sm:w-auto">
        <i class="fa-solid fa-plus"></i>
        Add Product
    </button>
@endsection

@section('content')
<div id="stats-container" class="grid grid-cols-2 gap-2 sm:gap-3 lg:grid-cols-4">
    <div class="rounded-lg border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Folders</p>
        <p id="stat-folders" class="mt-2 text-xl font-extrabold text-slate-950 sm:text-2xl">{{ $stats['folders'] }}</p>
    </div>
    <div class="rounded-lg border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Images</p>
        <p id="stat-images" class="mt-2 text-xl font-extrabold text-blue-700 sm:text-2xl">{{ $stats['images'] }}</p>
    </div>
    <div class="rounded-lg border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Quantity</p>
        <p id="stat-quantity" class="mt-2 text-xl font-extrabold text-emerald-700 sm:text-2xl">{{ $stats['quantity'] }}</p>
    </div>
    <div class="rounded-lg border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Marked</p>
        <p id="stat-marked" class="mt-2 text-xl font-extrabold text-violet-700 sm:text-2xl">{{ $stats['marked'] }}</p>
    </div>
</div>

<form method="GET" action="{{ route('summary.ebay.index') }}" class="mt-4 rounded-lg border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
    <div class="grid gap-2 sm:grid-cols-[1fr_180px_180px_auto_auto]">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
            <input type="search" name="search" value="{{ $search }}" placeholder="Search folder or location" class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-3 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
        </div>
        <select name="status" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
            <option value="">All Status</option>
            <option value="done" {{ $status === 'done' ? 'selected' : '' }}>Done</option>
            <option value="remaining" {{ $status === 'remaining' ? 'selected' : '' }}>Remaining</option>
        </select>
        <select name="folder_type" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
            <option value="Ebay" {{ $folderTypeSearch === 'Ebay' ? 'selected' : '' }}>Ebay</option>
            <option value="Denver" {{ $folderTypeSearch === 'Denver' ? 'selected' : '' }}>Denver</option>
            <option value="Both" {{ $folderTypeSearch === 'Both' ? 'selected' : '' }}>Both</option>
        </select>
        <div id="clear-button-container" class="inline-flex w-full sm:w-auto">
            @if ($search !== '' || $status !== '' || $folderTypeSearch !== '')
                <a href="{{ route('summary.ebay.index') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-50 sm:w-auto">
                    <i class="fa-solid fa-xmark"></i>
                    Clear
                </a>
            @endif
        </div>
    </div>
</form>

<form method="POST" action="{{ route('summary.ebay.image-search') }}" enctype="multipart/form-data" class="mt-4 rounded-lg border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
    @csrf
    <div class="grid gap-2 sm:grid-cols-[1fr_auto_auto]">
        <input type="file" name="search_image" accept="image/jpeg,image/png,image/gif,image/webp" required class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 file:mr-3 file:rounded-md file:border-0 file:bg-slate-900 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-white focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-700 sm:w-auto">
            <i class="fa-regular fa-image"></i>
            Find Similar
        </button>
        @if ($imageSearch)
            <a href="{{ route('summary.ebay.index') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-50 sm:w-auto">
                <i class="fa-solid fa-xmark"></i>
                Clear
            </a>
        @endif
    </div>
    @if ($imageSearch)
        <p class="mt-2 text-xs font-bold uppercase tracking-wide text-blue-600">Image: {{ $imageSearchName }}</p>
    @endif
</form>

<div class="mt-5">
    <div class="mb-3 rounded-lg border border-slate-200 bg-white px-4 py-3 shadow-sm">
        <h2 class="text-sm font-extrabold uppercase tracking-wide text-slate-700">{{ $imageSearch ? 'Image Search Results' : (($search !== '' || $status !== '') ? 'Search Results' : 'Product Folders') }}</h2>
    </div>

    <div id="product-list" class="grid gap-4 lg:grid-cols-2">
        @forelse ($products as $product)
            @include('ebay.partials.product-card', ['product' => $product])
        @empty
            <div id="empty-product-state" class="rounded-lg border border-slate-200 bg-white px-4 py-14 text-center shadow-sm lg:col-span-2">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-lg bg-slate-100 text-slate-400">
                    <i class="fa-regular fa-folder-open"></i>
                </div>
                <p class="mt-3 text-sm font-bold text-slate-700">{{ $imageSearch ? 'No folders have a similar image.' : (($search !== '' || $status !== '') ? 'No folders match these filters.' : 'No product folders found.') }}</p>
            </div>
        @endforelse
    </div>
</div>

<div id="product-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/50 px-3 py-3 sm:px-4 sm:py-6">
    <div class="mx-auto max-h-[calc(100vh-1.5rem)] max-w-3xl overflow-y-auto rounded-lg bg-white shadow-xl sm:max-h-none">
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3 sm:px-5 sm:py-4">
            <h2 class="text-base font-extrabold text-slate-950">Add Product</h2>
            <button type="button" id="close-product-modal" title="Close" aria-label="Close" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="product-form" method="POST" action="{{ route('summary.ebay.store') }}" enctype="multipart/form-data" class="p-4 sm:p-5">
            @csrf

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="folder_name" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Folder Name</label>
                    <input id="folder_name" name="folder_name" type="text" value="{{ old('folder_name') }}" required class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                </div>

                <div class="sm:col-span-2">
                    <label for="condition" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Condition</label>
                    <select id="condition" name="condition" required class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                        <option value="">Select condition</option>
                        <option value="New" {{ old('condition') === 'New' ? 'selected' : '' }}>New</option>
                        <option value="Used" {{ old('condition') === 'Used' ? 'selected' : '' }}>Used</option>
                        <option value="Refurbished" {{ old('condition') === 'Refurbished' ? 'selected' : '' }}>Refurbished</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label for="folder_type" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Folder Type</label>
                    <select id="folder_type" name="folder_type" required class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                        <option value="Ebay" {{ old('folder_type', 'Ebay') === 'Ebay' ? 'selected' : '' }}>Ebay</option>
                        <option value="Denver" {{ old('folder_type') === 'Denver' ? 'selected' : '' }}>Denver</option>
                        <option value="Both" {{ old('folder_type') === 'Both' ? 'selected' : '' }}>Both</option>
                    </select>
                </div>

                <div>
                    <label for="location" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Location</label>
                    <input id="location" name="location" type="text" value="{{ old('location') }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                </div>

                <div>
                    <label for="quantity" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Quantity</label>
                    <input id="quantity" name="quantity" type="number" min="1" value="{{ old('quantity', 1) }}" required class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                </div>

                <div class="sm:col-span-2">
                    <label for="notes" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Notes</label>
                    <textarea id="notes" name="notes" rows="4" class="w-full resize-y rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">{{ old('notes') }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Images</label>
                    <div class="grid gap-2 sm:flex sm:flex-wrap">
                        <button type="button" id="camera-button" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 sm:w-auto">
                            <i class="fa-solid fa-camera"></i>
                            Camera
                        </button>
                        <button type="button" id="gallery-button" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 sm:w-auto">
                            <i class="fa-regular fa-images"></i>
                            Select Images
                        </button>
                    </div>
                    <input id="image-files" name="images[]" type="file" accept="image/*" multiple class="hidden">
                    <input id="camera-input" type="file" accept="image/*" capture="environment" multiple class="hidden">
                    <input id="gallery-input" type="file" accept="image/*" multiple class="hidden">
                    <div id="camera-panel" class="mt-3 hidden overflow-hidden rounded-lg border border-blue-100 bg-slate-950">
                        <div class="relative bg-black">
                            <video id="camera-video" class="h-72 w-full bg-black object-contain" autoplay muted playsinline></video>
                            <div id="camera-status" class="absolute inset-x-0 top-0 bg-slate-950/70 px-3 py-2 text-xs font-bold text-white">Starting camera...</div>
                        </div>
                        <div class="grid gap-2 bg-white p-3 sm:flex sm:items-center sm:justify-between">
                            <p id="camera-capture-count" class="text-xs font-bold uppercase tracking-wide text-slate-500">0 captured</p>
                            <div class="grid gap-2 sm:flex">
                                <button type="button" id="capture-photo-button" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-700 sm:w-auto">
                                    <i class="fa-solid fa-camera-retro"></i>
                                    Capture
                                </button>
                                <button type="button" id="stop-camera-button" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-50 sm:w-auto">
                                    <i class="fa-solid fa-video-slash"></i>
                                    Close Camera
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="image-count" class="mt-3 text-xs font-bold uppercase tracking-wide text-slate-500">0 selected</div>
                    <div id="image-preview" class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-4"></div>
                </div>
            </div>

            <div class="mt-5 grid gap-2 border-t border-slate-100 pt-4 sm:flex sm:justify-end">
                <button type="button" id="cancel-product-modal" class="inline-flex w-full items-center justify-center rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-50 sm:w-auto">Cancel</button>
                <button type="submit" id="create-folder-button" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-700 disabled:cursor-wait disabled:opacity-70 sm:w-auto">
                    <i class="fa-solid fa-folder-plus"></i>
                    <span>Create Folder</span>
                </button>
            </div>
        </form>
    </div>
</div>

<div id="edit-product-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/50 px-3 py-3 sm:px-4 sm:py-6">
    <div class="mx-auto max-h-[calc(100vh-1.5rem)] max-w-2xl overflow-y-auto rounded-lg bg-white shadow-xl sm:max-h-none">
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3 sm:px-5 sm:py-4">
            <h2 id="edit-product-title" class="text-base font-extrabold text-slate-950">Edit Product</h2>
            <button type="button" id="close-edit-product-modal" title="Close" aria-label="Close" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-800">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="edit-product-form" method="POST" action="" class="p-4 sm:p-5">
            @csrf
            @method('PATCH')

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="edit_folder_name" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Folder Name</label>
                    <input id="edit_folder_name" name="folder_name" type="text" required class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                </div>

                <div class="sm:col-span-2">
                    <label for="edit_condition" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Condition</label>
                    <select id="edit_condition" name="condition" required class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                        <option value="">Select condition</option>
                        <option value="New">New</option>
                        <option value="Used">Used</option>
                        <option value="Refurbished">Refurbished</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label for="edit_folder_type" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Folder Type</label>
                    <select id="edit_folder_type" name="folder_type" required class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                        <option value="Ebay">Ebay</option>
                        <option value="Denver">Denver</option>
                        <option value="Both">Both</option>
                    </select>
                </div>

                <div>
                    <label for="edit_location" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Location</label>
                    <input id="edit_location" name="location" type="text" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                </div>

                <div>
                    <label for="edit_quantity" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Quantity</label>
                    <input id="edit_quantity" name="quantity" type="number" min="1" required class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
                </div>

                <div class="sm:col-span-2">
                    <label for="edit_notes" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500">Notes</label>
                    <textarea id="edit_notes" name="notes" rows="4" class="w-full resize-y rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"></textarea>
                </div>

                <label for="edit_marked" class="sm:col-span-2 flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-3 text-sm font-bold text-slate-700">
                    <input id="edit_marked" name="marked" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-200">
                    <span>Done</span>
                </label>
            </div>

            <div class="mt-5 grid gap-2 border-t border-slate-100 pt-4 sm:flex sm:justify-end">
                <button type="button" id="cancel-edit-product-modal" class="inline-flex w-full items-center justify-center rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-50 sm:w-auto">Cancel</button>
                <button type="submit" id="save-product-button" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-indigo-700 disabled:cursor-wait disabled:opacity-70 sm:w-auto">
                    <i class="fa-regular fa-floppy-disk"></i>
                    <span>Save Changes</span>
                </button>
            </div>
        </form>

        <div class="border-t border-slate-100 p-4 sm:p-5">
            <div class="grid gap-2 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Images</p>
                    <p id="edit-images-count" class="mt-1 text-sm font-bold text-slate-700">0 images</p>
                </div>
                <form id="edit-images-form" method="POST" action="" enctype="multipart/form-data" class="w-full sm:w-auto">
                    @csrf
                    <input type="hidden" name="return_to_edit" value="1">
                    <input id="edit-images-input" name="images[]" type="file" accept="image/*" multiple class="hidden">
                    <button type="button" id="edit-select-images-button" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 sm:w-auto">
                        <i class="fa-regular fa-images"></i>
                        Add Images
                    </button>
                </form>
            </div>
            <div id="edit-images-grid" class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-4"></div>
            <div id="edit-images-empty" class="mt-3 hidden rounded-lg border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm font-bold text-slate-500">
                No images
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (() => {
        const modal           = document.getElementById('product-modal');
        const editModal       = document.getElementById('edit-product-modal');
        const openButton      = document.getElementById('open-product-modal');
        const closeButtons    = [document.getElementById('close-product-modal'), document.getElementById('cancel-product-modal')];
        const editCloseButtons = [document.getElementById('close-edit-product-modal'), document.getElementById('cancel-edit-product-modal')];
        const productForm         = document.getElementById('product-form');
        const editProductForm     = document.getElementById('edit-product-form');
        const editProductTitle    = document.getElementById('edit-product-title');
        const saveProductButton   = document.getElementById('save-product-button');
        const editImagesForm      = document.getElementById('edit-images-form');
        const editImagesInput     = document.getElementById('edit-images-input');
        const editSelectImagesButton = document.getElementById('edit-select-images-button');
        const editImagesGrid      = document.getElementById('edit-images-grid');
        const editImagesEmpty     = document.getElementById('edit-images-empty');
        const editImagesCount     = document.getElementById('edit-images-count');
        const cameraButton        = document.getElementById('camera-button');
        const galleryButton       = document.getElementById('gallery-button');
        const imageFiles          = document.getElementById('image-files');
        const cameraInput         = document.getElementById('camera-input');
        const galleryInput        = document.getElementById('gallery-input');
        const cameraPanel         = document.getElementById('camera-panel');
        const cameraVideo         = document.getElementById('camera-video');
        const cameraStatus        = document.getElementById('camera-status');
        const capturePhotoButton  = document.getElementById('capture-photo-button');
        const stopCameraButton    = document.getElementById('stop-camera-button');
        const cameraCaptureCount  = document.getElementById('camera-capture-count');
        const imageCount          = document.getElementById('image-count');
        const preview             = document.getElementById('image-preview');
        const createFolderButton  = document.getElementById('create-folder-button');
        const productList         = document.getElementById('product-list');
        const emptyProductState   = document.getElementById('empty-product-state');

        const indexUrl    = @json(route('summary.ebay.index'));
        const removeBgUrl = @json(route('summary.ebay.remove-bg'));
        const editFolderToOpen = @json(session('edit_folder'));
        const csrfToken   = document.querySelector('meta[name="csrf-token"]')?.content || '';

        const removeBgMaxDimension = 640;
        const removeBgJpegQuality  = 0.72;

        let selectedImages          = [];
        let cameraStream            = null;
        let cameraCaptureTotal      = 0;
        let pendingBackgroundRemovals = 0;
        let isSubmitting            = false;

        // ---- Modal helpers ----
        const openModal = () => {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        };
        const closeModal = () => {
            stopCamera();
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };
        const closeEditModal = () => {
            editModal?.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        const decodeProductData = (encoded) => {
            try { return JSON.parse(atob(encoded || 'e30=')); } catch { return {}; }
        };
        const hiddenInput = (name, value) => {
            const input = document.createElement('input');
            input.type = 'hidden'; input.name = name; input.value = value;
            return input;
        };

        // ---- Image selection & preview ----
        const imageId = (file) =>
            `${file.name}-${file.size}-${file.lastModified}-${crypto.randomUUID?.() ?? Math.random()}`;

        const syncImageInput = () => {
            const transfer = new DataTransfer();
            selectedImages.forEach((item) => transfer.items.add(item.file));
            imageFiles.files = transfer.files;
        };

        const renderPreview = () => {
            imageCount.textContent = `${selectedImages.length} selected`;
            imageCount.className = 'mt-3 text-xs font-bold uppercase tracking-wide ' +
                (selectedImages.length === 0 ? 'text-red-600' : 'text-slate-500');
            preview.innerHTML = '';

            selectedImages.forEach((selected) => {
                const wrap   = document.createElement('div');
                const img    = document.createElement('img');
                const rmBtn  = document.createElement('button');
                const label  = document.createElement('div');

                wrap.className  = 'relative overflow-hidden rounded-lg border border-slate-200 bg-slate-100';
                img.className   = 'h-24 w-full object-cover';
                img.alt         = selected.file.name;
                img.src         = URL.createObjectURL(selected.file);
                img.onload      = () => URL.revokeObjectURL(img.src);

                rmBtn.type      = 'button';
                rmBtn.title     = 'Remove image';
                rmBtn.setAttribute('aria-label', 'Remove image');
                rmBtn.className = 'absolute right-1.5 top-1.5 inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/95 text-red-600 shadow-sm hover:bg-red-50';
                rmBtn.innerHTML = '<i class="fa-solid fa-xmark text-xs"></i>';
                rmBtn.addEventListener('click', () => {
                    selectedImages = selectedImages.filter((i) => i.id !== selected.id);
                    syncImageInput();
                    renderPreview();
                });

                label.className   = 'absolute inset-x-0 bottom-0 truncate bg-slate-950/60 px-2 py-1 text-[10px] font-bold text-white';
                label.textContent = selected.file.name;

                wrap.appendChild(img);
                wrap.appendChild(rmBtn);
                wrap.appendChild(label);
                preview.appendChild(wrap);
            });
        };

        const addImageFiles = (files) => {
            for (const file of files) {
                selectedImages.push({ id: imageId(file), file });
            }
            syncImageInput();
            renderPreview();
        };

        const addFromInput = (input) => {
            const files = Array.from(input.files || []);
            input.value = '';
            if (files.length === 0) return;
            addImageFiles(files);
        };

        // ---- Submit state ----
        const setSubmitState = (submitting) => {
            if (!createFolderButton) return;
            if (submitting) {
                createFolderButton.disabled = true;
                createFolderButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Creating...</span>';
            } else {
                createFolderButton.disabled = pendingBackgroundRemovals > 0;
                createFolderButton.innerHTML = pendingBackgroundRemovals > 0
                    ? '<i class="fa-solid fa-spinner fa-spin"></i><span>Processing...</span>'
                    : '<i class="fa-solid fa-folder-plus"></i><span>Create Folder</span>';
            }
        };

        // ---- Camera ----
        const setCameraStatus = (msg, isError = false) => {
            if (!cameraStatus) return;
            cameraStatus.textContent = msg;
            cameraStatus.classList.toggle('bg-red-700/80', isError);
            cameraStatus.classList.toggle('bg-slate-950/70', !isError);
            cameraStatus.classList.toggle('hidden', msg === '');
        };
        const updateCameraCaptureCount = () => {
            if (cameraCaptureCount) cameraCaptureCount.textContent = `${cameraCaptureTotal} captured this session`;
        };

        const canvasToBlob = (canvas, type = 'image/jpeg', quality = 0.9) =>
            new Promise((res, rej) => canvas.toBlob((b) => b ? res(b) : rej(new Error('Failed to prepare image.')), type, quality));

        const fileBaseName = (name) => (name || 'photo').replace(/\.[^.]+$/, '') || 'photo';

        const loadImageFromFile = (file) => new Promise((res, rej) => {
            const img = new Image();
            const url = URL.createObjectURL(file);
            img.onload  = () => { URL.revokeObjectURL(url); res(img); };
            img.onerror = () => { URL.revokeObjectURL(url); rej(new Error('Cannot load image.')); };
            img.src = url;
        });

        const createRemoveBgBlob = async (file) => {
            const image  = await loadImageFromFile(file);
            const scale  = Math.min(1, removeBgMaxDimension / Math.max(image.naturalWidth || image.width, image.naturalHeight || image.height));
            const w      = Math.max(1, Math.round((image.naturalWidth  || image.width)  * scale));
            const h      = Math.max(1, Math.round((image.naturalHeight || image.height) * scale));
            const canvas = document.createElement('canvas');
            const ctx    = canvas.getContext('2d');
            canvas.width = w; canvas.height = h;
            ctx.fillStyle = '#ffffff'; ctx.fillRect(0, 0, w, h);
            ctx.drawImage(image, 0, 0, w, h);
            return canvasToBlob(canvas, 'image/jpeg', removeBgJpegQuality);
        };

        const responseErrorMessage = async (response, fallback = 'Request failed.') => {
            try {
                const d = await response.json();
                return d.message || Object.values(d.errors || {})[0]?.[0] || fallback;
            } catch { return response.statusText || fallback; }
        };

        const startCamera = async () => {
            if (!navigator.mediaDevices?.getUserMedia) { cameraInput?.click(); return; }
            cameraPanel?.classList.remove('hidden');
            setCameraStatus('Starting camera...');
            try {
                if (!cameraStream) {
                    cameraStream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: { ideal: 'environment' }, width: { ideal: 1600 }, height: { ideal: 1200 } },
                        audio: false,
                    });
                }
                cameraVideo.srcObject = cameraStream;
                await cameraVideo.play();
                setCameraStatus('');
                updateCameraCaptureCount();
            } catch { stopCamera(); cameraInput?.click(); }
        };

        const stopCamera = () => {
            cameraStream?.getTracks().forEach((t) => t.stop());
            cameraStream = null;
            if (cameraVideo) { cameraVideo.pause(); cameraVideo.removeAttribute('src'); cameraVideo.srcObject = null; }
            cameraPanel?.classList.add('hidden');
            setCameraStatus('');
        };

        const capturePhoto = async () => {
            if (!cameraVideo || !cameraStream || cameraVideo.readyState < 2) {
                setCameraStatus('Camera starting, try again.', true); return;
            }
            const vw = cameraVideo.videoWidth || 1280, vh = cameraVideo.videoHeight || 720;
            const scale = Math.min(1, removeBgMaxDimension / Math.max(vw, vh));
            const canvas = document.createElement('canvas');
            canvas.width  = Math.max(1, Math.round(vw * scale));
            canvas.height = Math.max(1, Math.round(vh * scale));
            canvas.getContext('2d').drawImage(cameraVideo, 0, 0, canvas.width, canvas.height);
            try {
                const blob = await canvasToBlob(canvas, 'image/jpeg', removeBgJpegQuality);
                const file = new File([blob], `camera-${new Date().toISOString().replace(/[:.]/g, '-')}.jpg`, { type: 'image/jpeg', lastModified: Date.now() });
                addImageFiles([file]);
                cameraCaptureTotal++;
                updateCameraCaptureCount();
            } catch (err) { setCameraStatus(err.message || 'Capture failed.', true); }
        };

        const formValue = (name) => productForm?.elements[name]?.value?.trim() || '';

        const productDetail = (label, value) => {
            const wrap = document.createElement('div');
            const h    = document.createElement('p');
            const p    = document.createElement('p');
            h.className = 'text-xs font-bold uppercase tracking-wide text-slate-400';
            h.textContent = label;
            p.className = 'mt-1 font-bold text-slate-700';
            p.textContent = value || '-';
            wrap.appendChild(h); wrap.appendChild(p);
            return wrap;
        };

        const addInstantCard = () => {
            if (!productList) return null;
            emptyProductState?.remove();

            const card      = document.createElement('div');
            const titleRow  = document.createElement('div');
            const title     = document.createElement('h3');
            const spinner   = document.createElement('span');
            const badge     = document.createElement('span');
            const imageGrid = document.createElement('div');
            const details   = document.createElement('div');

            card.className      = 'rounded-lg border border-slate-200 bg-white p-3 shadow-sm sm:p-4';
            titleRow.className  = 'flex flex-wrap items-center gap-2';
            title.className     = 'min-w-0 break-words text-base font-extrabold text-slate-950 sm:text-lg';
            title.textContent   = formValue('folder_name') || 'New product';
            
            spinner.className   = 'inline-flex items-center justify-center text-slate-400';
            spinner.innerHTML   = '<i class="fa-solid fa-spinner fa-spin text-sm"></i>';
            
            const folderType = formValue('folder_type') || 'Ebay';
            const badgeColorClass = folderType === 'Denver' ? 'border-purple-200 bg-purple-50 text-purple-700' :
                                    folderType === 'Both' ? 'border-indigo-200 bg-indigo-50 text-indigo-700' :
                                    'border-blue-200 bg-blue-50 text-blue-700';
            
            badge.className     = 'inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-bold ' + badgeColorClass;
            badge.innerHTML     = '<i class="fa-regular fa-folder text-[10px]"></i> ' + folderType;
            
            imageGrid.className = 'mt-4 grid grid-cols-2 gap-2 sm:grid-cols-4';

            selectedImages.slice(0, 4).forEach(({ file }) => {
                const frame = document.createElement('div');
                const img   = document.createElement('img');
                frame.className = 'aspect-square overflow-hidden rounded-lg border border-slate-200 bg-slate-100';
                img.className   = 'h-full w-full object-cover';
                img.alt = file.name;
                img.src = URL.createObjectURL(file);
                img.onload = () => URL.revokeObjectURL(img.src);
                frame.appendChild(img);
                imageGrid.appendChild(frame);
            });

            if (selectedImages.length === 0) {
                const frame = document.createElement('div');
                frame.className = 'col-span-2 flex h-28 items-center justify-center rounded-lg bg-slate-100 text-slate-400 sm:col-span-4';
                frame.innerHTML = '<i class="fa-regular fa-image text-2xl"></i>';
                imageGrid.appendChild(frame);
            }

            details.className = 'mt-4 grid gap-3 text-sm sm:grid-cols-3';
            details.appendChild(productDetail('Condition', formValue('condition')));
            details.appendChild(productDetail('Location',  formValue('location')));
            details.appendChild(productDetail('Quantity',  formValue('quantity')));
            details.appendChild(productDetail('Images',    `${selectedImages.length}`));

            titleRow.appendChild(title);
            titleRow.appendChild(badge);
            titleRow.appendChild(spinner);
            card.appendChild(titleRow);
            card.appendChild(imageGrid);
            card.appendChild(details);
            productList.prepend(card);
            return card;
        };

        const resetProductForm = () => {
            productForm.reset();
            selectedImages = [];
            cameraCaptureTotal = 0;
            syncImageInput();
            renderPreview();
            updateCameraCaptureCount();
            setSubmitState(false);
            isSubmitting = false;
        };

        // ---- Background submit (no blocking of Add Product button) ----
        const submitInBackground = async (event) => {
            event.preventDefault();
            if (isSubmitting) return;

            if (pendingBackgroundRemovals > 0) {
                imageCount.textContent = 'Wait for background removal to finish';
                imageCount.className = 'mt-3 text-xs font-bold uppercase tracking-wide text-amber-600';
                return;
            }
            if (selectedImages.length === 0) {
                imageCount.textContent = 'Please select at least 1 image';
                imageCount.className = 'mt-3 text-xs font-bold uppercase tracking-wide text-red-600';
                return;
            }

            syncImageInput();
            const data = new FormData(productForm);
            const card = addInstantCard(); // Card appears immediately!

            isSubmitting = true;
            setSubmitState(true);
            stopCamera();
            closeModal();
            resetProductForm(); // Reset form fields immediately so they become empty

            try {
                const response = await fetch(productForm.action, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: data,
                });

                const resData = await response.json().catch(() => ({}));

                if (!response.ok) {
                    const firstError = Object.values(resData?.errors || {})[0]?.[0];
                    throw new Error(firstError || resData.message || 'Upload failed.');
                }

                // Replace the temporary card with the completed card
                if (resData.html && card) {
                    const parser = new DOMParser();
                    const parsedDoc = parser.parseFromString(resData.html, 'text/html');
                    const newCard = parsedDoc.body.firstElementChild;
                    if (newCard) {
                        card.replaceWith(newCard);
                    }
                }

                // Update the stats count on the page
                if (resData.stats) {
                    const stats = resData.stats;
                    const foldersEl = document.getElementById('stat-folders');
                    const imagesEl = document.getElementById('stat-images');
                    const quantityEl = document.getElementById('stat-quantity');
                    const markedEl = document.getElementById('stat-marked');
                    if (foldersEl) foldersEl.textContent = stats.folders;
                    if (imagesEl) imagesEl.textContent = stats.images;
                    if (quantityEl) quantityEl.textContent = stats.quantity;
                    if (markedEl) markedEl.textContent = stats.marked;
                }
            } catch (error) {
                if (card) {
                    card.remove(); // Remove the temporary card if upload failed
                }
                alert(error.message || 'Upload failed. Please try again.');
            }
        };

        // ---- Edit modal ----
        const renderEditImages = (images) => {
            if (!editImagesGrid || !editImagesEmpty || !editImagesCount) return;
            const list = Array.isArray(images) ? images : [];
            editImagesGrid.innerHTML = '';
            editImagesCount.textContent = `${list.length} ${list.length === 1 ? 'image' : 'images'}`;
            editImagesEmpty.classList.toggle('hidden', list.length > 0);
            editImagesGrid.classList.toggle('hidden', list.length === 0);

            list.forEach((image) => {
                const frame       = document.createElement('div');
                const link        = document.createElement('a');
                const deleteForm  = document.createElement('form');
                const deleteBtn   = document.createElement('button');

                frame.className = 'relative aspect-square overflow-hidden rounded-lg border border-slate-200 bg-slate-100';
                link.href       = image.url || '#';
                link.target     = '_blank';
                link.className  = 'block h-full w-full';

                if (image.can_preview) {
                    const img = document.createElement('img');
                    img.src = image.url;
                    img.alt = image.original_name || image.filename || 'Image';
                    img.className = 'h-full w-full object-cover';
                    link.appendChild(img);
                } else {
                    const ph  = document.createElement('div');
                    const ico = document.createElement('i');
                    const ext = document.createElement('span');
                    ph.className  = 'flex h-full w-full flex-col items-center justify-center gap-1 px-2 text-center text-slate-500';
                    ico.className = 'fa-regular fa-file-image text-2xl';
                    ext.className = 'text-[10px] font-extrabold uppercase tracking-wide';
                    ext.textContent = image.extension || 'file';
                    ph.appendChild(ico); ph.appendChild(ext);
                    link.appendChild(ph);
                }

                deleteForm.method    = 'POST';
                deleteForm.action    = image.delete_url || '#';
                deleteForm.className = 'absolute right-1.5 top-1.5';
                deleteForm.appendChild(hiddenInput('_token', csrfToken));
                deleteForm.appendChild(hiddenInput('_method', 'DELETE'));
                deleteForm.addEventListener('submit', (e) => { if (!confirm('Delete this image?')) e.preventDefault(); });

                deleteBtn.type  = 'submit';
                deleteBtn.title = 'Delete image';
                deleteBtn.setAttribute('aria-label', 'Delete image');
                deleteBtn.className = 'inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/95 text-red-600 shadow-sm hover:bg-red-50';
                deleteBtn.innerHTML = '<i class="fa-regular fa-trash-can text-xs"></i>';

                deleteForm.appendChild(deleteBtn);
                frame.appendChild(link);
                frame.appendChild(deleteForm);
                editImagesGrid.appendChild(frame);
            });
        };

        const openEditModal = (button) => {
            if (!editProductForm || !editModal) return;
            const product = decodeProductData(button.dataset.editProduct);
            editProductForm.action = button.dataset.updateUrl || '';
            if (editImagesForm) editImagesForm.action = product.upload_url || '';
            editProductForm.elements.folder_name.value = product.folder_name || '';
            editProductForm.elements.condition.value   = product.condition   || '';
            editProductForm.elements.folder_type.value = product.folder_type || 'Ebay';
            editProductForm.elements.location.value    = product.location    || '';
            editProductForm.elements.quantity.value    = product.quantity    || 1;
            editProductForm.elements.notes.value       = product.notes       || '';
            editProductForm.elements.marked.checked    = Boolean(product.marked);
            renderEditImages(product.images || []);
            if (editProductTitle) editProductTitle.textContent = product.folder_name ? `Edit ${product.folder_name}` : 'Edit Product';
            if (saveProductButton) { saveProductButton.disabled = false; saveProductButton.innerHTML = '<i class="fa-regular fa-floppy-disk"></i><span>Save Changes</span>'; }
            if (editSelectImagesButton) { editSelectImagesButton.disabled = false; editSelectImagesButton.innerHTML = '<i class="fa-regular fa-images"></i><span>Add Images</span>'; }
            stopCamera();
            modal?.classList.add('hidden');
            editModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        };

        // ---- Event listeners ----
        openButton?.addEventListener('click', openModal);
        closeButtons.forEach((b) => b?.addEventListener('click', closeModal));
        editCloseButtons.forEach((b) => b?.addEventListener('click', closeEditModal));

        editProductForm?.addEventListener('submit', () => {
            if (!saveProductButton) return;
            saveProductButton.disabled = true;
            saveProductButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Saving...</span>';
        });

        editSelectImagesButton?.addEventListener('click', () => editImagesInput?.click());
        editImagesInput?.addEventListener('change', () => {
            if (!(editImagesInput.files?.length)) return;
            if (editSelectImagesButton) {
                editSelectImagesButton.disabled = true;
                editSelectImagesButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Uploading...</span>';
            }
            editImagesForm?.submit();
        });

        cameraButton?.addEventListener('click', startCamera);
        capturePhotoButton?.addEventListener('click', capturePhoto);
        stopCameraButton?.addEventListener('click', stopCamera);
        galleryButton?.addEventListener('click', () => galleryInput?.click());
        cameraInput?.addEventListener('change', () => { addFromInput(cameraInput); cameraCaptureTotal += (cameraInput.files?.length || 0); updateCameraCaptureCount(); });
        galleryInput?.addEventListener('change', () => addFromInput(galleryInput));

        productForm?.addEventListener('submit', submitInBackground);

        // Event delegation — product list interactions
        document.addEventListener('click', (event) => {
            const editBtn = event.target.closest('.edit-product-button');
            if (editBtn) { openEditModal(editBtn); return; }

            const downloadBtn = event.target.closest('.download-images-button');
            if (downloadBtn && !downloadBtn.disabled) {
                const images = JSON.parse(atob(downloadBtn.dataset.downloadImages || 'W10='));
                images.forEach((img, i) => setTimeout(() => {
                    const a = document.createElement('a');
                    a.href = img.url; a.download = img.filename;
                    document.body.appendChild(a); a.click(); a.remove();
                }, i * 150));
                return;
            }

            const uploadBtn = event.target.closest('.upload-images-button');
            if (uploadBtn) { document.getElementById(uploadBtn.dataset.uploadInput || '')?.click(); }
        });

        document.addEventListener('change', (event) => {
            const input = event.target.closest('.existing-images-input');
            if (input && input.files?.length) {
                const btn = input.closest('form')?.querySelector('.upload-images-button');
                if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>'; }
                input.closest('form')?.submit();
            }
        });

        // Live search
        const searchForm = document.querySelector('form[action="{{ route('summary.ebay.index') }}"]');
        if (searchForm) {
            const searchInput  = searchForm.querySelector('input[name="search"]');
            const statusSelect = searchForm.querySelector('select[name="status"]');
            const typeSelect   = searchForm.querySelector('select[name="folder_type"]');
            let debounceTimer;

            const performSearch = async () => {
                const url = `${searchForm.action}?${new URLSearchParams(new FormData(searchForm))}`;
                window.history.replaceState({}, '', url);
                try {
                    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if (res.ok) {
                        const doc = new DOMParser().parseFromString(await res.text(), 'text/html');
                        ['product-list', 'stats-container', 'clear-button-container'].forEach((id) => {
                            const n = doc.getElementById(id), c = document.getElementById(id);
                            if (n && c) c.innerHTML = n.innerHTML;
                        });
                    }
                } catch {}
            };

            searchInput?.addEventListener('input', () => { clearTimeout(debounceTimer); debounceTimer = setTimeout(performSearch, 250); });
            statusSelect?.addEventListener('change', performSearch);
            typeSelect?.addEventListener('change', performSearch);
            searchForm.addEventListener('submit', (e) => { e.preventDefault(); clearTimeout(debounceTimer); performSearch(); });
        }

        modal?.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
        editModal?.addEventListener('click', (e) => { if (e.target === editModal) closeEditModal(); });

        if (editFolderToOpen) {
            const esc = window.CSS?.escape ? CSS.escape(editFolderToOpen) : editFolderToOpen;
            document.querySelector(`.edit-product-button[data-folder="${esc}"]`)?.click();
        }

        @if (isset($errors) && $errors->getBag('default')->any())
            openModal();
        @endif
    })();
</script>
@endsection
