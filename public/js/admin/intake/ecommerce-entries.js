// Ecommerce Entries AJAX Handler
const EcommerceEntries = {
    currentPage: 1,
    baseUrl: '/admin/ecommerce-entries',
    storageUrl: '/ecommerce',
    currentItemId: null,
    handlersAttached: false, // Flag to track if handlers are already attached
    newPhotosFiles: [], // Array to store new photo files for preview
    selectedIds: new Set(),

    init: function () {
        console.log('EcommerceEntries initialized');
        console.log('Base URL set to:', this.baseUrl);
        this.attachEventListeners();
        this.attachPhotoUploadHandler(); // Add photo upload handler
        this.loadData();
    },

    attachEventListeners: function () {
        // Search and filters
        $('#search-input').on('keyup', debounce(() => this.loadData(), 300));
        $('#status-filter, #category-filter, #pallet-filter').on('change', () => this.loadData());
        $('#btn-refresh').on('click', () => this.loadData());

        // Export CSV
        $('#btn-export-csv').on('click', () => this.exportCsv());

        $('#select-all-items').on('change', (e) => {
            const isChecked = $(e.currentTarget).is(':checked');

            $('.item-select').each((_, el) => {
                const id = String($(el).data('id'));
                $(el).prop('checked', isChecked);

                if (isChecked) {
                    this.selectedIds.add(id);
                } else {
                    this.selectedIds.delete(id);
                }
            });

            this.updateBulkActions();
        });

        $(document).on('change', '.item-select', (e) => {
            const id = String($(e.currentTarget).data('id'));
            const isChecked = $(e.currentTarget).is(':checked');

            $('.item-select').filter((_, el) => String($(el).data('id')) === id).prop('checked', isChecked);

            if (isChecked) {
                this.selectedIds.add(id);
            } else {
                this.selectedIds.delete(id);
            }

            this.updateBulkActions();
        });

        $('#bulk-status-btn').on('click', () => this.bulkUpdateStatus());
        $('#bulk-delete-btn').on('click', () => this.bulkDeleteItems());

        // Edit form
        $('#editItemForm').on('submit', (e) => this.handleEditSubmit(e));
        $('#edit-quantity, #edit-reuse-quantity, #edit-scrap-quantity').on('input', () => {
            this.updateRemainingQty();
        });
        $('#edit-weight, #edit-reuse-weight, #edit-scrap-weight').on('input', () => {
            this.updateNetWeight();
        });

        // Add to inventory form
        $('#saveToInventoryForm').on('submit', (e) => this.handleSaveToInventory(e));

        // Print modal button
        $('#btn-modal-print').on('click', () => this.printInventorySlip());
        $('#btn-modal-edit').on('click', () => this.editCurrentItem());

        // Attach table actions once (uses event delegation for dynamic elements)
        this.attachTableActions();
    },

    toInt: function (value) {
        const parsed = parseInt(value, 10);
        return Number.isFinite(parsed) ? parsed : 0;
    },

    toFloat: function (value) {
        const parsed = parseFloat(value);
        return Number.isFinite(parsed) ? parsed : 0;
    },

    availableQuantity: function (source = {}) {
        if (source.net_quantity !== undefined && source.net_quantity !== null) {
            return Math.max(0, this.toInt(source.net_quantity));
        }

        return Math.max(
            0,
            this.toInt(source.quantity)
                - this.toInt(source.reuse_quantity)
                - this.toInt(source.scrap_quantity)
                - this.toInt(source.transferred_quantity)
        );
    },

    availableWeight: function (source = {}) {
        if (source.net_weight !== undefined && source.net_weight !== null) {
            return Math.max(0, this.toFloat(source.net_weight));
        }

        return Math.max(
            0,
            this.toFloat(source.weight) - this.toFloat(source.reuse_weight) - this.toFloat(source.scrap_weight)
        );
    },

    loadData: function () {
        const filters = {
            search: $('#search-input').val(),
            statusFilter: $('#status-filter').val(),
            categoryFilter: $('#category-filter').val(),
            palletFilter: $('#pallet-filter').val(),
            page: this.currentPage,
        };

        console.log('Loading data from:', this.baseUrl);
        console.log('Filters:', filters);

        $.ajax({
            url: this.baseUrl,
            type: 'GET',
            data: filters,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: (response) => {
                console.log('AJAX Response:', response);
                this.selectedIds.clear();
                this.renderStats(response.stats);
                this.renderTable(response.items);
                this.renderMobileCards(response.items);
                this.renderPagination(response.pagination);
                this.updateBulkActions();
            },
            error: (xhr, status, error) => {
                console.error('AJAX Error:', status, error);
                console.error('Response:', xhr.responseText);
                // Only show error if it's not a 404 on initial load
                if (xhr.status !== 404) {
                    this.showError('Failed to load data: ' + error);
                }
            },
        });
    },

    renderStats: function (stats) {
        const html = stats.map(stat => `
            <div class="bg-white p-3 md:p-4 rounded-xl border border-slate-100 shadow-sm transition-all hover:shadow-md">
                <p class="text-[9px] md:text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">${stat.label}</p>
                <h3 class="text-lg md:text-xl font-black ${stat.color}">${stat.value}</h3>
            </div>
        `).join('');
        $('#stats-container').html(html);
    },

    renderTable: function (items) {
        if (items.length === 0) {
            $('#table-body').html(`
                <tr>
                    <td colspan="12" class="p-16 text-center text-slate-300 font-bold">No Records Found</td>
                </tr>
            `);
            return;
        }

        const html = items.map(item => this.renderTableRow(item)).join('');
        $('#table-body').html(html);
    },

    renderTableRow: function (item) {
        const qtyRemaining = this.availableQuantity(item);
        const isInventoryDepleted = qtyRemaining === 0;  // Only disable if no items remain
        const statusBgColor = ['Shipped', 'Delivered', 'Live Online'].includes(item.status)
            ? 'bg-emerald-500 text-white'
            : item.status === 'Completed'
                ? 'bg-emerald-100 text-emerald-700 border border-emerald-200'
            : item.status === 'In Progress'
                ? 'bg-amber-400 text-white'
                : item.status === 'Scrapped' || item.status === 'Scrap'
                    ? 'bg-red-500 text-white'
                    : 'bg-blue-500 text-white';

        const photoHtml = renderPhotoThumbnail(item.photos_paths, item.barcode);

        // Build category hierarchy
        let categoryDisplay = item.category || '-';
        if (item.sub_category) {
            categoryDisplay = `${item.category} > ${item.sub_category}`;
        }
        if (item.sub_sub_category) {
            categoryDisplay += ` > ${item.sub_sub_category}`;
        }

        return `
            <tr class="hover:bg-slate-50/50 transition-colors text-[13px] text-slate-700">
                <td class="px-4 py-3 text-center">
                    <input type="checkbox" class="item-select h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500" data-id="${item.id}">
                </td>
                <td class="px-4 py-3 font-bold text-slate-900 uppercase">${item.barcode}</td>
                <td class="px-4 py-3">
                    ${this.formatItemsSummary(item)}
                </td>
                <td class="px-4 py-3 text-[11px] font-semibold uppercase text-slate-500" title="${categoryDisplay}">${categoryDisplay}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-600 text-[9px] font-black uppercase">${item.intake_type || 'E-commerce'}</span>
                </td>
                <td class="px-4 py-3 text-center">${photoHtml}</td>
                <td class="px-4 py-3 text-center">
                    <input type="number" min="0" value="${qtyRemaining}" class="w-14 p-1 border border-slate-200 rounded-md text-center font-bold text-slate-800 text-sm qty-input" data-id="${item.id}" data-reuse-quantity="${this.toInt(item.reuse_quantity)}" data-scrap-quantity="${this.toInt(item.scrap_quantity)}" data-transferred-quantity="${this.toInt(item.transferred_quantity)}">
                </td>
                <td class="px-2 py-3" style="width: 11rem; min-width: 11rem;">
                    <select class="w-full px-2 py-2 rounded-lg text-[10px] font-black uppercase border-none cursor-pointer shadow-sm status-select ${statusBgColor}" style="width: 100%; min-width: 10rem;" data-id="${item.id}">
                        <option value="Recieved" ${item.status === 'Recieved' ? 'selected' : ''}>Recieved</option>
                        <option value="Ready for Inventory" ${item.status === 'Ready for Inventory' ? 'selected' : ''}>Ready for Inventory</option>
                        <option value="In Progress" ${item.status === 'In Progress' ? 'selected' : ''}>In Progress</option>
                        <option value="Live Online" ${item.status === 'Live Online' ? 'selected' : ''}>Live Online</option>
                        <option value="Completed" ${item.status === 'Completed' ? 'selected' : ''}>Completed</option>
                        ${!['Ready for Inventory', 'In Progress', 'Live Online', 'Recieved', 'Completed'].includes(item.status) ? `<option value="${item.status}" selected>${item.status}</option>` : ''}
                    </select>
                </td>
                <td class="px-4 py-3 font-black text-slate-900 text-sm">$${parseFloat(item.estimated_value).toFixed(2)}</td>
                <td class="px-4 py-3 font-bold text-slate-400 text-[11px]">${item.pallet_number || 'N/A'}</td>
                <td class="px-4 py-3 text-slate-400 font-medium text-[11px]">${new Date(item.created_at).toLocaleDateString()}</td>
                <td class="px-4 py-3">
                    <div class="flex flex-row items-center justify-end gap-1.5">
                        <button class="h-9 px-4 min-w-max rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm border ${isInventoryDepleted ? 'bg-emerald-50 text-emerald-600 border-emerald-100 cursor-not-allowed opacity-90' : 'bg-blue-50 text-blue-600 border-blue-100 hover:bg-blue-600 hover:text-white active:scale-95'} btn-add-inventory" data-id="${item.id}" ${isInventoryDepleted ? 'disabled' : ''}>
                            ${isInventoryDepleted ? `
                                <i class="fas fa-check-double text-sm"></i>
                                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">All Transferred</span>
                            ` : `
                                <i class="fas fa-plus-circle text-sm"></i>
                                <span class="text-xs font-semibold uppercase tracking-wider">Add to Inventory</span>
                            `}
                        </button>
                        <button class="h-9 w-9 bg-slate-50 text-slate-400 rounded-xl transition-all hover:bg-slate-900 hover:text-white flex items-center justify-center active:scale-90 btn-view-details" data-id="${item.id}" title="View Details">
                            <i class="fas fa-eye text-xs"></i>
                        </button>
                        <button class="h-9 w-9 bg-orange-50 text-orange-500 rounded-xl transition-all hover:bg-orange-500 hover:text-white flex items-center justify-center active:scale-90 btn-print" data-id="${item.id}" title="Print Label">
                            <i class="fas fa-print text-xs"></i>
                        </button>
                        <button class="h-9 w-9 bg-emerald-50 text-emerald-600 rounded-xl transition-all hover:bg-emerald-600 hover:text-white flex items-center justify-center active:scale-90 btn-edit" data-id="${item.id}" title="Edit Item">
                            <i class="fas fa-edit text-xs"></i>
                        </button>
                        <button class="h-9 w-9 bg-red-50 text-red-400 rounded-xl transition-all hover:bg-red-500 hover:text-white flex items-center justify-center active:scale-90 btn-delete" data-id="${item.id}" title="Delete Item">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    },

    renderMobileCards: function (items) {
        if (items.length === 0) {
            $('#mobile-container').html(`
                <div class="p-10 text-center text-slate-300 font-bold uppercase text-xs">No Records Found</div>
            `);
            return;
        }

        const html = items.map(item => {
            const qtyRemaining = this.availableQuantity(item);

            return `
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 space-y-4">
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" class="item-select h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500" data-id="${item.id}">
                        ${item.photos_paths && item.photos_paths.length > 0
                ? item.photos_paths.length === 1
                    ? `<img src="${this.storageUrl}/${item.photos_paths[0]}" class="w-12 h-12 rounded-xl border object-cover cursor-pointer hover:opacity-80 gallery-image" data-photos='${JSON.stringify(item.photos_paths)}'>`
                    : `<div class="relative w-12 h-12 group cursor-pointer gallery-image" data-photos='${JSON.stringify(item.photos_paths)}'>
                        <img src="${this.storageUrl}/${item.photos_paths[0]}" class="w-full h-full rounded-xl border object-cover">
                        <div class="absolute inset-0 bg-black/40 rounded-xl transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <span class="text-white font-bold text-sm">+${item.photos_paths.length - 1}</span>
                        </div>
                      </div>`
                : `<div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-slate-300"><i class="fas fa-image"></i></div>`
            }
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase leading-none mb-1">${item.barcode}</p>
                            <h4 class="text-sm font-black text-slate-900 leading-tight truncate max-w-[150px]">${item.name}</h4>
                            <p class="text-[10px] font-bold text-blue-500 uppercase">${item.category}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black text-slate-900">$${parseFloat(item.estimated_value).toFixed(2)}</p>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-1 border-t border-slate-50">
                    <div class="text-[10px] font-bold text-slate-400 uppercase">
                        Pallet: <span class="text-slate-900">${item.pallet_number || 'N/A'}</span>
                    </div>
                    <div class="flex gap-2">
                        ${qtyRemaining === 0 ? `
                            <button class="p-2 bg-emerald-50 text-emerald-600 rounded-lg cursor-not-allowed opacity-90 btn-add-inventory" disabled title="All items transferred">
                                <i class="fas fa-check-double text-sm"></i>
                            </button>
                        ` : `
                            <button class="p-2 bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white rounded-lg transition-all btn-add-inventory" data-id="${item.id}" title="Add to Inventory">
                                <i class="fas fa-plus-circle text-sm"></i>
                            </button>
                        `}
                        <button class="p-2 hover:text-blue-500 btn-view-details" data-id="${item.id}"><i class="fas fa-eye"></i></button>
                        <button class="p-2 hover:text-orange-500 btn-print" data-id="${item.id}"><i class="fas fa-print"></i></button>
                        <button class="p-2 hover:text-emerald-500 btn-edit" data-id="${item.id}"><i class="fas fa-edit"></i></button>
                        <button class="p-2 hover:text-red-400 btn-delete" data-id="${item.id}"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
            `;
        }).join('');
        $('#mobile-container').html(html);
    },

    renderPagination: function (pagination) {
        let html = '<nav class="flex justify-center gap-2">';

        // Previous button
        if (pagination.current_page > 1) {
            html += `<button class="px-3 py-2 border rounded-lg hover:bg-slate-100 pagination-btn" data-page="${pagination.current_page - 1}">Previous</button>`;
        }

        // Page numbers
        for (let i = 1; i <= pagination.last_page; i++) {
            if (i === pagination.current_page) {
                html += `<button class="px-3 py-2 bg-blue-600 text-white rounded-lg">${i}</button>`;
            } else {
                html += `<button class="px-3 py-2 border rounded-lg hover:bg-slate-100 pagination-btn" data-page="${i}">${i}</button>`;
            }
        }

        // Next button
        if (pagination.current_page < pagination.last_page) {
            html += `<button class="px-3 py-2 border rounded-lg hover:bg-slate-100 pagination-btn" data-page="${pagination.current_page + 1}">Next</button>`;
        }

        html += '</nav>';
        $('#pagination-container').html(html);

        $('.pagination-btn').on('click', function () {
            EcommerceEntries.currentPage = $(this).data('page');
            EcommerceEntries.loadData();
        });
    },

    getSelectedIds: function () {
        return Array.from(this.selectedIds).map(id => parseInt(id, 10)).filter(Boolean);
    },

    syncSelectAll: function () {
        const checkboxes = $('.item-select');
        const checkedCount = checkboxes.filter(':checked').length;

        $('#select-all-items')
            .prop('checked', checkboxes.length > 0 && checkedCount === checkboxes.length)
            .prop('indeterminate', checkedCount > 0 && checkedCount < checkboxes.length);
    },

    updateBulkActions: function () {
        const selectedCount = this.selectedIds.size;

        $('#bulk-selected-count').text(`${selectedCount} selected`);
        $('#bulk-actions').toggleClass('hidden', selectedCount === 0);
        $('#bulk-status-btn, #bulk-delete-btn')
            .prop('disabled', selectedCount === 0)
            .toggleClass('opacity-50 cursor-not-allowed', selectedCount === 0);

        this.syncSelectAll();
    },

    bulkUpdateStatus: function () {
        const ids = this.getSelectedIds();
        const status = $('#bulk-status-select').val();

        if (ids.length === 0) {
            this.showError('Select at least one item.');
            return;
        }

        if (!status) {
            this.showError('Choose a status first.');
            return;
        }

        $.ajax({
            url: `${this.baseUrl}/bulk-status`,
            type: 'PUT',
            data: { ids, status },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            success: (response) => {
                this.showSuccess(response.message || 'Selected items updated successfully.');
                this.selectedIds.clear();
                this.loadData();
            },
            error: (xhr) => {
                this.showError(xhr.responseJSON?.message || 'Failed to update selected items');
            },
        });
    },

    bulkDeleteItems: function () {
        const ids = this.getSelectedIds();

        if (ids.length === 0) {
            this.showError('Select at least one item.');
            return;
        }

        if (!confirm(`Delete ${ids.length} selected item(s)?`)) return;

        $.ajax({
            url: `${this.baseUrl}/bulk-delete`,
            type: 'DELETE',
            data: { ids },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            success: (response) => {
                this.showSuccess(response.message || 'Selected items deleted successfully.');
                this.selectedIds.clear();
                this.loadData();
            },
            error: (xhr) => {
                this.showError(xhr.responseJSON?.message || 'Failed to delete selected items');
            },
        });
    },

    attachTableActions: function () {
        // Skip if handlers are already attached
        if (this.handlersAttached) {
            console.log('Handlers already attached, skipping');
            return;
        }

        // Mark as attached
        this.handlersAttached = true;

        // Now attach handlers using event delegation for dynamically added elements
        $(document).on('change', '.qty-input', (e) => {
            const id = $(e.target).data('id');
            const quantity = $(e.target).val();
            const reuseQuantity = $(e.target).data('reuse-quantity');
            const scrapQuantity = $(e.target).data('scrap-quantity');
            const transferredQuantity = $(e.target).data('transferred-quantity');
            this.updateQuantity(id, quantity, reuseQuantity, scrapQuantity, transferredQuantity);
        });

        $(document).on('change', '.status-select', (e) => {
            const id = $(e.target).data('id');
            const status = $(e.target).val();
            this.updateStatus(id, status);
        });

        $(document).on('click', '.btn-view-details', (e) => {
            const id = $(e.target).closest('button').data('id');
            this.showItemDetails(id);
        });

        $(document).on('click', '.btn-edit', (e) => {
            const id = $(e.target).closest('button').data('id');
            this.editItem(id);
        });

        $(document).on('click', '.btn-delete', (e) => {
            const id = $(e.target).closest('button').data('id');
            if (confirm('Are you sure you want to delete this item?')) {
                this.deleteItem(id);
            }
        });

        $(document).on('click', '.btn-add-inventory', (e) => {
            const id = $(e.target).closest('button').data('id');
            this.prepareToInventory(id);
        });

        $(document).on('click', '.btn-print', (e) => {
            const id = $(e.target).closest('button').data('id');
            this.printItem(id);
        });

        // Delete photo handler
        $(document).on('click', '.delete-photo-btn', (e) => {
            e.preventDefault();
            const path = $(e.target).closest('button').data('path');
            // Use the current item ID which is set when editing
            if (this.currentItemId) {
                this.deletePhoto(this.currentItemId, path);
            }
        });

        // Gallery image handler
        $(document).on('click', '.gallery-image', (e) => {
            const photosData = $(e.currentTarget).data('photos');
            if (photosData && Array.isArray(photosData)) {
                openGallery(photosData);
            }
        });
    },
    getPhotoUrl: function (path) {
        if (!path) {
            return '/images/placeholder.png';
        }

        if (/^https?:\/\//.test(path)) {
            return path;
        }

        if (path.startsWith('/')) {
            return path;
        }

        if (path.startsWith('ecommerce/')) {
            return `/${path}`;
        }

        return `${this.storageUrl.replace(/\/$/, '')}/${path}`;
    },

    // Photo upload preview functionality
    attachPhotoUploadHandler: function () {
        $('#edit-photos').on('change', (e) => {
            this.newPhotosFiles = Array.from(e.target.files);
            this.updatePhotoPreview();
        });
    },

    updatePhotoPreview: function () {
        $('#edit-new-photos-preview').empty();
        this.newPhotosFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const preview = `
                    <div class="relative w-20 h-20 rounded-xl overflow-hidden border-2 border-blue-400 shadow-md">
                        <img src="${e.target.result}" class="w-full h-full object-cover">
                        <button type="button" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center shadow-md hover:bg-red-600 transition-colors" onclick="EcommerceEntries.removePhotoPreview(${index})">
                            <i class="fas fa-times text-[10px]"></i>
                        </button>
                    </div>
                `;
                $('#edit-new-photos-preview').append(preview);
            };
            reader.readAsDataURL(file);
        });
    },

    removePhotoPreview: function (index) {
        this.newPhotosFiles.splice(index, 1);
        // Update the file input
        const dt = new DataTransfer();
        this.newPhotosFiles.forEach(file => dt.items.add(file));
        $('#edit-photos')[0].files = dt.files;
        this.updatePhotoPreview();
    },

    showItemDetails: function (itemId) {
        $.ajax({
            url: `${this.baseUrl}/${itemId}/details`,
            type: 'GET',
            success: (response) => {
                const item = response.item;
                this.currentItemId = itemId;

                // Header
                const names = item.name ? item.name.split(',').map(s => s.trim()) : [];
                const titleName = names.length > 0 ? names[0] : 'Item';
                $('#modal-title').text(`View Ecommerce Item: ${titleName}`);

                // Status badge
                const status = item.status || '';
                let statusClass = 'bg-slate-500';
                if (['Live Online', 'Shipped', 'Delivered'].includes(status)) statusClass = 'bg-emerald-500';
                else if (status === 'In Progress') statusClass = 'bg-amber-500';
                else if (['Scrapped', 'Scrap'].includes(status)) statusClass = 'bg-red-500';
                else if (status === 'Ready for Inventory') statusClass = 'bg-blue-500';
                $('#modal-status').text(status).removeClass().addClass(`px-3 py-1 text-[10px] font-black uppercase rounded-lg text-white ${statusClass}`);

                // Category fields
                $('#modal-category-field').text(item.category || 'N/A');
                $('#modal-sub-category').text(item.sub_category || 'N/A');
                $('#modal-sub-sub-category').text(item.sub_sub_category || 'N/A');
                $('#modal-condition-field').text(item.condition || 'N/A');

                // Value & quantities
                const netQty = this.availableQuantity(item);
                const netWeight = this.availableWeight(item);
                $('#modal-value').text(`$${parseFloat(item.estimated_value || 0).toFixed(2)}`);
                $('#modal-qty-value').text(item.quantity || 0);
                $('#modal-reuse-quantity').text(item.reuse_quantity || 0);
                $('#modal-scrap-quantity').text(item.scrap_quantity || 0);
                $('#modal-remaining-qty').text(`${netQty} PCS`);

                // Weight
                $('#modal-weight').text(parseFloat(item.weight || 0).toFixed(2));
                $('#modal-reuse-weight').text(parseFloat(item.reuse_weight || 0).toFixed(2));
                $('#modal-scrap-weight').text(parseFloat(item.scrap_weight || 0).toFixed(2));
                $('#modal-net-weight').text(`${parseFloat(netWeight).toFixed(2)} Lbs`);

                // Location & Employee
                $('#modal-pallet').text(item.pallet_number || 'N/A');
                $('#modal-placement').text(item.item_placement || 'N/A');
                $('#modal-receiving-employee').text(item.receiving_employee || 'N/A');
                $('#modal-processing-employee').text(item.processing_employee || 'N/A');
                $('#modal-ecommerce-employee').text(item.ecommerce_employee || item.intake_employee || 'N/A');

                // Notes
                $('#modal-notes').text(item.notes || 'No internal notes provided.');

                // Last modified
                $('#modal-updated-at').text(new Date(item.updated_at).toLocaleDateString('en-US', { month: '2-digit', day: '2-digit', year: '2-digit', hour: '2-digit', minute: '2-digit' }));

                // Barcode
                $('#modal-barcode-img').attr('src', `https://bwipjs-api.metafloor.com/?bcid=code128&text=${encodeURIComponent(item.barcode)}&scale=2&height=10&includetext=true`);

                // Item cards (name/brand/model/serial per comma-separated entry)
                this.renderEcommerceModalItems(item);

                // Photos
                const photos = item.photos_paths && item.photos_paths.length > 0 ? item.photos_paths : [];
                const photosHtml = photos.length > 0
                    ? photos.map(path => {
                        const isImage = /\.(jpg|jpeg|png|webp|gif|svg|bmp)$/i.test(path);
                        const filename = path.split('/').pop();
                        if (isImage) {
                            return `
                                <div class="w-24 h-24 rounded-xl overflow-hidden border border-slate-200 shadow-sm group cursor-pointer">
                                    <img src="${this.getPhotoUrl(path)}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                </div>`;
                        }
                        let iconClass = 'fa-file-alt', iconColor = 'text-blue-500';
                        if (path.endsWith('.pdf')) { iconClass = 'fa-file-pdf'; iconColor = 'text-red-500'; }
                        else if (path.endsWith('.csv') || path.endsWith('.xlsx') || path.endsWith('.xls')) { iconClass = 'fa-file-excel'; iconColor = 'text-green-500'; }
                        else if (path.endsWith('.doc') || path.endsWith('.docx')) { iconClass = 'fa-file-word'; iconColor = 'text-blue-600'; }
                        return `
                            <a href="${this.getPhotoUrl(path)}" target="_blank" class="w-24 h-24 rounded-xl border border-slate-200 bg-white flex flex-col items-center justify-center p-2 text-center shadow-sm group hover:border-blue-300 transition-colors">
                                <i class="fas ${iconClass} ${iconColor} text-2xl mb-1 group-hover:scale-110 transition-transform"></i>
                                <span class="text-[9px] font-bold text-slate-600 truncate w-full" title="${filename}">${filename}</span>
                            </a>`;
                    }).join('')
                    : `<div class="col-span-full py-4 text-center text-slate-400 text-xs font-bold w-full bg-white border border-slate-100 rounded-xl">
                        <i class="fas fa-camera text-2xl mb-2 block"></i>No files found</div>`;
                $('#modal-photos').html(photosHtml);

                $('#itemDetailsModal').removeClass('hidden');
            },
            error: () => this.showError('Failed to load item details'),
        });
    },

    renderEcommerceModalItems: function (item) {
        const container = document.getElementById('ecommerce-modal-items-list');
        if (!container) return;

        const names   = item.name          ? item.name.split(',').map(s => s.trim())          : [];
        const brands  = item.brand         ? item.brand.split(',').map(s => s.trim())         : [];
        const models  = item.model         ? item.model.split(',').map(s => s.trim())         : [];
        const serials = item.serial_number ? item.serial_number.split(',').map(s => s.trim()) : [];

        const maxLen = Math.max(names.length, brands.length, models.length, serials.length, 1);

        const badge = document.getElementById('ecommerce-modal-items-count');
        if (badge) badge.textContent = `${maxLen} Item${maxLen > 1 ? 's' : ''}`;

        const getItemVal = (arr, index, defaultVal = 'N/A') => {
            if (!arr || arr.length === 0) return defaultVal;
            if (arr[index] !== undefined && arr[index] !== '') return arr[index];
            const fallback = arr[arr.length - 1] || arr[0];
            return (fallback !== undefined && fallback !== '') ? fallback : defaultVal;
        };

        let html = '';
        for (let i = 0; i < maxLen; i++) {
            const e = s => String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
            const name = getItemVal(names, i, 'N/A');
            const brand = getItemVal(brands, i, 'N/A');
            const model = getItemVal(models, i, 'N/A');
            const serial = serials[i] || 'N/A';

            html += `
                <div class="p-5 bg-[#f8fafc] border border-[#e2e8f0] rounded-2xl relative mb-3">
                    <div class="absolute top-4 right-4 text-slate-400 bg-slate-100 rounded-full w-6 h-6 flex items-center justify-center text-[10px] font-bold">${i + 1}</div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pr-6">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Item Name</label>
                            <div class="w-full p-2.5 bg-white border border-[#e2e8f0] rounded-xl text-xs font-bold text-slate-700">${e(name)}</div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Brand</label>
                            <div class="w-full p-2.5 bg-white border border-[#e2e8f0] rounded-xl text-xs font-bold text-slate-700">${e(brand)}</div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Model</label>
                            <div class="w-full p-2.5 bg-white border border-[#e2e8f0] rounded-xl text-xs font-bold text-slate-700">${e(model)}</div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Serial Number</label>
                            <div class="w-full p-2.5 bg-white border border-[#e2e8f0] rounded-xl text-xs font-bold text-slate-700 font-mono">${e(serial)}</div>
                        </div>
                    </div>
                </div>`;
        }
        container.innerHTML = html;
    },

    editItem: function (itemId) {
        $.ajax({
            url: `${this.baseUrl}/${itemId}/edit`,
            type: 'GET',
            success: (response) => {
                const item = response.item;
                const pallets = response.pallets_list;

                this.currentItemId = itemId;
                $('#edit-item-id').val(item.id);
                $('#edit-modal-title').text(`Edit Item: ${item.barcode}`);

                $('#edit-name').val(item.name);
                $('#edit-brand').val(item.brand);
                $('#edit-model').val(item.model);
                $('#edit-serial-number').val(item.serial_number);
                $('#edit-category').val(item.category);
                $('#edit-condition').val(item.condition);
                $('#edit-value').val(item.estimated_value);
                $('#edit-quantity').val(item.quantity);
                $('#edit-reuse-quantity').val(item.reuse_quantity || 0);
                $('#edit-scrap-quantity').val(item.scrap_quantity || 0);
                $('#edit-weight').val(item.weight);
                $('#edit-reuse-weight').val(item.reuse_weight || 0);
                $('#edit-scrap-weight').val(item.scrap_weight || 0);
                $('#edit-placement').val(item.item_placement);
                $('#edit-receiving-employee').val(item.receiving_employee || '');
                $('#edit-processing-employee').val(item.processing_employee || '');
                $('#edit-ecommerce-employee').val(item.ecommerce_employee || item.intake_employee || '');
                $('#edit-notes').val(item.notes);

                this.initializeEditCategoryHierarchy(item);

                // Populate pallet dropdown
                const palletHtml = '<option value="">No pallet assignment</option>' +
                    pallets.map(p => `<option value="${p.barcode_number}" ${item.pallet_number === p.barcode_number ? 'selected' : ''}>${p.barcode_number}</option>`).join('');
                $('#edit-pallet').html(palletHtml);

                // Existing photos
                const photosHtml = item.photos_paths && item.photos_paths.length > 0
                    ? item.photos_paths.map(path => `
                        <div class="relative w-20 h-20 group">
                            <img src="${this.getPhotoUrl(path)}" class="w-full h-full rounded-xl object-cover border shadow-sm">
                            <button type="button" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center shadow-md hover:bg-red-600 transition-colors delete-photo-btn" data-path="${path}">
                                <i class="fas fa-times text-[10px]"></i>
                            </button>
                        </div>
                    `).join('')
                    : '';
                $('#edit-existing-photos').html(photosHtml);

                this.updateRemainingQty();
                this.updateNetWeight();
                $('#editItemModal').removeClass('hidden');
            },
            error: () => this.showError('Failed to load item'),
        });
    },

    editCurrentItem: function () {
        this.editItem(this.currentItemId);
        $('#itemDetailsModal').addClass('hidden');
    },

    initializeEditCategoryHierarchy: function (item) {
        if (!window.categoryHierarchyManager) {
            return;
        }

        window.categoryHierarchyManager.whenReady().then(() => {
            this.populateEditCategoryHierarchy(item);
        });
    },

    populateEditCategoryHierarchy: function (item) {
        const manager = window.categoryHierarchyManager;
        const categoryMap = manager.categoryMap || {};
        const rootCategories = (manager.categories || []).filter(category => !category.parent_id);

        const categorySelect = $('#edit-category-select');
        const subCategorySelect = $('#edit-sub-category-select');
        const subSubCategorySelect = $('#edit-sub-sub-category-select');
        const normalize = (value) => (value || '').toString().trim().toLowerCase();

        const fillSelect = (select, placeholder, options, selectedId) => {
            select.empty().append(`<option value="">${placeholder}</option>`);
            options.forEach(category => {
                select.append(`<option value="${category.id}">${category.name}</option>`);
            });
            if (selectedId) {
                select.val(String(selectedId));
            }
        };

        let rootId = item.category_id || '';
        if (!rootId && item.category) {
            const matchedRoot = rootCategories.find(category => normalize(category.name) === normalize(item.category));
            rootId = matchedRoot ? matchedRoot.id : '';
        }

        const rootCategory = rootId ? categoryMap[rootId] : null;
        let subCategoryId = item.sub_category_id || '';
        if (!subCategoryId && item.sub_category && rootCategory && rootCategory.children) {
            const matchedSub = rootCategory.children.find(category => normalize(category.name) === normalize(item.sub_category));
            subCategoryId = matchedSub ? matchedSub.id : '';
        }

        const subCategory = subCategoryId ? categoryMap[subCategoryId] : null;
        let subSubCategoryId = item.sub_sub_category_id || '';
        if (!subSubCategoryId && item.sub_sub_category && subCategory && subCategory.children) {
            const matchedSubSub = subCategory.children.find(category => normalize(category.name) === normalize(item.sub_sub_category));
            subSubCategoryId = matchedSubSub ? matchedSubSub.id : '';
        }

        fillSelect(categorySelect, 'Select Category', rootCategories, rootId);

        const rootChildren = rootCategory && rootCategory.children ? rootCategory.children : [];
        fillSelect(subCategorySelect, 'Select Sub Category', rootChildren, subCategoryId);
        subCategorySelect.prop('disabled', !rootCategory || rootChildren.length === 0);

        const subChildren = subCategory && subCategory.children ? subCategory.children : [];
        fillSelect(subSubCategorySelect, 'Select Sub Sub Category', subChildren, subSubCategoryId);
        subSubCategorySelect.prop('disabled', !subCategory || subChildren.length === 0);

        $('#edit-category').val(rootCategory ? rootCategory.name : (item.category || ''));
        $('#edit-category-id').val(rootCategory ? rootCategory.id : '');
        $('#edit-sub-category').val(subCategory ? subCategory.name : (item.sub_category || ''));
        $('#edit-sub-category-id').val(subCategory ? subCategory.id : '');
        $('#edit-sub-sub-category').val(
            subSubCategoryId ? (categoryMap[subSubCategoryId]?.name || item.sub_sub_category || '') : (item.sub_sub_category || '')
        );
        $('#edit-sub-sub-category-id').val(subSubCategoryId || '');

        categorySelect.off('change').on('change', () => {
            const selectedCategoryId = categorySelect.val();
            const selectedCategory = selectedCategoryId ? categoryMap[selectedCategoryId] : null;
            const children = selectedCategory && selectedCategory.children ? selectedCategory.children : [];

            $('#edit-category').val(selectedCategory ? selectedCategory.name : '');
            $('#edit-category-id').val(selectedCategory ? selectedCategory.id : '');

            fillSelect(subCategorySelect, 'Select Sub Category', children, '');
            subCategorySelect.prop('disabled', !selectedCategory || children.length === 0);

            fillSelect(subSubCategorySelect, 'Select Sub Sub Category', [], '');
            subSubCategorySelect.prop('disabled', true);

            $('#edit-sub-category').val('');
            $('#edit-sub-category-id').val('');
            $('#edit-sub-sub-category').val('');
            $('#edit-sub-sub-category-id').val('');
        });

        subCategorySelect.off('change').on('change', () => {
            const selectedCategoryId = categorySelect.val();
            const selectedCategory = selectedCategoryId ? categoryMap[selectedCategoryId] : null;
            const selectedSubCategoryId = subCategorySelect.val();
            const selectedSubCategory = selectedSubCategoryId ? categoryMap[selectedSubCategoryId] : null;
            const children = selectedSubCategory && selectedSubCategory.children ? selectedSubCategory.children : [];

            $('#edit-sub-category').val(selectedSubCategory ? selectedSubCategory.name : '');
            $('#edit-sub-category-id').val(selectedSubCategory ? selectedSubCategory.id : '');

            fillSelect(subSubCategorySelect, 'Select Sub Sub Category', children, '');
            subSubCategorySelect.prop('disabled', !selectedSubCategory || children.length === 0);

            $('#edit-sub-sub-category').val('');
            $('#edit-sub-sub-category-id').val('');

            $('#edit-category').val(selectedCategory ? selectedCategory.name : $('#edit-category').val());
            $('#edit-category-id').val(selectedCategory ? selectedCategory.id : $('#edit-category-id').val());
        });

        subSubCategorySelect.off('change').on('change', () => {
            const selectedSubSubCategoryId = subSubCategorySelect.val();
            const selectedSubSubCategory = selectedSubSubCategoryId ? categoryMap[selectedSubSubCategoryId] : null;

            $('#edit-sub-sub-category').val(selectedSubSubCategory ? selectedSubSubCategory.name : '');
            $('#edit-sub-sub-category-id').val(selectedSubSubCategory ? selectedSubSubCategory.id : '');
        });

        if (rootId) {
            categorySelect.val(String(rootId)).trigger('change');
        }

        if (subCategoryId) {
            setTimeout(() => {
                subCategorySelect.val(String(subCategoryId)).trigger('change');
            }, 0);
        }

        if (subSubCategoryId) {
            setTimeout(() => {
                subSubCategorySelect.val(String(subSubCategoryId)).trigger('change');
            }, 0);
        }
    },

    handleEditSubmit: function (e) {
        e.preventDefault();

        const itemId = $('#edit-item-id').val();
        const formData = new FormData();

        formData.append('name', $('#edit-name').val());
        formData.append('brand', $('#edit-brand').val());
        formData.append('model', $('#edit-model').val());
        formData.append('serial_number', $('#edit-serial-number').val());
        formData.append('category', $('#edit-category').val());
        formData.append('sub_category', $('#edit-sub-category').val());
        formData.append('sub_sub_category', $('#edit-sub-sub-category').val());
        formData.append('category_id', $('#edit-category-id').val());
        formData.append('sub_category_id', $('#edit-sub-category-id').val());
        formData.append('sub_sub_category_id', $('#edit-sub-sub-category-id').val());
        formData.append('condition', $('#edit-condition').val());
        formData.append('estimated_value', $('#edit-value').val());
        formData.append('weight', $('#edit-weight').val());
        formData.append('reuse_weight', $('#edit-reuse-weight').val());
        formData.append('scrap_weight', $('#edit-scrap-weight').val());
        formData.append('quantity', $('#edit-quantity').val());
        formData.append('reuse_quantity', $('#edit-reuse-quantity').val());
        formData.append('scrap_quantity', $('#edit-scrap-quantity').val());
        formData.append('pallet_number', $('#edit-pallet').val());
        formData.append('item_placement', $('#edit-placement').val());
        formData.append('receiving_employee', $('#edit-receiving-employee').val());
        formData.append('processing_employee', $('#edit-processing-employee').val());
        formData.append('ecommerce_employee', $('#edit-ecommerce-employee').val());
        formData.append('intake_employee', $('#edit-ecommerce-employee').val());
        formData.append('notes', $('#edit-notes').val());

        // Handle new photos
        this.newPhotosFiles.forEach(file => {
            formData.append('new_photos[]', file);
        });

        $.ajax({
            url: `${this.baseUrl}/${itemId}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            success: (response) => {
                this.showSuccess(response.message);
                closeEditItemModal();
                this.loadData();
            },
            error: (xhr) => {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMsg = 'Validation failed:\n';
                    for (const field in errors) {
                        errorMsg += `${field}: ${errors[field].join(', ')}\n`;
                    }
                    this.showError(errorMsg);
                } else {
                    this.showError('Failed to update item');
                }
            },
        });
    },

    updateQuantity: function (itemId, quantity, reuseQuantity = 0, scrapQuantity = 0, transferredQuantity = 0) {
        const totalQuantity = this.toInt(quantity) + this.toInt(reuseQuantity) + this.toInt(scrapQuantity) + this.toInt(transferredQuantity);

        $.ajax({
            url: `${this.baseUrl}/${itemId}/quantity`,
            type: 'PUT',
            data: { quantity: totalQuantity },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            success: () => {
                this.showSuccess('Quantity updated!');
                this.loadData();
            },
            error: () => this.showError('Failed to update quantity'),
        });
    },

    updateStatus: function (itemId, status) {
        $.ajax({
            url: `${this.baseUrl}/${itemId}/status`,
            type: 'PUT',
            data: { status },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            success: (response) => {
                this.showSuccess(response.message);
                this.loadData();
            },
            error: () => this.showError('Failed to update status'),
        });
    },

    deletePhoto: function (itemId, path) {
        if (!confirm('Are you sure you want to delete this photo?')) return;

        $.ajax({
            url: `${this.baseUrl}/${itemId}/photo`,
            type: 'DELETE',
            data: { path },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            success: () => {
                this.showSuccess('Photo deleted!');
                this.editCurrentItem();
            },
            error: () => this.showError('Failed to delete photo'),
        });
    },

    deleteItem: function (itemId) {
        $.ajax({
            url: `${this.baseUrl}/${itemId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            success: (response) => {
                this.showSuccess(response.message);
                this.loadData();
            },
            error: () => this.showError('Failed to delete item'),
        });
    },

    prepareToInventory: function (itemId) {
        $.ajax({
            url: `${this.baseUrl}/${itemId}/prepare-inventory`,
            type: 'GET',
            success: (response) => {
                const item = response.item;
                const invData = response.inventoryData;

                $('#inventory-item-id').val(item.id);
                $('#inv-technician').val(invData.technician);
                $('#inv-customer-phone').val(invData.customer_phone);
                $('#inv-customer-email').val(invData.customer_email);
                // Do not populate device_type, category, price, serial_number, notes - user must fill manually
                
                // Store available quantity for validation (total - reuse - scrap = available for transfer)
                const availableQty = this.availableQuantity(invData);
                $('#inv-quantity').val('');  // Leave empty - user must enter amount
                $('#inv-quantity').data('available', availableQty);
                $('#inv-quantity').attr('max', availableQty);
                $('#remaining-qty').text(`Remaining: ${availableQty} items`);
                
                $('#inv-lot-info').val(invData.lot_info);
                $('#inv-status').val(invData.status);
                $('#inv-pallet-info').val(invData.pallet_info);
                $('#inv-device-specs').val(invData.device_specs);
                $('#inv-os').val(invData.operating_system);
                $('#inv-location').val(invData.put_away_location);

                // Clear previous validation state
                $('#quantity-error').hide();
                $('#inv-submit-btn').prop('disabled', true);  // Button starts disabled until user enters quantity

                // Initialize category hierarchy manager
                if (window.categoryHierarchyManager) {
                    window.categoryHierarchyManager.initialize();
                    // Pre-select categories if they exist in inventory data
                    if (invData.category_id) {
                        setTimeout(() => {
                            window.categoryHierarchyManager.selectPresetCategories(
                                invData.category_id,
                                invData.sub_category_id,
                                invData.sub_sub_category_id
                            );
                        }, 500);
                    }
                }

                $('#addToInventoryModal').removeClass('hidden');
            },
            error: () => this.showError('Failed to prepare item for inventory'),
        });
    },

    handleSaveToInventory: function (e) {
        e.preventDefault();

        const itemId = $('#inventory-item-id').val();
        const availableQty = parseInt($('#inv-quantity').data('available')) || 0;
        const selectedQty = parseInt($('#inv-quantity').val()) || 0;
        const formData = new FormData();
        const submitBtn = $('#inv-submit-btn');
        const submitSpinner = $('#inv-submit-spinner');
        const submitText = $('#inv-submit-text');
        const errorMsg = $('#quantity-error');

        // Validate quantity
        if (selectedQty <= 0) {
            errorMsg.text('Please enter a valid quantity').show();
            return;
        }

        if (selectedQty > availableQty) {
            errorMsg.text(`Not enough items available. Only ${availableQty} items remaining`).show();
            return;
        }

        // Clear error message if validation passes
        errorMsg.hide();

        formData.append('technician', $('#inv-technician').val());
        formData.append('customer_phone', $('#inv-customer-phone').val());
        formData.append('customer_email', $('#inv-customer-email').val());
        formData.append('device_type', $('#inv-device-type').val());
        formData.append('category', $('#inv-category').val());
        formData.append('sub_category', $('#inv-sub_category').val());
        formData.append('sub_sub_category', $('#inv-sub_sub_category').val());
        formData.append('category_id', $('#inv-category-id').val());
        formData.append('sub_category_id', $('#inv-subcategory-id').val());
        formData.append('sub_sub_category_id', $('#inv-subsubcategory-id').val());
        formData.append('brand_category', $('#inv-brand-category').val());
        formData.append('price', $('#inv-price').val());
        formData.append('quantity', selectedQty);
        formData.append('transferred_qty', selectedQty);  // Track how much was transferred
        formData.append('serial_number', $('#inv-serial-number').val());
        formData.append('lot_info', $('#inv-lot-info').val());
        formData.append('status', $('#inv-status').val());
        formData.append('pallet_info', $('#inv-pallet-info').val());
        formData.append('device_specs', $('#inv-device-specs').val());
        formData.append('operating_system', $('#inv-os').val());
        formData.append('put_away_location', $('#inv-location').val());
        formData.append('notes', $('#inv-notes').val());

        // Include generated barcode if present
        formData.append('barcode', $('#inv-barcode').val() || '');

        // Handle repair photos - use accumulated files from the modal manager
        if (window.inventoryPhotoManager && window.inventoryPhotoManager.accumulatedFiles) {
            window.inventoryPhotoManager.accumulatedFiles.forEach((item) => {
                // Support new structure {id,file,dataUrl} and legacy File objects
                const fileObj = item && item.file ? item.file : item;
                formData.append('repair_photos[]', fileObj);
            });
        }

        // Disable submit button and show spinner
        submitBtn.prop('disabled', true);
        submitSpinner.removeClass('hidden');
        submitText.text('Saving...');

        $.ajax({
            url: `${this.baseUrl}/${itemId}/save-inventory`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            success: (response) => {
                this.showSuccess(response.message);
                
                // Calculate new remaining quantity
                const newRemainingQty = availableQty - selectedQty;
                
                // Only disable button if ALL items are transferred (remaining = 0)
                if (newRemainingQty <= 0) {
                    $(`.btn-add-inventory[data-id="${itemId}"]`)
                        .prop('disabled', true)
                        .removeClass('bg-blue-50 text-blue-600 border-blue-100 hover:bg-blue-600 hover:text-white active:scale-95')
                        .addClass('bg-emerald-50 text-emerald-600 border-emerald-100 cursor-not-allowed opacity-90')
                        .html(`
                            <i class="fas fa-check-double text-sm"></i>
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">All Transferred</span>
                        `);
                }
                
                setTimeout(() => {
                    closeAddToInventoryModal();
                    this.loadData(); // Reload to update all data
                }, 1000);
            },
            error: (xhr) => {
                const message = xhr.responseJSON?.message || 'Failed to save to inventory';
                this.showError(message);
                // Re-enable submit button on error
                submitBtn.prop('disabled', false);
                submitSpinner.addClass('hidden');
                submitText.text('Save to Inventory');
            },
        });
    },

    exportCsv: function () {
        const filters = {
            search: $('#search-input').val(),
            statusFilter: $('#status-filter').val(),
        };

        window.location.href = `${this.baseUrl}/export-csv?${$.param(filters)}`;
    },

    updateRemainingQty: function () {
        const qty = parseInt($('#edit-quantity').val()) || 0;
        const reuseQty = parseInt($('#edit-reuse-quantity').val()) || 0;
        const scrapQty = parseInt($('#edit-scrap-quantity').val()) || 0;
        const deductedQty = reuseQty + scrapQty;
        const remaining = Math.max(0, qty - deductedQty);
        const color = remaining < 0 ? 'text-red-600' : 'text-emerald-600';
        $('#edit-total-qty-summary').text(qty);
        $('#edit-reuse-qty-summary').text(reuseQty);
        $('#edit-scrap-qty-summary').text(scrapQty);
        $('#edit-remaining-qty').html(`<span class="${color}">${remaining} PCS</span>`);
    },

    updateNetWeight: function () {
        const weight = parseFloat($('#edit-weight').val()) || 0;
        const reuseWeight = parseFloat($('#edit-reuse-weight').val()) || 0;
        const scrapWeight = parseFloat($('#edit-scrap-weight').val()) || 0;
        const deductedWeight = reuseWeight + scrapWeight;
        const netWeight = Math.max(0, weight - deductedWeight);
        const color = netWeight < 0 ? 'text-red-500' : 'text-blue-600';
        $('#edit-total-weight-summary').text(weight.toFixed(2));
        $('#edit-reuse-weight-summary').text(reuseWeight.toFixed(2));
        $('#edit-scrap-weight-summary').text(scrapWeight.toFixed(2));
        $('#edit-net-weight').html(`<span class="${color}">${netWeight.toFixed(2)} Lbs</span>`);
    },

    printItem: function (itemId) {
        $.ajax({
            url: `${this.baseUrl}/${itemId}/details`,
            type: 'GET',
            success: (response) => {
                const item = response.item;
                this.printInventoryLabel(item);
            },
            error: () => this.showError('Failed to load item for printing'),
        });
    },

    printInventoryLabel: function (item) {
        this.openLabelPrintWindow(this.generateProcessingStyleLabel(item, 'E-commerce Label'));
    },

    printInventorySlip: function () {
        $.ajax({
            url: `${this.baseUrl}/${this.currentItemId}/details`,
            type: 'GET',
            success: (response) => {
                const item = response.item;
                this.printInventoryLabel(item);
                $('#itemDetailsModal').addClass('hidden');
            },
            error: () => this.showError('Failed to print label'),
        });
    },

    generateProcessingStyleLabel: function (item, labelTitle = 'Item Label') {
        const barcode = item.barcode || 'N/A';
        const barcodeUrl = `https://bwipjs-api.metafloor.com/?bcid=code128&text=${encodeURIComponent(barcode)}&scale=2&height=12&includetext=false`;
        const brandModel = [item.brand, item.model].filter(Boolean).join(' ') || 'N/A';
        const quantity = this.availableQuantity(item);
        const createdDate = item.created_at ? new Date(item.created_at).toLocaleDateString() : new Date().toLocaleDateString();

        return `
            <section class="recycling-print-label">
                <div class="label-head">
                    <strong>IT Investment Recoveries</strong>
                    <span>${this.escapeHtml(labelTitle)}</span>
                </div>
                <div class="barcode-block">
                    <img src="${barcodeUrl}" alt="barcode">
                    <div>${this.escapeHtml(barcode)}</div>
                </div>
                <div class="label-grid">
                    ${this.renderLabelRow('Item', item.name)}
                    ${this.renderLabelRow('Brand/Model', brandModel)}
                    ${this.renderLabelRow('Serial', item.serial_number || 'N/A')}
                    ${this.renderLabelRow('Category', item.category || 'N/A')}
                    ${this.renderLabelRow('Condition', item.condition || 'Used')}
                    ${this.renderLabelRow('Item Count', quantity)}
                    ${this.renderLabelRow('Weight', `${this.numberFormat(this.availableWeight(item), 2)} lbs`)}
                    ${this.renderLabelRow('Value', `$${this.numberFormat(item.estimated_value, 2)}`)}
                    ${this.renderLabelRow('Pallet', item.pallet_number || 'N/A')}
                    ${this.renderLabelRow('Date', createdDate)}
                </div>
                <div class="label-foot">
                    ${this.escapeHtml(this.formatDenverDateTime())}
                </div>
            </section>
        `;
    },

    renderLabelRow: function (label, value) {
        return `
            <div class="label-row">
                <span>${this.escapeHtml(label)}</span>
                <strong>${this.escapeHtml(value === null || value === undefined || value === '' ? 'N/A' : value)}</strong>
            </div>
        `;
    },

    openLabelPrintWindow: function (html) {
        const printWindow = window.open('', '_blank', 'height=360,width=520');
        if (!printWindow) {
            this.showError('Print window was blocked');
            return;
        }

        printWindow.document.write(`
            <html>
                <head>
                    <title>Print Label</title>
                    <style>
                        ${this.getProcessingLabelPrintStyles()}
                    </style>
                </head>
                <body>
                    ${html}
                    <div class="print-actions"><button onclick="window.print()">Confirm Print</button></div>
                </body>
            </html>
        `);
        printWindow.document.close();
    },

    getProcessingLabelPrintStyles: function () {
        return `
            @page { size: 3.5in 2.25in; margin: 0; }
            html, body { margin: 0; width: 3.5in; min-height: 2.25in; background: #fff; color: #000; font-family: Arial, sans-serif; }
            .recycling-print-label { width: 3.5in; height: 2.25in; box-sizing: border-box; padding: 0.08in 0.12in; border: 1px solid #111827; overflow: hidden; }
            .label-head { display: flex; align-items: baseline; justify-content: space-between; border-bottom: 1px solid #111827; padding-bottom: 0.03in; margin-bottom: 0.04in; gap: 0.08in; }
            .label-head strong { font-size: 10px; font-weight: 900; text-transform: uppercase; white-space: nowrap; }
            .label-head span { font-size: 8px; font-weight: 800; text-transform: uppercase; text-align: right; }
            .barcode-block { text-align: center; margin-bottom: 0.04in; }
            .barcode-block img { max-width: 100%; width: 100%; height: 0.36in; object-fit: contain; }
            .barcode-block > div { font-family: monospace; font-size: 10px; font-weight: 900; margin-top: 0.01in; }
            .label-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.02in 0.08in; font-size: 6.8px; line-height: 1.15; }
            .label-row { min-width: 0; display: flex; justify-content: space-between; gap: 0.04in; border-bottom: 1px solid #e5e7eb; }
            .label-row span { color: #6b7280; font-weight: 800; text-transform: uppercase; white-space: nowrap; }
            .label-row strong { color: #111827; font-weight: 900; text-align: right; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .label-foot { margin-top: 0.03in; font-size: 6.5px; color: #4b5563; text-align: center; }
            .print-actions { margin-top: 16px; text-align: center; }
            .print-actions button { background: #111827; color: #fff; border: 0; border-radius: 10px; padding: 10px 20px; font-weight: 800; cursor: pointer; }
            @media screen { body { width: auto; min-height: auto; padding: 24px; background: #f1f5f9; display: flex; flex-direction: column; align-items: center; } .recycling-print-label { background: #fff; box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15); } }
            @media print { html, body { width: 3.5in; min-height: 2.25in; padding: 0; background: #fff; } .print-actions { display: none; } }
        `;
    },

    escapeHtml: function (value) {
        return String(value ?? '').replace(/[&<>"']/g, (character) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;',
        })[character]);
    },

    numberFormat: function (value, decimals = 0) {
        const number = parseFloat(value);
        return (Number.isFinite(number) ? number : 0).toLocaleString('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals,
        });
    },

    formatDenverDateTime: function (date = new Date()) {
        return new Intl.DateTimeFormat('en-US', {
            timeZone: 'America/Denver',
            year: 'numeric',
            month: 'short',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            timeZoneName: 'short',
        }).format(date);
    },

    showSuccess: function (message) {
        $('#success-message').text(message);
        $('#success-alert').removeClass('hidden');
        setTimeout(() => {
            $('#success-alert').addClass('hidden');
        }, 4000);
    },

    showError: function (message) {
        $('#error-message').text(message);
        $('#error-alert').removeClass('hidden');
        setTimeout(() => {
            $('#error-alert').addClass('hidden');
        }, 5000);
    },
};

