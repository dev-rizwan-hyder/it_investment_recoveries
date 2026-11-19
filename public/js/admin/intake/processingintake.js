/**
 * Processing Intake - AJAX & jQuery Implementation
 */

let currentPage = 1;
let currentSearch = "";
let currentStatusFilter = "All Statuses";
let currentDateFilter = "";
let selectedPalletData = null;
let allPallets = [];
let selectedPalletIds = new Set();
let editPhotosToDelete = new Set();

$(document).ready(function () {
    initializeApp();
});

function initializeApp() {
    loadStats();
    loadPallets();
    attachEventListeners();
}

/**
 * =================================================
 * EVENT LISTENERS
 * =================================================
 */

function attachEventListeners() {
    // Search input - debounced
    let searchTimeout;
    $("#searchInput").on("keyup", function () {
        clearTimeout(searchTimeout);
        currentSearch = $(this).val();
        currentPage = 1;
        searchTimeout = setTimeout(() => {
            loadPallets();
        }, 300);
    });

    // Status filter
    $("#statusFilter").on("change", function () {
        currentStatusFilter = $(this).val();
        currentPage = 1;
        loadPallets();
    });

    // Date filter
    $("#dateFilter").on("change", function () {
        currentDateFilter = $(this).val();
        if (currentDateFilter) {
            $("#clearDateFilterBtn").removeClass("hidden");
        } else {
            $("#clearDateFilterBtn").addClass("hidden");
        }
        currentPage = 1;
        loadPallets();
    });

    $("#clearDateFilterBtn").on("click", function () {
        $("#dateFilter").val("");
        currentDateFilter = "";
        $(this).addClass("hidden");
        currentPage = 1;
        loadPallets();
    });

    // Export CSV
    $("#exportCsvBtn").on("click", function () {
        const params = new URLSearchParams({
            search: currentSearch,
            statusFilter: currentStatusFilter,
            dateFilter: currentDateFilter,
        });
        window.location.href = API_ROUTES.exportCsv + "?" + params.toString();
    });

    $("#selectAllPallets").on("change", function () {
        const isChecked = $(this).is(":checked");

        $(".pallet-select").each(function () {
            const palletId = String($(this).data("pallet-id"));
            $(this).prop("checked", isChecked);

            if (isChecked) {
                selectedPalletIds.add(palletId);
            } else {
                selectedPalletIds.delete(palletId);
            }
        });

        updateBulkActions();
    });

    $(document).on("change", ".pallet-select", function () {
        const palletId = String($(this).data("pallet-id"));
        const isChecked = $(this).is(":checked");

        $(`.pallet-select[data-pallet-id="${palletId}"]`).prop("checked", isChecked);

        if (isChecked) {
            selectedPalletIds.add(palletId);
        } else {
            selectedPalletIds.delete(palletId);
        }

        updateBulkActions();
    });

    $("#bulkStatusBtn").on("click", bulkUpdateStatus);
    $("#bulkDeleteBtn").on("click", bulkDeletePallets);
    $("#bulkEmailBtn").on("click", bulkSendEmail);

    $(document).on("click", "#editAddPhotosBtn", function () {
        $("#editNewPhotos").trigger("click");
    });

    $(document).on("change", "#editNewPhotos", function () {
        renderNewEditPhotoPreview(this.files);
    });

    $(document).on("click", "#clearEditNewPhotos", function () {
        $("#editNewPhotos").val("");
        renderNewEditPhotoPreview([]);
    });

    $(document).on("click", ".edit-photo-delete", function (event) {
        event.preventDefault();
        event.stopPropagation();

        const photoPath = $(this).data("photo-path");
        if (!photoPath) return;

        editPhotosToDelete.add(String(photoPath));
        $(this).closest("[data-edit-photo]").remove();

        if ($("#editPhotoDetails [data-edit-photo]").length === 0) {
            renderEditPhotos([]);
        }
    });

    // Edit form submit
    $("#editSubmitBtn").on("click", function (e) {
        e.preventDefault();
        savePalletChanges();
    });

    // Listing photo popup (works for dynamically rendered rows)
    $(document).on("click", ".processing-gallery-image", function () {
        try {
            const encodedPhotos = $(this).attr("data-photos");
            const title = $(this).attr("data-title") || "Images";
            if (!encodedPhotos) return;

            const photos = JSON.parse(decodeURIComponent(encodedPhotos));
            if (Array.isArray(photos) && photos.length > 0) {
                openImageGallery(photos, title);
            }
        } catch (e) {
            console.error("Failed to open processing image gallery:", e);
        }
    });
}

/**
 * =================================================
 * LOAD DATA
 * =================================================
 */

function loadStats() {
    $.ajax({
        url: API_ROUTES.getStats,
        type: "GET",
        success: function (response) {
            if (response.success) {
                renderStats(response.stats);
            }
        },
        error: function () {
            console.error("Error loading stats");
        },
    });
}

function loadPallets() {
    $("#palletsTableBody").html(`
        <tr>
            <td colspan="12" class="py-12 text-center text-slate-400 font-bold">
                <div class="flex items-center justify-center gap-3">
                    <div class="w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                    <span class="text-sm font-bold text-slate-500">Loading pallets...</span>
                </div>
            </td>
        </tr>
    `);
    $("#mobileView").html(`
        <div class="p-8 text-center text-slate-400 font-bold flex items-center justify-center gap-3">
            <div class="w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
            <span class="text-sm font-bold text-slate-500">Loading pallets...</span>
        </div>
    `);

    $.ajax({
        url: API_ROUTES.getPallets,
        type: "GET",
        data: {
            search: currentSearch,
            statusFilter: currentStatusFilter,
            dateFilter: currentDateFilter,
            page: currentPage,
        },
        success: function (response) {
            console.log("Pallets Received:", response);
            if (response.success && response.data) {
                // IMPORTANT: Save data to global variable
                allPallets = response.data; 
                selectedPalletIds.clear();

                renderTable(response.data);
                renderMobileView(response.data);
                renderPagination(response.pagination);
                updateBulkActions();
            } else {
                allPallets = []; // Reset if no data
                selectedPalletIds.clear();
                $("#palletsTableBody").html(
                    '<tr><td colspan="12" class="p-20 text-center text-slate-400 font-bold">No data found.</td></tr>',
                );
                updateBulkActions();
            }
        },
        error: function (xhr) {
            $("#palletsTableBody").html(
                '<tr><td colspan="12" class="p-12 text-center text-red-500 font-bold">Failed to load data. Please refresh or try again.</td></tr>',
            );
            showError("Error: " + xhr.status + " - " + xhr.statusText);
        },
    });
}



/**
 * =================================================
 * RENDER STATS
 * =================================================
 */

function renderStats(stats) {
    // If stats are undefined, show default 0
    const totalPallets = stats.totalPallets || 0;
    const processing = stats.processingCount || 0;
    const completed = stats.completedCount || 0;
    const items = stats.totalItems || 0;
    const weight = stats.totalWeight || 0;

    const statsData = [
        {
            label: "Total Pallets",
            value: totalPallets,
            color: "text-[#1e293b]",
            bar: "bg-[#1e293b]",
        },
        {
            label: "Processing",
            value: processing,
            color: "text-[#f97316]",
            bar: "bg-[#f97316]",
        },
        {
            label: "Completed",
            value: completed,
            color: "text-[#10b981]",
            bar: "bg-[#10b981]",
        },
        {
            label: "Total Items",
            value: numberFormat(items),
            color: "text-[#2563eb]",
            bar: "bg-[#2563eb]",
        },
        {
            label: "Total Weight",
            value: numberFormat(weight, 1) + " lbs",
            color: "text-[#8b5cf6]",
            bar: "bg-[#8b5cf6]",
        },
    ];

    let html = "";
    statsData.forEach((stat, i) => {
        html += `
            <div class="bg-white p-4 md:p-5 rounded-xl shadow-sm border border-[#f1f5f9] relative overflow-hidden flex items-center ${i === statsData.length - 1 ? "col-span-2 lg:col-span-1" : ""}">
                <div class="absolute left-0 top-0 bottom-0 w-1.5 ${stat.bar}"></div>
                <div class="pl-3">
                    <p class="text-[10px] md:text-[11px] font-extrabold text-[#94a3b8] uppercase tracking-[0.12em] mb-1">${stat.label}</p>
                    <h3 class="text-xl md:text-2xl font-black ${stat.color} tracking-tight">${stat.value}</h3>
                </div>
            </div>`;
    });
    $("#statsContainer").html(html);
}

/**
 * =================================================
 * RENDER TABLE
 * =================================================
 */

