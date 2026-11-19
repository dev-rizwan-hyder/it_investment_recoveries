<!-- IT Assets Edit Item Modal -->
<div id="itAssetsEditModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-2 sm:p-4">
    <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="closeItAssetsEditModal()"></div>
    <div class="relative bg-white rounded-2xl md:rounded-3xl shadow-2xl w-full max-w-4xl max-h-[95vh] overflow-hidden flex flex-col transform transition-all animate-in zoom-in-95">
        <div class="px-6 py-4 border-b flex justify-between items-center sticky top-0 bg-white z-10">
            <h2 class="text-base md:text-xl font-black text-slate-800 flex items-center gap-2 flex-wrap">
                <i class="fas fa-edit text-purple-600"></i>
                <span>Edit IT Assets Item:</span>
                <span id="it-assets-edit-item-name-title" class="text-purple-700 font-bold"></span>
                <span id="it-assets-edit-barcode-title" class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-lg border border-slate-200 ml-1"></span>
            </h2>
            <button onclick="closeItAssetsEditModal()" class="h-8 w-8 rounded-full hover:bg-slate-100 text-slate-400"><i class="fas fa-times"></i></button>
        </div>

        <form id="it-assets-edit-item-form" class="flex-1 overflow-y-auto p-6 md:p-8 space-y-6">
            <input type="hidden" id="it-assets-edit-item-id" name="id">
            <input type="hidden" id="it-assets-edit-pallet-number" name="pallet_number">

            {{-- Multi-Items Section --}}
            <div id="it-assets-edit-items-container" class="space-y-4 border-b border-slate-100 pb-6">
                <div class="flex justify-between items-center">
                    <h4 class="text-[13px] font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-list text-purple-600"></i> IT Asset Items
                    </h4>
                    <button type="button" id="it-assets-edit-add-item-btn" class="px-3 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
                        <i class="fas fa-plus text-[10px]"></i> Add Another Item
                    </button>
                </div>
                <div id="it-assets-edit-items-list" class="space-y-3">
                    <!-- Dynamic edit item rows -->
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-slate-400">Category *</label>
                    <input type="text" id="it-assets-edit-category" name="category" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 outline-none focus:border-purple-500">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-slate-400">Sub Category</label>
                    <input type="text" id="it-assets-edit-sub-category" name="sub_category" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 outline-none focus:border-purple-500">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-slate-400">Condition</label>
                    <select id="it-assets-edit-condition" name="condition" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 outline-none focus:border-purple-500">
                        <option value="New">New</option>
                        <option value="Used">Used</option>
                        <option value="Refurbished">Refurbished</option>
                        <option value="For Parts/Not Working">For Parts/Not Working</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-slate-400">Estimated Value ($) *</label>
                    <input type="number" step="0.01" id="it-assets-edit-estimated-value" name="estimated_value" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 outline-none focus:border-purple-500">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-slate-400">Total Quantity *</label>
                    <input type="number" id="it-assets-edit-quantity" name="quantity" required min="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 outline-none focus:border-purple-500">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-slate-400">Total Weight (Lbs)</label>
                    <input type="number" step="0.01" id="it-assets-edit-weight" name="weight" min="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 outline-none focus:border-purple-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-purple-600">Reuse Quantity</label>
                    <input type="number" id="it-assets-edit-reuse-quantity" name="reuse_quantity" min="0" class="w-full bg-white border border-purple-200 rounded-xl px-4 py-3 text-sm font-bold text-purple-700 outline-none focus:border-purple-500">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-amber-600">Scrap Quantity</label>
                    <input type="number" id="it-assets-edit-scrap-quantity" name="scrap_quantity" min="0" class="w-full bg-white border border-amber-200 rounded-xl px-4 py-3 text-sm font-bold text-amber-700 outline-none focus:border-amber-500">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-orange-600">IT Asset Weight (Lbs)</label>
                    <input type="number" step="0.01" id="it-assets-edit-reuse-weight" name="reuse_weight" min="0" class="w-full bg-white border border-orange-200 rounded-xl px-4 py-3 text-sm font-bold text-orange-700 outline-none focus:border-orange-500">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-amber-600">Scrap Weight (Lbs)</label>
                    <input type="number" step="0.01" id="it-assets-edit-scrap-weight" name="scrap_weight" min="0" class="w-full bg-white border border-amber-200 rounded-xl px-4 py-3 text-sm font-bold text-amber-700 outline-none focus:border-amber-500">
                </div>
                <div class="flex justify-between items-center pt-3 mt-2 border-t border-slate-200/60 col-span-1 md:col-span-2">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between w-full gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Net Available Qty:</span>
                            <span id="it-assets-edit-remaining-qty" class="text-sm font-black text-emerald-600">0 PCS</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Net Available Weight:</span>
                            <span id="it-assets-edit-net-weight" class="text-sm font-black text-purple-600">0.00 Lbs</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-slate-400">Item Placement</label>
                    <input type="text" id="it-assets-edit-item-placement" name="item_placement" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 outline-none focus:border-purple-500">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold uppercase text-slate-400">IT Assets Employee</label>
                    <input type="text" id="it-assets-edit-it-assets-employee" name="it_assets_employee" list="employees-list" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 outline-none focus:border-purple-500">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-bold uppercase text-slate-400">Internal Notes</label>
                <textarea id="it-assets-edit-notes" name="notes" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 outline-none focus:border-purple-500"></textarea>
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-bold uppercase text-slate-400">Photos / Attachments</label>
                <div class="flex flex-wrap gap-3">
                    <div id="it-assets-edit-existing-photos" class="flex flex-wrap gap-3"></div>
                    <label class="cursor-pointer bg-purple-50 text-purple-600 w-24 h-24 rounded-xl flex flex-col items-center justify-center text-[10px] font-bold border-2 border-dashed border-purple-200 hover:bg-purple-100 transition-all">
                        <i class="fas fa-file-upload mb-1 text-lg"></i>
                        <span>Add Images/Files</span>
                        <input type="file" id="it-assets-edit-photos-input" multiple class="hidden" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv">
                    </label>
                </div>
                <div id="it-assets-edit-new-photos-preview" class="flex flex-wrap gap-3 mt-3"></div>
                <p class="text-[10px] text-slate-500 italic">You can add multiple files by selecting multiple files or clicking upload multiple times.</p>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t">
                <button type="button" onclick="closeItAssetsEditModal()" class="px-6 py-2.5 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-200">Cancel</button>
                <button type="submit" class="px-8 py-2.5 bg-purple-600 text-white rounded-xl text-xs font-bold shadow-lg shadow-purple-200 hover:bg-purple-700">Save Changes</button>
            </div>
        </form>
    </div>
</div>
