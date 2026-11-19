<!-- Edit Item Modal -->
<div id="destructionEditItemModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-2 sm:p-4">
    <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="closeDestructionEditItemModal()"></div>
    <div class="relative bg-white rounded-2xl md:rounded-3xl shadow-2xl w-full max-w-4xl max-h-[95vh] overflow-hidden flex flex-col transform transition-all animate-in zoom-in-95">
        <div class="px-6 py-4 border-b flex justify-between items-center sticky top-0 bg-white z-10">
            <h2 class="text-base md:text-xl font-black text-slate-800 flex items-center gap-2 flex-wrap">
                <i class="fas fa-edit text-red-600"></i>
                <span>Edit Data Destruction Item:</span>
                <span id="destruction-edit-item-name-title" class="text-red-700 font-bold"></span>
                <span id="destruction-edit-barcode-title" class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-lg border border-slate-200 ml-1"></span>
            </h2>
            <button type="button" onclick="closeDestructionEditItemModal()" class="h-8 w-8 rounded-full hover:bg-slate-100 text-slate-400 flex items-center justify-center transition-all"><i class="fas fa-times"></i></button>
        </div>

        <form id="destructionEditItemForm" class="flex-1 overflow-y-auto p-6 md:p-8 space-y-6">
            <input type="hidden" id="destruction-edit-item-id">
            <input type="hidden" id="destruction-edit-pallet">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                {{-- Dynamic Items Section --}}
                <div id="data-destruction-edit-items-section" class="col-span-1 md:col-span-3 space-y-4 mb-2">
                    <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                        <h4 class="text-[13px] font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-list text-red-500"></i> Items to Destroy
                        </h4>
                        <span class="bg-red-50 text-red-600 px-3 py-1 rounded-full text-xs font-black" id="edit-dd-items-count-badge">1 Item</span>
                    </div>
                    
                    <div id="data-destruction-edit-items-list" class="space-y-4">
                        <!-- Dynamic item rows will be inserted here by JS -->
                    </div>
                    
                    <button type="button" id="add-edit-dd-item-btn" class="w-full md:w-auto bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 px-4 py-2 rounded-xl font-bold text-xs flex items-center justify-center gap-2 transition-all shadow-sm">
                        <i class="fas fa-plus text-blue-600"></i> Add Another Item
                    </button>
                </div>

                {{-- Column 1: Category & Condition --}}
                <div class="space-y-4">
                    <div class="space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-slate-400">Category</label>
                            <select id="destruction-edit-category-select"
                                class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-blue-400">
                                <option value="">Select Category</option>
                            </select>
                            <input type="hidden" id="destruction-edit-category">
                            <input type="hidden" id="destruction-edit-category-id">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-slate-400">Sub Category</label>
                            <select id="destruction-edit-sub-category-select"
                                class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-blue-400 disabled:bg-slate-100 disabled:cursor-not-allowed"
                                disabled>
                                <option value="">Select Sub Category</option>
                            </select>
                            <input type="hidden" id="destruction-edit-sub-category">
                            <input type="hidden" id="destruction-edit-sub-category-id">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-slate-400">Sub Sub Category</label>
                            <select id="destruction-edit-sub-sub-category-select"
                                class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-blue-400 disabled:bg-slate-100 disabled:cursor-not-allowed"
                                disabled>
                                <option value="">Select Sub Sub Category</option>
                            </select>
                            <input type="hidden" id="destruction-edit-sub-sub-category">
                            <input type="hidden" id="destruction-edit-sub-sub-category-id">
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-slate-400">Condition</label>
                        <select id="destruction-edit-condition"
                            class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm">
                            <option value="New">New</option>
                            <option value="Good">Good</option>
                            <option value="Fair">Fair</option>
                            <option value="Poor">Poor</option>
                            <option value="For Parts">For Parts</option>
                        </select>
                    </div>
                </div>

                {{-- Column 3 --}}
                <div class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-slate-400">Estimated Value ($)</label>
                        <input type="number" step="0.01" id="destruction-edit-value"
                            class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm font-bold text-red-600">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-slate-400">Total Quantity</label>
                            <input type="number" id="destruction-edit-quantity"
                                class="w-full bg-white border-slate-200 rounded-xl px-4 py-3 text-sm font-bold">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-red-500">NIST Quantity</label>
                            <input type="number" id="destruction-edit-reuse-quantity"
                                class="w-full bg-red-50 border-red-200 rounded-xl px-4 py-3 text-sm font-bold text-red-600 focus:ring-2 focus:ring-red-400 outline-none">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-amber-600">Scrap Quantity</label>
                            <input type="number" id="destruction-edit-scrap-quantity"
                                class="w-full bg-amber-50 border-amber-200 rounded-xl px-4 py-3 text-sm font-bold text-amber-600 focus:ring-2 focus:ring-amber-400 outline-none">
                        </div>
                        <div class="flex justify-between items-center pt-3 mt-2 border-t border-slate-200/60 col-span-1 md:col-span-2">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Net Available</span>
                            <span id="destruction-edit-remaining-qty" class="text-sm font-black text-emerald-600">0 PCS</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Weight Logic Box --}}
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-3">
                <div class="grid grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Total Weight (Lbs)</label>
                        <input type="number" step="0.01" id="destruction-edit-weight"
                            class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm font-bold text-slate-900 outline-none focus:border-blue-400">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-orange-500 tracking-wider">NIST Weight</label>
                        <input type="number" step="0.01" id="destruction-edit-reuse-weight"
                            class="w-full bg-orange-50 border-orange-100 rounded-xl px-4 py-2 text-sm font-black text-orange-700 outline-none focus:ring-2 focus:ring-orange-400">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-amber-600 tracking-wider">Scrap Weight</label>
                        <input type="number" step="0.01" id="destruction-edit-scrap-weight"
                            class="w-full bg-amber-50 border-amber-100 rounded-xl px-4 py-2 text-sm font-black text-amber-700 outline-none focus:ring-2 focus:ring-amber-400">
                    </div>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-slate-200/60">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Net Available Weight</span>
                    <span id="destruction-edit-net-weight" class="text-sm font-black text-red-600">0.00 Lbs</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <h4 class="text-[13px] font-bold text-slate-800 mb-1">Item Placement</h4>
                    <input type="text" id="destruction-edit-placement"
                        class="w-full p-3.5 border rounded-xl text-sm bg-white font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="space-y-1">
                    <h4 class="text-[13px] font-bold text-slate-800 mb-1">Receiving Employee</h4>
                    <input type="text" id="destruction-edit-receiving-employee" list="employees-list" placeholder="Enter employee name"
                        class="w-full p-3.5 border rounded-xl text-sm bg-white font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="space-y-1">
                    <h4 class="text-[13px] font-bold text-slate-800 mb-1">Processing Employee</h4>
                    <input type="text" id="destruction-edit-processing-employee" list="employees-list" placeholder="Enter processing employee name"
                        class="w-full p-3.5 border rounded-xl text-sm bg-white font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="space-y-1">
                    <h4 class="text-[13px] font-bold text-slate-800 mb-1">Data Destruction Employee</h4>
                    <input type="text" id="destruction-edit-data-destruction-employee" list="employees-list" placeholder="Enter data destruction employee name"
                        class="w-full p-3.5 border rounded-xl text-sm bg-white font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-bold uppercase text-slate-400">Internal Notes</label>
                <textarea id="destruction-edit-notes" rows="3"
                    class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm italic"></textarea>
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-bold uppercase text-slate-400">Item Photos / Files / Certificates</label>
                <div class="flex flex-wrap gap-3">
                    <div id="destruction-edit-existing-photos" class="flex flex-wrap gap-3"></div>
                    <label class="cursor-pointer bg-red-50 text-red-600 w-30 h-30 rounded-xl flex flex-col items-center justify-center text-[10px] font-bold border-2 border-dashed border-red-200 hover:bg-red-100 transition-all">
                        <i class="fas fa-file-upload mb-1 text-lg"></i>
                        <span>Add Images/Files</span>
                        <input type="file" id="destruction-edit-photos" multiple class="hidden" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv">
                    </label>
                </div>
                <div id="destruction-edit-new-photos-preview" class="flex flex-wrap gap-3 mt-3"></div>
                <p class="text-[10px] text-slate-500 italic">You can add multiple files by clicking the upload button multiple times or selecting multiple files at once.</p>
            </div>

            <div class="pt-4 flex flex-col sm:flex-row justify-end gap-3 sticky bottom-0 bg-white border-t mt-auto py-4">
                <button type="button" onclick="closeDestructionEditItemModal()"
                    class="px-6 py-3 text-xs font-bold text-slate-500 order-2 sm:order-1 font-bold">Cancel</button>
                <button type="submit" id="destruction-btn-save-item"
                    class="px-10 py-3 bg-red-600 text-white rounded-xl text-xs font-bold shadow-lg shadow-red-200 order-1 sm:order-2 font-bold">
                    Update Item
                </button>
            </div>
        </form>
    </div>
</div>