function renderTable(pallets) {
    const tableBody = $("#palletsTableBody");

    // Check if element exists to avoid 'null' errors
    if (tableBody.length === 0) {
        console.error("Error: #palletsTableBody not found in HTML");
        return;
    }

    if (!pallets || pallets.length === 0) {
        tableBody.html(`
            <tr>
                <td colspan="12" class="p-20 text-center text-slate-400 font-bold">No results found.</td>
            </tr>
        `);
        return;
    }

    // Count occurrences of each barcode for numbering duplicates
    const barcodeCounts = {};
    const barcodeIndex = {};
    pallets.forEach((pallet) => {
        const barcode = pallet.barcode_number;
        if (!barcodeCounts[barcode]) {
            barcodeCounts[barcode] = 0;
        }
        barcodeCounts[barcode]++;
    });

    let html = "";
    let templatesHtml = ""; // Separate container for hidden print templates

    try {
        const currentIndex = {}; // Track current item number for each barcode
        
        pallets.forEach((pallet) => {
            const breakdown = getProcessingBreakdown(pallet);
            const statusColor =
                pallet.status === "Completed"
                    ? "bg-[#10b981]"
                    : pallet.status === "Processing"
                      ? "bg-[#f97316]"
                      : pallet.status === "Draft"
                        ? "bg-[#64748b]"
                        : "bg-[#3b82f6]";

            // Null checks for table display
            const vendorName = pallet.client ? pallet.client.name : "N/A";
            const companyName = pallet.client ? pallet.client.company || "N/A" : "N/A";
            
            // Add numbering for duplicate barcodes
            const barcode = pallet.barcode_number;
            let displayBarcode = barcode;
            if (barcodeCounts[barcode] > 1) {
                currentIndex[barcode] = (currentIndex[barcode] || 0) + 1;
                displayBarcode = `${barcode} (#${currentIndex[barcode]})`;
            }

            html += `
                <tr class="hover:bg-[#f8fafc]/80 transition-colors border-b border-slate-50">
                    <td class="px-6 py-6 text-center">
                        <input type="checkbox" class="pallet-select h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500" data-pallet-id="${pallet.id}">
                    </td>
                    <td class="px-6 py-6 text-sm font-bold text-slate-700 whitespace-nowrap">${formatDate(pallet.created_at)}</td>
                    <td class="px-6 py-6 font-bold text-[#475569] text-sm">${companyName}</td>
                    <td class="px-6 py-6 font-bold text-[#475569] text-sm">${vendorName}</td>
                    <td class="px-6 py-6 font-black text-[#1e293b] uppercase text-sm">${displayBarcode || "N/A"}${pallet.job_no ? `<div class="text-[11px] font-semibold text-slate-400 normal-case">Job: ${escapeHtml(pallet.job_no)}</div>` : ''}</td>
                    <td class="px-6 py-6 text-center">
                        <select data-pallet-id="${pallet.id}" class="status-select px-3 py-1.5 rounded-lg text-[10px] font-black uppercase text-white shadow-sm border-none cursor-pointer ${statusColor}">
                            <option value="Draft" ${pallet.status === "Draft" ? "selected" : ""}>Draft</option>
                            <option value="Received" ${pallet.status === "Received" ? "selected" : ""}>Received</option>
                            <option value="Processing" ${pallet.status === "Processing" ? "selected" : ""}>Processing</option>
                            <option value="Completed" ${pallet.status === "Completed" ? "selected" : ""}>Completed</option>
                        </select>
                    </td>
                    <td class="px-6 py-6 text-center">${renderQuantityBreakdown(breakdown)}</td>
                    <td class="px-6 py-6 text-center">${renderWeightBreakdown(breakdown)}</td>
                    <td class="px-6 py-6 text-sm font-bold text-slate-500">${pallet.put_away_location || "N/A"}</td>
                    <td class="px-6 py-6 text-sm text-slate-600 max-w-xs truncate" title="${pallet.description || "N/A"}">${pallet.description || "N/A"}</td>
                    <td class="px-6 py-6 text-center">${renderPhotoThumbnail(pallet.photos_paths, pallet.barcode_number)}</td>
                    <td class="px-6 py-6 text-right space-x-2 whitespace-nowrap">
                        <button onclick="viewPallet(${pallet.id})" class="text-slate-400 hover:text-blue-600"><i class="fas fa-eye"></i></button>
                        <button onclick="printLabelAction('label-${pallet.id}')" class="text-slate-400 hover:text-slate-600"><i class="fas fa-print"></i></button>
                        <button onclick="editPallet(${pallet.id})" class="text-slate-400 hover:text-green-600"><i class="fas fa-edit"></i></button>
                        <button onclick="printReceiptAction(${pallet.id})" class="text-slate-400 transition-colors" title="Print Receipt"><i class="fas fa-file-invoice-dollar text-lg"></i></button>
                        <button onclick="deletePallet(${pallet.id})" class="text-slate-400 hover:text-red-500"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;

            // Store templates in separate string
            if (typeof renderPrintTemplates === "function") {
                templatesHtml += renderPrintTemplates(pallet);
            }
        });

        // Step 1: Update table body
        tableBody.html(html);

        // Step 2: Inject hidden templates at end of body (not inside table)
        if ($("#printTemplatesContainer").length === 0) {
            $("body").append(
                '<div id="printTemplatesContainer" class="hidden"></div>',
            );
        }
        $("#printTemplatesContainer").html(templatesHtml);
    } catch (err) {
        console.error("Rendering Error:", err);
        tableBody.html(
            `<tr><td colspan="12" class="p-10 text-center text-red-500 font-bold">Data Rendering Failed. Check console.</td></tr>`,
        );
    }
}

/**
 * =================================================
 * RENDER MOBILE VIEW
 * =================================================
 */

function renderMobileView(pallets) {
    if (pallets.length === 0) {
        $("#mobileView").html(`
            <div class="p-10 text-center text-slate-400 font-bold">No results found.</div>
        `);
        return;
    }

    // Count occurrences of each barcode for numbering duplicates
    const barcodeCounts = {};
    pallets.forEach((pallet) => {
        const barcode = pallet.barcode_number;
        if (!barcodeCounts[barcode]) {
            barcodeCounts[barcode] = 0;
        }
        barcodeCounts[barcode]++;
    });

    let html = "";
    const currentIndex = {}; // Track current item number for each barcode
    
    pallets.forEach((pallet) => {
        const breakdown = getProcessingBreakdown(pallet);
        const statusColor =
            pallet.status === "Completed"
                ? "bg-[#10b981]"
                : pallet.status === "Processing"
                  ? "bg-[#f97316]"
                  : pallet.status === "Draft"
                    ? "bg-[#64748b]"
                    : "bg-[#2563eb]";

        // Add numbering for duplicate barcodes
        const barcode = pallet.barcode_number;
        let displayBarcode = barcode;
        if (barcodeCounts[barcode] > 1) {
            currentIndex[barcode] = (currentIndex[barcode] || 0) + 1;
            displayBarcode = `${barcode} (#${currentIndex[barcode]})`;
        }

        html += `
            <div class="p-5 space-y-4">
                <div class="flex justify-between items-start">
                    <div class="flex items-start gap-3">
                        <input type="checkbox" class="pallet-select mt-1 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500" data-pallet-id="${pallet.id}">
                        <div>
                        <h4 class="font-black text-[#1e293b] uppercase">${displayBarcode}</h4>
                        ${pallet.job_no ? `<p class="text-[11px] font-semibold text-slate-500">Job: ${escapeHtml(pallet.job_no)}</p>` : ''}
                        <p class="text-sm font-bold text-slate-500">${pallet.client?.name || "N/A"}</p>
                        <p class="text-xs font-bold text-slate-400">${pallet.client?.company || "N/A"}</p>
                        </div>
                    </div>
                    <select data-pallet-id="${pallet.id}" class="status-select px-3 py-1.5 rounded-lg text-[10px] font-black uppercase text-white shadow-sm border-none ${statusColor}">
                        <option value="Draft" ${pallet.status === "Draft" ? "selected" : ""}>Draft</option>
                        <option value="Received" ${pallet.status === "Received" ? "selected" : ""}>Received</option>
                        <option value="Processing" ${pallet.status === "Processing" ? "selected" : ""}>Processing</option>
                        <option value="Completed" ${pallet.status === "Completed" ? "selected" : ""}>Completed</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 gap-2 bg-slate-50 p-3 rounded-xl border border-slate-100">
                    ${renderQuantityBreakdown(breakdown)}
                    ${renderWeightBreakdown(breakdown)}
                    <div class="text-center">
                        <p class="text-[9px] uppercase font-black text-slate-400">Loc</p>
                        <p class="font-bold text-slate-700 truncate px-1">${pallet.put_away_location || "-"}</p>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-2">
                    <span class="text-[10px] font-medium text-slate-400">${formatDate(pallet.created_at)}</span>
                    <div class="flex gap-4">
                        <button onclick="viewPallet(${pallet.id})" class="text-blue-600"><i class="fas fa-eye"></i></button>
                        <button onclick="printLabelAction('label-${pallet.id}')" class="text-orange-500"><i class="fas fa-print"></i></button>
                        <button onclick="editPallet(${pallet.id})" class="text-green-600"><i class="fas fa-edit"></i></button>
                        <button onclick="deletePallet(${pallet.id})" class="text-red-500"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        `;
    });

    $("#mobileView").html(html);

    // Attach status change listeners
    $(".status-select").on("change", function () {
        const palletId = $(this).data("pallet-id");
        const newStatus = $(this).val();
        updateStatus(palletId, newStatus);
    });
}

/**
 * =================================================
 * PAGINATION
 * =================================================
 */

function renderPagination(pagination) {
    let html = "";

    if (pagination.last_page > 1) {
        html += `<div class="flex justify-center gap-2">`;

        // Previous button
        if (pagination.current_page > 1) {
            html += `<button onclick="goToPage(${pagination.current_page - 1})" class="px-3 py-2 bg-blue-600 text-white rounded text-sm">Previous</button>`;
        }

        // Page numbers
        for (let i = 1; i <= pagination.last_page; i++) {
            if (i === pagination.current_page) {
                html += `<button class="px-3 py-2 bg-blue-600 text-white rounded text-sm">${i}</button>`;
            } else {
                html += `<button onclick="goToPage(${i})" class="px-3 py-2 bg-slate-100 text-slate-600 rounded text-sm hover:bg-slate-200">${i}</button>`;
            }
        }

        // Next button
        if (pagination.current_page < pagination.last_page) {
            html += `<button onclick="goToPage(${pagination.current_page + 1})" class="px-3 py-2 bg-blue-600 text-white rounded text-sm">Next</button>`;
        }

        html += `</div>`;
    }

    $("#paginationContainer").html(html);
}

function goToPage(page) {
    currentPage = page;
    loadPallets();
    window.scrollTo({ top: 0, behavior: "smooth" });
}

/**
 * =================================================
 * MODAL FUNCTIONS
 * =================================================
 */

/**
 * =================================================
 * MODAL FUNCTIONS (VIEW)
 * =================================================
 */

function viewPallet(id) {
    if (!id) {
        console.error("No ID provided to viewPallet");
        return;
    }

    if (!API_ROUTES.getPalletDetails) {
        console.error("Route getPalletDetails is missing in API_ROUTES");
        return;
    }

    const url = API_ROUTES.getPalletDetails.replace("ID", id);

    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            if (response.success && response.data) {
                const pallet = response.data;
                const relatedPallets = getRelatedPallets(pallet);
                const breakdown = getProcessingBreakdown(pallet);
                const client = pallet.client || relatedPallets[0]?.client || {};
                const barcodeSvg = getBarcodeSvg(pallet, relatedPallets);
                selectedPalletData = pallet;

                const itemIndex = relatedPallets.findIndex(p => p.id === pallet.id);
                const itemSuffix = relatedPallets.length > 1 && itemIndex !== -1 ? ` (#${itemIndex + 1})` : '';
                $("#modalBarcode").text("#" + (pallet.barcode_number || "---") + itemSuffix);

                const statusBadgeClass =
                    pallet.status === "Completed"
                        ? "bg-emerald-100 text-emerald-700"
                        : pallet.status === "Processing"
                          ? "bg-orange-100 text-orange-700"
                          : pallet.status === "Draft"
                            ? "bg-slate-200 text-slate-700"
                            : "bg-blue-100 text-blue-700";

                $("#logisticsDetails").html(`
                    ${renderDetailRow("Customer/Vendor", client.name)}
                    ${renderDetailRow("Company", client.company)}
                    ${renderDetailRow("Email", client.email, "break-all")}
                    ${renderDetailRow("Phone", client.phone)}
                    ${renderDetailRow("Address", client.address)}
                    <div class="flex justify-between gap-3 border-b border-slate-100 pb-2">
                        <span class="text-slate-400 font-bold uppercase text-[10px]">Status</span>
                        <span class="px-3 py-0.5 rounded-lg ${statusBadgeClass} font-black text-[9px] uppercase">${escapeHtml(formatStatus(pallet.status))}</span>
                    </div>
                    ${renderDetailRow("Put-Away Location", pallet.put_away_location)}
                    ${renderDetailRow("Receiving Employee", pallet.receiving_employee || "N/A")}
                    ${renderDetailRow("Job No", pallet.job_no || "N/A")}
                    ${renderDetailRow("Processing Employee", pallet.processing_employee || "N/A")}
                    ${renderDetailRow("Marketing Ref", pallet.marketing_reference || "None")}
                    ${renderDetailRow("Created", formatDate(pallet.created_at))}
                    ${renderDetailRow("Updated", formatDate(pallet.updated_at), "", false, true)}
                `);

                const grossWeight = parseFloat(pallet.gross_weight) || 0;
                const tareWeight = parseFloat(pallet.tare_weight) || 0;
                const netWeight = Math.max(0, grossWeight - tareWeight);

                $("#weightDetails").html(`
                    <div class="grid grid-cols-2 md:grid-cols-5 bg-slate-50/50 border-b border-slate-100">
                        ${renderMetricCell("Total Items", pallet.estimated_count || 0)}
                        ${renderMetricCell("Remain Items", breakdown.remaining_quantity)}
                        ${renderMetricCell("Transferred Items", breakdown.transferred_quantity)}
                        ${renderMetricCell("Reuse Items", breakdown.reuse_quantity)}
                        ${renderMetricCell("Scrap Items", breakdown.scrap_quantity)}
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-5 bg-white border-b border-slate-100">
                        ${renderMetricCell("Total Weight", numberFormat(breakdown.total_weight, 2) + " lbs")}
                        ${renderMetricCell("Remain Weight", numberFormat(breakdown.remaining_weight, 2) + " lbs")}
                        ${renderMetricCell("Transferred Weight", numberFormat(breakdown.transferred_weight, 2) + " lbs")}
                        ${renderMetricCell("Reuse Weight", numberFormat(breakdown.reuse_weight, 2) + " lbs")}
                        ${renderMetricCell("Scrap Weight", numberFormat(breakdown.scrap_weight, 2) + " lbs")}
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 bg-slate-50/30 p-2">
                        <div class="p-3 text-center">
                            <span class="text-[9px] font-black text-slate-400 uppercase">Gross Weight:</span>
                            <span class="font-bold text-slate-700 ml-1">${numberFormat(grossWeight, 2)} lbs</span>
                        </div>
                        <div class="p-3 text-center">
                            <span class="text-[9px] font-black text-slate-400 uppercase">Tare Weight:</span>
                            <span class="font-bold text-slate-700 ml-1">${numberFormat(tareWeight, 2)} lbs</span>
                        </div>
                        <div class="p-3 text-center">
                            <span class="text-[9px] font-black text-slate-400 uppercase">Net Weight:</span>
                            <span class="font-bold text-slate-700 ml-1">${numberFormat(netWeight, 2)} lbs</span>
                        </div>
                    </div>
                `);

                $("#descriptionDetails").html(`
                    <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Description</p>
                    <p class="text-sm text-slate-800 mb-4 leading-relaxed">${escapeHtml(pallet.description || "No description")}</p>
                    <div class="pt-3 border-t border-slate-200">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Notes</p>
                        <p class="text-sm text-slate-500 italic">${escapeHtml(pallet.notes || "No notes")}</p>
                    </div>
                    <div class="pt-3 mt-3 border-t border-slate-200">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Barcode</p>
                        <p class="font-mono text-xs font-black text-slate-700">${escapeHtml(pallet.barcode_number || "N/A")}</p>
                    </div>
                `);

                // Display photos for THIS specific item
                const itemPhotos = normalizePhotoPaths(pallet.photos_paths);
                renderModalPhotos(itemPhotos, pallet.all_barcode_photos);

                $("#barcodeText").text(pallet.barcode_number || "");
                $("#barcodeDisplay").html(
                    barcodeSvg
                        ? `<div class="max-w-full overflow-hidden">${barcodeSvg}</div>`
                        : `<div class="text-lg font-bold tracking-widest">${escapeHtml(pallet.barcode_number || "N/A")}</div>`,
                );

                $("#viewToEditBtn")
                    .off("click")
                    .on("click", function () {
                        closeViewModal();
                        openEditModal(pallet);
                    });

                setViewMaximized(false);
                $("#viewModal").removeClass("hidden");
            } else {
                alert("Error: Pallet data was empty.");
            }
        },
        error: function (xhr) {
            console.error("Full Error details:", xhr.responseText);
            alert("Error: Could not load pallet details. Check console.");
        },
    });
}

