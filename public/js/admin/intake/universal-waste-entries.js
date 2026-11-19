class UniversalWasteEntries {
    constructor() {
        console.log('UniversalWasteEntries constructor called');
        this.currentPage = 1;
        this.filters = {
            search: '',
            statusFilter: '',
            palletFilter: '',
            categoryFilter: ''
        };
        this.selectedItem = null;
        this.newPhotosFiles = []; // Array to store new photo files for preview
        this.selectedIds = new Set();
        this.init();
    }

    init() {
        console.log('init() called');
        this.cacheElements();
        console.log('Elements cached');
        this.attachEventListeners();
        console.log('Event listeners attached');
        this.loadStats();
        console.log('Stats loaded');
        this.loadData();
        console.log('Data load initiated');
    }

    cacheElements() {
        this.searchInput = $('#search-input');
        this.statusFilter = $('#status-filter');
        this.palletFilter = $('#pallet-filter');
        this.categoryFilter = $('#category-filter');
        this.tableBody = $('#items-table-body');
        this.paginationContainer = $('#pagination-container');
        this.statsContainer = $('#stats-container');
        this.refreshBtn = $('#refresh-btn');
        this.exportCsvBtn = $('#export-csv-btn');
        this.selectAllItems = $('#select-all-items');
        this.bulkActions = $('#bulk-actions');
        this.bulkSelectedCount = $('#bulk-selected-count');
        this.bulkStatusSelect = $('#bulk-status-select');
        this.bulkStatusBtn = $('#bulk-status-btn');
        this.bulkDeleteBtn = $('#bulk-delete-btn');
    }

    attachEventListeners() {
        // Filter changes
        this.searchInput.on('input', () => {
            this.onFilterChange();
        });

        this.statusFilter.on('change', () => {
            this.onFilterChange();
        });

        this.palletFilter.on('change', () => {
            this.onFilterChange();
        });

        this.categoryFilter.on('change', () => {
            this.onFilterChange();
        });

        this.refreshBtn.on('click', () => {
            this.resetFilters();
        });

        this.exportCsvBtn.on('click', () => {
            this.exportCsv();
        });

        this.selectAllItems.on('change', (e) => {
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

        this.bulkStatusBtn.on('click', () => {
            this.bulkUpdateStatus();
        });

        this.bulkDeleteBtn.on('click', () => {
            this.bulkDeleteItems();
        });

        // Event delegation for dynamic content
        $(document).on('change', '[data-action="updateStatus"]', (e) => {
            const itemId = $(e.target).data('item-id');
            const newStatus = $(e.target).val();
            this.updateStatus(itemId, newStatus);
        });

        $(document).on('change', '[data-action="updateQuantity"]', (e) => {
            const itemId = $(e.target).data('item-id');
            const newQuantity = $(e.target).val();
            const reuseQuantity = $(e.target).data('reuse-quantity');
            const scrapQuantity = $(e.target).data('scrap-quantity');
            const transferredQuantity = $(e.target).data('transferred-quantity');
            this.updateQuantity(itemId, newQuantity, reuseQuantity, scrapQuantity, transferredQuantity);
        });

        $(document).on('click', '[data-action="showDetails"]', (e) => {
            const itemId = $(e.currentTarget).data('item-id');
            this.showItemDetails(itemId);
        });

        $(document).on('click', '[data-action="editItem"]', (e) => {
            const itemId = $(e.currentTarget).data('item-id');
            this.editItem(itemId);
        });

        $(document).on('click', '[data-action="deleteItem"]', (e) => {
            const itemId = $(e.currentTarget).data('item-id');
            if (confirm('Are you sure you want to delete this?')) {
                this.deleteItem(itemId);
            }
        });

        $(document).on('click', '[data-action="prepareToInventory"]', (e) => {
            const itemId = $(e.currentTarget).data('item-id');
            this.prepareToInventory(itemId);
        });

        // Gallery image handler
        $(document).on('click', '.gallery-image', (e) => {
            const photosData = $(e.currentTarget).data('photos');
            if (photosData && Array.isArray(photosData)) {
                openGallery(photosData);
            }
        });

        // Pagination
        $(document).on('click', '.pagination a', (e) => {
            e.preventDefault();
            const page = $(e.target).attr('data-page');
            this.currentPage = page;
            this.loadData();
        });

        // Quantity input validation for inventory modal
        $(document).on('input', '#inv-quantity', () => {
            this.validateInventoryQuantity();
        });

        // Photo upload handler for edit modal
        $(document).on('change', '#waste-edit-photos', (e) => {
            this.newPhotosFiles = Array.from(e.target.files);
            this.updatePhotoPreview();
        });
    }

    formatPhotoUrl(path) {
        if (!path) {
            return '/images/placeholder.png';
        }

        if (path.startsWith('data:')) {
            return path;
        }

        if (/^https?:\/\//.test(path)) {
            return path;
        }

        if (path.startsWith('/')) {
            return path;
        }

        if (path.startsWith('universal-waste/')) {
            return `/${path}`;
        }

        return `${storageUrl.replace(/\/$/, '')}/${path}`;
    }

    // Photo upload preview functionality
    updatePhotoPreview() {
        $('#waste-edit-new-photos-preview').empty();
        this.newPhotosFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const preview = `
                    <div class="relative w-20 h-20 rounded-xl overflow-hidden border-2 border-blue-400 shadow-md">
                        <img src="${e.target.result}" class="w-full h-full object-cover">
                        <button type="button" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center shadow-md hover:bg-red-600 transition-colors" onclick="wasteEntries.removePhotoPreview(${index})">
                            <i class="fas fa-times text-[10px]"></i>
                        </button>
                    </div>
                `;
                $('#waste-edit-new-photos-preview').append(preview);
            };
            reader.readAsDataURL(file);
        });
    }

    removePhotoPreview(index) {
        this.newPhotosFiles.splice(index, 1);
        // Update the file input
        const dt = new DataTransfer();
        this.newPhotosFiles.forEach(file => dt.items.add(file));
        $('#waste-edit-photos')[0].files = dt.files;
        this.updatePhotoPreview();
    }

    onFilterChange() {
        this.filters = {
            search: this.searchInput.val(),
            statusFilter: this.statusFilter.val(),
            palletFilter: this.palletFilter.val(),
            categoryFilter: this.categoryFilter.val()
        };
        this.currentPage = 1;
        this.loadData();
    }

    resetFilters() {
        this.searchInput.val('');
        this.statusFilter.val('');
        this.palletFilter.val('');
        this.categoryFilter.val('');
        this.currentPage = 1;
        this.filters = {
            search: '',
            statusFilter: '',
            palletFilter: '',
            categoryFilter: ''
        };
        this.loadData();
    }

    validateInventoryQuantity() {
        const availableQty = parseInt($('#inv-quantity').data('available')) || 0;
        const selectedQty = parseInt($('#inv-quantity').val()) || 0;
        const errorMsg = $('#quantity-error');
        const submitBtn = $('#inv-submit-btn');

        // Clear previous errors
        errorMsg.hide();

        // Enable submit button if quantity is valid
        if (selectedQty > 0 && selectedQty <= availableQty) {
            submitBtn.prop('disabled', false);
        } else {
            submitBtn.prop('disabled', true);
        }
    }

    toInt(value) {
        const parsed = parseInt(value, 10);
        return Number.isFinite(parsed) ? parsed : 0;
    }

    toFloat(value) {
        const parsed = parseFloat(value);
        return Number.isFinite(parsed) ? parsed : 0;
    }

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
    }

    availableWeight(source = {}) {
        if (source.net_weight !== undefined && source.net_weight !== null) {
            return Math.max(0, this.toFloat(source.net_weight));
        }

        return Math.max(
            0,
            this.toFloat(source.weight) - this.toFloat(source.reuse_weight) - this.toFloat(source.scrap_weight)
        );
    }

    loadStats() {
        $.ajax({
            url: `${baseUrl}/stats`,
            type: 'GET',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: (stats) => {
                this.renderStats(stats);
            }
        });
    }

    renderStats(stats) {
        let html = '';
        stats.forEach(stat => {
            html += `
                <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-100 shadow-sm transition-all hover:shadow-md relative overflow-hidden">
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 ${stat.color.replace('text-', 'bg-')}"></div>
                    <div class="pl-2">
                        <p class="text-[10px] md:text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1 opacity-80">
                            ${stat.label}
                        </p>
                        <h3 class="text-xl md:text-2xl font-black ${stat.color} tracking-tight">
                            ${stat.value}
                        </h3>
                    </div>
                </div>
            `;
        });
        this.statsContainer.html(html);
    }

    loadData() {
        const params = {
            search: this.filters.search,
            statusFilter: this.filters.statusFilter,
            palletFilter: this.filters.palletFilter,
            categoryFilter: this.filters.categoryFilter,
            page: this.currentPage,
            _t: Date.now()
        };

        console.log('loadData called, baseUrl:', baseUrl);
        console.log('Request URL:', `${baseUrl}/load-data`);

        $.ajax({
            url: `${baseUrl}/load-data`,
            type: 'GET',
            data: params,
            cache: false,
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: (response) => {
                console.log('AJAX success, response:', response);
                this.selectedIds.clear();
                this.renderTable(response.items);
                this.renderPagination(response);
                this.updateBulkActions();
                console.log('Loaded items. First item status:', response.items.length > 0 ? response.items[0].status : 'N/A');
            },
            error: (err) => {
                console.error('Error loading data:', err);
                console.error('Error status:', err.status);
                console.error('Error responseText:', err.responseText);
                this.tableBody.html(`<tr><td colspan="13" class="p-8 text-center text-red-500 font-bold">Error loading data</td></tr>`);
            }
        });
    }

    renderTable(items) {
        console.log('renderTable called with items:', items);
        console.log('items count:', items ? items.length : 0);
        
        if (!items || items.length === 0) {
            console.log('No items, showing empty message');
            this.tableBody.html(`<tr><td colspan="13" class="p-16 text-center text-slate-300 font-bold uppercase text-xs">No Records Found</td></tr>`);
            return;
        }

        let html = '';
        items.forEach((item, index) => {
            console.log(`Rendering item ${index}:`, item.id, item.name);
            html += this.renderTableRow(item);
        });
        console.log('Setting table body HTML, total rows:', items.length);
        this.tableBody.html(html);
    }

    escapeHtml(unsafe) {
        if (!unsafe) return '';
        return String(unsafe)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    formatItemsSummary(item) {
        const rawName = item.name || '';
        const rawBrand = item.brand || '';

        const names = rawName ? rawName.split(',').map(s => s.trim()).filter(Boolean) : [];
        const brands = rawBrand ? rawBrand.split(',').map(s => s.trim()).filter(Boolean) : [];

        const firstName = names.length > 0 ? names[0] : (rawName || 'N/A');
        const firstBrand = brands.length > 0 ? brands[0] : (rawBrand || '');

        const extraCount = names.length > 1 ? names.length - 1 : 0;

        const fullTooltipName = this.escapeHtml(names.join(', ') || rawName);
        const fullTooltipBrand = this.escapeHtml(brands.join(', ') || rawBrand);
        const safeFirstName = this.escapeHtml(firstName);
        const safeFirstBrand = this.escapeHtml(firstBrand);

        let badgeHtml = '';
        if (extraCount > 0) {
            badgeHtml = `<span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-black bg-emerald-100 text-emerald-700 border border-emerald-200 shrink-0" title="All items: ${fullTooltipName}">+ ${extraCount} more</span>`;
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
    }

    renderTableRow(item) {
        const netQuantity = this.availableQuantity(item);
        const photoHtml = renderPhotoThumbnail(item.photos_paths, item.barcode);

        const companyName = this.escapeHtml(item.company_name || (item.pallet && item.pallet.client ? (item.pallet.client.company || item.pallet.client.name) : '') || 'N/A');
        const clientName = this.escapeHtml(item.client_name || (item.pallet && item.pallet.client ? item.pallet.client.name : '') || 'N/A');
        const isPallet = !!(item.pallet_number && item.pallet_number !== 'N/A');
        const displayCode = this.escapeHtml(isPallet ? item.pallet_number : (item.barcode || 'N/A'));
        const jobNo = item.pallet_job_no || (item.pallet && item.pallet.job_no);
        const jobNoHtml = (isPallet && jobNo) ? `<div class="text-[10px] font-semibold text-slate-400 normal-case">Job: ${this.escapeHtml(jobNo)}</div>` : '';
        const dateFormatted = item.created_at ? new Date(item.created_at).toLocaleDateString('en-US', { month: '2-digit', day: '2-digit', year: '2-digit' }) : 'N/A';

        // Build category hierarchy
        let categoryDisplay = item.category || '-';
        if (item.sub_category) {
            categoryDisplay = `${item.category} > ${item.sub_category}`;
        }
        if (item.sub_sub_category) {
            categoryDisplay += ` > ${item.sub_sub_category}`;
        }

        const statusHtml = `
            <select data-action="updateStatus" data-item-id="${item.id}" style="width: 100%; min-width: 10rem;" class="w-full px-2 py-2 rounded-lg text-[10px] font-black uppercase border-none cursor-pointer shadow-sm
                ${['Shipped', 'Delivered', 'Live Online'].includes(item.status) ? 'bg-emerald-500 text-white' :
                  item.status === 'Completed' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' :
                  item.status === 'In Progress' ? 'bg-amber-400 text-white' :
                  item.status === 'Draft' ? 'bg-slate-200 text-slate-700 border border-slate-300' :
                  ['Scrapped', 'Scrap'].includes(item.status) ? 'bg-red-500 text-white' :
                  'bg-blue-500 text-white'}">
                <option value="Draft" ${item.status === 'Draft' ? 'selected' : ''}>Draft</option>
                <option value="Recieved" ${item.status === 'Recieved' ? 'selected' : ''}>Recieved</option>
                <option value="Ready for Inventory" ${item.status === 'Ready for Inventory' ? 'selected' : ''}>Ready for Inventory</option>
                <option value="In Progress" ${item.status === 'In Progress' ? 'selected' : ''}>In Progress</option>
                <option value="Live Online" ${item.status === 'Live Online' ? 'selected' : ''}>Live Online</option>
                <option value="Completed" ${item.status === 'Completed' ? 'selected' : ''}>Completed</option>
                ${!['Draft', 'Ready for Inventory', 'In Progress', 'Live Online', 'Recieved', 'Completed'].includes(item.status) ? `<option value="${item.status}" selected>${item.status}</option>` : ''}
            </select>
        `;

        // Add to Inventory button states - ONLY disable when ALL quantity is zero (netQuantity === 0)
        const isInventoryDepleted = netQuantity === 0;
        const buttonClass = isInventoryDepleted
            ? 'bg-emerald-50 text-emerald-600 border-emerald-100 cursor-not-allowed opacity-90'
            : 'bg-blue-50 text-blue-600 border-blue-100 hover:bg-blue-600 hover:text-white active:scale-95 group';

        const buttonContent = isInventoryDepleted
            ? `<i class="fas fa-check-double text-sm"></i><span class="text-xs font-bold uppercase tracking-wider text-emerald-700">All Transferred</span>`
            : `<i class="fas fa-plus-circle text-sm group-hover:rotate-90 transition-transform"></i><span class="text-xs font-semibold uppercase tracking-wider">Add to Inventory</span>`;

        return `
            <tr class="hover:bg-slate-50/50 transition-colors text-[13px] text-slate-700" data-item-id="${item.id}">
                <td class="px-4 py-4 text-center">
                    <input type="checkbox" class="item-select h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500" data-item-id="${item.id}">
                </td>
                <td class="px-4 py-4 text-slate-500 font-bold text-[11px] whitespace-nowrap">${dateFormatted}</td>
                <td class="px-4 py-4 font-bold text-[#475569] text-xs">${companyName}</td>
                <td class="px-4 py-4 font-bold text-[#475569] text-xs">${clientName}</td>
                <td class="px-4 py-4 font-black text-[#1e293b] uppercase text-xs">${displayCode}${jobNoHtml}</td>
                <td class="px-4 py-4">
                    ${this.formatItemsSummary(item)}
                </td>
                <td class="px-4 py-4 text-[11px] font-semibold uppercase text-slate-500 truncate" title="${categoryDisplay}">${categoryDisplay}</td>
                <td class="px-4 py-4">
                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-600 border-emerald-100 text-[9px] font-black uppercase">
                        Universal Waste
                    </span>
                </td>
                <td class="px-4 py-4 text-center">${photoHtml}</td>
                <td class="px-4 py-4 text-center">
                    <input type="number" min="0" value="${netQuantity}" data-action="updateQuantity" data-item-id="${item.id}" data-reuse-quantity="${this.toInt(item.reuse_quantity)}" data-scrap-quantity="${this.toInt(item.scrap_quantity)}" data-transferred-quantity="${this.toInt(item.transferred_quantity)}" class="w-14 p-1 border border-slate-200 rounded-md text-center font-bold text-slate-800 text-sm">
                </td>
                <td class="px-4 py-4" style="width: 11rem; min-width: 11rem;">${statusHtml}</td>
                <td class="px-4 py-4 font-black text-slate-900 text-sm">$${parseFloat(item.estimated_value || 0).toFixed(2)}</td>
                <td class="px-4 py-4">
                    <div class="flex flex-row items-center justify-end gap-1.5">
                        <button data-action="prepareToInventory" data-item-id="${item.id}" ${isInventoryDepleted ? 'disabled' : ''} class="h-9 px-4 min-w-max rounded-xl transition-all flex items-center justify-center gap-2 shadow-sm border ${buttonClass}">
                            ${buttonContent}
                        </button>
                        <button data-action="showDetails" data-item-id="${item.id}" class="h-9 w-9 bg-slate-50 text-slate-400 rounded-xl transition-all hover:bg-slate-900 hover:text-white flex items-center justify-center active:scale-90" title="View Details">
                            <i class="fas fa-eye text-xs"></i>
                        </button>
                        <button onclick="openPrintLabel('print-data-${item.id}')" class="h-9 w-9 bg-orange-50 text-orange-500 rounded-xl transition-all hover:bg-orange-500 hover:text-white flex items-center justify-center active:scale-90" title="Print Label">
                            <i class="fas fa-print text-xs"></i>
                        </button>
                        <button data-action="editItem" data-item-id="${item.id}" class="h-9 w-9 bg-emerald-50 text-emerald-600 rounded-xl transition-all hover:bg-emerald-600 hover:text-white flex items-center justify-center active:scale-90" title="Edit Item">
                            <i class="fas fa-edit text-xs"></i>
                        </button>
                        <button data-action="deleteItem" data-item-id="${item.id}" class="h-9 w-9 bg-red-50 text-red-400 rounded-xl transition-all hover:bg-red-500 hover:text-white flex items-center justify-center active:scale-90" title="Delete Item">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                    <div id="print-data-${item.id}" class="hidden">
                        <div style="width: 3.5in; padding: 20px; border: 2px solid #000; font-family: Arial, sans-serif; color: #000; background: #fff;">
                            <div style="text-align: center; margin-bottom: 15px;">
                                <img src="https://bwipjs-api.metafloor.com/?bcid=code128&text=${item.barcode}&scale=2&height=12&includetext=false" style="max-width: 95%;">
                                <p style="font-family: monospace; font-size: 22px; font-weight: bold; margin: 5px 0; letter-spacing: 2px;">${item.barcode}</p>
                            </div>
                            <hr style="border: 0; border-top: 1px solid #000; margin: 10px 0;">
                            <div style="font-size: 14px; line-height: 1.8; text-align: left;">
                                <div><span style="font-weight: bold;">Item:</span> <span>${item.name}</span></div>
                                <div><span style="font-weight: bold;">Serial:</span> <span>${item.serial_number || 'N/A'}</span></div>
                                <div><span style="font-weight: bold;">Category:</span> <span style="text-transform: lowercase;">${item.category}</span></div>
                                <div style="margin-top: 15px;">
                                    <div><span style="font-weight: bold;">Barcode:</span> <span>${item.barcode}</span></div>
                                    <div><span style="font-weight: bold;">Date:</span> <span>${new Date(item.created_at).toLocaleDateString('en-US', { month: 'numeric', day: 'numeric', year: '2-digit' })}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        `;
    }

    renderPagination(response) {
        const { current_page, last_page } = response;
        let html = '<div class="flex justify-between items-center mt-4"><div></div><div class="pagination flex gap-2">';

        if (current_page > 1) {
            html += `<a href="#" data-page="1" class="px-3 py-2 border rounded hover:bg-slate-100">First</a>`;
            html += `<a href="#" data-page="${current_page - 1}" class="px-3 py-2 border rounded hover:bg-slate-100">Previous</a>`;
        }

        for (let i = Math.max(1, current_page - 2); i <= Math.min(last_page, current_page + 2); i++) {
            html += `<a href="#" data-page="${i}" class="px-3 py-2 border rounded ${i === current_page ? 'bg-blue-500 text-white' : 'hover:bg-slate-100'}">${i}</a>`;
        }

        if (current_page < last_page) {
            html += `<a href="#" data-page="${current_page + 1}" class="px-3 py-2 border rounded hover:bg-slate-100">Next</a>`;
            html += `<a href="#" data-page="${last_page}" class="px-3 py-2 border rounded hover:bg-slate-100">Last</a>`;
        }

        html += '</div><div></div></div>';
        this.paginationContainer.html(html);
    }

    getSelectedIds() {
        return Array.from(this.selectedIds).map(id => parseInt(id, 10)).filter(Boolean);
    }

    syncSelectAll() {
        const checkboxes = $('.item-select');
        const checkedCount = checkboxes.filter(':checked').length;

        this.selectAllItems
            .prop('checked', checkboxes.length > 0 && checkedCount === checkboxes.length)
            .prop('indeterminate', checkedCount > 0 && checkedCount < checkboxes.length);
    }

    updateBulkActions() {
        const selectedCount = this.selectedIds.size;

        this.bulkSelectedCount.text(`${selectedCount} selected`);
        this.bulkActions.toggleClass('hidden', selectedCount === 0);
        this.bulkStatusBtn.add(this.bulkDeleteBtn)
            .prop('disabled', selectedCount === 0)
            .toggleClass('opacity-50 cursor-not-allowed', selectedCount === 0);

        this.syncSelectAll();
    }

    bulkUpdateStatus() {
        const ids = this.getSelectedIds();
        const status = this.bulkStatusSelect.val();

        if (ids.length === 0) {
            this.showError('Select at least one item.');
            return;
        }

        if (!status) {
            this.showError('Choose a status first.');
            return;
        }

        $.ajax({
            url: `${baseUrl}/bulk-status`,
            type: 'PUT',
            data: { ids, status },
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            success: (response) => {
                if (response.success) {
                    this.showSuccess(response.message || 'Selected items updated successfully.');
                    this.selectedIds.clear();
                    this.loadData();
                    this.loadStats();
                } else {
                    this.showError(response.message || 'Failed to update selected items');
                }
            },
            error: (err) => {
                this.showError(err.responseJSON?.message || 'Failed to update selected items');
            }
        });
    }

    bulkDeleteItems() {
        const ids = this.getSelectedIds();

        if (ids.length === 0) {
            this.showError('Select at least one item.');
            return;
        }

        if (!confirm(`Delete ${ids.length} selected item(s)?`)) return;

        $.ajax({
            url: `${baseUrl}/bulk-delete`,
            type: 'DELETE',
            data: { ids },
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            success: (response) => {
                if (response.success) {
                    this.showSuccess(response.message || 'Selected items deleted successfully.');
                    this.selectedIds.clear();
                    this.loadData();
                    this.loadStats();
                } else {
                    this.showError(response.message || 'Failed to delete selected items');
                }
            },
            error: (err) => {
                this.showError(err.responseJSON?.message || 'Failed to delete selected items');
            }
        });
    }

    showSuccess(message) {
        alert(message);
    }

    showError(message) {
        alert('Error: ' + message);
    }

    showItemDetails(itemId) {
        $.ajax({
            url: `${baseUrl}/${itemId}`,
            type: 'GET',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: (response) => {
                if (response.success) {
                    this.selectedItem = response.item;
                    this.renderDetailsModal(response.item);
                    $('#wasteItemDetailsModal').removeClass('hidden');
                }
            }
        });
    }

    renderDetailsModal(item) {
        const photos = item.photos_paths && item.photos_paths.length > 0 ? item.photos_paths : [];
        const netWeight = this.availableWeight(item);
        const netQuantity = this.availableQuantity(item);

        // Header
        const names = item.name ? item.name.split(',').map(s => s.trim()) : [];
        const titleName = names.length > 0 ? names[0] : 'Item';
        $('#waste-modal-title').text(`View Universal Waste Item: ${titleName}`);

        // Status badge
        const status = item.status || '';
        let statusClass = 'bg-slate-500 text-white';
        if (['Shipped', 'Delivered', 'Live Online'].includes(status)) statusClass = 'bg-emerald-500 text-white';
        else if (status === 'Completed') statusClass = 'bg-emerald-100 text-emerald-700 border border-emerald-200';
        else if (status === 'In Progress') statusClass = 'bg-amber-500 text-white';
        else if (status === 'Draft') statusClass = 'bg-slate-200 text-slate-700 border border-slate-300';
        else if (['Scrapped', 'Scrap'].includes(status)) statusClass = 'bg-red-500 text-white';
        else if (status === 'Ready for Inventory') statusClass = 'bg-blue-500 text-white';
        $('#waste-modal-status').text(status).removeClass().addClass(`px-3 py-1 text-[10px] font-black uppercase rounded-lg ${statusClass}`);

        // Category
        $('#waste-modal-category-field').text(item.category || 'N/A');
        $('#waste-modal-sub-category').text(item.sub_category || 'N/A');
        $('#waste-modal-sub-sub-category').text(item.sub_sub_category || 'N/A');
        $('#waste-modal-condition-field').text(item.condition || 'N/A');

        // Value & quantities
        $('#waste-modal-value').text('$' + parseFloat(item.estimated_value || 0).toFixed(2));
        $('#waste-modal-qty-value').text(item.quantity || 0);
        $('#waste-modal-reuse-quantity').text(item.reuse_quantity || 0);
        $('#waste-modal-scrap-quantity').text(item.scrap_quantity || 0);
        $('#waste-modal-remaining-qty').text(`${netQuantity} PCS`);

        // Weight
        $('#waste-modal-weight').text(parseFloat(item.weight || 0).toFixed(2));
        $('#waste-modal-reuse-weight').text(parseFloat(item.reuse_weight || 0).toFixed(2));
        $('#waste-modal-scrap-weight').text(parseFloat(item.scrap_weight || 0).toFixed(2));
        $('#waste-modal-net-weight').text(parseFloat(netWeight).toFixed(2) + ' Lbs');

        // Location & Employee
        $('#waste-modal-pallet').text(item.pallet_number || 'N/A');
        $('#waste-modal-placement').text(item.item_placement || 'N/A');
        $('#waste-modal-receiving-employee').text(item.receiving_employee || 'N/A');
        $('#waste-modal-processing-employee').text(item.processing_employee || 'N/A');
        $('#waste-modal-universal-waste-employee').text(item.universal_waste_employee || item.intake_employee || 'N/A');

        // Notes
        $('#waste-modal-notes').text(item.notes || 'No internal notes provided.');

        // Last modified
        $('#waste-modal-updated-at').text(new Date(item.updated_at).toLocaleDateString('en-US', { month: '2-digit', day: '2-digit', year: '2-digit', hour: '2-digit', minute: '2-digit' }));

        // Barcode
        $('#waste-modal-barcode-img').attr('src', `https://bwipjs-api.metafloor.com/?bcid=code128&text=${encodeURIComponent(item.barcode)}&scale=2&height=10&includetext=true`);

        // Item cards
        this.renderWasteModalItems(item);

        // Photos/files gallery
        const photoGallery = photos.length > 0
            ? photos.map(path => {
                const isImage = path.startsWith('data:image/') || /\.(jpg|jpeg|png|webp|gif|svg|bmp)$/i.test(path);
                const filename = path.startsWith('data:') ? 'Image' : path.split('/').pop();
                if (isImage) {
                    return `
                        <div class="w-24 h-24 rounded-xl overflow-hidden border border-slate-200 shadow-sm group cursor-pointer">
                            <img src="${this.formatPhotoUrl(path)}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300 gallery-image" data-photos='${JSON.stringify(photos.map(p => this.formatPhotoUrl(p)))}'>
                        </div>`;
                }
                let iconClass = 'fa-file-alt', iconColor = 'text-blue-500';
                if (path.endsWith('.pdf')) { iconClass = 'fa-file-pdf'; iconColor = 'text-red-500'; }
                else if (path.endsWith('.csv') || path.endsWith('.xlsx') || path.endsWith('.xls')) { iconClass = 'fa-file-excel'; iconColor = 'text-green-500'; }
                else if (path.endsWith('.doc') || path.endsWith('.docx')) { iconClass = 'fa-file-word'; iconColor = 'text-blue-600'; }
                return `
                    <a href="${this.formatPhotoUrl(path)}" target="_blank" class="w-24 h-24 rounded-xl border border-slate-200 bg-white flex flex-col items-center justify-center p-2 text-center shadow-sm group hover:border-emerald-300 transition-colors">
                        <i class="fas ${iconClass} ${iconColor} text-2xl mb-1 group-hover:scale-110 transition-transform"></i>
                        <span class="text-[9px] font-bold text-slate-600 truncate w-full" title="${filename}">${filename}</span>
                    </a>`;
            }).join('')
            : `<div class="col-span-full py-4 text-center text-slate-400 text-xs font-bold w-full bg-white border border-slate-100 rounded-xl">
                <i class="fas fa-file-invoice text-2xl mb-2 block"></i>No files found</div>`;
        $('#waste-modal-photos').html(photoGallery);

        // Attach action buttons
        $('#waste-btn-modal-edit').off('click').on('click', () => {
            this.editItem(item.id);
        });
        $('#waste-btn-modal-print').off('click').on('click', () => {
            this.printItemLabel(item);
        });
    }

    renderWasteModalItems(item) {
        const container = document.getElementById('waste-modal-items-list');
        if (!container) return;

        const names   = item.name          ? item.name.split(',').map(s => s.trim())          : [];
        const brands  = item.brand         ? item.brand.split(',').map(s => s.trim())         : [];
        const models  = item.model         ? item.model.split(',').map(s => s.trim())         : [];
        const serials = item.serial_number ? item.serial_number.split(',').map(s => s.trim()) : [];

        const maxLen = Math.max(names.length, brands.length, models.length, serials.length, 1);

        const badge = document.getElementById('waste-modal-items-count');
        if (badge) badge.textContent = `${maxLen} Item${maxLen > 1 ? 's' : ''}`;

        const e = s => String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        let html = '';
        for (let i = 0; i < maxLen; i++) {
            html += `
                <div class="p-5 bg-[#f8fafc] border border-[#e2e8f0] rounded-2xl relative mb-3">
                    <div class="absolute top-4 right-4 text-slate-400 bg-slate-100 rounded-full w-6 h-6 flex items-center justify-center text-[10px] font-bold">${i + 1}</div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pr-6">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Item Name</label>
                            <div class="w-full p-2.5 bg-white border border-[#e2e8f0] rounded-xl text-xs font-bold text-slate-700">${e(names[i] || 'N/A')}</div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Brand</label>
                            <div class="w-full p-2.5 bg-white border border-[#e2e8f0] rounded-xl text-xs font-bold text-slate-700">${e(brands[i] || 'N/A')}</div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Model</label>
                            <div class="w-full p-2.5 bg-white border border-[#e2e8f0] rounded-xl text-xs font-bold text-slate-700">${e(models[i] || 'N/A')}</div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Serial Number</label>
                            <div class="w-full p-2.5 bg-white border border-[#e2e8f0] rounded-xl text-xs font-bold text-slate-700 font-mono">${e(serials[i] || 'N/A')}</div>
                        </div>
                    </div>
                </div>`;
        }
        container.innerHTML = html;
    }

    printItemLabel(item) {
        const barcodeUrl = `https://bwipjs-api.metafloor.com/?bcid=code128&text=${encodeURIComponent(item.barcode)}&scale=2&height=12&includetext=false`;

        const html = `
            <div style="width: 3.5in; padding: 20px; border: 2px solid #000; font-family: Arial, sans-serif; color: #000; background: #fff;">
                <div style="text-align: center; margin-bottom: 15px;">
                    <img src="${barcodeUrl}" style="max-width: 95%;">
                    <p style="font-family: monospace; font-size: 22px; font-weight: bold; margin: 5px 0; letter-spacing: 2px;">${item.barcode}</p>
                </div>
                <div style="font-size: 14px; line-height: 1.8; text-align: left;">
                    <div><span style="font-weight: bold;">Item:</span> <span>${item.name}</span></div>
                    <div><span style="font-weight: bold;">Serial:</span> <span>${item.serial_number || 'N/A'}</span></div>
                    <div><span style="font-weight: bold;">Category:</span> <span style="text-transform: lowercase;">${item.category}</span></div>
                    <div style="margin-top: 15px;">
                        <div><span style="font-weight: bold;">Barcode:</span> <span>${item.barcode}</span></div>
                        <div><span style="font-weight: bold;">Date:</span> <span>${new Date(item.created_at).toLocaleDateString()}</span></div>
                    </div>
                </div>
            </div>
        `;

        this.openPrintWindow(html);
    }

    openPrintWindow(html) {
        const printWindow = window.open('', '_blank', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Waste Item Label</title>');
        printWindow.document.write('<style>body{margin:0; padding:20px; display:flex; justify-content:center; align-items:flex-start;}</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write(html);
        printWindow.document.write('</body></html>');
        printWindow.document.close();

        setTimeout(() => {
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }, 800);
    }

    editItem(itemId) {
        closeWasteItemDetailsModal();
        
        $.ajax({
            url: `${baseUrl}/${itemId}/edit`,
            type: 'GET',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: (response) => {
                if (response.success) {
                    this.selectedItem = response.item;
                    this.renderEditModal(response.item, response.pallets);
                    $('#wasteEditItemModal').removeClass('hidden');
                }
            },
            error: (err) => {
                console.error('Error loading edit form:', err);
                alert('Failed to load edit form: ' + (err.responseJSON?.message || 'Internal Server Error'));
            }
        });
    }

    renderEditModal(item, pallets) {
        const netWeight = this.availableWeight(item);
        const netQuantity = this.availableQuantity(item);

        // Populate modal title
        $('#waste-edit-item-name-title').text(item.name || 'N/A');
        $('#waste-edit-barcode-title').text(item.barcode ? `(${item.barcode})` : '');
        
        // Populate form fields
        $('#waste-edit-item-id').val(item.id);
        $('#waste-edit-name').val(item.name || '');
        $('#waste-edit-brand').val(item.brand || '');
        $('#waste-edit-model').val(item.model || '');
        $('#waste-edit-serial-number').val(item.serial_number || '');
        $('#waste-edit-condition').val(item.condition || 'New');
        $('#waste-edit-value').val(item.estimated_value || 0);
        $('#waste-edit-quantity').val(item.quantity || 0);
        $('#waste-edit-reuse-quantity').val(item.reuse_quantity || 0);
        $('#waste-edit-scrap-quantity').val(item.scrap_quantity || 0);
        $('#waste-edit-weight').val(item.weight || 0);
        $('#waste-edit-reuse-weight').val(item.reuse_weight || 0);
        $('#waste-edit-scrap-weight').val(item.scrap_weight || 0);
        $('#waste-edit-pallet').val(item.pallet_number || '');
        $('#waste-edit-placement').val(item.item_placement || '');
        $('#waste-edit-receiving-employee').val(item.receiving_employee || '');
        $('#waste-edit-processing-employee').val(item.processing_employee || '');
        $('#waste-edit-universal-waste-employee').val(item.universal_waste_employee || item.intake_employee || '');
        $('#waste-edit-notes').val(item.notes || '');

        this.initializeEditCategoryHierarchy(item);
        
        // Populate existing photos
        const photoHTML = item.photos_paths && item.photos_paths.length > 0
            ? item.photos_paths.map(path => `
                <div class="relative w-20 h-20 group rounded-xl overflow-hidden border border-slate-200">
                    <img src="${this.formatPhotoUrl(path)}" class="w-full h-full object-cover">
                    <button type="button" class="absolute inset-0 bg-red-600/70 text-white opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all duration-200" onclick="wasteEntries.deletePhoto('${item.id}', '${path}')">
                        <i class="fas fa-trash-alt text-xs"></i>
                    </button>
                </div>
            `).join('')
            : '';
        $('#waste-edit-existing-photos').html(photoHTML);
        
        // Update net calculations display
        this.updateWasteNetCalculations();

        // Attach calculator listeners (do not mirror fields)
        $(document).on('input', '#waste-edit-weight, #waste-edit-reuse-weight, #waste-edit-scrap-weight, #waste-edit-quantity, #waste-edit-reuse-quantity, #waste-edit-scrap-quantity', () => {
            this.updateWasteNetCalculations();
        });

        // Form submit
        $(document).off('submit', '#wasteEditItemForm').on('submit', '#wasteEditItemForm', (e) => {
            e.preventDefault();
            this.submitEditForm();
        });
    }

    initializeEditCategoryHierarchy(item) {
        if (!window.categoryHierarchyManager) {
            return;
        }

        window.categoryHierarchyManager.whenReady().then(() => {
            this.populateEditCategoryHierarchy(item);
        });
    }

    populateEditCategoryHierarchy(item) {
        const manager = window.categoryHierarchyManager;
        const categoryMap = manager.categoryMap || {};
        const rootCategories = (manager.categories || []).filter(category => !category.parent_id);

        const categorySelect = $('#waste-edit-category-select');
        const subCategorySelect = $('#waste-edit-sub-category-select');
        const subSubCategorySelect = $('#waste-edit-sub-sub-category-select');
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

        $('#waste-edit-category').val(rootCategory ? rootCategory.name : (item.category || ''));
        $('#waste-edit-category-id').val(rootCategory ? rootCategory.id : '');
        $('#waste-edit-sub-category').val(subCategory ? subCategory.name : (item.sub_category || ''));
        $('#waste-edit-sub-category-id').val(subCategory ? subCategory.id : '');
        $('#waste-edit-sub-sub-category').val(subSubCategoryId ? (categoryMap[subSubCategoryId]?.name || item.sub_sub_category || '') : (item.sub_sub_category || ''));
        $('#waste-edit-sub-sub-category-id').val(subSubCategoryId || '');

        categorySelect.off('change').on('change', () => {
            const selectedCategoryId = categorySelect.val();
            const selectedCategory = selectedCategoryId ? categoryMap[selectedCategoryId] : null;
            const children = selectedCategory && selectedCategory.children ? selectedCategory.children : [];

            $('#waste-edit-category').val(selectedCategory ? selectedCategory.name : '');
            $('#waste-edit-category-id').val(selectedCategory ? selectedCategory.id : '');

            fillSelect(subCategorySelect, 'Select Sub Category', children, '');
            subCategorySelect.prop('disabled', !selectedCategory || children.length === 0);

            fillSelect(subSubCategorySelect, 'Select Sub Sub Category', [], '');
            subSubCategorySelect.prop('disabled', true);

            $('#waste-edit-sub-category').val('');
            $('#waste-edit-sub-category-id').val('');
            $('#waste-edit-sub-sub-category').val('');
            $('#waste-edit-sub-sub-category-id').val('');
        });

        subCategorySelect.off('change').on('change', () => {
            const selectedCategoryId = categorySelect.val();
            const selectedCategory = selectedCategoryId ? categoryMap[selectedCategoryId] : null;
            const selectedSubCategoryId = subCategorySelect.val();
            const selectedSubCategory = selectedSubCategoryId ? categoryMap[selectedSubCategoryId] : null;
            const children = selectedSubCategory && selectedSubCategory.children ? selectedSubCategory.children : [];

            $('#waste-edit-sub-category').val(selectedSubCategory ? selectedSubCategory.name : '');
            $('#waste-edit-sub-category-id').val(selectedSubCategory ? selectedSubCategory.id : '');

            fillSelect(subSubCategorySelect, 'Select Sub Sub Category', children, '');
            subSubCategorySelect.prop('disabled', !selectedSubCategory || children.length === 0);

            $('#waste-edit-sub-sub-category').val('');
            $('#waste-edit-sub-sub-category-id').val('');

            $('#waste-edit-category').val(selectedCategory ? selectedCategory.name : $('#waste-edit-category').val());
            $('#waste-edit-category-id').val(selectedCategory ? selectedCategory.id : $('#waste-edit-category-id').val());
        });

        subSubCategorySelect.off('change').on('change', () => {
            const selectedSubSubCategoryId = subSubCategorySelect.val();
            const selectedSubSubCategory = selectedSubSubCategoryId ? categoryMap[selectedSubSubCategoryId] : null;

            $('#waste-edit-sub-sub-category').val(selectedSubSubCategory ? selectedSubSubCategory.name : '');
            $('#waste-edit-sub-sub-category-id').val(selectedSubSubCategory ? selectedSubSubCategory.id : '');
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
    }

    updateWasteNetCalculations() {
        const weight = parseFloat($('#waste-edit-weight').val() || 0);
        const reuseWeight = parseFloat($('#waste-edit-reuse-weight').val() || 0);
        const scrapWeight = parseFloat($('#waste-edit-scrap-weight').val() || 0);
        const quantity = parseInt($('#waste-edit-quantity').val() || 0);
        const reuseQuantity = parseInt($('#waste-edit-reuse-quantity').val() || 0);
        const scrapQuantity = parseInt($('#waste-edit-scrap-quantity').val() || 0);

        const deductedWeight = reuseWeight + scrapWeight;
        const netWeight = Math.max(0, weight - deductedWeight);
        const deductedQty = reuseQuantity + scrapQuantity;
        const netQuantity = Math.max(0, quantity - deductedQty);

        $('#waste-edit-total-qty-summary').text(quantity);
        $('#waste-edit-reuse-qty-summary').text(reuseQuantity);
        $('#waste-edit-scrap-qty-summary').text(scrapQuantity);
        $('#waste-edit-total-weight-summary').text(parseFloat(weight).toFixed(2));
        $('#waste-edit-reuse-weight-summary').text(parseFloat(reuseWeight).toFixed(2));
        $('#waste-edit-scrap-weight-summary').text(parseFloat(scrapWeight).toFixed(2));
        $('#waste-edit-net-weight').text(`${parseFloat(netWeight).toFixed(2)} Lbs`);
        $('#waste-edit-remaining-qty').text(`${netQuantity} PCS`);
    }

    submitEditForm() {
        const formData = new FormData();
        const itemId = $('#waste-edit-item-id').val();
        
        // Collect form data
        formData.append('name', $('#waste-edit-name').val());
        formData.append('brand', $('#waste-edit-brand').val());
        formData.append('model', $('#waste-edit-model').val());
        formData.append('serial_number', $('#waste-edit-serial-number').val());
        formData.append('category', $('#waste-edit-category').val());
        formData.append('sub_category', $('#waste-edit-sub-category').val());
        formData.append('sub_sub_category', $('#waste-edit-sub-sub-category').val());
        formData.append('category_id', $('#waste-edit-category-id').val());
        formData.append('sub_category_id', $('#waste-edit-sub-category-id').val());
        formData.append('sub_sub_category_id', $('#waste-edit-sub-sub-category-id').val());
        formData.append('condition', $('#waste-edit-condition').val());
        formData.append('estimated_value', $('#waste-edit-value').val());
        formData.append('quantity', $('#waste-edit-quantity').val());
        formData.append('reuse_quantity', $('#waste-edit-reuse-quantity').val());
        formData.append('scrap_quantity', $('#waste-edit-scrap-quantity').val());
        formData.append('weight', $('#waste-edit-weight').val());
        formData.append('reuse_weight', $('#waste-edit-reuse-weight').val());
        formData.append('scrap_weight', $('#waste-edit-scrap-weight').val());
        formData.append('pallet_number', $('#waste-edit-pallet').val());
        formData.append('item_placement', $('#waste-edit-placement').val());
        formData.append('receiving_employee', $('#waste-edit-receiving-employee').val());
        formData.append('processing_employee', $('#waste-edit-processing-employee').val());
        formData.append('universal_waste_employee', $('#waste-edit-universal-waste-employee').val());
        formData.append('intake_employee', $('#waste-edit-universal-waste-employee').val());
        formData.append('notes', $('#waste-edit-notes').val());
        
        // Handle photos if any
        this.newPhotosFiles.forEach(file => {
            formData.append('photos[]', file);
        });

        $.ajax({
            url: `${baseUrl}/${itemId}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: (response) => {
                if (response.success) {
                    closeWasteEditItemModal();
                    this.loadData();
                    this.loadStats();
                    alert(response.message || 'Item updated successfully!');
                }
            },
            error: (err) => {
                console.error('Error:', err);
                alert('Error: ' + (err.responseJSON?.message || 'Failed to update item'));
            }
        });
    }

    deletePhoto(itemId, photoPath) {
        if (!confirm('Delete this photo?')) return;

        $.ajax({
            url: `${baseUrl}/${itemId}/delete-photo`,
            type: 'POST',
            data: { photo_path: photoPath },
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: (response) => {
                if (response.success) {
                    // Remove the photo element from the modal
                    $(`img[src="${storageUrl}/${photoPath}"]`).closest('.relative').remove();
                }
            }
        });
    }

    updateStatus(itemId, newStatus) {
        $.ajax({
            url: `${baseUrl}/${itemId}/status`,
            type: 'POST',
            data: { status: newStatus },
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: (response) => {
                if (response.success) {
                    this.loadData();
                    this.loadStats();
                }
            }
        });
    }

    updateQuantity(itemId, newQuantity, reuseQuantity = 0, scrapQuantity = 0, transferredQuantity = 0) {
        const totalQuantity = this.toInt(newQuantity) + this.toInt(reuseQuantity) + this.toInt(scrapQuantity) + this.toInt(transferredQuantity);

        $.ajax({
            url: `${baseUrl}/${itemId}/quantity`,
            type: 'POST',
            data: { quantity: totalQuantity },
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: (response) => {
                if (response.success) {
                    this.loadData();
                    this.loadStats();
                }
            }
        });
    }

    deleteItem(itemId) {
        $.ajax({
            url: `${baseUrl}/${itemId}`,
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: (response) => {
                if (response.success) {
                    this.loadData();
                    this.loadStats();
                }
            },
            error: (err) => {
                console.error('Error:', err);
                alert('Error: ' + (err.responseJSON?.message || 'Failed to delete item'));
            }
        });
    }

    prepareToInventory(itemId) {
        $.ajax({
            url: `${baseUrl}/${itemId}/prepare-inventory`,
            type: 'GET',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: (response) => {
                if (response.success) {
                    this.openAddToInventoryModal(response.item, response.inventoryData);
                }
            }
        });
    }

    openAddToInventoryModal(item, inventoryData) {
        $('#inventory-item-id').val(item.id);
        $('#inv-technician').val(inventoryData.technician || '');
        $('#inv-customer-phone').val(inventoryData.customer_phone || '');
        $('#inv-customer-email').val(inventoryData.customer_email || '');
        // Do not populate device_type, category, price, serial_number, notes - user must fill manually
        $('#inv-brand-category').val(inventoryData.brand_category || '');
        
        // Calculate available quantity: total - reuse - scrap
        const availableQty = this.availableQuantity(inventoryData);
        
        // Leave quantity empty - user must enter amount, show available qty
        $('#inv-quantity').val('');
        $('#inv-quantity').data('available', availableQty);
        $('#inv-quantity').attr('max', availableQty);
        $('#remaining-qty').text(`Remaining: ${availableQty} items`);
        
        $('#inv-status').val(inventoryData.status || 'Pending');
        $('#inv-pallet-info').val(inventoryData.pallet_info || '');
        $('#inv-device-specs').val(inventoryData.device_specs || '');
        $('#inv-os').val(inventoryData.operating_system || '');
        $('#inv-location').val(inventoryData.put_away_location || '');
        $('#inv-lot-info').val(inventoryData.lot_info || '');
        
        // Clear previous validation state
        $('#quantity-error').hide();
        $('#inv-submit-btn').prop('disabled', true);  // Button starts disabled until user enters quantity

        // Initialize category hierarchy manager
        if (window.categoryHierarchyManager) {
            window.categoryHierarchyManager.initialize();
            // Pre-select categories if they exist in inventory data
            if (inventoryData.category_id) {
                setTimeout(() => {
                    window.categoryHierarchyManager.selectPresetCategories(
                        inventoryData.category_id,
                        inventoryData.sub_category_id,
                        inventoryData.sub_sub_category_id
                    );
                }, 500);
            }
        }

        console.log('Opening add to inventory modal for item:', item.id);
        console.log('Total quantity:', inventoryData.quantity || 0, 'Reuse quantity:', inventoryData.reuse_quantity || 0, 'Scrap quantity:', inventoryData.scrap_quantity || 0);
        console.log('Available quantity for transfer:', availableQty);
        $('#addToInventoryModal').removeClass('hidden');

        // Attach form submit handler
        $(document).off('submit', '#saveToInventoryForm').on('submit', '#saveToInventoryForm', (e) => {
            e.preventDefault();
            this.handleSaveToInventory();
        });
    }

    handleSaveToInventory() {
        const itemId = $('#inventory-item-id').val();
        const availableQty = parseInt($('#inv-quantity').data('available')) || 0;
        const selectedQty = parseInt($('#inv-quantity').val()) || 0;
        const submitBtn = $('#inv-submit-btn');
        const submitSpinner = $('#inv-submit-spinner');
        const submitText = $('#inv-submit-text');
        const errorMsg = $('#quantity-error');

        console.log('handleSaveToInventory called');
        console.log('Item ID:', itemId);
        console.log('Available qty:', availableQty, 'Selected qty:', selectedQty);
        
        if (!itemId) {
            alert('No item selected. Please try again.');
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
        formData.append('put_away_location', $('#inv-location').val());

        // Include generated barcode if present
        formData.append('barcode', $('#inv-barcode').val() || '');
        formData.append('reuse_quantity', 0);

        // Handle repair photos - use accumulated files from the modal manager
        if (window.inventoryPhotoManager && window.inventoryPhotoManager.accumulatedFiles) {
            window.inventoryPhotoManager.accumulatedFiles.forEach((item) => {
                const fileObj = item && item.file ? item.file : item;
                formData.append('repair_photos[]', fileObj);
            });
        }

        // Disable submit button and show spinner
        submitBtn.prop('disabled', true);
        submitSpinner.removeClass('hidden');
        submitText.text('Saving...');
        
        console.log('Submitting to inventory. Item ID:', itemId, 'Qty:', selectedQty);

        $.ajax({
            url: `${baseUrl}/${itemId}/save-inventory`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            success: (response) => {
                if (response.success) {
                    // Calculate new remaining quantity
                    const newRemainingQty = availableQty - selectedQty;
                    
                    console.log('Save successful. New remaining qty:', newRemainingQty);
                    
                    // Only disable button and update UI if ALL items are transferred (remaining = 0)
                    if (newRemainingQty <= 0) {
                        $(`.data-action[data-item-id="${itemId}"]`).prop('disabled', true)
                            .removeClass('bg-blue-50 text-blue-600 border-blue-100 hover:bg-blue-600 hover:text-white active:scale-95 group')
                            .addClass('bg-emerald-50 text-emerald-600 border-emerald-100 cursor-not-allowed opacity-90')
                            .html(`
                                <i class="fas fa-check-double text-sm"></i>
                                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">All Transferred</span>
                            `);
                    }
                    
                    // Reset all filters
                    $('#search-input').val('');
                    $('#status-filter').val('');
                    $('#pallet-filter').val('');
                    $('#category-filter').val('');
                    this.currentPage = 1;

                    // Reset spinner
                    submitSpinner.addClass('hidden');
                    submitText.text('Save to Inventory');
                    
                    // Reset form
                    $('#saveToInventoryForm')[0].reset();

                    // Close modal
                    closeAddToInventoryModal();

                    // Reload with delay for DB consistency
                    setTimeout(() => {
                        this.loadData();
                        this.loadStats();
                    }, 500);
                }
            },
            error: (err) => {
                submitSpinner.addClass('hidden');
                submitText.text('Save to Inventory');
                submitBtn.prop('disabled', false); // Re-enable submit button on error
                console.error('Error:', err);
                const message = err.responseJSON?.message || 'Failed to save to inventory';
                errorMsg.text(message).show();
            }
        });
    }

    exportCsv() {
        const params = new URLSearchParams({
            search: this.filters.search,
            statusFilter: this.filters.statusFilter,
            palletFilter: this.filters.palletFilter,
            categoryFilter: this.filters.categoryFilter
        });

        window.location.href = `${baseUrl}/export-csv?${params.toString()}`;
    }
}

// Global instance
let wasteEntries;

// Initialize when DOM is ready
$(document).ready(() => {
    console.log('$(document).ready() called');
    console.log('baseUrl:', baseUrl);
    console.log('jQuery version:', $.fn.jquery);
    wasteEntries = new UniversalWasteEntries();
    console.log('wasteEntries initialized');
});


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
        if (typeof path === 'string' && (path.startsWith('data:') || /^https?:\/\//.test(path) || path.startsWith('/'))) {
            return path;
        }
        if (typeof path === 'string' && (path.startsWith('/universal-waste/') || path.startsWith('universal-waste/'))) {
            return path.startsWith('/') ? path : '/' + path;
        }
        return `/universal-waste/${path}`;
    });
    const firstPhoto = photoUrls[0];
    const galleryJson = JSON.stringify(photoUrls);
    const title = itemBarcode ? `Item ${itemBarcode}` : "Images";
    
    return `<img src="${firstPhoto}" class="w-10 h-10 rounded-lg object-cover mx-auto cursor-pointer hover:ring-2 hover:ring-blue-500 transition-all" onclick='openGallery(${galleryJson})' title="Click to view all images" onerror="this.src='/images/placeholder.png'">`;
}

// Modal control functions
function closeWasteItemDetailsModal() {
    $('#wasteItemDetailsModal').addClass('hidden');
}

function closeWasteEditItemModal() {
    $('#wasteEditItemModal').addClass('hidden');
    $('#waste-edit-photos').val('');
    $('#waste-edit-new-photos-preview').empty();
    wasteEntries.newPhotosFiles = [];
}

function closeAddToInventoryModal() {
    $('#addToInventoryModal').addClass('hidden');
    $('#saveToInventoryForm')[0].reset();
}

// Print function
function openPrintLabel(containerId) {
    const el = document.getElementById(containerId);
    if (!el) {
        alert("Print data not found!");
        return;
    }

    const content = el.innerHTML;
    const printWindow = window.open('', '_blank', 'width=800,height=600');

    printWindow.document.write(`
        <html>
            <head>
                <title>Print Inventory Label</title>
                <style>
                    body { font-family: sans-serif; display: flex; flex-direction: column; align-items: center; padding: 20px; }
                    .print-card { border: 2px solid #000; padding: 20px; width: 320px; text-align: center; border-radius: 8px; }
                    .header { font-weight: bold; border-bottom: 2px solid #000; margin-bottom: 10px; padding-bottom: 5px; font-size: 16px; }
                    .info { text-align: left; font-size: 13px; margin-top: 10px; line-height: 1.6; }
                    .info p { margin: 4px 0; border-bottom: 1px solid #eee; }
                    .btn-print { background: #2563eb; color: #fff; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-bottom: 20px; font-weight: bold; }
                    @media print { .btn-print { display: none; } body { padding: 0; } .print-card { border: 1px solid #000; } }
                </style>
            </head>
            <body>
                <button class="btn-print" onclick="window.print()">Confirm & Print</button>
                <div class="print-card-wrapper">
                    ${content}
                </div>
            </body>
        </html>
    `);
    printWindow.document.close();
}
