<!-- Edit Item Modal -->
<div id="editItemModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-2 sm:p-3">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeEditItemModal()"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-[97vw] 2xl:max-w-[1600px] h-[95vh] max-h-[96vh] overflow-hidden flex flex-col transform transition-all animate-in zoom-in-95 duration-200">
        <!-- Modal Header -->
        <div class="px-6 py-2.5 border-b border-slate-100 flex justify-between items-center bg-white shrink-0">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-black text-xs shrink-0">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm font-black text-slate-900 tracking-tight leading-none uppercase">Edit Inventory</h2>
                        <span class="text-[10px] bg-blue-50 text-blue-600 font-black px-2 py-0.5 rounded uppercase tracking-wider">Barcode: <span id="edit-barcode"></span></span>
                    </div>
                    <p id="edit-name-label" class="text-xs font-semibold text-slate-500 truncate max-w-2xl mt-0.5"></p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="backFromEditItemModal()" class="h-8 px-3 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-slate-900 font-bold text-xs flex items-center gap-1.5 transition-all active:scale-95 border border-slate-200/80 shadow-sm" title="Back">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Back</span>
                </button>
                <button type="button" onclick="closeEditItemModal()" class="h-8 w-8 flex items-center justify-center rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-all" title="Close">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>

        <!-- Modal Form (3-Column Full View Layout) -->
        <form id="editItemForm" class="flex flex-col flex-1 overflow-hidden bg-white">
            <input type="hidden" id="edit-name">
            <input type="hidden" id="edit-serial">
            <input type="hidden" id="inv-category">
            <input type="hidden" id="inv-sub_category">
            <input type="hidden" id="inv-sub_sub_category">

            <div class="flex-1 overflow-y-auto p-4 grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
                
                <!-- COLUMN 1: Device & Customer Details (4 Cols) -->
                <div class="lg:col-span-4 bg-slate-50/70 p-3.5 rounded-xl border border-slate-100 space-y-2.5">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-4 h-[1.5px] bg-blue-500"></span> Device & Customer
                    </p>

                    <div class="grid grid-cols-2 gap-2">
                        <div class="space-y-0.5">
                            <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Technician</label>
                            <input type="text" id="edit-technician" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold outline-none focus:border-blue-400 transition-all">
                        </div>
                        <div class="space-y-0.5">
                            <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Customer Phone</label>
                            <input type="text" id="edit-customer-phone" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold outline-none focus:border-blue-400">
                        </div>
                    </div>

                    <div class="space-y-0.5">
                        <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Customer Email</label>
                        <input type="email" id="edit-customer-email" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold outline-none focus:border-blue-400">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div class="space-y-0.5">
                            <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Device Type</label>
                            <input type="text" id="edit-device-type" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold outline-none focus:border-blue-400">
                        </div>
                        <div class="space-y-0.5">
                            <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Operating System</label>
                            <input type="text" id="edit-operating-system" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold outline-none focus:border-blue-400">
                        </div>
                    </div>

                    <div class="space-y-0.5">
                        <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Brand & Model Number</label>
                        <input type="text" id="edit-brand-category" placeholder="Brand & Model" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold outline-none focus:border-blue-400">
                    </div>

                    <div class="space-y-0.5">
                        <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Device Specs</label>
                        <input type="text" id="edit-device-specs" placeholder="CPU, RAM, Storage" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold outline-none focus:border-blue-400">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div class="space-y-0.5">
                            <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Status</label>
                            <select id="edit-status" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold outline-none cursor-pointer focus:border-blue-400">
                                <option value="In Stock">IN STOCK</option>
                                <option value="Out of Stock">OUT OF STOCK</option>
                            </select>
                        </div>
                        <div class="space-y-0.5">
                            <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Put Away Location</label>
                            <input type="text" id="edit-put-away-location" placeholder="e.g. 9A/B3" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold outline-none focus:border-blue-400">
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
                            @include('admin.intake.modals.partials.category-hierarchy-selector')
                        </div>
                    </div>

                    <!-- Pricing & Pallet Card -->
                    <div class="bg-slate-50/70 p-3.5 rounded-xl border border-slate-100 space-y-2.5">
                        <div class="grid grid-cols-2 gap-2">
                            <div class="space-y-0.5">
                                <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Price ($)</label>
                                <input type="number" step="0.01" id="edit-price" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-black text-slate-900 outline-none focus:border-blue-400">
                            </div>
                            <div class="space-y-0.5">
                                <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Pallet Number</label>
                                <select id="edit-pallet-number" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold outline-none focus:border-blue-400">
                                    <option value="">-- No Pallet --</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-200/50">
                            <div class="space-y-0.5">
                                <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Quantity</label>
                                <input type="number" id="edit-quantity" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs font-black text-slate-900 outline-none focus:border-blue-400">
                            </div>
                            <div class="space-y-0.5">
                                <label class="text-[9px] font-bold uppercase text-blue-500 tracking-wider">Remaining</label>
                                <input type="text" id="edit-remaining-display" readonly class="w-full bg-blue-50/80 border border-blue-100 rounded-lg px-2.5 py-1.5 text-xs font-black text-blue-700 outline-none">
                                <span id="edit-remaining-items" class="hidden">0 items</span>
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
                                <input type="number" step="0.01" id="edit-parcel-length" placeholder="Len" class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1 text-xs font-bold text-slate-900 outline-none focus:border-blue-400 text-center">
                            </div>
                            <div class="space-y-0.5">
                                <label class="text-[8px] font-black uppercase text-slate-400">W</label>
                                <input type="number" step="0.01" id="edit-parcel-width" placeholder="Wid" class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1 text-xs font-bold text-slate-900 outline-none focus:border-blue-400 text-center">
                            </div>
                            <div class="space-y-0.5">
                                <label class="text-[8px] font-black uppercase text-slate-400">H</label>
                                <input type="number" step="0.01" id="edit-parcel-height" placeholder="Hgt" class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1 text-xs font-bold text-slate-900 outline-none focus:border-blue-400 text-center">
                            </div>
                            <div class="space-y-0.5">
                                <label class="text-[8px] font-black uppercase text-slate-400">Wt</label>
                                <input type="number" step="0.01" id="edit-parcel-weight" placeholder="Lbs" class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1 text-xs font-bold text-slate-900 outline-none focus:border-blue-400 text-center">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- COLUMN 3: Notes & Repair Photos (4 Cols) -->
                <div class="lg:col-span-4 bg-slate-50/70 p-3.5 rounded-xl border border-slate-100 flex flex-col justify-between space-y-2.5">
                    <div class="space-y-0.5">
                        <label class="text-[9px] font-bold uppercase text-slate-400 tracking-wider">Notes</label>
                        <textarea id="edit-notes" rows="2" placeholder="Item notes..." class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs italic font-medium text-slate-600 outline-none resize-none focus:border-blue-400"></textarea>
                    </div>

                    <div class="space-y-2 flex-1 flex flex-col">
                        <div class="flex justify-between items-center">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-1.5">
                                <span class="w-4 h-[1.5px] bg-purple-500"></span> Repair Photos
                            </p>
                            <span id="edit-repair-photo-count" class="text-[10px] font-bold text-slate-400">(0)</span>
                        </div>
                        <div class="flex-1 bg-white p-2.5 rounded-xl border border-slate-200/80 min-h-[160px] max-h-[220px] overflow-y-auto custom-scrollbar">
                            <div class="flex flex-wrap gap-2.5" id="edit-photos-container"></div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer (Sticky Bottom) -->
            <div class="px-6 py-2.5 bg-slate-50 border-t border-slate-100 flex justify-end items-center gap-2.5 sm:gap-3 shrink-0">
                <button type="button" onclick="backFromEditItemModal()" class="px-4 sm:px-5 py-2.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-xl text-[11px] font-black uppercase tracking-wider flex items-center gap-1.5 transition-all active:scale-95 shadow-sm">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Back</span>
                </button>
                <button type="button" onclick="closeEditItemModal()" class="text-[11px] font-black text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest px-3 sm:px-4 py-2">Discard</button>
                <button type="submit" id="edit-submit-btn" class="px-6 sm:px-8 py-2.5 bg-blue-600 text-white rounded-xl text-[11px] font-black hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all active:scale-95 uppercase tracking-wider flex items-center gap-2">
                    <span id="edit-submit-text">Update Record</span>
                    <i id="edit-submit-spinner" class="fas fa-spinner fa-spin hidden"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<div id="inventoryPhotoEditorModal" class="hidden fixed inset-0 z-[130] flex items-center justify-center p-3 sm:p-6">
    <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm" onclick="inventoryManager.closeInventoryPhotoEditor()"></div>

    <div class="relative bg-white rounded-[1.25rem] shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Image Editor</h3>
                <p id="inventory-photo-editor-label" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Repair photo</p>
            </div>
            <button type="button" onclick="inventoryManager.closeInventoryPhotoEditor()" class="h-9 w-9 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-400 transition-all">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_280px] gap-0 overflow-y-auto">
            <div class="bg-slate-100 min-h-[320px] flex items-center justify-center p-4">
                <canvas id="inventory-photo-editor-canvas" class="max-w-full max-h-[62vh] rounded-xl shadow-sm" style="background-color:#fff;background-image:linear-gradient(45deg,#e5e7eb 25%,transparent 25%),linear-gradient(-45deg,#e5e7eb 25%,transparent 25%),linear-gradient(45deg,transparent 75%,#e5e7eb 75%),linear-gradient(-45deg,transparent 75%,#e5e7eb 75%);background-size:16px 16px;background-position:0 0,0 8px,8px -8px,-8px 0;"></canvas>
            </div>

            <div class="p-5 space-y-5 border-t lg:border-t-0 lg:border-l border-slate-100">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="photo-editor-brightness" class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Brightness</label>
                        <span id="photo-editor-brightness-value" class="text-[10px] font-black text-slate-500">100%</span>
                    </div>
                    <input id="photo-editor-brightness" type="range" min="50" max="150" value="100" class="w-full accent-blue-600">
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="photo-editor-contrast" class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Contrast</label>
                        <span id="photo-editor-contrast-value" class="text-[10px] font-black text-slate-500">100%</span>
                    </div>
                    <input id="photo-editor-contrast" type="range" min="50" max="150" value="100" class="w-full accent-blue-600">
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="photo-editor-saturation" class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Saturation</label>
                        <span id="photo-editor-saturation-value" class="text-[10px] font-black text-slate-500">100%</span>
                    </div>
                    <input id="photo-editor-saturation" type="range" min="0" max="200" value="100" class="w-full accent-blue-600">
                </div>

                <div class="grid grid-cols-2 gap-2 pt-2">
                    <button type="button" id="photo-editor-auto-bg" class="px-3 py-2 bg-emerald-50 text-emerald-700 rounded-lg text-[10px] font-black uppercase tracking-wider hover:bg-emerald-100">
                        <i class="fas fa-cut mr-1"></i> Remove BG
                    </button>
                    <button type="button" id="photo-editor-reset" class="px-3 py-2 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-black uppercase tracking-wider hover:bg-slate-200">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
            <button type="button" onclick="inventoryManager.closeInventoryPhotoEditor()" class="px-4 py-2 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-700">Cancel</button>
            <button type="button" id="photo-editor-apply" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 shadow-lg shadow-blue-100">
                Apply to Photo
            </button>
        </div>
    </div>
</div>
