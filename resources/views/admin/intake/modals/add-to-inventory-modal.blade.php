<!-- Add to Inventory Modal -->
@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush

<div id="addToInventoryModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-2 sm:p-4">
    <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="closeAddToInventoryModal()"></div>
            <script>
                // Barcode generation and usage for this shared modal
                (function() {
                    function detectGenerateUrl() {
                        try {
                            if (typeof RefurbishingEntries !== 'undefined' && RefurbishingEntries && RefurbishingEntries.baseUrl) return RefurbishingEntries.baseUrl + '/generate-barcode';
                        } catch (e) {}
                        try {
                            if (typeof EcommerceEntries !== 'undefined' && EcommerceEntries && EcommerceEntries.baseUrl) return EcommerceEntries.baseUrl + '/generate-barcode';
                        } catch (e) {}
                        try {
                            if (typeof UniversalWasteEntries !== 'undefined' && UniversalWasteEntries && UniversalWasteEntries.baseUrl) return UniversalWasteEntries.baseUrl + '/generate-barcode';
                        } catch (e) {}
                        try {
                            if (typeof ItemIntakeManager !== 'undefined' && ItemIntakeManager && ItemIntakeManager.baseUrl) return ItemIntakeManager.baseUrl + '/generate-barcode';
                        } catch (e) {}
                        try {
                            if (typeof app !== 'undefined' && app && app.generateNewBarcode) return '/admin/recycling-intake/get-barcode';
                        } catch (e) {}
                        // Fallback generic
                        return '/admin/recycling-intake/get-barcode';
                    }

                    function setup() {
                        const genBtn = document.getElementById('inv-generate-barcode-btn');
                        const display = document.getElementById('inv-generated-barcode-display');
                        const numberEl = document.getElementById('inv-generated-barcode-number');
                        const textEl = document.getElementById('inv-generated-barcode-text');
                        const imgEl = document.getElementById('inv-generated-barcode-image');
                        const useBtn = document.getElementById('inv-use-generated-barcode-btn');
                        const hiddenInput = document.getElementById('inv-barcode');

                        if (!genBtn) return;

                        async function generate() {
                            const url = detectGenerateUrl();
                            if (!url) return;
                            genBtn.disabled = true;
                            genBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Generating...';
                            try {
                                const resp = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                                const data = await resp.json();
                                if (data.success) {
                                    // Support multiple response shapes:
                                    // { number, base64 } (our inventory generators)
                                    // { barcode, barcodeBase64 } (ItemIntakeController)
                                    const code = data.number || data.barcode || data.barcode_number || '';
                                    const imgBase = data.base64 || (data.barcodeBase64 ? 'data:image/png;base64,' + data.barcodeBase64 : '');
                                    if (numberEl) numberEl.textContent = code;
                                    if (textEl) textEl.textContent = code;
                                    if (imgEl && imgBase) imgEl.src = imgBase;
                                    if (hiddenInput) hiddenInput.value = code;
                                    if (display) display.style.display = 'block';
                                } else {
                                    alert(data.error || data.message || 'Failed to generate barcode');
                                }
                            } catch (e) {
                                console.error(e);
                                alert('Failed to generate barcode');
                            } finally {
                                genBtn.disabled = false;
                                genBtn.innerHTML = '<i class="fas fa-qrcode mr-2 text-lg"></i> Generate Barcode';
                            }
                        }

                        genBtn.addEventListener('click', generate);

                        if (useBtn) {
                            useBtn.addEventListener('click', (e) => {
                                e.preventDefault();
                                useBtn.textContent = 'Barcode Selected!';
                                setTimeout(() => useBtn.textContent = 'Use This Barcode', 900);
                            });
                        }
                    }

                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', setup);
                    } else {
                        setup();
                    }
                })();
            </script>
    <div
        class="relative bg-white rounded-2xl md:rounded-3xl shadow-2xl w-full max-w-4xl max-h-[95vh] overflow-hidden flex flex-col transform transition-all animate-in zoom-in-95">
        <div class="px-6 py-4 border-b flex justify-between items-center sticky top-0 bg-white z-10">
            <h2 class="text-base md:text-xl font-black text-slate-800">Transfer to Inventory</h2>
            <button onclick="closeAddToInventoryModal()" class="h-8 w-8 rounded-full hover:bg-slate-100 text-slate-400"><i
                    class="fas fa-times"></i></button>
        </div>

        <form id="saveToInventoryForm" class="flex-1 overflow-y-auto p-6 md:p-8 space-y-6">
            <input type="hidden" id="inventory-item-id" name="item_id">

            <!-- eBay API Import Dropdown Section -->
            <div class="bg-gradient-to-r from-amber-50 to-blue-50 border border-amber-200/80 rounded-2xl p-4 md:p-5 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center h-9 w-9 rounded-xl bg-amber-500 text-white font-bold text-sm shadow-sm">
                            <i class="fab fa-ebay text-xl"></i>
                        </span>
                        <div>
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-800">Auto-Fill Details from eBay API</h3>
                            <p class="text-[11px] text-slate-500 font-medium">Search & select an item from eBay to auto-fill brand, price, specs, location, and photos.</p>
                        </div>
                    </div>
                    <span id="ebay-import-badge" class="hidden text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800">
                        <i class="fas fa-check-circle mr-1"></i> Data Imported
                    </span>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-slate-500">Search & Select eBay Item</label>
                    <select id="inv-ebay-api-select" class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-amber-500">
                        <option value="">-- Type to search eBay items (e.g. Dell, Laptop, ThinkPad) --</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-slate-400">Technician</label>
                    <input type="text" id="inv-technician" name="technician"
                        class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-slate-400">Customer Phone</label>
                    <input type="text" id="inv-customer-phone" name="customer_phone"
                        class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-slate-400">Customer Email</label>
                    <input type="email" id="inv-customer-email" name="customer_email"
                        class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-slate-400">Device Type</label>
                    <input type="text" id="inv-device-type" name="device_type"
                        class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <input type="hidden" id="inv-category" name="category">
                <input type="hidden" id="inv-sub_category" name="sub_category">
                <input type="hidden" id="inv-sub_sub_category" name="sub_sub_category">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-slate-400">Brand & Model Number</label>
                    <input type="text" id="inv-brand-category" name="brand_category"
                        class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Category Hierarchy Selector --}}
                @include('admin.intake.modals.partials.category-hierarchy-selector')

                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-slate-400">Price</label>
                    <input type="number" step="0.01" id="inv-price" name="price"
                        class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-slate-400">Quantity</label>
                    <input type="number" id="inv-quantity" name="quantity"
                        class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500"
                        min="1" oninput="validateQuantity()">
                    <p id="remaining-qty" class="text-[10px] font-bold text-emerald-600 mt-1">Remaining: -- items</p>
                    <p id="quantity-error" class="text-[10px] font-bold text-red-600 mt-1" style="display: none;">Not
                        enough items available</p>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-slate-400">Status</label>
                    <select id="inv-status" name="status" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm">
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-slate-400">Device Specs</label>
                    <input type="text" id="inv-device-specs" name="device_specs"
                        class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-slate-400">Operating System</label>
                    <input type="text" id="inv-os" name="operating_system"
                        class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-slate-400">Put Away Location</label>
                    <input type="text" id="inv-location" name="put_away_location"
                        class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-bold uppercase text-slate-400">Notes</label>
                <textarea id="inv-notes" name="notes" rows="3" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm italic"></textarea>
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-bold uppercase text-slate-400">Parcel Dimensions (saved for checkout shipping)</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <input id="shippo-length" name="parcel_length" type="number" step="0.01" placeholder="Length" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                    <input id="shippo-width" name="parcel_width" type="number" step="0.01" placeholder="Width" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                    <input id="shippo-height" name="parcel_height" type="number" step="0.01" placeholder="Height" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                    <input id="shippo-weight" name="parcel_weight" type="number" step="0.01" placeholder="Weight" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <select id="shippo-distance-unit" name="parcel_distance_unit" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm">
                        <option value="in">in</option>
                        <option value="cm">cm</option>
                    </select>
                    <select id="shippo-mass-unit" name="parcel_mass_unit" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm">
                        <option value="lb">lb</option>
                        <option value="oz">oz</option>
                        <option value="kg">kg</option>
                        <option value="g">g</option>
                    </select>
                </div>
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-bold uppercase text-slate-400">Repair Photos (<span
                        id="photo-count">0</span>)</label>
                <div id="repair-photos-preview" class="flex flex-wrap gap-3">
                    <label
                        class="cursor-pointer bg-blue-50 text-blue-600 w-20 h-20 rounded-xl flex flex-col items-center justify-center text-[10px] font-bold border-2 border-dashed border-blue-200 hover:bg-blue-100 transition-all add-photos-btn">
                        <i class="fas fa-camera mb-1 text-lg"></i>
                        <span>Add</span>
                        <input type="file" id="inv-photos" multiple accept="image/*" class="hidden">
                    </label>
                </div>
            </div>

            {{-- Barcode Generation Section --}}
            <div class="space-y-3">
                <label class="text-[10px] font-bold uppercase text-slate-400">Generate Barcode</label>
                <div class="bg-blue-50/50 border border-blue-100 rounded-lg px-5 py-3 mb-4 text-blue-600 text-[13px]">
                    Generate a unique barcode for this inventory item.
                </div>

                <button type="button" id="inv-generate-barcode-btn"
                    class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold text-sm mb-4 flex justify-center items-center hover:bg-blue-700 transition-all">
                    <i class="fas fa-qrcode mr-2 text-lg"></i> Generate Barcode
                </button>

                <div id="inv-generated-barcode-display" style="display: none;">
                    <div
                        class="max-w-xs mx-auto border border-slate-100 rounded-2xl py-6 px-4 flex flex-col items-center bg-slate-50/40">
                        <p id="inv-generated-barcode-number" class="text-xl font-bold text-slate-800 mb-4">---</p>
                        <div class="bg-white p-4 border border-slate-200 rounded-xl mb-6 text-center shadow-sm w-full">
                            <img id="inv-generated-barcode-image" src="" class="h-16 md:h-20 max-w-full mx-auto mb-2"
                                alt="barcode">
                            <p id="inv-generated-barcode-text"
                                class="font-mono text-[13px] md:text-[15px] tracking-[0.3em] text-slate-800 font-bold uppercase">
                                ---</p>
                        </div>
                        <button type="button" id="inv-use-generated-barcode-btn"
                            class="mt-2 w-full bg-blue-600 text-white py-2 rounded-lg font-bold text-sm hover:bg-blue-700 transition-all">
                            <i class="fas fa-check mr-2"></i> Use This Barcode
                        </button>
                        <button type="button" id="inv-print-label-btn"
                            class="mt-2 w-full bg-slate-600 text-white py-2 rounded-lg font-bold text-sm hover:bg-slate-700 transition-all">
                            <i class="fas fa-print mr-2"></i> Print Label
                        </button>
                    </div>
                </div>

                <input type="hidden" id="inv-barcode" name="barcode">
            </div>

            <script>
                // Print Label Function - Display in modal on same page
                function printInventoryLabel() {
                    const barcode = document.getElementById('inv-generated-barcode-number').textContent;
                    const barcodeImage = document.getElementById('inv-generated-barcode-image').src;
                    const technician = document.getElementById('inv-technician').value || 'N/A';
                    const deviceType = document.getElementById('inv-device-type').value || 'N/A';
                    const brandModel = document.getElementById('inv-brand-category').value || 'N/A';
                    
                    // Get category name from selected option
                    const categorySelect = document.getElementById('inv-category-select');
                    const category = categorySelect?.options[categorySelect?.selectedIndex]?.text || 'N/A';
                    
                    // Get sub-category name from selected option
                    const subCategorySelect = document.getElementById('inv-subcategory-select');
                    const subCategory = subCategorySelect?.options[subCategorySelect?.selectedIndex]?.text || '';
                    
                    const price = document.getElementById('inv-price').value || 'N/A';
                    const quantity = document.getElementById('inv-quantity').value || 'N/A';
                    const deviceSpecs = document.getElementById('inv-device-specs').value || 'N/A';
                    const os = document.getElementById('inv-os').value || 'N/A';
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

                    // Open print dialog directly without preview modal
                    const printWindow = window.open('', '_blank', 'width=420,height=320');
                    const htmlContent = `
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
                                    <div class="barcode-number">${barcode}</div>
                                </div>
                                <div class="detail-grid">
                                    <div class="detail-item wide">
                                        <span class="detail-label">Brand & Model</span>
                                        <span class="detail-value">${brandModel}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Device</span>
                                        <span class="detail-value">${deviceType}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">OS</span>
                                        <span class="detail-value">${os}</span>
                                    </div>
                                    <div class="detail-item wide">
                                        <span class="detail-label">Specs</span>
                                        <span class="detail-value">${deviceSpecs.substring(0, 48)}</span>
                                    </div>
                                    <div class="detail-item wide">
                                        <span class="detail-label">Category</span>
                                        <span class="detail-value">${category}${subCategory && subCategory !== '-- Select Sub Category --' ? ' > ' + subCategory.substring(0, 24) : ''}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Price</span>
                                        <span class="detail-value">${price === 'N/A' ? 'N/A' : '$' + price}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Qty</span>
                                        <span class="detail-value">${quantity} Unt</span>
                                    </div>
                                    <div class="detail-item wide">
                                        <span class="detail-label">Technician</span>
                                        <span class="detail-value">${technician}</span>
                                    </div>
                                </div>
                                <div class="footer">
                                    ${printedAt}
                                </div>
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
                // Attach print button listener
                document.addEventListener('DOMContentLoaded', function() {
                    const printBtn = document.getElementById('inv-print-label-btn');
                    if (printBtn) {
                        printBtn.addEventListener('click', printInventoryLabel);
                    }
                });
            </script>


            <script>
                // Photo preview management with file accumulation and drag-to-reorder
                window.inventoryPhotoManager = {
                    // Each item: { id: string, file: File, dataUrl: string|null }
                    accumulatedFiles: [],
                    isSetup: false,

                    generateId: function() {
                        return 'ph_' + Date.now().toString(36) + '_' + Math.random().toString(36).slice(2, 8);
                    },

                    setupPhotoUpload: function() {
                        const modal = document.getElementById('addToInventoryModal');
                        if (!modal || this.isSetup) return;

                        const photoInput = modal.querySelector('#inv-photos');
                        if (!photoInput) return;

                        this.isSetup = true;

                        photoInput.addEventListener('change', (e) => {
                            this.handlePhotoSelect(e);
                        });

                        // Reset when modal is hidden
                        const observer = new MutationObserver(() => {
                            if (modal.classList.contains('hidden')) {
                                this.accumulatedFiles = [];
                                document.getElementById('photo-count').textContent = '0';
                                photoInput.value = '';
                                const previewContainer = document.getElementById('repair-photos-preview');
                                if (previewContainer) {
                                    previewContainer.querySelectorAll('[data-photo-wrapper]').forEach(el => el.remove());
                                }
                            }
                        });

                        observer.observe(modal, {
                            attributes: true,
                            attributeFilter: ['class']
                        });
                    },

                    handlePhotoSelect: function(e) {
                        const previewContainer = document.getElementById('repair-photos-preview');
                        if (!previewContainer) return;

                        const newFiles = Array.from(e.target.files || []);

                        // Preserve selection order: create placeholders then fill dataUrls as they load
                        newFiles.forEach(file => {
                            const exists = this.accumulatedFiles.some(item => item.file.name === file.name && item.file.size === file.size && item.file.lastModified === file.lastModified);
                            if (exists) return;

                            const id = this.generateId();
                            const item = { id, file, dataUrl: null };
                            this.accumulatedFiles.push(item);

                            const reader = new FileReader();
                            reader.onload = (event) => {
                                item.dataUrl = event.target.result;
                                this.refreshPhotoPreview();
                            };
                            reader.onerror = () => console.error('Failed to read file:', file.name);
                            reader.readAsDataURL(file);
                        });

                        // Clear input to allow re-selecting same files later
                        e.target.value = '';

                        // Immediately refresh to reflect placeholders (will fill as readers complete)
                        this.refreshPhotoPreview();
                    },

                    refreshPhotoPreview: function() {
                        const previewContainer = document.getElementById('repair-photos-preview');
                        if (!previewContainer) return;
                        const addButton = previewContainer.querySelector('.add-photos-btn');
                        const photoCount = document.getElementById('photo-count');

                        // Remove existing previews
                        previewContainer.querySelectorAll('[data-photo-wrapper]').forEach(el => el.remove());

                        // Update counter
                        if (photoCount) photoCount.textContent = this.accumulatedFiles.length;

                        // Render in current order
                        this.accumulatedFiles.forEach((item, index) => {
                            const wrapper = document.createElement('div');
                            wrapper.setAttribute('data-photo-wrapper', 'true');
                            wrapper.setAttribute('data-id', item.id);
                            wrapper.className = 'relative w-24 h-24 rounded-2xl overflow-hidden border-2 border-blue-400 shadow-md group';
                            wrapper.draggable = true;

                            // Drag handlers for reordering
                            wrapper.addEventListener('dragstart', (e) => {
                                e.dataTransfer.setData('text/plain', item.id);
                                wrapper.classList.add('opacity-50');
                            });
                            wrapper.addEventListener('dragend', () => wrapper.classList.remove('opacity-50'));
                            wrapper.addEventListener('dragover', (e) => e.preventDefault());
                            wrapper.addEventListener('drop', (e) => {
                                e.preventDefault();
                                const draggedId = e.dataTransfer.getData('text/plain');
                                const targetId = item.id;
                                if (!draggedId || draggedId === targetId) return;
                                this.reorderById(draggedId, targetId);
                            });

                            const img = document.createElement('img');
                            img.className = 'w-full h-full object-cover';
                            img.alt = (item.file && item.file.name) ? item.file.name : (item.filename || 'Photo');
                            if (item.dataUrl) {
                                img.src = item.dataUrl;
                            } else {
                                // Placeholder while loading
                                img.src = '';
                                img.style.background = '#f3f4f6';
                            }
                            wrapper.appendChild(img);

                            const removeBtn = document.createElement('button');
                            removeBtn.type = 'button';
                            removeBtn.title = 'Remove photo';
                            removeBtn.className = 'absolute top-1 right-1 w-5 h-5 bg-red-500 text-white rounded-full text-xs flex items-center justify-center hover:bg-red-600 transition-all opacity-0 group-hover:opacity-100 z-10 shadow-sm';
                            removeBtn.innerHTML = '<i class="fas fa-times text-[8px]"></i>';
                            removeBtn.addEventListener('click', (e) => {
                                e.preventDefault();
                                e.stopPropagation();
                                this.removeById(item.id);
                            });
                            wrapper.appendChild(removeBtn);

                            const rotateBtn = document.createElement('button');
                            rotateBtn.type = 'button';
                            rotateBtn.title = 'Rotate 90° Clockwise';
                            rotateBtn.className = 'rotate-btn absolute bottom-1 right-1 w-6 h-6 bg-blue-600 text-white rounded-full text-xs flex items-center justify-center hover:bg-blue-700 transition-all opacity-90 group-hover:opacity-100 z-10 shadow-md';
                            rotateBtn.innerHTML = '<i class="fas fa-redo text-[9px]"></i>';
                            rotateBtn.addEventListener('click', (e) => {
                                e.preventDefault();
                                e.stopPropagation();
                                this.rotateById(item.id);
                            });
                            wrapper.appendChild(rotateBtn);

                            // Insert before add button so add button remains last
                            if (addButton) previewContainer.insertBefore(wrapper, addButton);
                            else previewContainer.appendChild(wrapper);
                        });
                    },

                    rotateById: function(id) {
                        const item = this.accumulatedFiles.find(it => it.id === id);
                        if (!item || !item.dataUrl) return;

                        const wrapper = document.querySelector(`[data-photo-wrapper][data-id="${id}"]`);
                        if (wrapper) {
                            const icon = wrapper.querySelector('.rotate-btn i');
                            if (icon) icon.classList.add('fa-spin');
                        }

                        const img = new Image();
                        img.crossOrigin = "anonymous";
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            const ctx = canvas.getContext('2d');

                            canvas.width = img.height;
                            canvas.height = img.width;

                            ctx.translate(canvas.width / 2, canvas.height / 2);
                            ctx.rotate((90 * Math.PI) / 180);
                            ctx.drawImage(img, -img.width / 2, -img.height / 2);

                            const newUrl = canvas.toDataURL('image/jpeg', 0.92);
                            item.dataUrl = newUrl;

                            fetch(newUrl)
                                .then(res => res.blob())
                                .then(blob => {
                                    const fileName = (item.file && item.file.name) ? item.file.name : (item.filename || 'photo.jpg');
                                    item.file = new File([blob], fileName, { type: 'image/jpeg' });
                                    this.refreshPhotoPreview();
                                })
                                .catch(err => {
                                    console.error('Error converting rotated image blob:', err);
                                    this.refreshPhotoPreview();
                                });
                        };
                        img.onerror = (err) => {
                            console.error('Error loading image for rotation:', err);
                            this.refreshPhotoPreview();
                        };
                        img.src = item.dataUrl;
                    },

                    removeById: function(id) {
                        const idx = this.accumulatedFiles.findIndex(it => it.id === id);
                        if (idx > -1) this.accumulatedFiles.splice(idx, 1);
                        this.refreshPhotoPreview();
                    },

                    reorderById: function(draggedId, targetId) {
                        const arr = this.accumulatedFiles;
                        const from = arr.findIndex(i => i.id === draggedId);
                        const to = arr.findIndex(i => i.id === targetId);
                        if (from === -1 || to === -1) return;
                        const [item] = arr.splice(from, 1);
                        arr.splice(to, 0, item);
                        this.refreshPhotoPreview();
                    },

                    getFiles: async function() {
                        if (!this.accumulatedFiles || this.accumulatedFiles.length === 0) {
                            return [];
                        }

                        const hasAnyCustomFile = this.accumulatedFiles.some(it => it.file instanceof File);
                        if (!hasAnyCustomFile) {
                            return [];
                        }

                        const filePromises = this.accumulatedFiles.map(async (item, idx) => {
                            if (item.file instanceof File) {
                                return item.file;
                            }
                            if (item.dataUrl || item.existingUrl) {
                                try {
                                    const url = item.dataUrl || item.existingUrl;
                                    const res = await fetch(url);
                                    const blob = await res.blob();
                                    const filename = item.filename || `photo_${idx + 1}.jpg`;
                                    return new File([blob], filename, { type: blob.type || 'image/jpeg' });
                                } catch (e) {
                                    console.error('Failed to convert image to File blob:', e);
                                    return null;
                                }
                            }
                            return null;
                        });

                        const results = await Promise.all(filePromises);
                        return results.filter(f => f !== null);
                    }
                };

                // Initialize
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', () => {
                        window.inventoryPhotoManager.setupPhotoUpload();
                    });
                } else {
                    window.inventoryPhotoManager.setupPhotoUpload();
                }
            </script>

            <script>
                // eBay API Select2 Auto-Fill Handler
                (function() {
                    function initEbayApiSelect() {
                        const $select = $('#inv-ebay-api-select');
                        if (!$select.length || $select.data('select2')) return;

                        if (typeof $.fn.select2 === 'undefined') {
                            setTimeout(initEbayApiSelect, 200);
                            return;
                        }

                        $select.select2({
                            placeholder: 'Type to search eBay API items (e.g. Dell, Laptop, ThinkPad)...',
                            allowClear: true,
                            dropdownParent: $('#addToInventoryModal'),
                            ajax: {
                                url: '/ebay/products',
                                dataType: 'json',
                                delay: 350,
                                data: function (params) {
                                    return {
                                        q: params.term || 'laptop',
                                        format: 'json'
                                    };
                                },
                                processResults: function (data) {
                                    return {
                                        results: data.results || []
                                    };
                                },
                                cache: true
                            },
                            minimumInputLength: 1
                        });

                        $select.on('select2:select', function (e) {
                            const data = e.params.data;
                            if (!data || !data.item) return;

                            const item = data.item;

                            // 1. Device Type (#inv-device-type) -> Full eBay Item Title / Name
                            if (item.title) {
                                $('#inv-device-type').val(item.title).trigger('change');
                                $('#inv-brand-category').val(item.title).trigger('change');
                            }

                            // 2. Price (#inv-price)
                            let priceVal = 0;
                            if (item.price && isFinite(item.price.value)) {
                                priceVal = parseFloat(item.price.value);
                            }
                            if (priceVal > 0) {
                                $('#inv-price').val(priceVal).trigger('change');
                            }

                            // 3. Category Name for Hierarchy Selector
                            let categoryName = '';
                            if (item.categories && item.categories.length > 0) {
                                const primaryCat = item.categories.find(c => c.categoryName && !c.categoryName.includes('& Networking')) || item.categories[0];
                                categoryName = primaryCat.categoryName || '';
                            }

                            // Auto-select Category Hierarchy tree if matching category exists
                            if (window.categoryHierarchyManager && window.categoryHierarchyManager.categoryMap) {
                                const catMap = window.categoryHierarchyManager.categoryMap;
                                let matchedCatId = null;
                                let matchedSubCatId = null;

                                for (const id in catMap) {
                                    const cat = catMap[id];
                                    if (categoryName && cat.name && (cat.name.toLowerCase().includes(categoryName.toLowerCase()) || categoryName.toLowerCase().includes(cat.name.toLowerCase()))) {
                                        if (cat.parent_id) {
                                            matchedCatId = cat.parent_id;
                                            matchedSubCatId = cat.id;
                                        } else {
                                            matchedCatId = cat.id;
                                        }
                                        break;
                                    }
                                }

                                if (matchedCatId) {
                                    window.categoryHierarchyManager.selectPresetCategories(matchedCatId, matchedSubCatId);
                                }
                            }

                            // 4. Clean Device Specs (#inv-device-specs)
                            let specParts = [];
                            if (item.condition && item.condition !== 'N/A') {
                                specParts.push('Condition: ' + item.condition);
                            }

                            if (item.shortDescription) {
                                specParts.push(item.shortDescription);
                            } else if (item.localizedAspects && Array.isArray(item.localizedAspects)) {
                                const keyAspects = item.localizedAspects
                                    .filter(a => a.name && ['Processor', 'RAM Size', 'Storage Type', 'SSD Capacity', 'Hard Drive Capacity', 'Screen Size', 'GPU'].includes(a.name))
                                    .map(a => a.name + ': ' + a.value);
                                if (keyAspects.length) {
                                    specParts.push(keyAspects.join(', '));
                                }
                            }

                            if (item.title) {
                                const techDetails = [];
                                const ramMatch = item.title.match(/(\d+\s*(?:GB|TB)\s*(?:RAM|DDR\d)?)/i);
                                const storageMatch = item.title.match(/(\d+\s*(?:GB|TB)\s*(?:SSD|HDD|NVMe))/i);
                                const cpuMatch = item.title.match(/(i[3579]-\d+\w*|Ryzen\s*[3579]\s*\d+\w*|M[123]\s*(?:Pro|Max)?)/i);

                                if (ramMatch && !specParts.some(p => p.includes('RAM'))) techDetails.push(ramMatch[0]);
                                if (storageMatch && !specParts.some(p => p.includes('SSD') || p.includes('HDD'))) techDetails.push(storageMatch[0]);
                                if (cpuMatch && !specParts.some(p => p.includes('Intel') || p.includes('Ryzen'))) techDetails.push(cpuMatch[0]);

                                if (techDetails.length) {
                                    specParts.push(techDetails.join(' | '));
                                }
                            }

                            const finalSpecs = specParts.length ? specParts.join(' | ') : (item.condition ? 'Condition: ' + item.condition : '');
                            $('#inv-device-specs').val(finalSpecs).trigger('change');

                            // 5. Operating System (#inv-os)
                            if (item.title) {
                                const t = item.title.toLowerCase();
                                if (t.includes('windows 11') || t.includes('win 11')) $('#inv-os').val('Windows 11');
                                else if (t.includes('windows 10') || t.includes('win 10')) $('#inv-os').val('Windows 10');
                                else if (t.includes('macbook') || t.includes('macos')) $('#inv-os').val('macOS');
                                else if (t.includes('chromebook')) $('#inv-os').val('ChromeOS');
                                else $('#inv-os').val('');
                            }

                            // 6. Put Away Location (#inv-location) - Clear pre-filled city names unless specified in item
                            if (item.itemLocation && (item.itemLocation.country || item.itemLocation.postalCode)) {
                                const locParts = [];
                                if (item.itemLocation.postalCode) locParts.push(item.itemLocation.postalCode);
                                if (item.itemLocation.country) locParts.push(item.itemLocation.country);
                                $('#inv-location').val(locParts.join(', '));
                            } else {
                                $('#inv-location').val('');
                            }

                            // 7. Notes (#inv-notes) -> Clean description or empty for technician notes
                            let notesText = item.shortDescription || item.description || '';
                            $('#inv-notes').val(notesText);

                            // 8. Photo Manager Reset & Direct Auto-Load of eBay photos
                            if (window.inventoryPhotoManager) {
                                window.inventoryPhotoManager.accumulatedFiles = [];

                                let photoUrls = [];
                                if (item.images && item.images.length > 0) {
                                    item.images.forEach(imgObj => {
                                        if (imgObj.imageUrl) photoUrls.push(imgObj.imageUrl);
                                    });
                                } else if (item.thumbnail) {
                                    photoUrls.push(item.thumbnail);
                                }

                                photoUrls.forEach((imgUrl, idx) => {
                                    const photoId = window.inventoryPhotoManager.generateId();
                                    window.inventoryPhotoManager.accumulatedFiles.push({
                                        id: photoId,
                                        file: { name: 'eBay Photo ' + (idx + 1) },
                                        dataUrl: imgUrl,
                                        existingUrl: imgUrl
                                    });
                                });

                                window.inventoryPhotoManager.refreshPhotoPreview();
                            }

                            // Show data imported badge
                            $('#ebay-import-badge').removeClass('hidden');
                        });

                        $select.on('select2:clear', function() {
                            $('#ebay-import-badge').addClass('hidden');
                        });
                    }

                    // Setup listener when modal opens
                    const modal = document.getElementById('addToInventoryModal');
                    if (modal) {
                        const observer = new MutationObserver(() => {
                            if (!modal.classList.contains('hidden')) {
                                setTimeout(initEbayApiSelect, 150);
                            }
                        });
                        observer.observe(modal, { attributes: true, attributeFilter: ['class'] });
                    }

                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', initEbayApiSelect);
                    } else {
                        initEbayApiSelect();
                    }
                })();
            </script>

            <div
                class="pt-4 flex flex-col sm:flex-row justify-end gap-3 sticky bottom-0 bg-white border-t mt-auto py-4">
                <button type="button" onclick="closeAddToInventoryModal()"
                    class="px-6 py-3 text-xs font-bold text-slate-500 order-2 sm:order-1">Cancel</button>
                <button type="submit" id="inv-submit-btn" disabled
                    class="px-10 py-3 bg-emerald-600 text-white rounded-xl text-xs font-bold shadow-lg shadow-emerald-200 order-1 sm:order-2 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i id="inv-submit-spinner" class="fas fa-spinner fa-spin hidden"></i>
                    <span id="inv-submit-text">Save to Inventory</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Shippo Rates Modal -->
<div id="shippoRatesModal" class="hidden fixed inset-0 z-[120] flex items-center justify-center p-3 sm:p-4">
    <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" id="shippo-rates-backdrop"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[92vh] overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-base font-black text-slate-800">Shipping Rates (Shippo)</h3>
            <button type="button" id="close-shippo-rates-btn" class="h-8 w-8 rounded-full hover:bg-slate-100 text-slate-500">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-5 overflow-y-auto space-y-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <input id="shippo-length" type="number" step="0.01" placeholder="Length (e.g. 16.5)" class="bg-slate-50 rounded-lg px-3 py-2 text-sm">
                <input id="shippo-width" type="number" step="0.01" placeholder="Width (e.g. 12)" class="bg-slate-50 rounded-lg px-3 py-2 text-sm">
                <input id="shippo-height" type="number" step="0.01" placeholder="Height (e.g. 4.5)" class="bg-slate-50 rounded-lg px-3 py-2 text-sm">
                <input id="shippo-weight" type="number" step="0.01" placeholder="Weight (e.g. 7.25)" class="bg-slate-50 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <select id="shippo-distance-unit" class="bg-slate-50 rounded-lg px-3 py-2 text-sm">
                    <option value="in">in</option>
                    <option value="cm">cm</option>
                </select>
                <select id="shippo-mass-unit" class="bg-slate-50 rounded-lg px-3 py-2 text-sm">
                    <option value="lb">lb</option>
                    <option value="oz">oz</option>
                    <option value="kg">kg</option>
                    <option value="g">g</option>
                </select>
                <input id="shippo-to-city" type="text" placeholder="City (e.g. Denver)" class="bg-slate-50 rounded-lg px-3 py-2 text-sm">
                <input id="shippo-to-state" type="text" placeholder="State (e.g. CO)" class="bg-slate-50 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="grid grid-cols-1 gap-3">
                <input id="shippo-to-street1" type="text" placeholder="Street Address (e.g. 123 Main St)" class="bg-slate-50 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <input id="shippo-to-zip" type="text" placeholder="ZIP Code (e.g. 80223)" class="bg-slate-50 rounded-lg px-3 py-2 text-sm">
                <input id="shippo-to-country" type="text" value="US" placeholder="Country Code (e.g. US)" class="bg-slate-50 rounded-lg px-3 py-2 text-sm uppercase">
            </div>
            <button type="button" id="fetch-shippo-rates-btn"
                class="w-full px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold">
                <i class="fas fa-search-dollar mr-2"></i>Fetch Rates
            </button>
            <div id="shippo-rates-loading" class="hidden text-sm text-slate-500 font-semibold">Fetching rates...</div>
            <div id="shippo-rates-error" class="hidden text-sm text-red-600 font-semibold"></div>
            <div id="shippo-rates-list" class="space-y-2"></div>
        </div>
    </div>
</div>
