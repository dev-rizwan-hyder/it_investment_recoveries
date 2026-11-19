<!-- Category Hierarchy Selector Partial -->
<div class="space-y-1">
    <label class="text-[9px] font-bold uppercase text-slate-500">Category</label>
    <select id="inv-category-select" onchange="onCategoryChange()"
        class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
        <option value="">-- Select Category --</option>
    </select>
    <input type="hidden" id="inv-category-id" name="category_id">
</div>

<div class="space-y-1">
    <label class="text-[9px] font-bold uppercase text-slate-500">Sub Category</label>
    <select id="inv-subcategory-select" onchange="onSubCategoryChange()"
        class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 disabled:bg-slate-100 disabled:cursor-not-allowed"
        disabled>
        <option value="">-- Select Sub Category --</option>
    </select>
    <input type="hidden" id="inv-subcategory-id" name="sub_category_id">
</div>

<div class="space-y-1">
    <label class="text-[9px] font-bold uppercase text-slate-500">Sub Sub Category</label>
    <select id="inv-subsubcategory-select" onchange="onSubSubCategoryChange()"
        class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 disabled:bg-slate-100 disabled:cursor-not-allowed"
        disabled>
        <option value="">-- Select Sub Sub Category --</option>
    </select>
    <input type="hidden" id="inv-subsubcategory-id" name="sub_sub_category_id">
</div>

