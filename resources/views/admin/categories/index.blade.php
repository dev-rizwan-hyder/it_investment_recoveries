@extends('layouts.admin')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            height: 42px;
            border: 1px solid #cbd5e1;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px;
            color: #0f172a;
            padding-left: 12px;
            padding-right: 28px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 8px;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #3b82f6;
        }

        .select2-container--default .select2-results__option:hover {
            background-color: #dbeafe;
        }

        .select2-dropdown {
            border: 1px solid #cbd5e1;
            border-radius: 0.5rem;
        }

        .text-blue-600 {
            color: #2563eb;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 md:p-6">
            <h1 class="text-xl md:text-2xl font-bold text-slate-800">Category Management</h1>
            <p class="text-sm text-slate-500 mt-1">
                Create main categories, subcategories, and sub-subcategories by choosing a parent category.
            </p>
        </div>

        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl p-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4">
                <ul class="list-disc ml-5 space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-2xl p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Categories</h2>
                    <p class="text-sm text-slate-500">Manage parent, sub and sub-sub categories</p>
                </div>
                <div class="flex items-center gap-3">
                    <form method="GET" action="{{ route('admin.categories.index') }}" class="flex gap-2">
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                            class="w-56 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Search categories...">
                        <button type="submit"
                            class="bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-lg px-4 py-2 transition">
                            Search
                        </button>
                        @if (!empty($search))
                            <a href="{{ route('admin.categories.index') }}"
                                class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg px-4 py-2 transition">
                                Clear
                            </a>
                        @endif
                    </form>

                    <button id="openCategoryModal" type="button" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg px-4 py-2 transition">
                        Add Category
                    </button>
                </div>
            </div>

            @if ($categories->isEmpty())
                @if (!empty($search))
                    <div class="text-center py-8">
                        <p class="text-slate-600 text-sm mb-3">No categories found matching "<strong>{{ $search }}</strong>"</p>
                    </div>
                @else
                    <p class="text-slate-500 text-sm">No categories found yet.</p>
                @endif
            @else
                <div class="space-y-4">
                    @foreach ($categories as $category)
                        <div class="flex items-start justify-between border border-slate-200 rounded-xl p-4">
                            <div>
                                <p class="font-semibold text-slate-800">{{ $category->name }}</p>
                                @if ($category->children->isNotEmpty())
                                    <ul class="mt-2 space-y-1 text-sm text-slate-700">
                                        @foreach ($category->children as $subCategory)
                                            <li class="flex items-center justify-between">
                                                <div>
                                                    <span class="font-medium">- {{ $subCategory->name }}</span>
                                                    @if ($subCategory->children->isNotEmpty())
                                                        <ul class="ml-5 mt-1 space-y-1 text-sm text-slate-600">
                                                            @foreach ($subCategory->children as $subSubCategory)
                                                                <li class="flex items-center justify-between">
                                                                    <span>-- {{ $subSubCategory->name }}</span>
                                                                    <form method="POST" action="{{ route('admin.categories.destroy', $subSubCategory->id) }}" class="delete-form inline-block ml-2">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" onclick="return confirm('Delete this sub-subcategory?')" data-type="sub-subcategory"
                                                                            class="text-xs text-red-600 hover:text-red-800" aria-label="Delete sub-subcategory">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3" />
                                                                            </svg>
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                      <form method="POST" action="{{ route('admin.categories.destroy', $subCategory->id) }}" class="delete-form">
                                                          @csrf
                                                          @method('DELETE')
                                                          <button type="submit" onclick="return confirm('Delete this subcategory?')" data-type="subcategory"
                                                            class="text-sm text-red-600 hover:text-red-800" aria-label="Delete subcategory">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                  <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" class="delete-form">
                                      @csrf
                                      @method('DELETE')
                                      <button type="submit" onclick="return confirm('Delete this category?')" data-type="category"
                                        class="text-sm text-red-600 hover:text-red-800" aria-label="Delete category">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>

        <!-- Modal -->
        <div id="categoryModal" class="fixed inset-0 z-50 hidden items-center justify-center">
            <div id="modalOverlay" class="absolute inset-0 bg-black opacity-40"></div>
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-xl z-10 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold">Add Category</h3>
                    <button id="closeCategoryModal" class="text-slate-500 hover:text-slate-700">Close</button>
                </div>

                <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4" id="categoryForm">
                    @csrf

                    <div>
                        <label for="parent_category" class="block text-sm font-semibold text-slate-700 mb-1">Parent Category</label>
                        <select id="parent_category" name="parent_category" class="category-select w-full" data-level="1">
                            <option value="">-- No Parent (Main Category) --</option>
                            @foreach ($allCategories->whereNull('parent_id')->sortBy('name') as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-500 mt-1">Select existing or type to create new</p>
                    </div>

                    <div>
                        <label for="sub_category" class="block text-sm font-semibold text-slate-700 mb-1">Sub Category</label>
                        <select id="sub_category" name="sub_category" class="category-select w-full" data-level="2">
                            <option value="">-- Select Sub Category --</option>
                        </select>
                        <p class="text-xs text-slate-500 mt-1">Select existing or type to create new</p>
                    </div>

                    <div>
                        <label for="sub_sub_category" class="block text-sm font-semibold text-slate-700 mb-1">Sub Sub Category</label>
                        <select id="sub_sub_category" name="sub_sub_category" class="category-select w-full" data-level="3">
                            <option value="">-- Select Sub Sub Category --</option>
                        </select>
                        <p class="text-xs text-slate-500 mt-1">Select existing or type to create new</p>
                    </div>

                    <input type="hidden" id="final_parent_id" name="parent_id" value="">

                    <div class="flex items-center gap-3">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg px-4 py-2 transition">Save</button>
                        <button type="button" id="cancelCategoryModal" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-lg px-4 py-2 transition">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        const allCategoriesData = {!! json_encode($allCategories->toArray()) !!};

        function getCategoriesByParent(parentId) {
            return allCategoriesData.filter(cat => cat.parent_id == parentId);
        }

        function updateSubcategoryOptions(parentId, targetSelectId) {
            const $select = $('#' + targetSelectId);
            const subcategories = getCategoriesByParent(parentId);
            
            $select.empty();
            $select.append('<option value="">-- Select ' + targetSelectId.replace('_', ' ').toUpperCase() + ' --</option>');
            
            subcategories.forEach(subcat => {
                $select.append(`<option value="${subcat.id}">${subcat.name}</option>`);
            });
            
            $select.trigger('change');
        }

        function updateFinalParentId() {
            const subSubCategoryId = $('#sub_sub_category').val();
            const subCategoryId = $('#sub_category').val();
            const parentCategoryId = $('#parent_category').val();
            
            // Determine the actual parent based on the hierarchy
            let finalParentId = null;
            
            if (subSubCategoryId) {
                finalParentId = subCategoryId;
            } else if (subCategoryId) {
                finalParentId = parentCategoryId;
            } else if (parentCategoryId) {
                finalParentId = null; // New category under parent
            }
            
            $('#final_parent_id').val(finalParentId || '');
        }

        $(document).ready(function() {
            // Initialize Select2 for all category selects with creatable tags
            function initCategorySelects(dropdownParent) {
                $('.category-select').select2({
                    width: '100%',
                    tags: true,
                    tokenSeparators: [','],
                    createTag: function(params) {
                        var term = $.trim(params.term);
                        if (term === '') {
                            return null;
                        }
                        return {
                            id: 'new:' + term,
                            text: term,
                            isNew: true
                        }
                    },
                    insertTag: function(data, tag) {
                        // place the created tag at the top so it's clickable when there are no results
                        data.unshift(tag);
                    },
                    templateResult: function(data) {
                        if (!data.element) {
                            return data.text;
                        }
                        if (data.isNew) {
                            return $('<span class="text-blue-600">✚ ' + data.text + ' (create)</span>');
                        }
                        return data.text;
                    },
                    escapeMarkup: function(markup) { return markup; },
                    dropdownParent: dropdownParent || $(document.body)
                });
            }

            // initialize for page (non-modal)
            initCategorySelects();

            // Handle parent category change
            $('#parent_category').on('change', function() {
                const parentId = $(this).val();
                
                if (parentId && !parentId.startsWith('new:')) {
                    updateSubcategoryOptions(parentId, 'sub_category');
                } else {
                    $('#sub_category').empty().append('<option value="">-- Select Sub Category --</option>');
                    $('#sub_sub_category').empty().append('<option value="">-- Select Sub Sub Category --</option>');
                }
                updateFinalParentId();
            });

            // Handle sub category change
            $('#sub_category').on('change', function() {
                const subCategoryId = $(this).val();
                
                if (subCategoryId && !subCategoryId.startsWith('new:')) {
                    updateSubcategoryOptions(subCategoryId, 'sub_sub_category');
                } else {
                    $('#sub_sub_category').empty().append('<option value="">-- Select Sub Sub Category --</option>');
                }
                updateFinalParentId();
            });

            // Handle sub sub category change
            $('#sub_sub_category').on('change', function() {
                updateFinalParentId();
            });

            // Handle form submission
            $('#categoryForm').on('submit', function(e) {
                e.preventDefault();

                const parentCategoryVal = $('#parent_category').val();
                const subCategoryVal = $('#sub_category').val();
                const subSubCategoryVal = $('#sub_sub_category').val();

                if (!parentCategoryVal && !subCategoryVal && !subSubCategoryVal) {
                    alert('Please select or create a category in one of the dropdowns.');
                    return;
                }

                // Ensure final_parent_id is set correctly
                updateFinalParentId();

                // Submit the form with AJAX to handle new categories
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response && response.success) {
                            // close modal if open
                            try { closeModal(); } catch (e) {}

                            // show inline success message and keep it for 3 seconds
                            const msg = `<div id="category-success" class="bg-green-50 border border-green-200 text-green-700 rounded-xl p-4 mb-4">${response.message}</div>`;
                            $('.max-w-7xl').prepend(msg);
                            setTimeout(function() {
                                $('#category-success').fadeOut(200, function() { $(this).remove(); });
                                location.reload();
                            }, 3000);
                        } else {
                            window.location.href = '{{ route("admin.categories.index") }}';
                        }
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            const msg = `<div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-4">${xhr.responseJSON.message}</div>`;
                            $('.max-w-7xl').prepend(msg);
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            alert('Error: ' + Object.values(xhr.responseJSON.errors).flat().join(', '));
                        } else {
                            alert('An error occurred. Please try again.');
                        }
                    }
                });
            });

            // Restore old values if editing
            const oldParentCategory = $('#parent_category').data('old-value');
            if (oldParentCategory) {
                $('#parent_category').val(oldParentCategory).trigger('change');
            }

            // Modal open/close handlers
            function openModal() {
                $('#categoryModal').removeClass('hidden').addClass('flex');
                // initialize select2 dropdowns inside modal
                initCategorySelects($('#categoryModal'));
            }

            function closeModal() {
                $('#categoryModal').removeClass('flex').addClass('hidden');
            }

            $('#openCategoryModal').on('click', function() {
                openModal();
            });

            $('#closeCategoryModal, #cancelCategoryModal').on('click', function() {
                closeModal();
            });

            $('#modalOverlay').on('click', function() {
                closeModal();
            });

            // Handle delete forms via AJAX with 3-second message timeout
            $(document).on('submit', '.delete-form', function(e) {
                e.preventDefault();
                const form = $(this);
                const url = form.attr('action');

                $.ajax({
                    url: url,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response && response.success) {
                            const msg = `<div id="category-success" class="bg-green-50 border border-green-200 text-green-700 rounded-xl p-4 mb-4">${response.message}</div>`;
                            $('.max-w-7xl').prepend(msg);
                            setTimeout(function() {
                                $('#category-success').fadeOut(200, function() { $(this).remove(); });
                                location.reload();
                            }, 3000);
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON || {};
                        const message = response.message || 'An error occurred while deleting the category.';
                        const msg = `<div id="category-error" class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-4">${message}</div>`;
                        $('.max-w-7xl').prepend(msg);
                        setTimeout(function() {
                            $('#category-error').fadeOut(200, function() { $(this).remove(); });
                        }, 3000);
                    }
                });
            });
        });
    </script>
@endpush