// Helper function: debounce
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Modal control functions
function closeItemDetailsModal() {
    $('#itemDetailsModal').addClass('hidden');
}

function closeEditItemModal() {
    $('#editItemModal').addClass('hidden');
    $('#edit-photos').val('');
    $('#edit-new-photos-preview').empty();
    EcommerceEntries.newPhotosFiles = [];
}

function closeAddToInventoryModal() {
    $('#addToInventoryModal').addClass('hidden');
    $('#inv-photos').val('');
}

// Validate quantity in inventory transfer form
function validateQuantity() {
    const available = parseInt($("#inv-quantity").data("available")) || 0;
    const entered = parseInt($("#inv-quantity").val()) || 0;
    const errorMsg = $("#quantity-error");
    const submitBtn = $("#inv-submit-btn");
    const remainingDisplay = $("#remaining-qty");

    // Update remaining quantity display in real-time
    const remaining = Math.max(0, available - entered);
    remainingDisplay.text(`Remaining: ${remaining} items`);

    // Validation logic
    if (entered <= 0) {
        errorMsg.text("Quantity must be at least 1 item").show();
        submitBtn.prop("disabled", true);
        return false;
    }

    if (entered > available) {
        errorMsg.text(`Not enough items available. Only ${available} items remaining.`).show();
        submitBtn.prop("disabled", true);
        return false;
    }

    // Validation passed
    errorMsg.hide();
    submitBtn.prop("disabled", false);
    return true;
}