/**
 * =================================================
 * EDIT MODAL LOGIC
 * =================================================
 */

function editPallet(id) {
    if (!id) return;
    const url = API_ROUTES.getPalletDetails.replace("ID", id);

    $.get(url, function (response) {
        if (response.success) {
            const pallet = response.data;
            openEditModal(pallet);
        }
    }).fail(function () {
        alert("Error loading pallet data for editing.");
    });
}

function openEditModal(pallet) {
    if (!pallet) return;
    selectedPalletData = pallet;
    resetEditPhotoState();
    const breakdown = getProcessingBreakdown(pallet);

    // Fill form fields for this specific pallet item
    $("#editPalletId").val(pallet.id);
    
    const subtitle = `#${pallet.barcode_number || ''} (Item #${pallet.id}${pallet.description ? ' - ' + escapeHtml(pallet.description) : ''})`;
    $("#editModalItemSubtitle").html(subtitle);

    // Set status - convert "pending" to "Received" as default
    let statusValue = pallet.status || "Received";
    if (statusValue === "pending") {
        statusValue = "Received";
    }
    $("#editStatus").val(statusValue);
    $("#editIntakeDate").val(pallet.created_at ? String(pallet.created_at).substring(0, 10) : "");
    
    $("#editVendor").val(pallet.client ? pallet.client.name : "N/A");
    $("#editVendorId").val(pallet.client_id);
    updateEditClientDetails(pallet.client);
    $("#editGrossWeight").val(pallet.gross_weight);
    $("#editTareWeight").val(pallet.tare_weight);
    $("#editReuseWeight").val(breakdown.scrap_weight);
    $("#editEstimatedCount").val(pallet.estimated_count || 0);
    $("#editReuseQuantity").val(breakdown.scrap_quantity);
    $("#editDescription").val(pallet.description);
    $("#editLocation").val(pallet.put_away_location);
    $("#editMarketingReference").val(pallet.marketing_reference);
    $("#editReceivingEmployee").val(pallet.receiving_employee || "");
    $("#editJobNo").val(pallet.job_no || "");
    $("#editProcessingEmployee").val(pallet.processing_employee || "");
    $("#editNotes").val(pallet.notes);

    // Render ONLY this specific item's photos in the edit modal
    renderEditPhotos(pallet.photos_paths, pallet.barcode_number);

    // Initial Display Update
    updateNetWeightDisplay();
    updateNetItemsDisplay();

    // Bind real-time calculations
    $("#editGrossWeight, #editTareWeight")
        .off("input")
        .on("input", updateNetWeightDisplay);
    $("#editEstimatedCount")
        .off("input")
        .on("input", updateNetItemsDisplay);

    $("#editModal").removeClass("hidden");
}

function updateEditClientDetails(client) {
    $("#editClientCompany").text(client?.company || "N/A");
    $("#editClientEmail").text(client?.email || "N/A");
    $("#editClientPhone").text(client?.phone || "N/A");
    $("#editClientAddress").text(client?.address || "N/A");
}

function checkAutoCompletedStatus() {
    const gross = parseFloat($("#editGrossWeight").val()) || 0;
    const tare = parseFloat($("#editTareWeight").val()) || 0;
    const reuseW = parseFloat($("#editReuseWeight").val()) || 0;
    const netW = Math.max(0, gross - tare - reuseW);

    const estimated = parseInt($("#editEstimatedCount").val()) || 0;
    const reuseQ = parseInt($("#editReuseQuantity").val()) || 0;
    const netItems = Math.max(0, estimated - reuseQ);

    if ((estimated > 0 || gross > 0 || reuseQ > 0 || reuseW > 0) && netItems <= 0 && netW <= 0) {
        $("#editStatus").val("Completed");
    }
}

function updateNetWeightDisplay() {
    // Get values from input fields
    const gross = parseFloat($("#editGrossWeight").val()) || 0;
    const tare = parseFloat($("#editTareWeight").val()) || 0;
    const reuse = parseFloat($("#editReuseWeight").val()) || 0;

    // Logic: Gross - Tare - Reuse = Net
    const net = gross - tare - reuse;

    // Update the blue display box (clamping to 0 if result is negative)
    const displayValue = Math.max(0, net).toFixed(1);
    $("#netWeightDisplay").text(displayValue + " lbs");
    checkAutoCompletedStatus();
}

// Attach listeners to all three fields inside your initialization or attachEventListeners function
$(document).on(
    "input",
    "#editGrossWeight, #editTareWeight, #editReuseWeight",
    updateNetWeightDisplay,
);

function updateNetItemsDisplay() {
    // 1. Get values from the inputs
    const estimated = parseInt($("#editEstimatedCount").val()) || 0;
    const reuse = parseInt($("#editReuseQuantity").val()) || 0;

    // 2. Logic: Estimated - Reuse = Net
    const net = estimated - reuse;

    // 3. Update the display (Ensuring it doesn't go below 0)
    $("#netItemsDisplay").text(Math.max(0, net) + " Units");
    checkAutoCompletedStatus();
}

// Ensure listeners are bound to both inputs
$(document).on(
    "input",
    "#editEstimatedCount, #editReuseQuantity",
    updateNetItemsDisplay,
);

function savePalletChanges() {
    const palletId = $("#editPalletId").val();

    const formData = new FormData();
    formData.append("editStatus", $("#editStatus").val());
    formData.append("editIntakeDate", $("#editIntakeDate").val() || "");
    formData.append("editVendor", $("#editVendorId").val());
    formData.append("editGrossWeight", $("#editGrossWeight").val());
    formData.append("editTareWeight", $("#editTareWeight").val());
    formData.append("editReuseWeight", $("#editReuseWeight").val());
    formData.append("editEstimatedCount", $("#editEstimatedCount").val());
    formData.append("editReuseQuantity", $("#editReuseQuantity").val());
    formData.append("editDescription", $("#editDescription").val());
    formData.append("editLocation", $("#editLocation").val());
    formData.append("editMarketingReference", $("#editMarketingReference").val());
    formData.append("editReceivingEmployee", $("#editReceivingEmployee").val() || "");
    formData.append("editJobNo", $("#editJobNo").val() || "");
    formData.append("editProcessingEmployee", $("#editProcessingEmployee").val() || "");
    formData.append("editNotes", $("#editNotes").val());

    editPhotosToDelete.forEach((photoPath) => {
        formData.append("photos_to_delete[]", photoPath);
    });

    const newPhotos = $("#editNewPhotos")[0]?.files || [];
    Array.from(newPhotos).forEach((file) => {
        formData.append("new_photos[]", file);
    });

    $.ajax({
        url: API_ROUTES.updatePallet.replace("ID", palletId),
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.success) {
                resetEditPhotoState();
                closeEditModal();
                loadPallets();
                loadStats();
                // Optional: alert('Success!');
            }
        },
        error: function (xhr) {
            // This will now show specifically what is missing
            console.error(xhr.responseText);
            alert("Update Failed: Check if all required fields are filled.");
        },
    });
}

function closeViewModal() {
    setViewMaximized(false);
    $("#viewModal").addClass("hidden");
}

function toggleViewMaximize() {
    const isMaximized = $("#viewModal").data("maximized") === true;
    setViewMaximized(!isMaximized);
}

