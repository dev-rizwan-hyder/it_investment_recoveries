const app = {
    currentStep: 1,
    intakeMode: "full",
    barcodeBase64: "",
    additionalItems: [],

    // Global DataTransfer holders so multiple selections accumulate
    _mainPhotoData: new DataTransfer(),
    _additionalPhotoData: {},

    init() {
        this.setupEventListeners();
        // Detect default mode (full or quick) based on UI or preference
        let defaultMode = "full";
        const activeBtn = document.querySelector("[data-mode].active");
        if (activeBtn && activeBtn.dataset.mode) {
            defaultMode = activeBtn.dataset.mode;
        }
        this.switchMode(defaultMode);
        this.renderStep();

        // Always generate a barcode for quick intake on page load if in quick mode
        if (defaultMode === "quick") {
            const quickNum = document.getElementById("barcodeNumberDisplay");
            if (
                !quickNum ||
                !quickNum.textContent ||
                quickNum.textContent === "Generating..." ||
                quickNum.textContent === ""
            ) {
                this.generateNewBarcode();
            }
        }
        // If full mode, generate barcode for full intake
        if (defaultMode === "full") {
            this.generateNewBarcode();
        }
    },

    attachUseBarcodeHandler() {
        // Attach handler for 'Use This Barcode' button (works for dynamic rendering)
        const attach = () => {
            const useBtn = document.getElementById("useGeneratedBarcodeBtn");
            if (useBtn && !useBtn.dataset.bound) {
                useBtn.dataset.bound = "true";
                useBtn.addEventListener("click", () => {
                    const barcode = document.getElementById(
                        "generatedBarcodeNumber",
                    ).textContent;
                    document.getElementById("manualBarcodeEntry").value =
                        barcode;
                    // Optionally, visually confirm selection
                    useBtn.textContent = "Barcode Selected!";
                    useBtn.classList.remove("bg-blue-600");
                    useBtn.classList.add("bg-green-600");
                    setTimeout(() => {
                        useBtn.textContent = "Use This Barcode";
                        useBtn.classList.remove("bg-green-600");
                        useBtn.classList.add("bg-blue-600");
                    }, 1000);
                    // Go to next step automatically
                    app.currentStep = 2;
                    app.renderStep();
                    // Re-attach for next barcode if needed
                    setTimeout(attach, 200);
                });
            }
        };
        // Try to attach immediately and after render changes
        attach();
        // Also attach after mode switch or barcode generation
        document.addEventListener("click", (e) => {
            if (e.target && e.target.id === "generateBarcodeBtn") {
                setTimeout(attach, 300);
            }
        });
    },

    setupEventListeners() {
        // Mode switching
        document.querySelectorAll("[data-mode]").forEach((btn) => {
            btn.addEventListener("click", (e) => {
                e.preventDefault();
                this.switchMode(e.target.dataset.mode);
            });
        });

        // Navigation
        document.getElementById("nextBtn")?.addEventListener("click", (e) => {
            e.preventDefault();
            this.nextStep();
        });
        document.getElementById("prevBtn")?.addEventListener("click", (e) => {
            e.preventDefault();
            this.prevStep();
        });
        document.getElementById("resetBtn")?.addEventListener("click", (e) => {
            e.preventDefault();
            this.resetForm();
        });

        // Full Mode Step 1: Barcode Entry
        document
            .getElementById("hasExistingBarcode")
            ?.addEventListener("change", (e) => {
                const hasExisting = e.target.checked;
                document.getElementById("newBarcodeSection").style.display =
                    hasExisting ? "none" : "block";
                document.getElementById(
                    "existingBarcodeSection",
                ).style.display = hasExisting ? "block" : "none";
            });

        document
            .getElementById("generateBarcodeBtn")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                this.generateNewBarcode();
            });

        // 3. USE THIS BARCODE logic (Navigate from Step 1 to Step 2)
        document
            .getElementById("useGeneratedBarcodeBtn")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                const barcode = document.getElementById(
                    "generatedBarcodeNumber",
                ).textContent;

                if (
                    barcode &&
                    barcode !== "---" &&
                    barcode !== "Generating..."
                ) {
                    document.getElementById("manualBarcodeEntry").value =
                        barcode;

                    // Success feedback and navigate
                    const btn = e.target.closest("button");
                    btn.innerHTML =
                        '<i class="fas fa-check mr-2"></i> BARCODE SELECTED!';
                    btn.classList.replace("bg-blue-600", "bg-green-600");

                    setTimeout(() => {
                        this.nextStep(); // Automatically moves to Step 2
                        // Button reset for next time
                        btn.innerHTML =
                            '<i class="fas fa-check mr-2"></i> Use This Barcode';
                        btn.classList.replace("bg-green-600", "bg-blue-600");
                    }, 600);
                } else {
                    this.showError("Please wait for barcode to generate.");
                }
            });

        document
            .getElementById("existingBarcodeSelect")
            ?.addEventListener("change", (e) => {
                if (e.target.value) {
                    document.getElementById("manualBarcodeEntry").value =
                        e.target.value;
                    this.displayExistingBarcodeImage(e.target.value);
                }
            });

        // Manual barcode entry - display image when user enters or pastes
        document
            .getElementById("manualBarcodeEntry")
            ?.addEventListener("input", (e) => {
                if (e.target.value && e.target.value.trim()) {
                    this.displayExistingBarcodeImage(e.target.value.trim());
                } else {
                    document.getElementById("selectedBarcodeDisplay").style.display = "none";
                }
            });

        // Scanner Button and Modal Handlers
        document
            .getElementById("openScannerBtn")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                this.openScannerModal();
            });

        document
            .getElementById("closeScannerBtn")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                this.closeScannerModal();
            });

        // Camera Mode Selection
        document
            .getElementById("cameraScanMode")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                this.switchToCamera();
            });

        // USB Mode Selection
        document
            .getElementById("usbScanMode")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                this.switchToUSBScanner();
            });

        // Camera Scanner Controls
        document
            .getElementById("scanAgainBtn")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                this.resetScannerUI();
            });

        document
            .getElementById("backToModeBtn")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                this.backToModeSelection();
            });

        document
            .getElementById("useScannerResult")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                this.useScannerResult();
            });

        // USB Scanner Controls
        document
            .getElementById("usbScannerInput")
            ?.addEventListener("keypress", (e) => {
                if (e.key === "Enter") {
                    e.preventDefault();
                    this.processUSBScan();
                }
            });

        document
            .getElementById("clearUsbInputBtn")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                this.clearUSBInput();
            });

        document
            .getElementById("backToModeBtn2")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                this.backToModeSelection();
            });

        document
            .getElementById("useUsbScannerResult")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                this.useUSBScannerResult();
            });

        // Client creation
        document
            .getElementById("newClientBtn")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                document.getElementById("newClientForm").style.display = "flex";
            });
        document
            .getElementById("closeClientForm")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                document.getElementById("newClientForm").style.display = "none";
            });
        document
            .getElementById("createClientBtn")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                this.createNewClient();
            });

        // Weight calculation (both modes)
        document
            .getElementById("grossWeight")
            ?.addEventListener("change", () => this.calculateNet());
        document
            .getElementById("tareWeight")
            ?.addEventListener("change", () => this.calculateNet());
        document
            .getElementById("grossWeightFull")
            ?.addEventListener("change", () => this.calculateNetFull());
        document
            .getElementById("tareWeightFull")
            ?.addEventListener("change", () => this.calculateNetFull());

        // Full Mode Step 1 & Step 2: Client Selection
        document
            .getElementById("selectedVendorFullModeStep1")
            ?.addEventListener("change", (e) => {
                const step2Select = document.getElementById("selectedVendorFullMode");
                const quickSelect = document.getElementById("selectedVendor");
                if (step2Select) step2Select.value = e.target.value;
                if (quickSelect) quickSelect.value = e.target.value;
                this.updateClientDisplay();
            });

        document
            .getElementById("selectedVendorFullMode")
            ?.addEventListener("change", (e) => {
                const step1Select = document.getElementById("selectedVendorFullModeStep1");
                const quickSelect = document.getElementById("selectedVendor");
                if (step1Select) step1Select.value = e.target.value;
                if (quickSelect) quickSelect.value = e.target.value;
                this.updateClientDisplay();
            });

        document
            .getElementById("selectedVendor")
            ?.addEventListener("change", (e) => {
                const step1Select = document.getElementById("selectedVendorFullModeStep1");
                const step2Select = document.getElementById("selectedVendorFullMode");
                if (step1Select) step1Select.value = e.target.value;
                if (step2Select) step2Select.value = e.target.value;
                this.updateClientDisplay();
            });

        // Edit Client Modal Triggers
        document.querySelectorAll(".openEditClientModalBtn").forEach((btn) => {
            btn.addEventListener("click", (e) => {
                e.preventDefault();
                this.openEditClientModal();
            });
        });

        document
            .getElementById("closeEditClientModalBtn")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                this.closeEditClientModal();
            });

        document
            .getElementById("cancelEditClientBtn")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                this.closeEditClientModal();
            });

        document
            .getElementById("saveEditClientBtn")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                this.updateClientDetails();
            });

        // Intake Date Syncing
        document
            .getElementById("intakeDateQuick")
            ?.addEventListener("change", (e) => {
                const fullDate = document.getElementById("intakeDateFullModeStep1");
                if (fullDate) fullDate.value = e.target.value;
            });

        document
            .getElementById("intakeDateFullModeStep1")
            ?.addEventListener("change", (e) => {
                const quickDate = document.getElementById("intakeDateQuick");
                if (quickDate) quickDate.value = e.target.value;
            });

        // Job # Generation Buttons
        ["generateJobNoBtnStep1", "generateJobNoBtnQuick", "generateJobNoBtnFull", "generateJobNoBtn"].forEach(btnId => {
            document.getElementById(btnId)?.addEventListener("click", (e) => {
                e.preventDefault();
                this.generateJobNumber();
            });
        });

        // Job # Syncing across all inputs
        const jobInputs = ["jobNoStep1", "jobNoQuick", "jobNoFull", "jobNo"];
        jobInputs.forEach(sourceId => {
            const inputEl = document.getElementById(sourceId);
            if (inputEl) {
                inputEl.addEventListener("input", (e) => {
                    const val = e.target.value;
                    jobInputs.forEach(targetId => {
                        if (targetId !== sourceId) {
                            const targetEl = document.getElementById(targetId);
                            if (targetEl) targetEl.value = val;
                        }
                    });
                    const jobNoDisplay = document.getElementById("reviewMainJobNo");
                    const jobNoVal = document.getElementById("reviewMainJobNoVal");
                    if (jobNoDisplay && jobNoVal) {
                        if (val.trim()) {
                            jobNoVal.textContent = val.trim();
                            jobNoDisplay.style.display = "block";
                        } else {
                            jobNoDisplay.style.display = "none";
                        }
                    }
                });
            }
        });

        // New Client Form Toggle Step 1
        document
            .getElementById("toggleNewClientFormBtnStep1")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                const formSection = document.getElementById("newClientFormSectionStep1");
                if (formSection) formSection.style.display = formSection.style.display === "none" ? "block" : "none";
            });

        // Create New Client Button Step 1
        document
            .getElementById("createNewClientBtnStep1")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                this.createNewClientFullModeStep1();
            });

        // Cancel New Client Button Step 1
        document
            .getElementById("cancelNewClientBtnStep1")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                const formSection = document.getElementById("newClientFormSectionStep1");
                if (formSection) formSection.style.display = "none";
            });

        // New Client Form Toggle
        document
            .getElementById("toggleNewClientFormBtn")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                const formSection = document.getElementById(
                    "newClientFormSection",
                );
                formSection.style.display =
                    formSection.style.display === "none" ? "block" : "none";
            });

        // Create New Client Button
        document
            .getElementById("createNewClientBtn")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                this.createNewClientFullMode();
            });

        // Cancel New Client Button
        document
            .getElementById("cancelNewClientBtn")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                document.getElementById("newClientFormSection").style.display =
                    "none";
            });

        // Add Another Item Button (Full Mode Step 2)
        document
            .getElementById("addAnotherItemBtn")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                this.addAnotherItemFullMode();
            });

        // Add Another Button from success view
        document
            .getElementById("addAnotherBtn")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                this.resetForm();
            });

        document
            .getElementById("addAnotherFull")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                this.resetForm();
            });

        // Form submission
        document
            .getElementById("recyclingForm")
            ?.addEventListener("submit", (e) => {
                e.preventDefault();
                this.submitForm(false);
            });

        document
            .getElementById("saveDraftBtnFull")
            ?.addEventListener("click", (e) => {
                e.preventDefault();
                this.submitForm(true);
            });

        // jQuery Photo Upload Handlers
        // Main pallet photo upload - accumulate selections & compress images
        $(document).on("change", "#mainPhotoInput, #quickPhotoInput", async (e) => {
            const input = e.target;
            const newFiles = Array.from(input.files || []);
            if (newFiles.length === 0) return;

            for (const file of newFiles) {
                const processedFile = await app.compressImage(file);
                app._mainPhotoData.items.add(processedFile);
            }

            app.renderMainPhotoPreviews();

            try {
                input.files = app._mainPhotoData.files;
            } catch (err) {
                console.warn('Could not assign aggregated files to input.files, submit handler will read from app._mainPhotoData');
            }
        });

        // Additional items photo upload (delegated event) - accumulate & compress per item index
        $(document).on("change", ".additionalPhotoInput", async (e) => {
            const input = e.target;
            const index = $(input).data("index");
            const newFiles = Array.from(input.files || []);
            if (newFiles.length === 0) return;

            if (!app._additionalPhotoData[index]) {
                app._additionalPhotoData[index] = new DataTransfer();
            }

            for (const file of newFiles) {
                const processedFile = await app.compressImage(file);
                app._additionalPhotoData[index].items.add(processedFile);
            }

            app.renderAdditionalPhotoPreviews(index);

            try {
                input.files = app._additionalPhotoData[index].files;
            } catch (err) {
                console.warn('Could not assign aggregated files to additional input.files; submit will read from app._additionalPhotoData');
            }
        });

        // Handle delete photo button clicks
        $(document).on("click", ".deleteMainPhoto", (e) => {
            e.preventDefault();
            const fileIndex = $(e.target).closest("button").data("fileIndex");
            app.deleteMainPhoto(fileIndex);
        });

        $(document).on("click", ".deleteAdditionalPhoto", (e) => {
            e.preventDefault();
            const fileIndex = $(e.target).closest("button").data("fileIndex");
            const itemIndex = $(e.target).closest("button").data("itemIndex");
            app.deleteAdditionalPhoto(itemIndex, fileIndex);
        });
    },

    getFileIcon(fileOrPath) {
        let type = '';
        let name = '';
        if (typeof fileOrPath === 'string') {
            name = fileOrPath;
            if (fileOrPath.startsWith('data:')) {
                type = fileOrPath.split(';')[0].replace('data:', '');
            }
        } else if (fileOrPath && fileOrPath.name) {
            name = fileOrPath.name;
            type = fileOrPath.type || '';
        }

        const ext = name ? name.split('.').pop().toLowerCase() : '';
        const isImg = type.startsWith('image/') || ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'].includes(ext);

        if (isImg) {
            return { isImage: true };
        }

        if (type.includes('pdf') || ext === 'pdf') {
            return { isImage: false, icon: 'fa-file-pdf', color: 'text-red-500', bg: 'bg-red-50', border: 'border-red-200', label: 'PDF' };
        }
        if (type.includes('word') || ['doc', 'docx'].includes(ext)) {
            return { isImage: false, icon: 'fa-file-word', color: 'text-blue-500', bg: 'bg-blue-50', border: 'border-blue-200', label: 'DOC' };
        }
        if (type.includes('excel') || type.includes('spreadsheet') || ['xls', 'xlsx', 'csv'].includes(ext)) {
            return { isImage: false, icon: 'fa-file-excel', color: 'text-emerald-500', bg: 'bg-emerald-50', border: 'border-emerald-200', label: 'EXCEL' };
        }
        if (type.includes('zip') || type.includes('compressed') || ['zip', 'rar', '7z', 'tar', 'gz'].includes(ext)) {
            return { isImage: false, icon: 'fa-file-archive', color: 'text-amber-500', bg: 'bg-amber-50', border: 'border-amber-200', label: 'ZIP' };
        }
        if (type.includes('text') || ['txt', 'rtf', 'log', 'json', 'xml'].includes(ext)) {
            return { isImage: false, icon: 'fa-file-alt', color: 'text-slate-600', bg: 'bg-slate-50', border: 'border-slate-200', label: 'TXT' };
        }

        return { isImage: false, icon: 'fa-file', color: 'text-indigo-500', bg: 'bg-indigo-50', border: 'border-indigo-200', label: ext ? ext.toUpperCase() : 'FILE' };
    },

    compressImage(file, maxDimension = 1600, quality = 0.82) {
        return new Promise((resolve) => {
            if (!file || !file.type || !file.type.startsWith('image/')) {
                resolve(file);
                return;
            }

            if (file.size < 400 * 1024) {
                resolve(file);
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    let width = img.width;
                    let height = img.height;

                    if (width <= maxDimension && height <= maxDimension && file.size < 800 * 1024) {
                        resolve(file);
                        return;
                    }

                    if (width > maxDimension || height > maxDimension) {
                        if (width >= height) {
                            height = Math.round((height / width) * maxDimension);
                            width = maxDimension;
                        } else {
                            width = Math.round((width / height) * maxDimension);
                            height = maxDimension;
                        }
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;

                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    canvas.toBlob(
                        (blob) => {
                            if (blob) {
                                const newName = file.name.replace(/\.[^/.]+$/, "") + ".jpg";
                                const compressedFile = new File([blob], newName, {
                                    type: 'image/jpeg',
                                    lastModified: Date.now()
                                });
                                resolve(compressedFile);
                            } else {
                                resolve(file);
                            }
                        },
                        'image/jpeg',
                        quality
                    );
                };
                img.onerror = () => resolve(file);
                img.src = e.target.result;
            };
            reader.onerror = () => resolve(file);
            reader.readAsDataURL(file);
        });
    },

    renderMainPhotoPreviews() {
        const containers = [
            document.getElementById("mainPhotoPreviews"),
            document.getElementById("quickPhotoPreviews")
        ].filter(Boolean);

        containers.forEach(container => { container.innerHTML = ''; });

        const files = Array.from(app._mainPhotoData.files);
        if (files.length === 0) return;

        const htmlParts = new Array(files.length);
        let loadedCount = 0;

        const finishRender = () => {
            const finalHtml = htmlParts.join('');
            containers.forEach(container => {
                container.innerHTML = finalHtml;
            });
            document.querySelectorAll('.deleteMainPhoto').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    app.deleteMainPhoto(parseInt(this.dataset.fileIndex, 10));
                });
            });
        };

        files.forEach((file, fileIndex) => {
            const fileInfo = app.getFileIcon(file);

            if (fileInfo.isImage) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    const src = event.target.result;
                    htmlParts[fileIndex] = `<div class="relative w-24 h-24 rounded-xl overflow-hidden border-2 border-blue-200 shadow-sm group bg-slate-100 flex items-center justify-center">
                            <img src="${src}" class="w-full h-full object-cover" loading="lazy">
                            <button type="button" class="deleteMainPhoto absolute inset-0 bg-red-600/80 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center text-white font-bold" data-file-index="${fileIndex}" title="Delete this file">
                                <i class="fas fa-trash-alt text-lg"></i>
                            </button>
                        </div>`;
                    loadedCount++;
                    if (loadedCount === files.length) finishRender();
                };
                reader.readAsDataURL(file);
            } else {
                htmlParts[fileIndex] = `<div class="relative w-24 h-24 rounded-xl overflow-hidden border-2 ${fileInfo.border} ${fileInfo.bg} shadow-sm group p-2 flex flex-col items-center justify-center text-center">
                        <i class="fas ${fileInfo.icon} ${fileInfo.color} text-2xl mb-1"></i>
                        <span class="text-[9px] font-bold text-slate-700 truncate w-full px-1">${app.escapeHtml(file.name)}</span>
                        <span class="text-[8px] font-extrabold ${fileInfo.color} uppercase">${fileInfo.label}</span>
                        <button type="button" class="deleteMainPhoto absolute inset-0 bg-red-600/80 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center text-white font-bold" data-file-index="${fileIndex}" title="Delete this file">
                            <i class="fas fa-trash-alt text-lg"></i>
                        </button>
                    </div>`;
                loadedCount++;
                if (loadedCount === files.length) finishRender();
            }
        });
    },

    renderAdditionalPhotoPreviews(index) {
        const previewContainer = document.getElementById("additionalPhotoPreviews" + index);
        if (!previewContainer) return;
        previewContainer.innerHTML = '';

        if (!app._additionalPhotoData[index]) return;

        const files = Array.from(app._additionalPhotoData[index].files);
        if (files.length === 0) return;

        const htmlParts = new Array(files.length);
        let loadedCount = 0;

        const finishRender = () => {
            previewContainer.innerHTML = htmlParts.join('');
            document.querySelectorAll('.deleteAdditionalPhoto').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    app.deleteAdditionalPhoto(parseInt(this.dataset.itemIndex, 10), parseInt(this.dataset.fileIndex, 10));
                });
            });
        };

        files.forEach((file, fileIndex) => {
            const fileInfo = app.getFileIcon(file);

            if (fileInfo.isImage) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    const src = event.target.result;
                    htmlParts[fileIndex] = `<div class="relative w-20 h-20 rounded-lg overflow-hidden border-2 border-emerald-100 shadow-sm group bg-slate-100 flex items-center justify-center">
                            <img src="${src}" class="w-full h-full object-cover" loading="lazy">
                            <button type="button" class="deleteAdditionalPhoto absolute inset-0 bg-red-600/80 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center text-white font-bold" data-file-index="${fileIndex}" data-item-index="${index}" title="Delete this file">
                                <i class="fas fa-trash-alt text-sm"></i>
                            </button>
                        </div>`;
                    loadedCount++;
                    if (loadedCount === files.length) finishRender();
                };
                reader.readAsDataURL(file);
            } else {
                htmlParts[fileIndex] = `<div class="relative w-20 h-20 rounded-lg overflow-hidden border-2 ${fileInfo.border} ${fileInfo.bg} shadow-sm group p-1.5 flex flex-col items-center justify-center text-center">
                        <i class="fas ${fileInfo.icon} ${fileInfo.color} text-xl mb-1"></i>
                        <span class="text-[8px] font-bold text-slate-700 truncate w-full px-1">${app.escapeHtml(file.name)}</span>
                        <span class="text-[7px] font-extrabold ${fileInfo.color} uppercase">${fileInfo.label}</span>
                        <button type="button" class="deleteAdditionalPhoto absolute inset-0 bg-red-600/80 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center text-white font-bold" data-file-index="${fileIndex}" data-item-index="${index}" title="Delete this file">
                            <i class="fas fa-trash-alt text-sm"></i>
                        </button>
                    </div>`;
                loadedCount++;
                if (loadedCount === files.length) finishRender();
            }
        });
    },

    deleteMainPhoto(fileIndex) {
        const files = Array.from(app._mainPhotoData.files);

        // Create a new DataTransfer without the deleted file
        app._mainPhotoData = new DataTransfer();
        files.forEach((file, idx) => {
            if (idx !== fileIndex) {
                app._mainPhotoData.items.add(file);
            }
        });

        // Update the file input
        try {
            const input = document.getElementById("mainPhotoInput");
            if (input) input.files = app._mainPhotoData.files;
        } catch (err) {
            console.warn('Could not update input.files after deletion');
        }

        // Re-render previews
        this.renderMainPhotoPreviews();
    },

    deleteAdditionalPhoto(itemIndex, fileIndex) {
        if (!app._additionalPhotoData[itemIndex]) {
            return;
        }

        const files = Array.from(app._additionalPhotoData[itemIndex].files);

        // Create a new DataTransfer without the deleted file
        app._additionalPhotoData[itemIndex] = new DataTransfer();
        files.forEach((file, idx) => {
            if (idx !== fileIndex) {
                app._additionalPhotoData[itemIndex].items.add(file);
            }
        });

        // Update the file input
        try {
            const input = document.querySelector(`.additionalPhotoInput[data-index="${itemIndex}"]`);
            if (input) {
                input.files = app._additionalPhotoData[itemIndex].files;
            }
        } catch (err) {
            console.warn('Could not update input.files after deletion');
        }

        // Re-render previews
        this.renderAdditionalPhotoPreviews(itemIndex);
    },

    switchMode(mode) {
        this.intakeMode = mode;

        // Update button styles
        document.querySelectorAll("[data-mode]").forEach((btn) => {
            if (btn.dataset.mode === mode) {
                btn.classList.add(
                    "bg-white",
                    "shadow-sm",
                    "text-blue-600",
                    "active",
                );
                btn.classList.remove("text-slate-500", "hover:text-slate-700");
            } else {
                btn.classList.remove(
                    "bg-white",
                    "shadow-sm",
                    "text-blue-600",
                    "active",
                );
                btn.classList.add("text-slate-500", "hover:text-slate-700");
            }
        });

        // Update mode alert
        const alert = document.getElementById("modeAlert");
        if (mode === "quick") {
            alert.innerHTML =
                '<span class="font-bold">Quick Intake Mode:</span> Enter customer info and description to generate a receipt quickly. You can complete weight and item details later by editing the pallet.';
            alert.style.display = "block";
        } else {
            alert.innerHTML =
                '<span class="font-bold">Full Intake Mode:</span> Complete all details including weights and item counts now. All information will be saved immediately.';
            alert.style.display = "block";
        }

        // Update progress bar display
        if (mode === "quick") {
            document.getElementById("quickModeProgress").style.display = "flex";
            document.getElementById("fullModeProgress").style.display = "none";
        } else {
            document.getElementById("quickModeProgress").style.display = "none";
            document.getElementById("fullModeProgress").style.display = "flex";
        }

        // Toggle form fields based on mode
        this.updateFormVisibility();

        // Reset to step 1
        this.currentStep = 1;
        this.renderStep();

        // If switching to quick mode and no barcode is present, generate one for quick intake
        if (mode === "quick") {
            const quickNum = document.getElementById("barcodeNumberDisplay");
            if (
                !quickNum ||
                !quickNum.textContent ||
                quickNum.textContent === "Generating..." ||
                quickNum.textContent === ""
            ) {
                this.generateNewBarcode();
            }
        }
    },

    updateFormVisibility() {
        const quickElements = document.querySelectorAll(".quick-mode-only");
        const fullElements = document.querySelectorAll(".full-mode-only");

        if (this.intakeMode === "quick") {
            quickElements.forEach((el) => (el.style.display = "block"));
            fullElements.forEach((el) => (el.style.display = "none"));
        } else {
            quickElements.forEach((el) => (el.style.display = "none"));
            fullElements.forEach((el) => (el.style.display = "block"));
        }
    },

    async generateNewBarcode() {
        const genBtn = document.getElementById("generateBarcodeBtn");
        const useBtnContainer = document.getElementById(
            "generatedBarcodeDisplay",
        );

        try {
            // Visual loading state
            if (genBtn)
                genBtn.innerHTML =
                    '<i class="fas fa-spinner fa-spin mr-2"></i> GENERATING...';

            const response = await fetch(API_ROUTES.generateBarcode, {
                method: "GET",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            const data = await response.json();
            if (data.success) {
                // UI Updates for Full Intake
                document.getElementById("generatedBarcodeNumber").textContent =
                    data.number;
                document.getElementById("generatedBarcodeText").textContent =
                    data.number;
                document.getElementById("generatedBarcodeImage").src =
                    data.base64;
                document.getElementById("manualBarcodeEntry").value =
                    data.number;

                // UI Updates for Quick Intake
                const quickNum = document.getElementById(
                    "barcodeNumberDisplay",
                );
                const quickText = document.getElementById("barcodeNumberText");
                const quickImg = document.getElementById("barcodeImageDisplay");
                if (quickNum) quickNum.textContent = data.number;
                if (quickText) quickText.textContent = data.number;
                if (quickImg) quickImg.src = data.base64;

                this.barcodeBase64 = data.base64;

                // Show the barcode container
                if (useBtnContainer) useBtnContainer.style.display = "block";

                // Reset regenerate button text
                if (genBtn)
                    genBtn.innerHTML =
                        '<i class="fas fa-qrcode mr-2 text-lg"></i> Regenerate New Barcode';
            }
        } catch (error) {
            console.error("Error generating barcode:", error);
            if (genBtn)
                genBtn.innerHTML =
                    '<i class="fas fa-sync mr-2"></i> Retry Generation';
            this.showError("Failed to generate barcode");
        }
    },

    async displayExistingBarcodeImage(barcodeNumber) {
        try {
            const response = await fetch(API_ROUTES.getBarcodeImage + '?barcode=' + encodeURIComponent(barcodeNumber), {
                method: "GET",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            const data = await response.json();
            if (data.success) {
                document.getElementById("selectedBarcodeNumber").textContent = data.number;
                document.getElementById("selectedBarcodeText").textContent = data.number;
                document.getElementById("selectedBarcodeImage").src = data.base64;
                document.getElementById("selectedBarcodeDisplay").style.display = "block";

                // Store base64 so it can be used in success screen
                this.barcodeBase64 = data.base64;
            } else {
                console.error("Failed to fetch barcode image:", data.error);
                document.getElementById("selectedBarcodeDisplay").style.display = "none";
            }
        } catch (error) {
            console.error("Error displaying barcode:", error);
            document.getElementById("selectedBarcodeDisplay").style.display = "none";
        }
    },

    async generateJobNumber() {
        const buttons = [
            document.getElementById("generateJobNoBtnStep1"),
            document.getElementById("generateJobNoBtnQuick"),
            document.getElementById("generateJobNoBtnFull"),
            document.getElementById("generateJobNoBtn"),
        ];

        try {
            buttons.forEach(btn => {
                if (btn) btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
            });

            let jobNo = "";
            if (typeof API_ROUTES !== "undefined" && API_ROUTES.generateJobNumber) {
                const response = await fetch(API_ROUTES.generateJobNumber, {
                    method: "GET",
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                });
                const data = await response.json();
                if (data && data.success && data.job_no) {
                    jobNo = String(data.job_no);
                }
            }

            if (!jobNo) {
                // Fallback numeric-only generation (6 digits)
                jobNo = Math.floor(100000 + Math.random() * 900000).toString();
            }

            // Guarantee ONLY NUMBERS
            jobNo = jobNo.replace(/\D/g, "");

            this.setJobNumber(jobNo);

            buttons.forEach(btn => {
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-check text-green-600"></i> Generated!';
                    setTimeout(() => {
                        if (btn.id === "generateJobNoBtnFull" || btn.id === "generateJobNoBtn") {
                            btn.innerHTML = '<i class="fas fa-bolt"></i> Generate';
                        } else {
                            btn.innerHTML = '<i class="fas fa-bolt"></i> Generate Job #';
                        }
                    }, 1200);
                }
            });
        } catch (error) {
            console.error("Error generating job number:", error);
            const fallbackJobNo = Math.floor(100000 + Math.random() * 900000).toString();
            this.setJobNumber(fallbackJobNo);

            buttons.forEach(btn => {
                if (btn) {
                    if (btn.id === "generateJobNoBtnFull" || btn.id === "generateJobNoBtn") {
                        btn.innerHTML = '<i class="fas fa-bolt"></i> Generate';
                    } else {
                        btn.innerHTML = '<i class="fas fa-bolt"></i> Generate Job #';
                    }
                }
            });
        }
    },

    setJobNumber(jobNo) {
        // Enforce ONLY numeric digits
        const numericOnly = String(jobNo || "").replace(/\D/g, "");
        const ids = ["jobNoStep1", "jobNoQuick", "jobNoFull", "jobNo"];
        ids.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.value = numericOnly;
                el.classList.add("ring-2", "ring-green-500", "bg-green-50/50");
                setTimeout(() => {
                    el.classList.remove("ring-2", "ring-green-500", "bg-green-50/50");
                }, 600);
            }
        });

        const jobNoDisplay = document.getElementById("reviewMainJobNo");
        const jobNoVal = document.getElementById("reviewMainJobNoVal");
        if (jobNoDisplay && jobNoVal) {
            if (numericOnly) {
                jobNoVal.textContent = numericOnly;
                jobNoDisplay.style.display = "block";
            } else {
                jobNoDisplay.style.display = "none";
            }
        }
    },

    async createNewClient() {
        try {
            const name = document.getElementById("clientName").value.trim();
            if (!name) {
                alert("Please enter a client name");
                return;
            }

            const formData = new FormData();
            formData.append("clientName", name);
            formData.append(
                "clientEmail",
                document.getElementById("clientEmail").value.trim(),
            );
            formData.append(
                "clientPhone",
                document.getElementById("clientPhone").value.trim(),
            );
            formData.append(
                "clientCompany",
                document.getElementById("clientCompany").value.trim(),
            );

            const response = await fetch(API_ROUTES.createClient, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN":
                        document.querySelector('[name="_token"]').value,
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: formData,
            });

            const data = await response.json();
            if (data.success) {
                // Add to dropdown
                const select = document.getElementById("selectedVendor");
                const option = document.createElement("option");
                option.value = data.client.id;
                option.text = data.client.name;
                option.dataset.email = data.client.email || "";
                option.dataset.phone = data.client.phone || "";
                option.dataset.company = data.client.company || "";
                option.selected = true;
                select.appendChild(option);

                // Clear and hide form
                document.getElementById("newClientForm").style.display = "none";
                document.getElementById("clientName").value = "";
                document.getElementById("clientEmail").value = "";
                document.getElementById("clientPhone").value = "";
                document.getElementById("clientCompany").value = "";
            } else {
                alert("Error: " + (data.message || "Failed to create client"));
            }
        } catch (error) {
            alert("Error creating client: " + error.message);
        }
    },

    calculateNet() {
        const gross =
            parseFloat(document.getElementById("grossWeight")?.value) || 0;
        const tare =
            parseFloat(document.getElementById("tareWeight")?.value) || 0;
        const net = Math.max(0, gross - tare);
        const display = document.getElementById("netWeightDisplay");
        if (display) display.textContent = net.toFixed(2) + " lbs";
    },

    calculateNetFull() {
        const gross =
            parseFloat(document.getElementById("grossWeightFull")?.value) || 0;
        const tare =
            parseFloat(document.getElementById("tareWeightFull")?.value) || 0;
        const net = Math.max(0, gross - tare);
        const display = document.getElementById("netWeightDisplayFull");
        if (display) display.textContent = net.toFixed(2) + " lbs";
    },

    nextStep() {
        if (this.validateStep(this.currentStep)) {
            const maxSteps = 3;
            if (this.currentStep < maxSteps) {
                this.currentStep++;
                this.renderStep();
            }
        }
    },

    prevStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.renderStep();
        }
    },

    validateStep(step) {
        if (step === 1) {
            if (this.intakeMode === "quick") {
                // Quick mode Step 1: barcode + vendor + location
                const barcodeNum = document
                    .getElementById("barcodeNumberDisplay")
                    .textContent.trim();
                if (!barcodeNum || barcodeNum === "Generating...") {
                    this.showError("Please generate a barcode");
                    return false;
                }
                if (!document.getElementById("selectedVendor").value) {
                    this.showError("Please select a vendor");
                    return false;
                }
                if (!document.getElementById("putAwayLocation").value.trim()) {
                    this.showError("Please enter a put-away location");
                    return false;
                }
            } else {
                // Full mode Step 1: barcode (generated or selected)
                const barcodeNum = document
                    .getElementById("manualBarcodeEntry")
                    .value.trim();
                if (!barcodeNum) {
                    this.showError("Please generate or select a barcode");
                    return false;
                }
            }
        }

        if (step === 2) {
            if (this.intakeMode === "quick") {
                // Quick mode validation - description is now optional
                return true;
            } else {
                // Full mode validation
                const vendorSelect =
                    document.getElementById("selectedVendorFullModeStep1") ||
                    document.getElementById("selectedVendorFullMode");
                const vendor = vendorSelect ? vendorSelect.value : "";
                if (!vendor) {
                    this.showError("Please select a vendor/client");
                    return false;
                }
                const putAway = document
                    .getElementById("putAwayLocationFull")
                    .value.trim();
                if (!putAway) {
                    this.showError("Please enter a put-away location");
                    return false;
                }
            }
        }

        return true;
    },

    renderStep() {
        // 1. Hide all step panes
        document.querySelectorAll(".step-pane").forEach((pane) => {
            pane.style.display = "none";
        });

        // 2. Show correct pane based on mode
        if (this.intakeMode === "quick") {
            const quickPane = document.querySelector(
                `.step-${this.currentStep}`,
            );
            if (quickPane) quickPane.style.display = "block";
        } else {
            // Full mode logic
            if (this.currentStep === 1) {
                document.querySelector(".step-full-1").style.display = "block";
            } else {
                const fullPane = document.querySelector(
                    `.step-${this.currentStep}`,
                );
                if (fullPane) fullPane.style.display = "block";
            }
        }

        // 3. Progress Line Update
        const progressWidth = ((this.currentStep - 1) / 2) * 100; // Assuming 3 steps (0%, 50%, 100%)
        const line = document.getElementById("progressLine");
        if (line) line.style.width = progressWidth + "%";

        // 4. Select Progress Container
        const progressContainer =
            this.intakeMode === "quick"
                ? document.getElementById("quickModeProgress")
                : document.getElementById("fullModeProgress");

        // 5. Update Indicators and Labels
        if (progressContainer) {
            progressContainer
                .querySelectorAll(".step-btn")
                .forEach((btn, idx) => {
                    const stepNum = idx + 1;
                    const indicator = btn.querySelector("div:first-child");
                    const label = btn.querySelector("span");

                    if (stepNum < this.currentStep) {
                        // STEP COMPLETED (Green)
                        indicator.className =
                            "w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center font-bold text-xs transition-all bg-green-500 text-white";
                        indicator.innerHTML = '<i class="fas fa-check"></i>';
                        if (label) {
                            label.classList.replace(
                                "text-slate-400",
                                "text-slate-800",
                            );
                        }
                    } else if (stepNum === this.currentStep) {
                        // STEP ACTIVE (Blue)
                        indicator.className =
                            "w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center font-bold text-xs transition-all bg-blue-600 text-white shadow-lg shadow-blue-200 scale-110";
                        indicator.textContent = stepNum;
                        if (label) {
                            label.classList.replace(
                                "text-slate-400",
                                "text-slate-800",
                            );
                        }
                    } else {
                        // STEP FUTURE (Grey)
                        indicator.className =
                            "w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center font-bold text-xs transition-all bg-slate-50 text-slate-400 border border-slate-200";
                        indicator.textContent = stepNum;
                        if (label) {
                            label.classList.replace(
                                "text-slate-800",
                                "text-slate-400",
                            );
                        }
                    }
                });
        }

        // 6. Navigation Buttons toggle
        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");

        if (prevBtn)
            prevBtn.style.display = this.currentStep === 1 ? "none" : "block";
        if (nextBtn)
            nextBtn.style.display = this.currentStep === 3 ? "none" : "block";

        // 7. Success View aur Review logic
        if (this.currentStep < 3) {
            const successView = document.getElementById("successView");
            const successViewFull = document.getElementById("successViewFull");
            const navBar = document.getElementById("navBar");

            if (successView) successView.style.display = "none";
            if (successViewFull) successViewFull.style.display = "none";
            if (navBar) navBar.style.display = "flex";
        }

        if (this.currentStep === 3) {
            this.updateReview();
        }
    },

    updateReview() {
        if (this.intakeMode === "quick") {
            this.updateReviewQuick();
        } else {
            this.populateReviewFull();
        }
    },

    formatDateDisplay(dateStr) {
        if (!dateStr) return "N/A";
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            const dateObj = new Date(parts[0], parts[1] - 1, parts[2]);
            return dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }
        return dateStr;
    },

    updateReviewQuick() {
        const barcodeNum = document.getElementById(
            "barcodeNumberDisplay",
        )?.textContent || "";

        const vendorSelect = document.getElementById("selectedVendor");
        const option = vendorSelect ? vendorSelect.options[vendorSelect.selectedIndex] : null;
        const clientName = option ? option.text : "N/A";
        const clientCompany = option ? (option.dataset.company || "N/A") : "N/A";
        const clientEmail = option ? (option.dataset.email || "client") : "client";
        const description = document.getElementById("description")?.value || "";
        const intakeDateVal = document.getElementById("intakeDateQuick")?.value || "";

        if (document.getElementById("reviewBarcode")) document.getElementById("reviewBarcode").textContent = barcodeNum;
        if (document.getElementById("headerBarcode")) document.getElementById("headerBarcode").textContent = barcodeNum;

        if (document.getElementById("reviewClient")) document.getElementById("reviewClient").textContent = clientName;
        if (document.getElementById("headerClientName")) document.getElementById("headerClientName").textContent = clientName;

        if (document.getElementById("reviewCompany")) document.getElementById("reviewCompany").textContent = clientCompany;
        if (document.getElementById("headerClientCompany")) document.getElementById("headerClientCompany").textContent = clientCompany;

        if (document.getElementById("sendEmailAddress")) document.getElementById("sendEmailAddress").textContent = clientEmail;

        if (document.getElementById("reviewDescription")) document.getElementById("reviewDescription").textContent = description;
        if (document.getElementById("reviewMainDescription")) document.getElementById("reviewMainDescription").textContent = description || "N/A";

        if (document.getElementById("headerIntakeDate")) {
            document.getElementById("headerIntakeDate").textContent = this.formatDateDisplay(intakeDateVal);
        }

        if (document.getElementById("emailLabel")) {
            document.getElementById("emailLabel").innerHTML =
                `<i class="fas fa-envelope mr-2 text-blue-400"></i> Send receipt email to ${clientEmail}`;
        }
    },

    async submitForm(isDraft = false) {
        try {
            const submitBtn = document.getElementById("saveBtnFull");
            const draftBtn = document.getElementById("saveDraftBtnFull");
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add("opacity-50", "cursor-not-allowed");
                if (!isDraft) {
                    const span = submitBtn.querySelector("span");
                    if (span)
                        span.innerHTML =
                            '<i class="fas fa-spinner fa-spin mr-2"></i> SAVING...';
                }
            }
            if (draftBtn) {
                draftBtn.disabled = true;
                draftBtn.classList.add("opacity-50", "cursor-not-allowed");
                if (isDraft) {
                    const span = draftBtn.querySelector("span");
                    if (span)
                        span.innerHTML =
                            '<i class="fas fa-spinner fa-spin mr-2"></i> SAVING AS DRAFT...';
                }
            }

            const formData = new FormData();

            // 1. Determine Barcode and Mode
            let barcodeNumber =
                this.intakeMode === "quick"
                    ? document.getElementById("barcodeNumberDisplay")
                        .textContent
                    : document.getElementById("manualBarcodeEntry").value;

            formData.append("barcodeNumber", barcodeNumber);
            formData.append("intakeMode", this.intakeMode);
            formData.append("status", isDraft ? "Draft" : "Received");

            if (this.intakeMode === "quick") {
                // QUICK MODE FIELDS
                formData.append(
                    "intakeDate",
                    document.getElementById("intakeDateQuick")?.value || "",
                );
                formData.append(
                    "selectedVendor",
                    document.getElementById("selectedVendor").value,
                );
                formData.append(
                    "putAwayLocation",
                    document.getElementById("putAwayLocation").value,
                );
                formData.append(
                    "description",
                    document.getElementById("description").value,
                );
                formData.append(
                    "notes",
                    document.getElementById("notes").value || "",
                );
                formData.append(
                    "receivingEmployee",
                    document.getElementById("receivingEmployee")?.value || "",
                );
                formData.append(
                    "jobNo",
                    document.getElementById("jobNoStep1")?.value || document.getElementById("jobNoQuick")?.value || document.getElementById("jobNo")?.value || document.getElementById("jobNoFull")?.value || "",
                );
                formData.append(
                    "processingEmployee",
                    document.getElementById("processingEmployee")?.value || "",
                );
                const quickEmailCheckbox = document.getElementById("sendEmail") || document.getElementById("sendEmailFull");
                const sendQuickEmail = (!isDraft && quickEmailCheckbox?.checked) ? true : false;

                if (sendQuickEmail) {
                    const vendorSelect = document.getElementById("selectedVendor");
                    const option = vendorSelect ? vendorSelect.options[vendorSelect.selectedIndex] : null;
                    if (!option || !(option.dataset.email || "").trim()) {
                        this.enableSubmitBtn();
                        this.showError("Selected client does not have an email address.");
                        return;
                    }
                }

                formData.append(
                    "sendEmail",
                    sendQuickEmail ? 1 : 0,
                );

                // Append main pallet photos in quick mode
                const quickPhotoInput = document.getElementById("quickPhotoInput") || document.getElementById("mainPhotoInput");
                const quickFiles = app._mainPhotoData && app._mainPhotoData.files.length > 0
                    ? Array.from(app._mainPhotoData.files)
                    : quickPhotoInput && quickPhotoInput.files.length > 0
                        ? Array.from(quickPhotoInput.files)
                        : [];

                if (quickFiles.length > 0) {
                    quickFiles.forEach((file, idx) => {
                        formData.append(`main_photos[${idx}]`, file);
                    });
                }
            } else {
                const fullVendorSelect = document.getElementById("selectedVendorFullModeStep1") || document.getElementById("selectedVendorFullMode");
                const fullVendorOption = fullVendorSelect ? fullVendorSelect.options[fullVendorSelect.selectedIndex] : null;
                const sendEmailFull = (!isDraft && document.getElementById("sendEmailFull")?.checked) ? true : false;

                if (sendEmailFull && !(fullVendorOption?.dataset.email || "").trim()) {
                    this.enableSubmitBtn();
                    this.showError("Selected client does not have an email address.");
                    return;
                }

                formData.append(
                    "intakeDate",
                    document.getElementById("intakeDateFullModeStep1")?.value || "",
                );
                formData.append(
                    "selectedVendor",
                    fullVendorSelect.value,
                );
                formData.append(
                    "putAwayLocation",
                    document.getElementById("putAwayLocationFull").value,
                );
                formData.append(
                    "description",
                    document.getElementById("descriptionFull").value,
                );
                formData.append(
                    "notes",
                    document.getElementById("notesFull").value || "",
                );
                formData.append(
                    "receivingEmployee",
                    document.getElementById("receivingEmployeeFull")?.value || "",
                );
                formData.append(
                    "jobNo",
                    document.getElementById("jobNoStep1")?.value || document.getElementById("jobNoFull")?.value || "",
                );
                formData.append(
                    "processingEmployee",
                    document.getElementById("processingEmployeeFull")?.value || "",
                );
                formData.append(
                    "marketingReference",
                    document.getElementById("marketingReferenceFull").value ||
                    "",
                );
                formData.append(
                    "estimatedCount",
                    document.getElementById("estimatedCountFull").value || 0,
                );
                formData.append(
                    "sendEmail",
                    sendEmailFull ? 1 : 0,
                );

                // Weights calculation
                const gross =
                    parseFloat(
                        document.getElementById("grossWeightFull").value,
                    ) || 0;
                const tare =
                    parseFloat(
                        document.getElementById("tareWeightFull").value,
                    ) || 0;

                const net = gross - tare > 0 ? gross - tare : 0;
                formData.append("grossWeight", gross);
                formData.append("tareWeight", tare);
                formData.append("netWeight", net); // Ensure it's 0 or more and numeric

                // Append main pallet photos as 'main_photos'
                const mainPhotoInput = document.getElementById("mainPhotoInput") || document.getElementById("quickPhotoInput");
                // Prefer aggregated DataTransfer if available
                const mainFiles = app._mainPhotoData && app._mainPhotoData.files.length > 0
                    ? Array.from(app._mainPhotoData.files)
                    : mainPhotoInput && mainPhotoInput.files.length > 0
                        ? Array.from(mainPhotoInput.files)
                        : [];

                if (mainFiles.length > 0) {
                    console.log('Processing ' + mainFiles.length + ' photos');
                    mainFiles.forEach((file, idx) => {
                        formData.append(`main_photos[${idx}]`, file);
                    });
                }

                // Additional Items Loop
                let validIndex = 0;
                this.additionalItems.forEach((item, index) => {
                    const itemDesc =
                        document.getElementById(`additionalDesc${index}`)
                            ?.value || "";
                    const itemGross =
                        parseFloat(
                            document.getElementById(`additionalGross${index}`)
                                ?.value,
                        ) || 0;
                    const itemTare =
                        parseFloat(
                            document.getElementById(`additionalTare${index}`)
                                ?.value,
                        ) || 0;

                    const itemNet =
                        itemGross - itemTare > 0 ? itemGross - itemTare : 0;

                    const itemLocation =
                        document.getElementById(
                            `additionalLocation${index}`,
                        )?.value.trim() ||
                        document.getElementById("putAwayLocationFull")?.value.trim() ||
                        "Warehouse";

                    formData.append(
                        `palletItems[${validIndex}][description]`,
                        itemDesc,
                    );
                    formData.append(
                        `palletItems[${validIndex}][grossWeight]`,
                        itemGross,
                    );
                    formData.append(
                        `palletItems[${validIndex}][tareWeight]`,
                        itemTare,
                    );
                    formData.append(
                        `palletItems[${validIndex}][netWeight]`,
                        itemNet,
                    ); // Ensure numeric value
                    formData.append(
                        `palletItems[${validIndex}][putAwayLocation]`,
                        itemLocation,
                    );
                    formData.append(
                        `palletItems[${validIndex}][estimatedCount]`,
                        document.getElementById(`additionalCount${index}`)
                            ?.value || 0,
                    );
                    formData.append(
                        `palletItems[${validIndex}][marketingReference]`,
                        document.getElementById(`additionalMarketing${index}`)
                            ?.value || "",
                    );
                    formData.append(
                        `palletItems[${validIndex}][notes]`,
                        document.getElementById(`additionalNotes${index}`)
                            ?.value || "",
                    );
                    formData.append(
                        `palletItems[${validIndex}][receivingEmployee]`,
                        document.getElementById(`additionalReceivingEmployee${index}`)
                            ?.value || document.getElementById("receivingEmployeeFull")?.value || "",
                    );
                    formData.append(
                        `palletItems[${validIndex}][jobNo]`,
                        document.getElementById(`additionalJobNo${index}`)
                            ?.value || document.getElementById("jobNoStep1")?.value || document.getElementById("jobNoFull")?.value || "",
                    );

                    // Handle photos (if input exists)
                    const photoInput = document.getElementById(
                        `additionalPhotos${index}`,
                    );
                    // Prefer aggregated DataTransfer for this index if present
                    const additionalFiles = app._additionalPhotoData && app._additionalPhotoData[index] && app._additionalPhotoData[index].files.length > 0
                        ? Array.from(app._additionalPhotoData[index].files)
                        : photoInput && photoInput.files.length > 0
                            ? Array.from(photoInput.files)
                            : [];

                    if (additionalFiles.length > 0) {
                        additionalFiles.forEach((file, fIdx) => {
                            formData.append(
                                `palletItems[${validIndex}][photos][${fIdx}]`,
                                file,
                            );
                        });
                    }
                    validIndex++;
                });
            }

            // Debugging: Check what is being sent
            for (var pair of formData.entries()) {
                console.log(pair[0] + ": " + pair[1]);
            }

            const response = await fetch(
                this.intakeMode === "quick"
                    ? API_ROUTES.saveQuick
                    : API_ROUTES.saveFull,
                {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN":
                            document.querySelector('[name="_token"]').value,
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: formData,
                },
            );

            const data = await response.json();

            if (data.success) {
                this.showSuccess(barcodeNumber, data);
            } else {
                this.enableSubmitBtn();
                this.showError(data.message || "Error saving data");
            }
        } catch (error) {
            console.error("Submission Error:", error);
            this.enableSubmitBtn();
            this.showError("Network Error: " + error.message);
        }
    },

    enableSubmitBtn() {
        const submitBtn = document.getElementById("saveBtnFull");
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.classList.remove("opacity-50", "cursor-not-allowed");
            const span = submitBtn.querySelector("span");
            if (span)
                span.innerHTML = '<i class="fas fa-save mr-3"></i> SAVE PALLET';
        }
        const draftBtn = document.getElementById("saveDraftBtnFull");
        if (draftBtn) {
            draftBtn.disabled = false;
            draftBtn.classList.remove("opacity-50", "cursor-not-allowed");
            const span = draftBtn.querySelector("span");
            if (span)
                span.innerHTML = '<i class="fas fa-file-alt mr-3 text-slate-400"></i> SAVE AS DRAFT';
        }
    },

    getDenverDateTime() {
        const now = new Date();
        const timeZone = "America/Denver";

        return {
            date: now.toLocaleDateString("en-US", {
                timeZone,
                year: "numeric",
                month: "long",
                day: "numeric",
            }),
            time: now.toLocaleTimeString("en-US", {
                timeZone,
                hour: "numeric",
                minute: "2-digit",
                second: "2-digit",
                timeZoneName: "short",
            }),
        };
    },

    getSelectedIntakeDate() {
        const inputVal = (this.intakeMode === "quick"
            ? document.getElementById("intakeDateQuick")?.value
            : document.getElementById("intakeDateFullModeStep1")?.value) || "";

        if (inputVal) {
            const parts = inputVal.split('-');
            if (parts.length === 3) {
                const dateObj = new Date(parts[0], parts[1] - 1, parts[2]);
                return dateObj.toLocaleDateString("en-US", {
                    year: "numeric",
                    month: "long",
                    day: "numeric",
                });
            }
        }

        return new Date().toLocaleDateString("en-US", {
            year: "numeric",
            month: "long",
            day: "numeric",
        });
    },

    showSuccess(barcodeNum, result = {}) {
        const targetId =
            this.intakeMode === "quick" ? "successView" : "successViewFull";
        const barcodeImgId =
            this.intakeMode === "quick"
                ? "successBarcodeImg"
                : "successBarcodeImgFull";
        const barcodeTextId =
            this.intakeMode === "quick"
                ? "successBarcode"
                : "successBarcodeFull";
        const subTextId =
            this.intakeMode === "quick"
                ? "successBarcodeText"
                : "successBarcodeTextFull";

        // Update Text
        document.getElementById(barcodeTextId).textContent = barcodeNum;
        document.getElementById(subTextId).textContent = barcodeNum;

        const titleElement = document.getElementById(
            this.intakeMode === "quick" ? "successTitle" : "successTitleFull",
        );
        if (titleElement) {
            const isDraftSaved = (result.pallet && result.pallet.status === 'Draft') || (result.message && result.message.toLowerCase().includes('draft'));
            titleElement.textContent = isDraftSaved ? "Draft Saved Successfully!" : "Item Added Successfully!";
        }

        const messageElement = document.getElementById(
            this.intakeMode === "quick" ? "successMessage" : "successMessageFull",
        );
        if (messageElement && result.message) {
            messageElement.textContent = result.message;
        }

        // Fix broken image: Use the base64 from current state
        const imgElement = document.getElementById(barcodeImgId);
        if (imgElement && this.barcodeBase64) {
            imgElement.src = this.barcodeBase64;
        }

        // Populate print label template
        this.populatePrintLabel(barcodeNum);

        // Show View
        const targetView = document.getElementById(targetId);
        targetView.style.display = "flex";
        document.getElementById("navBar").style.display = "none";

        // Scroll Down
        setTimeout(() => {
            targetView.scrollIntoView({ behavior: "smooth", block: "start" });
        }, 150);
    },

    populatePrintLabel(barcodeNum) {
        const printLabelTemplate = document.getElementById("printLabelTemplate");

        // Get client/vendor info (same for all labels)
        const vendorSelect =
            document.getElementById("selectedVendorFullModeStep1") ||
            document.getElementById("selectedVendorFullMode");
        const selectedOption = vendorSelect?.options[vendorSelect?.selectedIndex];
        const clientName = selectedOption?.text || "N/A";
        const clientCompany = selectedOption?.dataset.company || "N/A";
        const clientEmail = selectedOption?.dataset.email || "N/A";
        const clientPhone = selectedOption?.dataset.phone || "N/A";

        const dateStr = this.getSelectedIntakeDate();
        const timeStr = "";

        const mainReceivingEmp = document.getElementById("receivingEmployeeFull")?.value || document.getElementById("receivingEmployee")?.value || "";
        const mainJobNo = document.getElementById("jobNoStep1")?.value || document.getElementById("jobNoQuick")?.value || document.getElementById("jobNoFull")?.value || document.getElementById("jobNo")?.value || "";

        // Generate label for main item
        let allLabelsHTML = this.generateSingleLabel(
            barcodeNum,
            document.getElementById("descriptionFull")?.value || "N/A",
            document.getElementById("estimatedCountFull")?.value || "0",
            document.getElementById("grossWeightFull")?.value || "0.00",
            document.getElementById("tareWeightFull")?.value || "0.00",
            document.getElementById("netWeightDisplayFull")?.textContent || "0.00 lbs",
            document.getElementById("putAwayLocationFull")?.value || "N/A",
            document.getElementById("notesFull")?.value || "",
            clientName,
            clientCompany,
            clientEmail,
            clientPhone,
            this.barcodeBase64,
            dateStr,
            timeStr,
            mainReceivingEmp,
            mainJobNo
        );

        // Generate labels for additional items if they exist
        if (this.additionalItems && this.additionalItems.length > 0) {
            // Generate labels for additional items using the SAME barcode as main item
            this.additionalItems.forEach(item => {
                const addReceivingEmp = item.receivingEmployee || mainReceivingEmp;
                const addJobNo = item.jobNo || mainJobNo;
                const additionalLabelHTML = this.generateSingleLabel(
                    barcodeNum,  // Use same barcode number
                    item.description || "N/A",
                    item.estimatedCount || "0",
                    item.grossWeight || "0.00",
                    item.tareWeight || "0.00",
                    (Math.max(0, item.grossWeight - item.tareWeight) || 0).toFixed(2) + " lbs",
                    item.location || "N/A",
                    item.notes || "",
                    clientName,
                    clientCompany,
                    clientEmail,
                    clientPhone,
                    this.barcodeBase64,  // Use same barcode image
                    dateStr,
                    timeStr,
                    addReceivingEmp,
                    addJobNo
                );
                allLabelsHTML += additionalLabelHTML;
            });
        }

        printLabelTemplate.innerHTML = allLabelsHTML;
    },

    escapeHtml(value) {
        return String(value ?? "").replace(/[&<>"']/g, (character) => {
            const entities = {
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                '"': "&quot;",
                "'": "&#039;",
            };

            return entities[character];
        });
    },

    generateSingleLabel(barcodeNum, description, itemCount, grossWeight, tareWeight, netWeight, location, notes, clientName, clientCompany, clientEmail, clientPhone, barcodeImage, dateStr, timeStr, receivingEmployee = "", jobNo = "") {
        const safeBarcodeNum = this.escapeHtml(barcodeNum);
        const safeDescription = this.escapeHtml(description);
        const safeItemCount = this.escapeHtml(itemCount);
        const safeGrossWeight = this.escapeHtml(grossWeight);
        const safeTareWeight = this.escapeHtml(tareWeight);
        const safeNetWeight = this.escapeHtml(netWeight);
        const safeLocation = this.escapeHtml(location);
        const safeNotes = this.escapeHtml(notes);
        const safeClientName = this.escapeHtml(clientName);
        const safeClientCompany = this.escapeHtml(clientCompany);
        const safeClientEmail = this.escapeHtml(clientEmail);
        const safeClientPhone = this.escapeHtml(clientPhone);
        const safeBarcodeImage = this.escapeHtml(barcodeImage);
        const safeDateStr = this.escapeHtml(dateStr);
        const safeTimeStr = this.escapeHtml(timeStr);
        const safeReceivingEmployee = this.escapeHtml(receivingEmployee);
        const safeJobNo = this.escapeHtml(jobNo);

        return `
            <section class="recycling-print-label">
                <div class="label-head">
                    <strong>IT Investment Recoveries</strong>
                    <span>Pallet Label</span>
                </div>

                <div class="barcode-block">
                    ${safeBarcodeImage ? `<img src="${safeBarcodeImage}" alt="barcode">` : ""}
                    <div>${safeBarcodeNum}</div>
                </div>

                <div class="label-grid">
                    ${safeJobNo ? `<span>Job #</span><strong>${safeJobNo}</strong>` : ""}
                    ${safeReceivingEmployee ? `<span>Rec. Employee</span><strong>${safeReceivingEmployee}</strong>` : ""}
                    <span>Description</span><strong>${safeDescription}</strong>
                    <span>Client</span><strong>${safeClientName}</strong>
                    <span>Company</span><strong>${safeClientCompany}</strong>
                    <span>Phone</span><strong>${safeClientPhone}</strong>
                    <span>Email</span><strong>${safeClientEmail}</strong>
                    <span>Item Count</span><strong>${safeItemCount}</strong>
                    <span>Gross / Tare</span><strong>${safeGrossWeight} / ${safeTareWeight} lbs</strong>
                    <span>Net</span><strong>${safeNetWeight}</strong>
                    <span>Location</span><strong>${safeLocation}</strong>
                    ${safeNotes ? `<span>Notes</span><strong>${safeNotes}</strong>` : ""}
                </div>
                <div class="label-checks">
                   <span><i></i> Reuse</span>
                   <span><i></i> Universal E-waste</span>
                </div>


                <div class="label-foot">${safeDateStr}${safeTimeStr ? ' ' + safeTimeStr : ''} (Denver, CO)</div>
            </section>
        `;
        return `
            <div style="
                border: 2px solid #1e293b;
                padding: 25px;
                width: 420px;
                margin: auto;
                margin-bottom: 20px;
                font-family: 'Segoe UI', Tahoma, Geneva, sans-serif;
                color: #1e293b;
                background: #ffffff;
                box-sizing: border-box;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                page-break-after: always;
            ">
                    <div style="
                        font-size: 16px;
                        color: #0071e3;
                        text-transform: uppercase;
                        letter-spacing: 1.5px;
                        font-weight: 900;
                        margin-bottom: 4px;
                    ">PALLET LABEL</div>
                    <div style="
                        font-size: 11px;
                        color: #64748b;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                    ">Item Details</div>
                </div>
                
                <!-- Barcode Section -->
                <div style="
                    text-align: center;
                    margin: 14px 0;
                    padding: 12px;
                    background: #f8fafc;
                    border: 1px solid #e2e8f0;
                    border-radius: 6px;
                ">
                    <img src="${barcodeImage}" style="
                        max-width: 100%;
                        height: auto;
                        max-height: 50px;
                        margin-bottom: 6px;
                    " alt="barcode">
                    <div style="
                        font-family: 'Courier New', monospace;
                        font-size: 13px;
                        font-weight: bold;
                        letter-spacing: 2px;
                        color: #1e293b;
                        margin-top: 8px;
                    ">${barcodeNum}</div>
                </div>
                
                <!-- Detail Rows -->
                <div style="font-size: 11px; line-height: 1.7;">
                    <div style="
                        display: flex;
                        justify-content: space-between;
                        border-bottom: 1px solid #e2e8f0;
                        padding: 7px 0;
                    ">
                        <span style="
                            font-weight: bold;
                            color: #0071e3;
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                        ">Barcode:</span>
                        <span style="font-weight: 600; color: #1e293b;">${barcodeNum}</span>
                    </div>
                    
                    <div style="
                        display: flex;
                        justify-content: space-between;
                        border-bottom: 1px solid #e2e8f0;
                        padding: 7px 0;
                    ">
                        <span style="
                            font-weight: bold;
                            color: #0071e3;
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                        ">Description:</span>
                        <span style="font-weight: 600; color: #1e293b; text-align: right; max-width: 200px;">${description}</span>
                    </div>
                    
                    <div style="
                        display: flex;
                        justify-content: space-between;
                        border-bottom: 1px solid #e2e8f0;
                        padding: 7px 0;
                    ">
                        <span style="
                            font-weight: bold;
                            color: #0071e3;
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                        ">Customer:</span>
                        <span style="font-weight: 600; color: #1e293b;">${clientName}</span>
                    </div>

                    <div style="
                        display: flex;
                        justify-content: space-between;
                        border-bottom: 1px solid #e2e8f0;
                        padding: 7px 0;
                    ">
                        <span style="
                            font-weight: bold;
                            color: #0071e3;
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                        ">Client Company:</span>
                        <span style="font-weight: 600; color: #1e293b; text-align: right; max-width: 200px;">${clientCompany}</span>
                    </div>

                    <div style="
                        display: flex;
                        justify-content: space-between;
                        border-bottom: 1px solid #e2e8f0;
                        padding: 7px 0;
                    ">
                        <span style="
                            font-weight: bold;
                            color: #0071e3;
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                        ">Email:</span>
                        <span style="font-weight: 600; color: #1e293b; text-align: right; max-width: 230px; overflow-wrap: anywhere;">${clientEmail}</span>
                    </div>
                    
                    <div style="
                        display: flex;
                        justify-content: space-between;
                        border-bottom: 1px solid #e2e8f0;
                        padding: 7px 0;
                    ">
                        <span style="
                            font-weight: bold;
                            color: #0071e3;
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                        ">Gross Weight:</span>
                        <span style="font-weight: 600; color: #1e293b;">${grossWeight} lbs</span>
                    </div>
                    
                    <div style="
                        display: flex;
                        justify-content: space-between;
                        border-bottom: 1px solid #e2e8f0;
                        padding: 7px 0;
                    ">
                        <span style="
                            font-weight: bold;
                            color: #0071e3;
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                        ">Tare Weight:</span>
                        <span style="font-weight: 600; color: #1e293b;">${tareWeight} lbs</span>
                    </div>
                    
                    <div style="
                        display: flex;
                        justify-content: space-between;
                        border-bottom: 1px solid #10b981;
                        padding: 7px 0;
                        background: #ecfdf5;
                    ">
                        <span style="
                            font-weight: bold;
                            color: #059669;
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                        ">Net Weight:</span>
                        <span style="font-weight: 700; color: #047857;">${netWeight}</span>
                    </div>
                    
                    <div style="
                        display: flex;
                        justify-content: space-between;
                        border-bottom: 1px solid #e2e8f0;
                        padding: 7px 0;
                    ">
                        <span style="
                            font-weight: bold;
                            color: #0071e3;
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                        ">Location:</span>
                        <span style="font-weight: 600; color: #1e293b;">${location}</span>
                    </div>
                    
                    ${notes ? `<div style="
                        display: flex;
                        justify-content: space-between;
                        border-bottom: 1px solid #e2e8f0;
                        padding: 7px 0;
                    ">
                        <span style="
                            font-weight: bold;
                            color: #0071e3;
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                        ">Notes:</span>
                        <span style="font-weight: 600; color: #1e293b; text-align: right; max-width: 200px; font-size: 10px;">${notes}</span>
                    </div>` : ''}
                </div>
                
                <!-- Footer -->
                <div style="
                    text-align: center;
                    margin-top: 12px;
                    padding-top: 10px;
                    border-top: 1px dashed #cbd5e1;
                    font-size: 9px;
                    color: #94a3b8;
                ">
                    ${dateStr} ${timeStr} (Denver, CO)
                </div>
            </div>
        `;
    },

    showError(message) {
        const errorDiv = document.getElementById("errorMessages");
        errorDiv.innerHTML = `<p><i class="fas fa-exclamation-circle mr-1"></i>${message}</p>`;
        errorDiv.style.display = "block";
    },

    printLabel() {
        const barcodeNum =
            document.getElementById("successBarcodeFull")?.textContent;

        if (barcodeNum) {
            this.populatePrintLabel(barcodeNum);
        }

        const printLabelTemplate = document.getElementById("printLabelTemplate");
        const labelHTML = printLabelTemplate?.innerHTML || "";
        if (!labelHTML.trim()) {
            this.showError("No label data is available to print.");
            return;
        }

        const printWindow = window.open("", "_blank", "width=420,height=320");
        if (!printWindow) {
            this.showError("Popup blocked. Please allow popups to print the label.");
            return;
        }

        printWindow.document.open();
        printWindow.document.write(`
            <!doctype html>
            <html>
                <head>
                    <title>&#8203;</title>
                    <style>
                        @page {
                            size: 3.5in calc(2.25in + 5px);
                            margin: 0;
                        }

                        * {
                            box-sizing: border-box;
                        }

                        html,
                        body {
                            width: 3.5in;
                            min-height: calc(2.25in + 5px);
                            margin: 0;
                            padding: 0;
                            background: #fff;
                            color: #111827;
                            font-family: Arial, Helvetica, sans-serif;
                        }

                        .print-sheet {
                            width: 3.5in;
                        }

                        .recycling-print-label {
                            width: 3.5in;
                            height: calc(2.25in + 5px);
                            padding: 0.08in 0.1in;
                            border: 1px solid #111827;
                            background: #fff;
                            break-after: page;
                            page-break-after: always;
                            overflow: hidden;
                        }

                        .recycling-print-label:last-child {
                            break-after: auto;
                            page-break-after: auto;
                        }

                        .label-head {
                            display: flex;
                            align-items: baseline;
                            justify-content: space-between;
                            gap: 0.08in;
                            border-bottom: 1px solid #111827;
                            padding-bottom: 0.03in;
                            text-transform: uppercase;
                        }

                        .label-head strong {
                            font-size: 8.5pt;
                            line-height: 1;
                        }

                        .label-head span {
                            color: #475569;
                            font-size: 6.5pt;
                            font-weight: 700;
                            white-space: nowrap;
                        }

                        .barcode-block {
                            padding: 0.04in 0 0.03in;
                            text-align: center;
                        }

                        .barcode-block img {
                            display: block;
                            width: 100%;
                            max-height: 0.42in;
                            object-fit: contain;
                            margin: 0 auto;
                        }

                        .barcode-block div {
                            margin-top: 0.01in;
                            font-family: "Courier New", monospace;
                            font-size: 8.5pt;
                            font-weight: 700;
                            letter-spacing: 0.5pt;
                        }

                        .label-grid {
                            display: grid;
                            grid-template-columns: 0.78in 1fr;
                            column-gap: 0.06in;
                            row-gap: 0.015in;
                            font-size: 6.5pt;
                            line-height: 1.12;
                        }

                        .label-grid span {
                            color: #475569;
                            font-weight: 700;
                            text-transform: uppercase;
                        }

                        .label-grid strong {
                            min-width: 0;
                            font-weight: 700;
                            overflow-wrap: anywhere;
                        }

                        .label-checks {
                            display: flex;
                            justify-content: center;
                            gap: 0.18in;
                            margin-top: 0.04in;
                            font-size: 8px;
                            font-weight: 900;
                            text-transform: uppercase;
                        }

                        .label-checks span {
                            display: inline-flex;
                            align-items: center;
                            gap: 0.04in;
                        }

                        .label-checks i {
                            width: 0.09in;
                            height: 0.09in;
                            border: 1px solid #111827;
                            display: inline-block;
                        }

                        .label-foot {
                            margin-top: 0.04in;
                            border-top: 1px dashed #94a3b8;
                            padding-top: 0.02in;
                            color: #64748b;
                            font-size: 5.7pt;
                            text-align: center;
                        }

                        @media screen {
                            body {
                                background: #e5e7eb;
                                padding: 18px;
                            }

                            .recycling-print-label {
                                margin: 0 auto 12px;
                                box-shadow: 0 8px 20px rgba(15, 23, 42, 0.18);
                            }
                        }

                        @media print {
                            html,
                            body {
                                width: 3.5in;
                                margin: 0 !important;
                                padding: 0 !important;
                                background: #fff !important;
                            }

                            .recycling-print-label {
                                margin: 0;
                                box-shadow: none;
                            }
                        }
                    </style>
                </head>
                <body>
                    <main class="print-sheet">${labelHTML}</main>
                    <script>
                        window.addEventListener("load", function () {
                            window.focus();
                            window.print();
                        });
                    <\/script>
                </body>
            </html>
        `);
        printWindow.document.close();
    },

    printReceipt() {
        const barcodeNum =
            document.getElementById("successBarcode").textContent;

        let vendorSelect, selectedOption, clientName, clientCompany, clientEmail, clientPhone;

        if (this.intakeMode === "quick") {
            vendorSelect = document.getElementById("selectedVendor");
            selectedOption = vendorSelect.options[vendorSelect.selectedIndex];
            clientName = selectedOption.text;
            clientCompany = selectedOption.dataset.company || "N/A";
            clientEmail = selectedOption.dataset.email || "N/A";
            clientPhone = selectedOption.dataset.phone || "N/A";
        } else {
            vendorSelect =
                document.getElementById("selectedVendorFullModeStep1") ||
                document.getElementById("selectedVendorFullMode");
            selectedOption = vendorSelect ? vendorSelect.options[vendorSelect.selectedIndex] : null;
            clientName = selectedOption ? selectedOption.text : "N/A";
            clientCompany = selectedOption ? (selectedOption.dataset.company || "N/A") : "N/A";
            clientEmail = selectedOption ? (selectedOption.dataset.email || "N/A") : "N/A";
            clientPhone = selectedOption ? (selectedOption.dataset.phone || "N/A") : "N/A";
        }

        const intakeDateStr = this.getSelectedIntakeDate();

        let receiptHTML = `
            <html>
                <head>
                    <title>&#8203;</title>
                    <style>
                        @page { margin: 0; }
                        html { margin: 0; padding: 0; }
                        body { font-family: Arial, sans-serif; color: #000; background: #fff; margin: 0; padding: 0; }
                        .receipt { width: 8in; margin: auto; padding: 30px; background: #fff; line-height: 1.5; }
                        .header { text-align: center; border-bottom: 2px solid #000; margin-bottom: 5px; }
                        h1 { margin: 0; font-size: 28px; font-weight: bold; text-transform: uppercase; }
                        .subtitle { margin: 0; font-size: 16px; font-weight: bold; }
                        .info-row { display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1px solid #000; padding: 10px 0; margin-bottom: 20px; }
                        .info-label { font-size: 10px; color: #666; text-transform: uppercase; margin-bottom: 3px; }
                        .info-value { font-weight: bold; }
                        table { width: 100%; font-size: 12px; border-collapse: collapse; margin: 20px 0; }
                        th { background: #f8f9fa; border-bottom: 2px solid #000; padding: 8px; text-align: left; }
                        td { border-bottom: 1px solid #eee; padding: 8px; }
                        tfoot tr { border-top: 2px solid #000; }
                        .total-label { text-align: right; font-weight: bold; font-size: 14px; padding: 10px; }
                        .total-value { text-align: right; font-weight: bold; font-size: 14px; padding: 10px; border-bottom: 3px double #000; }
                        .barcode-section { text-align: center; margin: 40px 0; }
                        .barcode-img { height: 60px; margin: 10px 0; }
                        .barcode-text { font-family: monospace; font-size: 18px; font-weight: bold; letter-spacing: 2px; margin-top: 5px; }
                        .footer { text-align: center; border-top: 1px solid #000; padding-top: 15px; margin-top: 30px; }
                        .footer-title { margin: 0; font-weight: bold; font-size: 16px; }
                        .footer-text { margin: 5px 0 0 0; font-size: 10px; color: #666; }
                    </style>
                </head>
                <body onload="window.print(); window.close();">
                    <div class="receipt">
                        <div class="header">
                            <h1>IT Investment Recoveries</h1>
                            <p class="subtitle">E-waste Recycling Receipt</p>
                        </div>
                        
                        <div class="info-row">
                            <div>
                                <div class="info-label">Receipt Number</div>
                                <div class="info-value">REC-${new Date().getTime()}</div>
                            </div>
                            <div style="text-align: right;">
                                <div class="info-label">Date</div>
                                <div class="info-value">${intakeDateStr}</div>
                            </div>
                        </div>

                        <table>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="font-weight: bold; width: 200px;">Customer:</td>
                                <td style="text-align: right;">${clientName}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="font-weight: bold;">Client Company:</td>
                                <td style="text-align: right;">${clientCompany}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="font-weight: bold;">Client Phone:</td>
                                <td style="text-align: right;">${clientPhone}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="font-weight: bold;">Email:</td>
                                <td style="text-align: right;">${clientEmail}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="font-weight: bold;">Pallet Barcode:</td>
                                <td style="text-align: right;">${barcodeNum}</td>
                            </tr>
                            ${(document.getElementById("jobNoStep1")?.value || document.getElementById("jobNoQuick")?.value || document.getElementById("jobNo")?.value || document.getElementById("jobNoFull")?.value) ? `
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="font-weight: bold;">Job #:</td>
                                <td style="text-align: right;">${this.escapeHtml(document.getElementById("jobNoStep1")?.value || document.getElementById("jobNoQuick")?.value || document.getElementById("jobNo")?.value || document.getElementById("jobNoFull")?.value)}</td>
                            </tr>` : ''}
                            ${(document.getElementById("receivingEmployee")?.value || document.getElementById("receivingEmployeeFull")?.value) ? `
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="font-weight: bold;">Receiving Employee:</td>
                                <td style="text-align: right;">${this.escapeHtml(document.getElementById("receivingEmployee")?.value || document.getElementById("receivingEmployeeFull")?.value)}</td>
                            </tr>` : ''}
                        </table>

                        <p style="font-size: 11px; font-weight: bold; text-transform: uppercase; margin: 20px 0 5px 0;">Itemized Breakdown:</p>

                        <table>
                            <thead>
                                <tr>
                                    <th>Unit</th>
                                    <th>Description</th>
                                    <th style="text-align: right;">Gross</th>
                                    <th style="text-align: right;">Tare</th>
                                    <th style="text-align: right;">Net Weight</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#01 (Main)</td>
                                    <td>${document.getElementById("descriptionFull")?.value || document.getElementById("description").value || "N/A"}</td>
                                    <td style="text-align: right;">${document.getElementById("grossWeightFull")?.value || document.getElementById("grossWeight")?.value || "0"}</td>
                                    <td style="text-align: right;">${document.getElementById("tareWeightFull")?.value || document.getElementById("tareWeight")?.value || "0"}</td>
                                    <td style="text-align: right; font-weight: bold;">${document.getElementById("netWeightDisplayFull")?.textContent || document.getElementById("netWeightDisplay")?.textContent || "0.00"} lbs</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="total-label">Total Shipment Weight:</td>
                                    <td class="total-value">${document.getElementById("netWeightDisplayFull")?.textContent || document.getElementById("netWeightDisplay")?.textContent || "0.00"} lbs</td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="barcode-section">
                            <p style="font-size: 10px; color: #9ca3af; text-transform: uppercase;">Pallet Barcode Reference:</p>
                            <img id="receiptBarcodeImg" src="" class="barcode-img" alt="barcode">
                            <div class="barcode-text">${barcodeNum}</div>
                        </div>

                        <div class="footer">
                            <p class="footer-title">Thank you for recycling with us!</p>
                            <p class="footer-text">This receipt confirms the drop-off of recyclable materials.</p>
                        </div>
                    </div>
                </body>
            </html>
        `;

        const printWindow = window.open("", "_blank");
        printWindow.document.write(receiptHTML);
        printWindow.document.close();

        setTimeout(() => {
            const barcodeImg =
                printWindow.document.getElementById("receiptBarcodeImg");
            if (barcodeImg) {
                barcodeImg.src = this.barcodeBase64;
            }
        }, 100);
    },

    updateClientDisplay() {
        const vendorSelectStep2 = document.getElementById("selectedVendorFullMode");
        const vendorSelectStep1 = document.getElementById("selectedVendorFullModeStep1");
        const vendorSelectQuick = document.getElementById("selectedVendor");

        let selectedValue = "";
        let activeSelect = null;

        if (this.intakeMode === "quick" && vendorSelectQuick && vendorSelectQuick.value) {
            selectedValue = vendorSelectQuick.value;
            activeSelect = vendorSelectQuick;
        } else if (vendorSelectStep1 && vendorSelectStep1.value) {
            selectedValue = vendorSelectStep1.value;
            activeSelect = vendorSelectStep1;
        } else if (vendorSelectStep2 && vendorSelectStep2.value) {
            selectedValue = vendorSelectStep2.value;
            activeSelect = vendorSelectStep2;
        } else if (vendorSelectQuick && vendorSelectQuick.value) {
            selectedValue = vendorSelectQuick.value;
            activeSelect = vendorSelectQuick;
        }

        if (selectedValue) {
            if (vendorSelectStep1 && vendorSelectStep1.value !== selectedValue) vendorSelectStep1.value = selectedValue;
            if (vendorSelectStep2 && vendorSelectStep2.value !== selectedValue) vendorSelectStep2.value = selectedValue;
            if (vendorSelectQuick && vendorSelectQuick.value !== selectedValue) vendorSelectQuick.value = selectedValue;
        }

        const displayDivStep2 = document.getElementById("selectedClientDisplay");
        const displayDivStep1 = document.getElementById("selectedClientDisplayStep1");

        if (selectedValue && activeSelect && activeSelect.selectedIndex >= 0) {
            const option = activeSelect.options[activeSelect.selectedIndex];
            if (option && option.value) {
                const clientName = option.dataset.name || option.text;
                const clientCompany = option.dataset.company || "Personal Client";
                const clientEmail = option.dataset.email || "No Email";

                if (document.getElementById("selectedClientName")) {
                    document.getElementById("selectedClientName").textContent = clientName;
                }
                if (document.getElementById("selectedClientMeta")) {
                    document.getElementById("selectedClientMeta").textContent = `${clientCompany} • ${clientEmail}`;
                }
                if (displayDivStep2) displayDivStep2.style.display = "block";

                if (document.getElementById("selectedClientNameStep1")) {
                    document.getElementById("selectedClientNameStep1").textContent = clientName;
                }
                if (document.getElementById("selectedClientMetaStep1")) {
                    document.getElementById("selectedClientMetaStep1").textContent = `${clientCompany} • ${clientEmail}`;
                }
                if (displayDivStep1) displayDivStep1.style.display = "block";
                return;
            }
        }

        if (displayDivStep2) displayDivStep2.style.display = "none";
        if (displayDivStep1) displayDivStep1.style.display = "none";
    },

    openEditClientModal() {
        const vendorSelectStep1 = document.getElementById("selectedVendorFullModeStep1");
        const vendorSelectQuick = document.getElementById("selectedVendor");
        const vendorSelectStep2 = document.getElementById("selectedVendorFullMode");

        let activeSelect = null;
        if (this.intakeMode === "quick" && vendorSelectQuick && vendorSelectQuick.value) {
            activeSelect = vendorSelectQuick;
        } else if (vendorSelectStep1 && vendorSelectStep1.value) {
            activeSelect = vendorSelectStep1;
        } else if (vendorSelectStep2 && vendorSelectStep2.value) {
            activeSelect = vendorSelectStep2;
        } else if (vendorSelectQuick && vendorSelectQuick.value) {
            activeSelect = vendorSelectQuick;
        }

        if (!activeSelect || !activeSelect.value) {
            alert("Please select a client to edit.");
            return;
        }

        const clientId = activeSelect.value;
        const option = activeSelect.options[activeSelect.selectedIndex];
        if (!option) return;

        document.getElementById("editClientId").value = clientId;
        document.getElementById("editClientName").value = option.dataset.name || option.text || "";
        document.getElementById("editClientEmail").value = option.dataset.email || "";
        document.getElementById("editClientPhone").value = option.dataset.phone || "";
        document.getElementById("editClientCompany").value = option.dataset.company || "";
        document.getElementById("editClientAddress").value = option.dataset.address || "";

        const modal = document.getElementById("editClientModal");
        if (modal) modal.style.display = "flex";
    },

    closeEditClientModal() {
        const modal = document.getElementById("editClientModal");
        if (modal) modal.style.display = "none";
    },

    async updateClientDetails() {
        const id = document.getElementById("editClientId").value;
        const name = document.getElementById("editClientName").value.trim();
        if (!name) {
            alert("Please enter a client name");
            return;
        }

        const email = document.getElementById("editClientEmail").value.trim();
        const phone = document.getElementById("editClientPhone").value.trim();
        const company = document.getElementById("editClientCompany").value.trim();
        const address = document.getElementById("editClientAddress").value.trim();

        const btn = document.getElementById("saveEditClientBtn");
        const origText = btn ? btn.innerHTML : "";
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Updating...';
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
                              document.querySelector('input[name="_token"]')?.value;

            const response = await fetch(`/admin/clients/${id}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: JSON.stringify({
                    _method: "PUT",
                    name: name,
                    email: email,
                    phone: phone,
                    company: company,
                    address: address
                })
            });

            const result = await response.json();

            if (result.success) {
                const clientSelects = [
                    document.getElementById("selectedVendorFullModeStep1"),
                    document.getElementById("selectedVendor"),
                    document.getElementById("selectedVendorFullMode")
                ];

                clientSelects.forEach(select => {
                    if (!select) return;
                    const opt = select.querySelector(`option[value="${id}"]`);
                    if (opt) {
                        opt.textContent = name;
                        opt.dataset.name = name;
                        opt.dataset.email = email;
                        opt.dataset.phone = phone;
                        opt.dataset.company = company;
                        opt.dataset.address = address;
                    }
                });

                this.updateClientDisplay();
                this.closeEditClientModal();
            } else {
                alert(result.message || "Failed to update client");
            }
        } catch (err) {
            console.error(err);
            alert("Error updating client: " + err.message);
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = origText;
            }
        }
    },

    async createNewClientFullModeStep1() {
        try {
            const name = document
                .getElementById("newClientNameInputStep1")
                .value.trim();
            if (!name) {
                this.showError("Please enter a client name");
                return;
            }

            const formData = new FormData();
            formData.append("clientName", name);
            formData.append(
                "clientEmail",
                document.getElementById("newClientEmailInputStep1").value.trim(),
            );
            formData.append(
                "clientPhone",
                document.getElementById("newClientPhoneInputStep1").value.trim(),
            );
            formData.append(
                "clientCompany",
                document.getElementById("newClientCompanyInputStep1").value.trim(),
            );
            formData.append(
                "clientAddress",
                document.getElementById("newClientAddressInputStep1").value.trim(),
            );

            const response = await fetch(API_ROUTES.createClient, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN":
                        document.querySelector('[name="_token"]').value,
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: formData,
            });

            const data = await response.json();
            if (data.success) {
                const selects = [
                    document.getElementById("selectedVendorFullModeStep1"),
                    document.getElementById("selectedVendorFullMode"),
                    document.getElementById("selectedVendor")
                ];

                selects.forEach(select => {
                    if (select) {
                        const option = document.createElement("option");
                        option.value = data.client.id;
                        option.text = data.client.name;
                        option.dataset.email = data.client.email || "";
                        option.dataset.phone = data.client.phone || "";
                        option.dataset.company = data.client.company || "";
                        option.selected = true;
                        select.appendChild(option);
                        select.value = data.client.id;
                    }
                });

                document.getElementById("newClientNameInputStep1").value = "";
                document.getElementById("newClientEmailInputStep1").value = "";
                document.getElementById("newClientPhoneInputStep1").value = "";
                document.getElementById("newClientCompanyInputStep1").value = "";
                document.getElementById("newClientAddressInputStep1").value = "";
                document.getElementById("newClientFormSectionStep1").style.display = "none";

                this.updateClientDisplay();
            } else {
                this.showError(data.message || "Failed to create client");
            }
        } catch (error) {
            this.showError("Error creating client: " + error.message);
        }
    },

    async createNewClientFullMode() {
        try {
            const name = document
                .getElementById("newClientNameInput")
                .value.trim();
            if (!name) {
                this.showError("Please enter a client name");
                return;
            }

            const formData = new FormData();
            formData.append("clientName", name);
            formData.append(
                "clientEmail",
                document.getElementById("newClientEmailInput").value.trim(),
            );
            formData.append(
                "clientPhone",
                document.getElementById("newClientPhoneInput").value.trim(),
            );
            formData.append(
                "clientCompany",
                document.getElementById("newClientCompanyInput").value.trim(),
            );
            formData.append(
                "clientAddress",
                document.getElementById("newClientAddressInput").value.trim(),
            );

            const response = await fetch(API_ROUTES.createClient, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN":
                        document.querySelector('[name="_token"]').value,
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: formData,
            });

            const data = await response.json();
            if (data.success) {
                const select = document.getElementById(
                    "selectedVendorFullMode",
                );
                const option = document.createElement("option");
                option.value = data.client.id;
                option.text = data.client.name;
                option.dataset.email = data.client.email || "";
                option.dataset.phone = data.client.phone || "";
                option.dataset.company = data.client.company || "";
                option.selected = true;
                select.appendChild(option);

                document.getElementById("newClientNameInput").value = "";
                document.getElementById("newClientEmailInput").value = "";
                document.getElementById("newClientPhoneInput").value = "";
                document.getElementById("newClientCompanyInput").value = "";
                document.getElementById("newClientAddressInput").value = "";
                document.getElementById("newClientFormSection").style.display =
                    "none";

                this.updateClientDisplay();
            } else {
                this.showError(data.message || "Failed to create client");
            }
        } catch (error) {
            this.showError("Error creating client: " + error.message);
        }
    },

    addAnotherItemFullMode() {
        const itemIndex = this.additionalItems.length;
        const defaultReceiving = document.getElementById("receivingEmployeeFull")?.value || "";
        const defaultJobNo = document.getElementById("jobNoStep1")?.value || document.getElementById("jobNoFull")?.value || "";
        this.additionalItems.push({
            description: "",
            estimatedCount: 0,
            grossWeight: 0,
            tareWeight: 0,
            netWeight: 0,
            location: "",
            marketing: "",
            notes: "",
            receivingEmployee: defaultReceiving,
            jobNo: defaultJobNo,
        });
        this.renderAdditionalItem(itemIndex);
    },

    renderAdditionalItem(index) {
        const container = document.getElementById("additionalItemsContainer");
        const itemNum = index + 2;

        // Retrieve marketing reference options from hidden data
        const marketingRefsDiv = document.getElementById("marketingRefsData");
        let marketingOptions = '<option value="">-- Use Default --</option>';
        if (marketingRefsDiv) {
            const refs = marketingRefsDiv.querySelectorAll('[data-ref]');
            refs.forEach((ref) => {
                const value = ref.getAttribute('data-ref');
                marketingOptions += `<option value="${value}">${value}</option>`;
            });
        }

        // Retrieve employee options from hidden data
        const employeesDiv = document.getElementById("employeesData");
        let employeeOptions = '<option value="">-- Select Receiving Employee --</option>';
        if (employeesDiv) {
            const emps = employeesDiv.querySelectorAll('[data-name]');
            emps.forEach((emp) => {
                const name = emp.getAttribute('data-name');
                const empId = emp.getAttribute('data-id');
                employeeOptions += `<option value="${name}">${name}${empId ? ' (' + empId + ')' : ''}</option>`;
            });
        }

        const itemHTML = `
            <div class="bg-white rounded-xl border-l-4 border-emerald-500 p-6 shadow-sm relative mt-6 animate-in slide-in-from-top-4">
                <button type="button" onclick="app.removeItemFullMode(${index})"
                    class="absolute top-6 right-6 text-red-400 hover:text-red-600">
                    <i class="fas fa-trash-alt text-lg"></i>
                </button>

                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                    <span class="w-8 h-8 bg-emerald-500 text-white rounded-full flex items-center justify-center mr-3 text-sm font-black italic">${itemNum}</span>
                    Item #${itemNum}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Description</label>
                        <input type="text" id="additionalDesc${index}" placeholder="Item Description"
                            class="w-full border-slate-200 rounded-lg p-3 border text-sm" 
                            onchange="app.updateItemData(${index}, 'description', this.value)">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Estimated Count</label>
                        <input type="number" id="additionalCount${index}" placeholder="Count"
                            class="w-full border-slate-200 rounded-lg p-3 border text-sm" 
                            onchange="app.updateItemData(${index}, 'estimatedCount', this.value)">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 bg-emerald-50/30 p-4 rounded-xl border border-emerald-50">
                    <div>
                        <label class="block text-[10px] font-bold text-emerald-700 uppercase mb-1">Gross (lbs)</label>
                        <input type="number" id="additionalGross${index}" step="0.1" placeholder="0.00"
                            class="w-full p-3 border rounded-lg text-sm font-bold" 
                            oninput="app.calculateAdditionalNet(${index})"
                            onchange="app.calculateAdditionalNet(${index})">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-emerald-700 uppercase mb-1">Tare (lbs)</label>
                        <input type="number" id="additionalTare${index}" step="0.1" placeholder="0.00"
                            class="w-full p-3 border rounded-lg text-sm font-bold" 
                            oninput="app.calculateAdditionalNet(${index})"
                            onchange="app.calculateAdditionalNet(${index})">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-emerald-700 uppercase mb-1">Net Weight</label>
                        <div id="additionalNet${index}" class="w-full p-3 bg-white text-emerald-700 font-black rounded-lg border border-emerald-100 text-sm flex items-center justify-center">0.00 lbs</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Put-away Location *</label>
                        <input type="text" id="additionalLocation${index}" placeholder="Location"
                            class="w-full border-slate-200 rounded-lg p-3 border text-sm" 
                            onchange="app.updateItemData(${index}, 'location', this.value)">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 text-emerald-700">Marketing Reference</label>
                        <select id="additionalMarketing${index}"
                            class="w-full border-slate-200 rounded-lg p-3 border bg-white text-sm" onchange="app.updateItemData(${index}, 'marketing', this.value)">
                            ${marketingOptions}
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Receiving Employee</label>
                        <input type="text" id="additionalReceivingEmployee${index}" list="employeesList"
                            value="${this.escapeHtml(this.additionalItems[index]?.receivingEmployee || '')}"
                            class="w-full border-slate-200 rounded-lg p-3 text-sm border focus:ring-4 focus:ring-blue-500/5 outline-none font-medium"
                            placeholder="Enter receiving employee name..."
                            oninput="app.updateItemData(${index}, 'receivingEmployee', this.value)"
                            onchange="app.updateItemData(${index}, 'receivingEmployee', this.value)">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Job #</label>
                        <input type="text" id="additionalJobNo${index}"
                            value="${this.escapeHtml(this.additionalItems[index]?.jobNo || '')}"
                            class="w-full border-slate-200 rounded-lg p-3 text-sm border focus:ring-4 focus:ring-blue-500/5 outline-none font-medium"
                            placeholder="e.g., JOB-10023"
                            oninput="app.updateItemData(${index}, 'jobNo', this.value)"
                            onchange="app.updateItemData(${index}, 'jobNo', this.value)">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Notes</label>
                    <textarea id="additionalNotes${index}" placeholder="Notes for this specific item..."
                        class="w-full border-slate-200 rounded-lg p-3 text-sm border h-24 focus:ring-4 focus:ring-blue-500/5 outline-none" 
                        onchange="app.updateItemData(${index}, 'notes', this.value)"></textarea>
                </div>

                <div class="mt-6 border-t pt-6 border-emerald-50">
                    <label class="block text-[10px] font-black text-emerald-700 mb-3 uppercase tracking-tighter italic">Item Photos & Files</label>
                    <div class="flex flex-wrap gap-3">
                        <div id="additionalPhotoPreviews${index}" class="flex flex-wrap gap-3 contents"></div>
                        <label class="cursor-pointer bg-white hover:bg-emerald-50 border-2 border-dashed border-emerald-200 px-4 py-3 rounded-xl font-bold text-[9px] text-emerald-500 transition-all flex flex-col items-center justify-center min-w-[120px] text-center">
                            <i class="fas fa-paperclip text-sm mb-1"></i>
                            <span>ADD IMAGE OR FILE</span>
                            <input type="file" id="additionalPhotos${index}" name="additionalPhotos${index}" multiple class="hidden additionalPhotoInput" accept="*/*" data-index="${index}">
                        </label>
                    </div>
                    <div id="additionalPhotoProgress${index}" class="w-full bg-slate-100 rounded-full h-1 mt-2 overflow-hidden hidden">
                        <div class="bg-emerald-500 h-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        `;

        if (container.innerHTML === "") {
            container.innerHTML = itemHTML;
        } else {
            // Use insertAdjacentHTML instead of += to prevent re-parsing existing HTML
            container.insertAdjacentHTML('beforeend', itemHTML);
        }
    },

    updateItemData(index, field, value) {
        if (this.additionalItems[index]) {
            this.additionalItems[index][field] = value;
        }
    },

    calculateAdditionalNet(index) {
        const gross =
            parseFloat(
                document.getElementById(`additionalGross${index}`)?.value,
            ) || 0;
        const tare =
            parseFloat(
                document.getElementById(`additionalTare${index}`)?.value,
            ) || 0;
        const net = Math.max(0, gross - tare);
        const display = document.getElementById(`additionalNet${index}`);
        if (display) {
            display.textContent = net.toFixed(2) + " lbs";
            if (this.additionalItems[index]) {
                this.additionalItems[index].grossWeight = gross;
                this.additionalItems[index].tareWeight = tare;
                this.additionalItems[index].netWeight = net;
            }
        }
    },

    removeItemFullMode(index) {
        // Remove only the specific item from DOM without clearing others
        const itemDiv = document.getElementById(`additionalItemsContainer`);
        const allItemDivs = itemDiv.querySelectorAll('[class*="rounded-xl border-l-4"]');

        if (allItemDivs[index]) {
            allItemDivs[index].remove();
        }

        // Remove from data array
        this.additionalItems.splice(index, 1);
    },

    populateReviewFull() {
        const barcode = document.getElementById("manualBarcodeEntry").value;
        const vendorSelect =
            document.getElementById("selectedVendorFullModeStep1") ||
            document.getElementById("selectedVendorFullMode");
        const option = vendorSelect ? vendorSelect.options[vendorSelect.selectedIndex] : null;
        const clientName = option ? option.text : "N/A";
        const clientCompany = option ? (option.dataset.company || "N/A") : "N/A";

        const intakeDateVal = document.getElementById("intakeDateFullModeStep1")?.value || "";
        if (document.getElementById("headerIntakeDate")) {
            document.getElementById("headerIntakeDate").textContent = this.formatDateDisplay(intakeDateVal);
        }

        document.getElementById("headerBarcode").textContent = barcode;
        document.getElementById("headerClientName").textContent = clientName;
        document.getElementById("headerClientCompany").textContent =
            clientCompany;
        document.getElementById("sendEmailAddress").textContent =
            option.dataset.email || "client";
        document.getElementById("totalUnitsCount").textContent = (
            1 + this.additionalItems.length
        ).toString();

        document.getElementById("reviewMainDescription").textContent =
            document.getElementById("descriptionFull").value;
        document.getElementById("reviewMainGross").textContent =
            document.getElementById("grossWeightFull").value;
        document.getElementById("reviewMainTare").textContent =
            document.getElementById("tareWeightFull").value;
        document.getElementById("reviewMainNet").textContent =
            document.getElementById("netWeightDisplayFull").textContent;
        document.getElementById("reviewMainLocation").textContent =
            document.getElementById("putAwayLocationFull").value ||
            "No Location";
        const mainPhotoCount = document.querySelectorAll(
            "#mainPhotoPreviews > div, #quickPhotoPreviews > div",
        ).length;
        document.getElementById("reviewMainPhotos").textContent =
            mainPhotoCount;

        const mainNotes = document.getElementById("notesFull").value;
        const mainNotesDisplay = document.getElementById("reviewMainNotes");
        if (mainNotes) {
            mainNotesDisplay.textContent = mainNotes;
            mainNotesDisplay.style.display = "block";
        } else {
            mainNotesDisplay.style.display = "none";
        }

        const receivingEmp = document.getElementById("receivingEmployeeFull")?.value;
        const recEmpDisplay = document.getElementById("reviewMainReceivingEmployee");
        const recEmpVal = document.getElementById("reviewMainReceivingEmployeeVal");
        if (receivingEmp && recEmpDisplay && recEmpVal) {
            recEmpVal.textContent = receivingEmp;
            recEmpDisplay.style.display = "block";
        } else if (recEmpDisplay) {
            recEmpDisplay.style.display = "none";
        }

        const jobNo = document.getElementById("jobNoStep1")?.value || document.getElementById("jobNoQuick")?.value || document.getElementById("jobNoFull")?.value || document.getElementById("jobNo")?.value || "";
        const jobNoDisplay = document.getElementById("reviewMainJobNo");
        const jobNoVal = document.getElementById("reviewMainJobNoVal");
        if (jobNo && jobNoDisplay && jobNoVal) {
            jobNoVal.textContent = jobNo;
            jobNoDisplay.style.display = "block";
        } else if (jobNoDisplay) {
            jobNoDisplay.style.display = "none";
        }

        const additionalContainer = document.getElementById(
            "reviewAdditionalItemsContainer",
        );
        additionalContainer.innerHTML = "";

        this.additionalItems.forEach((item, index) => {
            const itemNum = index + 2;
            // Read values from DOM inputs to ensure accuracy
            const gross =
                parseFloat(
                    document.getElementById(`additionalGross${index}`)?.value,
                ) ||
                item.grossWeight ||
                0;
            const tare =
                parseFloat(
                    document.getElementById(`additionalTare${index}`)?.value,
                ) ||
                item.tareWeight ||
                0;
            const net = Math.max(0, gross - tare);
            const description =
                document.getElementById(`additionalDesc${index}`)?.value ||
                item.description ||
                "N/A";
            const location =
                document.getElementById(`additionalLocation${index}`)?.value ||
                item.location ||
                "Warehouse Default";
            const photoCount = document.querySelectorAll(
                `#additionalPhotoPreviews${index} > div`,
            ).length;
            const itemReceivingEmp = document.getElementById(`additionalReceivingEmployee${index}`)?.value || item.receivingEmployee || "";
            const itemJobNo = document.getElementById(`additionalJobNo${index}`)?.value || item.jobNo || "";

            const itemHTML = `
                <div class="group relative bg-white rounded-2xl border border-slate-100 p-6 hover:border-emerald-200 transition-all shadow-sm">
                    <span class="absolute -top-3 left-6 bg-emerald-500 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase">Item #${itemNum}</span>
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mt-2">
                        <div class="md:col-span-5">
                            <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Description</p>
                            <p class="text-slate-800 font-bold leading-snug">${description}</p>
                            ${itemReceivingEmp ? `<p class="text-xs font-semibold text-emerald-600 mt-1"><i class="fas fa-user-check mr-1"></i> Receiver: <span>${app.escapeHtml(itemReceivingEmp)}</span></p>` : ''}
                            ${itemJobNo ? `<p class="text-xs font-semibold text-slate-600 mt-1"><i class="fas fa-briefcase mr-1"></i> Job #: <span>${app.escapeHtml(itemJobNo)}</span></p>` : ''}
                        </div>
                        <div class="md:col-span-4 grid grid-cols-3 gap-2 text-center items-center">
                            <div class="bg-slate-50 p-2 rounded-lg">
                                <span class="block text-[9px] font-bold text-slate-400 uppercase">Gross</span>
                                <span class="text-sm font-bold text-slate-700">${gross}</span>
                            </div>
                            <div class="bg-slate-50 p-2 rounded-lg">
                                <span class="block text-[9px] font-bold text-slate-400 uppercase">Tare</span>
                                <span class="text-sm font-bold text-slate-700">${tare}</span>
                            </div>
                            <div class="bg-emerald-50 p-2 rounded-lg border border-emerald-100">
                                <span class="block text-[9px] font-black text-emerald-600 uppercase">Net</span>
                                <span class="text-sm font-black text-emerald-700">${net.toFixed(2)} lbs</span>
                            </div>
                        </div>
                        <div class="md:col-span-3 flex flex-col justify-center text-right border-l border-slate-50 pl-4">
                            <p class="text-xs font-bold text-slate-600 italic mb-1"><i class="fas fa-map-marker-alt mr-1"></i>${location}</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase"><i class="fas fa-paperclip mr-1"></i> ${photoCount} Photos / Files</p>
                        </div>
                    </div>
                </div>
            `;
            additionalContainer.innerHTML += itemHTML;
        });

        const mainNet = parseFloat(
            document.getElementById("netWeightDisplayFull").textContent,
        );
        const additionalTotal = this.additionalItems.reduce(
            (sum, item, index) => {
                const gross =
                    parseFloat(
                        document.getElementById(`additionalGross${index}`)
                            ?.value,
                    ) ||
                    item.grossWeight ||
                    0;
                const tare =
                    parseFloat(
                        document.getElementById(`additionalTare${index}`)
                            ?.value,
                    ) ||
                    item.tareWeight ||
                    0;
                return sum + Math.max(0, gross - tare);
            },
            0,
        );
        const grandTotal = mainNet + additionalTotal;
        document.getElementById("totalWeightDisplay").textContent =
            grandTotal.toFixed(2);
    },

    printReceiptFull() {
        const barcode =
            document.getElementById("successBarcodeFull").textContent;
        const vendorSelect =
            document.getElementById("selectedVendorFullModeStep1") ||
            document.getElementById("selectedVendorFullMode");
        const option = vendorSelect ? vendorSelect.options[vendorSelect.selectedIndex] : null;
        const clientName = option ? option.text : "N/A";
        const clientCompany = option ? (option.dataset.company || "N/A") : "N/A";
        const clientEmail = option ? (option.dataset.email || "N/A") : "N/A";
        const clientPhone = option ? (option.dataset.phone || "N/A") : "N/A";
        const mainDesc = document.getElementById("descriptionFull").value;
        const mainGross = document.getElementById("grossWeightFull").value;
        const mainTare = document.getElementById("tareWeightFull").value;
        const mainNet = document.getElementById(
            "netWeightDisplayFull",
        ).textContent;

        let itemsHTML = `
            <tr>
                <td>#01 (Main)</td>
                <td>${mainDesc}</td>
                <td style="text-align: right;">${mainGross}</td>
                <td style="text-align: right;">${mainTare}</td>
                <td style="text-align: right; font-weight: bold;">${mainNet}</td>
            </tr>
        `;

        this.additionalItems.forEach((item, index) => {
            itemsHTML += `
                <tr>
                    <td>#${String(index + 2).padStart(2, "0")}</td>
                    <td>${item.description || "N/A"}</td>
                    <td style="text-align: right;">${item.grossWeight || 0}</td>
                    <td style="text-align: right;">${item.tareWeight || 0}</td>
                    <td style="text-align: right; font-weight: bold;">${item.netWeight || 0}</td>
                </tr>
            `;
        });

        const mainNet_num = parseFloat(mainNet);
        const addTotal = this.additionalItems.reduce(
            (sum, i) => sum + (i.netWeight || 0),
            0,
        );
        const grandTotal = (mainNet_num + addTotal).toFixed(2);
        const intakeDateStr = this.getSelectedIntakeDate();

        let receiptHTML = `
            <html>
                <head>
                    <title>&#8203;</title>
                    <style>
                        @page { margin: 0; }
                        html { margin: 0; padding: 0; }
                        body { font-family: Arial, sans-serif; color: #000; background: #fff; margin: 0; padding: 0; }
                        .receipt { width: 8in; margin: auto; padding: 30px; line-height: 1.5; }
                        .header { text-align: center; border-bottom: 2px solid #000; margin-bottom: 5px; }
                        h1 { margin: 0; font-size: 28px; font-weight: bold; text-transform: uppercase; }
                        .info-row { display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1px solid #000; padding: 10px 0; margin-bottom: 20px; }
                        .info-label { font-size: 10px; color: #666; text-transform: uppercase; margin-bottom: 3px; }
                        .info-value { font-weight: bold; }
                        table { width: 100%; font-size: 12px; border-collapse: collapse; margin-bottom: 30px; }
                        th { background: #f8f9fa; border-bottom: 2px solid #000; padding: 8px; text-align: left; }
                        td { border-bottom: 1px solid #eee; padding: 8px; }
                        tfoot tr { border-top: 2px solid #000; }
                        .total-value { text-align: right; font-weight: bold; font-size: 14px; padding: 10px; border-bottom: 3px double #000; }
                    </style>
                </head>
                <body>
                    <div class="receipt">
                        <div class="header">
                            <h1>IT Investment Recoveries</h1>
                            <p style="margin: 0; font-size: 16px; font-weight: bold;">E-waste Recycling Receipt</p>
                        </div>
                        <div class="info-row">
                            <div>
                                <p class="info-label">Receipt Number</p>
                                <p class="info-value">REC-${new Date().getTime()}</p>
                            </div>
                            <div style="text-align: right;">
                                <p class="info-label">Date</p>
                                <p class="info-value">${intakeDateStr}</p>
                            </div>
                        </div>
                        <table>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="font-weight: bold; width: 200px;">Customer:</td>
                                <td style="text-align: right;">${clientName}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="font-weight: bold;">Client Company:</td>
                                <td style="text-align: right;">${clientCompany}</td>
                            </tr>
                             <tr style="border-bottom: 1px solid #eee;">
                                <td style="font-weight: bold;">Client Phone:</td>
                                <td style="text-align: right;">${clientPhone}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="font-weight: bold;">Email:</td>
                                <td style="text-align: right;">${clientEmail}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="font-weight: bold;">Pallet Barcode:</td>
                                <td style="text-align: right;">${barcode}</td>
                            </tr>
                            ${(document.getElementById("jobNoStep1")?.value || document.getElementById("jobNoFull")?.value || document.getElementById("jobNo")?.value) ? `
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="font-weight: bold;">Job #</td>
                                <td style="text-align: right;">${this.escapeHtml(document.getElementById("jobNoStep1")?.value || document.getElementById("jobNoFull")?.value || document.getElementById("jobNo")?.value)}</td>
                            </tr>` : ''}
                            ${document.getElementById("receivingEmployeeFull")?.value ? `
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="font-weight: bold;">Receiving Employee:</td>
                                <td style="text-align: right;">${this.escapeHtml(document.getElementById("receivingEmployeeFull")?.value)}</td>
                            </tr>` : ''}
                        </table>
                        <p style="font-size: 11px; font-weight: bold; text-transform: uppercase; margin: 20px 0 5px 0;">Itemized Breakdown:</p>
                        <table>
                            <thead>
                                <tr style="background: #f8f9fa; border-bottom: 2px solid #000;">
                                    <th>Unit</th>
                                    <th>Description</th>
                                    <th style="text-align: right;">Gross</th>
                                    <th style="text-align: right;">Tare</th>
                                    <th style="text-align: right;">Net Weight</th>
                                </tr>
                            </thead>
                            <tbody>${itemsHTML}</tbody>
                            <tfoot>
                                <tr style="border-top: 2px solid #000;">
                                    <td colspan="4" style="padding: 10px; text-align: right; font-weight: bold; font-size: 14px;">Total Shipment Weight:</td>
                                    <td class="total-value">${grandTotal} lbs</td>
                                </tr>
                            </tfoot>
                        </table>
                        <div style="text-align: center; border-top: 1px solid #000; padding-top: 15px;">
                            <p style="margin: 0; font-weight: bold; font-size: 16px;">Thank you for recycling with us!</p>
                        </div>
                    </div>
                </body>
            </html>
        `;

        const popupWindow = window.open("", "_blank", "width=900,height=600");
        popupWindow.document.write(receiptHTML);
        popupWindow.document.close();
        popupWindow.print();
    },

    // Call this function from resetForm() or direct button click
    resetForm() {
        document.getElementById("recyclingForm").reset();
        this.enableSubmitBtn(); // Button ko "SAVE PALLET" par wapas layein
        this.additionalItems = [];
        this._mainPhotoData = new DataTransfer();
        this._additionalPhotoData = {};
        const mainPreviews = document.getElementById("mainPhotoPreviews");
        if (mainPreviews) mainPreviews.innerHTML = "";
        const quickPreviews = document.getElementById("quickPhotoPreviews");
        if (quickPreviews) quickPreviews.innerHTML = "";
        const jobNoDisplay = document.getElementById("reviewMainJobNo");
        if (jobNoDisplay) jobNoDisplay.style.display = "none";
        this.currentStep = 1;
        this.renderStep();
        window.scrollTo({ top: 0, behavior: "smooth" });
    },

    // Scanner Modal Methods
    openScannerModal() {
        const modal = document.getElementById("scannerModal");
        if (!modal) {
            console.error("Scanner modal not found");
            return;
        }
        modal.style.display = "flex";
        // Show mode selection
        document.getElementById("scannerModeSelection").style.display = "block";
        document.getElementById("cameraScannerInterface").style.display = "none";
        document.getElementById("usbScannerInterface").style.display = "none";
    },

    closeScannerModal() {
        const modal = document.getElementById("scannerModal");
        if (modal) {
            modal.style.display = "none";
        }
        this.stopQuaggaScanner();
    },

    switchToCamera() {
        // Hide mode selection
        document.getElementById("scannerModeSelection").style.display = "none";
        // Show camera interface
        document.getElementById("cameraScannerInterface").style.display = "block";
        document.getElementById("usbScannerInterface").style.display = "none";
        // Initialize camera
        this.initializeQuaggaScanner();
    },

    switchToUSBScanner() {
        // Hide mode selection
        document.getElementById("scannerModeSelection").style.display = "none";
        // Show USB interface
        document.getElementById("cameraScannerInterface").style.display = "none";
        document.getElementById("usbScannerInterface").style.display = "block";
        // Stop camera if running
        this.stopQuaggaScanner();
        // Focus on USB input
        setTimeout(() => {
            document.getElementById("usbScannerInput").focus();
        }, 200);
    },

    backToModeSelection() {
        // Stop any active scanning
        this.stopQuaggaScanner();
        // Reset all interfaces
        document.getElementById("scannerModeSelection").style.display = "block";
        document.getElementById("cameraScannerInterface").style.display = "none";
        document.getElementById("usbScannerInterface").style.display = "none";
        // Clear USB input
        document.getElementById("usbScannerInput").value = "";
        document.getElementById("usbScanResult").style.display = "none";
    },

    resetScannerUI() {
        // Hide result display
        document.getElementById("scanResult").style.display = "none";
        // Show loading state
        document.getElementById("scannerLoading").style.display = "flex";
        // Restart scanner
        this.initializeQuaggaScanner();
    },

    useScannerResult() {
        const barcodeValue = document.getElementById("scannedBarcodeValue").textContent;
        if (barcodeValue && barcodeValue !== "---") {
            // Fill the barcode entry field
            document.getElementById("manualBarcodeEntry").value = barcodeValue;

            // Display barcode image
            this.displayExistingBarcodeImage(barcodeValue);

            // Close modal
            this.closeScannerModal();

            // Visual feedback
            this.showSuccess("Barcode scanned successfully: " + barcodeValue);
        }
    },

    processUSBScan() {
        const input = document.getElementById("usbScannerInput");
        const barcodeValue = input.value.trim();

        if (barcodeValue) {
            // Show result
            document.getElementById("scannedUsbBarcodeValue").textContent = barcodeValue;
            document.getElementById("usbScanResult").style.display = "block";
            // Disable further input until confirmed or cleared
            input.disabled = true;
        }
    },

    useUSBScannerResult() {
        const barcodeValue = document.getElementById("scannedUsbBarcodeValue").textContent;
        if (barcodeValue && barcodeValue !== "---") {
            // Fill the barcode entry field
            document.getElementById("manualBarcodeEntry").value = barcodeValue;

            // Display barcode image
            this.displayExistingBarcodeImage(barcodeValue);

            // Close modal
            this.closeScannerModal();

            // Visual feedback
            this.showSuccess("Barcode scanned successfully: " + barcodeValue);
        }
    },

    clearUSBInput() {
        document.getElementById("usbScannerInput").value = "";
        document.getElementById("usbScannerInput").disabled = false;
        document.getElementById("usbScanResult").style.display = "none";
        document.getElementById("usbScannerInput").focus();
    },

    initializeQuaggaScanner() {
        const container = document.getElementById("scannerContainer");
        const video = document.getElementById("scannerVideo");
        const canvas = document.getElementById("scannerCanvas");
        const loading = document.getElementById("scannerLoading");

        // Clear previous instance
        if (window.QuaggaScanning) {
            try {
                Quagga.stop();
            } catch (e) {
                // Ignore errors
            }
            window.QuaggaScanning = false;
        }

        // Show video element
        if (video) {
            video.style.display = "block";
            loading.style.display = "none";
        }

        try {
            Quagga.init(
                {
                    inputStream: {
                        name: "Live",
                        type: "LiveStream",
                        target: video,
                        constraints: {
                            facingMode: "environment",
                            width: { min: 640 },
                            height: { min: 480 },
                        },
                    },
                    decoder: {
                        workers: 2,
                        debug: false,
                        multiple: false,
                        formats: [
                            "code_128",
                            "code_39",
                            "code_39_vin",
                            "codabar",
                            "ean",
                            "ean_8",
                            "ean_13",
                            "upc",
                            "upc_e",
                            "i2of5",
                        ],
                    },
                    locator: {
                        halfSample: true,
                    },
                },
                (err) => {
                    if (err) {
                        console.error("Quagga initialization error:", err);
                        loading.style.display = "flex";
                        loading.innerHTML =
                            '<div class="text-center"><div class="text-white mb-4"><i class="fas fa-exclamation-triangle text-4xl"></i></div><p class="text-white font-bold">Camera access denied</p><p class="text-slate-300 text-sm mt-2">Please check your camera permissions</p></div>';
                        return;
                    }

                    Quagga.start();
                    window.QuaggaScanning = true;

                    // Handle detected barcodes
                    Quagga.onDetected((result) => {
                        if (result && result.codeResult && result.codeResult.code) {
                            const barcode = result.codeResult.code;
                            console.log("Barcode detected:", barcode);

                            // Stop scanner
                            app.stopQuaggaScanner();

                            // Show result
                            document.getElementById("scannedBarcodeValue").textContent =
                                barcode;
                            document.getElementById("scanResult").style.display =
                                "block";
                            document.getElementById("scannerLoading").style.display =
                                "none";
                            if (video) video.style.display = "none";
                        }
                    });
                },
            );
        } catch (error) {
            console.error("Error initializing Quagga:", error);
            loading.style.display = "flex";
            loading.innerHTML =
                '<div class="text-center"><div class="text-white mb-4"><i class="fas fa-exclamation-circle text-4xl"></i></div><p class="text-white font-bold">Scanner initialization failed</p><p class="text-slate-300 text-sm mt-2">' +
                error.message +
                "</p></div>";
        }
    },

    stopQuaggaScanner() {
        try {
            if (window.QuaggaScanning) {
                Quagga.stop();
                window.QuaggaScanning = false;
            }
        } catch (error) {
            console.error("Error stopping Quagga:", error);
        }
    },
};

document.addEventListener("DOMContentLoaded", () => app.init());
