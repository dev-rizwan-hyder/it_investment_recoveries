/* jshint ignore:start */
/* eslint-disable */
/**
 * Item Intake - Part 2: Data Destruction Intake
 * Handles: Data Destruction Items list, serialization, single label printing
 */

(function () {
    if (typeof ItemIntakeManager === "undefined") {
        console.error("ItemIntakeManager base class not found. Ensure item-intake.js is loaded first.");
        return;
    }

    ItemIntakeManager.prototype.applyDataDestructionUI = function () {
        const standardFields = document.getElementById("standard-item-fields");
        const ddSection = document.getElementById("data-destruction-items-section");
        const headerText = document.getElementById("dd-items-header-text");
        const headerIcon = document.getElementById("dd-items-header-icon");
        const badge = document.getElementById("dd-items-count-badge");

        if (standardFields) standardFields.classList.add("hidden");
        if (ddSection) ddSection.classList.remove("hidden");

        if (headerText) headerText.textContent = "Data Destruction Items";
        if (headerIcon) headerIcon.className = "fas fa-list text-red-500";
        if (badge) badge.className = "bg-red-50 text-red-600 px-3 py-1 rounded-full text-xs font-black";

        this.ddItems = [{ name: "", brand: "", model: "", serial: "" }];
        this.renderDataDestructionItems();

        const ddQtyInput = document.querySelector('input[name="quantity"]');
        if (ddQtyInput) {
            ddQtyInput.value = "";
            ddQtyInput.readOnly = false;
            ddQtyInput.style.backgroundColor = "";
        }
    };

    ItemIntakeManager.prototype.renderDataDestructionItems = function () {
        const container = document.getElementById("data-destruction-items-list");
        if (!container) return;
        
        const badge = document.getElementById("dd-items-count-badge");
        if (badge) {
            badge.textContent = `${this.ddItems.length} Item${this.ddItems.length > 1 ? "s" : ""}`;
        }
        
        container.innerHTML = this.ddItems.map((item, idx) => {
            const isOnlyItem = this.ddItems.length === 1;
            const removeBtnHtml = isOnlyItem ? "" : `
                <button type="button" class="remove-dd-item-btn absolute top-4 right-4 text-slate-400 hover:text-red-500 transition-colors w-7 h-7 rounded-full flex items-center justify-center hover:bg-red-50" data-index="${idx}" title="Remove Item">
                    <i class="fas fa-trash-alt text-sm"></i>
                </button>
            `;
            return `
                <div class="dd-item-row p-5 bg-[#f8fafc] border border-[#e2e8f0] rounded-2xl relative group animate-in fade-in duration-300" data-index="${idx}">
                    ${removeBtnHtml}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pr-6">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Item Name</label>
                            <input type="text" class="dd-item-name form-input w-full p-2.5 border border-[#e2e8f0] rounded-xl text-xs outline-none focus:border-[#2563eb] bg-white" placeholder="e.g. Laptop" value="${this.escapeHtml(item.name || '')}">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Brand</label>
                            <input type="text" class="dd-item-brand form-input w-full p-2.5 border border-[#e2e8f0] rounded-xl text-xs outline-none focus:border-[#2563eb] bg-white" placeholder="e.g. Dell" value="${this.escapeHtml(item.brand || '')}">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Model</label>
                            <input type="text" class="dd-item-model form-input w-full p-2.5 border border-[#e2e8f0] rounded-xl text-xs outline-none focus:border-[#2563eb] bg-white" placeholder="e.g. Latitude" value="${this.escapeHtml(item.model || '')}">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Serial Number</label>
                            <div class="flex gap-1.5">
                                <input type="text" class="dd-item-serial form-input flex-1 p-2.5 border border-[#e2e8f0] rounded-xl text-xs outline-none focus:border-[#2563eb] bg-white" placeholder="e.g. ABC123XYZ" value="${this.escapeHtml(item.serial || '')}">
                                <button type="button" class="generate-dd-serial-btn bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1 whitespace-nowrap" data-index="${idx}" title="Generate Random Serial Number">
                                    <i class="fas fa-magic text-[11px]"></i> Gen
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400">Item #${idx + 1}</span>
                        <button type="button" class="print-dd-serial-btn bg-slate-800 hover:bg-slate-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm" data-index="${idx}">
                            <i class="fas fa-print text-xs"></i> Print Serial Label
                        </button>
                    </div>
                </div>
            `;
        }).join("");
        
        container.querySelectorAll(".dd-item-row").forEach(row => {
            const idx = parseInt(row.dataset.index, 10);
            row.querySelector(".dd-item-name")?.addEventListener("input", (e) => {
                this.ddItems[idx].name = e.target.value;
            });
            row.querySelector(".dd-item-brand")?.addEventListener("input", (e) => {
                this.ddItems[idx].brand = e.target.value;
            });
            row.querySelector(".dd-item-model")?.addEventListener("input", (e) => {
                this.ddItems[idx].model = e.target.value;
            });
            row.querySelector(".dd-item-serial")?.addEventListener("input", (e) => {
                this.ddItems[idx].serial = e.target.value;
            });
            row.querySelector(".generate-dd-serial-btn")?.addEventListener("click", (e) => {
                e.preventDefault();
                const randomSerial = "SN" + Math.floor(1000000000 + Math.random() * 9000000000);
                this.ddItems[idx].serial = randomSerial;
                const serialInput = row.querySelector(".dd-item-serial");
                if (serialInput) serialInput.value = randomSerial;
            });
            row.querySelector(".print-dd-serial-btn")?.addEventListener("click", (e) => {
                e.preventDefault();
                this.printSingleDdItemLabel(idx);
            });
        });
    };

    ItemIntakeManager.prototype.printSingleDdItemLabel = function (idx) {
        const item = this.ddItems[idx];
        if (!item) return;

        const name = item.name || "N/A";
        const brand = item.brand || "N/A";
        const model = item.model || "N/A";
        const serial = item.serial || "N/A";

        const serialBarcode = (item.serial && item.serial.trim()) ? item.serial.trim() : (this.barcode || "N/A");
        const intakeBarcode = this.barcode || "N/A";
        const createdDate = new Date().toLocaleDateString();

        const barcodeImageSrc = `https://bwipjs-api.metafloor.com/?bcid=code128&text=${encodeURIComponent(serialBarcode)}&scale=2&height=12&includetext=false`;
        const labelSubtitle = this.intakeType === "IT Assets Intake" ? "IT Assets" : "Data Destruction";

        const html = `
            <section class="recycling-print-label">
                <div class="label-head">
                    <strong>IT Investment Recoveries</strong>
                    <span>${this.escapeHtml(labelSubtitle)}</span>
                </div>
                <div class="barcode-block">
                    <img src="${this.escapeHtml(barcodeImageSrc)}" alt="serial barcode">
                    <div>${this.escapeHtml(serialBarcode)}</div>
                </div>
                <div class="label-grid">
                    ${this.renderLabelRow("Item Name", name)}
                    ${this.renderLabelRow("Brand", brand)}
                    ${this.renderLabelRow("Model", model)}
                    ${this.renderLabelRow("Serial Number", serial)}
                    ${this.renderLabelRow("Intake Barcode", intakeBarcode)}
                    ${this.renderLabelRow("Date", createdDate)}
                </div>
                <div class="label-checks">
                   <span><i></i> Verified</span>
                   <span><i></i> Label Printed</span>
                </div>
                <div class="label-foot">
                    ${this.escapeHtml(this.formatDenverDateTime())}
                </div>
            </section>
        `;

        this.openLabelPrintWindow(html);
    };

    ItemIntakeManager.prototype.addDataDestructionItem = function () {
        this.ddItems.push({ name: "", brand: "", model: "", serial: "" });
        this.renderDataDestructionItems();
    };

    ItemIntakeManager.prototype.removeDataDestructionItem = function (index) {
        if (this.ddItems.length > 1) {
            this.ddItems.splice(index, 1);
            this.renderDataDestructionItems();
        }
    };
})();
/* jshint ignore:end */
/* eslint-enable */