function setViewMaximized(isMaximized) {
    const modal = $("#viewModal");
    const panel = $("#viewModalPanel");
    const content = $("#viewModalContent");
    const button = $("#viewMaximizeBtn");
    const icon = $("#viewMaximizeIcon");

    modal.data("maximized", isMaximized);
    modal.toggleClass("p-4", !isMaximized);
    modal.toggleClass("p-0", isMaximized);

    panel.toggleClass("max-w-5xl rounded-[2.5rem] max-h-[calc(100vh-2rem)]", !isMaximized);
    panel.toggleClass("max-w-none h-screen rounded-none max-h-none", isMaximized);

    content.toggleClass("max-h-[60vh]", !isMaximized);
    content.toggleClass("max-h-none flex-1", isMaximized);

    button.attr("title", isMaximized ? "Restore view" : "Maximize view");
    button.attr("aria-label", isMaximized ? "Restore view" : "Maximize view");
    icon.toggleClass("fa-expand", !isMaximized);
    icon.toggleClass("fa-compress", isMaximized);
}
function closeEditModal() {
    resetEditPhotoState();
    $("#editModal").addClass("hidden");
}

/**
 * =================================================
 * ACTIONS
 * =================================================
 */

function updateStatus(palletId, status) {
    $.ajax({
        url: API_ROUTES.updateStatus.replace("ID", palletId),
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: { status: status },
        success: function (response) {
            if (response.success) {
                showSuccess("Status updated successfully!");
                loadStats();
            }
        },
        error: function () {
            showError("Error updating status");
        },
    });
}

function updateBulkActions() {
    const selectedCount = selectedPalletIds.size;

    $("#bulkSelectedCount").text(`${selectedCount} selected`);
    $("#bulkActions").toggleClass("hidden", selectedCount === 0);
    $("#bulkStatusBtn, #bulkDeleteBtn")
        .prop("disabled", selectedCount === 0)
        .toggleClass("opacity-50 cursor-not-allowed", selectedCount === 0);

    syncSelectAllPallets();
}

function syncSelectAllPallets() {
    const checkboxes = $(".pallet-select");
    const checkedCount = checkboxes.filter(":checked").length;

    $("#selectAllPallets")
        .prop("checked", checkboxes.length > 0 && checkedCount === checkboxes.length)
        .prop("indeterminate", checkedCount > 0 && checkedCount < checkboxes.length);
}

function getSelectedPalletIds() {
    return Array.from(selectedPalletIds).map((id) => parseInt(id, 10)).filter(Boolean);
}

function bulkUpdateStatus() {
    const ids = getSelectedPalletIds();
    const status = $("#bulkStatusSelect").val();

    if (ids.length === 0) {
        showError("Select at least one pallet.");
        return;
    }

    if (!status) {
        showError("Choose a status first.");
        return;
    }

    $.ajax({
        url: API_ROUTES.bulkStatus,
        type: "POST",
        data: { ids, status },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            if (response.success) {
                showSuccess(response.message || "Selected pallets updated successfully.");
                selectedPalletIds.clear();
                loadPallets();
                loadStats();
            } else {
                showError(response.message || "Error updating selected pallets");
            }
        },
        error: function (xhr) {
            showError(xhr.responseJSON?.message || "Error updating selected pallets");
        },
    });
}

function bulkDeletePallets() {
    const ids = getSelectedPalletIds();

    if (ids.length === 0) {
        showError("Select at least one pallet.");
        return;
    }

    if (!confirm(`Delete ${ids.length} selected pallet(s)?`)) return;

    $.ajax({
        url: API_ROUTES.bulkDelete,
        type: "DELETE",
        data: { ids },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            if (response.success) {
                showSuccess(response.message || "Selected pallets deleted successfully.");
                selectedPalletIds.clear();
                loadPallets();
                loadStats();
            } else {
                showError(response.message || "Error deleting selected pallets");
            }
        },
        error: function (xhr) {
            showError(xhr.responseJSON?.message || "Error deleting selected pallets");
        },
    });
}

function bulkSendEmail() {
    const ids = getSelectedPalletIds();

    if (ids.length === 0) {
        showError("Select at least one pallet.");
        return;
    }

    if (!confirm(`Send receipt email to client(s) for ${ids.length} selected pallet(s)?`)) return;

    // Disable button and show loading state
    const btn = $("#bulkEmailBtn");
    const originalHtml = btn.html();
    btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');

    $.ajax({
        url: API_ROUTES.bulkSendEmail,
        type: "POST",
        data: { ids },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            if (response.success) {
                showSuccess(response.message || "Emails sent successfully.");
            } else {
                showError(response.message || "Error sending emails.");
            }
            selectedPalletIds.clear();
            updateBulkActions();
        },
        error: function (xhr) {
            showError(xhr.responseJSON?.message || "Error sending emails.");
        },
        complete: function () {
            btn.prop("disabled", false).html(originalHtml);
        },
    });
}

function deletePallet(palletId) {
    if (!confirm("Are you sure you want to delete this pallet?")) return;

    $.ajax({
        url: API_ROUTES.deletePallet.replace("ID", palletId),
        type: "DELETE",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            if (response.success) {
                showSuccess("Pallet deleted successfully!");
                loadPallets();
                loadStats();
            } else {
                showError(response.message || "Error deleting pallet");
            }
        },
        error: function (xhr) {
            let errorMessage = "Error deleting pallet";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            showError(errorMessage);
        },
    });
}

/**
 * =================================================
 * PRINT FUNCTIONS
 * =================================================
 */

function printLabelAction(elementId) {
    const data = document.getElementById(elementId)?.innerHTML;
    if (!data) {
        showError("Print template not found");
        return;
    }
    openLabelPrintPage(data);
}

function printReceiptAction(palletId) {
    if (!palletId) {
        showError("Pallet data not found");
        return;
    }

    if (!API_ROUTES.getPalletDetails) {
        showError("Pallet details route is missing");
        return;
    }

    $.ajax({
        url: API_ROUTES.getPalletDetails.replace("ID", palletId),
        type: "GET",
        dataType: "json",
        success: function (response) {
            if (response.success && response.data) {
                openReceiptPrintPage(generateReceiptTemplate(response.data));
            } else {
                showError("Could not load pallet receipt data");
            }
        },
        error: function () {
            showError("Could not load pallet receipt data");
        },
    });
}

function printLabel() {
    if (!selectedPalletData) {
        showError("Open a pallet before printing the label");
        return;
    }

    openLabelPrintPage(generateLabelTemplate(selectedPalletData));
}

function printReceipt() {
    if (!selectedPalletData) {
        showError("Open a pallet before printing the receipt");
        return;
    }

    openReceiptPrintPage(generateReceiptTemplate(selectedPalletData));
}

function generateLabelTemplate(pallet) {
    const relatedPallets = getRelatedPallets(pallet);
    const totals = getPalletTotals(pallet, relatedPallets);
    const client = pallet.client || relatedPallets[0]?.client || {};
    const barcodeNumber = escapeHtml(pallet.barcode_number || "N/A");
    const barcodeSvg = getBarcodeSvg(pallet, relatedPallets);
    const description = getFirstValue(relatedPallets, "description") || pallet.description || "N/A";
    const notes = getFirstValue(relatedPallets, "notes") || pallet.notes || "";
    const receivingEmployee = getFirstValue(relatedPallets, "receiving_employee") || pallet.receiving_employee || "";
    const jobNo = getFirstValue(relatedPallets, "job_no") || pallet.job_no || "";

    return `
        <section class="recycling-print-label">
            <div class="label-head">
                <strong>IT Investment Recoveries</strong>
                <span>Pallet Label</span>
            </div>
            <div class="barcode-block">
                ${barcodeSvg || '<div class="barcode-fallback">Barcode unavailable</div>'}
                <div>${barcodeNumber}</div>
            </div>
            <div class="label-grid">
                ${jobNo ? renderLabelRow("Job No", jobNo) : ""}
                ${receivingEmployee ? renderLabelRow("Rec. Employee", receivingEmployee) : ""}
                ${renderLabelRow("Description", description)}
                ${renderLabelRow("Client", client.name)}
                ${renderLabelRow("Company", client.company)}
                ${renderLabelRow("Phone", client.phone)}
                ${renderLabelRow("Total / Remain Qty", `${numberFormat(totals.total_quantity)} / ${numberFormat(totals.remaining_quantity)}`)}
                ${renderLabelRow("Tr / Re / Sc Qty", `${numberFormat(totals.transferred_quantity)} / ${numberFormat(totals.reuse_quantity)} / ${numberFormat(totals.scrap_quantity)}`)}
                ${renderLabelRow("Total / Remain Wt", `${numberFormat(totals.total_weight, 1)} / ${numberFormat(totals.remaining_weight, 1)} lbs`)}
                ${renderLabelRow("Tr / Re / Sc Wt", `${numberFormat(totals.transferred_weight, 1)} / ${numberFormat(totals.reuse_weight, 1)} / ${numberFormat(totals.scrap_weight, 1)} lbs`)}
                ${notes ? renderLabelRow("Notes", notes) : ""}
            </div>
            <div class="label-checks">
                <span><i></i> Reuse</span>
                <span><i></i> Universal E-waste</span>
            </div>
            <div class="label-foot">
                __DENVER_LABEL_TIMESTAMP__
            </div>
        </section>
    `;
}

function generateReceiptTemplate(pallet) {
    const relatedPallets = getRelatedPallets(pallet);
    const totals = getPalletTotals(pallet, relatedPallets);
    const client = pallet.client || relatedPallets[0]?.client || {};
    const barcodeNumber = escapeHtml(pallet.barcode_number || "N/A");
    const barcodeSvg = getBarcodeSvg(pallet, relatedPallets);
    const itemRows = relatedPallets
        .map((item, index) => renderReceiptItemRow(item, index))
        .join("");

    return `
        <section class="processing-receipt">
            <div class="receipt-head">
                <h1>IT Investment Recoveries</h1>
                <p>E-waste Recycling Receipt</p>
            </div>
            <div class="receipt-meta">
                ${renderReceiptMeta("Receipt Number", `REC-${pallet.barcode_number || pallet.id || "N/A"}`)}
                ${renderReceiptMeta("Date", formatDate(pallet.created_at))}
                ${renderReceiptMeta("Pallet Barcode", pallet.barcode_number)}
                ${renderReceiptMeta("Status", formatStatus(pallet.status))}
                ${pallet.job_no ? renderReceiptMeta("Job No", pallet.job_no) : ""}
            </div>
            <div class="receipt-section receipt-client-grid">
                ${renderReceiptField("Customer/Vendor", client.name)}
                ${renderReceiptField("Company", client.company)}
                ${renderReceiptField("Phone", client.phone)}
                ${renderReceiptField("Email", client.email)}
                ${renderReceiptField("Address", client.address, true)}
                ${pallet.receiving_employee ? renderReceiptField("Receiving Employee", pallet.receiving_employee) : ""}
            </div>
            <div class="receipt-section">
                <h2>Itemized Intake Records</h2>
                <table class="receipt-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Description</th>
                            <th>Total Qty</th>
                            <th>Remain Qty</th>
                            <th>Transferred Qty</th>
                            <th>Reuse Qty</th>
                            <th>Scrap Qty</th>
                            <th>Total Wt</th>
                            <th>Remain Wt</th>
                            <th>Transferred Wt</th>
                            <th>Reuse Wt</th>
                            <th>Scrap Wt</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>${itemRows}</tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">Totals</td>
                            <td>${numberFormat(totals.total_quantity)}</td>
                            <td>${numberFormat(totals.remaining_quantity)}</td>
                            <td>${numberFormat(totals.transferred_quantity)}</td>
                            <td>${numberFormat(totals.reuse_quantity)}</td>
                            <td>${numberFormat(totals.scrap_quantity)}</td>
                            <td>${numberFormat(totals.total_weight, 2)} lbs</td>
                            <td>${numberFormat(totals.remaining_weight, 2)} lbs</td>
                            <td>${numberFormat(totals.transferred_weight, 2)} lbs</td>
                            <td>${numberFormat(totals.reuse_weight, 2)} lbs</td>
                            <td>${numberFormat(totals.scrap_weight, 2)} lbs</td>
                            <td>${numberFormat(totals.remaining_quantity)} items</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="receipt-barcode-wrap">
                <p>Pallet Barcode Visualization</p>
                <div class="receipt-barcode">${barcodeSvg || barcodeNumber}</div>
                <strong>${barcodeNumber}</strong>
            </div>
            <div class="receipt-foot">
                <strong>Thank you for recycling with us!</strong>
                <span>This receipt confirms the drop-off of recyclable materials. Printed __DENVER_PRINT_TIMESTAMP__.</span>
            </div>
        </section>
    `;
}

