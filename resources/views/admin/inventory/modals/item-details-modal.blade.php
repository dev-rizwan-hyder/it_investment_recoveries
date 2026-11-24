<!-- Item Details Modal -->
<div id="itemDetailsModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-2 sm:p-3">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeItemDetailsModal()"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-[97vw] 2xl:max-w-[1600px] h-[95vh] max-h-[96vh] overflow-hidden flex flex-col transform transition-all animate-in zoom-in-95 duration-200">
        <!-- Modal Header -->
        <div class="px-6 py-2.5 border-b border-slate-100 flex justify-between items-center bg-white shrink-0">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-black text-xs">
                    <i class="fas fa-eye"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm font-black text-slate-900 tracking-tight leading-none uppercase">Inventory Details</h2>
                        <span class="text-[10px] bg-blue-50 text-blue-600 font-black px-2 py-0.5 rounded uppercase tracking-wider">Barcode: <span id="detail-barcode"></span></span>
                        <span id="detail-header-intake" class="hidden text-[10px] bg-slate-100 text-slate-600 font-bold px-2 py-0.5 rounded uppercase"></span>
                        <a id="detail-header-store-link" href="#" target="_blank" class="hidden text-[10px] bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-black px-2 py-0.5 rounded uppercase tracking-wider items-center gap-1 transition-all" title="View product on Denver Discount Computers">
                            <i class="fas fa-store text-[9px]"></i>
                            <span>Store Front</span>
                            <i class="fas fa-external-link-alt text-[8px]"></i>
                        </a>
                    </div>
                    <p id="detail-name-label" class="text-xs font-semibold text-slate-500 truncate max-w-2xl mt-0.5"></p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <div id="detail-header-invoice-actions" class="flex items-center gap-1.5"></div>
                <button type="button" onclick="inventoryManager.editItem(inventoryManager.selectedItem?.id)" class="h-8 px-3 rounded-lg bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white font-bold text-xs flex items-center gap-1.5 transition-all active:scale-95">
                    <i class="fas fa-edit text-xs"></i>
                    <span class="hidden sm:inline">Edit</span>
                </button>
                <button onclick="closeItemDetailsModal()" class="h-8 w-8 flex items-center justify-center rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-all">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body (3-Column Full View Layout matching Edit Modal) -->
        <div class="flex flex-col flex-1 overflow-hidden bg-white">
            <div class="flex-1 overflow-y-auto p-4 grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
                
                <!-- COLUMN 1: Device & Customer Details (4 Cols) -->
                <div class="lg:col-span-4 bg-slate-50/70 p-3.5 rounded-xl border border-slate-100 space-y-2.5">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-4 h-[1.5px] bg-blue-500"></span> Device & Customer
                    </p>

                    <div class="grid grid-cols-2 gap-2">
                        <div class="space-y-0.5">
                            <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Technician</label>
                            <div id="detail-technician" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-slate-800 min-h-[30px] flex items-center truncate">-</div>
                        </div>
                        <div class="space-y-0.5">
                            <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Customer Phone</label>
                            <div id="detail-customer-phone" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-slate-800 min-h-[30px] flex items-center truncate">-</div>
                        </div>
                    </div>

                    <div class="space-y-0.5">
                        <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Customer Email</label>
                        <div id="detail-customer-email" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-slate-800 min-h-[30px] flex items-center truncate">-</div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div class="space-y-0.5">
                            <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Device Type</label>
                            <div id="detail-device-type" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-slate-800 min-h-[30px] flex items-center truncate">-</div>
                        </div>
                        <div class="space-y-0.5">
                            <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Operating System</label>
                            <div id="detail-operating-system" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-slate-800 min-h-[30px] flex items-center truncate">-</div>
                        </div>
                    </div>

                    <div class="space-y-0.5">
                        <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Brand & Model Number</label>
                        <div id="detail-brand-model" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-slate-800 min-h-[30px] flex items-center truncate">-</div>
                    </div>

                    <div class="space-y-0.5">
                        <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Device Specs</label>
                        <div id="detail-device-specs" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-slate-800 min-h-[30px] flex items-center break-words leading-relaxed">-</div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div class="space-y-0.5">
                            <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Status</label>
                            <div id="detail-status-pill" class="w-full min-h-[30px] flex items-center">
                                <span id="detail-status" class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider bg-slate-100 text-slate-700">In Stock</span>
                            </div>
                        </div>
                        <div class="space-y-0.5">
                            <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Put Away Location</label>
                            <div id="detail-put-away-location" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-bold text-slate-800 min-h-[30px] flex items-center truncate">-</div>
                        </div>
                    </div>
                </div>

                <!-- COLUMN 2: Category, Stock & Parcel Dimensions (4 Cols) -->
                <div class="lg:col-span-4 space-y-2.5">
                    <!-- Category Hierarchy Card -->
                    <div class="bg-slate-50/70 p-3.5 rounded-xl border border-slate-100 space-y-2">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <span class="w-4 h-[1.5px] bg-emerald-500"></span> Category Hierarchy
                        </p>
                        <div class="space-y-1.5">
                            <div class="space-y-0.5">
                                <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Category</label>
                                <div id="detail-category" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-slate-800 min-h-[30px] flex items-center truncate">-</div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="space-y-0.5">
                                    <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Sub Category</label>
                                    <div id="detail-sub-category" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-slate-800 min-h-[30px] flex items-center truncate">-</div>
                                </div>
                                <div class="space-y-0.5">
                                    <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Sub Sub Category</label>
                                    <div id="detail-sub-sub-category" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-slate-800 min-h-[30px] flex items-center truncate">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Pallet Card -->
                    <div class="bg-slate-50/70 p-3.5 rounded-xl border border-slate-100 space-y-2.5">
                        <div class="grid grid-cols-2 gap-2">
                            <div class="space-y-0.5">
                                <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Price ($)</label>
                                <div id="detail-price" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-black text-slate-900 min-h-[30px] flex items-center">$0.00</div>
                            </div>
                            <div class="space-y-0.5">
                                <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Pallet Number</label>
                                <div id="detail-pallet-number" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-slate-800 min-h-[30px] flex items-center truncate">-- No Pallet --</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-200/50">
                            <div class="space-y-0.5">
                                <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Quantity</label>
                                <div id="detail-quantity" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-black text-slate-900 min-h-[30px] flex items-center">0</div>
                            </div>
                            <div class="space-y-0.5">
                                <label class="text-[9px] font-bold uppercase text-blue-500 tracking-wider">Remaining</label>
                                <div id="detail-remaining-display" class="w-full bg-blue-50/80 border border-blue-100 rounded-lg px-2.5 py-1.5 text-xs font-black text-blue-700 min-h-[30px] flex items-center">0 items</div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Parcel Dimensions -->
                    <div class="bg-slate-50/70 p-3 rounded-xl border border-slate-100 space-y-1.5">
                        <div class="flex justify-between items-center">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Parcel Dimensions</p>
                            <span class="text-[9px] font-black text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">in / lb</span>
                        </div>
                        <div class="grid grid-cols-4 gap-1.5">
                            <div class="space-y-0.5">
                                <label class="text-[8px] font-black uppercase text-slate-400">L</label>
                                <div id="detail-parcel-length" class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1 text-xs font-bold text-slate-900 text-center min-h-[28px] flex items-center justify-center">-</div>
                            </div>
                            <div class="space-y-0.5">
                                <label class="text-[8px] font-black uppercase text-slate-400">W</label>
                                <div id="detail-parcel-width" class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1 text-xs font-bold text-slate-900 text-center min-h-[28px] flex items-center justify-center">-</div>
                            </div>
                            <div class="space-y-0.5">
                                <label class="text-[8px] font-black uppercase text-slate-400">H</label>
                                <div id="detail-parcel-height" class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1 text-xs font-bold text-slate-900 text-center min-h-[28px] flex items-center justify-center">-</div>
                            </div>
                            <div class="space-y-0.5">
                                <label class="text-[8px] font-black uppercase text-slate-400">Wt</label>
                                <div id="detail-parcel-weight" class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1 text-xs font-bold text-slate-900 text-center min-h-[28px] flex items-center justify-center">-</div>
                            </div>
                        </div>
                    </div>
                    <!-- Sales Invoice Information Card -->
                    <div id="detail-invoice-card" class="bg-slate-50/70 p-3.5 rounded-xl border border-slate-100 space-y-2">
                        <div class="flex justify-between items-center">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <span class="w-4 h-[1.5px] bg-amber-500"></span> Sales Invoice
                            </p>
                            <span id="detail-invoice-status-badge" class="hidden text-[10px] font-black px-2 py-0.5 rounded uppercase"></span>
                        </div>
                        <div id="detail-invoice-content" class="space-y-2">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>
                </div>

                <!-- COLUMN 3: Notes & Repair Photos (4 Cols) -->
                <div class="lg:col-span-4 bg-slate-50/70 p-3.5 rounded-xl border border-slate-100 flex flex-col justify-between space-y-2.5">
                    <div class="space-y-0.5">
                        <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Notes</label>
                        <div id="detail-notes" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs italic font-medium text-slate-600 min-h-[60px] max-h-[90px] overflow-y-auto leading-relaxed whitespace-pre-wrap">No notes provided for this item.</div>
                    </div>

                    <div class="space-y-2 flex-1 flex flex-col">
                        <div class="flex justify-between items-center">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-1.5">
                                <span class="w-4 h-[1.5px] bg-purple-500"></span> Photos & Media
                            </p>
                            <span id="detail-repair-photo-count" class="text-[10px] font-bold text-slate-400">(0)</span>
                        </div>
                        <div class="flex-1 bg-white p-2.5 rounded-xl border border-slate-200/80 min-h-[160px] max-h-[220px] overflow-y-auto custom-scrollbar">
                            <div class="flex flex-wrap gap-2.5" id="detail-repair-photos"></div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer (Sticky Bottom matching Edit Modal) -->
            <div class="px-6 py-2.5 bg-slate-50 border-t border-slate-100 flex justify-between items-center shrink-0">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Intake:</span>
                    <span id="detail-footer-intake" class="text-[10px] font-extrabold text-slate-700 bg-white border border-slate-200 px-2 py-0.5 rounded uppercase">--</span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider ml-2">Created:</span>
                    <span id="detail-footer-date" class="text-[10px] font-bold text-slate-600">--</span>
                </div>
                <div class="flex items-center gap-2 sm:gap-3">
                    <div id="detail-footer-invoice-actions" class="flex items-center gap-2"></div>
                    <button type="button" onclick="closeItemDetailsModal()" class="text-[11px] font-black text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest px-3 sm:px-4 py-2">Close</button>
                    <button type="button" onclick="inventoryManager.editItem(inventoryManager.selectedItem?.id)" class="px-6 sm:px-8 py-2.5 bg-blue-600 text-white rounded-xl text-[11px] font-black hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all active:scale-95 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-edit text-xs"></i>
                        <span>Edit Item</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