@push('scripts')
<script>
    // Global category data structure
    window.categoryHierarchyManager = {
        categories: [],
        categoryMap: {}, // for quick lookups
        isLoaded: false,
        loadingPromise: null,

        /**
         * Initialize category data - Always load from API
         */
        initialize: function() {
            if (!this.isLoaded) {
                return this.loadCategoriesFromAPI();
            }

            return Promise.resolve();
        },

        whenReady: function() {
            if (this.isLoaded) {
                return Promise.resolve();
            }

            if (!this.loadingPromise) {
                this.loadingPromise = this.loadCategoriesFromAPI();
            }

            return this.loadingPromise;
        },

        /**
         * Load categories from API endpoint
         */
        loadCategoriesFromAPI: function() {
            const self = this;
            console.log('Loading categories from API...');
            const apiUrl = '/admin/inventory/categories-tree';
            console.log('API URL:', apiUrl);

            this.loadingPromise = fetch(apiUrl)
                .then(response => {
                    console.log('API Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Categories data received:', data);
                    self.categories = data.categories || [];
                    self.isLoaded = true;
                    self.buildCategoryMap();
                    self.populateRootCategories();
                    console.log('Categories populated. Root categories count:', self.categories.filter(c =>
                        !c.parent_id).length);
                    console.log('Category map size:', Object.keys(self.categoryMap).length);
                    return self.categories;
                })
                .catch(error => {
                    console.error('Error loading categories:', error);
                    self.loadingPromise = null;
                    // Try to show a more helpful error
                    alert('Failed to load categories from ' + apiUrl +
                        '. Check browser console for details.');
                    throw error;
                });

            return this.loadingPromise;
        },

        /**
         * Build a map for quick category lookups
         */
        buildCategoryMap: function() {
            this.categoryMap = {};
            const flattenCategories = (cats) => {
                cats.forEach(cat => {
                    this.categoryMap[cat.id] = cat;
                    if (cat.children && cat.children.length > 0) {
                        flattenCategories(cat.children);
                    }
                });
            };
            flattenCategories(this.categories);
        },

        /**
         * Populate root categories
         */
        populateRootCategories: function() {
            const select = document.getElementById('inv-category-select');
            if (!select) {
                console.log('Select element not found');
                return;
            }

            // Only show root categories (no parent)
            const rootCategories = this.categories.filter(cat => !cat.parent_id);
            console.log('Root categories:', rootCategories);

            rootCategories.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.id;
                option.textContent = cat.name;
                option.dataset.id = cat.id;
                select.appendChild(option);
            });

            if (rootCategories.length === 0) {
                console.warn('No root categories found');
            }
        },

        /**
         * Handle category selection change
         */
        onCategoryChange: function() {
            const select = document.getElementById('inv-category-select');
            const categoryId = select.value;
            const categoryName = select.options[select.selectedIndex].text;

            // Clear dependent selects
            this.clearSubCategories();
            this.clearSubSubCategories();

            if (!categoryId) return;

            // Update hidden field
            document.getElementById('inv-category-id').value = categoryId;
            document.getElementById('inv-category').value = categoryName;

            // Get category and populate subcategories
            const category = this.categoryMap[categoryId];
            if (category && category.children && category.children.length > 0) {
                this.populateSubCategories(category.children);
                document.getElementById('inv-subcategory-select').disabled = false;
            } else {
                document.getElementById('inv-subcategory-select').disabled = true;
            }
        },

        /**
         * Populate subcategories
         */
        populateSubCategories: function(subCats) {
            const select = document.getElementById('inv-subcategory-select');
            this.clearSubCategories();

            subCats.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.id;
                option.textContent = cat.name;
                option.dataset.id = cat.id;
                option.dataset.children = cat.children ? cat.children.length : 0;
                select.appendChild(option);
            });

            // Trigger Select2 update
            if (typeof $ !== 'undefined' && $(select).hasClass('select2-hidden-accessible')) {
                $(select).trigger('change');
            }
        },

        /**
         * Handle sub-category selection change
         */
        onSubCategoryChange: function() {
            const select = document.getElementById('inv-subcategory-select');
            const subCategoryId = select.value;
            const subCategoryName = select.options[select.selectedIndex].text;

            // Clear dependent select
            this.clearSubSubCategories();

            if (!subCategoryId) return;

            // Update hidden field
            document.getElementById('inv-subcategory-id').value = subCategoryId;
            document.getElementById('inv-sub_category').value = subCategoryName;

            // Get subcategory and populate sub-subcategories
            const subCategory = this.categoryMap[subCategoryId];
            if (subCategory && subCategory.children && subCategory.children.length > 0) {
                this.populateSubSubCategories(subCategory.children);
                document.getElementById('inv-subsubcategory-select').disabled = false;
            } else {
                document.getElementById('inv-subsubcategory-select').disabled = true;
            }
        },

        /**
         * Populate sub-subcategories
         */
        populateSubSubCategories: function(subSubCats) {
            const select = document.getElementById('inv-subsubcategory-select');
            this.clearSubSubCategories();

            subSubCats.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.id;
                option.textContent = cat.name;
                option.dataset.id = cat.id;
                select.appendChild(option);
            });

            // Trigger Select2 update
            if (typeof $ !== 'undefined' && $(select).hasClass('select2-hidden-accessible')) {
                $(select).trigger('change');
            }
        },

        /**
         * Handle sub-sub-category selection change
         */
        onSubSubCategoryChange: function() {
            const select = document.getElementById('inv-subsubcategory-select');
            const subSubCategoryId = select.value;
            const subSubCategoryName = select.options[select.selectedIndex].text;

            if (!subSubCategoryId) return;

            // Update hidden field
            document.getElementById('inv-subsubcategory-id').value = subSubCategoryId;
            document.getElementById('inv-sub_sub_category').value = subSubCategoryName;
        },

        /**
         * Load pre-selected values from inventory data
         */
        selectPresetCategories: function(categoryId, subCategoryId, subSubCategoryId) {
            if (categoryId && typeof $ !== 'undefined') {
                // Use Select2's val method and trigger change
                $('#inv-category-select').val(categoryId).trigger('change');
                this.onCategoryChange();

                // Then select subcategory if available
                setTimeout(() => {
                    if (subCategoryId) {
                        $('#inv-subcategory-select').val(subCategoryId).trigger('change');
                        this.onSubCategoryChange();

                        // Then select sub-subcategory if available
                        setTimeout(() => {
                            if (subSubCategoryId) {
                                $('#inv-subsubcategory-select').val(subSubCategoryId).trigger(
                                    'change');
                                this.onSubSubCategoryChange();
                            }
                        }, 100);
                    }
                }, 100);
            }
        },

        /**
         * Clear subcategories select
         */
        clearSubCategories: function() {
            const select = document.getElementById('inv-subcategory-select');
            if (!select) return;
            // Clear all options except the first one
            while (select.options.length > 1) {
                select.remove(1);
            }
            select.value = '';
            document.getElementById('inv-subcategory-id').value = '';
            // Trigger Select2 update
            if (typeof $ !== 'undefined' && $(select).hasClass('select2-hidden-accessible')) {
                $(select).val('').trigger('change');
            }
        },

        /**
         * Clear sub-subcategories select
         */
        clearSubSubCategories: function() {
            const select = document.getElementById('inv-subsubcategory-select');
            if (!select) return;
            // Clear all options except the first one
            while (select.options.length > 1) {
                select.remove(1);
            }
            select.value = '';
            document.getElementById('inv-subsubcategory-id').value = '';
            // Trigger Select2 update
            if (typeof $ !== 'undefined' && $(select).hasClass('select2-hidden-accessible')) {
                $(select).val('').trigger('change');
            }
        },

        /**
         * Clear all category selects
         */
        clearCategorySelects: function() {
            const categorySelect = document.getElementById('inv-category-select');
            if (categorySelect) {
                // Clear all options except the first one
                while (categorySelect.options.length > 1) {
                    categorySelect.remove(1);
                }
                categorySelect.value = '';
                // Trigger Select2 update
                if (typeof $ !== 'undefined' && $(categorySelect).hasClass('select2-hidden-accessible')) {
                    $(categorySelect).val('').trigger('change');
                }
            }
            this.clearSubCategories();
            this.clearSubSubCategories();
        }
    };

    // Make functions globally available for inline onchange handlers
    window.onCategoryChange = function() {
        window.categoryHierarchyManager.onCategoryChange();
    };

    window.onSubCategoryChange = function() {
        window.categoryHierarchyManager.onSubCategoryChange();
    };

    window.onSubSubCategoryChange = function() {
        window.categoryHierarchyManager.onSubSubCategoryChange();
    };

    // Auto-initialize categories tree
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            if (window.categoryHierarchyManager) {
                window.categoryHierarchyManager.initialize();
            }
        });
    } else {
        if (window.categoryHierarchyManager) {
            window.categoryHierarchyManager.initialize();
        }
    }
</script>
@endpush