function openLabelPrintPage(data) {
    const printWindow = window.open("", "_blank", "height=360,width=520");
    const printableData = applyPrintTimestamp(data);

    if (!printWindow) {
        showError("Print window was blocked");
        return;
    }

    printWindow.document.write(`
        <!doctype html>
        <html>
            <head>
                <title>Print Pallet Label</title>
                <style>
                    @page {
                        size: 3.5in calc(2.5in + 5px);
                        margin: 0;
                    }

                    * {
                        box-sizing: border-box;
                    }

                    html,
                    body {
                        width: 3.5in;
                        min-height: calc(2.75in + 5px);
                        margin: 0;
                        padding: 0;
                        background: #fff;
                        color: #111827;
                        font-family: Arial, Helvetica, sans-serif;
                    }

                    .print-sheet {
                        width: 3.5in;
                    }

                    .recycling-print-label {
                        display: flex;
                        flex-direction: column;
                        width: 3.5in;
                        height: calc(2.5in + 5px);
                        padding: 0.08in 0.1in;
                        border: 1px solid #111827;
                        background: #fff;
                        break-after: page;
                        page-break-after: always;
                        overflow: hidden;
                    }

                    .recycling-print-label:last-child {
                        break-after: auto;
                        page-break-after: auto;
                    }

                    .label-head {
                        display: flex;
                        align-items: baseline;
                        justify-content: space-between;
                        gap: 0.08in;
                        border-bottom: 1px solid #111827;
                        padding-bottom: 0.03in;
                        text-transform: uppercase;
                        flex-shrink: 0;
                    }

                    .label-head strong {
                        font-size: 8.5pt;
                        line-height: 1;
                    }

                    .label-head span {
                        color: #475569;
                        font-size: 6.5pt;
                        font-weight: 700;
                        white-space: nowrap;
                    }

                    .barcode-block {
                        padding: 0.04in 0 0.03in;
                        text-align: center;
                        flex-shrink: 0;
                    }

                    .barcode-block img,
                    .barcode-block svg {
                        display: block;
                        width: 100%;
                        height: 0.42in;
                        max-height: 0.42in;
                        object-fit: contain;
                        margin: 0 auto;
                    }

                    .barcode-block div {
                        margin-top: 0.01in;
                        font-family: "Courier New", monospace;
                        font-size: 8.5pt;
                        font-weight: 700;
                        letter-spacing: 0.5pt;
                    }

                    .barcode-fallback {
                        color: #64748b;
                        font-size: 7pt;
                        line-height: 0.42in;
                    }

                    .label-grid {
                        display: grid;
                        grid-template-columns: 0.78in 1fr;
                        column-gap: 0.06in;
                        row-gap: 0.015in;
                        font-size: 6.5pt;
                        line-height: 1.12;
                        flex: 1 1 auto;
                        min-height: 0;
                        overflow: hidden;
                        align-content: start;
                    }

                    .label-grid span {
                        color: #475569;
                        font-weight: 700;
                        text-transform: uppercase;
                    }

                    .label-grid strong {
                        min-width: 0;
                        font-weight: 700;
                        overflow-wrap: anywhere;
                    }

                    .label-checks {
                        display: flex;
                        justify-content: center;
                        gap: 0.18in;
                        margin-top: 0.04in;
                        font-size: 8px;
                        font-weight: 900;
                        text-transform: uppercase;
                        flex-shrink: 0;
                    }
                    .label-checks span {
                        display: inline-flex;
                        align-items: center;
                        gap: 0.04in;
                    }
                    .label-checks i {
                        width: 0.09in;
                        height: 0.09in;
                        border: 1px solid #111827;
                        display: inline-block;
                    }

                    .label-foot {
                        margin-top: 0.04in;
                        border-top: 1px dashed #94a3b8;
                        padding-top: 0.02in;
                        color: #64748b;
                        font-size: 5.7pt;
                        text-align: center;
                        flex-shrink: 0;
                    }

                    @media screen {
                        body {
                            width: auto;
                            min-height: auto;
                            padding: 18px;
                            background: #e5e7eb;
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                        }

                        .recycling-print-label {
                            margin: 0 auto 12px;
                            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.18);
                        }
                    }

                    @media print {
                        html,
                        body {
                            width: 3.5in;
                            margin: 0 !important;
                            padding: 0 !important;
                            background: #fff !important;
                        }

                        .recycling-print-label {
                            margin: 0;
                            box-shadow: none;
                        }
                    }
                </style>
            </head>
            <body>
                <main class="print-sheet">${printableData}</main>
                <script>
                    window.addEventListener("load", function () {
                        window.focus();
                        window.print();
                    });
                <\/script>
            </body>
        </html>
    `);
    printWindow.document.close();
}

function openReceiptPrintPage(data) {
    const printWindow = window.open("", "_blank", "height=850,width=1000");
    const printableData = applyPrintTimestamp(data);

    if (!printWindow) {
        showError("Print window was blocked");
        return;
    }

    printWindow.document.write(`
        <html>
            <head>
                <title>Print Recycling Receipt</title>
                <style>
                    @page { size: letter; margin: 0.4in; }
                    body {
                        margin: 0;
                        padding: 24px;
                        background: #f8fafc;
                        color: #000;
                        font-family: Arial, sans-serif;
                    }
                    .processing-receipt {
                        width: 7.5in;
                        margin: 0 auto;
                        padding: 0.35in;
                        box-sizing: border-box;
                        background: #fff;
                        border: 1px solid #e5e7eb;
                    }
                    .receipt-head {
                        text-align: center;
                        border-bottom: 2px solid #000;
                        padding-bottom: 12px;
                        margin-bottom: 18px;
                    }
                    .receipt-head h1 {
                        margin: 0;
                        font-size: 28px;
                        font-weight: 900;
                        text-transform: uppercase;
                    }
                    .receipt-head p {
                        margin: 6px 0 0;
                        font-size: 14px;
                        font-weight: 900;
                        text-transform: uppercase;
                    }
                    .receipt-meta {
                        display: grid;
                        grid-template-columns: repeat(5, 1fr);
                        gap: 10px;
                        border-bottom: 1px solid #000;
                        padding-bottom: 14px;
                        margin-bottom: 20px;
                    }
                    .receipt-meta div,
                    .receipt-field {
                        min-width: 0;
                    }
                    .receipt-meta span,
                    .receipt-field span {
                        display: block;
                        color: #6b7280;
                        font-size: 9px;
                        font-weight: 900;
                        text-transform: uppercase;
                    }
                    .receipt-meta strong,
                    .receipt-field strong {
                        display: block;
                        margin-top: 3px;
                        font-size: 12px;
                        color: #111827;
                        overflow-wrap: anywhere;
                    }
                    .receipt-section {
                        margin-bottom: 22px;
                    }
                    .receipt-client-grid {
                        display: grid;
                        grid-template-columns: repeat(2, 1fr);
                        gap: 12px 18px;
                    }
                    .receipt-field.is-wide {
                        grid-column: 1 / -1;
                    }
                    .receipt-section h2 {
                        margin: 0 0 8px;
                        font-size: 12px;
                        font-weight: 900;
                        text-transform: uppercase;
                    }
                    .receipt-table {
                        width: 100%;
                        border-collapse: collapse;
                        font-size: 10px;
                    }
                    .receipt-table th {
                        background: #111827;
                        color: #fff;
                        text-align: left;
                        padding: 7px 6px;
                        font-size: 8px;
                        text-transform: uppercase;
                    }
                    .receipt-table td {
                        border-bottom: 1px solid #e5e7eb;
                        padding: 7px 6px;
                        vertical-align: top;
                    }
                    .receipt-table tfoot td {
                        background: #f3f4f6;
                        font-weight: 900;
                    }
                    .receipt-item-note {
                        margin-top: 2px;
                        color: #6b7280;
                        font-size: 8px;
                    }
                    .receipt-barcode-wrap {
                        text-align: center;
                        background: #f9fafb;
                        border: 1px solid #e5e7eb;
                        padding: 18px;
                        margin-top: 16px;
                    }
                    .receipt-barcode-wrap p {
                        margin: 0 0 8px;
                        color: #6b7280;
                        font-size: 9px;
                        font-weight: 900;
                        text-transform: uppercase;
                    }
                    .receipt-barcode svg {
                        max-width: 320px;
                        width: 100%;
                        height: 60px;
                    }
                    .receipt-barcode-wrap strong {
                        display: block;
                        margin-top: 8px;
                        font-family: monospace;
                        font-size: 14px;
                    }
                    .receipt-foot {
                        text-align: center;
                        margin-top: 24px;
                        border-top: 1px solid #000;
                        padding-top: 16px;
                    }
                    .receipt-foot strong,
                    .receipt-foot span {
                        display: block;
                    }
                    .receipt-foot strong {
                        font-size: 16px;
                    }
                    .receipt-foot span {
                        margin-top: 5px;
                        color: #4b5563;
                        font-size: 10px;
                    }
                    .print-actions {
                        margin-top: 18px;
                        text-align: center;
                    }
                    .print-actions button {
                        background: #ea580c;
                        color: #fff;
                        border: 0;
                        border-radius: 10px;
                        padding: 12px 28px;
                        font-weight: 900;
                        cursor: pointer;
                    }
                    @media print {
                        body {
                            padding: 0;
                            background: #fff;
                        }
                        .processing-receipt {
                            width: auto;
                            margin: 0;
                            padding: 0;
                            border: 0;
                        }
                        .print-actions {
                            display: none;
                        }
                    }
                </style>
            </head>
            <body>
                ${printableData}
                <div class="print-actions"><button onclick="window.print()">Confirm Print</button></div>
            </body>
        </html>
    `);
    printWindow.document.close();
}

function openPrintPage(data) {
    openReceiptPrintPage(data);
}

