/* jshint ignore:start */
/* eslint-disable */
/**
 * Item Intake - Part 4: Repair Intake
 * Handles: Repair Intake flow, client selection/creation, multiple repair items, technician section, photo attachments, draft & save
 */

(function () {
    if (typeof ItemIntakeManager === "undefined") {
        console.error("ItemIntakeManager base class not found. Ensure item-intake.js is loaded first.");
        return;
    }

    ItemIntakeManager.prototype.initRepairIntake = function () {
        if (!this.repairInitialized) {
            this.repairInitialized = true;
            this.attachRepairEventListeners();
        }

        if (!this.repairItems || this.repairItems.length === 0) {
            this.repairItems = [{ name: "", brand: "", model: "", serial: "" }];
        }
        this.renderRepairItems();

        if (!this.repairBarcode) {
            this.regenerateRepairBarcode();
        }

        const jobNoInput = document.getElementById("repair-job-no");
        if (jobNoInput && !jobNoInput.value.trim()) {
            this.generateRepairJobNo();
        }
    };

    ItemIntakeManager.prototype.attachRepairEventListeners = function () {
        const existingCb = document.getElementById("repair-has-existing-barcode");
        const newSec = document.getElementById("repair-new-barcode-section");
        const existSec = document.getElementById("repair-existing-barcode-section");
        const activeBarcodeBadge = document.getElementById("repair-active-barcode-badge");
        const activeBarcodeText = document.getElementById("repair-active-barcode-text");
        const activeBarcodeInput = document.getElementById("repair-active-pallet-barcode");

        if (existingCb) {
            existingCb.addEventListener("change", (e) => {
                if (e.target.checked) {
                    if (newSec) newSec.classList.add("hidden");
                    if (existSec) existSec.classList.remove("hidden");
                    const selectedVal = document.getElementById("repair-existing-pallet-select")?.value;
                    if (activeBarcodeInput) activeBarcodeInput.value = selectedVal || "";
                    if (selectedVal) {
                        if (activeBarcodeBadge) activeBarcodeBadge.classList.remove("hidden");
                        if (activeBarcodeText) activeBarcodeText.textContent = selectedVal;
                    } else {
                        if (activeBarcodeBadge) activeBarcodeBadge.classList.add("hidden");
                    }
                } else {
                    if (newSec) newSec.classList.remove("hidden");
                    if (existSec) existSec.classList.add("hidden");
                    if (activeBarcodeInput) activeBarcodeInput.value = this.repairBarcode || "";
                    if (this.repairBarcode) {
                        if (activeBarcodeBadge) activeBarcodeBadge.classList.remove("hidden");
                        if (activeBarcodeText) activeBarcodeText.textContent = this.repairBarcode;
                    }
                }
            });
        }

        const regenBtn = document.getElementById("repair-regenerate-barcode-btn");
        if (regenBtn) {
            regenBtn.addEventListener("click", (e) => {
                e.preventDefault();
                this.regenerateRepairBarcode();
            });
        }

        const useBtn = document.getElementById("repair-use-generated-barcode-btn");
        if (useBtn) {
            useBtn.addEventListener("click", (e) => {
                e.preventDefault();
                if (!this.repairBarcode) {
                    alert("Please wait for barcode to generate.");
                    return;
                }
                if (activeBarcodeInput) activeBarcodeInput.value = this.repairBarcode;
                if (activeBarcodeBadge) activeBarcodeBadge.classList.remove("hidden");
                if (activeBarcodeText) activeBarcodeText.textContent = this.repairBarcode;
                useBtn.innerHTML = `<i class="fas fa-check-circle"></i> Barcode In Use!`;
                useBtn.classList.remove("bg-[#2563eb]");
                useBtn.classList.add("bg-emerald-600");
                setTimeout(() => {
                    useBtn.innerHTML = `<i class="fas fa-check"></i> Use This Barcode`;
                    useBtn.classList.remove("bg-emerald-600");
                    useBtn.classList.add("bg-[#2563eb]");
                }, 2000);
            });
        }

        const existSelect = document.getElementById("repair-existing-pallet-select");
        if (existSelect) {
            existSelect.addEventListener("change", (e) => {
                const val = e.target.value;
                if (activeBarcodeInput) activeBarcodeInput.value = val;
                if (val) {
                    if (activeBarcodeBadge) activeBarcodeBadge.classList.remove("hidden");
                    if (activeBarcodeText) activeBarcodeText.textContent = val;
                } else {
                    if (activeBarcodeBadge) activeBarcodeBadge.classList.add("hidden");
                }
            });
        }

        const clientSelect = document.getElementById("repair-client-select");
        if (clientSelect) {
            clientSelect.addEventListener("change", (e) => {
                const opt = clientSelect.selectedOptions[0];
                const display = document.getElementById("repair-selected-client-display");
                const nameEl = document.getElementById("repair-selected-client-name");
                const metaEl = document.getElementById("repair-selected-client-meta");
                if (opt && opt.value) {
                    const name = opt.getAttribute("data-name") || opt.textContent.trim();
                    const email = opt.getAttribute("data-email") || "";
                    const phone = opt.getAttribute("data-phone") || "";
                    if (nameEl) nameEl.textContent = name;
                    if (metaEl) metaEl.textContent = [email, phone].filter(Boolean).join(" • ");
                    if (display) display.classList.remove("hidden");
                } else {
                    if (display) display.classList.add("hidden");
                }
            });
        }

        const toggleClientBtn = document.getElementById("repair-toggle-new-client-btn");
        const clientFormSec = document.getElementById("repair-new-client-form-section");
        if (toggleClientBtn && clientFormSec) {
            toggleClientBtn.addEventListener("click", (e) => {
                e.preventDefault();
                clientFormSec.classList.toggle("hidden");
            });
        }

        const cancelClientBtn = document.getElementById("repair-cancel-new-client-btn");
        if (cancelClientBtn && clientFormSec) {
            cancelClientBtn.addEventListener("click", (e) => {
                e.preventDefault();
                clientFormSec.classList.add("hidden");
            });
        }

        const createClientBtn = document.getElementById("repair-create-new-client-btn");
        if (createClientBtn) {
            createClientBtn.addEventListener("click", (e) => {
                e.preventDefault();
                const name = document.getElementById("repair-new-client-name")?.value.trim();
                const email = document.getElementById("repair-new-client-email")?.value.trim();
                const phone = document.getElementById("repair-new-client-phone")?.value.trim();
                const company = document.getElementById("repair-new-client-company")?.value.trim();
                const address = document.getElementById("repair-new-client-address")?.value.trim();

                if (!name) {
                    alert("Client name is required.");
                    return;
                }

                this.showLoading("Creating client...");
                fetch(CREATE_CLIENT_API, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({ name, email, phone, company, address })
                })
                .then(res => res.json())
                .then(data => {
                    this.hideLoading();
                    if (data.success && data.client) {
                        const newOpt = new Option(data.client.name + (data.client.company ? ` (${data.client.company})` : ''), data.client.id, true, true);
                        newOpt.setAttribute("data-name", data.client.name);
                        newOpt.setAttribute("data-email", data.client.email || "");
                        newOpt.setAttribute("data-phone", data.client.phone || "");
                        newOpt.setAttribute("data-company", data.client.company || "");
                        newOpt.setAttribute("data-address", data.client.address || "");
                        
                        clientSelect.appendChild(newOpt);
                        clientSelect.value = data.client.id;
                        clientSelect.dispatchEvent(new Event("change"));

                        document.getElementById("repair-new-client-name").value = "";
                        document.getElementById("repair-new-client-email").value = "";
                        document.getElementById("repair-new-client-phone").value = "";
                        document.getElementById("repair-new-client-company").value = "";
                        document.getElementById("repair-new-client-address").value = "";
                        if (clientFormSec) clientFormSec.classList.add("hidden");
                    } else {
                        alert(data.message || "Failed to create client.");
                    }
                })
                .catch(err => {
                    this.hideLoading();
                    alert("Error creating client.");
                    console.error(err);
                });
            });
        }

        const genJobBtn = document.getElementById("repair-generate-job-no-btn");
        if (genJobBtn) {
            genJobBtn.addEventListener("click", (e) => {
                e.preventDefault();
                this.generateRepairJobNo();
            });
        }

        const addItemBtn = document.getElementById("add-repair-item-btn");
        if (addItemBtn) {
            addItemBtn.addEventListener("click", (e) => {
                e.preventDefault();
                this.addRepairItem();
            });
        }

        const photoInput = document.getElementById("repair-photo-input");
        if (photoInput) {
            photoInput.addEventListener("change", (e) => {
                const files = Array.from(e.target.files);
                this.repairPhotos.push(...files);
                this.renderRepairPhotos();
            });
        }

        const receivingEmpInput = document.getElementById("repair-receiving-employee");
        const receivingToggleBtn = document.getElementById("repair-receiving-employee-toggle-btn");
        const receivingDropdown = document.getElementById("repair-receiving-employee-dropdown");
        const receivingChevron = document.getElementById("repair-receiving-employee-chevron");
        const receivingOptionsContainer = document.getElementById("repair-receiving-employee-options-container");

        const openReceivingDropdown = () => {
            if (receivingDropdown) receivingDropdown.classList.remove("hidden");
            if (receivingChevron) receivingChevron.classList.add("rotate-180");
            filterReceivingDropdown();
        };

        const closeReceivingDropdown = () => {
            if (receivingDropdown) receivingDropdown.classList.add("hidden");
            if (receivingChevron) receivingChevron.classList.remove("rotate-180");
        };

        const filterReceivingDropdown = () => {
            if (!receivingEmpInput || !receivingOptionsContainer) return;
            const query = (receivingEmpInput.value || "").toLowerCase().trim();
            const options = receivingOptionsContainer.querySelectorAll(".repair-receiving-emp-option");
            let hasVisible = false;

            options.forEach(opt => {
                const text = (opt.dataset.value || "").toLowerCase();
                if (!query || text.includes(query)) {
                    opt.style.display = "flex";
                    hasVisible = true;
                } else {
                    opt.style.display = "none";
                }
            });

            let noResultMsg = receivingOptionsContainer.querySelector(".no-receiving-emp-found");
            if (!hasVisible && query) {
                if (!noResultMsg) {
                    noResultMsg = document.createElement("div");
                    noResultMsg.className = "no-receiving-emp-found px-4 py-3 text-xs text-blue-600 font-bold italic text-center cursor-pointer hover:bg-blue-50";
                    receivingOptionsContainer.appendChild(noResultMsg);
                }
                noResultMsg.innerHTML = `<i class="fas fa-plus-circle mr-1"></i> Use "${this.escapeHtml(query)}"`;
                noResultMsg.onclick = () => {
                    addCustomReceivingEmployee(query);
                    closeReceivingDropdown();
                };
                noResultMsg.style.display = "block";
            } else if (noResultMsg) {
                noResultMsg.style.display = "none";
            }
        };

        const addCustomReceivingEmployee = (name) => {
            if (!name || !receivingOptionsContainer) return;
            name = name.trim();
            const exists = Array.from(receivingOptionsContainer.querySelectorAll(".repair-receiving-emp-option"))
                .some(opt => (opt.dataset.value || "").toLowerCase() === name.toLowerCase());
            if (!exists) {
                const optDiv = document.createElement("div");
                optDiv.className = "repair-receiving-emp-option px-4 py-2.5 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 cursor-pointer font-medium transition-colors flex items-center justify-between";
                optDiv.dataset.value = name;
                optDiv.innerHTML = `<span>${this.escapeHtml(name)}</span><span class="text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded">New</span>`;
                optDiv.onclick = () => {
                    if (receivingEmpInput) receivingEmpInput.value = name;
                    closeReceivingDropdown();
                };
                receivingOptionsContainer.insertBefore(optDiv, receivingOptionsContainer.firstChild);
            }
            if (receivingEmpInput) receivingEmpInput.value = name;
        };

        if (receivingOptionsContainer) {
            receivingOptionsContainer.querySelectorAll(".repair-receiving-emp-option").forEach(opt => {
                opt.onclick = () => {
                    const val = opt.dataset.value || opt.textContent.trim();
                    if (receivingEmpInput) receivingEmpInput.value = val;
                    closeReceivingDropdown();
                };
            });
        }

        if (receivingEmpInput) {
            receivingEmpInput.addEventListener("focus", openReceivingDropdown);
            receivingEmpInput.addEventListener("click", openReceivingDropdown);
            receivingEmpInput.addEventListener("input", openReceivingDropdown);
            receivingEmpInput.addEventListener("blur", () => {
                setTimeout(() => {
                    closeReceivingDropdown();
                    const val = receivingEmpInput.value.trim();
                    if (val) addCustomReceivingEmployee(val);
                }, 200);
            });
        }

        if (receivingToggleBtn) {
            receivingToggleBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                if (receivingDropdown && receivingDropdown.classList.contains("hidden")) {
                    openReceivingDropdown();
                } else {
                    closeReceivingDropdown();
                }
            });
        }

        document.addEventListener("click", (e) => {
            const wrapper = document.getElementById("repair-receiving-employee-wrapper");
            if (wrapper && !wrapper.contains(e.target)) {
                closeReceivingDropdown();
            }
        });

        const draftBtn = document.getElementById("repair-save-draft-btn");
        if (draftBtn) {
            draftBtn.addEventListener("click", (e) => {
                e.preventDefault();
                this.saveRepairEntry(true);
            });
        }

        const saveBtn = document.getElementById("repair-save-btn");
        if (saveBtn) {
            saveBtn.addEventListener("click", (e) => {
                e.preventDefault();
                this.saveRepairEntry(false);
            });
        }
    };

    ItemIntakeManager.prototype.regenerateRepairBarcode = function () {
        this.showLoading("Generating pallet barcode...");
        fetch(`${INTAKE_API}?type=PAL&intake_type=Repair%20Intake`)
            .then(res => res.json())
            .then(data => {
                this.hideLoading();
                if (data.success && data.barcode) {
                    this.repairBarcode = data.barcode;
                    this.repairBarcodeBase64 = data.barcodeBase64 || "";
                    
                    const numEl = document.getElementById("repair-generated-barcode-number");
                    const txtEl = document.getElementById("repair-generated-barcode-text");
                    const imgEl = document.getElementById("repair-generated-barcode-image");
                    
                    if (numEl) numEl.textContent = data.barcode;
                    if (txtEl) txtEl.textContent = data.barcode;
                    if (imgEl) {
                        if (data.barcodeBase64) {
                            imgEl.innerHTML = `<img src="data:image/png;base64,${data.barcodeBase64}" class="h-16 md:h-20 max-w-full mx-auto" alt="barcode">`;
                        } else {
                            const bwipUrl = `https://bwipjs-api.metafloor.com/?bcid=code128&text=${encodeURIComponent(data.barcode)}&scale=2&height=12&includetext=false`;
                            imgEl.innerHTML = `<img src="${bwipUrl}" class="h-16 md:h-20 max-w-full mx-auto" alt="barcode">`;
                        }
                    }

                    const activeInput = document.getElementById("repair-active-pallet-barcode");
                    if (activeInput && !activeInput.value) {
                        activeInput.value = data.barcode;
                    }
                } else {
                    alert("Failed to generate pallet barcode.");
                }
            })
            .catch(err => {
                this.hideLoading();
                console.error("Barcode generation error:", err);
            });
    };

    ItemIntakeManager.prototype.generateRepairJobNo = function () {
        fetch(JOB_NO_API)
            .then(res => res.json())
            .then(data => {
                const jobInput = document.getElementById("repair-job-no");
                if (jobInput && data.job_no) {
                    jobInput.value = data.job_no;
                }
            })
            .catch(err => console.error("Job # error:", err));
    };

    ItemIntakeManager.prototype.renderRepairItems = function () {
        const container = document.getElementById("repair-items-list");
        if (!container) return;

        const badge = document.getElementById("repair-items-count-badge");
        if (badge) {
            badge.textContent = `${this.repairItems.length} Item${this.repairItems.length > 1 ? "s" : ""}`;
        }

        container.innerHTML = this.repairItems.map((item, idx) => {
            const isOnlyItem = this.repairItems.length === 1;
            const removeBtnHtml = isOnlyItem ? "" : `
                <button type="button" class="remove-repair-item-btn absolute top-4 right-4 text-slate-400 hover:text-red-500 transition-colors w-7 h-7 rounded-full flex items-center justify-center hover:bg-red-50" data-index="${idx}" title="Remove Item">
                    <i class="fas fa-trash-alt text-sm"></i>
                </button>
            `;
            return `
                <div class="repair-item-row p-5 bg-[#f8fafc] border border-[#e2e8f0] rounded-2xl relative group animate-in fade-in duration-300" data-index="${idx}">
                    ${removeBtnHtml}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pr-6">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Item Name</label>
                            <input type="text" class="repair-item-name form-input w-full p-2.5 border border-[#e2e8f0] rounded-xl text-xs outline-none focus:border-[#2563eb] bg-white" placeholder="e.g. Laptop" value="${this.escapeHtml(item.name || '')}">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Brand</label>
                            <input type="text" class="repair-item-brand form-input w-full p-2.5 border border-[#e2e8f0] rounded-xl text-xs outline-none focus:border-[#2563eb] bg-white" placeholder="e.g. Dell" value="${this.escapeHtml(item.brand || '')}">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Model</label>
                            <input type="text" class="repair-item-model form-input w-full p-2.5 border border-[#e2e8f0] rounded-xl text-xs outline-none focus:border-[#2563eb] bg-white" placeholder="e.g. Latitude" value="${this.escapeHtml(item.model || '')}">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Serial Number</label>
                            <div class="flex gap-1.5">
                                <input type="text" class="repair-item-serial form-input flex-1 p-2.5 border border-[#e2e8f0] rounded-xl text-xs outline-none focus:border-[#2563eb] bg-white" placeholder="e.g. ABC123XYZ" value="${this.escapeHtml(item.serial || '')}">
                                <button type="button" class="generate-repair-serial-btn bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1 whitespace-nowrap" data-index="${idx}" title="Generate Random Serial Number">
                                    <i class="fas fa-magic text-[11px]"></i> Gen
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400">Item #${idx + 1}</span>
                        <button type="button" class="print-repair-serial-btn bg-slate-800 hover:bg-slate-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 shadow-sm" data-index="${idx}">
                            <i class="fas fa-print text-xs"></i> Print Serial Label
                        </button>
                    </div>
                </div>
            `;
        }).join("");

        container.querySelectorAll(".repair-item-row").forEach(row => {
            const idx = parseInt(row.dataset.index, 10);
            row.querySelector(".repair-item-name")?.addEventListener("input", (e) => {
                this.repairItems[idx].name = e.target.value;
            });
            row.querySelector(".repair-item-brand")?.addEventListener("input", (e) => {
                this.repairItems[idx].brand = e.target.value;
            });
            row.querySelector(".repair-item-model")?.addEventListener("input", (e) => {
                this.repairItems[idx].model = e.target.value;
            });
            row.querySelector(".repair-item-serial")?.addEventListener("input", (e) => {
                this.repairItems[idx].serial = e.target.value;
            });
            row.querySelector(".generate-repair-serial-btn")?.addEventListener("click", (e) => {
                e.preventDefault();
                const randomSerial = "REP" + Math.floor(1000000000 + Math.random() * 9000000000);
                this.repairItems[idx].serial = randomSerial;
                const serialInput = row.querySelector(".repair-item-serial");
                if (serialInput) serialInput.value = randomSerial;
            });
            row.querySelector(".print-repair-serial-btn")?.addEventListener("click", (e) => {
                e.preventDefault();
                this.printSingleRepairItemLabel(idx);
            });
            row.querySelector(".remove-repair-item-btn")?.addEventListener("click", (e) => {
                e.preventDefault();
                this.removeRepairItem(idx);
            });
        });
    };

    ItemIntakeManager.prototype.syncRepairItemsFromDOM = function () {
        const container = document.getElementById("repair-items-container");
        if (!container) return;
        container.querySelectorAll(".repair-item-row").forEach(row => {
            const idx = parseInt(row.dataset.index, 10);
            if (this.repairItems[idx]) {
                const nameInput = row.querySelector(".repair-item-name");
                const brandInput = row.querySelector(".repair-item-brand");
                const modelInput = row.querySelector(".repair-item-model");
                const serialInput = row.querySelector(".repair-item-serial");
                if (nameInput) this.repairItems[idx].name = nameInput.value;
                if (brandInput) this.repairItems[idx].brand = brandInput.value;
                if (modelInput) this.repairItems[idx].model = modelInput.value;
                if (serialInput) this.repairItems[idx].serial = serialInput.value;
            }
        });
    };

    ItemIntakeManager.prototype.addRepairItem = function () {
        this.syncRepairItemsFromDOM();
        this.repairItems.push({ name: "", brand: "", model: "", serial: "" });
        this.renderRepairItems();
    };

    ItemIntakeManager.prototype.removeRepairItem = function (index) {
        this.syncRepairItemsFromDOM();
        if (this.repairItems.length > 1) {
            this.repairItems.splice(index, 1);
            this.renderRepairItems();
        }
    };

    ItemIntakeManager.prototype.printSingleRepairItemLabel = function (idx) {
        const item = this.repairItems[idx];
        if (!item) return;

        const name = item.name || "N/A";
        const brand = item.brand || "N/A";
        const model = item.model || "N/A";
        const serial = item.serial || "N/A";
        const palletBarcode = document.getElementById("repair-active-pallet-barcode")?.value || this.repairBarcode || "N/A";
        const serialBarcode = (item.serial && item.serial.trim()) ? item.serial.trim() : palletBarcode;
        const createdDate = new Date().toLocaleDateString();

        const barcodeImageSrc = `https://bwipjs-api.metafloor.com/?bcid=code128&text=${encodeURIComponent(serialBarcode)}&scale=2&height=12&includetext=false`;

        const html = `
            <section class="recycling-print-label">
                <div class="label-head">
                    <strong>IT Investment Recoveries</strong>
                    <span>Repair Intake</span>
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
                    ${this.renderLabelRow("Pallet Barcode", palletBarcode)}
                    ${this.renderLabelRow("Date", createdDate)}
                </div>
                <div class="label-checks">
                   <span><i></i> Received</span>
                   <span><i></i> Label Printed</span>
                </div>
                <div class="label-foot">
                    ${this.escapeHtml(this.formatDenverDateTime())}
                </div>
            </section>
        `;

        this.openLabelPrintWindow(html);
    };

    ItemIntakeManager.prototype.renderRepairPhotos = function () {
        const previewContainer = document.getElementById("repair-photo-previews");
        const badge = document.getElementById("repair-photo-count-badge");
        if (badge) badge.textContent = `${this.repairPhotos.length} attached`;
        if (!previewContainer) return;

        previewContainer.innerHTML = "";
        this.repairPhotos.forEach((file, idx) => {
            const isImg = file.type && file.type.startsWith("image/");
            const div = document.createElement("div");
            div.className = "relative group w-16 h-16 rounded-xl border border-slate-200 overflow-hidden bg-slate-50 hover:bg-white flex flex-col items-center justify-center shadow-sm cursor-pointer transition-all";
            div.title = `${file.name} (Click to open)`;

            if (isImg) {
                const img = document.createElement("img");
                img.src = URL.createObjectURL(file);
                img.className = "w-full h-full object-cover";
                div.appendChild(img);
                div.onclick = () => window.open(URL.createObjectURL(file), '_blank');
            } else {
                const name = (file.name || "").toLowerCase();
                let icon = "fas fa-file-alt text-slate-500";
                let tag = "FILE";
                let tagColor = "text-slate-600";
                if (name.endsWith(".pdf") || (file.type && file.type.includes("pdf"))) {
                    icon = "fas fa-file-pdf text-red-600";
                    tag = "PDF";
                    tagColor = "text-red-600";
                } else if (name.endsWith(".doc") || name.endsWith(".docx")) {
                    icon = "fas fa-file-word text-blue-600";
                    tag = "DOC";
                    tagColor = "text-blue-600";
                } else if (name.endsWith(".xls") || name.endsWith(".xlsx") || name.endsWith(".csv")) {
                    icon = "fas fa-file-excel text-emerald-600";
                    tag = "XLS";
                    tagColor = "text-emerald-600";
                }

                div.innerHTML = `
                    <i class="${icon} text-lg mb-0.5"></i>
                    <span class="text-[9px] font-black ${tagColor}">${tag}</span>
                `;
                div.onclick = () => window.open(URL.createObjectURL(file), '_blank');
            }

            const delBtn = document.createElement("button");
            delBtn.type = "button";
            delBtn.className = "absolute top-1 right-1 bg-red-600 text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px] opacity-80 hover:opacity-100 transition-opacity shadow z-10";
            delBtn.innerHTML = "&times;";
            delBtn.title = "Remove";
            delBtn.onclick = (e) => {
                e.stopPropagation();
                this.repairPhotos.splice(idx, 1);
                this.renderRepairPhotos();
            };
            div.appendChild(delBtn);
            previewContainer.appendChild(div);
        });
    };

    ItemIntakeManager.prototype.saveRepairEntry = function (isDraft = false) {
        this.syncRepairItemsFromDOM();
        const clientId = document.getElementById("repair-client-select")?.value;
        if (!clientId) {
            alert("Please select a Client/Vendor.");
            return;
        }

        const palletBarcode = document.getElementById("repair-active-pallet-barcode")?.value || this.repairBarcode;
        if (!palletBarcode) {
            alert("Pallet barcode is required. Please generate or select a pallet barcode.");
            return;
        }

        const putAwayLocation = document.getElementById("repair-put-away-location")?.value.trim();
        if (!putAwayLocation) {
            alert("Put-away Location is required.");
            return;
        }

        const intakeDate = document.getElementById("repair-intake-date")?.value || "";
        const jobNo = document.getElementById("repair-job-no")?.value.trim() || "";
        const marketingRef = document.getElementById("repair-marketing-ref")?.value || "No reference";
        const receivingEmployee = document.getElementById("repair-receiving-employee")?.value || "";
        const receivingNotes = document.getElementById("repair-receiving-notes")?.value || "";
        const techName = document.getElementById("repair-tech-name")?.value || "";
        const repairStatus = document.getElementById("repair-status-select")?.value || "Ready for Pickup";
        const techNotes = document.getElementById("repair-tech-notes")?.value || "";
        const sendEmail = document.getElementById("repair-send-email-checkbox")?.checked ? "1" : "0";

        const formData = new FormData();
        formData.append("intake_type", "Repair Intake");
        formData.append("barcode", palletBarcode);
        formData.append("client_id", clientId);
        formData.append("intake_date", intakeDate);
        formData.append("job_no", jobNo);
        formData.append("put_away_location", putAwayLocation);
        formData.append("marketing_reference", marketingRef);
        formData.append("receiving_employee", receivingEmployee);
        formData.append("receiving_notes", receivingNotes);
        formData.append("technician", techName);
        formData.append("technician_notes", techNotes);
        formData.append("repair_status", repairStatus);
        formData.append("send_email", sendEmail);

        if (isDraft) {
            formData.append("is_draft", "1");
            formData.append("status", "Draft");
        }

        this.repairItems.forEach((item, index) => {
            formData.append(`repair_items[${index}][name]`, item.name || "");
            formData.append(`repair_items[${index}][brand]`, item.brand || "");
            formData.append(`repair_items[${index}][model]`, item.model || "");
            formData.append(`repair_items[${index}][serial]`, item.serial || "");
        });

        if (this.repairPhotos && this.repairPhotos.length > 0) {
            for (let i = 0; i < this.repairPhotos.length; i++) {
                formData.append("photos[]", this.repairPhotos[i]);
            }
        }

        this.showLoading(isDraft ? "Saving repair entry as draft..." : "Saving repair entry...");

        fetch(SAVE_API, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
            body: formData,
        })
        .then(res => res.json())
        .then(data => {
            this.hideLoading();
            if (data.success) {
                const targetUrl = data.redirect_url || "/admin/repair-intake";
                window.location.href = targetUrl;
            } else {
                alert("Error: " + (data.message || "Failed to save repair entry."));
            }
        })
        .catch(err => {
            this.hideLoading();
            console.error("Save repair error:", err);
            alert("Failed to save repair entry.");
        });
    };
})();
/* jshint ignore:end */
/* eslint-enable */
