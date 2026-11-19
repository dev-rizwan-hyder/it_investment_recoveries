{{-- EDIT MODAL --}}
<div id="editModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full animate-in fade-in duration-200 overflow-hidden">
        <div class="p-5 border-b flex justify-between items-center">
            <div class="flex items-center gap-2">
                <h2 class="font-black text-slate-800 uppercase text-sm">Update Pallet Data</h2>
                <span id="editModalItemSubtitle" class="text-blue-600 font-mono text-xs font-bold"></span>
            </div>
            <button type="button" onclick="closeEditModal()" class="text-slate-400">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="editForm" class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
            <input type="hidden" id="editPalletId">
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase">Status *</label>
                    <select id="editStatus" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 font-bold text-sm outline-none">
                        <option value="Draft">Draft</option>
                        <option value="Received" selected>Received</option>
                        <option value="Processing">Processing</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase">Intake Date / Date Added *</label>
                    <input type="date" id="editIntakeDate" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 font-bold text-sm outline-none">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase">Customer/Vendor</label>
                    <input type="text" id="editVendor" readonly class="w-full bg-slate-100 border border-slate-200 rounded-xl p-3 font-bold text-sm text-slate-600">
                    <input type="hidden" id="editVendorId">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-1 gap-4">
                <div class="mt-2 w-full rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs text-slate-600">
                    <div class="flex justify-between gap-3 border-b border-slate-200 pb-2">
                        <span class="font-black uppercase text-[9px] text-slate-400">Company</span>
                        <span id="editClientCompany" class="text-right font-bold text-slate-700">N/A</span>
                    </div>
                    <div class="flex justify-between gap-3 border-b border-slate-200 py-2">
                        <span class="font-black uppercase text-[9px] text-slate-400">Email</span>
                        <span id="editClientEmail" class="break-all text-right font-bold text-slate-700">N/A</span>
                    </div>
                    <div class="flex justify-between gap-3 border-b border-slate-200 py-2">
                        <span class="font-black uppercase text-[9px] text-slate-400">Phone</span>
                        <span id="editClientPhone" class="text-right font-bold text-slate-700">N/A</span>
                    </div>
                    <div class="flex justify-between gap-3 pt-2">
                        <span class="font-black uppercase text-[9px] text-slate-400">Address</span>
                        <span id="editClientAddress" class="text-right font-bold text-slate-700">N/A</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase">Gross Weight (lbs)</label>
                    <input type="number" step="0.1" id="editGrossWeight" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 font-bold text-sm">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase">Tare Weight (lbs)</label>
                    <input type="number" step="0.1" id="editTareWeight" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 font-bold text-sm">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase">Scrap Weight (lbs) <span class="text-blue-500 font-normal lowercase">(preview only)</span></label>
                    <input type="number" step="0.1" id="editReuseWeight" readonly class="w-full bg-slate-100 border border-slate-200 rounded-xl p-3 font-bold text-sm text-slate-500 cursor-not-allowed">
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 flex justify-between items-center">
                <span class="text-[10px] font-black text-blue-600 uppercase">Remaining Weight (lbs)</span>
                <span id="netWeightDisplay" class="text-lg font-black text-blue-700">0.0 lbs</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase">Estimated Items</label>
                    <input type="number" id="editEstimatedCount" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 font-bold text-sm">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-blue-500 uppercase">Scrap Quantity <span class="text-blue-500 font-normal lowercase">(preview only)</span></label>
                    <input type="number" id="editReuseQuantity" readonly class="w-full bg-slate-100 border border-slate-200 rounded-xl p-3 font-bold text-sm text-slate-500 cursor-not-allowed">
                </div>
            </div>

            <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 flex justify-between items-center">
                <span class="text-[10px] font-black text-emerald-600 uppercase">Remaining Quantity</span>
                <span id="netItemsDisplay" class="text-xl font-black text-emerald-700">0 Units</span>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-slate-400 uppercase">Material Description</label>
                <input type="text" id="editDescription" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 font-bold text-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase">Put-Away Location</label>
                    <input type="text" id="editLocation" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 font-bold text-sm">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase">Marketing Ref</label>
                    <input type="text" id="editMarketingReference" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 font-bold text-sm">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase">Receiving Employee</label>
                    <input type="text" id="editReceivingEmployee" list="employeesList" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 font-bold text-sm outline-none" placeholder="Receiving employee name...">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase">Job #</label>
                    <input type="text" id="editJobNo" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 font-bold text-sm outline-none" placeholder="e.g., JOB-10023">
                </div>
                <div class="space-y-1 sm:col-span-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase">Processing Employee</label>
                    <input type="text" id="editProcessingEmployee" list="employeesList" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 font-bold text-sm outline-none" placeholder="Processing employee name...">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-slate-400 uppercase">Internal Notes</label>
                <textarea id="editNotes" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 font-bold text-sm"></textarea>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase">Current Photos & Files</label>
                <div id="editPhotoDetails" class="grid grid-cols-3 sm:grid-cols-4 gap-2 rounded-2xl border border-slate-200 bg-slate-50 p-3">
                    {{-- Populated via JS --}}
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between gap-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase">Upload New Photos & Files</label>
                    <button type="button" id="editAddPhotosBtn" class="px-3 py-2 rounded-xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all">
                        <i class="fas fa-paperclip mr-1"></i> Add Image / File
                    </button>
                </div>
                <input type="file" id="editNewPhotos" accept="*/*" multiple class="hidden">
                <div id="editNewPhotoPreview" class="hidden grid grid-cols-3 sm:grid-cols-4 gap-2 rounded-2xl border border-blue-100 bg-blue-50/40 p-3">
                    {{-- Populated via JS --}}
                </div>
                <button type="button" id="clearEditNewPhotos" class="hidden text-[10px] font-black uppercase tracking-widest text-red-500 hover:text-red-700">
                    Clear selected uploads
                </button>
            </div>
        </form>

        <div class="p-5 bg-slate-50 border-t flex flex-col sm:flex-row justify-end gap-3">
            <button type="button" onclick="closeEditModal()" class="order-2 sm:order-1 py-2 text-slate-500 font-black uppercase text-[10px] tracking-widest">Cancel</button>
            <button type="button" id="editSubmitBtn" class="order-1 sm:order-2 w-full sm:w-auto px-8 py-3 bg-blue-600 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-lg active:scale-95 transition-all">Save Changes</button>
        </div>
    </div>
</div>