function renderPrintTemplates(pallet) {
    const labelId = `label-${pallet.id}`;
    const receiptId = `receipt-${pallet.id}`;

    return `
        <div id="${labelId}" style="display: none;">
            ${generateLabelTemplate(pallet)}
        </div>
        <div id="${receiptId}" style="display: none;">
            ${generateReceiptTemplate(pallet)}
        </div>
    `;
}

/**
 * =================================================
 * HELPER FUNCTIONS
 * =================================================
 */

function escapeHtml(value) {
    return String(value ?? "").replace(/[&<>"']/g, (character) => {
        const entities = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;",
        };

        return entities[character];
    });
}

function formatDisplayValue(value, fallback = "N/A") {
    return value === null || value === undefined || value === "" ? fallback : value;
}

function toNumber(value) {
    const number = parseFloat(value);
    return Number.isFinite(number) ? number : 0;
}

function getProcessingBreakdown(row) {
    const incoming = row?.intake_breakdown || {};
    const totalQuantity = toNumber(incoming.total_quantity ?? row?.estimated_count);
    const hasIncomingBreakdown = row?.intake_breakdown && Object.keys(row.intake_breakdown).length > 0;
    const reuseQuantity = toNumber(incoming.reuse_quantity ?? 0);
    const processingScrapQuantity = hasIncomingBreakdown ? 0 : toNumber(row?.reuse_quantity);
    const scrapQuantity = toNumber(incoming.scrap_quantity ?? 0);
    const totalWeight = toNumber(incoming.total_weight ?? getPalletTotalWeight(row));
    const reuseWeight = toNumber(incoming.reuse_weight ?? 0);
    const processingScrapWeight = hasIncomingBreakdown ? 0 : toNumber(row?.reuse_weight);
    const scrapWeight = toNumber(incoming.scrap_weight ?? 0);
    const remainingQuantity = Math.max(
        0,
        toNumber(incoming.remaining_quantity ?? row?.remaining_quantity ?? (totalQuantity - processingScrapQuantity - reuseQuantity - scrapQuantity)),
    );
    const remainingWeight = Math.max(
        0,
        toNumber(incoming.remaining_weight ?? row?.remaining_weight ?? (totalWeight - processingScrapWeight - reuseWeight - scrapWeight)),
    );
    const transferredQuantity = Math.max(
        0,
        toNumber(incoming.transferred_quantity ?? row?.transferred_quantity ?? (totalQuantity - remainingQuantity - reuseQuantity - scrapQuantity)),
    );
    const transferredWeight = Math.max(
        0,
        toNumber(incoming.transferred_weight ?? row?.transferred_weight ?? (totalWeight - remainingWeight - reuseWeight - scrapWeight)),
    );

    return {
        records: toNumber(incoming.records ?? 0),
        intake_records: toNumber(incoming.intake_records ?? incoming.records ?? 0),
        total_quantity: Math.max(0, totalQuantity),
        remaining_quantity: remainingQuantity,
        transferred_quantity: transferredQuantity,
        reuse_quantity: Math.max(0, reuseQuantity),
        scrap_quantity: Math.max(0, scrapQuantity),
        total_weight: Math.max(0, totalWeight),
        remaining_weight: remainingWeight,
        transferred_weight: transferredWeight,
        reuse_weight: Math.max(0, reuseWeight),
        scrap_weight: Math.max(0, scrapWeight),
        sources: Array.isArray(incoming.sources) ? incoming.sources : [],
        has_intake_records: Boolean(incoming.has_intake_records),
    };
}

function getPalletTotalWeight(row) {
    const gross = toNumber(row?.gross_weight);
    const tare = toNumber(row?.tare_weight);
    const total = Math.max(0, gross - tare);
    const remaining = toNumber(row?.remaining_weight ?? row?.net_weight);
    const scrap = toNumber(row?.reuse_weight);

    return total > 0 ? total : Math.max(0, remaining + scrap);
}

function formatStatus(status) {
    if (!status || status === "pending") {
        return "Received";
    }

    return status;
}

function getRelatedPallets(pallet) {
    if (!pallet) return [];

    const related =
        Array.isArray(pallet.related_pallets) && pallet.related_pallets.length > 0
            ? pallet.related_pallets
            : [pallet];

    return related
        .filter(Boolean)
        .map((item) =>
            Object.assign({}, item, {
                client: item.client || pallet.client || null,
                barcode_number: item.barcode_number || pallet.barcode_number,
                barcode_svg: item.barcode_svg || pallet.barcode_svg || "",
            }),
        );
}

function getRemainingQuantity(row) {
    return getProcessingBreakdown(row).remaining_quantity;
}

function getRemainingWeight(row) {
    return getProcessingBreakdown(row).remaining_weight;
}

function calculatePalletTotals(pallets) {
    const rows = Array.isArray(pallets) ? pallets : [];
    const totals = {
        records: rows.length,
        intake_records: 0,
        total_quantity: 0,
        remaining_quantity: 0,
        transferred_quantity: 0,
        reuse_quantity: 0,
        scrap_quantity: 0,
        net_items: 0,
        estimated_count: 0,
        total_weight: 0,
        gross_weight: 0,
        tare_weight: 0,
        remaining_weight: 0,
        transferred_weight: 0,
        reuse_weight: 0,
        scrap_weight: 0,
        net_weight: 0,
    };

    rows.forEach((row) => {
        const breakdown = getProcessingBreakdown(row);

        totals.intake_records += breakdown.intake_records;
        totals.total_quantity += breakdown.total_quantity;
        totals.remaining_quantity += breakdown.remaining_quantity;
        totals.transferred_quantity += breakdown.transferred_quantity;
        totals.reuse_quantity += breakdown.reuse_quantity;
        totals.scrap_quantity += breakdown.scrap_quantity;
        totals.total_weight += breakdown.total_weight;
        totals.gross_weight += breakdown.total_weight;
        totals.tare_weight += toNumber(row.tare_weight);
        totals.remaining_weight += breakdown.remaining_weight;
        totals.transferred_weight += breakdown.transferred_weight;
        totals.reuse_weight += breakdown.reuse_weight;
        totals.scrap_weight += breakdown.scrap_weight;
    });

    totals.estimated_count = totals.total_quantity;
    totals.net_items = totals.remaining_quantity;
    totals.net_weight = totals.remaining_weight;

    return totals;
}

function getPalletTotals(pallet, relatedPallets = []) {
    if (pallet?.totals) {
        const totals = Object.assign({}, pallet.totals);
        totals.records = toNumber(totals.records ?? (Array.isArray(relatedPallets) ? relatedPallets.length : 0));
        totals.intake_records = toNumber(totals.intake_records ?? totals.records);
        totals.total_quantity = toNumber(totals.total_quantity ?? totals.estimated_count);
        totals.remaining_quantity = toNumber(totals.remaining_quantity ?? totals.net_items);
        totals.transferred_quantity = toNumber(totals.transferred_quantity);
        totals.reuse_quantity = toNumber(totals.reuse_quantity);
        totals.scrap_quantity = toNumber(totals.scrap_quantity);
        totals.total_weight = toNumber(totals.total_weight ?? totals.gross_weight);
        totals.remaining_weight = toNumber(totals.remaining_weight ?? totals.net_weight);
        totals.transferred_weight = toNumber(totals.transferred_weight);
        totals.reuse_weight = toNumber(totals.reuse_weight);
        totals.scrap_weight = toNumber(totals.scrap_weight);
        totals.estimated_count = totals.total_quantity;
        totals.net_items = totals.remaining_quantity;
        totals.gross_weight = totals.total_weight;
        totals.net_weight = totals.remaining_weight;

        return totals;
    }

    return calculatePalletTotals(relatedPallets);
}

function getBarcodeSvg(pallet, relatedPallets = []) {
    if (pallet?.barcode_svg) return pallet.barcode_svg;
    const rowWithBarcode = relatedPallets.find((item) => item?.barcode_svg);
    return rowWithBarcode?.barcode_svg || "";
}

function getFirstValue(rows, key) {
    const match = (Array.isArray(rows) ? rows : []).find((row) => {
        const value = row?.[key];
        return value !== null && value !== undefined && value !== "";
    });

    return match?.[key] || "";
}

function renderDetailRow(label, value, valueClass = "", rawValue = false, isLast = false) {
    const borderClass = isLast ? "" : "border-b border-slate-100 pb-2";
    const displayValue = rawValue
        ? formatDisplayValue(value)
        : escapeHtml(formatDisplayValue(value));

    return `
        <div class="flex justify-between gap-3 ${borderClass}">
            <span class="text-slate-400 font-bold uppercase text-[10px]">${escapeHtml(label)}</span>
            <span class="font-black text-slate-800 text-right ${valueClass}">${displayValue}</span>
        </div>
    `;
}

function renderMetricCell(label, value) {
    return `
        <div class="p-4 border-b border-r border-slate-100 last:border-r-0">
            <p class="text-[9px] font-black text-slate-400 uppercase">${escapeHtml(label)}</p>
            <p class="font-black text-slate-700">${escapeHtml(formatDisplayValue(value, "0"))}</p>
        </div>
    `;
}

function renderQuantityBreakdown(breakdown) {
    return `
        <div class="mx-auto w-full min-w-[140px] max-w-[185px] rounded-xl border border-slate-100 bg-slate-50 p-2 text-[10px] font-black uppercase tracking-wide text-slate-500 space-y-0.5">
            ${renderBreakdownLine("Total Qty", numberFormat(breakdown.total_quantity), "text-slate-900")}
            ${renderBreakdownLine("Remain Qty", numberFormat(breakdown.remaining_quantity), "text-emerald-700")}
            ${renderBreakdownLine("Transferred Qty", numberFormat(breakdown.transferred_quantity), "text-blue-600")}
            ${renderBreakdownLine("Reuse Qty", numberFormat(breakdown.reuse_quantity), "text-orange-600")}
            ${renderBreakdownLine("Scrap Qty", numberFormat(breakdown.scrap_quantity), "text-amber-700")}
        </div>
    `;
}

function renderWeightBreakdown(breakdown) {
    return `
        <div class="mx-auto w-full min-w-[150px] max-w-[195px] rounded-xl border border-slate-100 bg-slate-50 p-2 text-[10px] font-black uppercase tracking-wide text-slate-500 space-y-0.5">
            ${renderBreakdownLine("Total Wt", `${numberFormat(breakdown.total_weight, 1)} lbs`, "text-slate-900")}
            ${renderBreakdownLine("Remain Wt", `${numberFormat(breakdown.remaining_weight, 1)} lbs`, "text-emerald-700")}
            ${renderBreakdownLine("Transferred Wt", `${numberFormat(breakdown.transferred_weight, 1)} lbs`, "text-blue-600")}
            ${renderBreakdownLine("Reuse Wt", `${numberFormat(breakdown.reuse_weight, 1)} lbs`, "text-orange-600")}
            ${renderBreakdownLine("Scrap Wt", `${numberFormat(breakdown.scrap_weight, 1)} lbs`, "text-amber-700")}
        </div>
    `;
}

function renderBreakdownLine(label, value, valueClass = "text-slate-900", emphasized = false) {
    return `
        <div class="flex items-center justify-between gap-2 ${emphasized ? "mt-1 border-t border-slate-200 pt-1" : ""}">
            <span>${escapeHtml(label)}</span>
            <span class="${valueClass}">${escapeHtml(value)}</span>
        </div>
    `;
}