// Photo rendering function - same style as processing intake
function renderPhotoThumbnail(photosPaths, itemBarcode = "") {
    if (!photosPaths) {
        return `<div class="w-10 h-10 rounded-lg bg-slate-50 border flex items-center justify-center mx-auto"><i class="fas fa-image text-slate-300"></i></div>`;
    }
    
    let photos = photosPaths;
    if (typeof photos === 'string') {
        try {
            photos = JSON.parse(photos);
        } catch (e) {
            console.error('Failed to parse photos JSON:', e);
            photos = [];
        }
    }
    
    if (!Array.isArray(photos) || photos.length === 0) {
        return `<div class="w-10 h-10 rounded-lg bg-slate-50 border flex items-center justify-center mx-auto"><i class="fas fa-image text-slate-300"></i></div>`;
    }
    
    const photoUrls = photos.map(path => {
        if (path.startsWith('/ecommerce/') || path.startsWith('ecommerce/')) {
            return path.startsWith('/') ? path : '/' + path;
        }
        return `/ecommerce/${path}`;
    });
    const firstPhoto = photoUrls[0];
    const galleryJson = JSON.stringify(photoUrls);
    const title = itemBarcode ? `Item ${itemBarcode}` : "Images";
    
    return `<img src="${firstPhoto}" class="w-10 h-10 rounded-lg object-cover mx-auto cursor-pointer hover:ring-2 hover:ring-blue-500 transition-all" onclick='openGallery(${galleryJson})' title="Click to view all images" onerror="this.src='/images/placeholder.png'">`;
}
