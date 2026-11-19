class InventoryManager {
    constructor() {
        this.currentPage = 1;
        this.filters = {
            search: "",
            statusFilter: "",
            intakeFilter: "",
            categoryFilter: "",
            subCategoryFilter: "",
            subSubCategoryFilter: "",
            palletFilter: "",
        };
        this.selectedItem = null;
        this.selectedItemIds = new Set();
        this.visibleItemIds = [];
        this.editPhotos = []; // holds existing photo paths (string) and new photo items {id,file,preview}
        this.renderEditRepairPhotos = null;
        this.isRemovingBackground = false;
        this.photoEditor = {
            index: null,
            image: null,
            sourcePreview: null,
            adjustments: {
                brightness: 100,
                contrast: 100,
                saturation: 100,
                removeBg: 0,
            },
        };
        this.isProcessingSell = false;
        this.isBulkDeleting = false;
        this.init();
    }

    init() {
        console.log("InventoryManager initialized");
        this.cacheElements();
        this.attachEventListeners();
        this.syncSelectionUI();
        this.loadCategoryFilters();
        this.loadStats();
        this.loadData();
    }

    cacheElements() {
        this.searchInput = $("#search-input");
        this.statusFilter = $("#status-filter");
        this.intakeFilter = $("#intake-filter");
        this.categoryFilter = $("#category-filter");
        this.subCategoryFilter = $("#sub-category-filter");
        this.subSubCategoryFilter = $("#sub-sub-category-filter");
        this.palletFilter = $("#pallet-filter");
        this.tableBody = $("#items-table-body");
        this.paginationContainer = $("#pagination-container");
        this.statsContainer = $("#stats-container");
        this.refreshBtn = $("#refresh-btn");
        this.exportCsvBtn = $("#export-csv-btn");
        this.bulkDeleteBtn = $("#bulk-delete-btn");
        this.bulkDeleteCount = $("#bulk-delete-count");
        this.selectAllItems = $("#select-all-items");
    }

    attachEventListeners() {
        // Filter change events
        this.searchInput.on("keyup", (e) => {
            this.resetSelection();
            this.filters.search = this.searchInput.val();
            this.currentPage = 1;
            this.loadData();
        });

        this.statusFilter.on("change", (e) => {
            this.resetSelection();
            this.filters.statusFilter = this.statusFilter.val();
            this.currentPage = 1;
            this.loadData();
        });

        this.intakeFilter.on("change", (e) => {
            this.resetSelection();
            this.filters.intakeFilter = this.intakeFilter.val();
            this.currentPage = 1;
            this.loadData();
        });

        this.categoryFilter.on("change", (e) => {
            this.resetSelection();
            this.filters.categoryFilter = this.categoryFilter.val();
            this.filters.subCategoryFilter = "";
            this.filters.subSubCategoryFilter = "";
            this.currentPage = 1;
            this.updateSubCategoryOptions();
            this.loadData();
        });

        this.subCategoryFilter.on("change", (e) => {
            this.resetSelection();
            this.filters.subCategoryFilter = this.subCategoryFilter.val();
            this.filters.subSubCategoryFilter = "";
            this.currentPage = 1;
            this.updateSubSubCategoryOptions();
            this.loadData();
        });

        this.subSubCategoryFilter.on("change", (e) => {
            this.resetSelection();
            this.filters.subSubCategoryFilter = this.subSubCategoryFilter.val();
            this.currentPage = 1;
            this.loadData();
        });

        this.palletFilter.on("change", (e) => {
            this.resetSelection();
            this.filters.palletFilter = this.palletFilter.val();
            this.currentPage = 1;
            this.loadData();
        });

        this.refreshBtn.on("click", (e) => {
            this.resetSelection();
            this.loadData();
            this.loadStats();
        });

        this.exportCsvBtn.on("click", (e) => {
            this.exportCsv();
        });

        this.selectAllItems.on("change", (e) => {
            this.toggleVisibleSelection(e.currentTarget.checked);
        });

        this.bulkDeleteBtn.on("click", () => {
            this.bulkDeleteSelected();
        });

        $(document).on("change", ".inventory-item-checkbox", (e) => {
            const itemId = Number($(e.currentTarget).data("item-id"));
            this.updateItemSelection(itemId, e.currentTarget.checked);
        });

        // Table action handlers (delegated)
        $(document).on("click", '[data-action="showDetails"]', (e) => {
            const itemId = $(e.currentTarget).data("item-id");
            this.showItemDetails(itemId);
        });

        $(document).on("click", '[data-action="editItem"]', (e) => {
            const itemId = $(e.currentTarget).data("item-id");
            this.editItem(itemId);
        });

        $(document).on("click", '[data-action="deleteItem"]', (e) => {
            const itemId = $(e.currentTarget).data("item-id");
            if (confirm("Are you sure you want to delete this item?")) {
                this.deleteItem(itemId);
            }
        });

        $(document).on("click", '[data-action="viewShippingRates"]', (e) => {
            const itemId = $(e.currentTarget).data("item-id");
            this.viewShippingRates(itemId);
        });
        
        $(document).on("click", '[data-action="printLabel"]', (e) => {
            const itemId = $(e.currentTarget).data("item-id");
            this.printLabel(itemId);
        });

        $(document).on("click", '[data-action="sellItem"]', (e) => {
            const itemId = $(e.currentTarget).data("item-id");
            const itemName = $(e.currentTarget).data("item-name");
            const itemPrice = $(e.currentTarget).data("item-price");
            if ($(e.currentTarget).prop("disabled")) {
                return;
            }
            this.openSellModal(itemId, itemName, itemPrice);
        });

        $(document).on("input", "#sell-quantity", () => {
            this.updateSellQuantityRemaining();
        });

        $(document).on("change", '[data-action="updateStatus"]', (e) => {
            const itemId = $(e.currentTarget).data("item-id");
            const newStatus = $(e.currentTarget).val();
            this.updateStatus(itemId, newStatus);
        });

        // Pagination
        $(document).on("click", "[data-page]", (e) => {
            e.preventDefault();
            this.currentPage = $(e.currentTarget).data("page");
            this.loadData();
        });

        // Edit form submission
        $("#editItemForm").on("submit", (e) => {
            e.preventDefault();
            this.handleUpdateItem();
        });

        // Quantity calculation
        $("#edit-quantity").on("change", (e) => {
            this.updateQuantityCalculation();
        });

        $("#edit-parcel-weight").on("change", (e) => {
            this.updateWeightCalculation();
        });

        $("#photo-editor-brightness, #photo-editor-contrast, #photo-editor-saturation").on("input", () => {
            this.updatePhotoEditorFromControls();
        });

        $("#photo-editor-auto-bg").on("click", () => {
            this.removeBackgroundWithService();
        });

        $("#photo-editor-reset").on("click", () => {
            this.setPhotoEditorControls({
                brightness: 100,
                contrast: 100,
                saturation: 100,
                removeBg: 0,
            });
            this.renderPhotoEditorCanvas();
        });

        $("#photo-editor-apply").on("click", () => {
            this.applyPhotoEditorChanges();
        });

        // Horizontal drag-to-scroll for main table
        const slider = document.getElementById("table-scroll-container");
        if (slider) {
            let isDown = false;
            let startX;
            let scrollLeft;

            slider.addEventListener("mousedown", (e) => {
                // Ignore drag scroll on inputs, dropdowns, buttons, links, etc.
                if (e.target.closest("input, select, button, a, label, .cursor-pointer")) {
                    return;
                }
                isDown = true;
                slider.classList.add("active-grabbing");
                startX = e.pageX - slider.offsetLeft;
                scrollLeft = slider.scrollLeft;
            });

            slider.addEventListener("mouseleave", () => {
                isDown = false;
                slider.classList.remove("active-grabbing");
            });

            slider.addEventListener("mouseup", () => {
                isDown = false;
                slider.classList.remove("active-grabbing");
            });

            slider.addEventListener("mousemove", (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - slider.offsetLeft;
                const walk = (x - startX) * 1.5; // multiplier for scrolling speed
                slider.scrollLeft = scrollLeft - walk;
            });
        }
    }

    loadData() {
        const params = {
            search: this.filters.search,
            statusFilter: this.filters.statusFilter,
            intakeFilter: this.filters.intakeFilter,
            categoryFilter: this.filters.categoryFilter,
            subCategoryFilter: this.filters.subCategoryFilter,
            subSubCategoryFilter: this.filters.subSubCategoryFilter,
            palletFilter: this.filters.palletFilter,
            page: this.currentPage,
            _t: Date.now(),
        };

        console.log("Loading data with params:", params);

        $.ajax({
            url: `${baseUrl}/load-data`,
            type: "GET",
            data: params,
            cache: false,
            headers: { "X-CSRF-TOKEN": csrfToken },
            success: (response) => {
                console.log("Data loaded successfully:", response);

                if (
                    response.last_page &&
                    this.currentPage > response.last_page
                ) {
                    this.currentPage = response.last_page;
                    this.loadData();
                    return;
                }

                this.renderTable(response.items);
                this.renderPagination(response);
            },
            error: (err) => {
                console.error("Error loading data:", err);
                this.visibleItemIds = [];
                this.syncSelectionUI();
                this.tableBody.html(
                    `<tr><td colspan="12" class="p-8 text-center text-red-500 font-bold">Error loading data</td></tr>`,
                );
            },
        });
    }

    loadStats() {
        $.ajax({
            url: `${baseUrl}/stats`,
            type: "GET",
            headers: { "X-CSRF-TOKEN": csrfToken },
            success: (response) => {
                if (response.success) {
                    this.renderStats(response.stats);
                }
            },
        });
    }

    renderStats(stats) {
        const statsHtml = `
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-slate-800"></div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Items</p>
                <h3 class="text-2xl font-black tracking-tight text-slate-800">${stats.total}</h3>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-blue-600"></div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Received</p>
                <h3 class="text-2xl font-black tracking-tight text-blue-600">${stats.received}</h3>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-orange-500"></div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">In Repair</p>
                <h3 class="text-2xl font-black tracking-tight text-orange-500">${stats.repair}</h3>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-emerald-500"></div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Ready</p>
                <h3 class="text-2xl font-black tracking-tight text-emerald-500">${stats.ready}</h3>
            </div>
        `;
        this.statsContainer.html(statsHtml);
    }

    renderTable(items) {
        this.visibleItemIds = Array.isArray(items)
            ? items.map((item) => Number(item.id))
            : [];

        if (!items || items.length === 0) {
            this.tableBody.html(
                `<tr><td colspan="12" class="p-16 text-center text-slate-300 font-bold uppercase text-xs">No inventory records found.</td></tr>`,
            );
            this.syncSelectionUI();
            return;
        }

        let html = "";
        items.forEach((item) => {
            html += this.renderTableRow(item);
        });
        this.tableBody.html(html);
        this.syncSelectionUI();
    }

    renderTableRow(item) {
        // Handle item photos (product photos from eBay/intake & repair photos)
        const allPhotos = this.getAllPhotosArray(item);
        const repairPhotoHtml = this.renderRepairPhotoThumbnail(allPhotos);

        const netQuantity = (item.quantity || 0) - (item.reuse_quantity || 0);
        const sellButtonDisabled = netQuantity <= 0;

        // Build category hierarchy
        let categoryDisplay = item.category || "-";
        if (item.sub_category) {
            categoryDisplay = `${item.category} > ${item.sub_category}`;
        }
        if (item.sub_sub_category) {
            categoryDisplay += ` > ${item.sub_sub_category}`;
        }

        // Determine row background color based on sync status
        let rowBgClass = "hover:bg-slate-50/50";
        let syncButtonDisabled = "";
        let syncButtonHtml = `<button onclick="inventoryManager.syncIndividualItem(${item.id})"
            class="group h-9 w-9 bg-blue-50 text-blue-600 rounded-xl transition-all hover:bg-blue-600 hover:text-white flex items-center justify-center active:scale-90"
            title="Sync to Store">
            <i class="fas fa-sync-alt text-xs group-hover:rotate-180 duration-500"></i>
        </button>`;

        if (item.sync_status == 1) {
            rowBgClass = "bg-emerald-50/60 hover:bg-emerald-50/80 border-l-4 border-emerald-500";
            syncButtonDisabled = "disabled";
            syncButtonHtml = `<button disabled
                class="group h-9 w-9 bg-emerald-100 text-emerald-600 rounded-xl transition-all flex items-center justify-center cursor-default"
                title="Already Synced to Store">
                <i class="fas fa-check text-xs font-bold"></i>
            </button>`;
        }

        const isSelected = this.selectedItemIds.has(Number(item.id));

        return `
            <tr class="transition-colors text-[13px] text-slate-700 ${rowBgClass}" data-item-id="${item.id}" data-sync-status="${item.sync_status}">
                <td class="px-3 py-4 text-center">
                    <input type="checkbox"
                        class="inventory-item-checkbox h-4 w-4 cursor-pointer rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                        data-item-id="${item.id}"
                        aria-label="Select inventory item ${item.id}"
                        ${isSelected ? "checked" : ""}>
                </td>
                <td class="px-4 py-4 font-black text-slate-900 uppercase tracking-tighter">${item.barcode}</td>
                <td class="px-4 py-4">
                    <div class="truncate">
                        <p class="font-extrabold text-slate-900 leading-tight mb-0.5 truncate">${item.device_type}</p>
                        <p class="text-[9px] text-slate-400 font-bold uppercase">${item.brand_category || ""}</p>
                    </div>
                </td>
                <td class="px-4 py-4 text-center"><span class="text-[11px] font-black uppercase text-slate-500" title="${categoryDisplay}">${categoryDisplay}</span></td>
                <td class="px-4 py-4 text-center">
                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase border
                        ${item.intake_type == "Refurbishing" ? "bg-amber-50 text-amber-600 border-amber-100" : item.intake_type == "Universal Waste" ? "bg-emerald-50 text-emerald-600 border-emerald-100" : "bg-indigo-50 text-indigo-600 border-indigo-100"}">
                        ${item.intake_type || "E-commerce"}
                    </span>
                </td>
                <td class="px-4 py-4 text-center flex items-center justify-center">${repairPhotoHtml}</td>
                <td class="px-4 py-4 text-center"><span class="font-black text-slate-800">${netQuantity}</span></td>
                <td class="px-4 py-4 text-center min-w-[150px]">
                    <select data-action="updateStatus" data-item-id="${item.id}" class="w-full px-3 py-1.5 rounded-lg text-[10px] font-black uppercase border-none cursor-pointer shadow-sm
                        ${item.status.toUpperCase() == "LIVE ONLINE" ? "bg-blue-600 text-white" : ""}
                        ${item.status.toUpperCase() == "COMPLETED" ? "bg-purple-500 text-white" : ""}
                        ${item.status.toUpperCase() == "IN STOCK" ? "bg-slate-700 text-white" : ""}">
                        <option value="COMPLETED" ${item.status.toUpperCase() == "COMPLETED" ? "selected" : ""}>COMPLETED</option>
                        <option value="LIVE ONLINE" ${item.status.toUpperCase() == "LIVE ONLINE" ? "selected" : ""}>LIVE ONLINE</option>
                        <option value="In Stock" ${item.status.toUpperCase() == "IN STOCK" ? "selected" : ""}>IN STOCK</option>
                    </select>
                </td>
                <td class="px-4 py-4 font-black text-slate-900 text-sm">$${parseFloat(item.price || 0).toFixed(2)}</td>
                <td class="px-4 py-4 text-slate-400 font-medium text-[11px] text-center">${new Date(item.created_at).toLocaleDateString("en-US", { month: "2-digit", day: "2-digit", year: "2-digit" })}</td>
                <td class="px-4 py-4 text-center">
                    <button data-action="sellItem" data-item-id="${item.id}" data-item-name="${item.name}" data-item-price="${item.price}"
                        ${sellButtonDisabled ? "disabled" : ""}
                        class="h-9 w-full rounded-lg font-bold text-[10px] uppercase transition-all active:scale-95 ${sellButtonDisabled ? "bg-slate-100 text-slate-400 cursor-not-allowed" : "bg-green-50 text-green-600 hover:bg-green-600 hover:text-white"}">
                        <i class="fas fa-shopping-cart mr-1"></i> Sell
                    </button>
                </td>
                <td class="px-4 py-3">
                    <div class="flex flex-row items-center justify-end gap-1.5">
    ${syncButtonHtml}

    <button data-action="showDetails" data-item-id="${item.id}"
        class="h-9 w-9 bg-slate-50 text-slate-400 rounded-xl transition-all hover:bg-slate-900 hover:text-white flex items-center justify-center active:scale-90"
        title="View Details">
        <i class="fas fa-eye text-xs"></i>
    </button>

    <button data-action="editItem" data-item-id="${item.id}"
        class="h-9 w-9 bg-emerald-50 text-emerald-600 rounded-xl transition-all hover:bg-emerald-600 hover:text-white flex items-center justify-center active:scale-90"
        title="Edit Item">
        <i class="fas fa-edit text-xs"></i>
    </button>

    <button data-action="deleteItem" data-item-id="${item.id}"
        class="h-9 w-9 bg-red-50 text-red-400 rounded-xl transition-all hover:bg-red-500 hover:text-white flex items-center justify-center active:scale-90"
        title="Delete Item">
        <i class="fas fa-trash text-xs"></i>
    </button>
    <button data-action="printLabel" data-item-id="${item.id}"
        class="h-9 w-9 bg-amber-50 text-amber-600 rounded-xl transition-all hover:bg-amber-600 hover:text-white flex items-center justify-center active:scale-90"
        title="Print Label">
        <i class="fas fa-print text-xs"></i>
    </button>
</div>
                </td>
            </tr>
        `;
    }

    loadCategoryFilters() {
        if (window.categoryHierarchyManager) {
            window.categoryHierarchyManager.whenReady().then(() => {
                this.categories = window.categoryHierarchyManager.categories || [];
                this.categoryMap = window.categoryHierarchyManager.categoryMap || {};
                this.populateRootCategories();
            });
        } else {
            // Fallback if not loaded
            fetch('/admin/inventory/categories-tree')
                .then(res => res.json())
                .then(data => {
                    this.categories = data.categories || [];
                    this.categoryMap = {};
                    const flatten = (cats) => {
                        cats.forEach(c => {
                            this.categoryMap[c.id] = c;
                            if (c.children) flatten(c.children);
                        });
                    };
                    flatten(this.categories);
                    this.populateRootCategories();
                });
        }
    }

    populateRootCategories() {
        const rootCats = this.categories.filter(c => !c.parent_id);
        this.categoryFilter.html('<option value="">All Categories</option>');
        rootCats.forEach(c => {
            this.categoryFilter.append(`<option value="${c.name}" data-id="${c.id}">${c.name}</option>`);
        });
    }

    updateSubCategoryOptions() {
        this.subCategoryFilter.html('<option value="">All Sub Categories</option>');
        this.subSubCategoryFilter.html('<option value="">All Sub Sub Categories</option>');
        
        const catName = this.categoryFilter.val();
        const catId = this.categoryFilter.find('option:selected').data('id');
        
        if (catId && this.categoryMap[catId] && this.categoryMap[catId].children && this.categoryMap[catId].children.length > 0) {
            this.categoryMap[catId].children.forEach(c => {
                this.subCategoryFilter.append(`<option value="${c.name}" data-id="${c.id}">${c.name}</option>`);
            });
            this.subCategoryFilter.prop("disabled", false);
        } else {
            this.subCategoryFilter.prop("disabled", true);
        }
        this.subSubCategoryFilter.prop("disabled", true);
    }

    updateSubSubCategoryOptions() {
        this.subSubCategoryFilter.html('<option value="">All Sub Sub Categories</option>');
        const subCatId = this.subCategoryFilter.find('option:selected').data('id');
        
        if (subCatId && this.categoryMap[subCatId] && this.categoryMap[subCatId].children && this.categoryMap[subCatId].children.length > 0) {
            this.categoryMap[subCatId].children.forEach(c => {
                this.subSubCategoryFilter.append(`<option value="${c.name}" data-id="${c.id}">${c.name}</option>`);
            });
            this.subSubCategoryFilter.prop("disabled", false);
        } else {
            this.subSubCategoryFilter.prop("disabled", true);
        }
    }

    resetSelection() {
        this.selectedItemIds.clear();
        this.syncSelectionUI();
    }

    updateItemSelection(itemId, isSelected) {
        if (!Number.isInteger(itemId)) {
            return;
        }

        if (isSelected) {
            this.selectedItemIds.add(itemId);
        } else {
            this.selectedItemIds.delete(itemId);
        }

        this.syncSelectionUI();
    }

    toggleVisibleSelection(isSelected) {
        this.visibleItemIds.forEach((itemId) => {
            if (isSelected) {
                this.selectedItemIds.add(itemId);
            } else {
                this.selectedItemIds.delete(itemId);
            }
        });

        this.syncSelectionUI();
    }

    syncSelectionUI() {
        const selectedVisibleCount = this.visibleItemIds.filter((itemId) =>
            this.selectedItemIds.has(itemId),
        ).length;
        const hasVisibleItems = this.visibleItemIds.length > 0;
        const allVisibleSelected =
            hasVisibleItems &&
            selectedVisibleCount === this.visibleItemIds.length;
        const someVisibleSelected =
            selectedVisibleCount > 0 && !allVisibleSelected;

        this.selectAllItems
            .prop("checked", allVisibleSelected)
            .prop("indeterminate", someVisibleSelected)
            .prop("disabled", !hasVisibleItems);

        $(".inventory-item-checkbox").each((index, checkbox) => {
            const itemId = Number($(checkbox).data("item-id"));
            checkbox.checked = this.selectedItemIds.has(itemId);
        });

        this.bulkDeleteCount.text(this.selectedItemIds.size);
        this.bulkDeleteBtn.prop(
            "disabled",
            this.selectedItemIds.size === 0 || this.isBulkDeleting,
        );
    }

    bulkDeleteSelected() {
        const itemIds = Array.from(this.selectedItemIds);

        if (itemIds.length === 0 || this.isBulkDeleting) {
            return;
        }

        if (itemIds.length > 500) {
            alert("You can delete up to 500 inventory items at one time.");
            return;
        }

        const itemLabel = itemIds.length === 1 ? "item" : "items";
        if (
            !confirm(
                `Are you sure you want to delete ${itemIds.length} selected ${itemLabel}? This action cannot be undone.`,
            )
        ) {
            return;
        }

        this.isBulkDeleting = true;
        this.bulkDeleteBtn
            .find("i")
            .removeClass("fa-trash-alt")
            .addClass("fa-spinner fa-spin");
        this.syncSelectionUI();

        $.ajax({
            url: `${baseUrl}/bulk-delete`,
            type: "DELETE",
            data: { ids: itemIds },
            headers: { "X-CSRF-TOKEN": csrfToken },
            success: (response) => {
                if (response.success) {
                    this.resetSelection();
                    this.loadData();
                    this.loadStats();
                    alert(response.message || "Selected items deleted successfully!");
                }
            },
            error: (err) => {
                alert(
                    "Error: " +
                        (err.responseJSON?.message ||
                            "Failed to delete selected items"),
                );
            },
            complete: () => {
                this.isBulkDeleting = false;
                this.bulkDeleteBtn
                    .find("i")
                    .removeClass("fa-spinner fa-spin")
                    .addClass("fa-trash-alt");
                this.syncSelectionUI();
            },
        });
    }

    viewShippingRates(itemId) {
        $("#inventoryShippingRatesList").html('<div class="text-sm text-slate-500 font-semibold">Loading...</div>');
        $("#inventoryShippingRatesModal").removeClass("hidden");
        $.ajax({
            url: `${baseUrl}/${itemId}/shipping-rates`,
            type: "GET",
            headers: { "X-CSRF-TOKEN": csrfToken },
            success: (response) => {
                if (!response.success) {
                    $("#inventoryShippingRatesList").html('<div class="text-sm text-red-500 font-semibold">Failed to load rates.</div>');
                    return;
                }
                const rates = response.rates || [];
                if (!Array.isArray(rates) || rates.length === 0) {
                    $("#inventoryShippingRatesList").html('<div class="text-sm text-slate-500 font-semibold">No shipping rates saved for this item.</div>');
                    return;
                }
                const html = rates.map((rate) => `
                    <div class="p-3 border border-slate-200 rounded-xl bg-slate-50 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-slate-800">${rate.provider || "-"} - ${rate.servicelevel || "-"}</p>
                            <p class="text-xs text-slate-500">${rate.to_city || "-"}, ${rate.to_state || "-"} ${rate.to_zip || ""} (${rate.to_country || ""})</p>
                        </div>
                        <p class="text-sm font-black text-emerald-700">${rate.amount || "0.00"} ${rate.currency || ""}</p>
                    </div>
                `).join("");
                $("#inventoryShippingRatesList").html(html);
            },
            error: () => {
                $("#inventoryShippingRatesList").html('<div class="text-sm text-red-500 font-semibold">Failed to load rates.</div>');
            },
        });
    }

    printLabel(itemId) {
        $.ajax({
            url: `${baseUrl}/${itemId}`,
            type: "GET",
            headers: { "X-CSRF-TOKEN": csrfToken },
            success: (response) => {
                if (!response.success || !response.item) {
                    alert("Failed to load item details for printing.");
                    return;
                }

                const item = response.item;
                const barcode = this.formatDetailValue(item.barcode, "-");
                const barcodeImage = this.generateBarcodeImage(barcode);
                const technician = this.formatDetailValue(item.technician);
                const deviceType = this.formatDetailValue(item.device_type);
                const brandModel = this.formatDetailValue(
                    item.brand_category || [item.brand, item.model].filter(Boolean).join(" / "),
                );
                const category = this.formatLabelCategory(item);
                const price = this.formatLabelPrice(item.price);
                const quantity = this.formatDetailValue(item.quantity);
                const deviceSpecs = this.truncateLabelValue(item.device_specs, 48);
                const os = this.formatDetailValue(item.operating_system);
                const printedAt = this.formatDenverLabelTime();

                const printWindow = window.open("", "_blank", "width=420,height=320");
                if (!printWindow) {
                    alert("Popup blocked. Please allow popups for printing.");
                    return;
                }

                const html = `
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <title>Inventory Label</title>
                        <style>
                            * {
                                margin: 0;
                                padding: 0;
                                box-sizing: border-box;
                            }
                            @page {
                                size: 3in 2in;
                                margin: 0;
                            }
                            body {
                                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                                width: 3in;
                                height: 2in;
                                padding: 0;
                                background: white;
                                color: #111827;
                            }
                            .label-container {
                                width: 3in;
                                height: 2in;
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
                                gap: 0.05in;
                            }
                            .label-header h1 {
                                font-size: 9px;
                                color: #111827;
                                font-weight: 900;
                                letter-spacing: 0.4px;
                                line-height: 1;
                            }
                            .label-header p {
                                font-size: 6px;
                                color: #4b5563;
                                text-transform: uppercase;
                                letter-spacing: 0.4px;
                                line-height: 1;
                                white-space: nowrap;
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
                                max-height: 0.38in;
                                margin-bottom: 0.02in;
                            }
                            .barcode-number {
                                font-family: 'Courier New', monospace;
                                font-size: 7px;
                                font-weight: bold;
                                letter-spacing: 0.7px;
                                color: #111827;
                                line-height: 1;
                            }
                            .detail-grid {
                                display: grid;
                                grid-template-columns: 1fr 1fr;
                                gap: 0.025in 0.05in;
                                margin-top: 0.04in;
                                min-height: 0;
                                flex: 1;
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
                                letter-spacing: 0.25px;
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
                                html, body {
                                    width: 3in;
                                    height: 2in;
                                    background: white;
                                }
                            }
                        </style>
                    </head>
                    <body>
	                        <div class="label-container">
	                            <div class="label-header">
	                                <h1>INVENTORY LABEL</h1>
	                            </div>
	                            <div class="barcode-section">
	                                <img src="${barcodeImage}" alt="Barcode">
	                                <div class="barcode-number">${this.escapeHtml(barcode)}</div>
	                            </div>
	                            <div class="detail-grid">
	                                <div class="detail-item wide">
	                                    <span class="detail-label">Brand & Model</span>
	                                    <span class="detail-value">${this.escapeHtml(brandModel)}</span>
	                                </div>
	                                <div class="detail-item">
	                                    <span class="detail-label">Device</span>
	                                    <span class="detail-value">${this.escapeHtml(deviceType)}</span>
	                                </div>
	                                <div class="detail-item">
	                                    <span class="detail-label">OS</span>
	                                    <span class="detail-value">${this.escapeHtml(os)}</span>
	                                </div>
	                                <div class="detail-item wide">
	                                    <span class="detail-label">Specs</span>
	                                    <span class="detail-value">${this.escapeHtml(deviceSpecs)}</span>
	                                </div>
	                                <div class="detail-item wide">
	                                    <span class="detail-label">Category</span>
	                                    <span class="detail-value">${this.escapeHtml(category)}</span>
	                                </div>
	                                <div class="detail-item">
	                                    <span class="detail-label">Price</span>
	                                    <span class="detail-value">${this.escapeHtml(price)}</span>
	                                </div>
	                                <div class="detail-item">
	                                    <span class="detail-label">Qty</span>
	                                    <span class="detail-value">${this.escapeHtml(quantity)} Unt</span>
	                                </div>
	                                <div class="detail-item wide">
	                                    <span class="detail-label">Technician</span>
	                                    <span class="detail-value">${this.escapeHtml(technician)}</span>
	                                </div>
	                            </div>
	                            <div class="footer">
	                                ${this.escapeHtml(printedAt)}
                            </div>
                        </div>
                    </body>
                    </html>
                `;

                printWindow.document.open();
                printWindow.document.write(html);
                printWindow.document.close();

                setTimeout(() => {
                    printWindow.print();
                }, 300);
            },
            error: () => {
                alert("Failed to load item for print.");
            },
        });
    }

    generateBarcodeImage(barcode) {
        if (window.JsBarcode && barcode && barcode !== "-") {
            try {
                const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
                window.JsBarcode(svg, barcode, {
                    format: "CODE128",
                    displayValue: false,
                    width: 2,
                    height: 50,
                    margin: 0,
                });

                const svgMarkup = new XMLSerializer().serializeToString(svg);
                return `data:image/svg+xml;base64,${btoa(unescape(encodeURIComponent(svgMarkup)))}`;
            } catch (error) {
                console.error("Failed to generate barcode image", error);
            }
        }

        return `https://bwipjs-api.metafloor.com/?bcid=code128&text=${encodeURIComponent(barcode)}&scale=3&height=12&includetext=false`;
    }

    formatLabelCategory(item) {
        const parts = [item?.category, item?.sub_category, item?.sub_sub_category]
            .filter((value) => value !== null && value !== undefined && String(value).trim() !== "")
            .map((value) => String(value).trim());

        return parts.length > 0 ? parts.join(" > ") : "N/A";
    }

    formatLabelPrice(value) {
        const numericValue = parseFloat(value);
        return Number.isNaN(numericValue) ? "N/A" : `$${numericValue.toFixed(2)}`;
    }

    formatDenverLabelTime(date = new Date()) {
        return `Denver: ${date.toLocaleString("en-US", {
            timeZone: "America/Denver",
            month: "2-digit",
            day: "2-digit",
            year: "numeric",
            hour: "numeric",
            minute: "2-digit",
            hour12: true,
            timeZoneName: "short",
        })}`;
    }

    truncateLabelValue(value, maxLength) {
        const formattedValue = this.formatDetailValue(value);
        return formattedValue.length > maxLength ? formattedValue.substring(0, maxLength) : formattedValue;
    }

    escapeHtml(value) {
        return String(value ?? "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    renderRepairPhotoThumbnail(repairPhotos) {
        if (!repairPhotos || repairPhotos.length === 0) {
            return `<div class="w-8 h-8 rounded-lg bg-slate-50 border flex items-center justify-center"><i class="fas fa-hammer text-slate-300 text-[10px]"></i></div>`;
        }

        let photos = repairPhotos;
        if (typeof photos === 'string') {
            try {
                photos = JSON.parse(photos);
            } catch (e) {
                console.error('Failed to parse repair photos JSON:', e);
                photos = [];
            }
        }

        if (!Array.isArray(photos) || photos.length === 0) {
            return `<div class="w-8 h-8 rounded-lg bg-slate-50 border flex items-center justify-center"><i class="fas fa-hammer text-slate-300 text-[10px]"></i></div>`;
        }

        const photoUrls = photos.map(path => getImageUrl(path));
        const firstPhoto = photoUrls[0];
        const galleryJson = JSON.stringify(photoUrls);

        return `<img src="${firstPhoto}" class="w-8 h-8 rounded-lg object-cover mx-auto cursor-pointer hover:ring-2 hover:ring-orange-500 transition-all" onclick='openGallery(${galleryJson})' title="Click to view images" onerror="this.src='/images/placeholder.png'"><span class="text-[10px] font-black text-slate-400">${photos.length}</span>`;
    }

    getRepairPhotosArray(item) {
        return this.normalizePhotoArray(item?.repair_photos ?? []);
    }

    getAllPhotosArray(item) {
        const productPhotos = this.normalizePhotoArray(item?.photos_paths ?? []);
        const repairPhotos = this.normalizePhotoArray(item?.repair_photos ?? []);
        const combined = [...productPhotos, ...repairPhotos];
        return Array.from(new Set(combined));
    }

    normalizePhotoArray(photos) {
        if (typeof photos === "string") {
            try {
                photos = JSON.parse(photos);
            } catch (e) {
                photos = [];
            }
        }

        if (!Array.isArray(photos)) {
            return [];
        }

        return photos
            .filter((path) => typeof path === "string" && path.trim() !== "")
            .map((path) => path.trim());
    }

    makePhotoItemId(prefix = "photo") {
        return `${prefix}_${Date.now().toString(36)}_${Math.random().toString(36).slice(2, 8)}`;
    }

    openInventoryPhotoEditor(index) {
        if (!Number.isInteger(index) || index < 0 || index >= this.editPhotos.length) {
            return;
        }

        const photoItem = this.editPhotos[index];
        const isExisting = typeof photoItem === "string";
        const sourcePreview = isExisting
            ? getImageUrl(photoItem)
            : (photoItem.sourcePreview || photoItem.preview);

        if (!sourcePreview) {
            alert("Unable to load this image for editing.");
            return;
        }

        const image = new Image();
        image.onload = () => {
            this.photoEditor.index = index;
            this.photoEditor.image = image;
            this.photoEditor.sourcePreview = sourcePreview;
            this.setPhotoEditorControls(photoItem.adjustments || {
                brightness: 100,
                contrast: 100,
                saturation: 100,
                removeBg: 0,
            });
            $("#inventory-photo-editor-label").text(`Repair photo ${index + 1} of ${this.editPhotos.length}`);
            $("#inventoryPhotoEditorModal").removeClass("hidden");
            this.renderPhotoEditorCanvas();
        };
        image.onerror = () => {
            alert("Unable to load this image for editing.");
        };
        image.src = sourcePreview;
    }

    closeInventoryPhotoEditor() {
        $("#inventoryPhotoEditorModal").addClass("hidden");
        this.photoEditor.index = null;
        this.photoEditor.image = null;
        this.photoEditor.sourcePreview = null;
    }

    setPhotoEditorControls(adjustments) {
        const normalized = {
            brightness: this.numberOrDefault(adjustments?.brightness, 100),
            contrast: this.numberOrDefault(adjustments?.contrast, 100),
            saturation: this.numberOrDefault(adjustments?.saturation, 100),
            removeBg: this.numberOrDefault(adjustments?.removeBg, 0),
        };

        $("#photo-editor-brightness").val(normalized.brightness);
        $("#photo-editor-contrast").val(normalized.contrast);
        $("#photo-editor-saturation").val(normalized.saturation);
        $("#photo-editor-bg-remove").val(normalized.removeBg);
        this.photoEditor.adjustments = normalized;
        this.syncPhotoEditorLabels();
    }

    updatePhotoEditorFromControls() {
        this.photoEditor.adjustments = {
            brightness: this.numberOrDefault($("#photo-editor-brightness").val(), 100),
            contrast: this.numberOrDefault($("#photo-editor-contrast").val(), 100),
            saturation: this.numberOrDefault($("#photo-editor-saturation").val(), 100),
            removeBg: this.numberOrDefault($("#photo-editor-bg-remove").val(), 0),
        };
        this.syncPhotoEditorLabels();
        this.renderPhotoEditorCanvas();
    }

    syncPhotoEditorLabels() {
        const adjustments = this.photoEditor.adjustments;
        $("#photo-editor-brightness-value").text(`${adjustments.brightness}%`);
        $("#photo-editor-contrast-value").text(`${adjustments.contrast}%`);
        $("#photo-editor-saturation-value").text(`${adjustments.saturation}%`);
        $("#photo-editor-bg-remove-value").text(`${adjustments.removeBg}%`);
    }

    renderPhotoEditorCanvas() {
        const image = this.photoEditor.image;
        const canvas = document.getElementById("inventory-photo-editor-canvas");
        if (!image || !canvas) {
            return;
        }

        const maxDimension = 1600;
        const scale = Math.min(1, maxDimension / Math.max(image.naturalWidth, image.naturalHeight));
        canvas.width = Math.max(1, Math.round(image.naturalWidth * scale));
        canvas.height = Math.max(1, Math.round(image.naturalHeight * scale));

        const ctx = canvas.getContext("2d", { willReadFrequently: true });
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(image, 0, 0, canvas.width, canvas.height);

        try {
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            this.applyPhotoAdjustments(imageData, this.photoEditor.adjustments);
            ctx.putImageData(imageData, 0, 0);
        } catch (error) {
            console.error("Image editor failed to process canvas", error);
            alert("This image could not be edited in the browser.");
            this.closeInventoryPhotoEditor();
        }
    }

    applyPhotoAdjustments(imageData, adjustments) {
        const data = imageData.data;
        const brightness = this.numberOrDefault(adjustments.brightness, 100) / 100;
        const contrast = this.numberOrDefault(adjustments.contrast, 100) / 100;
        const saturation = this.numberOrDefault(adjustments.saturation, 100) / 100;

        for (let i = 0; i < data.length; i += 4) {
            let r = data[i];
            let g = data[i + 1];
            let b = data[i + 2];

            r = ((r - 128) * contrast + 128) * brightness;
            g = ((g - 128) * contrast + 128) * brightness;
            b = ((b - 128) * contrast + 128) * brightness;

            const gray = (0.2126 * r) + (0.7152 * g) + (0.0722 * b);
            r = gray + ((r - gray) * saturation);
            g = gray + ((g - gray) * saturation);
            b = gray + ((b - gray) * saturation);

            data[i] = this.clampColor(r);
            data[i + 1] = this.clampColor(g);
            data[i + 2] = this.clampColor(b);
        }
    }

    removeBackgroundWithService() {
        const canvas = document.getElementById("inventory-photo-editor-canvas");
        if (!this.photoEditor.image || !canvas || this.isRemovingBackground) {
            return;
        }

        this.renderPhotoEditorCanvas();
        this.setRemoveBgLoading(true);

        this.createRemoveBgUploadBlob(canvas)
            .then(async (blob) => {
                const formData = new FormData();
                formData.append("image", new File([blob], this.buildEditedRepairPhotoName("jpg"), { type: "image/jpeg" }));

                try {
                    const response = await fetch(`${baseUrl}/remove-bg`, {
                        method: "POST",
                        headers: { "X-CSRF-TOKEN": csrfToken },
                        body: formData,
                    });

                    if (!response.ok) {
                        let message = "Background removal failed.";
                        try {
                            const errorPayload = await response.json();
                            message = errorPayload.message || message;
                        } catch (error) {
                            message = response.statusText || message;
                        }
                        throw new Error(message);
                    }

                    const processedBlob = await response.blob();
                    const dataUrl = await this.blobToDataUrl(processedBlob);
                    const processedImage = await this.loadImageFromDataUrl(dataUrl);
                    this.photoEditor.image = processedImage;
                    this.photoEditor.sourcePreview = dataUrl;
                    this.renderPhotoEditorCanvas();
                } catch (error) {
                    alert(error.message || "Background removal failed.");
                } finally {
                    this.setRemoveBgLoading(false);
                }
            })
            .catch((error) => {
                this.setRemoveBgLoading(false);
                alert(error.message || "Unable to prepare this image for background removal.");
            });
    }

    setRemoveBgLoading(isLoading) {
        const button = $("#photo-editor-auto-bg");
        this.isRemovingBackground = isLoading;
        if (!button.length) {
            return;
        }

        if (isLoading) {
            button
                .prop("disabled", true)
                .addClass("opacity-70 cursor-not-allowed")
                .html('<i class="fas fa-spinner fa-spin mr-1"></i> Removing');
        } else {
            button
                .prop("disabled", false)
                .removeClass("opacity-70 cursor-not-allowed")
                .html('<i class="fas fa-cut mr-1"></i> AI Remove BG');
        }
    }

    createRemoveBgUploadBlob(sourceCanvas) {
        return new Promise((resolve, reject) => {
            const maxDimension = 900;
            const scale = Math.min(1, maxDimension / Math.max(sourceCanvas.width, sourceCanvas.height));
            const width = Math.max(1, Math.round(sourceCanvas.width * scale));
            const height = Math.max(1, Math.round(sourceCanvas.height * scale));
            const uploadCanvas = document.createElement("canvas");
            uploadCanvas.width = width;
            uploadCanvas.height = height;

            const ctx = uploadCanvas.getContext("2d");
            ctx.fillStyle = "#ffffff";
            ctx.fillRect(0, 0, width, height);
            ctx.drawImage(sourceCanvas, 0, 0, width, height);

            uploadCanvas.toBlob((blob) => {
                if (blob) {
                    resolve(blob);
                    return;
                }

                reject(new Error("Unable to prepare this image for background removal."));
            }, "image/jpeg", 0.86);
        });
    }

    blobToDataUrl(blob) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result);
            reader.onerror = () => reject(new Error("Unable to read processed image."));
            reader.readAsDataURL(blob);
        });
    }

    loadImageFromDataUrl(dataUrl) {
        return new Promise((resolve, reject) => {
            const image = new Image();
            image.onload = () => resolve(image);
            image.onerror = () => reject(new Error("Unable to load processed image."));
            image.src = dataUrl;
        });
    }

    clampColor(value) {
        return Math.max(0, Math.min(255, Math.round(value)));
    }

    numberOrDefault(value, fallback) {
        const parsed = parseInt(value, 10);
        return Number.isFinite(parsed) ? parsed : fallback;
    }

    applyPhotoEditorChanges() {
        const index = this.photoEditor.index;
        const canvas = document.getElementById("inventory-photo-editor-canvas");
        if (!Number.isInteger(index) || !canvas) {
            return;
        }

        this.renderPhotoEditorCanvas();
        $("#photo-editor-apply").prop("disabled", true).text("Applying...");

        canvas.toBlob((blob) => {
            $("#photo-editor-apply").prop("disabled", false).text("Apply to Photo");
            if (!blob) {
                alert("Unable to apply changes to this photo.");
                return;
            }

            const previousPhoto = this.editPhotos[index];
            const originalPath = typeof previousPhoto === "string"
                ? previousPhoto
                : (previousPhoto.originalPath || null);
            const sourcePreview = this.photoEditor.sourcePreview || (
                typeof previousPhoto === "string" ? getImageUrl(previousPhoto) : previousPhoto.sourcePreview
            );
            const fileName = this.buildEditedRepairPhotoName();
            const editedFile = new File([blob], fileName, { type: "image/png" });

            this.editPhotos[index] = {
                id: this.makePhotoItemId("repair_edited"),
                file: editedFile,
                preview: canvas.toDataURL("image/png"),
                sourcePreview,
                originalPath,
                adjustments: { ...this.photoEditor.adjustments },
                edited: true,
            };

            this.closeInventoryPhotoEditor();
            if (typeof this.renderEditRepairPhotos === "function") {
                this.renderEditRepairPhotos();
            }
        }, "image/png");
    }

    buildEditedRepairPhotoName(extension = "png") {
        const barcode = String(this.selectedItem?.barcode || "inventory")
            .replace(/[^a-z0-9_-]+/gi, "-")
            .replace(/^-+|-+$/g, "")
            .toLowerCase() || "inventory";

        return `${barcode}-repair-edited-${Date.now()}.${extension}`;
    }

    formatDetailValue(value, fallback = "N/A") {
        if (value === null || value === undefined || value === "") {
            return fallback;
        }

        return String(value);
    }

    formatCurrencyValue(value) {
        const numericValue = parseFloat(value);
        if (Number.isNaN(numericValue)) {
            return "N/A";
        }

        return `$${numericValue.toFixed(2)}`;
    }

    renderDetailCard(label, value) {
        return `
            <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">${label}</p>
                <p class="text-sm font-bold text-slate-800 break-words">${this.formatDetailValue(value)}</p>
            </div>
        `;
    }

    getBrandModelDisplay(item) {
        return item?.brand_category || item?.brand || "";
    }

    renderInventoryIntakeDetails(item) {
        const fields = [
            ["Technician", item?.technician],
            ["Customer Phone", item?.customer_phone],
            ["Customer Email", item?.customer_email],
            ["Device Type", item?.device_type],
            ["Brand & Model Number", this.getBrandModelDisplay(item)],
            ["Category", item?.category],
            ["Sub Category", item?.sub_category],
            ["Sub Sub Category", item?.sub_sub_category],
            ["Price", this.formatCurrencyValue(item?.price ?? item?.estimated_value)],
            ["Quantity", item?.quantity],
            ["Remaining", `${Math.max(0, (parseInt(item?.quantity, 10) || 0) - (parseInt(item?.reuse_quantity, 10) || 0))} items`],
            ["Device Specs", item?.device_specs],
            ["Operating System", item?.operating_system],
            ["Put Away Location", item?.put_away_location],
        ];

        return fields
            .map(([label, value]) => this.renderDetailCard(label, value))
            .join("");
    }

    getParcelRate(item) {
        const rates = item?.shippingRates || item?.shipping_rates || [];
        let rate = (Array.isArray(rates) && rates.length > 0) ? { ...rates[0] } : null;
        if (!rate && item?.weight) {
            rate = {
                parcel_length: null,
                parcel_width: null,
                parcel_height: null,
                parcel_weight: item.weight,
            };
        } else if (rate && (rate.parcel_weight === null || rate.parcel_weight === undefined) && item?.weight) {
            rate.parcel_weight = item.weight;
        }
        return rate;
    }

    renderParcelDetails(item) {
        const rate = this.getParcelRate(item);
        const fields = [
            ["Length", rate?.parcel_length],
            ["Width", rate?.parcel_width],
            ["Height", rate?.parcel_height],
            ["Weight", rate?.parcel_weight],
        ];

        const hasAnyValue = fields.some(([_, val]) => val !== null && val !== undefined && val !== '');

        let html = fields
            .map(([label, value]) => `
                <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">${label}</p>
                    <p class="text-sm font-bold text-slate-800">${this.formatDetailValue(value)}${value !== null && value !== undefined && value !== '' ? label === 'Weight' ? ' lb' : ' in' : ''}</p>
                </div>
            `)
            .join("");

        if (!hasAnyValue) {
            html += `
                <div class="col-span-2 md:col-span-4 text-xs font-medium text-slate-400 mt-1 flex items-center gap-1.5 bg-blue-50/60 border border-blue-100 rounded-xl px-3 py-2">
                    <i class="fas fa-info-circle text-blue-500"></i> Dimensions not specified on eBay listing. Click <strong>Edit Item</strong> to manually set parcel dimensions & weight.
                </div>
            `;
        }

        return html;
    }

    renderPagination(response) {
        const { current_page, last_page } = response;

        if (!last_page || last_page <= 1) {
            this.paginationContainer.empty().hide();
            return;
        }

        this.paginationContainer.show();
        let html =
            '<div class="flex justify-between items-center mt-4"><div></div><div class="pagination flex gap-2">';

        if (current_page > 1) {
            html += `<a href="#" data-page="1" class="px-3 py-2 border rounded hover:bg-slate-100">First</a>`;
            html += `<a href="#" data-page="${current_page - 1}" class="px-3 py-2 border rounded hover:bg-slate-100">Previous</a>`;
        }

        for (
            let i = Math.max(1, current_page - 2);
            i <= Math.min(last_page, current_page + 2);
            i++
        ) {
            html += `<a href="#" data-page="${i}" class="px-3 py-2 border rounded ${i === current_page ? "bg-blue-500 text-white" : "hover:bg-slate-100"}">${i}</a>`;
        }

        if (current_page < last_page) {
            html += `<a href="#" data-page="${current_page + 1}" class="px-3 py-2 border rounded hover:bg-slate-100">Next</a>`;
            html += `<a href="#" data-page="${last_page}" class="px-3 py-2 border rounded hover:bg-slate-100">Last</a>`;
        }

        html += "</div><div></div></div>";
        this.paginationContainer.html(html);
    }

    showItemDetails(itemId) {
        $.ajax({
            url: `${baseUrl}/${itemId}`,
            type: "GET",
            headers: { "X-CSRF-TOKEN": csrfToken },
            success: (response) => {
                if (response.success) {
                    this.selectedItem = response.item;
                    this.populateDetailsModal(response.item);
                    $("#itemDetailsModal").removeClass("hidden");
                }
            },
        });
    }

    async syncIndividualItem(itemId) {
        // Target the specific button that was clicked
        const btn = $(event.currentTarget);
        const icon = btn.find(".fa-sync-alt");

        icon.addClass("fa-spin");
        btn.prop("disabled", true).addClass("opacity-50");

        try {
            const response = await fetch(`${baseUrl}/${itemId}/sync`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Content-Type": "application/json",
                },
            });

            const data = await response.json();
            if (data.success) {
                this.loadData(); // Refresh table to show new sync date/status

                // Show sync success modal with product link
                const storeProductUrl = window.location.origin + "/product/" + itemId;
                $("#sync-store-link").text(storeProductUrl).attr("href", storeProductUrl);

                // Reset Copy button status
                const copyBtn = $("#copy-sync-link-btn");
                copyBtn.html('<i class="fas fa-copy"></i> Copy').removeClass("bg-emerald-650 bg-emerald-600").addClass("bg-blue-600");

                $("#syncSuccessModal").removeClass("hidden");

                // Set up 10-second auto-close timer with countdown
                let timeLeft = 10;
                $("#sync-timer-counter").text(timeLeft);

                if (window.syncModalTimer) {
                    clearInterval(window.syncModalTimer);
                }

                window.syncModalTimer = setInterval(() => {
                    timeLeft--;
                    $("#sync-timer-counter").text(timeLeft);
                    if (timeLeft <= 0) {
                        clearInterval(window.syncModalTimer);
                        $("#syncSuccessModal").addClass("hidden");
                    }
                }, 1000);
            } else {
                alert("Sync Error: " + data.message);
            }
        } catch (error) {
            console.error("Sync failed", error);
        } finally {
            setTimeout(() => {
                icon.removeClass("fa-spin");
                btn.prop("disabled", false).removeClass("opacity-50");
            }, 800);
        }
    }

    populateDetailsModal(item) {
        $("#detail-category").text(item.category);
        $("#detail-name").text(item.name);
        $("#detail-brand-model").text(this.formatDetailValue(this.getBrandModelDisplay(item)));
        $("#detail-status").text(item.status || "Pending");
        $("#detail-intake-details").html(this.renderInventoryIntakeDetails(item));
        $("#detail-parcel-details").html(this.renderParcelDetails(item));

        const photos = this.getAllPhotosArray(item);
        const photoGallery = photos.length > 0
            ? photos.map((path) => `
                <div class="aspect-square rounded-[1.5rem] overflow-hidden border border-slate-50 group shadow-sm">
                    <img src="${getImageUrl(path)}" class="w-full h-full object-cover group-hover:scale-110 transition-all duration-700" onerror="this.src='/images/placeholder.png'">
                </div>
            `).join("")
            : `
                <div class="col-span-2 aspect-video bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-100 flex flex-col items-center justify-center text-slate-300">
                    <i class="fas fa-camera text-4xl mb-3"></i>
                    <p class="text-[10px] font-black uppercase tracking-widest">No Media Uploaded</p>
                </div>
            `;
        $("#detail-repair-photo-count").text(`(${photos.length})`);
        $("#detail-repair-photos").html(photoGallery);

        if (item.notes) {
            $("#detail-notes").text(item.notes);
            $("#detail-notes-section").removeClass("hidden");
        } else {
            $("#detail-notes-section").addClass("hidden");
        }
    }

    editItem(itemId) {
        closeItemDetailsModal();

        $.ajax({
            url: `${baseUrl}/${itemId}/edit`,
            type: "GET",
            headers: { "X-CSRF-TOKEN": csrfToken },
            success: (response) => {
                if (response.success) {
                    this.selectedItem = response.item;
                    this.populateEditModal(
                        response.item,
                        response.pallets_list,
                    );
                    $("#editItemModal").removeClass("hidden");
                }
            },
        });
    }

    populateEditModal(item, pallets) {
        $("#edit-barcode").text(item.barcode);
        $("#edit-name").val(item.name || "");
        $("#edit-name-label").text(item.name || "");
        $("#edit-serial").val(item.serial_number || "");
        this.selectedItem.reuse_quantity = parseInt(item.reuse_quantity, 10) || 0;
        $("#edit-technician").val(item.technician || "");
        $("#edit-customer-phone").val(item.customer_phone || "");
        $("#edit-customer-email").val(item.customer_email || "");
        $("#edit-device-type").val(item.device_type || "");
        $("#edit-price").val(parseFloat(item.price || item.estimated_value || 0).toFixed(2));
        $("#edit-status").val(item.status || "");
        $("#edit-brand-category").val(item.brand_category || item.brand || "");
        $("#edit-device-specs").val(item.device_specs || "");
        $("#edit-operating-system").val(item.operating_system || "");
        $("#edit-put-away-location").val(item.put_away_location || "");
        $("#edit-quantity").val(item.quantity || 0);
        const remainingQty = Math.max(0, (parseInt(item.quantity, 10) || 0) - (parseInt(item.reuse_quantity, 10) || 0));
        $("#edit-remaining-display").val(`${remainingQty} items`);
        $("#edit-remaining-items").text(`${remainingQty} items`);
        $("#edit-notes").val(item.notes || "");
        $("#edit-parcel-length").val(this.getParcelRate(item)?.parcel_length || "");
        $("#edit-parcel-width").val(this.getParcelRate(item)?.parcel_width || "");
        $("#edit-parcel-height").val(this.getParcelRate(item)?.parcel_height || "");
        $("#edit-parcel-weight").val(this.getParcelRate(item)?.parcel_weight || "");

        if (window.categoryHierarchyManager) {
            window.categoryHierarchyManager.initialize();
            if (item.category_id) {
                window.categoryHierarchyManager.whenReady().then(() => {
                    window.categoryHierarchyManager.selectPresetCategories(
                        item.category_id,
                        item.sub_category_id,
                        item.sub_sub_category_id,
                    );
                });
            }
        }

        // Populate pallet select
        let palletHtml = '<option value="">-- No Pallet Assigned --</option>';
        pallets.forEach((pallet) => {
            palletHtml += `<option value="${pallet.barcode_number}" ${item.pallet_number === pallet.barcode_number ? "selected" : ""}>${pallet.barcode_number}</option>`;
        });
        $("#edit-pallet-number").html(palletHtml);

        // Edit popup should show all photos (product photos from eBay/intake & repair photos)
        const photos = this.getAllPhotosArray(item);
        // Initialize editPhotos with existing photo paths (strings)
        this.editPhotos = photos.slice();

        // Render function for edit photos area
        const renderEditPhotos = () => {
            const $container = $("#edit-photos-container");
            $container.empty();

            // Update count
            $("#edit-repair-photo-count").text(`(${this.editPhotos.length})`);

            // Render each photo (existing path or new file object)
            this.editPhotos.forEach((it, idx) => {
                if (typeof it === 'string') {
                    const path = it;
                    const escapedPath = this.escapeHtml(path);
                    const $wrap = $(
                        `<div class="relative w-24 h-24 group rounded-2xl overflow-hidden border border-slate-200 shadow-sm editable-photo" data-index="${idx}" data-existing="1" data-photo-path="${escapedPath}" draggable="true">
                            <img src="${getImageUrl(path)}" class="w-full h-full object-cover" onerror="this.src='/images/placeholder.png'">
                            <div class="absolute inset-x-1 bottom-1 grid grid-cols-2 gap-1 opacity-0 group-hover:opacity-100 transition-all">
                                <button type="button" data-index="${idx}" class="edit-photo h-8 rounded-lg bg-white/90 text-blue-600 shadow-sm hover:bg-blue-600 hover:text-white" title="Edit Photo">
                                    <i class="fas fa-sliders-h text-xs"></i>
                                </button>
                                <button type="button" data-photo-path="${escapedPath}" class="delete-photo h-8 rounded-lg bg-white/90 text-red-500 shadow-sm hover:bg-red-600 hover:text-white" title="Delete Photo">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </div>
                        </div>`
                    );
                    $container.append($wrap);
                } else if (it && it.file) {
                    const preview = it.preview || '';
                    const id = it.id || idx;
                    const editedBadge = it.edited
                        ? `<span class="absolute left-2 top-2 px-1.5 py-0.5 rounded bg-blue-600 text-white text-[8px] font-black uppercase tracking-wider">Edited</span>`
                        : "";
                    const $wrap = $(
                        `<div class="relative w-24 h-24 group rounded-2xl overflow-hidden border border-slate-200 shadow-sm editable-photo" data-index="${idx}" data-id="${id}" draggable="true">
                            <img src="${preview}" class="w-full h-full object-cover" onerror="this.src='/images/placeholder.png'">
                            ${editedBadge}
                            <div class="absolute inset-x-1 bottom-1 grid grid-cols-2 gap-1 opacity-0 group-hover:opacity-100 transition-all">
                                <button type="button" data-index="${idx}" class="edit-photo h-8 rounded-lg bg-white/90 text-blue-600 shadow-sm hover:bg-blue-600 hover:text-white" title="Edit Photo">
                                    <i class="fas fa-sliders-h text-xs"></i>
                                </button>
                                <button type="button" data-id="${id}" data-index="${idx}" class="delete-new-photo h-8 rounded-lg bg-white/90 text-red-500 shadow-sm hover:bg-red-600 hover:text-white" title="Delete Photo">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </div>
                        </div>`
                    );
                    $container.append($wrap);
                }
            });

            // Add photo input
            const $add = $(
                `<label class="w-24 h-24 cursor-pointer bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl flex flex-col items-center justify-center hover:bg-blue-50 hover:border-blue-300 transition-all group">
                    <i class="fas fa-plus text-slate-300 group-hover:text-blue-500 mb-1"></i>
                    <span class="text-[8px] font-black text-slate-400 group-hover:text-blue-600 uppercase tracking-tighter">Add Photo</span>
                    <input type="file" id="new-photos" multiple accept="image/*" class="hidden">
                </label>`
            );
            $container.append($add);

            // Attach drag handlers (delegated)
            $container.find('.editable-photo').each((i, el) => {
                const $el = $(el);
                el.addEventListener('dragstart', (e) => {
                    e.dataTransfer.setData('text/plain', String($el.data('index')));
                    setTimeout(() => $el.css('opacity', '0.5'), 0);
                });
                el.addEventListener('dragend', () => $el.css('opacity', ''));
                el.addEventListener('dragover', (e) => e.preventDefault());
                el.addEventListener('drop', (e) => {
                    e.preventDefault();
                    const fromIndex = parseInt(e.dataTransfer.getData('text/plain'), 10);
                    const toIndex = parseInt($el.attr('data-index'), 10);
                    if (!Number.isFinite(fromIndex) || fromIndex === toIndex) return;
                    const moved = this.editPhotos.splice(fromIndex, 1)[0];
                    this.editPhotos.splice(toIndex, 0, moved);
                    renderEditPhotos();
                });
            });

            // Edit repair photo handler
            $container.find('.edit-photo').off('click').on('click', (e) => {
                e.preventDefault();
                this.openInventoryPhotoEditor(parseInt($(e.currentTarget).data('index'), 10));
            });

            // Delete new photo handler
            $container.find('.delete-new-photo').off('click').on('click', (e) => {
                e.preventDefault();
                const clickedIndex = parseInt($(e.currentTarget).data('index'), 10);
                const clickedItem = Number.isInteger(clickedIndex) ? this.editPhotos[clickedIndex] : null;
                if (clickedItem?.originalPath) {
                    if (confirm("Delete this repair photo?")) {
                        this.deletePhoto(this.selectedItem.id, clickedItem.originalPath);
                    }
                    return;
                }

                const id = $(e.currentTarget).data('id');
                const idx = this.editPhotos.findIndex(it => it && it.id === id);
                if (idx > -1) this.editPhotos.splice(idx, 1);
                renderEditPhotos();
            });

            // Existing photo delete handler (calls server)
            $container.find('.delete-photo').off('click').on('click', (e) => {
                e.preventDefault();
                const photoPath = $(e.currentTarget).data('photo-path');
                this.deletePhoto(this.selectedItem.id, photoPath);
            });

            // New photos input change handler
            $container.find('#new-photos').off('change').on('change', (e) => {
                const files = Array.from(e.target.files || []);
                files.forEach((file) => {
                    const reader = new FileReader();
                    const id = this.makePhotoItemId('repair_new');
                    const item = { id, file, preview: null, sourcePreview: null, originalPath: null, edited: false };
                    this.editPhotos.push(item);
                    reader.onload = (ev) => {
                        item.preview = ev.target.result;
                        item.sourcePreview = ev.target.result;
                        renderEditPhotos();
                    };
                    reader.readAsDataURL(file);
                });
                // clear input
                e.target.value = '';
            });
        };

        this.renderEditRepairPhotos = renderEditPhotos;

        // Initial render
        renderEditPhotos();

        this.updateQuantityCalculation();
        this.updateWeightCalculation();
    }

    updateQuantityCalculation() {
        const quantity = parseInt($("#edit-quantity").val()) || 0;
        const reuseQuantity = parseInt(this.selectedItem?.reuse_quantity, 10) || 0;
        const remaining = Math.max(0, quantity - reuseQuantity);
        $("#edit-remaining-display").val(`${remaining} items`);
        $("#edit-remaining-items").text(`${remaining} items`);
    }

    updateWeightCalculation() {
        return;
    }

    handleUpdateItem() {
        const itemId = this.selectedItem.id;

        const formData = new FormData();
        formData.append("name", $("#edit-name").val());
        formData.append("serial_number", $("#edit-serial").val());
        formData.append("brand_category", $("#edit-brand-category").val());
        formData.append("technician", $("#edit-technician").val());
        formData.append("customer_phone", $("#edit-customer-phone").val());
        formData.append("customer_email", $("#edit-customer-email").val());
        formData.append("device_type", $("#edit-device-type").val());
        formData.append("price", $("#edit-price").val());
        formData.append("status", $("#edit-status").val());
        formData.append("category", $("#inv-category").val());
        formData.append("sub_category", $("#inv-sub_category").val());
        formData.append("sub_sub_category", $("#inv-sub_sub_category").val());
        formData.append("category_id", $("#inv-category-id").val());
        formData.append("sub_category_id", $("#inv-subcategory-id").val());
        formData.append("sub_sub_category_id", $("#inv-subsubcategory-id").val());
        formData.append("device_specs", $("#edit-device-specs").val());
        formData.append("operating_system", $("#edit-operating-system").val());
        formData.append("put_away_location", $("#edit-put-away-location").val());
        formData.append("quantity", $("#edit-quantity").val());
        formData.append("pallet_number", $("#edit-pallet-number").val());
        formData.append("notes", $("#edit-notes").val());
        formData.append("parcel_length", $("#edit-parcel-length").val());
        formData.append("parcel_width", $("#edit-parcel-width").val());
        formData.append("parcel_height", $("#edit-parcel-height").val());
        formData.append("parcel_weight", $("#edit-parcel-weight").val());
        formData.append("distance_unit", "in");
        formData.append("mass_unit", "lb");

        // Append existing repair photos order and new repair photo files
        const removedRepairPhotoPaths = new Set();
        let repairUploadIndex = 0;
        if (Array.isArray(this.editPhotos)) {
            this.editPhotos.forEach((it) => {
                if (typeof it === 'string') {
                    formData.append('existing_repair_photos_order[]', it);
                    formData.append('repair_photo_order[]', `existing:${it}`);
                } else if (it && it.file) {
                    formData.append('repair_photos[]', it.file);
                    formData.append('repair_photo_order[]', `upload:${repairUploadIndex}`);
                    repairUploadIndex += 1;
                    if (it.originalPath) {
                        removedRepairPhotoPaths.add(it.originalPath);
                    }
                }
            });
        }

        removedRepairPhotoPaths.forEach((photoPath) => {
            formData.append('removed_existing_repair_photos[]', photoPath);
        });

        $("#edit-submit-spinner").removeClass("hidden");
        $("#edit-submit-text").text("Saving...");

        $.ajax({
            url: `${baseUrl}/${itemId}`,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: { "X-CSRF-TOKEN": csrfToken },
            success: (response) => {
                if (response.success) {
                    $("#edit-submit-spinner").addClass("hidden");
                    $("#edit-submit-text").text("Update Record");
                    closeEditItemModal();
                    this.loadData();
                    this.loadStats();
                    alert("Item updated successfully!");
                }
            },
            error: (err) => {
                $("#edit-submit-spinner").addClass("hidden");
                $("#edit-submit-text").text("Update Record");
                alert(
                    "Error: " +
                        (err.responseJSON?.message || "Failed to update item"),
                );
            },
        });
    }

    deletePhoto(itemId, photoPath) {
        $.ajax({
            url: `${baseUrl}/${itemId}/delete-photo`,
            type: "POST",
            data: { photo_path: photoPath },
            headers: { "X-CSRF-TOKEN": csrfToken },
            success: (response) => {
                if (response.success) {
                    this.editItem(itemId);
                }
            },
        });
    }

    deleteItem(itemId) {
        $.ajax({
            url: `${baseUrl}/${itemId}`,
            type: "DELETE",
            headers: { "X-CSRF-TOKEN": csrfToken },
            success: (response) => {
                if (response.success) {
                    this.selectedItemIds.delete(Number(itemId));
                    this.syncSelectionUI();
                    this.loadData();
                    this.loadStats();
                    alert("Item deleted successfully!");
                }
            },
            error: (err) => {
                alert(
                    "Error: " +
                        (err.responseJSON?.message || "Failed to delete item"),
                );
            },
        });
    }

    updateStatus(itemId, newStatus) {
        $.ajax({
            url: `${baseUrl}/${itemId}/status`,
            type: "POST",
            data: { status: newStatus },
            headers: { "X-CSRF-TOKEN": csrfToken },
            success: (response) => {
                if (response.success) {
                    this.loadStats();
                }
            },
        });
    }

    exportCsv() {
        const params = new URLSearchParams({
            search: this.filters.search,
            statusFilter: this.filters.statusFilter,
            intakeFilter: this.filters.intakeFilter,
            categoryFilter: this.filters.categoryFilter,
            subCategoryFilter: this.filters.subCategoryFilter,
            subSubCategoryFilter: this.filters.subSubCategoryFilter,
            palletFilter: this.filters.palletFilter,
        });

        window.location.href = `${baseUrl}/export-csv?${params.toString()}`;
    }

    openSellModal(itemId, itemName, itemPrice) {
        // Load item details and populate the sell modal
        $.ajax({
            url: `${baseUrl}/${itemId}`,
            type: "GET",
            headers: { "X-CSRF-TOKEN": csrfToken },
            success: (response) => {
                if (response.success) {
                    const item = response.item;
                    const availableQuantity = Math.max(
                        0,
                        (parseInt(item.quantity, 10) || 0) - (parseInt(item.reuse_quantity, 10) || 0),
                    );
                    if (availableQuantity <= 0) {
                        alert("This item is out of stock and cannot be sold.");
                        return;
                    }
                    const displayCategory = [item.category, item.sub_category, item.sub_sub_category]
                        .filter(Boolean)
                        .join(' / ') || 'N/A';
                    const displayBrandModel = item.brand_category || [item.brand, item.model].filter(Boolean).join(' ').trim() || 'N/A';

                    $("#sell-item-id").val(item.id);
                    $("#sell-item-name").text(item.name);
                    $("#sell-barcode").text(item.barcode || 'N/A');
                    $("#sell-status").text(item.status || 'N/A');
                    $("#sell-category").text(displayCategory);
                    $("#sell-brand-model").text(displayBrandModel);
                    $("#sell-device-type").text(item.device_type || 'N/A');
                    $("#sell-location").text(item.put_away_location || 'N/A');
                    $("#sell-serial-number").text(item.serial_number || 'N/A');
                    $("#sell-device-specs").text(item.device_specs || 'N/A');
                    $("#sell-operating-system").text(item.operating_system || 'N/A');
                    $("#sell-technician").text(item.technician || 'N/A');
                    $("#sell-inventory-contact").text([item.customer_email, item.customer_phone].filter(Boolean).join(' / ') || 'N/A');
                    $("#sell-lot-pallet").text([item.lot_info, item.pallet_number].filter(Boolean).join(' / ') || 'N/A');
                    const parcel = [item.parcel_length, item.parcel_width, item.parcel_height, item.parcel_weight]
                        .filter(Boolean)
                        .join(' x ');
                    $("#sell-parcel-dims").text(parcel ? parcel + ' (in / lb)' : 'N/A');
                    $("#sell-notes").text(item.notes || 'No notes provided.');
                    $("#sell-available-quantity").text(availableQuantity);
                    $("#sell-item-price").data("unit-price", parseFloat(item.price || 0) || 0);
                    $("#sell-unit-price").text(`Unit Price: $${(parseFloat(item.price || 0) || 0).toFixed(2)}`);
                    $("#sell-quantity").val(1);
                    $("#sell-quantity").attr("max", availableQuantity || 1);
                    $("#sell-quantity-max").val(availableQuantity || 1);
                    $("#sell-payment-method").val("cash");
                    $("#sell-order-status").val(typeof defaultDirectSellOrderStatus !== "undefined" ? defaultDirectSellOrderStatus : "in_store_purchased");
                    $("#sell-customer-name").val("");
                    $("#sell-customer-email").val("");
                    $("#sell-customer-phone").val("");
                    $("#sell-customer-company").val("");
                    this.updateSellQuantityRemaining();
                    $("#directSellModal").removeClass("hidden");
                }
            },
            error: (error) => {
                console.error("Failed to load item", error);
                alert("Failed to load item details");
            }
        });
    }

    updateSellQuantityRemaining() {
        const maxQuantity = parseInt($("#sell-quantity-max").val(), 10) || 0;
        const enteredQuantity = parseInt($("#sell-quantity").val(), 10) || 0;
        const unitPrice = parseFloat($("#sell-item-price").data("unit-price")) || 0;
        const remainingQuantity = Math.max(0, maxQuantity - enteredQuantity);
        const subtotal = unitPrice * Math.max(enteredQuantity, 0);
        const tax = Math.round(subtotal * 0.08 * 100) / 100;
        const totalCharge = subtotal + tax;
        const isOutOfStock = maxQuantity <= 0;
        const isQuantityInvalid = enteredQuantity < 1 || enteredQuantity > maxQuantity;

        $("#sell-item-price").val(subtotal.toFixed(2));
        $("#sell-tax-amount").text(`$${tax.toFixed(2)}`);
        $("#sell-total-charge").text(`$${totalCharge.toFixed(2)}`);
        $("#sell-quantity-remaining").text(`Remaining Quantity: ${remainingQuantity}`);

        const warning = $("#sell-stock-warning");
        if (isOutOfStock) {
            warning.removeClass("hidden").text("Not enough stock available.");
        } else if (enteredQuantity < 1) {
            warning.removeClass("hidden").text("Quantity must be at least 1.");
        } else if (enteredQuantity > maxQuantity) {
            warning.removeClass("hidden").text(`Not enough stock. Only ${maxQuantity} available.`);
        } else {
            warning.addClass("hidden").text("");
        }

        if (!this.isProcessingSell) {
            $("#confirm-sell-btn").prop("disabled", isOutOfStock || isQuantityInvalid);
        }
    }

    setSellButtonLoading(isLoading) {
        const button = $("#confirm-sell-btn");
        this.isProcessingSell = isLoading;

        if (!button.length) {
            return;
        }

        if (isLoading) {
            button.prop("disabled", true)
                .addClass("opacity-70 cursor-not-allowed")
                .html('<i class="fas fa-spinner fa-spin mr-2"></i> Processing...');
        } else {
            button.prop("disabled", false)
                .removeClass("opacity-70 cursor-not-allowed")
                .html('<i class="fas fa-check mr-2"></i> Confirm Sale');
            this.updateSellQuantityRemaining();
        }
    }

    processSell() {
        const itemId = $("#sell-item-id").val();
        const quantity = parseInt($("#sell-quantity").val()) || 1;
        const customerName = $("#sell-customer-name").val().trim();
        const customerEmail = $("#sell-customer-email").val().trim();
        const customerPhone = $("#sell-customer-phone").val().trim();
        const paymentMethod = $("#sell-payment-method").val() || "cash";
        const orderStatus = $("#sell-order-status").val() || (typeof defaultDirectSellOrderStatus !== "undefined" ? defaultDirectSellOrderStatus : "in_store_purchased");
        const maxQuantity = parseInt($("#sell-quantity-max").val(), 10) || 0;

        if (!customerName) {
            alert("Please enter customer name");
            return;
        }

        if (!customerEmail && !customerPhone) {
            alert("Please enter either email or phone number");
            return;
        }

        if (quantity < 1) {
            alert("Invalid quantity");
            return;
        }

        if (maxQuantity <= 0 || quantity > maxQuantity) {
            alert("Not enough stock available.");
            return;
        }

        this.setSellButtonLoading(true);

        const ajaxUrl = `${baseUrl}/${itemId}/direct-sell`;
        console.log("Sale Request URL:", ajaxUrl);
        console.log("Sale Request Data:", {
            quantity: quantity,
            customer_name: customerName,
            customer_email: customerEmail,
            customer_phone: customerPhone,
            order_status: orderStatus,
            sale_type: "store_pickup"
        });

        $.ajax({
            url: ajaxUrl,
            type: "POST",
            headers: { "X-CSRF-TOKEN": csrfToken },
            data: {
                quantity: quantity,
                customer_name: customerName,
                customer_email: customerEmail,
                customer_phone: customerPhone,
                payment_method: paymentMethod,
                order_status: orderStatus,
                sale_type: "store_pickup"
            },
            success: (response) => {
                console.log("Sale Response:", response);
                if (response.success) {
                    alert("Item sold successfully!");
                    this.setSellButtonLoading(false);
                    $("#directSellModal").addClass("hidden");
                    this.loadData();
                } else {
                    this.setSellButtonLoading(false);
                    alert("Error: " + (response.message || "Failed to process sale"));
                }
            },
            error: (error) => {
                this.setSellButtonLoading(false);
                console.error("Sale failed - Status:", error.status);
                console.error("Sale failed - Response:", error.responseText);
                console.error("Full error:", error);
                alert("Failed to process sale. Check console for details.");
            }
        });
    }
}

// Modal close functions
function closeItemDetailsModal() {
    $("#itemDetailsModal").addClass("hidden");
}

function closeSyncSuccessModal() {
    $("#syncSuccessModal").addClass("hidden");
    if (window.syncModalTimer) {
        clearInterval(window.syncModalTimer);
    }
}

function copySyncLink() {
    const linkText = $("#sync-store-link").text();
    navigator.clipboard.writeText(linkText).then(() => {
        const copyBtn = $("#copy-sync-link-btn");
        copyBtn.html('<i class="fas fa-check"></i> Copied!').removeClass("bg-blue-600").addClass("bg-emerald-600");
    }).catch(err => {
        console.error("Could not copy link", err);
    });
}

function closeEditItemModal() {
    $("#editItemModal").addClass("hidden");
    $("#inventoryPhotoEditorModal").addClass("hidden");
    $("#edit-submit-spinner").addClass("hidden");
    $("#edit-submit-text").text("Update Record");
    if (inventoryManager) {
        inventoryManager.closeInventoryPhotoEditor();
    }
}

// Global instance
let inventoryManager;

// Initialize when DOM is ready
$(document).ready(() => {
    console.log("$(document).ready() called");
    inventoryManager = new InventoryManager();
});