function renderRelatedPalletDetails(pallet) {
    const relatedPallets = getRelatedPallets(pallet);
    const sourceBreakdown = renderIntakeSourceBreakdown(pallet);

    if (relatedPallets.length === 0) {
        return `
            <div class="p-8 text-center text-slate-400 font-bold text-xs uppercase tracking-widest">
                No itemized records found
            </div>
        `;
    }

    const records = relatedPallets
        .map((item, index) => {
            const breakdown = getProcessingBreakdown(item);
            const showRecordHeader = relatedPallets.length > 1;
            const isCurrentItem = String(item.id) === String(pallet.id);
            const cardStyle = isCurrentItem
                ? "border-2 border-blue-500 bg-blue-50/20 shadow-md"
                : "border border-slate-200 bg-white";
            const currentBadge = isCurrentItem
                ? `<span class="bg-blue-600 text-white text-[9px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider">Viewing This Item</span>`
                : "";

            const itemPhotosHtml = renderPhotoThumbnail(item.photos_paths, item.barcode_number);

            return `
                <div class="${cardStyle} rounded-[1.25rem] p-4 md:p-5 relative">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <p class="text-[10px] font-black uppercase tracking-[0.18em] text-slate-400">Record ${String(index + 1).padStart(2, "0")}</p>
                            ${currentBadge}
                        </div>
                        <div class="flex items-center gap-3">
                            <p class="font-mono text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">ID: ${item.id} | ${escapeHtml(item.barcode_number || pallet.barcode_number || "N/A")}</p>
                            <button type="button" onclick="closeViewModal(); editPallet(${item.id});" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1 shadow-sm">
                                <i class="fas fa-edit text-xs"></i> Edit Item
                            </button>
                        </div>
                    </div>

                    ${renderItemizedField("Material Description", item.description || "N/A", "slate", true)}

                    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5">
                        ${renderQuantityBreakdown(breakdown)}
                        ${renderWeightBreakdown(breakdown)}
                    </div>

                    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4 md:gap-5">
                        ${renderItemizedField("Put-Away Location", item.put_away_location || "N/A")}
                        ${renderItemizedField("Marketing Ref", item.marketing_reference || "N/A")}
                        ${renderItemizedField("Receiving Employee", item.receiving_employee || "N/A")}
                        ${renderItemizedField("Processing Employee", item.processing_employee || pallet.processing_employee || "N/A")}
                        ${renderItemizedField("Barcode", item.barcode_number || pallet.barcode_number || "N/A")}
                    </div>

                    <div class="mt-5 flex flex-col md:flex-row gap-4 items-start justify-between">
                        <div class="flex-1 w-full">
                            ${renderItemizedField("Internal Notes", item.notes || "N/A", "slate", true, "min-h-[60px] items-start")}
                        </div>
                        <div class="w-full md:w-auto min-w-[120px]">
                            <p class="mb-2 text-[10px] font-black uppercase text-slate-400">Item Photos (${normalizePhotoPaths(item.photos_paths).length})</p>
                            ${itemPhotosHtml}
                        </div>
                    </div>
                </div>
            `;
        })
        .join("");

    return `
        <div class="space-y-5 bg-white p-5 md:p-6">
            ${sourceBreakdown}
            ${records}
        </div>
    `;
}

function renderIntakeSourceBreakdown(pallet) {
    const breakdown = getProcessingBreakdown(pallet);
    const sources = breakdown.sources;

    if (!Array.isArray(sources) || sources.length === 0) {
        return "";
    }

    const cards = sources
        .map((source) => {
            const sourceBreakdown = getProcessingBreakdown({ intake_breakdown: source });

            return `
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <p class="text-[10px] font-black uppercase tracking-[0.18em] text-slate-500">${escapeHtml(source.source_label || "Intake")}</p>
                        <span class="rounded-full bg-white px-2 py-1 text-[9px] font-black text-slate-400">${numberFormat(sourceBreakdown.records)} rec</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        ${renderQuantityBreakdown(sourceBreakdown)}
                        ${renderWeightBreakdown(sourceBreakdown)}
                    </div>
                </div>
            `;
        })
        .join("");

    return `
        <div>
            <p class="mb-3 text-[10px] font-black uppercase tracking-[0.18em] text-blue-600">Assigned Intake Breakdown</p>
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-3">${cards}</div>
        </div>
    `;
}

function renderItemizedField(label, value, tone = "slate", isWide = false, extraValueClass = "") {
    const labelClass = tone === "blue" ? "text-blue-600" : "text-slate-400";

    return `
        <div class="${isWide ? "md:col-span-2" : ""}">
            <p class="mb-2 text-[10px] font-black uppercase ${labelClass}">${escapeHtml(label)}</p>
            <div class="flex min-h-[46px] items-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-black text-slate-900 ${extraValueClass}">
                <span class="whitespace-pre-wrap break-words">${escapeHtml(formatDisplayValue(value))}</span>
            </div>
        </div>
    `;
}

function renderLabelRow(label, value) {
    return `
        <span>${escapeHtml(label)}</span><strong>${escapeHtml(formatDisplayValue(value))}</strong>
    `;
}

function renderReceiptItemRow(item, index) {
    const breakdown = getProcessingBreakdown(item);
    const marketing = item.marketing_reference
        ? `<div class="receipt-item-note">Marketing: ${escapeHtml(item.marketing_reference)}</div>`
        : "";
    const notes = item.notes
        ? `<div class="receipt-item-note">Notes: ${escapeHtml(item.notes)}</div>`
        : "";

    return `
        <tr>
            <td>${String(index + 1).padStart(2, "0")}</td>
            <td>
                <strong>${escapeHtml(item.description || "N/A")}</strong>
                ${marketing}
                ${notes}
            </td>
            <td>${numberFormat(breakdown.total_quantity)}</td>
            <td>${numberFormat(breakdown.remaining_quantity)}</td>
            <td>${numberFormat(breakdown.transferred_quantity)}</td>
            <td>${numberFormat(breakdown.reuse_quantity)}</td>
            <td>${numberFormat(breakdown.scrap_quantity)}</td>
            <td>${numberFormat(breakdown.total_weight, 2)} lbs</td>
            <td>${numberFormat(breakdown.remaining_weight, 2)} lbs</td>
            <td>${numberFormat(breakdown.transferred_weight, 2)} lbs</td>
            <td>${numberFormat(breakdown.reuse_weight, 2)} lbs</td>
            <td>${numberFormat(breakdown.scrap_weight, 2)} lbs</td>
            <td>${escapeHtml(item.put_away_location || "N/A")}</td>
        </tr>
    `;
}

function renderReceiptMeta(label, value) {
    return `
        <div>
            <span>${escapeHtml(label)}</span>
            <strong>${escapeHtml(formatDisplayValue(value))}</strong>
        </div>
    `;
}

function renderReceiptField(label, value, isWide = false) {
    return `
        <div class="receipt-field ${isWide ? "is-wide" : ""}">
            <span>${escapeHtml(label)}</span>
            <strong>${escapeHtml(formatDisplayValue(value))}</strong>
        </div>
    `;
}

function applyPrintTimestamp(data) {
    return String(data || "")
        .split("__DENVER_LABEL_TIMESTAMP__")
        .join(escapeHtml(formatDenverLabelDateTime()))
        .split("__DENVER_PRINT_TIMESTAMP__")
        .join(escapeHtml(formatDenverDateTime()));
}

