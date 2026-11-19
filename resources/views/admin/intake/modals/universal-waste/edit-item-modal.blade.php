<!-- Edit Item Modal -->
<div id="wasteEditItemModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-2 sm:p-4">
    <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="closeWasteEditItemModal()"></div>
    <div class="relative bg-white rounded-2xl md:rounded-3xl shadow-2xl w-full max-w-4xl max-h-[95vh] overflow-hidden flex flex-col transform transition-all animate-in zoom-in-95">
        <div class="px-6 py-4 border-b flex justify-between items-center sticky top-0 bg-white z-10">
            <h2 class="text-base md:text-xl font-black text-slate-800 flex items-center gap-2 flex-wrap">
                <i class="fas fa-edit text-emerald-600"></i>
                <span>Edit Universal Waste Item:</span>
                <span id="waste-edit-item-name-title" class="text-emerald-700 font-bold"></span>
                <span id="waste-edit-barcode-title" class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-lg border border-slate-200 ml-1"></span>
            </h2>
            <button type="button" onclick="closeWasteEditItemModal()" class="h-8 w-8 rounded-full hover:bg-slate-100 text-slate-400 flex items-center justify-center transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="wasteEditItemForm" class="flex-1 overflow-y-auto p-6 md:p-8 space-y-6">
            <input type="hidden" id="waste-edit-item-id">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                {{-- Column 1 --}}
                <div class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-slate-400">Item Name</label>
                        <input type="text" id="waste-edit-name"
                            class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-slate-400">Brand</label>
                        <input type="text" id="waste-edit-brand"
                            class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-slate-400">Model</label>
                        <input type="text" id="waste-edit-model"
                            class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                {{-- Column 2 --}}
                <div class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-slate-400">Serial Number</label>
                        <input type="text" id="waste-edit-serial-number"
                            class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-slate-400">Category</label>
                            <select id="waste-edit-category-select"
                                class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-blue-400">
                                <option value="">Select Category</option>
                            </select>
                            <input type="hidden" id="waste-edit-category">
                            <input type="hidden" id="waste-edit-category-id">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-slate-400">Sub Category</label>
                            <select id="waste-edit-sub-category-select"
                                class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-blue-400 disabled:bg-slate-100 disabled:cursor-not-allowed"
                                disabled>
                                <option value="">Select Sub Category</option>
                            </select>
                            <input type="hidden" id="waste-edit-sub-category">
                            <input type="hidden" id="waste-edit-sub-category-id">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-slate-400">Sub Sub Category</label>
                            <select id="waste-edit-sub-sub-category-select"
                                class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-blue-400 disabled:bg-slate-100 disabled:cursor-not-allowed"
                                disabled>
                                <option value="">Select Sub Sub Category</option>
                            </select>
                            <input type="hidden" id="waste-edit-sub-sub-category">
                            <input type="hidden" id="waste-edit-sub-sub-category-id">
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-slate-400">Condition</label>
                        <select id="waste-edit-condition"
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
                        <input type="number" step="0.01" id="waste-edit-value"
                            class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm font-bold text-red-600">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-slate-400">Total Quantity</label>
                            <input type="number" id="waste-edit-quantity"
                                class="w-full bg-white border-slate-200 rounded-xl px-4 py-3 text-sm font-bold">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-red-500">Reuse Quantity</label>
                            <input type="number" id="waste-edit-reuse-quantity"
                                class="w-full bg-red-50 border-red-200 rounded-xl px-4 py-3 text-sm font-bold text-red-600 focus:ring-2 focus:ring-red-400 outline-none">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-amber-600">Scrap Quantity</label>
                            <input type="number" id="waste-edit-scrap-quantity"
                                class="w-full bg-amber-50 border-amber-200 rounded-xl px-4 py-3 text-sm font-bold text-amber-600 focus:ring-2 focus:ring-amber-400 outline-none">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Weight Logic Box --}}
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-3">
                <div class="grid grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Total Weight (Lbs)</label>
                        <input type="number" step="0.01" id="waste-edit-weight"
                            class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm font-bold text-slate-900 outline-none focus:border-blue-400">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-orange-500 tracking-wider">Reuse Weight</label>
                        <input type="number" step="0.01" id="waste-edit-reuse-weight"
                            class="w-full bg-orange-50 border border-orange-100 rounded-xl px-4 py-2 text-sm font-black text-orange-700 outline-none focus:ring-2 focus:ring-orange-400">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-amber-600 tracking-wider">Scrap Weight</label>
                        <input type="number" step="0.01" id="waste-edit-scrap-weight"
                            class="w-full bg-amber-50 border border-amber-100 rounded-xl px-4 py-2 text-sm font-black text-amber-700 outline-none focus:ring-2 focus:ring-amber-400">
                    </div>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-slate-200/60">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Net Available Weight</span>
                    <span id="waste-edit-net-weight" class="text-sm font-black text-red-600">0.00 Lbs</span>
                </div>
            </div>

            <div class="bg-slate-50 border border-slate-100 rounded-2xl p-5">
                <h4 class="text-[13px] font-bold text-slate-800 mb-1">Pallet Assignment</label>
                <input type="text" id="waste-edit-pallet"
                    class="w-full p-3.5 border rounded-xl text-sm bg-white font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <h4 class="text-[13px] font-bold text-slate-800 mb-1">Item Placement</h4>
                    <input type="text" id="waste-edit-placement"
                        class="w-full p-3.5 border rounded-xl text-sm bg-white font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="space-y-1">
                    <h4 class="text-[13px] font-bold text-slate-800 mb-1">Receiving Employee</h4>
                    <input type="text" id="waste-edit-receiving-employee" list="employees-list" placeholder="Enter employee name"
                        class="w-full p-3.5 border rounded-xl text-sm bg-white font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="space-y-1">
                    <h4 class="text-[13px] font-bold text-slate-800 mb-1">Processing Employee</h4>
                    <input type="text" id="waste-edit-processing-employee" list="employees-list" placeholder="Enter processing employee name"
                        class="w-full p-3.5 border rounded-xl text-sm bg-white font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="space-y-1">
                    <h4 class="text-[13px] font-bold text-slate-800 mb-1">Universal Waste Employee</h4>
                    <input type="text" id="waste-edit-universal-waste-employee" list="employees-list" placeholder="Enter universal waste employee name"
                        class="w-full p-3.5 border rounded-xl text-sm bg-white font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-bold uppercase text-slate-400">Internal Notes</label>
                <textarea id="waste-edit-notes" rows="3"
                    class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm italic"></textarea>
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-bold uppercase text-slate-400">Item Photos</label>
                <div class="flex flex-wrap gap-3">
                    <div id="waste-edit-existing-photos"></div>
                    <label class="cursor-pointer bg-red-50 text-red-600 w-20 h-20 rounded-xl flex flex-col items-center justify-center text-[10px] font-bold border-2 border-dashed border-red-200 hover:bg-red-100 transition-all">
                        <i class="fas fa-camera mb-1 text-lg"></i>
                        <span>Add</span>
                        <input type="file" id="waste-edit-photos" multiple accept="image/*" class="hidden">
                    </label>
                </div>
                <div id="waste-edit-new-photos-preview" class="flex flex-wrap gap-3 mt-3"></div>
            </div>

            <div class="pt-4 flex flex-col sm:flex-row justify-end gap-3 sticky bottom-0 bg-white border-t mt-auto py-4">
                <button type="button" onclick="closeWasteEditItemModal()"
                    class="px-6 py-3 text-xs font-bold text-slate-500 order-2 sm:order-1">Cancel</button>
                <button type="submit" id="waste-btn-save-item"
                    class="px-10 py-3 bg-red-600 text-white rounded-xl text-xs font-bold shadow-lg shadow-red-200 order-1 sm:order-2">
                    Update Item
                </button>
            </div>
        </form>
    </div>
</div>
