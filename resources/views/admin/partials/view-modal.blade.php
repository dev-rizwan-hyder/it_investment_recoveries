{{-- VIEW DETAILS MODAL --}}
<div id="viewModal" class="fixed inset-0 bg-slate-900/70 backdrop-blur-md z-50 flex items-center justify-center p-4 hidden">
    <div id="viewModalPanel" class="bg-white rounded-[2.5rem] shadow-2xl max-w-5xl w-full max-h-[calc(100vh-2rem)] overflow-hidden animate-in zoom-in duration-300 border border-slate-200 flex flex-col transition-all">
        {{-- Header Section --}}
        <div class="p-6 md:p-8 border-b flex justify-between items-center bg-white shrink-0">
            <div>
                <h2 class="text-xl md:text-2xl font-black text-slate-900 uppercase tracking-tighter">
                    Pallet Details <span id="modalBarcode" class="text-blue-600">#---</span>
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <button id="viewMaximizeBtn" type="button" onclick="toggleViewMaximize()" title="Maximize view" aria-label="Maximize view" class="bg-slate-50 p-3 rounded-2xl text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all">
                    <i id="viewMaximizeIcon" class="fas fa-expand text-xl"></i>
                </button>
                <button onclick="closeViewModal()" class="bg-slate-50 p-3 rounded-2xl text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        {{-- Scrollable Content Area --}}
        <div id="viewModalContent" class="p-6 md:p-8 overflow-y-auto max-h-[60vh] custom-scrollbar">
            {{-- Barcode Visualization --}}
            <div class="mb-6 flex flex-col items-center justify-center p-6 bg-white border-2 border-dashed border-slate-100 rounded-[2rem]">
                <div id="barcodeDisplay" class="bg-white p-3 border rounded-xl shadow-sm mb-2"></div>
                <p id="barcodeText" class="font-mono text-[10px] font-black tracking-[0.3em] text-slate-400 uppercase"></p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Column 1: Logistics & Weight --}}
                <div class="space-y-6">
                    <section>
                        <h4 class="font-black text-blue-600 uppercase text-[10px] tracking-[0.2em] mb-4 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-xs"></i> Logistics Details
                        </h4>
                        <div id="logisticsDetails" class="space-y-3 bg-slate-50/50 p-5 rounded-[1.5rem] border border-slate-100 shadow-sm text-sm">
                            {{-- Populated via JS --}}
                        </div>
                    </section>

                    <section>
                        <h4 class="font-black text-blue-600 uppercase text-[10px] tracking-[0.2em] mb-4 flex items-center">
                            <i class="fas fa-weight-hanging mr-2 text-xs"></i> Weight & Items
                        </h4>
                        <div id="weightDetails" class="bg-white border border-slate-200 rounded-[1.5rem] overflow-hidden shadow-sm">
                             {{-- Populated via JS --}}
                        </div>
                    </section>
                </div>

                {{-- Column 2: Description & Media --}}
                <div class="space-y-6">
                    <section>
                        <h4 class="font-black text-blue-600 uppercase text-[10px] tracking-[0.2em] mb-4 flex items-center">
                            <i class="fas fa-sticky-note mr-2 text-xs"></i> Description & Notes
                        </h4>
                        <div id="descriptionDetails" class="bg-slate-50/50 p-5 rounded-[1.5rem] border border-slate-100 h-[180px] overflow-y-auto custom-scrollbar">
                             {{-- Populated via JS --}}
                        </div>
                    </section>

                    <section>
                        <h4 class="font-black text-blue-600 uppercase text-[10px] tracking-[0.2em] mb-4 flex items-center">
                            <i class="fas fa-paperclip mr-2 text-xs"></i> Inspection Photos & Files
                        </h4>
                        <div id="photoDetails" class="grid grid-cols-3 gap-2">
                             {{-- Populated via JS --}}
                        </div>
                    </section>
                </div>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="p-6 md:p-8 bg-slate-50 border-t shrink-0">
            <div class="flex flex-wrap md:flex-nowrap items-center justify-center gap-2">
                <button onclick="printLabel()" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-5 py-3.5 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all shadow-lg shadow-slate-200">
                    <i class="fas fa-barcode"></i> Print Label
                </button>
                <button onclick="printReceipt()" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-5 py-3.5 bg-orange-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-700 transition-all shadow-lg shadow-orange-200">
                    <i class="fas fa-file-invoice"></i> Print Receipt
                </button>
                <button id="viewToEditBtn" class="flex-1 md:flex-none px-8 py-3.5 bg-blue-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                    Edit Pallet
                </button>
                <button onclick="closeViewModal()" class="flex-1 md:flex-none px-8 py-3.5 bg-white border border-slate-200 text-slate-500 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-50 transition-all">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