function normalizePhotoPaths(photosPaths) {
    let photos = photosPaths;

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

function buildPhotoUrl(path) {
    const photoPath = String(path || "").trim();
    if (!photoPath) return "";

    if (/^(https?:|data:|blob:)/i.test(photoPath)) {
        return photoPath;
    }

    let baseUrl = "/";
    if (typeof palletImageUrl !== "undefined" && palletImageUrl) {
        baseUrl = palletImageUrl;
    } else if (typeof storageUrl !== "undefined" && storageUrl) {
        baseUrl = storageUrl;
    }

    const cleanPath = photoPath.startsWith("/") ? photoPath.substring(1) : photoPath;
    return `${baseUrl}${cleanPath}`;
}

function collectPalletPhotos(pallets) {
    const seen = new Set();
    const photos = [];

    (Array.isArray(pallets) ? pallets : []).forEach((pallet) => {
        normalizePhotoPaths(pallet?.photos_paths).forEach((path) => {
            if (!seen.has(path)) {
                seen.add(path);
                photos.push(path);
            }
        });
    });

    return photos;
}

function getFileIcon(fileOrPath) {
    let type = '';
    let name = '';
    if (typeof fileOrPath === 'string') {
        name = fileOrPath;
        if (fileOrPath.startsWith('data:')) {
            type = fileOrPath.split(';')[0].replace('data:', '');
        }
    } else if (fileOrPath && fileOrPath.name) {
        name = fileOrPath.name;
        type = fileOrPath.type || '';
    }

    const ext = name ? name.split('.').pop().toLowerCase() : '';
    const isImg = type.startsWith('image/') || ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'].includes(ext);

    if (isImg) {
        return { isImage: true };
    }

    if (type.includes('pdf') || ext === 'pdf') {
        return { isImage: false, icon: 'fa-file-pdf', color: 'text-red-500', bg: 'bg-red-50', border: 'border-red-200', label: 'PDF' };
    }
    if (type.includes('word') || ['doc', 'docx'].includes(ext)) {
        return { isImage: false, icon: 'fa-file-word', color: 'text-blue-500', bg: 'bg-blue-50', border: 'border-blue-200', label: 'DOC' };
    }
    if (type.includes('excel') || type.includes('spreadsheet') || ['xls', 'xlsx', 'csv'].includes(ext)) {
        return { isImage: false, icon: 'fa-file-excel', color: 'text-emerald-500', bg: 'bg-emerald-50', border: 'border-emerald-200', label: 'EXCEL' };
    }
    if (type.includes('zip') || type.includes('compressed') || ['zip', 'rar', '7z', 'tar', 'gz'].includes(ext)) {
        return { isImage: false, icon: 'fa-file-archive', color: 'text-amber-500', bg: 'bg-amber-50', border: 'border-amber-200', label: 'ZIP' };
    }
    if (type.includes('text') || ['txt', 'rtf', 'log', 'json', 'xml'].includes(ext)) {
        return { isImage: false, icon: 'fa-file-alt', color: 'text-slate-600', bg: 'bg-slate-50', border: 'border-slate-200', label: 'TXT' };
    }

    return { isImage: false, icon: 'fa-file', color: 'text-indigo-500', bg: 'bg-indigo-50', border: 'border-indigo-200', label: ext ? ext.toUpperCase() : 'FILE' };
}

function renderPhotoThumbnail(photosPaths, palletBarcode = "") {
    const photos = normalizePhotoPaths(photosPaths);

    if (photos.length > 0) {
        const photoUrls = photos.map(buildPhotoUrl).filter(Boolean);
        const firstPhoto = photoUrls[0];
        const fileInfo = getFileIcon(firstPhoto);
        const encodedPhotos = encodeURIComponent(JSON.stringify(photoUrls));
        const title = palletBarcode ? `Pallet ${palletBarcode}` : "Photos & Files";

        if (fileInfo.isImage) {
            return `<img src="${escapeHtml(firstPhoto)}" class="processing-gallery-image w-10 h-10 rounded-lg object-cover mx-auto cursor-pointer hover:ring-2 hover:ring-blue-500 transition-all shadow-sm" data-photos="${escapeHtml(encodedPhotos)}" data-title="${escapeHtml(title)}" title="Click to view all attachments" onerror="this.onerror=null;this.src='/images/placeholder.png'">`;
        } else {
            return `<div class="processing-gallery-image w-10 h-10 rounded-lg ${fileInfo.bg} ${fileInfo.border} border flex items-center justify-center mx-auto cursor-pointer hover:ring-2 hover:ring-blue-500 transition-all shadow-sm" data-photos="${escapeHtml(encodedPhotos)}" data-title="${escapeHtml(title)}" title="Click to view attachments">
                <i class="fas ${fileInfo.icon} ${fileInfo.color} text-lg"></i>
            </div>`;
        }
    }

    return `<div class="w-10 h-10 rounded-lg bg-slate-50 border flex items-center justify-center mx-auto"><i class="fas fa-paperclip text-slate-300"></i></div>`;
}

function renderModalPhotos(photosPaths, allBarcodePhotos = []) {
    let photos = normalizePhotoPaths(photosPaths);
    let isFallback = false;

    if (photos.length === 0) {
        photos = normalizePhotoPaths(allBarcodePhotos);
        isFallback = true;
    }

    if (photos.length === 0) {
        $("#photoDetails").html(`
            <div class="col-span-full py-8 text-center bg-slate-50 border border-dashed border-slate-200 rounded-2xl">
                <i class="fas fa-paperclip text-slate-300 text-2xl mb-2"></i>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">No Attachments</p>
            </div>
        `);
        return;
    }

    const photoUrls = photos.map(buildPhotoUrl).filter(Boolean);
    const encodedPhotos = encodeURIComponent(JSON.stringify(photoUrls));
    const html = photoUrls
        .map((url, index) => {
            const fileInfo = getFileIcon(url);
            const title = (isFallback ? `Batch File - ${index + 1}` : `Item File - ${index + 1}`);

            if (fileInfo.isImage) {
                return `
                    <div class="relative group cursor-pointer">
                        <img src="${escapeHtml(url)}"
                             class="processing-gallery-image w-full aspect-square object-cover rounded-xl border border-slate-200 shadow-sm transition-all hover:ring-2 hover:ring-blue-500"
                             data-photos="${escapeHtml(encodedPhotos)}"
                             data-title="${escapeHtml(title)}"
                             title="Click to view image"
                             onerror="this.onerror=null;this.src='/images/placeholder.png'">
                        <div class="pointer-events-none absolute inset-0 bg-black/0 group-hover:bg-black/40 rounded-xl transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <i class="fas fa-search-plus text-white text-2xl"></i>
                        </div>
                    </div>
                `;
            } else {
                return `
                    <a href="${escapeHtml(url)}" target="_blank" download class="relative group border-2 ${fileInfo.border} ${fileInfo.bg} rounded-xl p-3 aspect-square flex flex-col items-center justify-center text-center shadow-sm hover:shadow-md transition-all hover:ring-2 hover:ring-blue-500">
                        <i class="fas ${fileInfo.icon} ${fileInfo.color} text-3xl mb-2 group-hover:scale-110 transition-transform"></i>
                        <span class="text-[9px] font-extrabold ${fileInfo.color} uppercase tracking-wider">${fileInfo.label}</span>
                        <span class="text-[8px] font-semibold text-slate-500 mt-1 flex items-center gap-1">
                            <i class="fas fa-external-link-alt text-[7px]"></i> View/Download
                        </span>
                    </a>
                `;
            }
        })
        .join("");

    const fallbackHeader = isFallback ? `<div class="col-span-full mb-1 text-[10px] font-bold text-amber-600 uppercase">No files for this specific item. Showing batch files (${photos.length}):</div>` : "";

    $("#photoDetails").html(fallbackHeader + html);
}

function resetEditPhotoState() {
    editPhotosToDelete.clear();
    $("#editNewPhotos").val("");
    renderNewEditPhotoPreview([]);
}

function renderEditPhotos(photosPaths, palletBarcode = "") {
    const container = $("#editPhotoDetails");
    if (container.length === 0) return;

    const photos = normalizePhotoPaths(photosPaths);

    if (photos.length === 0) {
        container.html(`
            <div class="col-span-full py-8 text-center bg-white border border-dashed border-slate-200 rounded-2xl">
                <i class="fas fa-paperclip text-slate-300 text-2xl mb-2"></i>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">No Attachments</p>
            </div>
        `);
        return;
    }

    const photoUrls = photos.map(buildPhotoUrl).filter(Boolean);
    if (photoUrls.length === 0) {
        container.html(`
            <div class="col-span-full py-8 text-center bg-white border border-dashed border-slate-200 rounded-2xl">
                <i class="fas fa-paperclip text-slate-300 text-2xl mb-2"></i>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">No Attachments</p>
            </div>
        `);
        return;
    }

    const encodedPhotos = encodeURIComponent(JSON.stringify(photoUrls));
    const title = palletBarcode ? `Pallet ${palletBarcode} Attachments` : "Pallet Attachments";
    const html = photos
        .map((path) => {
            const url = buildPhotoUrl(path);
            const fileInfo = getFileIcon(url || path);

            if (fileInfo.isImage) {
                return `
                    <div data-edit-photo data-photo-path="${escapeHtml(path)}" class="relative group">
                        <img src="${escapeHtml(url)}"
                             class="processing-gallery-image aspect-square w-full cursor-pointer rounded-xl border border-slate-200 bg-white object-cover shadow-sm transition-all hover:ring-2 hover:ring-blue-500"
                             data-photos="${escapeHtml(encodedPhotos)}"
                             data-title="${escapeHtml(title)}"
                             title="Click to view image"
                             onerror="this.onerror=null;this.src='/images/placeholder.png'">
                        <button type="button"
                                class="edit-photo-delete absolute right-1.5 top-1.5 flex h-7 w-7 items-center justify-center rounded-full bg-red-600 text-white opacity-95 shadow-lg transition-all hover:bg-red-700"
                                data-photo-path="${escapeHtml(path)}"
                                title="Remove this photo">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                `;
            } else {
                return `
                    <div data-edit-photo data-photo-path="${escapeHtml(path)}" class="relative group border-2 ${fileInfo.border} ${fileInfo.bg} rounded-xl p-2 aspect-square flex flex-col items-center justify-center text-center shadow-sm">
                        <a href="${escapeHtml(url)}" target="_blank" download class="flex flex-col items-center justify-center text-center w-full h-full">
                            <i class="fas ${fileInfo.icon} ${fileInfo.color} text-2xl mb-1"></i>
                            <span class="text-[8px] font-extrabold ${fileInfo.color} uppercase">${fileInfo.label}</span>
                        </a>
                        <button type="button"
                                class="edit-photo-delete absolute right-1.5 top-1.5 flex h-6 w-6 items-center justify-center rounded-full bg-red-600 text-white opacity-95 shadow-lg transition-all hover:bg-red-700 z-10"
                                data-photo-path="${escapeHtml(path)}"
                                title="Remove this file">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                `;
            }
        })
        .join("");

    container.html(html);
}

function renderNewEditPhotoPreview(fileList) {
    const preview = $("#editNewPhotoPreview");
    const clearButton = $("#clearEditNewPhotos");
    if (preview.length === 0) return;

    const rawFiles = Array.from(fileList || []);

    if (rawFiles.length === 0) {
        preview.addClass("hidden").html("");
        clearButton.addClass("hidden");
        return;
    }

    const htmlParts = rawFiles.map((file) => {
        const fileInfo = getFileIcon(file);

        if (fileInfo.isImage) {
            const objectUrl = URL.createObjectURL(file);
            return `
                <div class="relative aspect-square w-full rounded-xl overflow-hidden border border-blue-100 bg-white shadow-sm">
                    <img src="${escapeHtml(objectUrl)}" class="w-full h-full object-cover" title="${escapeHtml(file.name)}">
                    <div class="absolute inset-x-1 bottom-1 rounded-lg bg-black/60 px-1.5 py-1 text-center text-[9px] font-bold text-white truncate">
                        ${escapeHtml(file.name)}
                    </div>
                </div>
            `;
        } else {
            return `
                <div class="relative aspect-square w-full rounded-xl overflow-hidden border-2 ${fileInfo.border} ${fileInfo.bg} p-2 flex flex-col items-center justify-center text-center shadow-sm">
                    <i class="fas ${fileInfo.icon} ${fileInfo.color} text-2xl mb-1"></i>
                    <span class="text-[8px] font-bold text-slate-700 truncate w-full px-1">${escapeHtml(file.name)}</span>
                    <span class="text-[7px] font-extrabold ${fileInfo.color} uppercase">${fileInfo.label}</span>
                </div>
            `;
        }
    });

    preview.html(htmlParts.join("")).removeClass("hidden");
}

function numberFormat(num, decimals = 0) {
    return toNumber(num).toLocaleString("en-US", {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    });
}

function formatDate(dateString) {
    if (!dateString) return "N/A";
    const str = String(dateString).trim();
    const match = str.match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (match) {
        const year = parseInt(match[1], 10);
        const month = parseInt(match[2], 10) - 1;
        const day = parseInt(match[3], 10);
        const d = new Date(year, month, day);
        if (!Number.isNaN(d.getTime())) {
            return d.toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric",
            });
        }
    }

    const date = new Date(dateString);
    if (Number.isNaN(date.getTime())) return "N/A";

    return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
}

function formatTime(dateString) {
    if (!dateString) return "N/A";
    const date = new Date(dateString);
    if (Number.isNaN(date.getTime())) return "N/A";

    return date.toLocaleTimeString("en-US", {
        hour: "2-digit",
        minute: "2-digit",
        hour12: true,
    });
}

function formatDenverDateTime(date = new Date()) {
    return new Intl.DateTimeFormat("en-US", {
        timeZone: "America/Denver",
        year: "numeric",
        month: "short",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: true,
        timeZoneName: "short",
    }).format(date);
}

function formatDenverLabelDateTime(date = new Date()) {
    const timeZone = "America/Denver";
    const dateStr = date.toLocaleDateString("en-US", {
        timeZone,
        year: "numeric",
        month: "long",
        day: "numeric",
    });
    const timeStr = date.toLocaleTimeString("en-US", {
        timeZone,
        hour: "numeric",
        minute: "2-digit",
        second: "2-digit",
        timeZoneName: "short",
    });

    return `${dateStr} ${timeStr} (Denver, CO)`;
}

function formatDenverDate(date = new Date()) {
    return new Intl.DateTimeFormat("en-US", {
        timeZone: "America/Denver",
        year: "numeric",
        month: "short",
        day: "2-digit",
    }).format(date);
}

function formatDenverTime(date = new Date()) {
    return new Intl.DateTimeFormat("en-US", {
        timeZone: "America/Denver",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: true,
        timeZoneName: "short",
    }).format(date);
}

function showSuccess(message) {
    alert(message);
}

function showError(message) {
    alert("Error: " + message);
}
