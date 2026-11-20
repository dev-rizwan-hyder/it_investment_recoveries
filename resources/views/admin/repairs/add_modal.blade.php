@php
    $oldRepairItems = old('items');

    if (!is_array($oldRepairItems) || empty($oldRepairItems)) {
        $oldRepairItems = [
            [
                'brand_category' => old('brand_category'),
                'device_type' => old('device_type'),
                'category' => old('category'),
                'sub_category' => old('sub_category'),
                'sub_sub_category' => old('sub_sub_category'),
                'category_id' => old('category_id'),
                'sub_category_id' => old('sub_category_id'),
                'sub_sub_category_id' => old('sub_sub_category_id'),
                'price' => old('price'),
                'quantity' => old('quantity', 1),
                'status' => old('status', \App\Models\RepairEntry::STATUS_RECEIVING),
                'operating_system' => old('operating_system'),
                'put_away_location' => old('put_away_location'),
                'device_specs' => old('device_specs'),
                'notes' => old('notes'),
                'item_barcode' => old('item_barcode'),
            ],
        ];
    }

    $statusOptions = \App\Models\RepairEntry::statusChoices();
@endphp

<div id="addEntryModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-2 sm:p-4">
    <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="closeModal()"></div>

    <div class="relative bg-white rounded-2xl md:rounded-3xl shadow-2xl w-full max-w-5xl max-h-[95vh] overflow-hidden flex flex-col transform transition-all animate-in zoom-in-95">
        <div class="px-6 py-4 border-b flex justify-between items-center sticky top-0 bg-white z-10">
            <h2 class="text-base md:text-xl font-black text-slate-800">Add New Repair Entry</h2>
            <button type="button" onclick="closeModal()" class="h-8 w-8 rounded-full hover:bg-slate-100 text-slate-400">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="repair-entry-form" action="{{ route('admin.repair.entries.store') }}" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-6 md:p-8 space-y-6">
            @csrf

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <p class="font-black text-red-900">Please fix the repair entry details below.</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 md:p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6">
                    
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-slate-400">Customer Name</label>
                        <input id="repair-customer-name" type="text" name="customer_name" value="{{ old('customer_name') }}"
                            class="w-full bg-white border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500"
                            placeholder="Customer name">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-slate-400">Customer Phone</label>
                        <input id="repair-customer-phone" type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                            class="w-full bg-white border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500"
                            placeholder="(555) 123-4567">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-slate-400">Customer Email</label>
                        <input id="repair-customer-email" type="email" name="customer_email" value="{{ old('customer_email') }}"
                            class="w-full bg-white border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500"
                            placeholder="customer@example.com">
                    </div>
                </div>
            </div>

            <div id="repair-items" class="space-y-6">
                @foreach ($oldRepairItems as $itemIndex => $repairItem)
                    @php
                        $repairItem = is_array($repairItem) ? $repairItem : [];
                    @endphp

                    <div class="repair-item-card rounded-2xl border border-slate-200 bg-white p-4 md:p-5 space-y-5" data-repair-item-card data-repair-item-index="{{ $itemIndex }}">
                        <div class="flex items-center justify-between gap-3">
                            <h3 class="text-sm font-black text-slate-800" data-item-title>Repair Item #{{ $loop->iteration }}</h3>
                            <button type="button" data-remove-item
                                class="{{ count($oldRepairItems) === 1 ? 'hidden ' : '' }}px-3 py-2 rounded-lg bg-red-50 text-red-600 text-xs font-bold hover:bg-red-100">
                                <i class="fas fa-trash mr-1"></i>
                                Remove
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                                <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-slate-400">Technician</label>
                            <input id="repair-technician" type="text" name="technician" value="{{ old('technician') }}" required
                                class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500"
                                placeholder="Technician name">
                        </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-slate-400">Device Type</label>
                                <input type="text" data-field="brand_category" name="items[{{ $itemIndex }}][brand_category]" value="{{ $repairItem['brand_category'] ?? '' }}"
                                    class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500"
                                    placeholder="Laptop, desktop, monitor">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-slate-400">Brand & Model Number</label>
                                <input type="text" data-field="device_type" name="items[{{ $itemIndex }}][device_type]" value="{{ $repairItem['device_type'] ?? '' }}" required
                                    class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500"
                                    placeholder="Dell Latitude 5420">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-slate-400">Category</label>
                                <select data-field="category_id" name="items[{{ $itemIndex }}][category_id]"
                                    data-old-value="{{ $repairItem['category_id'] ?? '' }}"
                                    data-old-name="{{ $repairItem['category'] ?? '' }}"
                                    class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select category</option>
                                </select>
                                <input type="hidden" data-field="category" name="items[{{ $itemIndex }}][category]" value="{{ $repairItem['category'] ?? '' }}">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-slate-400">Sub Category</label>
                                <select data-field="sub_category_id" name="items[{{ $itemIndex }}][sub_category_id]"
                                    data-old-value="{{ $repairItem['sub_category_id'] ?? '' }}"
                                    data-old-name="{{ $repairItem['sub_category'] ?? '' }}"
                                    class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 disabled:bg-slate-100 disabled:cursor-not-allowed"
                                    disabled>
                                    <option value="">Select sub category</option>
                                </select>
                                <input type="hidden" data-field="sub_category" name="items[{{ $itemIndex }}][sub_category]" value="{{ $repairItem['sub_category'] ?? '' }}">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-slate-400">Sub Sub Category</label>
                                <select data-field="sub_sub_category_id" name="items[{{ $itemIndex }}][sub_sub_category_id]"
                                    data-old-value="{{ $repairItem['sub_sub_category_id'] ?? '' }}"
                                    data-old-name="{{ $repairItem['sub_sub_category'] ?? '' }}"
                                    class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 disabled:bg-slate-100 disabled:cursor-not-allowed"
                                    disabled>
                                    <option value="">Select sub sub category</option>
                                </select>
                                <input type="hidden" data-field="sub_sub_category" name="items[{{ $itemIndex }}][sub_sub_category]" value="{{ $repairItem['sub_sub_category'] ?? '' }}">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-slate-400">Repair Price</label>
                                <input type="number" step="0.01" min="0" data-field="price" name="items[{{ $itemIndex }}][price]" value="{{ $repairItem['price'] ?? '' }}"
                                    class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500"
                                    placeholder="0.00">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-slate-400">Estimated Quantity</label>
                                <input type="number" min="1" data-field="quantity" name="items[{{ $itemIndex }}][quantity]" value="{{ $repairItem['quantity'] ?? 1 }}" required
                                    class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-slate-400">Status</label>
                                <select data-field="status" name="items[{{ $itemIndex }}][status]" required class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                                    @foreach ($statusOptions as $status => $label)
                                        <option value="{{ $status }}" {{ ($repairItem['status'] ?? \App\Models\RepairEntry::STATUS_REPAIR_SERVICES) === $status ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-slate-400">Operating System</label>
                                <input type="text" data-field="operating_system" name="items[{{ $itemIndex }}][operating_system]" value="{{ $repairItem['operating_system'] ?? '' }}"
                                    class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500"
                                    placeholder="Windows 11, macOS">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-slate-400">Put Away Location</label>
                                <input type="text" data-field="put_away_location" name="items[{{ $itemIndex }}][put_away_location]" value="{{ $repairItem['put_away_location'] ?? '' }}"
                                    class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500"
                                    placeholder="Warehouse shelf">
                            </div>

                            <div class="space-y-1 md:col-span-2">
                                <label class="text-[10px] font-bold uppercase text-slate-400">Device Specs</label>
                                <textarea data-field="device_specs" name="items[{{ $itemIndex }}][device_specs]" rows="3"
                                    class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500"
                                    placeholder="RAM, storage, processor, condition">{{ $repairItem['device_specs'] ?? '' }}</textarea>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-slate-400">Notes</label>
                            <textarea data-field="notes" name="items[{{ $itemIndex }}][notes]" rows="3"
                                class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm italic focus:ring-2 focus:ring-blue-500"
                                placeholder="Internal repair notes">{{ $repairItem['notes'] ?? '' }}</textarea>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-bold uppercase text-slate-400">Repair Photos (<span data-photo-count>0</span>)</label>
                            <div data-photos-preview class="flex flex-wrap gap-3">
                                <label data-add-photos class="cursor-pointer bg-blue-50 text-blue-600 w-20 h-20 rounded-xl flex flex-col items-center justify-center text-[10px] font-bold border-2 border-dashed border-blue-200 hover:bg-blue-100 transition-all">
                                    <i class="fas fa-camera mb-1 text-lg"></i>
                                    <span>Add</span>
                                    <input type="file" data-photo-input name="items[{{ $itemIndex }}][photos][]" multiple accept="image/*" class="hidden">
                                </label>
                            </div>
                        </div>

                        <div data-add-item-wrapper>
                            <button type="button" data-add-repair-item
                                class="w-full rounded-xl border-2 border-dashed border-blue-200 bg-blue-50 px-4 py-3 text-sm font-black text-blue-700 hover:bg-blue-100 transition-all">
                                <i class="fas fa-plus mr-2"></i>
                                Add Another Item To This Customer
                            </button>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-bold uppercase text-slate-400">Generate Barcode</label>
                            <div class="bg-blue-50/50 border border-blue-100 rounded-lg px-5 py-3 text-blue-600 text-[13px]">
                                Generate a unique barcode for this repair item.
                            </div>

                            <button type="button" data-generate-barcode
                                class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold text-sm flex justify-center items-center hover:bg-blue-700 transition-all">
                                <i class="fas fa-qrcode mr-2 text-lg"></i>
                                Generate Barcode
                            </button>

                            <div data-barcode-display class="hidden">
                                <div class="max-w-xs mx-auto border border-slate-100 rounded-2xl py-6 px-4 flex flex-col items-center bg-slate-50/40">
                                    <p data-barcode-number class="text-xl font-bold text-slate-800 mb-4">---</p>
                                    <div class="bg-white p-4 border border-slate-200 rounded-xl mb-6 text-center shadow-sm w-full">
                                        <img data-barcode-image src="" class="h-16 md:h-20 max-w-full mx-auto mb-2" alt="barcode">
                                        <p data-barcode-text class="font-mono text-[13px] md:text-[15px] text-slate-800 font-bold uppercase">---</p>
                                    </div>
                                    <button type="button" data-use-barcode
                                        class="mt-2 w-full bg-blue-600 text-white py-2 rounded-lg font-bold text-sm hover:bg-blue-700 transition-all">
                                        <i class="fas fa-check mr-2"></i>
                                        Use This Barcode
                                    </button>
                                    <button type="button" data-print-label
                                        class="mt-2 w-full bg-slate-600 text-white py-2 rounded-lg font-bold text-sm hover:bg-slate-700 transition-all">
                                        <i class="fas fa-print mr-2"></i>
                                        Print Label
                                    </button>
                                </div>
                            </div>

                            <input type="hidden" data-field="item_barcode" name="items[{{ $itemIndex }}][item_barcode]" value="{{ $repairItem['item_barcode'] ?? '' }}">
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-4 flex flex-col sm:flex-row justify-end gap-3 sticky bottom-0 bg-white border-t mt-auto py-4">
                <button type="button" onclick="closeModal()" class="px-6 py-3 text-xs font-bold text-slate-500 order-2 sm:order-1">Cancel</button>
                <button type="submit" data-repair-submit class="px-10 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-lg shadow-blue-200 order-1 sm:order-2 flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    Save Repair Entries
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    (function() {
        const modal = document.getElementById('addEntryModal');
        const itemsContainer = document.getElementById('repair-items');
        const categoryUrl = '/admin/inventory/categories-tree';
        const barcodeUrl = @json(route('admin.repair.entries.generate-barcode'));

        if (!modal || !itemsContainer) return;

        let categoryTree = [];
        let categoryMap = {};
        let categoryPromise = null;

        function escapeHtml(value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function flatten(categories) {
            categories.forEach((category) => {
                categoryMap[String(category.id)] = category;

                if (Array.isArray(category.children)) {
                    flatten(category.children);
                }
            });
        }

        function loadCategories() {
            if (categoryPromise) return categoryPromise;

            categoryPromise = fetch(categoryUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    categoryTree = data.categories || [];
                    categoryMap = {};
                    flatten(categoryTree);
                    return categoryTree;
                })
                .catch((error) => {
                    console.error('Failed to load repair categories:', error);
                    return [];
                });

            return categoryPromise;
        }

        function resetSelect(select, placeholder) {
            select.innerHTML = '';
            const option = document.createElement('option');
            option.value = '';
            option.textContent = placeholder;
            select.appendChild(option);
        }

        function populate(select, categories, placeholder) {
            resetSelect(select, placeholder);

            categories.forEach((category) => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                select.appendChild(option);
            });
        }

        function findByName(categories, name) {
            if (!name) return null;
            const normalizedName = String(name).toLowerCase();

            for (const category of categories) {
                if (String(category.name).toLowerCase() === normalizedName) {
                    return category;
                }

                if (Array.isArray(category.children)) {
                    const match = findByName(category.children, name);
                    if (match) return match;
                }
            }

            return null;
        }

        function setHiddenName(input, select) {
            if (!input || !select) return;
            input.value = select.value && select.selectedIndex >= 0 ? select.options[select.selectedIndex].text : '';
        }

        function setupRepairCategorySelector(card) {
            if (card.dataset.categorySetup === '1') return;

            const categorySelect = card.querySelector('[data-field="category_id"]');
            const subCategorySelect = card.querySelector('[data-field="sub_category_id"]');
            const subSubCategorySelect = card.querySelector('[data-field="sub_sub_category_id"]');
            const categoryName = card.querySelector('[data-field="category"]');
            const subCategoryName = card.querySelector('[data-field="sub_category"]');
            const subSubCategoryName = card.querySelector('[data-field="sub_sub_category"]');

            if (!categorySelect || !subCategorySelect || !subSubCategorySelect) return;

            card.dataset.categorySetup = '1';

            const oldValues = {
                categoryId: categorySelect.dataset.oldValue || categorySelect.value || '',
                subCategoryId: subCategorySelect.dataset.oldValue || subCategorySelect.value || '',
                subSubCategoryId: subSubCategorySelect.dataset.oldValue || subSubCategorySelect.value || '',
                category: categorySelect.dataset.oldName || (categoryName ? categoryName.value : ''),
                subCategory: subCategorySelect.dataset.oldName || (subCategoryName ? subCategoryName.value : ''),
                subSubCategory: subSubCategorySelect.dataset.oldName || (subSubCategoryName ? subSubCategoryName.value : ''),
            };

            function populateSubSubCategories(parentId, selectedValue = '') {
                const category = categoryMap[String(parentId)];
                const children = category && Array.isArray(category.children) ? category.children : [];

                populate(subSubCategorySelect, children, 'Select sub sub category');
                subSubCategorySelect.disabled = children.length === 0;
                subSubCategorySelect.value = selectedValue || '';
                setHiddenName(subSubCategoryName, subSubCategorySelect);
            }

            function populateSubCategories(parentId, selectedValue = '', selectedSubSubValue = '') {
                const category = categoryMap[String(parentId)];
                const children = category && Array.isArray(category.children) ? category.children : [];

                populate(subCategorySelect, children, 'Select sub category');
                subCategorySelect.disabled = children.length === 0;
                subCategorySelect.value = selectedValue || '';
                setHiddenName(subCategoryName, subCategorySelect);
                populateSubSubCategories(subCategorySelect.value, selectedSubSubValue);
            }

            categorySelect.addEventListener('change', function() {
                setHiddenName(categoryName, categorySelect);
                if (subCategoryName) subCategoryName.value = '';
                if (subSubCategoryName) subSubCategoryName.value = '';
                populateSubCategories(categorySelect.value);
            });

            subCategorySelect.addEventListener('change', function() {
                setHiddenName(subCategoryName, subCategorySelect);
                if (subSubCategoryName) subSubCategoryName.value = '';
                populateSubSubCategories(subCategorySelect.value);
            });

            subSubCategorySelect.addEventListener('change', function() {
                setHiddenName(subSubCategoryName, subSubCategorySelect);
            });

            loadCategories().then(() => {
                populate(categorySelect, categoryTree, 'Select category');

                const selectedCategory = oldValues.categoryId || (findByName(categoryTree, oldValues.category) || {}).id || '';
                const selectedSubCategory = oldValues.subCategoryId || (findByName(categoryTree, oldValues.subCategory) || {}).id || '';
                const selectedSubSubCategory = oldValues.subSubCategoryId || (findByName(categoryTree, oldValues.subSubCategory) || {}).id || '';

                if (selectedCategory) {
                    categorySelect.value = selectedCategory;
                    setHiddenName(categoryName, categorySelect);
                    populateSubCategories(selectedCategory, selectedSubCategory, selectedSubSubCategory);
                    setHiddenName(subCategoryName, subCategorySelect);
                    setHiddenName(subSubCategoryName, subSubCategorySelect);
                }
            });
        }

        function setupRepairPhotoUpload(card) {
            if (card.dataset.photoSetup === '1') return;

            const input = card.querySelector('[data-photo-input]');
            const preview = card.querySelector('[data-photos-preview]');
            const addButton = preview ? preview.querySelector('[data-add-photos]') : null;
            const count = card.querySelector('[data-photo-count]');
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

            if (!input || !preview || !addButton || typeof DataTransfer === 'undefined') return;

            card.dataset.photoSetup = '1';
            let selectedFiles = new DataTransfer();

            function renderPreview() {
                preview.querySelectorAll('[data-photo-wrapper]').forEach((item) => item.remove());

                Array.from(selectedFiles.files).forEach((file, index) => {
                    const reader = new FileReader();
                    const wrapper = document.createElement('div');
                    wrapper.setAttribute('data-photo-wrapper', 'true');
                    wrapper.className = 'relative w-24 h-24 rounded-2xl overflow-hidden border-2 border-blue-400 shadow-md group bg-slate-50';

                    const image = document.createElement('img');
                    image.className = 'w-full h-full object-cover';
                    image.alt = file.name;
                    wrapper.appendChild(image);

                    const removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.className = 'absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full text-xs flex items-center justify-center hover:bg-red-600 transition-all opacity-0 group-hover:opacity-100';
                    removeButton.innerHTML = '<i class="fas fa-times text-[8px]"></i>';
                    removeButton.addEventListener('click', function(event) {
                        event.preventDefault();
                        removePhoto(index);
                    });
                    wrapper.appendChild(removeButton);

                    reader.onload = function(event) {
                        image.src = event.target.result;
                    };
                    reader.readAsDataURL(file);

                    preview.insertBefore(wrapper, addButton);
                });

                input.files = selectedFiles.files;
                if (count) count.textContent = selectedFiles.files.length;
            }

            function addFiles(files) {
                const incomingFiles = Array.from(files || []);

                for (const file of incomingFiles) {
                    if (!allowedTypes.includes(file.type)) {
                        alert('Only JPG, PNG, and WebP images are allowed.');
                        input.value = '';
                        input.files = selectedFiles.files;
                        return;
                    }
                }

                incomingFiles.forEach((file) => {
                    selectedFiles.items.add(file);
                });

                input.value = '';
                renderPreview();
            }

            function removePhoto(index) {
                const files = Array.from(selectedFiles.files);
                files.splice(index, 1);
                selectedFiles = new DataTransfer();
                files.forEach((file) => selectedFiles.items.add(file));
                renderPreview();
            }

            input.addEventListener('change', function(event) {
                addFiles(event.target.files);
            });

            preview.addEventListener('dragover', function(event) {
                event.preventDefault();
                addButton.classList.add('bg-blue-100');
            });

            preview.addEventListener('dragleave', function() {
                addButton.classList.remove('bg-blue-100');
            });

            preview.addEventListener('drop', function(event) {
                event.preventDefault();
                addButton.classList.remove('bg-blue-100');
                addFiles(event.dataTransfer.files);
            });
        }

        function fieldValue(scope, fieldName) {
            const field = scope.querySelector(`[data-field="${fieldName}"]`);
            return field ? String(field.value || '').trim() : '';
        }

        function selectText(scope, fieldName) {
            const select = scope.querySelector(`[data-field="${fieldName}"]`);
            if (!select || !select.value || select.selectedIndex < 0) return '';
            return select.options[select.selectedIndex].text || '';
        }

        function setupRepairBarcode(card) {
            if (card.dataset.barcodeSetup === '1') return;

            const generateButton = card.querySelector('[data-generate-barcode]');
            const display = card.querySelector('[data-barcode-display]');
            const numberEl = card.querySelector('[data-barcode-number]');
            const textEl = card.querySelector('[data-barcode-text]');
            const imageEl = card.querySelector('[data-barcode-image]');
            const useButton = card.querySelector('[data-use-barcode]');
            const printButton = card.querySelector('[data-print-label]');
            const hiddenInput = card.querySelector('[data-field="item_barcode"]');

            if (!generateButton) return;

            card.dataset.barcodeSetup = '1';

            async function generateBarcode() {
                generateButton.disabled = true;
                generateButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Generating...';

                try {
                    const response = await fetch(barcodeUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        throw new Error(data.error || data.message || 'Failed to generate barcode');
                    }

                    const code = data.number || data.barcode || data.barcode_number || '';
                    const image = data.base64 || (data.barcodeBase64 ? 'data:image/png;base64,' + data.barcodeBase64 : '');

                    if (hiddenInput) hiddenInput.value = code;
                    if (numberEl) numberEl.textContent = code;
                    if (textEl) textEl.textContent = code;
                    if (imageEl && image) imageEl.src = image;
                    if (display) display.classList.remove('hidden');
                } catch (error) {
                    console.error(error);
                    alert(error.message || 'Failed to generate barcode');
                } finally {
                    generateButton.disabled = false;
                    generateButton.innerHTML = '<i class="fas fa-qrcode mr-2 text-lg"></i> Generate Barcode';
                }
            }

            generateButton.addEventListener('click', generateBarcode);

            if (useButton) {
                useButton.addEventListener('click', function(event) {
                    event.preventDefault();

                    if (hiddenInput && numberEl && numberEl.textContent !== '---') {
                        hiddenInput.value = numberEl.textContent;
                    }

                    useButton.innerHTML = '<i class="fas fa-check mr-2"></i> Barcode Selected';
                    setTimeout(() => {
                        useButton.innerHTML = '<i class="fas fa-check mr-2"></i> Use This Barcode';
                    }, 900);
                });
            }

            if (printButton) {
                printButton.addEventListener('click', function() {
                    printRepairLabel(card);
                });
            }
        }

        function printRepairLabel(card) {
            const barcode = fieldValue(card, 'item_barcode') || (card.querySelector('[data-barcode-number]') ? card.querySelector('[data-barcode-number]').textContent : '');
            const barcodeImage = card.querySelector('[data-barcode-image]') ? card.querySelector('[data-barcode-image]').src : '';

            if (!barcode || barcode === '---' || !barcodeImage) {
                alert('Generate a barcode before printing the label.');
                return;
            }

            const technician = document.getElementById('repair-technician')?.value || 'N/A';
            const customerName = document.getElementById('repair-customer-name')?.value || 'N/A';
            const customerPhone = document.getElementById('repair-customer-phone')?.value || 'N/A';
            const customerEmail = document.getElementById('repair-customer-email')?.value || 'N/A';
            const deviceType = fieldValue(card, 'brand_category') || 'N/A';
            const brandModel = fieldValue(card, 'device_type') || 'N/A';
            const category = selectText(card, 'category_id') || fieldValue(card, 'category') || 'N/A';
            const subCategory = selectText(card, 'sub_category_id') || fieldValue(card, 'sub_category');
            const price = fieldValue(card, 'price') || 'N/A';
            const quantity = fieldValue(card, 'quantity') || 'N/A';
            const deviceSpecs = fieldValue(card, 'device_specs') || 'N/A';
            const notes = fieldValue(card, 'notes') || 'N/A';
            const os = fieldValue(card, 'operating_system') || 'N/A';
            const printedAt = `Denver: ${new Date().toLocaleString('en-US', {
                timeZone: 'America/Denver',
                month: '2-digit',
                day: '2-digit',
                year: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true,
                timeZoneName: 'short',
            })}`;

            const printWindow = window.open('', '_blank', 'width=420,height=320');
            if (!printWindow) {
                alert('Allow pop-ups to print the label.');
                return;
            }

            const categoryLine = `${category}${subCategory ? ' > ' + subCategory.substring(0, 24) : ''}`;
            const htmlContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Repair Label</title>
                    <style>
                        * {
                            margin: 0;
                            padding: 0;
                            box-sizing: border-box;
                        }
                        @page {
                            size: 3in 3in;
                            margin: 0;
                        }
                        body {
                            font-family: Arial, Helvetica, sans-serif;
                            width: 3in;
                            height: 3in;
                            background: white;
                            color: #111827;
                        }
                        .label-container {
                            width: 3in;
                            height: 3in;
                            margin: 0 auto;
                            background: white;
                            padding: 0.08in;
                            border: 1px solid #111;
                            display: flex;
                            flex-direction: column;
                            overflow: hidden;
                        }
                        .label-header {
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                            border-bottom: 1px solid #0071e3;
                            padding-bottom: 0.035in;
                            margin-bottom: 0.04in;
                        }
                        .label-header h1 {
                            font-size: 9px;
                            color: #111827;
                            font-weight: 900;
                            letter-spacing: 0;
                            line-height: 1;
                        }
                        .barcode-section {
                            text-align: center;
                            margin: 0;
                            padding: 0.035in 0.04in 0.025in;
                            background: #f8fafc;
                            border: 1px solid #d1d5db;
                        }
                        .barcode-section img {
                            max-width: 100%;
                            height: auto;
                            max-height: 0.32in;
                            margin-bottom: 0.02in;
                        }
                        .barcode-number {
                            font-family: "Courier New", monospace;
                            font-size: 7px;
                            font-weight: bold;
                            letter-spacing: 0;
                            color: #111827;
                            line-height: 1;
                        }
                        .detail-grid {
                            display: grid;
                            grid-template-columns: 1fr 1fr;
                            gap: 0.025in 0.05in;
                            margin-top: 0.04in;
                            flex: 1;
                            min-height: 0;
                        }
                        .detail-item {
                            min-width: 0;
                            border-bottom: 1px solid #e5e7eb;
                            padding-bottom: 0.012in;
                        }
                        .detail-item.wide {
                            grid-column: 1 / -1;
                        }
                        .detail-label {
                            display: block;
                            font-weight: bold;
                            color: #0071e3;
                            text-transform: uppercase;
                            letter-spacing: 0;
                            font-size: 5.5px;
                            line-height: 1.05;
                        }
                        .detail-value {
                            display: block;
                            font-weight: 600;
                            color: #111827;
                            font-size: 7px;
                            line-height: 1.12;
                            white-space: nowrap;
                            overflow: hidden;
                            text-overflow: ellipsis;
                        }
                        .footer {
                            text-align: center;
                            margin-top: 0.025in;
                            padding-top: 0.018in;
                            border-top: 1px dashed #ccc;
                            font-size: 5.5px;
                            color: #6b7280;
                            line-height: 1;
                        }
                        @media print {
                            html,
                            body {
                                width: 3in;
                                height: 3in;
                                background: white;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="label-container">
                        <div class="label-header">
                            <h1>REPAIR LABEL</h1>
                        </div>
                        <div class="barcode-section">
                            <img src="${escapeHtml(barcodeImage)}" alt="Barcode">
                            <div class="barcode-number">${escapeHtml(barcode)}</div>
                        </div>
                        <div class="detail-grid">
                            <div class="detail-item wide">
                                <span class="detail-label">Customer</span>
                                <span class="detail-value">${escapeHtml(customerName)}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Phone</span>
                                <span class="detail-value">${escapeHtml(customerPhone)}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Email</span>
                                <span class="detail-value">${escapeHtml(customerEmail)}</span>
                            </div>
                            <div class="detail-item wide">
                                <span class="detail-label">Brand & Model</span>
                                <span class="detail-value">${escapeHtml(brandModel)}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Device</span>
                                <span class="detail-value">${escapeHtml(deviceType)}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">OS</span>
                                <span class="detail-value">${escapeHtml(os)}</span>
                            </div>
                            <div class="detail-item wide">
                                <span class="detail-label">Specs</span>
                                <span class="detail-value">${escapeHtml(deviceSpecs.substring(0, 48))}</span>
                            </div>
                            <div class="detail-item wide">
                                <span class="detail-label">Notes</span>
                                <span class="detail-value">${escapeHtml(notes.substring(0, 56))}</span>
                            </div>
                            <div class="detail-item wide">
                                <span class="detail-label">Category</span>
                                <span class="detail-value">${escapeHtml(categoryLine)}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Price</span>
                                <span class="detail-value">${price === 'N/A' ? 'N/A' : '$' + escapeHtml(price)}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Qty</span>
                                <span class="detail-value">${escapeHtml(quantity)} Unt</span>
                            </div>
                            <div class="detail-item wide">
                                <span class="detail-label">Technician</span>
                                <span class="detail-value">${escapeHtml(technician)}</span>
                            </div>
                        </div>
                        <div class="footer">${escapeHtml(printedAt)}</div>
                    </div>
                </body>
                </html>
            `;

            printWindow.document.write(htmlContent);
            printWindow.document.close();

            setTimeout(() => {
                printWindow.print();
            }, 300);
        }

        function resetRepairItemCard(card) {
            delete card.dataset.categorySetup;
            delete card.dataset.photoSetup;
            delete card.dataset.barcodeSetup;

            card.querySelectorAll('[data-old-value]').forEach((field) => {
                field.dataset.oldValue = '';
            });
            card.querySelectorAll('[data-old-name]').forEach((field) => {
                field.dataset.oldName = '';
            });
            card.querySelectorAll('[data-remove-setup]').forEach((field) => {
                delete field.dataset.removeSetup;
            });
            card.querySelectorAll('[data-photo-wrapper]').forEach((item) => item.remove());

            card.querySelectorAll('input, textarea, select').forEach((field) => {
                const fieldName = field.dataset.field || '';

                if (field.type === 'file') {
                    field.value = '';
                    return;
                }

                if (field.tagName === 'SELECT') {
                    field.value = '';
                    field.disabled = fieldName === 'sub_category_id' || fieldName === 'sub_sub_category_id';
                    return;
                }

                if (field.type === 'hidden') {
                    field.value = '';
                    return;
                }

                field.value = fieldName === 'quantity' ? '1' : '';
            });

            const statusSelect = card.querySelector('[data-field="status"]');
            if (statusSelect) statusSelect.value = @json(\App\Models\RepairEntry::STATUS_REPAIR_SERVICES);

            const count = card.querySelector('[data-photo-count]');
            if (count) count.textContent = '0';

            const display = card.querySelector('[data-barcode-display]');
            if (display) display.classList.add('hidden');

            const numberEl = card.querySelector('[data-barcode-number]');
            const textEl = card.querySelector('[data-barcode-text]');
            const imageEl = card.querySelector('[data-barcode-image]');
            if (numberEl) numberEl.textContent = '---';
            if (textEl) textEl.textContent = '---';
            if (imageEl) imageEl.removeAttribute('src');

            const generateButton = card.querySelector('[data-generate-barcode]');
            const useButton = card.querySelector('[data-use-barcode]');
            if (generateButton) {
                generateButton.disabled = false;
                generateButton.innerHTML = '<i class="fas fa-qrcode mr-2 text-lg"></i> Generate Barcode';
            }
            if (useButton) {
                useButton.innerHTML = '<i class="fas fa-check mr-2"></i> Use This Barcode';
            }
        }

        function renumberRepairItems() {
            const cards = Array.from(itemsContainer.querySelectorAll('[data-repair-item-card]'));

            cards.forEach((card, index) => {
                card.dataset.repairItemIndex = String(index);

                const title = card.querySelector('[data-item-title]');
                if (title) title.textContent = `Repair Item #${index + 1}`;

                const removeButton = card.querySelector('[data-remove-item]');
                if (removeButton) {
                    removeButton.classList.toggle('hidden', cards.length === 1);
                }

                const addItemWrapper = card.querySelector('[data-add-item-wrapper]');
                if (addItemWrapper) {
                    addItemWrapper.classList.toggle('hidden', index !== cards.length - 1);
                }

                card.querySelectorAll('[name]').forEach((field) => {
                    field.name = field.name.replace(/items\[\d+\]/, `items[${index}]`);
                });
            });
        }

        function setupRepairItemCard(card) {
            setupRepairCategorySelector(card);
            setupRepairPhotoUpload(card);
            setupRepairBarcode(card);

            const removeButton = card.querySelector('[data-remove-item]');
            if (removeButton && removeButton.dataset.removeSetup !== '1') {
                removeButton.dataset.removeSetup = '1';
                removeButton.addEventListener('click', function() {
                    if (itemsContainer.querySelectorAll('[data-repair-item-card]').length === 1) return;
                    card.remove();
                    renumberRepairItems();
                });
            }
        }

        function setupSubmitLoading() {
            const form = document.getElementById('repair-entry-form');
            const submitButton = form ? form.querySelector('[data-repair-submit]') : null;

            if (!form || !submitButton || form.dataset.submitLoadingSetup === '1') return;

            form.dataset.submitLoadingSetup = '1';

            form.addEventListener('submit', function() {
                submitButton.disabled = true;
                submitButton.classList.add('opacity-70', 'cursor-not-allowed');
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            });
        }

        function initializeRepairItems() {
            itemsContainer.querySelectorAll('[data-repair-item-card]').forEach((card) => {
                setupRepairItemCard(card);
            });
            renumberRepairItems();
            setupSubmitLoading();
        }

        function addRepairItem() {
            const sourceCard = itemsContainer.querySelector('[data-repair-item-card]');
            if (!sourceCard) return;

            const newCard = sourceCard.cloneNode(true);
            resetRepairItemCard(newCard);
            itemsContainer.appendChild(newCard);
            setupRepairItemCard(newCard);
            renumberRepairItems();
            newCard.scrollIntoView({
                behavior: 'smooth',
                block: 'start',
            });
        }

        itemsContainer.addEventListener('click', function(event) {
            const addButton = event.target.closest('[data-add-repair-item]');
            if (!addButton || !itemsContainer.contains(addButton)) return;

            event.preventDefault();
            addRepairItem();
        });

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeRepairItems);
        } else {
            initializeRepairItems();
        }
    })();
</script>

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof openModal === 'function') {
                openModal();
            }
        });
    </script>
@endif
