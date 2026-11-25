<!-- Product Issue Alert Email Modal -->
<div id="productIssueAlertModal" class="fixed inset-0 z-[130] hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm transition-opacity" style="align-items: center; justify-content: center;">
    <div class="fixed inset-0" onclick="closeProductAlertModal()"></div>
    <div class="relative w-full max-w-lg overflow-hidden rounded-3xl bg-white p-6 shadow-2xl transition-all border border-slate-100 my-8 mx-4 z-10 animate-in zoom-in-95 duration-200">
        
        <!-- Modal Header -->
        <div class="flex items-start justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center text-lg shadow-xs shrink-0">
                    <i class="fa-solid fa-bell animate-bounce"></i>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900">Report Product Issue</h3>
                    <p class="text-xs text-slate-500">Send an issue alert email to <strong class="text-red-700 font-bold">sal@it-investmentrecoveries.com</strong></p>
                </div>
            </div>
            <button type="button" onclick="closeProductAlertModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 hover:bg-slate-200 flex items-center justify-center transition-all">
                <i class="fa-solid fa-times text-xs"></i>
            </button>
        </div>

        <!-- Product Summary Box -->
        <div class="mt-4 rounded-2xl bg-slate-50 border border-slate-200/80 p-3.5 flex items-start gap-3">
            <div id="alert-modal-thumb-wrap" class="w-14 h-14 rounded-xl border border-slate-200 overflow-hidden bg-white shrink-0 flex items-center justify-center">
                <img id="alert-modal-thumb" src="" alt="Product" class="w-full h-full object-cover hidden">
                <i id="alert-modal-thumb-fallback" class="fa-solid fa-box text-slate-300 text-xl"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                    <span id="alert-modal-product-no-badge" class="inline-flex items-center gap-1 rounded-lg bg-slate-900 text-white px-2 py-0.5 text-xs font-black tracking-wide shrink-0">#---</span>
                    <h4 id="alert-modal-product-title" class="text-xs font-extrabold text-slate-900 truncate">Product Title</h4>
                </div>
                <div class="mt-2 grid grid-cols-2 gap-x-2 gap-y-1 text-[11px] text-slate-500">
                    <div><span class="text-slate-400 font-bold uppercase text-[9px] block">Category</span> <span id="alert-modal-category" class="font-bold text-slate-700 truncate block">-</span></div>
                    <div><span class="text-slate-400 font-bold uppercase text-[9px] block">Location</span> <span id="alert-modal-location" class="font-bold text-slate-700 truncate block">-</span></div>
                    <div><span class="text-slate-400 font-bold uppercase text-[9px] block">Serial No</span> <span id="alert-modal-serial" class="font-bold text-slate-700 truncate block">-</span></div>
                    <div><span class="text-slate-400 font-bold uppercase text-[9px] block">Quantity</span> <span id="alert-modal-quantity" class="font-bold text-slate-700 truncate block">-</span></div>
                </div>
            </div>
        </div>

        <!-- Email Form -->
        <form id="productAlertEmailForm" onsubmit="event.preventDefault(); submitProductAlertEmail();" class="mt-4 space-y-3.5">
            <input type="hidden" id="alert-modal-folder">

            <!-- Send To Field -->
            <div>
                <label class="block text-[11px] font-black uppercase text-slate-500 mb-1 tracking-wider">Send Email To</label>
                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-700">
                    <i class="fa-solid fa-envelope text-red-500 text-xs"></i>
                    <input type="email" id="alert-modal-recipient" value="sal@it-investmentrecoveries.com"
                           class="w-full bg-transparent outline-none font-bold text-slate-800 text-xs" required>
                </div>
            </div>

            <!-- Subject Field -->
            <div>
                <label class="block text-[11px] font-black uppercase text-slate-500 mb-1 tracking-wider">Subject</label>
                <input type="text" id="alert-modal-subject"
                       class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold text-slate-800 focus:border-red-500 focus:ring-1 focus:ring-red-500 outline-none" required>
            </div>

            <!-- Issue Message Field -->
            <div>
                <label class="block text-[11px] font-black uppercase text-slate-500 mb-1 tracking-wider">
                    Issue Details / Notes <span class="text-red-500">*</span>
                </label>
                <textarea id="alert-modal-message" rows="4"
                          class="w-full rounded-xl border border-slate-200 p-3 text-xs text-slate-700 focus:border-red-500 focus:ring-1 focus:ring-red-500 outline-none leading-relaxed"
                          placeholder="Describe the issue with this item (e.g. damaged ports, wrong serial number, missing parts, listing error...)" required></textarea>
            </div>

            <!-- Alert Checkbox / Status -->
            <div class="rounded-xl border border-red-100 bg-red-50/70 p-3 flex flex-wrap items-center justify-between gap-2">
                <label class="flex items-center gap-2 cursor-pointer select-none text-xs font-bold text-red-900">
                    <input type="checkbox" id="alert-modal-set-blinking" checked
                           class="h-4 w-4 rounded text-red-600 focus:ring-red-500 border-red-300">
                    <span>Mark Card with Blinking Red Alert</span>
                </label>
                <button type="button" id="alert-modal-clear-blink-btn" onclick="clearBlinkFromModal()"
                        class="hidden text-[11px] font-bold text-red-700 hover:text-red-900 hover:underline">
                    <i class="fa-solid fa-bell-slash mr-1"></i> Remove Alert Style
                </button>
            </div>

            <!-- Action Buttons -->
            <div class="pt-2 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeProductAlertModal()"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 transition active:scale-95">
                    Cancel
                </button>
                <button type="submit" id="alert-modal-submit-btn"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-red-600 px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-red-500/20 hover:bg-red-700 active:scale-95 transition-all">
                    <i class="fa-solid fa-paper-plane text-[11px]"></i>
                    <span id="alert-modal-submit-text">Send Email to Sal</span>
                </button>
            </div>
        </form>

    </div>
</div>
