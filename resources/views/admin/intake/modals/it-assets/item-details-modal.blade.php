<!-- IT Assets Item Details Modal -->
<div id="itAssetsItemDetailsModal" class="hidden fixed inset-0 z-[110] flex items-center justify-center p-2 sm:p-4">
    <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="closeItAssetsItemDetailsModal()"></div>
    <div class="relative bg-white rounded-2xl md:rounded-3xl shadow-2xl w-full max-w-4xl max-h-[95vh] overflow-hidden flex flex-col transform transition-all animate-in zoom-in-95">
        <div class="px-6 py-4 border-b flex justify-between items-center sticky top-0 bg-white z-10">
            <h2 id="it-assets-modal-title" class="text-base md:text-xl font-black text-slate-800"></h2>
            <div class="flex items-center gap-4">
                <span id="it-assets-modal-id" class="text-xs font-bold text-slate-400 uppercase tracking-widest hidden"></span>
                <span id="it-assets-modal-status" class="px-3 py-1 text-[10px] font-black uppercase rounded-lg text-white"></span>
                <button onclick="closeItAssetsItemDetailsModal()" class="h-8 w-8 rounded-full hover:bg-slate-100 text-slate-400"><i class="fas fa-times"></i></button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-6 md:p-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
                {{-- Dynamic Items Section --}}
                <div id="it-assets-modal-items-section" class="col-span-1 md:col-span-3 space-y-4 mb-2">
                    <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                        <h4 class="text-[13px] font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-desktop text-purple-600"></i> IT Asset Items
                        </h4>
                        <span class="bg-purple-50 text-purple-600 px-3 py-1 rounded-full text-xs font-black" id="it-assets-modal-items-count">0 Items</span>
                    </div>
                    
                    <div id="it-assets-modal-items-list" class="space-y-4">
                        <!-- Dynamic item rows will be inserted here by JS -->
                    </div>
                </div>

                {{-- Column 1: Category & Condition --}}
                <div class="space-y-4">
                    <div class="space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-slate-400">Category</label>
                            <div id="it-assets-modal-category" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700"></div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-slate-400">Sub Category</label>
                            <div id="it-assets-modal-sub-category" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700"></div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold uppercase text-slate-400">Sub Sub Category</label>
                            <div id="it-assets-modal-sub-sub-category" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700"></div>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-slate-400">Condition</label>
                        <div id="it-assets-modal-condition" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-slate-700"></div>
                    </div>
                </div>

                {{-- Column 3 --}}
                <div class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase text-slate-400">Estimated Value ($)</label>
                        <div id="it-assets-modal-value" class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-black text-purple-600"></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-slate-400">Total Quantity</label>
                            <div id="it-assets-modal-qty-value" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm font-black text-slate-700"></div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-purple-600">Reuse Quantity</label>
                            <div id="it-assets-modal-reuse-quantity" class="w-full bg-purple-50 border border-purple-200 rounded-xl px-4 py-3 text-sm font-black text-purple-600"></div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-amber-600">Scrap Quantity</label>
                            <div id="it-assets-modal-scrap-quantity" class="w-full bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-sm font-black text-amber-600"></div>
                        </div>
                        <div class="flex justify-between items-center pt-3 mt-2 border-t border-slate-200/60 col-span-1 md:col-span-2">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Net Available</span>
                            <span id="it-assets-modal-remaining-qty" class="text-sm font-black text-emerald-600"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Weight Logic Box --}}
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-3">
                <div class="grid grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Total Weight (Lbs)</label>
                        <div id="it-assets-modal-weight" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm font-black text-slate-900"></div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-orange-500 tracking-wider">IT Asset Weight (Lbs)</label>
                        <div id="it-assets-modal-reuse-weight" class="w-full bg-orange-50 border border-orange-100 rounded-xl px-4 py-2 text-sm font-black text-orange-700"></div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-amber-600 tracking-wider">Scrap Weight</label>
                        <div id="it-assets-modal-scrap-weight" class="w-full bg-amber-50 border border-amber-100 rounded-xl px-4 py-2 text-sm font-black text-amber-700"></div>
                    </div>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-slate-200/60">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Net Available Weight</span>
                    <span id="it-assets-modal-net-weight" class="text-sm font-black text-purple-600"></span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-5">
                    <h4 class="text-[13px] font-bold text-slate-800 mb-1">Pallet Assignment</h4>
                    <div id="it-assets-modal-pallet" class="w-full p-3.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700"></div>
                </div>

                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-5">
                    <h4 class="text-[13px] font-bold text-slate-800 mb-1">Item Placement</h4>
                    <div id="it-assets-modal-placement" class="w-full p-3.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700"></div>
                </div>

                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-5">
                    <h4 class="text-[13px] font-bold text-slate-800 mb-1">Receiving Employee</h4>
                    <div id="it-assets-modal-receiving-employee" class="w-full p-3.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700"></div>
                </div>

                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-5">
                    <h4 class="text-[13px] font-bold text-slate-800 mb-1">Processing Employee</h4>
                    <div id="it-assets-modal-processing-employee" class="w-full p-3.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700"></div>
                </div>

                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-5 col-span-1 md:col-span-2">
                    <h4 class="text-[13px] font-bold text-slate-800 mb-1">IT Assets Employee</h4>
                    <div id="it-assets-modal-it-assets-employee" class="w-full p-3.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700"></div>
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-bold uppercase text-slate-400">Internal Notes</label>
                <div id="it-assets-modal-notes" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm italic text-slate-600 min-h-[40px]"></div>
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-bold uppercase text-slate-400">Item Photos / Files / Documents</label>
                <div id="it-assets-modal-photos" class="flex flex-wrap gap-3"></div>
            </div>

            <div class="pt-4 flex flex-col sm:flex-row justify-between items-center gap-3 sticky bottom-0 bg-white border-t mt-auto py-4">
                <div class="flex items-center gap-3">
                    <div class="text-center sm:text-left">
                        <p class="text-[9px] font-black text-slate-400 uppercase">Last Modified</p>
                        <p id="it-assets-modal-updated-at" class="text-xs font-bold text-slate-700"></p>
                    </div>
                    <div class="bg-white p-2 rounded-xl border border-slate-100">
                        <img id="it-assets-modal-barcode-img" src="" style="height: 30px;" alt="barcode">
                    </div>
                </div>
                <div class="flex gap-3">
                    <button id="it-assets-btn-modal-print" class="px-8 py-3 bg-slate-900 text-white rounded-xl text-xs font-bold shadow-lg shadow-slate-200 transition-all hover:bg-slate-800 flex items-center gap-2">
                        <i class="fas fa-print"></i> Print Label
                    </button>
                    <button id="it-assets-btn-modal-edit" class="px-8 py-3 bg-purple-600 text-white rounded-xl text-xs font-bold shadow-lg shadow-purple-200 transition-all hover:bg-purple-700 flex items-center gap-2">
                        <i class="fas fa-pencil-alt"></i> Edit Record
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
