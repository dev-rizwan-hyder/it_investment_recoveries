class ItAssetsEntries {
    constructor() {
        console.log('ItAssetsEntries constructor called');
        this.currentPage = 1;
        this.filters = {
            search: '',
            statusFilter: '',
            palletFilter: '',
            categoryFilter: ''
        };
        this.selectedItem = null;
        this.newPhotosFiles = [];
        this.selectedIds = new Set();
        this.editDdItems = [];
        this.init();
    }

    init() {
        this.cacheElements();
        this.attachEventListeners();
        this.loadStats();
        this.loadData();
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
        this.searchInput.on('input', () => this.onFilterChange());
        this.statusFilter.on('change', () => this.onFilterChange());
        this.palletFilter.on('change', () => this.onFilterChange());
        this.categoryFilter.on('change', () => this.onFilterChange());
        this.refreshBtn.on('click', () => this.resetFilters());
        this.exportCsvBtn.on('click', () => this.exportCsv());

        this.selectAllItems.on('change', (e) => {
            const isChecked = $(e.currentTarget).is(':checked');
            $('.item-select').each((_, el) => {
                const itemId = String($(el).data('item-id'));
                $(el).prop('checked', isChecked);
                if (isChecked) this.selectedIds.add(itemId);
                else this.selectedIds.delete(itemId);
            });
            this.updateBulkActions();
        });

        $(document).on('change', '.item-select', (e) => {
            const itemId = String($(e.currentTarget).data('item-id'));
            const isChecked = $(e.currentTarget).is(':checked');
            $('.item-select').filter((_, el) => String($(el).data('item-id')) === itemId).prop('checked', isChecked);
            if (isChecked) this.selectedIds.add(itemId);
            else this.selectedIds.delete(itemId);
            this.updateBulkActions();
        });

        this.bulkStatusBtn.on('click', () => this.bulkUpdateStatus());
        this.bulkDeleteBtn.on('click', () => this.bulkDeleteItems());

        $(document).on('change', '[data-action="updateStatus"]', (e) => {
            const itemId = $(e.target).data('item-id');
            const newStatus = $(e.target).val();
            this.updateStatus(itemId, newStatus);
        });

        $(document).on('change', '[data-action="updateQuantity"]', (e) => {
            const itemId = $(e.target).data('item-id');
            const newQuantity = $(e.target).val();
            this.updateQuantity(itemId, newQuantity);
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
            if (confirm('Are you sure you want to delete this IT Assets record?')) {
                this.deleteItem(itemId);
            }
        });

        $(document).on('click', '[data-action="prepareToInventory"]', (e) => {
            const itemId = $(e.currentTarget).data('item-id');
            this.prepareToInventory(itemId);
        });

        $(document).on('click', '[data-action="printLabel"]', (e) => {
            const itemId = $(e.currentTarget).data('item-id');
            $.ajax({
                url: `${baseUrl}/${itemId}`,
                type: 'GET',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: (response) => {
                    if (response.success) {
                        this.printItemLabel(response.item);
                    }
                }
            });
        });

        $(document).on('click', '.gallery-image', (e) => {
            const photosData = $(e.currentTarget).data('photos');
            if (photosData && Array.isArray(photosData)) {
                openGallery(photosData);
            }
        });

        $(document).on('click', '.pagination a', (e) => {
            e.preventDefault();
            this.currentPage = $(e.target).attr('data-page');
            this.loadData();
        });

        $(document).on('change', '#it-assets-edit-photos-input', (e) => {
            const newFiles = Array.from(e.target.files);
            this.newPhotosFiles = this.newPhotosFiles.concat(newFiles);
            this.updatePhotoPreview();
            $(e.target).val('');
        });

        $(document).on('click', '#it-assets-edit-add-item-btn', () => {
            this.addEditDdItem();
        });

        $(document).on('click', '.remove-it-assets-edit-item-btn', (e) => {
            const btn = $(e.currentTarget);
            const idx = btn.data('index');
            this.removeEditDdItem(idx);
        });
    }

    formatPhotoUrl(path) {
        if (!path) return '/images/placeholder.png';
        if (path.startsWith('data:') || /^https?:\/\//.test(path) || path.startsWith('/')) return path;
        if (path.startsWith('it-assets/') || path.startsWith('it_assets_files/')) return `/${path}`;
        return `${storageUrl.replace(/\/$/, '')}/${path}`;
    }

    updatePhotoPreview() {
        const previewContainer = $('#it-assets-edit-new-photos-preview');
        if (!previewContainer.length) return;
        previewContainer.empty();
        this.newPhotosFiles.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewContainer.append(`
                        <div class="relative w-20 h-20 rounded-xl overflow-hidden border-2 border-purple-400 shadow-md">
                            <img src="${e.target.result}" class="w-full h-full object-cover">
                            <button type="button" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center shadow-md hover:bg-red-600 transition-colors" onclick="itAssetsEntries.removePhotoPreview(${index})">
                                <i class="fas fa-times text-[10px]"></i>
                            </button>
                        </div>
                    `);
                };
                reader.readAsDataURL(file);
            } else {
                let iconClass = 'fa-file-alt';
                let iconColor = 'text-blue-500';
                if (file.name.endsWith('.pdf')) {
                    iconClass = 'fa-file-pdf';
                    iconColor = 'text-red-500';
                } else if (file.name.endsWith('.csv') || file.name.endsWith('.xlsx') || file.name.endsWith('.xls')) {
                    iconClass = 'fa-file-excel';
                    iconColor = 'text-green-500';
                } else if (file.name.endsWith('.doc') || file.name.endsWith('.docx')) {
                    iconClass = 'fa-file-word';
                    iconColor = 'text-blue-600';
                }
                previewContainer.append(`
                    <div class="relative w-20 h-20 rounded-xl border-2 border-purple-400 bg-purple-50 flex flex-col items-center justify-center p-1 text-center shadow-md">
                        <i class="fas ${iconClass} ${iconColor} text-2xl mb-1"></i>
                        <span class="text-[8px] font-bold text-slate-700 truncate w-full px-1" title="${file.name}">${file.name}</span>
                        <button type="button" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center shadow-md hover:bg-red-600 transition-colors" onclick="itAssetsEntries.removePhotoPreview(${index})">
                            <i class="fas fa-times text-[10px]"></i>
                        </button>
                    </div>
                `);
            }
        });
    }

    removePhotoPreview(index) {
        this.newPhotosFiles.splice(index, 1);
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
        this.filters = { search: '', statusFilter: '', palletFilter: '', categoryFilter: '' };
        this.loadData();
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
        return Math.max(0, this.toInt(source.quantity) - this.toInt(source.reuse_quantity) - this.toInt(source.scrap_quantity) - this.toInt(source.transferred_quantity));
    }

    availableWeight(source = {}) {
        if (source.net_weight !== undefined && source.net_weight !== null) {
            return Math.max(0, this.toFloat(source.net_weight));
        }
        return Math.max(0, this.toFloat(source.weight) - this.toFloat(source.reuse_weight) - this.toFloat(source.scrap_weight));
    }

    loadStats() {
        $.ajax({
            url: `${baseUrl}/stats`,
            type: 'GET',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: (stats) => this.renderStats(stats)
        });
    }

    renderStats(stats) {
        let html = '';
        stats.forEach(stat => {
            html += `
                <div class="bg-white p-4 md:p-5 rounded-xl border border-slate-100 shadow-sm transition-all hover:shadow-md relative overflow-hidden">
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 ${stat.color.replace('text-', 'bg-')}"></div>
                    <div class="pl-2">
                        <p class="text-[10px] md:text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-1 opacity-80">${stat.label}</p>
                        <h3 class="text-xl md:text-2xl font-black ${stat.color} tracking-tight">${stat.value}</h3>
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

        $.ajax({
            url: `${baseUrl}/load-data`,
            type: 'GET',
            data: params,
            cache: false,
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: (response) => {
                this.selectedIds.clear();
                this.renderTable(response.items);
                this.renderPagination(response);
                this.updateBulkActions();
            },
            error: (err) => {
                this.tableBody.html(`<tr><td colspan="13" class="p-8 text-center text-red-500 font-bold">Error loading IT assets data</td></tr>`);
            }
        });
    }

    renderTable(items) {
        if (!items || items.length === 0) {
            this.tableBody.html(`<tr><td colspan="13" class="p-16 text-center text-slate-300 font-bold uppercase text-xs">No Records Found</td></tr>`);
            return;
        }

        let html = '';
        items.forEach((item) => {
            html += this.renderTableRow(item);
        });
        this.tableBody.html(html);
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
            badgeHtml = `<span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-black bg-purple-100 text-purple-700 border border-purple-200 shrink-0" title="All items: ${fullTooltipName}">+ ${extraCount} more</span>`;
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

        let categoryDisplay = item.category || '-';
        if (item.sub_category) categoryDisplay = `${item.category} > ${item.sub_category}`;
        if (item.sub_sub_category) categoryDisplay += ` > ${item.sub_sub_category}`;

        const statusHtml = `
            <select data-action="updateStatus" data-item-id="${item.id}" style="width: 100%; min-width: 10rem;" class="w-full px-2 py-2 rounded-lg text-[10px] font-black uppercase border-none cursor-pointer shadow-sm
                ${item.status === 'Completed' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' :
                  item.status === 'In Progress' ? 'bg-amber-400 text-white' :
                  item.status === 'Draft' ? 'bg-slate-200 text-slate-700 border border-slate-300' :
                  'bg-purple-600 text-white'}">
                <option value="Draft" ${item.status === 'Draft' ? 'selected' : ''}>Draft</option>
                <option value="Recieved" ${['Recieved', 'Received'].includes(item.status) ? 'selected' : ''}>Recieved</option>
                <option value="In Progress" ${item.status === 'In Progress' ? 'selected' : ''}>In Progress</option>
                <option value="Ready for Inventory" ${item.status === 'Ready for Inventory' ? 'selected' : ''}>Ready for Inventory</option>
                <option value="Completed" ${item.status === 'Completed' ? 'selected' : ''}>Completed</option>
                ${!['Draft', 'Recieved', 'Received', 'In Progress', 'Ready for Inventory', 'Completed'].includes(item.status) ? `<option value="${item.status}" selected>${item.status}</option>` : ''}
            </select>
        `;

        return `
            <tr class="hover:bg-slate-50/50 transition-colors text-[13px] text-slate-700" data-item-id="${item.id}">
                <td class="px-4 py-4 text-center">
                    <input type="checkbox" class="item-select h-4 w-4 rounded border-slate-300 text-purple-600 focus:ring-purple-500" data-item-id="${item.id}">
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
                    <span class="px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 border-purple-100 text-[9px] font-black uppercase">
                        IT Assets
                    </span>
                </td>
                <td class="px-4 py-4 text-center">${photoHtml}</td>
                <td class="px-4 py-4 text-center">
                    <input type="number" min="0" value="${this.toInt(item.quantity)}" data-action="updateQuantity" data-item-id="${item.id}" class="w-14 p-1 border border-slate-200 rounded-md text-center font-bold text-slate-800 text-sm">
                </td>
                <td class="px-4 py-4" style="width: 11rem; min-width: 11rem;">${statusHtml}</td>
                <td class="px-4 py-4 font-black text-slate-900 text-sm">$${parseFloat(item.estimated_value || 0).toFixed(2)}</td>
                <td class="px-4 py-4">
                    <div class="flex flex-row items-center justify-end gap-1.5">
                        <button data-action="showDetails" data-item-id="${item.id}" class="h-9 w-9 bg-slate-50 text-slate-400 rounded-xl transition-all hover:bg-slate-900 hover:text-white flex items-center justify-center active:scale-90" title="View Details">
                            <i class="fas fa-eye text-xs"></i>
                        </button>
                        <button data-action="printLabel" data-item-id="${item.id}" class="h-9 w-9 bg-orange-50 text-orange-500 rounded-xl transition-all hover:bg-orange-500 hover:text-white flex items-center justify-center active:scale-90" title="Print Label">
                            <i class="fas fa-print text-xs"></i>
                        </button>
                        <button data-action="editItem" data-item-id="${item.id}" class="h-9 w-9 bg-purple-50 text-purple-600 rounded-xl transition-all hover:bg-purple-600 hover:text-white flex items-center justify-center active:scale-90" title="Edit Item">
                            <i class="fas fa-edit text-xs"></i>
                        </button>
                        <button data-action="deleteItem" data-item-id="${item.id}" class="h-9 w-9 bg-red-50 text-red-400 rounded-xl transition-all hover:bg-red-500 hover:text-white flex items-center justify-center active:scale-90" title="Delete Item">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
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
            html += `<a href="#" data-page="${i}" class="px-3 py-2 border rounded ${i === current_page ? 'bg-purple-600 text-white' : 'hover:bg-slate-100'}">${i}</a>`;
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
        if (ids.length === 0 || !status) return;

        $.ajax({
            url: `${baseUrl}/bulk-status`,
            type: 'PUT',
            data: { ids, status },
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: (response) => {
                if (response.success) {
                    this.selectedIds.clear();
                    this.loadData();
                    this.loadStats();
                }
            }
        });
    }

    bulkDeleteItems() {
        const ids = this.getSelectedIds();
        if (ids.length === 0 || !confirm(`Delete ${ids.length} selected item(s)?`)) return;

        $.ajax({
            url: `${baseUrl}/bulk-delete`,
            type: 'DELETE',
            data: { ids },
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: (response) => {
                if (response.success) {
                    this.selectedIds.clear();
                    this.loadData();
                    this.loadStats();
                }
            }
        });
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
                    $('#itAssetsItemDetailsModal').removeClass('hidden');
                }
            }
        });
    }

    renderDetailsModal(item) {
        const photos = item.photos_paths && item.photos_paths.length > 0 ? item.photos_paths : [];
        const netWeight = this.availableWeight(item);
        const netQuantity = this.availableQuantity(item);

        const names = item.name ? item.name.split(',').map(s => s.trim()) : [];
        const titleName = names.length > 0 ? names[0] : 'Item';
        $('#it-assets-modal-title').text(`View IT Asset Item: ${titleName}`);
        
        $('#it-assets-modal-category').text(item.category || 'N/A');
        $('#it-assets-modal-sub-category').text(item.sub_category || 'N/A');
        $('#it-assets-modal-sub-sub-category').text(item.sub_sub_category || 'N/A');
        $('#it-assets-modal-id').text(item.id);
        $('#it-assets-modal-value').text('$' + parseFloat(item.estimated_value || 0).toFixed(2));
        $('#it-assets-modal-qty-value').text(item.quantity || 0);
        $('#it-assets-modal-reuse-quantity').text(item.reuse_quantity || 0);
        $('#it-assets-modal-scrap-quantity').text(item.scrap_quantity || 0);
        $('#it-assets-modal-remaining-qty').text(netQuantity + ' PCS');
        $('#it-assets-modal-condition').text(item.condition || 'N/A');
        $('#it-assets-modal-status').text(item.status);
        if (item.status === 'Draft') {
            $('#it-assets-modal-status').removeClass().addClass('px-3 py-1 text-[10px] font-black uppercase rounded-lg text-slate-700 bg-slate-200 border border-slate-300');
        } else if (item.status === 'Completed') {
            $('#it-assets-modal-status').removeClass().addClass('px-3 py-1 text-[10px] font-black uppercase rounded-lg text-emerald-700 bg-emerald-100 border border-emerald-200');
        } else if (item.status === 'In Progress') {
            $('#it-assets-modal-status').removeClass().addClass('px-3 py-1 text-[10px] font-black uppercase rounded-lg text-white bg-amber-400');
        } else {
            $('#it-assets-modal-status').removeClass().addClass('px-3 py-1 text-[10px] font-black uppercase rounded-lg text-white bg-purple-600');
        }
        
        this.renderViewDdItems(item);
        
        $('#it-assets-modal-weight').text(parseFloat(item.weight || 0).toFixed(2));
        $('#it-assets-modal-reuse-weight').text(parseFloat(item.reuse_weight || 0).toFixed(2));
        $('#it-assets-modal-scrap-weight').text(parseFloat(item.scrap_weight || 0).toFixed(2));
        $('#it-assets-modal-net-weight').text(parseFloat(netWeight).toFixed(2) + ' Lbs');
        
        $('#it-assets-modal-placement').text(item.item_placement || 'N/A');
        $('#it-assets-modal-pallet').text(item.pallet_number || 'N/A');
        $('#it-assets-modal-receiving-employee').text(item.receiving_employee || 'N/A');
        $('#it-assets-modal-processing-employee').text(item.processing_employee || 'N/A');
        $('#it-assets-modal-it-assets-employee').text(item.it_assets_employee || item.intake_employee || 'N/A');
        $('#it-assets-modal-updated-at').text(new Date(item.updated_at).toLocaleDateString('en-US', { month: '2-digit', day: '2-digit', year: '2-digit', hour: '2-digit', minute: '2-digit' }));
        $('#it-assets-modal-notes').text(item.notes || 'No internal notes provided.');
        
        $('#it-assets-modal-barcode-img').attr('src', `https://bwipjs-api.metafloor.com/?bcid=code128&text=${encodeURIComponent(item.barcode)}&scale=2&height=10&includetext=true`);
        
        const photoGallery = photos.length > 0
            ? photos.map(path => {
                const isImage = path.startsWith('data:image/') || /\.(jpg|jpeg|png|webp|gif|svg|bmp)$/i.test(path);
                let filename = 'file';
                if (path.startsWith('data:')) {
                    const mime = path.substring(5, path.indexOf(';'));
                    if (mime.includes('pdf')) filename = 'document.pdf';
                    else if (mime.includes('csv')) filename = 'document.csv';
                    else if (mime.includes('excel') || mime.includes('spreadsheet') || mime.includes('sheet')) filename = 'document.xlsx';
                    else if (mime.includes('word') || mime.includes('processing')) filename = 'document.docx';
                    else filename = 'attachment';
                } else {
                    filename = path.split('/').pop();
                }

                if (isImage) {
                    return `
                        <div class="w-24 h-24 rounded-xl overflow-hidden border border-slate-200 shadow-sm cursor-pointer relative">
                            <img src="${this.formatPhotoUrl(path)}" class="w-full h-full object-cover gallery-image" data-photos='${JSON.stringify(photos.map(p => this.formatPhotoUrl(p)))}'>
                        </div>
                    `;
                } else {
                    let iconClass = 'fa-file-alt';
                    let iconColor = 'text-purple-500';
                    if (path.includes('pdf') || path.endsWith('.pdf')) {
                        iconClass = 'fa-file-pdf';
                        iconColor = 'text-red-500';
                    } else if (path.includes('csv') || path.includes('excel') || path.endsWith('.csv') || path.endsWith('.xlsx') || path.endsWith('.xls')) {
                        iconClass = 'fa-file-excel';
                        iconColor = 'text-green-500';
                    } else if (path.includes('word') || path.endsWith('.doc') || path.endsWith('.docx')) {
                        iconClass = 'fa-file-word';
                        iconColor = 'text-blue-600';
                    }
                    return `
                        <a href="${this.formatPhotoUrl(path)}" target="_blank" download="${filename}" class="w-24 h-24 rounded-xl border border-slate-200 bg-white flex flex-col items-center justify-center p-2 text-center shadow-sm group hover:border-purple-300 transition-colors">
                            <i class="fas ${iconClass} ${iconColor} text-2xl mb-1 group-hover:scale-110 transition-transform"></i>
                            <span class="text-[9px] font-bold text-slate-600 truncate w-full" title="${filename}">${filename}</span>
                        </a>
                    `;
                }
            }).join('')
            : '<div class="text-slate-400 text-xs font-bold py-4">No photos found</div>';
        $('#it-assets-modal-photos').html(photoGallery);

        $('#it-assets-btn-modal-edit').off('click').on('click', () => this.editItem(item.id));
        $('#it-assets-btn-modal-print').off('click').on('click', () => this.printItemLabel(item));
    }

    renderViewDdItems(item) {
        const container = document.getElementById("it-assets-modal-items-list");
        if (!container) return;

        const names = item.name ? item.name.split(',').map(s => s.trim()) : [];
        const brands = item.brand ? item.brand.split(',').map(s => s.trim()) : [];
        const models = item.model ? item.model.split(',').map(s => s.trim()) : [];
        const serials = item.serial_number ? item.serial_number.split(',').map(s => s.trim()) : [];
        const maxLen = Math.max(names.length, brands.length, models.length, serials.length, 1);

        const getItemVal = (arr, index, defaultVal = 'N/A') => {
            if (!arr || arr.length === 0) return defaultVal;
            if (arr[index] !== undefined && arr[index] !== '') return arr[index];
            const fallback = arr[arr.length - 1] || arr[0];
            return (fallback !== undefined && fallback !== '') ? fallback : defaultVal;
        };

        let html = '';
        for (let i = 0; i < maxLen; i++) {
            const name = getItemVal(names, i, 'N/A');
            const brand = getItemVal(brands, i, 'N/A');
            const model = getItemVal(models, i, 'N/A');
            const serial = serials[i] || 'N/A';

            html += `
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl relative mb-2">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div><label class="text-[10px] font-bold text-slate-400 uppercase">Item Name</label><div class="p-2 bg-white border border-slate-200 rounded-lg text-xs font-bold">${this.escapeHtml(name)}</div></div>
                        <div><label class="text-[10px] font-bold text-slate-400 uppercase">Brand</label><div class="p-2 bg-white border border-slate-200 rounded-lg text-xs font-bold">${this.escapeHtml(brand)}</div></div>
                        <div><label class="text-[10px] font-bold text-slate-400 uppercase">Model</label><div class="p-2 bg-white border border-slate-200 rounded-lg text-xs font-bold">${this.escapeHtml(model)}</div></div>
                        <div><label class="text-[10px] font-bold text-slate-400 uppercase">Serial</label><div class="p-2 bg-white border border-slate-200 rounded-lg text-xs font-bold font-mono">${this.escapeHtml(serial)}</div></div>
                    </div>
                </div>
            `;
        }
        container.innerHTML = html;
    }

    editItem(itemId) {
        closeItAssetsItemDetailsModal();
        $.ajax({
            url: `${baseUrl}/${itemId}/edit`,
            type: 'GET',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: (response) => {
                if (response.success) {
                    this.selectedItem = response.item;
                    this.renderEditModal(response.item, response.pallets);
                    $('#itAssetsEditModal').removeClass('hidden');
                }
            }
        });
    }

    renderEditModal(item, pallets) {
        this.newPhotosFiles = [];
        $('#it-assets-edit-photos-input').val('');
        $('#it-assets-edit-new-photos-preview').empty();

        $('#it-assets-edit-item-name-title').text(item.name || 'N/A');
        $('#it-assets-edit-barcode-title').text(item.barcode ? `(${item.barcode})` : '');
        $('#it-assets-edit-item-id').val(item.id);
        
        const names = item.name ? item.name.split(',').map(s => s.trim()) : [];
        const brands = item.brand ? item.brand.split(',').map(s => s.trim()) : [];
        const models = item.model ? item.model.split(',').map(s => s.trim()) : [];
        const serials = item.serial_number ? item.serial_number.split(',').map(s => s.trim()) : [];
        const maxLen = Math.max(names.length, brands.length, models.length, serials.length, 1);
        
        const getItemVal = (arr, index, defaultVal = '') => {
            if (!arr || arr.length === 0) return defaultVal;
            if (arr[index] !== undefined && arr[index] !== '') return arr[index];
            const fallback = arr[arr.length - 1] || arr[0];
            return (fallback !== undefined && fallback !== '') ? fallback : defaultVal;
        };

        this.editDdItems = [];
        for (let i = 0; i < maxLen; i++) {
            this.editDdItems.push({
                name: getItemVal(names, i, ''),
                brand: getItemVal(brands, i, ''),
                model: getItemVal(models, i, ''),
                serial: serials[i] || ''
            });
        }
        this.renderEditDdItems();

        $('#it-assets-edit-category').val(item.category || '');
        $('#it-assets-edit-sub-category').val(item.sub_category || '');
        $('#it-assets-edit-condition').val(item.condition || 'New');
        $('#it-assets-edit-estimated-value').val(item.estimated_value || 0);
        $('#it-assets-edit-quantity').val(item.quantity || 0);
        $('#it-assets-edit-weight').val(item.weight || 0);
        $('#it-assets-edit-reuse-quantity').val(item.reuse_quantity || 0);
        $('#it-assets-edit-scrap-quantity').val(item.scrap_quantity || 0);
        $('#it-assets-edit-reuse-weight').val(item.reuse_weight || 0);
        $('#it-assets-edit-scrap-weight').val(item.scrap_weight || 0);
        $('#it-assets-edit-item-placement').val(item.item_placement || '');
        $('#it-assets-edit-it-assets-employee').val(item.it_assets_employee || item.intake_employee || '');
        $('#it-assets-edit-notes').val(item.notes || '');

        $('#it-assets-edit-pallet-number').val(item.pallet_number || '');

        // Render existing photos/files
        const photoHTML = item.photos_paths && item.photos_paths.length > 0
            ? item.photos_paths.map(path => {
                const isImage = path.startsWith('data:image/') || /\.(jpg|jpeg|png|webp|gif|svg|bmp)$/i.test(path);
                let filename = 'file';
                if (path.startsWith('data:')) {
                    const mime = path.substring(5, path.indexOf(';'));
                    if (mime.includes('pdf')) filename = 'document.pdf';
                    else if (mime.includes('csv')) filename = 'document.csv';
                    else if (mime.includes('excel') || mime.includes('spreadsheet') || mime.includes('sheet')) filename = 'document.xlsx';
                    else if (mime.includes('word') || mime.includes('processing')) filename = 'document.docx';
                    else filename = 'attachment';
                } else {
                    filename = path.split('/').pop();
                }

                const safePathEscaped = path.replace(/'/g, "\\'");

                if (isImage) {
                    return `
                        <div class="relative w-20 h-20 group rounded-xl overflow-hidden border border-slate-200 shadow-sm" data-path="${path}">
                            <img src="${this.formatPhotoUrl(path)}" class="w-full h-full object-cover">
                            <button type="button" class="absolute inset-0 bg-red-600/70 text-white opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all duration-200" onclick="itAssetsEntries.deletePhoto('${item.id}', '${safePathEscaped}')">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </div>
                    `;
                } else {
                    let iconClass = 'fa-file-alt';
                    let iconColor = 'text-blue-500';
                    if (path.includes('pdf') || path.endsWith('.pdf')) {
                        iconClass = 'fa-file-pdf';
                        iconColor = 'text-red-500';
                    } else if (path.includes('csv') || path.includes('excel') || path.endsWith('.csv') || path.endsWith('.xlsx') || path.endsWith('.xls')) {
                        iconClass = 'fa-file-excel';
                        iconColor = 'text-green-500';
                    } else if (path.includes('word') || path.endsWith('.doc') || path.endsWith('.docx')) {
                        iconClass = 'fa-file-word';
                        iconColor = 'text-blue-600';
                    }
                    return `
                        <div class="relative w-20 h-20 group rounded-xl border border-slate-200 bg-slate-50 flex flex-col items-center justify-center p-1 text-center shadow-sm" data-path="${path}">
                            <i class="fas ${iconClass} ${iconColor} text-2xl mb-1"></i>
                            <span class="text-[8px] font-bold text-slate-600 truncate w-full px-1" title="${filename}">${filename}</span>
                            <button type="button" class="absolute inset-0 bg-red-600/70 text-white opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all duration-200" onclick="itAssetsEntries.deletePhoto('${item.id}', '${safePathEscaped}')">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </div>
                    `;
                }
            }).join('')
            : '';
        $('#it-assets-edit-existing-photos').html(photoHTML);

        this.updateItAssetsNetCalculations();

        $(document).off('input', '#it-assets-edit-weight, #it-assets-edit-reuse-weight, #it-assets-edit-scrap-weight, #it-assets-edit-quantity, #it-assets-edit-reuse-quantity, #it-assets-edit-scrap-quantity')
                   .on('input', '#it-assets-edit-weight, #it-assets-edit-reuse-weight, #it-assets-edit-scrap-weight, #it-assets-edit-quantity, #it-assets-edit-reuse-quantity, #it-assets-edit-scrap-quantity', () => {
            this.updateItAssetsNetCalculations();
        });

        $(document).off('submit', '#it-assets-edit-item-form').on('submit', '#it-assets-edit-item-form', (e) => {
            e.preventDefault();
            this.submitEditForm();
        });
    }

    updateItAssetsNetCalculations() {
        const weight = parseFloat($('#it-assets-edit-weight').val() || 0);
        const reuseWeight = parseFloat($('#it-assets-edit-reuse-weight').val() || 0);
        const scrapWeight = parseFloat($('#it-assets-edit-scrap-weight').val() || 0);
        const quantity = parseInt($('#it-assets-edit-quantity').val() || 0);
        const reuseQuantity = parseInt($('#it-assets-edit-reuse-quantity').val() || 0);
        const scrapQuantity = parseInt($('#it-assets-edit-scrap-quantity').val() || 0);

        const deductedWeight = reuseWeight + scrapWeight;
        const netWeight = Math.max(0, weight - deductedWeight);
        const deductedQty = reuseQuantity + scrapQuantity;
        const netQuantity = Math.max(0, quantity - deductedQty);

        $('#it-assets-edit-remaining-qty').text(netQuantity + ' PCS');
        $('#it-assets-edit-net-weight').text(parseFloat(netWeight).toFixed(2) + ' Lbs');
    }

    renderEditDdItems() {
        const container = document.getElementById("it-assets-edit-items-list");
        if (!container) return;
        
        container.innerHTML = this.editDdItems.map((item, idx) => `
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl relative mb-2" data-index="${idx}">
                ${this.editDdItems.length > 1 ? `<button type="button" class="remove-it-assets-edit-item-btn absolute top-3 right-3 text-red-500" data-index="${idx}"><i class="fas fa-trash-alt"></i></button>` : ''}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 pr-6">
                    <div><label class="text-[10px] font-bold text-slate-400 uppercase">Item Name</label><input type="text" class="edit-item-name form-input w-full p-2 border border-slate-200 rounded-lg text-xs bg-white" value="${this.escapeHtml(item.name)}"></div>
                    <div><label class="text-[10px] font-bold text-slate-400 uppercase">Brand</label><input type="text" class="edit-item-brand form-input w-full p-2 border border-slate-200 rounded-lg text-xs bg-white" value="${this.escapeHtml(item.brand)}"></div>
                    <div><label class="text-[10px] font-bold text-slate-400 uppercase">Model</label><input type="text" class="edit-item-model form-input w-full p-2 border border-slate-200 rounded-lg text-xs bg-white" value="${this.escapeHtml(item.model)}"></div>
                    <div><label class="text-[10px] font-bold text-slate-400 uppercase">Serial Number</label><input type="text" class="edit-item-serial form-input w-full p-2 border border-slate-200 rounded-lg text-xs bg-white" value="${this.escapeHtml(item.serial)}"></div>
                </div>
            </div>
        `).join('');

        container.querySelectorAll(".p-4").forEach(row => {
            const idx = parseInt(row.dataset.index, 10);
            row.querySelector(".edit-item-name")?.addEventListener("input", (e) => this.editDdItems[idx].name = e.target.value);
            row.querySelector(".edit-item-brand")?.addEventListener("input", (e) => this.editDdItems[idx].brand = e.target.value);
            row.querySelector(".edit-item-model")?.addEventListener("input", (e) => this.editDdItems[idx].model = e.target.value);
            row.querySelector(".edit-item-serial")?.addEventListener("input", (e) => this.editDdItems[idx].serial = e.target.value);
        });
    }

    addEditDdItem() {
        this.editDdItems.push({ name: "", brand: "", model: "", serial: "" });
        this.renderEditDdItems();
    }

    removeEditDdItem(index) {
        if (this.editDdItems.length > 1) {
            this.editDdItems.splice(index, 1);
            this.renderEditDdItems();
        }
    }

    submitEditForm() {
        const formData = new FormData();
        const itemId = $('#it-assets-edit-item-id').val();

        formData.append('name', this.editDdItems.map(i => (i.name || '').trim()).join(', '));
        formData.append('brand', this.editDdItems.map(i => (i.brand || '').trim()).join(', '));
        formData.append('model', this.editDdItems.map(i => (i.model || '').trim()).join(', '));
        formData.append('serial_number', this.editDdItems.map(i => (i.serial || '').trim()).join(', '));
        formData.append('category', $('#it-assets-edit-category').val());
        formData.append('sub_category', $('#it-assets-edit-sub-category').val());
        formData.append('condition', $('#it-assets-edit-condition').val());
        formData.append('estimated_value', $('#it-assets-edit-estimated-value').val());
        formData.append('quantity', $('#it-assets-edit-quantity').val());
        formData.append('weight', $('#it-assets-edit-weight').val());
        formData.append('reuse_quantity', $('#it-assets-edit-reuse-quantity').val());
        formData.append('scrap_quantity', $('#it-assets-edit-scrap-quantity').val());
        formData.append('reuse_weight', $('#it-assets-edit-reuse-weight').val());
        formData.append('scrap_weight', $('#it-assets-edit-scrap-weight').val());
        formData.append('pallet_number', $('#it-assets-edit-pallet-number').val());
        formData.append('item_placement', $('#it-assets-edit-item-placement').val());
        formData.append('it_assets_employee', $('#it-assets-edit-it-assets-employee').val());
        formData.append('notes', $('#it-assets-edit-notes').val());

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
                    this.newPhotosFiles = [];
                    closeItAssetsEditModal();
                    this.loadData();
                    this.loadStats();
                    alert(response.message || 'IT Assets item updated!');
                }
            }
        });
    }

    deletePhoto(itemId, photoPath) {
        if (!confirm('Are you sure you want to delete this file/image?')) return;

        $.ajax({
            url: `${baseUrl}/${itemId}/delete-photo`,
            type: 'POST',
            data: { photo_path: photoPath },
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: (response) => {
                if (response.success) {
                    $(`div[data-path="${photoPath}"]`).remove();
                    this.loadData();
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

    updateQuantity(itemId, newQuantity) {
        const totalQuantity = this.toInt(newQuantity);

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
            }
        });
    }

    prepareToInventory(itemId) {
        $.ajax({
            url: `${baseUrl}/${itemId}/prepare-inventory`,
            type: 'GET',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: (response) => {
                if (response.success && response.inventoryData) {
                    window.currentIntakeItem = response.item;
                    window.currentIntakeType = 'IT Assets';
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
        $('#inv-brand-category').val(inventoryData.brand_category || '');
        
        const availableQty = this.availableQuantity(inventoryData);
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
        
        $('#quantity-error').hide();
        $('#inv-submit-btn').prop('disabled', true);

        if (window.categoryHierarchyManager) {
            window.categoryHierarchyManager.initialize();
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

        $('#addToInventoryModal').removeClass('hidden');

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

        if (!itemId) {
            alert('No item selected. Please try again.');
            return;
        }
        
        if (selectedQty <= 0) {
            errorMsg.text('Please enter a valid quantity').show();
            return;
        }

        if (selectedQty > availableQty) {
            errorMsg.text(`Not enough items available. Only ${availableQty} items remaining`).show();
            return;
        }

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
        formData.append('transferred_qty', selectedQty);
        formData.append('lot_info', $('#inv-lot-info').val());
        formData.append('serial_number', $('#inv-serial-number').val());
        formData.append('status', $('#inv-status').val());
        formData.append('pallet_info', $('#inv-pallet-info').val());
        formData.append('device_specs', $('#inv-device-specs').val());
        formData.append('operating_system', $('#inv-os').val());
        formData.append('notes', $('#inv-notes').val());
        formData.append('put_away_location', $('#inv-location').val());

        const fileInput = $('#inv-photos')[0];
        if (fileInput && fileInput.files.length > 0) {
            for (let i = 0; i < fileInput.files.length; i++) {
                formData.append('photos[]', fileInput.files[i]);
            }
        }

        submitBtn.prop('disabled', true);
        submitSpinner.removeClass('hidden');
        submitText.text('Saving...');

        $.ajax({
            url: `${baseUrl}/${itemId}/save-to-inventory`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: (response) => {
                submitBtn.prop('disabled', false);
                submitSpinner.addClass('hidden');
                submitText.text('Save to Inventory');
                
                if (response.success) {
                    $('#addToInventoryModal').addClass('hidden');
                    $('#saveToInventoryForm')[0].reset();
                    this.loadData();
                    this.loadStats();
                    alert(response.message || 'Item successfully added to inventory!');
                } else {
                    alert('Error: ' + (response.message || 'Failed to save item to inventory'));
                }
            },
            error: (err) => {
                submitBtn.prop('disabled', false);
                submitSpinner.addClass('hidden');
                submitText.text('Save to Inventory');
                alert('Error: ' + (err.responseJSON?.message || 'Failed to save item to inventory'));
            }
        });
    }

    printItemLabel(item) {
        const serialVal = item.serial_number ? item.serial_number.split(',')[0].trim() : '';
        const serialBarcode = serialVal || item.barcode || 'N/A';
        const barcodeImageSrc = `https://bwipjs-api.metafloor.com/?bcid=code128&text=${encodeURIComponent(serialBarcode)}&scale=2&height=12&includetext=false`;
        const html = `
            <section class="recycling-print-label">
                <div class="label-head">
                    <strong>IT Investment Recoveries</strong>
                    <span>IT Assets Serial Label</span>
                </div>
                <div class="barcode-block">
                    <img src="${this.escapeHtml(barcodeImageSrc)}" alt="barcode">
                    <div>${this.escapeHtml(serialBarcode)}</div>
                </div>
                <div class="label-grid">
                    ${this.renderLabelRow('Item', item.name ? item.name.split(',')[0] : 'N/A')}
                    ${this.renderLabelRow('Brand/Model', [item.brand, item.model].filter(Boolean).join(' ') || 'N/A')}
                    ${this.renderLabelRow('Serial', item.serial_number ? item.serial_number.split(',')[0] : 'N/A')}
                    ${this.renderLabelRow('Category', item.category || 'N/A')}
                    ${this.renderLabelRow('Condition', item.condition || 'N/A')}
                    ${this.renderLabelRow('Total Quantity', item.quantity || 1)}
                    ${this.renderLabelRow('IT Asset quantities', item.reuse_quantity || 0)}
                    ${this.renderLabelRow('Scrap Quantity', item.scrap_quantity || 0)}
                    ${this.renderLabelRow('Total Weight', `${parseFloat(item.weight || 0).toFixed(2)} lbs`)}
                    ${this.renderLabelRow('IT Asset Weight', `${parseFloat(item.reuse_weight || 0).toFixed(2)} lbs`)}
                    ${this.renderLabelRow('Scrap Weight', `${parseFloat(item.scrap_weight || 0).toFixed(2)} lbs`)}
                    ${this.renderLabelRow('Value', `$${parseFloat(item.estimated_value || 0).toFixed(2)}`)}
                    ${this.renderLabelRow('Intake Barcode', item.barcode || 'N/A')}
                    ${this.renderLabelRow('Date', new Date().toLocaleDateString())}
                </div>
            </section>
        `;
        const printWindow = window.open('', '_blank', 'height=360,width=520');
        if (printWindow) {
            printWindow.document.write(`<html><head><title>Print Label</title><style>@page { size: 3.5in 2.25in; margin: 0; } body { font-family: Arial; padding: 10px; }</style></head><body>${html}<script>window.print();</script></body></html>`);
            printWindow.document.close();
        }
    }

    renderLabelRow(label, value) {
        return `<div class="label-row" style="display:flex; justify-between; border-bottom:1px solid #ddd; font-size:10px;"><span>${this.escapeHtml(label)}:</span> <strong>${this.escapeHtml(value)}</strong></div>`;
    }

    escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe.toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    exportCsv() {
        const params = new URLSearchParams(this.filters).toString();
        window.location.href = `${baseUrl}/export-csv?${params}`;
    }
}

function closeItAssetsItemDetailsModal() {
    $('#itAssetsItemDetailsModal').addClass('hidden');
}

function closeItAssetsEditModal() {
    $('#itAssetsEditModal').addClass('hidden');
}

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
        if (path.startsWith('data:') || /^https?:\/\//.test(path) || path.startsWith('/')) return path;
        if (path.startsWith('it-assets/') || path.startsWith('it_assets_files/')) return '/' + path;
        return `/it_assets_files/${path}`;
    });
    const firstPhoto = photoUrls[0];
    const galleryJson = JSON.stringify(photoUrls);
    
    return `<img src="${firstPhoto}" class="w-10 h-10 rounded-lg object-cover mx-auto cursor-pointer hover:ring-2 hover:ring-purple-500 transition-all" onclick='openGallery(${galleryJson})' title="Click to view all images" onerror="this.src='/images/placeholder.png'">`;
}

let itAssetsEntries;
$(document).ready(() => {
    itAssetsEntries = new ItAssetsEntries();
});
