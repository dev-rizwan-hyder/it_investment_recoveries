const RefurbishingEntries = {
    currentPage: 1,
    baseUrl: '/admin/refurbishing',
    storageUrl: '/refurbishing',
    currentItemId: null,
    currentEditingItem: null,
    newPhotosFiles: [],
    previewObjectUrls: [],
    repairPhotosFiles: [],
    photosToDelete: [],
    selectedShippoRates: [],
    latestShippoRequest: null,
    selectedIds: new Set(),

    init() {
        console.log('RefurbishingEntries initialized');
        console.log('Base URL:', this.baseUrl);
        console.log('Storage URL:', this.storageUrl);
        
        this.attachEventListeners();
        this.loadData();
    },

    attachEventListeners() {
        // Search and filter
        $('#search-input').on('keyup', this.debounce(() => {
            this.currentPage = 1;
            this.loadData();
        }, 300));

        $('#pallet-filter, #status-filter, #category-filter').on('change', () => {
            this.currentPage = 1;
            this.loadData();
        });

        $('#refresh-btn').on('click', () => this.loadData());
        $('#export-csv-btn').on('click', () => this.exportCsv());

        $('#select-all-items').on('change', (e) => {
            const isChecked = $(e.currentTarget).is(':checked');

            $('.item-select').each((_, el) => {
                const itemId = String($(el).data('item-id'));
                $(el).prop('checked', isChecked);

                if (isChecked) {
                    this.selectedIds.add(itemId);
                } else {
                    this.selectedIds.delete(itemId);
                }
            });

            this.updateBulkActions();
        });

        $(document).on('change', '.item-select', (e) => {
            const itemId = String($(e.currentTarget).data('item-id'));
            const isChecked = $(e.currentTarget).is(':checked');

            $('.item-select').filter((_, el) => String($(el).data('item-id')) === itemId).prop('checked', isChecked);

            if (isChecked) {
                this.selectedIds.add(itemId);
            } else {
                this.selectedIds.delete(itemId);
            }

            this.updateBulkActions();
        });

        $('#bulk-status-btn').on('click', () => this.bulkUpdateStatus());
        $('#bulk-delete-btn').on('click', () => this.bulkDeleteItems());

        // Modal editing
        $(document).on('click', '.btn-show-details', (e) => {
            const itemId = $(e.currentTarget).data('item-id');
            this.showItemDetails(itemId);
        });

        $(document).on('click', '.btn-edit-item', (e) => {
            const itemId = $(e.currentTarget).data('item-id');
            console.log('Edit button clicked for item:', itemId);
            this.editItem(itemId);
        });

        $(document).on('click', '.btn-add-inventory', (e) => {
            const itemId = $(e.currentTarget).data('item-id');
            this.prepareToInventory(itemId);
        });

        $(document).on('click', '.btn-print-label', (e) => {
            const itemId = $(e.currentTarget).data('item-id');
            this.printLabel(itemId);
        });

        $(document).on('click', '.btn-delete-item', (e) => {
            const itemId = $(e.currentTarget).data('item-id');
            if (confirm('Are you sure you want to delete this item?')) {
                this.deleteItem(itemId);
            }
        });

        // Gallery image handler
        $(document).on('click', '.gallery-image', (e) => {
            const photosData = $(e.currentTarget).data('photos');
            if (photosData && Array.isArray(photosData)) {
                openGallery(photosData);
            }
        });

        // Inline updates
        $(document).on('change', '.status-select', (e) => {
            const itemId = $(e.currentTarget).data('item-id');
            const newStatus = $(e.currentTarget).val();
            this.updateStatus(itemId, newStatus);
        });

        $(document).on('change', '.quantity-input', (e) => {
            const itemId = $(e.currentTarget).data('item-id');
            const newQuantity = $(e.currentTarget).val();
            const reuseQuantity = $(e.currentTarget).data('reuse-quantity');
            const scrapQuantity = $(e.currentTarget).data('scrap-quantity');
            const transferredQuantity = $(e.currentTarget).data('transferred-quantity');
            this.updateQuantity(itemId, newQuantity, reuseQuantity, scrapQuantity, transferredQuantity);
        });

        // Edit form submit - CRITICAL: Make sure this binds correctly
        console.log('Attaching edit form submit handler to #edit-form');
        $(document).on('submit', '#edit-form', (e) => {
            console.log('Edit form submitted!');
            e.preventDefault();
            this.handleEditSubmit();
        });

        // New photos upload
        $(document).on('change', '#new-photos', (e) => {
            const selectedFiles = Array.from(e.target.files || []);
            const existingKeys = new Set(
                this.newPhotosFiles.map(file => `${file.name}_${file.size}_${file.lastModified}`)
            );

            selectedFiles.forEach(file => {
                const key = `${file.name}_${file.size}_${file.lastModified}`;
                if (!existingKeys.has(key)) {
                    this.newPhotosFiles.push(file);
                    existingKeys.add(key);
                }
            });

            // Reset input so user can pick again (even same file) on next click
            e.target.value = '';
            this.updatePhotoPreview();
        });

        $(document).on('click', '#add-more-new-photos-btn', () => {
            $('#new-photos').trigger('click');
        });

        $(document).on('click', '.remove-new-photo-btn', (e) => {
            const index = parseInt($(e.currentTarget).data('new-photo-index'), 10);
            this.removeNewPhoto(index);
        });

        // Inventory form
        $('#inventory-form').on('submit', (e) => {
            e.preventDefault();
            this.handleSaveToInventory();
        });

        // Save to Inventory Modal Form
        $('#saveToInventoryForm').on('submit', (e) => {
            e.preventDefault();
            this.handleSaveToInventory();
        });

        // Shippo rates modal
        $(document).on('click', '#open-shippo-rates-btn', () => this.openShippoRatesModal());
        $(document).on('click', '#close-shippo-rates-btn, #shippo-rates-backdrop', () => this.closeShippoRatesModal());
        $(document).on('click', '#fetch-shippo-rates-btn', () => this.fetchShippoRates());
        $(document).on('click', '.select-shippo-rate-btn', (e) => {
            const raw = $(e.currentTarget).attr('data-rate');
            if (!raw) return;
            try {
                const rate = JSON.parse(raw.replace(/&#39;/g, "'"));
                this.selectShippoRate(rate, e.currentTarget);
            } catch (err) {
                console.error('Failed to parse selected rate', err);
            }
        });

        // Quantity validation for inventory transfer
        $(document).on('input', '#inv-quantity', () => {
            this.validateQuantity();
        });

        // Repair photos upload
        $('#repair-photos').on('change', (e) => {
            this.repairPhotosFiles = Array.from(e.target.files);
            this.updateRepairPhotoPreview();
        });

        // Quantity/weight calculations - use both 'input' for live updates and 'change' for final value
        $(document).on('input change', '#edit-quantity, #edit-reuse-qty, #edit-scrap-qty', () => {
            console.log('Quantity input/change event triggered');
            this.updateNetQuantityDisplay();
        });

        $(document).on('input change', '#edit-weight, #edit-reuse-weight, #edit-scrap-weight', () => {
            console.log('Weight input/change event triggered');
            this.updateNetWeightDisplay();
        });
    },

    toInt(value) {
        const parsed = parseInt(value, 10);
        return Number.isFinite(parsed) ? parsed : 0;
    },

    toFloat(value) {
        const parsed = parseFloat(value);
        return Number.isFinite(parsed) ? parsed : 0;
    },

    availableQuantity(source = {}) {
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

    availableWeight(source = {}) {
        if (source.net_weight !== undefined && source.net_weight !== null) {
            return Math.max(0, this.toFloat(source.net_weight));
        }

        return Math.max(
            0,
            this.toFloat(source.weight) - this.toFloat(source.reuse_weight) - this.toFloat(source.scrap_weight)
        );
    },

    getPhotoUrl(path) {
        if (!path) {
            return '/images/placeholder.png';
        }

        if (/^https?:\/\//.test(path)) {
            return path;
        }

        if (path.startsWith('/')) {
            return path;
        }

        if (path.startsWith('refurbishing/')) {
            return `/${path}`;
        }

        return `${this.storageUrl.replace(/\/$/, '')}/${path}`;
    },

    loadData() {
        const params = {
            search: $('#search-input').val(),
            statusFilter: $('#status-filter').val(),
            palletFilter: $('#pallet-filter').val(),
            categoryFilter: $('#category-filter').val(),
            page: this.currentPage,
            _t: Date.now(), // Cache buster
        };

        console.log('Loading data with params:', params);

        $.ajax({
            url: this.baseUrl,
            type: 'GET',
            data: params,
            cache: false, // Ensure no caching
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            success: (response) => {
                console.log('Data loaded successfully. Items count:', response.items.length);
                console.log('First item status:', response.items.length > 0 ? response.items[0].status : 'N/A');
                this.selectedIds.clear();
                this.renderStats(response.stats);
                this.renderTable(response.items);
                this.renderMobileCards(response.items);
                this.renderPagination(response.pagination);
                this.populatePalletFilter(response.items);
                this.updateBulkActions();
            },
            error: (xhr, status, error) => {
                if (xhr.status !== 404) {
                    this.showError('Failed to load items: ' + error);
                    console.error('Load error:', xhr.responseText);
                }
            }
        });
    },

    renderStats(stats) {
        const html = stats.map(stat => `
            <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-100 shadow-sm transition-all hover:shadow-md relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 w-1.5 ${stat.color.replace('text', 'bg')}"></div>
                <div class="pl-2">
                    <p class="text-[10px] md:text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1 opacity-80">
                        ${stat.label}
                    </p>
                    <h3 class="text-xl md:text-2xl font-black ${stat.color} tracking-tight">
                        ${stat.value}
                    </h3>
                </div>
            </div>
        `).join('');
        $('#stats-container').html(html);
    },

    renderTable(items) {
        if (items.length === 0) {
            $('#table-body').html(`
                <tr>
                    <td colspan="12" class="p-16 text-center text-slate-300 font-bold uppercase text-xs">No Records Found</td>
                </tr>
            `);
            return;
        }

        const html = items.map(item => this.renderTableRow(item)).join('');
        $('#table-body').html(html);
    },

    formatItemsSummary(item) {
        const rawName = item.name || '';
        const rawBrand = item.brand || '';

        const names = rawName ? rawName.split(',').map(s => s.trim()).filter(Boolean) : [];
        const brands = rawBrand ? rawBrand.split(',').map(s => s.trim()).filter(Boolean) : [];

        const firstName = names.length > 0 ? names[0] : (rawName || 'N/A');
        const firstBrand = brands.length > 0 ? brands[0] : (rawBrand || '');

        const extraCount = names.length > 1 ? names.length - 1 : 0;

        const fullTooltipName = this.escapeHtml ? this.escapeHtml(names.join(', ') || rawName) : (names.join(', ') || rawName);
        const fullTooltipBrand = this.escapeHtml ? this.escapeHtml(brands.join(', ') || rawBrand) : (brands.join(', ') || rawBrand);
        const safeFirstName = this.escapeHtml ? this.escapeHtml(firstName) : firstName;
        const safeFirstBrand = this.escapeHtml ? this.escapeHtml(firstBrand) : firstBrand;

        let badgeHtml = '';
        if (extraCount > 0) {
            badgeHtml = `<span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-black bg-amber-100 text-amber-700 border border-amber-200 shrink-0" title="All items: ${fullTooltipName}">+ ${extraCount} more</span>`;
        }

        return `
            <div class="truncate" title="All items: ${fullTooltipName}">
                <div class="font-extrabold text-slate-900 leading-tight mb-0.5 flex items-center min-w-0 gap-1.5">
                    <span class="truncate min-w-0 flex-1">${safeFirstName}</span>
                    ${badgeHtml}
                </div>
                <p class="text-[9px] text-slate-400 font-bold uppercase truncate" title="${fullTooltipBrand}">${safeFirstBrand}</p>
            </div>
        `;
    },

    renderTableRow(item) {
        const netQuantity = this.availableQuantity(item);
        const statusColor = item.status === 'Completed' 
            ? 'bg-emerald-100 text-emerald-700 border-emerald-200'
            : item.status === 'In Progress'
            ? 'bg-amber-400 text-white'
            : item.status === 'Live Online'
            ? 'bg-emerald-500 text-white'
            : 'bg-blue-500 text-white';

        const photoUrl = item.photos_paths && item.photos_paths.length > 0
            ? this.getPhotoUrl(item.photos_paths[0])
            : '';

        // Build category hierarchy
        let categoryDisplay = item.category || '-';
        if (item.sub_category) {
            categoryDisplay = `${item.category} > ${item.sub_category}`;
        }
        if (item.sub_sub_category) {
            categoryDisplay += ` > ${item.sub_sub_category}`;
        }

        const statusHtml = `
            <select class="status-select w-full px-2 py-2 rounded-lg text-[10px] font-black uppercase border-none cursor-pointer shadow-sm ${statusColor}"
                style="width: 100%; min-width: 10rem;"
                data-item-id="${item.id}">
                <option value="Recieved" ${item.status === 'Recieved' ? 'selected' : ''}>Recieved</option>
                <option value="Ready for Inventory" ${item.status === 'Ready for Inventory' ? 'selected' : ''}>Ready for Inventory</option>
                <option value="In Progress" ${item.status === 'In Progress' ? 'selected' : ''}>In Progress</option>
                <option value="Live Online" ${item.status === 'Live Online' ? 'selected' : ''}>Live Online</option>
                <option value="Completed" ${item.status === 'Completed' ? 'selected' : ''}>Completed</option>
                ${!['Ready for Inventory', 'In Progress', 'Live Online', 'Recieved', 'Completed'].includes(item.status) ? `<option value="${item.status}" selected>${item.status}</option>` : ''}
            </select>
        `;

        return `
            <tr class="hover:bg-slate-50/50 transition-colors text-[13px] text-slate-700">
                <td class="px-4 py-4 text-center">
                    <input type="checkbox" class="item-select h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500" data-item-id="${item.id}">
                </td>
                <td class="px-4 py-4 font-bold text-slate-900 uppercase truncate">${item.barcode}</td>
                <td class="px-4 py-4">
                    ${this.formatItemsSummary(item)}
                </td>
                <td class="px-4 py-4 text-[11px] font-semibold uppercase text-slate-500 truncate" title="${categoryDisplay}">${categoryDisplay}</td>
                <td class="px-4 py-4">
                    <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-600 border-amber-100 text-[9px] font-black uppercase">
                        Refurbishing
                    </span>
                </td>
                <td class="px-4 py-4 text-center">
                    ${RefurbishingEntries.renderPhotoThumbnail(item.photos_paths, item.barcode)}
                </td>
                <td class="px-4 py-4 text-center">
                    <input type="number" min="0" value="${netQuantity}" data-item-id="${item.id}" data-reuse-quantity="${this.toInt(item.reuse_quantity)}" data-scrap-quantity="${this.toInt(item.scrap_quantity)}" data-transferred-quantity="${this.toInt(item.transferred_quantity)}" class="quantity-input w-14 p-1 border border-slate-200 rounded-md text-center font-bold text-slate-800 text-sm">
                </td>
                <td class="px-4 py-4" style="width: 11rem; min-width: 11rem;">${statusHtml}</td>
                <td class="px-4 py-4 font-black text-slate-900 text-sm">$${parseFloat(item.estimated_value).toFixed(2)}</td>
                <td class="px-4 py-4 font-bold text-slate-400 text-[11px]">${(!item.pallet_number || item.pallet_number === 'undefined') ? 'N/A' : item.pallet_number}</td>
                <td class="px-4 py-4 text-slate-400 font-medium text-[11px]">${new Date(item.created_at).toLocaleDateString()}</td>
                <td class="px-4 py-4">
                    <div class="flex flex-row items-center justify-end gap-1.5">
                        ${netQuantity === 0
                            ? `<button disabled class="h-9 px-4 min-w-max rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm border bg-emerald-50 text-emerald-600 border-emerald-100 cursor-not-allowed opacity-90">
                                <i class="fas fa-check-double text-sm"></i>
                                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">All Transferred</span>
                              </button>`
                            : `<button class="btn-add-inventory h-9 px-4 min-w-max rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm border bg-blue-50 text-blue-600 border-blue-100 hover:bg-blue-600 hover:text-white active:scale-95" data-item-id="${item.id}">
                                <i class="fas fa-plus-circle text-sm"></i>
                                <span class="text-xs font-semibold uppercase tracking-wider">Add to Inventory</span>
                              </button>`
                        }
                        <button class="btn-show-details h-9 w-9 bg-slate-50 text-slate-400 rounded-xl transition-all hover:bg-slate-900 hover:text-white flex items-center justify-center active:scale-90" data-item-id="${item.id}" title="View Details">
                            <i class="fas fa-eye text-xs"></i>
                        </button>
                        <button class="btn-print-label h-9 w-9 bg-orange-50 text-orange-500 rounded-xl transition-all hover:bg-orange-500 hover:text-white flex items-center justify-center active:scale-90" data-item-id="${item.id}" title="Print Label">
                            <i class="fas fa-print text-xs"></i>
                        </button>
                        <button class="btn-edit-item h-9 w-9 bg-emerald-50 text-emerald-600 rounded-xl transition-all hover:bg-emerald-600 hover:text-white flex items-center justify-center active:scale-90" data-item-id="${item.id}" title="Edit Item">
                            <i class="fas fa-edit text-xs"></i>
                        </button>
                        <button class="btn-delete-item h-9 w-9 bg-red-50 text-red-400 rounded-xl transition-all hover:bg-red-500 hover:text-white flex items-center justify-center active:scale-90" data-item-id="${item.id}" title="Delete Item">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    },

    renderMobileCards(items) {
        if (items.length === 0) {
            $('#mobile-container').html(`
                <div class="p-10 text-center text-slate-300 font-bold uppercase text-xs">No Records Found</div>
            `);
            return;
        }

        const html = items.map(item => {
            const netQuantity = this.availableQuantity(item);

            return `
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 space-y-4">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" class="item-select h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500" data-item-id="${item.id}">
                            ${item.photos_paths && item.photos_paths.length > 0 
                                ? item.photos_paths.length === 1
                                    ? `<img src="${this.getPhotoUrl(item.photos_paths[0])}" class="w-12 h-12 rounded-xl border object-cover cursor-pointer hover:opacity-80 gallery-image" data-photos='${JSON.stringify(item.photos_paths.map(p => this.getPhotoUrl(p)))}'>`
                                    : `<div class="relative w-12 h-12 group cursor-pointer gallery-image" data-photos='${JSON.stringify(item.photos_paths.map(p => this.getPhotoUrl(p)))}'>
                                        <img src="${this.getPhotoUrl(item.photos_paths[0])}" class="w-full h-full rounded-xl border object-cover">
                                        <div class="absolute inset-0 bg-black/40 rounded-xl transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                                            <span class="text-white font-bold text-sm">+${item.photos_paths.length - 1}</span>
                                        </div>
                                      </div>`
                                : `<div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-slate-300"><i class="fas fa-box"></i></div>`}
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase mb-0.5">${item.barcode}</p>
                                <h4 class="text-sm font-black text-slate-900 leading-tight">${item.name}</h4>
                                <p class="text-[10px] font-bold text-amber-600 uppercase">${item.category}</p>
                            </div>
                        </div>
                        <p class="text-sm font-black text-slate-900">$${parseFloat(item.estimated_value).toFixed(2)}</p>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-slate-50">
                        <div class="text-[10px] font-bold text-slate-400">STATUS: <span class="text-slate-900 ${item.status === 'Completed' ? 'text-emerald-600' : ''}">${item.status}</span></div>
                        <div class="flex gap-2">
                            <button class="btn-show-details p-2 text-slate-300 hover:text-blue-500" data-item-id="${item.id}"><i class="fas fa-eye"></i></button>
                            <button class="btn-print-label p-2 text-slate-300 hover:text-orange-500" data-item-id="${item.id}"><i class="fas fa-print"></i></button>
                            <button class="btn-edit-item p-2 text-slate-300 hover:text-emerald-500" data-item-id="${item.id}"><i class="fas fa-edit"></i></button>
                            <button class="btn-delete-item p-2 text-slate-300 hover:text-red-400" data-item-id="${item.id}"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        $('#mobile-container').html(html);
    },

    renderPagination(pagination) {
        if (pagination.last_page <= 1) {
            $('#pagination-container').html('');
            return;
        }

        let html = '<nav class="flex items-center gap-2"><ul class="flex gap-1">';
        
        // Previous button
        if (pagination.current_page > 1) {
            html += `<li><button class="px-3 py-2 border rounded hover:bg-slate-100" onclick="RefurbishingEntries.currentPage = ${pagination.current_page - 1}; RefurbishingEntries.loadData();">← Prev</button></li>`;
        }

        // Page numbers
        for (let i = 1; i <= pagination.last_page; i++) {
            if (i === pagination.current_page) {
                html += `<li><span class="px-3 py-2 bg-blue-600 text-white rounded">${i}</span></li>`;
            } else {
                html += `<li><button class="px-3 py-2 border rounded hover:bg-slate-100" onclick="RefurbishingEntries.currentPage = ${i}; RefurbishingEntries.loadData();">${i}</button></li>`;
            }
        }

        // Next button
        if (pagination.current_page < pagination.last_page) {
            html += `<li><button class="px-3 py-2 border rounded hover:bg-slate-100" onclick="RefurbishingEntries.currentPage = ${pagination.current_page + 1}; RefurbishingEntries.loadData();">Next →</button></li>`;
        }

        html += '</ul></nav>';
        $('#pagination-container').html(html);
    },

    populatePalletFilter(items) {
        const pallets = [...new Set(
            items
                .map(item => item.pallet_number)
                .filter(pallet => pallet && pallet !== 'undefined')
        )];
        let html = '<option value="">All Pallets</option>';
        pallets.forEach(pallet => {
            html += `<option value="${pallet}">${pallet}</option>`;
        });
        $('#pallet-filter').html(html);
    },

    getSelectedIds() {
        return Array.from(this.selectedIds).map(id => parseInt(id, 10)).filter(Boolean);
    },

    syncSelectAll() {
        const checkboxes = $('.item-select');
        const checkedCount = checkboxes.filter(':checked').length;

        $('#select-all-items')
            .prop('checked', checkboxes.length > 0 && checkedCount === checkboxes.length)
            .prop('indeterminate', checkedCount > 0 && checkedCount < checkboxes.length);
    },

    updateBulkActions() {
        const selectedCount = this.selectedIds.size;

        $('#bulk-selected-count').text(`${selectedCount} selected`);
        $('#bulk-actions').toggleClass('hidden', selectedCount === 0);
        $('#bulk-status-btn, #bulk-delete-btn')
            .prop('disabled', selectedCount === 0)
            .toggleClass('opacity-50 cursor-not-allowed', selectedCount === 0);

        this.syncSelectAll();
    },

    bulkUpdateStatus() {
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
            }
        });
    },

    bulkDeleteItems() {
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
                this.showError(xhr.responseJSON?.error || xhr.responseJSON?.message || 'Failed to delete selected items');
            }
        });
    },

    showItemDetails(itemId) {
        this.currentItemId = itemId;

        $.ajax({
            url: `${this.baseUrl}/${itemId}/details`,
            type: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: (item) => {
                console.log('Item details loaded:', item);

                // Header
                const names = item.name ? item.name.split(',').map(s => s.trim()) : [];
                const titleName = names.length > 0 ? names[0] : 'Item';
                $('#refurb-modal-title').text(`View Refurbishing Item: ${titleName}`);

                // Status badge
                const status = item.status || '';
                let statusClass = 'bg-slate-500';
                if (['Shipped', 'Delivered', 'Live Online'].includes(status)) statusClass = 'bg-emerald-500';
                else if (status === 'In Progress') statusClass = 'bg-amber-500';
                else if (['Scrapped', 'Scrap'].includes(status)) statusClass = 'bg-red-500';
                else if (status === 'Ready for Inventory') statusClass = 'bg-blue-500';
                $('#refurb-modal-status').text(status).removeClass().addClass(`px-3 py-1 text-[10px] font-black uppercase rounded-lg text-white ${statusClass}`);

                // Category
                $('#refurb-modal-category').text(item.category || 'N/A');
                $('#refurb-modal-sub-category').text(item.sub_category || 'N/A');
                $('#refurb-modal-sub-sub-category').text(item.sub_sub_category || 'N/A');
                $('#refurb-modal-condition').text(item.condition || 'N/A');

                // Value & quantities
                const netQty = this.availableQuantity(item);
                const netWeight = this.availableWeight(item);
                $('#refurb-modal-value').text(`$${parseFloat(item.estimated_value || 0).toFixed(2)}`);
                $('#refurb-modal-qty-value').text(item.quantity || 0);
                $('#refurb-modal-reuse-quantity').text(item.reuse_quantity || 0);
                $('#refurb-modal-scrap-quantity').text(item.scrap_quantity || 0);
                $('#refurb-modal-remaining-qty').text(`${netQty} PCS`);

                // Weight
                $('#refurb-modal-weight').text(parseFloat(item.weight || 0).toFixed(2));
                $('#refurb-modal-reuse-weight').text(parseFloat(item.reuse_weight || 0).toFixed(2));
                $('#refurb-modal-scrap-weight').text(parseFloat(item.scrap_weight || 0).toFixed(2));
                $('#refurb-modal-net-weight').text(`${parseFloat(netWeight).toFixed(2)} Lbs`);

                // Location & Employee
                $('#refurb-modal-pallet').text((!item.pallet_number || item.pallet_number === 'undefined') ? 'N/A' : item.pallet_number);
                $('#refurb-modal-placement').text(item.item_placement || 'N/A');
                $('#refurb-modal-receiving-employee').text(item.receiving_employee || 'N/A');
                $('#refurb-modal-processing-employee').text(item.processing_employee || 'N/A');
                $('#refurb-modal-refurbishing-employee').text(item.refurbishing_employee || item.intake_employee || 'N/A');

                // Notes
                $('#refurb-modal-notes').text(item.notes || 'No internal notes provided.');

                // Last modified
                $('#refurb-modal-updated-at').text(new Date(item.updated_at).toLocaleDateString('en-US', { month: '2-digit', day: '2-digit', year: '2-digit', hour: '2-digit', minute: '2-digit' }));

                // Barcode
                $('#refurb-modal-barcode-img').attr('src', `https://bwipjs-api.metafloor.com/?bcid=code128&text=${encodeURIComponent(item.barcode)}&scale=2&height=10&includetext=true`);

                // Item cards
                this.renderRefurbModalItems(item);

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
                $('#refurb-modal-photos').html(photosHtml);

                // Action buttons
                $('#refurb-btn-modal-print').off('click').on('click', () => this.printLabel(itemId));
                $('#refurb-btn-modal-edit').off('click').on('click', () => this.editItem(itemId));

                $('#itemDetailsModal').removeClass('hidden');
            },
            error: (xhr, status, error) => {
                this.showError('Failed to load item details: ' + error);
                console.error('Error:', xhr.responseText);
            }
        });
    },

    renderRefurbModalItems(item) {
        const container = document.getElementById('refurb-modal-items-list');
        if (!container) return;

        const names   = item.name          ? item.name.split(',').map(s => s.trim())          : [];
        const brands  = item.brand         ? item.brand.split(',').map(s => s.trim())         : [];
        const models  = item.model         ? item.model.split(',').map(s => s.trim())         : [];
        const serials = item.serial_number ? item.serial_number.split(',').map(s => s.trim()) : [];

        const maxLen = Math.max(names.length, brands.length, models.length, serials.length, 1);

        const badge = document.getElementById('refurb-modal-items-count');
        if (badge) badge.textContent = `${maxLen} Item${maxLen > 1 ? 's' : ''}`;

        const getItemVal = (arr, index, defaultVal = 'N/A') => {
            if (!arr || arr.length === 0) return defaultVal;
            if (arr[index] !== undefined && arr[index] !== '') return arr[index];
            const fallback = arr[arr.length - 1] || arr[0];
            return (fallback !== undefined && fallback !== '') ? fallback : defaultVal;
        };

        const e = s => String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        let html = '';
        for (let i = 0; i < maxLen; i++) {
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

    editItem(itemId) {
        this.currentItemId = itemId;
        this.photosToDelete = [];
        this.newPhotosFiles = [];

        $.ajax({
            url: `${this.baseUrl}/${itemId}/edit`,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            success: (data) => {
                console.log('Edit modal loaded:', data);
                
                const item = data.item;
                this.currentEditingItem = item;

                // Store item ID in hidden input
                $('#edit-item-id').val(item.id);

                $('#edit-barcode').text(`Barcode: ${item.barcode}`);
                $('#edit-name').val(item.name);
                $('#edit-serial').val(item.serial_number || '');
                $('#edit-value').val(item.estimated_value || 0);
                $('#edit-brand').val(item.brand || '');
                $('#edit-model').val(item.model || '');
                $('#edit-condition').val(item.condition || '');
                $('#edit-quantity').val(item.quantity || 0);
                $('#edit-reuse-qty').val(item.reuse_quantity || 0);
                $('#edit-scrap-qty').val(item.scrap_quantity || 0);
                $('#edit-weight').val(item.weight || 0);
                $('#edit-reuse-weight').val(item.reuse_weight || 0);
                $('#edit-scrap-weight').val(item.scrap_weight || 0);
                const defaultPallet = (item.pallet_number || '').toString().trim();
                $('#edit-notes').val(item.notes || '');
                $('#edit-placement').val(item.item_placement || '');
                $('#edit-receiving-employee').val(item.receiving_employee || '');
                $('#edit-processing-employee').val(item.processing_employee || '');
                $('#edit-refurbishing-employee').val(item.refurbishing_employee || item.intake_employee || '');

                this.initializeEditCategoryHierarchy(item);

                // Populate pallet dropdown
                let palletHtml = '<option value="">-- No Pallet Assigned --</option>';
                let foundDefaultPallet = false;
                if (data.pallets_list) {
                    data.pallets_list.forEach(pallet => {
                        const palletValue = (pallet.barcode_number || '').toString().trim();
                        const isMatch = palletValue.toLowerCase() === defaultPallet.toLowerCase();
                        if (isMatch) foundDefaultPallet = true;
                        palletHtml += `<option value="${palletValue}" ${isMatch ? 'selected' : ''}>${palletValue}</option>`;
                    });
                }

                if (defaultPallet && !foundDefaultPallet) {
                    palletHtml += `<option value="${defaultPallet}" selected>${defaultPallet}</option>`;
                }
                $('#edit-pallet').html(palletHtml);
                $('#edit-pallet').val(defaultPallet);

                // Display existing photos
                let existingPhotosHtml = '';
                if (item.photos_paths && item.photos_paths.length > 0) {
                    item.photos_paths.forEach(path => {
                        existingPhotosHtml += `
                            <div class="relative w-24 h-24 group rounded-2xl overflow-hidden border border-slate-200 shadow-sm" data-photo-path="${path}">
                                <img src="${this.getPhotoUrl(path)}" class="w-full h-full object-cover">
                                <button type="button" class="absolute inset-0 bg-red-600/70 text-white opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all duration-200" onclick="RefurbishingEntries.markPhotoForDeletion('${path}')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        `;
                    });
                }
                $('#existing-photos').html(existingPhotosHtml);

                this.updateNetQuantityDisplay();
                this.updateNetWeightDisplay();
                $('#new-photos').val('');
                this.updatePhotoPreview();

                // Attach live update events for weight inputs (do not mirror reuse/scrap)
                $('#edit-weight, #edit-reuse-weight, #edit-scrap-weight').off('input change').on('input change', () => {
                    this.updateNetWeightDisplay();
                });

                $('#edit-quantity, #edit-reuse-qty, #edit-scrap-qty').off('input change').on('input change', () => {
                    this.updateNetQuantityDisplay();
                });

                $('#editItemModal').removeClass('hidden');
                $('#itemDetailsModal').addClass('hidden');
                this.updatePhotoPreview();
            },
            error: (xhr, status, error) => {
                this.showError('Failed to load edit form: ' + error);
            }
        });
    },

    initializeEditCategoryHierarchy(item) {
        if (!window.categoryHierarchyManager) {
            return;
        }

        window.categoryHierarchyManager.whenReady().then(() => {
            this.populateEditCategoryHierarchy(item);
        });
    },

    populateEditCategoryHierarchy(item) {
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
        $('#edit-sub-sub-category').val(subSubCategoryId ? (categoryMap[subSubCategoryId]?.name || item.sub_sub_category || '') : (item.sub_sub_category || ''));
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

    markPhotoForDeletion(path) {
        if (!this.photosToDelete.includes(path)) {
            this.photosToDelete.push(path);
        }
        $(`[data-photo-path="${path}"]`).fadeOut();
        
        // Remove from display
        $(`img[src*="${path}"]`).closest('[data-photo-path]').fadeOut();
        
        // Actually remove the element
        setTimeout(() => {
            $(`img[src*="${path}"]`).parent().remove();
        }, 300);
    },

    getPhotoUrl(path) {
        const inlinePlaceholder = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120"><rect width="100%" height="100%" fill="%23f1f5f9"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="%2394a3b8" font-size="12" font-family="Arial">No Image</text></svg>';
        if (!path) return inlinePlaceholder;
        if (typeof path !== 'string') return inlinePlaceholder;
        if (path.startsWith('http://') || path.startsWith('https://')) return path;
        if (path.startsWith('/')) return path;
        if (path.includes('/')) return `/${path}`;
        return `${this.storageUrl}/${path}`;
    },

    updatePhotoPreview() {
        this.previewObjectUrls.forEach((url) => URL.revokeObjectURL(url));
        this.previewObjectUrls = [];

        const previewsHtml = this.newPhotosFiles.map((file, index) => {
            const previewUrl = URL.createObjectURL(file);
            this.previewObjectUrls.push(previewUrl);
            return `
                <div class="relative w-24 h-24 rounded-2xl overflow-hidden border-2 border-blue-400 shadow-md group" data-new-photo-index="${index}">
                    <img src="${previewUrl}" class="w-full h-full object-cover">
                    <button type="button" class="absolute inset-0 bg-red-600/70 text-white opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all duration-200 remove-new-photo-btn" data-new-photo-index="${index}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            `;
        }).join('');

        const addMoreTile = `
            <button type="button" id="add-more-new-photos-btn" class="w-24 h-24 bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl flex flex-col items-center justify-center hover:bg-blue-50 hover:border-blue-300 transition-all group">
                <i class="fas fa-plus text-slate-300 group-hover:text-blue-500 mb-1"></i>
                <span class="text-[8px] font-black text-slate-400 group-hover:text-blue-600 uppercase tracking-tighter">Add More</span>
            </button>
        `;

        $('#new-photos-preview').html(previewsHtml + addMoreTile);
    },

    removeNewPhoto(index) {
        if (index < 0 || index >= this.newPhotosFiles.length) return;
        this.newPhotosFiles.splice(index, 1);
        this.updatePhotoPreview();
    },

    removePhotoPreview(index) {
        this.newPhotosFiles.splice(index, 1);
        // Update the file input
        const dt = new DataTransfer();
        this.newPhotosFiles.forEach(file => dt.items.add(file));
        $('#new-photos')[0].files = dt.files;
        this.updatePhotoPreview();
    },

    updateRepairPhotoPreview() {
        let html = '';
        this.repairPhotosFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const preview = `
                    <div class="relative w-24 h-24 rounded-2xl overflow-hidden border-2 border-green-400 shadow-md">
                        <img src="${e.target.result}" class="w-full h-full object-cover">
                    </div>
                `;
                $('#repair-photos-preview').append(preview);
            };
            reader.readAsDataURL(file);
        });
    },

    updateNetQuantityDisplay() {
        const quantity = parseInt($('#edit-quantity').val()) || 0;
        const reusedQty = parseInt($('#edit-reuse-qty').val()) || 0;
        const scrapQty = parseInt($('#edit-scrap-qty').val()) || 0;
        const deductedQty = reusedQty + scrapQty;
        const net = Math.max(0, quantity - deductedQty);
        $('#edit-total-qty-summary').text(quantity);
        $('#edit-reuse-qty-summary').text(reusedQty);
        $('#edit-scrap-qty-summary').text(scrapQty);
        $('#net-quantity-display').text(`${net} Units`);
        if (net < 0) {
            $('#net-quantity-display').removeClass('text-emerald-600').addClass('text-red-500');
        } else {
            $('#net-quantity-display').removeClass('text-red-500').addClass('text-emerald-600');
        }
    },

    updateNetWeightDisplay() {
        console.log('updateNetWeightDisplay called');
        const weight = parseFloat($('#edit-weight').val()) || 0;
        const reusedWeight = parseFloat($('#edit-reuse-weight').val()) || 0;
        const scrapWeight = parseFloat($('#edit-scrap-weight').val()) || 0;
        const deductedWeight = reusedWeight + scrapWeight;
        console.log('Weight:', weight, 'Reused Weight:', reusedWeight, 'Scrap Weight:', scrapWeight);
        const net = Math.max(0, weight - deductedWeight);
        console.log('Net weight:', net);
        $('#edit-total-weight-summary').text(weight.toFixed(2));
        $('#edit-reuse-weight-summary').text(reusedWeight.toFixed(2));
        $('#edit-scrap-weight-summary').text(scrapWeight.toFixed(2));
        $('#net-weight-display').text(`${net.toFixed(2)} Lbs`);
        if (net < 0) {
            $('#net-weight-display').removeClass('text-blue-600').addClass('text-red-500');
        } else {
            $('#net-weight-display').removeClass('text-red-500').addClass('text-blue-600');
        }
    },

    handleEditSubmit() {
        const itemId = $('#edit-item-id').val();
        
        console.log('handleEditSubmit called');
        console.log('Item ID from hidden input:', itemId);
        console.log('Item ID from currentItemId:', this.currentItemId);
        
        if (!itemId) {
            console.error('NO ITEM ID FOUND IN HIDDEN INPUT!');
            this.showError('No item ID found. Please reload and try again.');
            return;
        }

        const formData = new FormData();
        
        // Get all values
        const name = $('#edit-name').val();
        const brand = $('#edit-brand').val();
        const model = $('#edit-model').val();
        const serial = $('#edit-serial').val();
        const category = $('#edit-category').val();
        const subCategory = $('#edit-sub-category').val();
        const subSubCategory = $('#edit-sub-sub-category').val();
        const categoryId = $('#edit-category-id').val();
        const subCategoryId = $('#edit-sub-category-id').val();
        const subSubCategoryId = $('#edit-sub-sub-category-id').val();
        const condition = $('#edit-condition').val();
        const value = $('#edit-value').val();
        const weight = $('#edit-weight').val();
        const reuse_weight = $('#edit-reuse-weight').val();
        const scrap_weight = $('#edit-scrap-weight').val();
        const quantity = $('#edit-quantity').val();
        const reuse_qty = $('#edit-reuse-qty').val();
        const scrap_qty = $('#edit-scrap-qty').val();
        const palletInput = $('#edit-pallet');
        const rawPallet = palletInput.length
            ? palletInput.val()
            : (this.currentEditingItem?.pallet_number || '');
        const pallet = (rawPallet && rawPallet !== 'undefined') ? rawPallet : '';
        const notes = $('#edit-notes').val();
        const placement = $('#edit-placement').val();
        const receivingEmployee = $('#edit-receiving-employee').val();
        const processingEmployee = $('#edit-processing-employee').val();
        const refurbishingEmployee = $('#edit-refurbishing-employee').val();

        formData.append('name', name);
        formData.append('brand', brand);
        formData.append('model', model);
        formData.append('serial_number', serial);
        formData.append('category', category);
        formData.append('sub_category', subCategory);
        formData.append('sub_sub_category', subSubCategory);
        formData.append('category_id', categoryId);
        formData.append('sub_category_id', subCategoryId);
        formData.append('sub_sub_category_id', subSubCategoryId);
        formData.append('condition', condition);
        formData.append('estimated_value', value);
        formData.append('weight', weight);
        formData.append('reuse_weight', reuse_weight);
        formData.append('scrap_weight', scrap_weight);
        formData.append('quantity', quantity);
        formData.append('reuse_quantity', reuse_qty);
        formData.append('scrap_quantity', scrap_qty);
        formData.append('pallet_number', pallet);
        formData.append('notes', notes);
        formData.append('item_placement', placement);
        formData.append('receiving_employee', receivingEmployee);
        formData.append('processing_employee', processingEmployee);
        formData.append('refurbishing_employee', refurbishingEmployee);
        formData.append('intake_employee', refurbishingEmployee);

        console.log('=== FORM DATA ===');
        console.log('Name:', name);
        console.log('Brand:', brand);
        console.log('Category:', category);
        console.log('Value:', value);
        console.log('Quantity:', quantity);
        console.log('==================');

        this.photosToDelete.forEach((path) => {
            formData.append('photos_to_delete[]', path);
        });

        const inputFiles = Array.from($('#new-photos')[0]?.files || []);
        const filesToUpload = inputFiles.length > 0 ? inputFiles : this.newPhotosFiles;
        console.log('New photos selected for upload:', filesToUpload.length);
        filesToUpload.forEach((file) => {
            formData.append('new_photos[]', file);
        });

        $('#edit-submit-spinner').removeClass('hidden');
        $('#edit-submit-text').text('Saving');

        const url = `${this.baseUrl}/${itemId}`;
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        
        console.log('AJAX Request Details:');
        console.log('  URL:', url);
        console.log('  Method: POST');
        console.log('  CSRF Token:', csrfToken ? 'Present' : 'MISSING!!!');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            },
            success: (response) => {
                console.log('✓ SUCCESS from server:', response);
                this.showSuccess('Item updated successfully!');
                $('#edit-submit-spinner').addClass('hidden');
                $('#edit-submit-text').text('Update Record');
                this.closeModal();
                this.loadData();
            },
            error: (xhr, status, error) => {
                console.error('✗ AJAX ERROR');
                console.error('Status:', xhr.status);
                console.error('Status Text:', xhr.statusText);
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);
                
                $('#edit-submit-spinner').addClass('hidden');
                $('#edit-submit-text').text('Update Record');
                
                let errorMsg = 'Failed to update item';
                if (xhr.status === 404) {
                    errorMsg = '404: Item not found. Check if item ID is correct.';
                } else if (xhr.status === 422) {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        errorMsg = Object.values(errors).flat().join(', ');
                    } else {
                        errorMsg = 'Validation failed';
                    }
                } else if (xhr.status === 419) {
                    errorMsg = '419: CSRF token expired - refresh page';
                } else if (xhr.status === 500) {
                    errorMsg = '500: Server error - check logs';
                } else if (xhr.responseJSON?.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                console.log('Error message to show:', errorMsg);
                this.showError(errorMsg);
            }
        });
    },

    handleSaveToInventory() {
        const itemId = $('#inventory-item-id').val();
        const availableQty = parseInt($('#inv-quantity').data('available')) || 0;
        const selectedQty = parseInt($('#inv-quantity').val()) || 0;
        const errorMsg = $('#quantity-error');
        
        if (!itemId) {
            this.showError('No item selected. Please try again.');
            return;
        }
        
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
        
        const formData = new FormData();
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
        formData.append('lot_info', $('#inv-lot-info').val());
        formData.append('serial_number', $('#inv-serial-number').val());
        formData.append('status', $('#inv-status').val());
        formData.append('pallet_info', $('#inv-pallet-info').val());
        formData.append('device_specs', $('#inv-device-specs').val());
        formData.append('operating_system', $('#inv-os').val());
        formData.append('notes', $('#inv-notes').val());
        formData.append('barcode', $('#inv-barcode').val() || '');
        formData.append('put_away_location', $('#inv-location').val());
        formData.append('parcel_length', $('#shippo-length').val() || '');
        formData.append('parcel_width', $('#shippo-width').val() || '');
        formData.append('parcel_height', $('#shippo-height').val() || '');
        formData.append('parcel_weight', $('#shippo-weight').val() || '');
        formData.append('distance_unit', $('#shippo-distance-unit').val() || 'in');
        formData.append('mass_unit', $('#shippo-mass-unit').val() || 'lb');

        // Handle repair photos - use accumulated files from the modal manager
        if (window.inventoryPhotoManager && window.inventoryPhotoManager.accumulatedFiles) {
            window.inventoryPhotoManager.accumulatedFiles.forEach((item) => {
                const fileObj = item && item.file ? item.file : item;
                formData.append('repair_photos[]', fileObj);
            });
        }

        $('#inv-submit-spinner').removeClass('hidden');
        $('#inv-submit-text').text('Processing...');
        
        console.log('Submitting to inventory. Item ID:', itemId, 'Qty:', selectedQty);

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
                console.log('Success:', response);
                
                // Reset spinner
                $('#inv-submit-spinner').addClass('hidden');
                $('#inv-submit-text').text('Save to Inventory');
                
                this.showSuccess(response.message);
                
                // Reset filters to show updated item
                $('#search-input').val('');
                $('#status-filter').val('');
                $('#pallet-filter').val('');
                $('#category-filter').val('');
                this.currentPage = 1;
                
                // Close modal
                $('#addToInventoryModal').addClass('hidden');
                
                // Small delay then reload to ensure database is updated
                setTimeout(() => {
                    console.log('Reloading data after save...');
                    // Clear any cached pagination
                    this.currentPage = 1;
                    // Force reload with no filters
                    this.loadData();
                }, 500);
            },
            error: (xhr, status, error) => {
                console.error('Error:', {status, error, xhr});
                $('#inv-submit-spinner').addClass('hidden');
                $('#inv-submit-text').text('Save to Inventory');
                
                let errorMsg = 'Failed to save to inventory';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                } else if (xhr.status === 422) {
                    errorMsg = 'Please fill in all required fields';
                } else if (xhr.status === 404) {
                    errorMsg = 'Item not found';
                }
                
                this.showError(errorMsg);
            }
        });
    },

    openShippoRatesModal() {
        this.selectedShippoRates = [];
        $('#shippo-rates-error').addClass('hidden').text('');
        $('#shippo-rates-list').html('');
        $('#shippoRatesModal').removeClass('hidden');
    },

    closeShippoRatesModal() {
        $('#shippoRatesModal').addClass('hidden');
    },

    fetchShippoRates() {
        const payload = {
            length: $('#shippo-length').val(),
            width: $('#shippo-width').val(),
            height: $('#shippo-height').val(),
            weight: $('#shippo-weight').val(),
            distance_unit: $('#shippo-distance-unit').val() || 'in',
            mass_unit: $('#shippo-mass-unit').val() || 'lb',
            to_street1: $('#shippo-to-street1').val(),
            to_city: $('#shippo-to-city').val(),
            to_state: $('#shippo-to-state').val(),
            to_zip: $('#shippo-to-zip').val(),
            to_country: ($('#shippo-to-country').val() || 'US').toUpperCase(),
            to_name: $('#inv-technician').val() || 'Customer',
        };
        this.latestShippoRequest = payload;

        const requiredFields = [
            ['length', 'Length'],
            ['width', 'Width'],
            ['height', 'Height'],
            ['weight', 'Weight'],
            ['to_street1', 'Street Address'],
            ['to_city', 'City'],
            ['to_state', 'State'],
            ['to_zip', 'ZIP'],
            ['to_country', 'Country'],
        ];
        const missing = requiredFields.filter(([key]) => !payload[key]).map(([, label]) => label);
        if (missing.length > 0) {
            $('#shippo-rates-error').removeClass('hidden').text(`Please fill: ${missing.join(', ')}`);
            return;
        }

        $('#shippo-rates-error').addClass('hidden').text('');
        $('#shippo-rates-loading').removeClass('hidden');
        $('#shippo-rates-list').html('');

        $.ajax({
            url: `${this.baseUrl}/shippo/rates`,
            type: 'POST',
            data: payload,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest',
            },
            success: (response) => {
                $('#shippo-rates-loading').addClass('hidden');
                const rates = response.rates || [];
                if (!Array.isArray(rates) || rates.length === 0) {
                    $('#shippo-rates-list').html('<div class="text-sm text-slate-500 font-semibold">No rates returned for these dimensions.</div>');
                    return;
                }

                const html = rates.map((rate) => `
                    <div class="p-3 border border-slate-200 rounded-xl bg-slate-50 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-800">${rate.provider} - ${rate.servicelevel}</p>
                            <p class="text-xs text-slate-500">${rate.estimated_days ? `Est. ${rate.estimated_days} day(s)` : (rate.duration_terms || 'ETA not available')}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-black text-emerald-700">${rate.amount} ${rate.currency}</p>
                            <button type="button" class="select-shippo-rate-btn mt-1 px-2 py-1 rounded-md bg-indigo-600 text-white text-[10px] font-bold"
                                data-rate='${JSON.stringify(rate).replace(/'/g, '&#39;')}'>Select</button>
                        </div>
                    </div>
                `).join('');
                $('#shippo-rates-list').html(html);
            },
            error: (xhr) => {
                $('#shippo-rates-loading').addClass('hidden');
                let msg = 'Failed to fetch shipping rates.';
                if (xhr.responseJSON?.error) msg = xhr.responseJSON.error;
                if (xhr.responseJSON?.errors) msg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                $('#shippo-rates-error').removeClass('hidden').text(msg);
            }
        });
    },

    selectShippoRate(rate, buttonEl) {
        const key = `${rate.provider}|${rate.servicelevel}|${rate.amount}|${rate.currency}`;
        const idx = this.selectedShippoRates.findIndex((r) =>
            `${r.provider}|${r.servicelevel}|${r.amount}|${r.currency}` === key
        );

        if (idx > -1) {
            this.selectedShippoRates.splice(idx, 1);
            $(buttonEl).removeClass('bg-emerald-600').addClass('bg-indigo-600').text('Select');
        } else {
            this.selectedShippoRates.push(rate);
            $(buttonEl).removeClass('bg-indigo-600').addClass('bg-emerald-600').text('Selected');
        }

        const count = this.selectedShippoRates.length;
        if (count > 0) {
            $('#shippo-rates-error')
                .removeClass('hidden text-red-600')
                .addClass('text-emerald-600')
                .text(`${count} rate(s) selected`);
        } else {
            $('#shippo-rates-error').addClass('hidden').text('');
        }
    },

    updateStatus(itemId, newStatus) {
        $.ajax({
            url: `${this.baseUrl}/${itemId}/update-status`,
            type: 'PUT',
            data: { status: newStatus },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            success: (response) => {
                this.showSuccess('Status updated successfully!');
                this.loadData();
            },
            error: (xhr, status, error) => {
                this.showError(xhr.responseJSON?.message || xhr.responseJSON?.error || 'Failed to update status');
            }
        });
    },

    updateQuantity(itemId, newQuantity, reuseQuantity = 0, scrapQuantity = 0, transferredQuantity = 0) {
        if (!isNaN(newQuantity) && newQuantity >= 0) {
            const totalQuantity = this.toInt(newQuantity) + this.toInt(reuseQuantity) + this.toInt(scrapQuantity) + this.toInt(transferredQuantity);

            $.ajax({
                url: `${this.baseUrl}/${itemId}/update-quantity`,
                type: 'PUT',
                data: { quantity: totalQuantity },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                success: () => {
                    this.loadData();
                },
                error: (xhr, status, error) => {
                    this.showError(xhr.responseJSON?.message || xhr.responseJSON?.error || 'Failed to update quantity');
                }
            });
        }
    },

    deleteItem(itemId) {
        $.ajax({
            url: `${this.baseUrl}/${itemId}`,
            type: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            success: (response) => {
                this.showSuccess('Item deleted successfully!');
                this.loadData();
            },
            error: (xhr, status, error) => {
                let errorMsg = 'Failed to delete item';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                } else if (xhr.status === 419) {
                    errorMsg = 'Session expired. Please refresh the page.';
                } else if (xhr.status === 403) {
                    errorMsg = 'Unauthorized to delete this item.';
                }
                this.showError(errorMsg);
            }
        });
    },

    prepareToInventory(itemId) {
        this.currentItemId = itemId;

        $.ajax({
            url: `${this.baseUrl}/${itemId}/prepare-inventory`,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            success: (data) => {
                console.log('Inventory prep data:', data);

                $('#inventory-item-id').val(itemId);
                // Do not populate device_type, category, serial_number, price, notes - user must fill manually
                $('#inv-brand-category').val(data.inventoryData.brand_category);
                $('#inv-os').val(data.inventoryData.operating_system);
                $('#inv-device-specs').val(data.inventoryData.device_specs);
                
                // Calculate available quantity: total - reuse - scrap
                const availableQty = this.availableQuantity(data.inventoryData);
                
                // Leave quantity empty - user must enter amount, show available qty
                $('#inv-quantity').val('');
                $('#inv-quantity').data('available', availableQty);
                $('#inv-quantity').attr('max', availableQty);
                $('#remaining-qty').text(`Remaining: ${availableQty} items`);
                
                $('#inv-status').val(data.inventoryData.status);
                $('#inv-pallet-info').val(data.inventoryData.pallet_info);
                $('#inv-location').val(data.inventoryData.put_away_location);
                $('#inv-lot-info').val(data.inventoryData.lot_info);
                $('#inv-technician').val('');
                $('#inv-customer-phone').val('');
                $('#inv-customer-email').val('');
                $('#shippo-length').val('');
                $('#shippo-width').val('');
                $('#shippo-height').val('');
                $('#shippo-weight').val('');
                $('#shippo-distance-unit').val('in');
                $('#shippo-mass-unit').val('lb');

                // Clear previous validation state
                $('#quantity-error').hide();
                $('#inv-submit-btn').prop('disabled', true);  // Button starts disabled until user enters quantity

                $('#inv-photos').val('');

                // Initialize category hierarchy manager
                if (window.categoryHierarchyManager) {
                    window.categoryHierarchyManager.initialize();
                    // Pre-select categories if they exist in inventory data
                    if (data.inventoryData.category_id) {
                        setTimeout(() => {
                            window.categoryHierarchyManager.selectPresetCategories(
                                data.inventoryData.category_id,
                                data.inventoryData.sub_category_id,
                                data.inventoryData.sub_sub_category_id
                            );
                        }, 500);
                    }
                }

                console.log('Opening add to inventory modal for item:', itemId);
                console.log('Total quantity:', data.inventoryData.quantity || 0);
                console.log('Available quantity for transfer:', availableQty);
                $('#addToInventoryModal').removeClass('hidden');
                $('#itemDetailsModal').addClass('hidden');
                
                // Attach form submit handler
                $(document).off('submit', '#saveToInventoryForm').on('submit', '#saveToInventoryForm', (e) => {
                    e.preventDefault();
                    this.handleSaveToInventory();
                });
            },
            error: (xhr, status, error) => {
                this.showError('Failed to prepare inventory form: ' + error);
            }
        });
    },

    validateQuantity() {
        const selectedQty = parseInt($('#inv-quantity').val()) || 0;
        const availableQty = parseInt($('#inv-quantity').data('available')) || 0;
        const submitBtn = $('#inv-submit-btn');
        const errorMsg = $('#quantity-error');
        
        // Calculate and update remaining quantity in real-time
        const remainingQty = availableQty - selectedQty;
        if (selectedQty > 0) {
            $('#remaining-qty').text(`Remaining: ${remainingQty} items`);
        } else {
            $('#remaining-qty').text(`Remaining: ${availableQty} items`);
        }

        // Validate quantity
        if (selectedQty <= 0) {
            submitBtn.prop('disabled', true);
            errorMsg.text('Please enter a valid quantity').show();
            return;
        }

        if (selectedQty > availableQty) {
            submitBtn.prop('disabled', true);
            errorMsg.text(`Not enough items available. Only ${availableQty} items remaining`).show();
            return;
        }

        // All validation passed
        errorMsg.hide();
        submitBtn.prop('disabled', false);
    },

    printLabel(itemId) {
        $.ajax({
            url: `${this.baseUrl}/${itemId}/details`,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            success: (item) => {
                this.openLabelPrintWindow(this.generateProcessingStyleLabel(item, 'Item Intake Label'));
            },
            error: () => {
                this.showError('Failed to load item for printing');
            }
        });
    },

    generateProcessingStyleLabel(item, labelTitle = 'Item Label', barcodeImageSrc = '') {
        const barcode = item.barcode || 'N/A';
        const brandModel = [item.brand, item.model].filter(Boolean).join(' ') || 'N/A';
        const createdDate = item.created_at ? new Date(item.created_at).toLocaleDateString() : new Date().toLocaleDateString();
        const barcodeBlock = barcodeImageSrc
            ? `<img src="${this.escapeHtml(barcodeImageSrc)}" alt="barcode">`
            : `<img src="https://bwipjs-api.metafloor.com/?bcid=code128&text=${encodeURIComponent(barcode)}&scale=2&height=12&includetext=false" alt="barcode">`;

        return `
            <section class="recycling-print-label">
                <div class="label-head">
                    <strong>IT Investment Recoveries</strong>
                    <span>${this.escapeHtml(labelTitle)}</span>
                </div>
                <div class="barcode-block">
                    ${barcodeBlock}
                    <div>${this.escapeHtml(barcode)}</div>
                </div>
                <div class="label-grid">
                    ${this.renderLabelRow('Item', item.name)}
                    ${this.renderLabelRow('Brand/Model', brandModel)}
                    ${this.renderLabelRow('Serial', item.serial_number || 'N/A')}
                    ${this.renderLabelRow('Category', item.category || 'N/A')}
                    ${this.renderLabelRow('Condition', item.condition || 'N/A')}
                    ${this.renderLabelRow('Item Count', this.availableQuantity(item))}
                    ${this.renderLabelRow('Weight', `${this.numberFormat(this.availableWeight(item), 2)} lbs`)}
                    ${this.renderLabelRow('Value', `$${this.numberFormat(item.estimated_value, 2)}`)}
                    ${this.renderLabelRow('Barcode', barcode)}
                    ${this.renderLabelRow('Date', createdDate)}
                </div>
                <div class="label-checks">
                   <span><i></i> Reuse</span>
                   <span><i></i> Universal E-waste</span>
                </div>
                <div class="label-foot">
                    ${this.escapeHtml(this.formatDenverDateTime())}
                </div>
            </section>
        `;
    },

    renderLabelRow(label, value) {
        return `
            <div class="label-row">
                <span>${this.escapeHtml(label)}</span>
                <strong>${this.escapeHtml(value === null || value === undefined || value === '' ? 'N/A' : value)}</strong>
            </div>
        `;
    },

    openLabelPrintWindow(html) {
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

    getProcessingLabelPrintStyles() {
        return `
            @page { size: 3.5in 2.25in; margin: 0; }
            html, body { margin: 0; width: 3.5in; min-height: 2.25in; background: #fff; color: #000; font-family: Arial, sans-serif; }
            .recycling-print-label { width: 3.5in; height: 2.25in; box-sizing: border-box; padding: 0.08in 0.12in; border: 1px solid #111827; overflow: hidden; }
            .label-head { display: flex; align-items: baseline; justify-content: space-between; border-bottom: 1px solid #111827; padding-bottom: 0.03in; margin-bottom: 0.04in; gap: 0.08in; }
            .label-head strong { font-size: 10px; font-weight: 900; text-transform: uppercase; white-space: nowrap; }
            .label-head span { font-size: 8px; font-weight: 800; text-transform: uppercase; text-align: right; }
            .barcode-block { text-align: center; margin-bottom: 0.04in; width: 100%; }
            .barcode-block img { display: block; width: 100%; max-width: none; height: 0.36in; object-fit: fill; }
            .barcode-block > div { font-family: monospace; font-size: 10px; font-weight: 900; margin-top: 0.01in; }
            .label-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.02in 0.08in; font-size: 6.8px; line-height: 1.15; }
            .label-row { min-width: 0; display: flex; justify-content: space-between; gap: 0.04in; border-bottom: 1px solid #e5e7eb; }
            .label-row span { color: #6b7280; font-weight: 800; text-transform: uppercase; white-space: nowrap; }
            .label-row strong { color: #111827; font-weight: 900; text-align: right; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .label-checks { display: flex; justify-content: center; gap: 0.18in; margin-top: 0.1in; font-size: 8px; font-weight: 900; text-transform: uppercase; }
            .label-checks span { display: inline-flex; align-items: center; gap: 0.04in; }
            .label-checks i { width: 0.09in; height: 0.09in; border: 1px solid #111827; display: inline-block; }
            .label-foot { margin-top: 0.03in; font-size: 6.5px; color: #4b5563; text-align: center; }
            .print-actions { margin-top: 16px; text-align: center; }
            .print-actions button { background: #111827; color: #fff; border: 0; border-radius: 10px; padding: 10px 20px; font-weight: 800; cursor: pointer; }
            @media screen { body { width: auto; min-height: auto; padding: 24px; background: #f1f5f9; display: flex; flex-direction: column; align-items: center; } .recycling-print-label { background: #fff; box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15); } }
            @media print { html, body { width: 3.5in; min-height: 2.25in; padding: 0; background: #fff; } .print-actions { display: none; } }
        `;
    },

    escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, (character) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;',
        })[character]);
    },

    numberFormat(value, decimals = 0) {
        const number = parseFloat(value);
        return (Number.isFinite(number) ? number : 0).toLocaleString('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals,
        });
    },

    formatDenverDateTime(date = new Date()) {
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

    exportCsv() {
        const params = {
            search: $('#search-input').val(),
            statusFilter: $('#status-filter').val(),
            palletFilter: $('#pallet-filter').val(),
        };

        const queryString = new URLSearchParams(params).toString();
        window.location.href = `${this.baseUrl}/export-csv?${queryString}`;
    },

    closeModal() {
        $('#itemDetailsModal').addClass('hidden');
        $('#editItemModal').addClass('hidden');
        $('#addToInventoryModal').addClass('hidden');
        this.previewObjectUrls.forEach((url) => URL.revokeObjectURL(url));
        this.previewObjectUrls = [];
        this.currentItemId = null;
        this.currentEditingItem = null;
        this.newPhotosFiles = [];
        this.repairPhotosFiles = [];
        this.photosToDelete = [];
        this.selectedShippoRates = [];
        this.latestShippoRequest = null;
    },

    showSuccess(message) {
        $('#success-message').text(message);
        $('#success-alert').removeClass('hidden');
        setTimeout(() => {
            $('#success-alert').addClass('hidden');
        }, 4000);
    },

    showError(message) {
        $('#error-message').text(message);
        $('#error-alert').removeClass('hidden');
        setTimeout(() => {
            $('#error-alert').addClass('hidden');
        }, 5000);
    },

    debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    },

    renderPhotoThumbnail(photosPaths, itemBarcode = "") {
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
        
        const photoUrls = photos.map(path => this.getPhotoUrl(path));
        const firstPhoto = photoUrls[0];
        const galleryJson = JSON.stringify(photoUrls);
        const title = itemBarcode ? `Item ${itemBarcode}` : "Images";
        
        return `<img src="${firstPhoto}" class="w-10 h-10 rounded-lg object-cover mx-auto cursor-pointer hover:ring-2 hover:ring-blue-500 transition-all" onclick='openGallery(${galleryJson})' title="Click to view all images" onerror="this.onerror=null;this.src='data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;120&quot; height=&quot;120&quot;><rect width=&quot;100%&quot; height=&quot;100%&quot; fill=&quot;%23f1f5f9&quot;/><text x=&quot;50%&quot; y=&quot;50%&quot; dominant-baseline=&quot;middle&quot; text-anchor=&quot;middle&quot; fill=&quot;%2394a3b8&quot; font-size=&quot;12&quot; font-family=&quot;Arial&quot;>No Image</text></svg>';">`;
    }
};

// Global helper function for closing the add to inventory modal
function closeAddToInventoryModal() {
    RefurbishingEntries.closeModal();
}
