<div id="editItemModal" class="fixed inset-0 z-[110] flex items-center justify-center p-3 sm:p-6 hidden">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-md transition-opacity"
        onclick="RefurbishingEntries.closeModal()"></div>

    <div
        class="relative bg-white rounded-[1.5rem] shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden flex flex-col transform transition-all animate-in zoom-in-95 duration-200">

        {{-- HEADER --}}
        <div class="px-8 py-5 border-b border-slate-100 flex justify-between items-center bg-white sticky top-0 z-20">
            <div>
                <h2 class="text-lg font-black text-slate-900 tracking-tight leading-none uppercase">Edit Inventory</h2>
                <p id="edit-barcode" class="text-[9px] text-blue-600 font-bold uppercase tracking-widest mt-1"></p>
            </div>
            <button onclick="RefurbishingEntries.closeModal()"
                class="h-9 w-9 flex items-center justify-center rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-400 transition-all">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form id="edit-form" class="flex flex-col flex-1 overflow-hidden bg-white">
            <input type="hidden" id="edit-item-id">
            <div class="flex-1 overflow-y-auto p-8 custom-scrollbar space-y-8">

                {{-- SECTION 1: PRIMARY INFO --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-slate-400 tracking-wider ml-1">Item
                            Name</label>
                        <input type="text" id="edit-name"
                            class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-700 outline-none focus:border-blue-400 transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-slate-400 tracking-wider ml-1">Serial
                            Number</label>
                        <input type="text" id="edit-serial"
                            class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2.5 text-sm font-mono font-bold text-slate-600 outline-none uppercase focus:border-blue-400">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-slate-400 tracking-wider ml-1">Market Value
                            ($)</label>
                        <input type="number" step="0.01" id="edit-value"
                            class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2.5 text-sm font-black text-emerald-600 outline-none focus:border-emerald-400">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-slate-400 tracking-wider ml-1">Brand
                            Name</label>
                        <input type="text" id="edit-brand"
                            class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2.5 text-sm font-semibold outline-none focus:border-blue-400">
                    </div>
                    <div class="space-y-3 bg-slate-50 rounded-[1.5rem] border border-slate-100 p-5 md:col-span-2 xl:col-span-1">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <span class="w-6 h-[1.5px] bg-slate-200"></span> Category
                        </p>
                        <div class="space-y-1">
                            <label class="text-[9px] font-bold uppercase text-slate-500">Category</label>
                            <select id="edit-category-select"
                                class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm font-semibold outline-none cursor-pointer focus:border-blue-400">
                                <option value="">Select Category</option>
                            </select>
                            <input type="hidden" id="edit-category">
                            <input type="hidden" id="edit-category-id">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[9px] font-bold uppercase text-slate-500">Sub Category</label>
                            <select id="edit-sub-category-select"
                                class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm font-semibold outline-none cursor-pointer focus:border-blue-400 disabled:bg-slate-100 disabled:cursor-not-allowed"
                                disabled>
                                <option value="">Select Sub Category</option>
                            </select>
                            <input type="hidden" id="edit-sub-category">
                            <input type="hidden" id="edit-sub-category-id">
                        </div>
                        <div class="space-y-1">
                            <label class="text-[9px] font-bold uppercase text-slate-500">Sub Sub Category</label>
                            <select id="edit-sub-sub-category-select"
                                class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm font-semibold outline-none cursor-pointer focus:border-blue-400 disabled:bg-slate-100 disabled:cursor-not-allowed"
                                disabled>
                                <option value="">Select Sub Sub Category</option>
                            </select>
                            <input type="hidden" id="edit-sub-sub-category">
                            <input type="hidden" id="edit-sub-sub-category-id">
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: MODEL, CONDITION & QUANTITY --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-50">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-slate-400 tracking-wider ml-1">Model
                                Ref</label>
                            <input type="text" id="edit-model"
                                class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2.5 text-sm font-semibold outline-none focus:border-blue-400">
                        </div>
                        <div class="space-y-1">
                            <label
                                class="text-[10px] font-bold uppercase text-slate-400 tracking-wider ml-1">Condition</label>
                            <select id="edit-condition"
                                class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2.5 text-sm font-semibold outline-none cursor-pointer focus:border-blue-400">
                                <option value="New">New</option>
                                <option value="Used">Used</option>
                                <option value="Refurbished">Refurbished</option>
                            </select>
                        </div>
                    </div>

                    {{-- Quantity Logic Box --}}
                    <div class="bg-slate-50 p-5 rounded-[1.5rem] border border-slate-100 space-y-3">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-slate-400 tracking-wider ml-1">Total
                                    Quantity</label>
                                <input type="number" id="edit-quantity"
                                    class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm font-black text-slate-900 outline-none">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-blue-500 tracking-wider ml-1">Reuse
                                    Quantity</label>
                                <input type="number" id="edit-reuse-qty"
                                    class="w-full bg-blue-50 border border-blue-100 rounded-xl px-4 py-2 text-sm font-black text-blue-700 outline-none">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-amber-600 tracking-wider ml-1">Scrap
                                    Quantity</label>
                                <input type="number" id="edit-scrap-qty"
                                    class="w-full bg-amber-50 border border-amber-100 rounded-xl px-4 py-2 text-sm font-black text-amber-700 outline-none">
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-slate-200/60">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Net
                                Available</span>
                            <span id="net-quantity-display" class="text-sm font-black text-emerald-600">0 Units</span>
                        </div>
                    </div>
                </div>

                {{-- Weight Logic Box --}}
                <div class="space-y-3">
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-3">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Total
                                    Weight (Lbs)</label>
                                <input type="number" step="0.01" id="edit-weight"
                                    class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm font-bold text-slate-900 outline-none focus:border-blue-400">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold uppercase text-orange-500 tracking-wider ml-1">Reuse
                                    Weight</label>
                                <input type="number" step="0.01" id="edit-reuse-weight"
                                    class="w-full bg-orange-50 border border-orange-100 rounded-xl px-4 py-2 text-sm font-black text-orange-700 outline-none focus:ring-2 focus:ring-orange-400">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase text-amber-600 tracking-wider">Scrap
                                    Weight</label>
                                <input type="number" step="0.01" id="edit-scrap-weight"
                                    class="w-full bg-amber-50 border border-amber-100 rounded-xl px-4 py-2 text-sm font-black text-amber-700 outline-none focus:ring-2 focus:ring-amber-400">
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-2 border-t border-slate-200/60">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Net Available
                                Weight</span>
                            <span id="net-weight-display" class="text-sm font-black text-blue-600">0.00 Lbs</span>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: LOGISTICS & NOTES --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-50">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-slate-400 tracking-widest ml-1">Internal
                            Remarks</label>
                        <textarea id="edit-notes" rows="2"
                            class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-2.5 text-sm italic font-medium text-slate-600 outline-none resize-none focus:border-blue-400"></textarea>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-[13px] font-bold text-slate-800 mb-1">Pallet Assignment</h4>
                        <select id="edit-pallet"
                            class="w-full p-3.5 border rounded-xl text-sm bg-white font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">No pallet assignment</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-[13px] font-bold text-slate-800 mb-1">Item Placement</h4>
                        <input type="text" id="edit-placement" placeholder=""
                            class="w-full p-3.5 border rounded-xl text-sm bg-white font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-[13px] font-bold text-slate-800 mb-1">Receiving Employee</h4>
                        <input type="text" id="edit-receiving-employee" list="employees-list" placeholder="Enter employee name"
                            class="w-full p-3.5 border rounded-xl text-sm bg-white font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-[13px] font-bold text-slate-800 mb-1">Processing Employee</h4>
                        <input type="text" id="edit-processing-employee" list="employees-list" placeholder="Enter processing employee name"
                            class="w-full p-3.5 border rounded-xl text-sm bg-white font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-1 col-span-1 md:col-span-2">
                        <h4 class="text-[13px] font-bold text-slate-800 mb-1">Refurbishing Employee</h4>
                        <input type="text" id="edit-refurbishing-employee" list="employees-list" placeholder="Enter refurbishing employee name"
                            class="w-full p-3.5 border rounded-xl text-sm bg-white font-bold text-slate-700 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>



                {{-- SECTION 4: MEDIA MANAGER --}}
                <div class="space-y-4 pt-4 border-t border-slate-50">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-6 h-[1.5px] bg-slate-200"></span> Media Asset Manager
                    </p>
                    <div id="existing-photos" class="flex flex-wrap gap-4">
                        {{-- Existing photos populated by JS --}}
                    </div>
                    <div id="new-photos-preview" class="flex flex-wrap gap-4">
                        {{-- New photo previews populated by JS --}}
                    </div>

                    <label
                        class="w-24 h-24 cursor-pointer bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl flex flex-col items-center justify-center hover:bg-blue-50 hover:border-blue-300 transition-all group">
                        <i class="fas fa-plus text-slate-300 group-hover:text-blue-500 mb-1"></i>
                        <span
                            class="text-[8px] font-black text-slate-400 group-hover:text-blue-600 uppercase tracking-tighter">Add
                            Photo</span>
                        <input type="file" id="new-photos" multiple accept="image/*" class="hidden">
                    </label>
                </div>
            </div>

            {{-- FOOTER --}}
            <div
                class="px-8 py-5 bg-slate-50 border-t border-slate-100 flex justify-end items-center gap-4 sticky bottom-0 z-30">
                <button type="button" onclick="RefurbishingEntries.closeModal()"
                    class="text-[11px] font-black text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest px-4">Discard</button>
                <button type="submit"
                    class="px-10 py-3 bg-blue-600 text-white rounded-xl text-[11px] font-black hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all active:scale-95 uppercase tracking-wider flex items-center gap-3">
                    <i class="fas fa-spinner fa-spin hidden" id="edit-submit-spinner"></i>
                    <span id="edit-submit-text">Update Record</span>
                </button>
            </div>
        </form>
    </div>
</div>
