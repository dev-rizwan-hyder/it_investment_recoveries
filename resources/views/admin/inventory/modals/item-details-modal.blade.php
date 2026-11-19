<!-- Item Details Modal -->
<div id="itemDetailsModal" class="hidden fixed inset-0 z-[99] flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-md transition-opacity" onclick="closeItemDetailsModal()"></div>

    <div class="relative bg-[#F8FAFC] rounded-[2rem] shadow-2xl w-full max-w-5xl max-h-[92vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="px-10 py-8 bg-white border-b border-slate-100 flex justify-between items-start gap-6">
            <div>
                <h2 id="detail-name" class="text-3xl font-black text-slate-900 tracking-tight leading-none mb-2"></h2>
                <p class="text-slate-500 font-medium flex flex-wrap items-center gap-2">
                    <span id="detail-brand-model" class="text-blue-600 font-bold uppercase tracking-wider text-xs"></span>
                    <span class="text-slate-300">/</span>
                    <span id="detail-category" class="text-slate-400 text-xs uppercase tracking-widest font-bold"></span>
                </p>
            </div>
            <button onclick="closeItemDetailsModal()" class="h-12 w-12 flex items-center justify-center rounded-2xl hover:bg-slate-50 text-slate-400 hover:text-red-500 transition-all group">
                <i class="fas fa-times text-xl group-hover:rotate-90 duration-200"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-10 custom-scrollbar space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4" id="detail-intake-details"></div>

            <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm">
                <h4 class="text-sm font-black text-slate-900 uppercase tracking-[0.2em] mb-5 flex items-center gap-3">
                    <span class="w-2 h-2 bg-blue-600 rounded-full"></span> Parcel Dimensions
                </h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="detail-parcel-details"></div>
                <p class="mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">in / lb</p>
            </div>

            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-4">Product & Repair Photos <span id="detail-repair-photo-count" class="text-slate-300">(0)</span></h4>
                <div id="detail-repair-photos" class="grid grid-cols-2 md:grid-cols-4 gap-3"></div>
            </div>

            <section id="detail-notes-section" class="bg-amber-50/50 p-6 rounded-[2rem] border border-amber-100 relative overflow-hidden hidden">
                <div class="absolute top-0 right-0 p-3 opacity-10">
                    <i class="fas fa-quote-right text-4xl text-amber-600"></i>
                </div>
                <p class="text-[10px] font-black text-amber-600 uppercase mb-3 tracking-widest">Notes</p>
                <p id="detail-notes" class="text-sm text-slate-700 leading-relaxed font-medium italic relative z-10"></p>
            </section>
        </div>

        <div class="px-10 py-8 bg-white border-t border-slate-100 flex justify-between items-center">
            <div class="flex flex-col">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Status</span>
                <span id="detail-status" class="text-xs font-bold text-slate-600 uppercase"></span>
            </div>
            <div class="flex gap-4">
                <button onclick="closeItemDetailsModal()" class="px-6 py-3 text-xs font-black text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest">
                    Close
                </button>
                <button onclick="inventoryManager.editItem(inventoryManager.selectedItem.id)" class="px-10 py-3 bg-blue-600 text-white rounded-xl text-xs font-black hover:bg-blue-700 shadow-xl shadow-blue-200 transition-all active:scale-95">
                    Edit Item
                </button>
            </div>
        </div>
    </div>
</div>
