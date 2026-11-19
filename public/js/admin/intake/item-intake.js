/* jshint ignore:start */
/* eslint-disable */
window.addEventListener('error', function(e) {
    console.error('Global JS Error:', e.error);
});

class ItemIntakeManager {
    constructor() {
        this.currentStep = 1;
        this.intakeType = "";
        this.barcode = "";
        this.selectedPalletId = null;
        this.selectedPalletData = {};
        this.palletRemaining = { quantity: 0, weight: 0 };
        this.formData = {};
        this.accumulatedFiles = [];
        this.latestPrintLabelHtml = "";
        this.latestBarcodeBase64 = "";
        this.scanMode = "camera";
        this.showScanner = false;
        this.quaggaStarted = false;
        this.serialNumbers = [];
        this.ddItems = [];
        this.repairItems = [{ name: "", brand: "", model: "", serial: "" }];
        this.repairPhotos = [];
        this.repairBarcode = "";
        this.repairBarcodeBase64 = "";
        this.repairInitialized = false;
        this.cameraStream = null;
        this.cameraFacingMode = "environment";
        this.init();
    }

    init() {
        this.attachEventListeners();
        if (typeof this.initCameraFeatures === "function") {
            this.initCameraFeatures();
        }
        this.restoreRememberedEmployee();
        this.checkUrlTypeParam();
        this.updateStepperUI();
    }

    checkUrlTypeParam() {
        const urlParams = new URLSearchParams(window.location.search);
        const typeParam = urlParams.get('type') || urlParams.get('intake_type');
        if (typeParam) {
            const normalized = typeParam.toLowerCase().trim();
            const cards = document.querySelectorAll(".intake-type-card");
            let matchedCard = null;

            cards.forEach(card => {
                const dataType = (card.dataset.type || "").toLowerCase();
                const cardTitle = (card.querySelector('h4')?.textContent || "").toLowerCase();

                if (
                    dataType.includes(normalized) ||
                    cardTitle.includes(normalized) ||
                    (normalized.includes('repair') && (dataType.includes('repair') || cardTitle.includes('repair'))) ||
                    (normalized.includes('ebay') && dataType.includes('ebay')) ||
                    ((normalized.includes('refurb') || normalized.includes('ecommerce')) && (dataType.includes('ebay') || dataType.includes('refurb') || cardTitle.includes('refurb') || cardTitle.includes('ecommerce')))
                ) {
                    matchedCard = card;
                }
            });

            if (matchedCard) {
                this.selectIntakeType(matchedCard);
            }
        }
    }

    restoreRememberedEmployee() {
        this.populateCustomEmployees();
        const savedEmp = localStorage.getItem("remembered_intake_employee");
        const intakeEmpSelect = document.getElementById("intake-employee-select");
        if (savedEmp && intakeEmpSelect) {
            intakeEmpSelect.value = savedEmp;
        }
    }

    addCustomEmployeeToDatalist(empName) {
        if (!empName) return;
        const cleanName = empName.trim();
        if (!cleanName) return;

        let customList = [];
        try {
            customList = JSON.parse(localStorage.getItem("custom_intake_employees") || "[]");
        } catch (e) { customList = []; }

        if (!customList.map(n => n.toLowerCase()).includes(cleanName.toLowerCase())) {
            customList.push(cleanName);
            localStorage.setItem("custom_intake_employees", JSON.stringify(customList));
            this.populateCustomEmployees();
        }
    }

    populateCustomEmployees() {
        const container = document.getElementById("intake-employee-options-container");
        if (!container) return;

        const existingElements = Array.from(container.querySelectorAll(".intake-emp-option"));
        const existingNames = existingElements.map(el => (el.dataset.value || "").trim());

        let customList = [];
        try {
            customList = JSON.parse(localStorage.getItem("custom_intake_employees") || "[]");
        } catch (e) { customList = []; }

        customList.forEach(name => {
            if (name && !existingNames.map(n => n.toLowerCase()).includes(name.toLowerCase())) {
                const div = document.createElement("div");
                div.className = "intake-emp-option px-4 py-2.5 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 cursor-pointer font-medium transition-colors flex items-center justify-between";
                div.dataset.value = name;
                div.innerHTML = `<span>${this.escapeHtml(name)}</span>`;
                container.appendChild(div);
                existingNames.push(name);
            }
        });

        this.bindEmployeeDropdownOptionEvents();
    }

    bindEmployeeDropdownOptionEvents() {
        const container = document.getElementById("intake-employee-options-container");
        if (!container) return;

        const options = container.querySelectorAll(".intake-emp-option");
        options.forEach(opt => {
            opt.onclick = (e) => {
                e.stopPropagation();
                const val = opt.dataset.value;
                const input = document.getElementById("intake-employee-select");
                if (input) {
                    input.value = val;
                    localStorage.setItem("remembered_intake_employee", val);
                }
                this.closeEmployeeDropdown();
            };
        });
    }

    openEmployeeDropdown() {
        const dropdown = document.getElementById("intake-employee-dropdown");
        const chevron = document.getElementById("intake-employee-chevron");
        if (dropdown) dropdown.classList.remove("hidden");
        if (chevron) chevron.classList.add("rotate-180");
        this.filterEmployeeDropdown();
    }

    closeEmployeeDropdown() {
        const dropdown = document.getElementById("intake-employee-dropdown");
        const chevron = document.getElementById("intake-employee-chevron");
        if (dropdown) dropdown.classList.add("hidden");
        if (chevron) chevron.classList.remove("rotate-180");
    }

    filterEmployeeDropdown() {
        const input = document.getElementById("intake-employee-select");
        const container = document.getElementById("intake-employee-options-container");
        if (!input || !container) return;

        const query = (input.value || "").toLowerCase().trim();
        const options = container.querySelectorAll(".intake-emp-option");
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

        let noResultMsg = container.querySelector(".no-emp-found");
        if (!hasVisible && query) {
            if (!noResultMsg) {
                noResultMsg = document.createElement("div");
                noResultMsg.className = "no-emp-found px-4 py-3 text-xs text-slate-400 italic text-center";
                noResultMsg.textContent = "Press Tab or Save to use custom name";
                container.appendChild(noResultMsg);
            }
            noResultMsg.style.display = "block";
        } else if (noResultMsg) {
            noResultMsg.style.display = "none";
        }
    }

    attachEventListeners() {
        const intakeEmpInput = document.getElementById("intake-employee-select");
        const toggleBtn = document.getElementById("intake-employee-toggle-btn");

        if (intakeEmpInput) {
            intakeEmpInput.addEventListener("focus", () => this.openEmployeeDropdown());
            intakeEmpInput.addEventListener("click", () => this.openEmployeeDropdown());
            intakeEmpInput.addEventListener("input", () => {
                this.openEmployeeDropdown();
                const val = intakeEmpInput.value.trim();
                if (val) {
                    localStorage.setItem("remembered_intake_employee", val);
                }
            });
            intakeEmpInput.addEventListener("blur", () => {
                setTimeout(() => {
                    this.closeEmployeeDropdown();
                    const val = intakeEmpInput.value.trim();
                    if (val) {
                        localStorage.setItem("remembered_intake_employee", val);
                        this.addCustomEmployeeToDatalist(val);
                    }
                }, 200);
            });
        }

        if (toggleBtn) {
            toggleBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                const dropdown = document.getElementById("intake-employee-dropdown");
                if (dropdown && dropdown.classList.contains("hidden")) {
                    this.openEmployeeDropdown();
                } else {
                    this.closeEmployeeDropdown();
                }
            });
        }

        document.addEventListener("click", (e) => {
            const wrapper = document.getElementById("intake-employee-wrapper");
            if (wrapper && !wrapper.contains(e.target)) {
                this.closeEmployeeDropdown();
            }
        });

        const cards = document.querySelectorAll(".intake-type-card");
        cards.forEach((card) => {
            card.addEventListener("click", (e) => this.selectIntakeType(card));
        });

        const nextBtn = document.getElementById("next-btn");
        if (nextBtn) nextBtn.addEventListener("click", (e) => { e.preventDefault(); this.nextStep(); });

        const prevBtn = document.getElementById("prev-btn");
        if (prevBtn) prevBtn.addEventListener("click", (e) => { e.preventDefault(); this.previousStep(); });

        const existingBarcodeCheckbox = document.getElementById("existing-barcode-checkbox");
        if (existingBarcodeCheckbox) {
            existingBarcodeCheckbox.addEventListener("change", (e) => this.toggleBarcodeMode(e.target.checked));
        }

        const generateBarCodeBtn = document.getElementById("generate-barcode-btn");
        if (generateBarCodeBtn) {
            generateBarCodeBtn.addEventListener("click", (e) => { e.preventDefault(); this.generateNewBarcode(); });
        }

        const palletSelect = document.getElementById("pallet-select");
        if (palletSelect) {
            palletSelect.addEventListener("change", (e) => {
                this.selectedPalletId = e.target.value;
                if (this.selectedPalletId) {
                    this.populatePalletDetails(this.selectedPalletId);
                } else {
                    this.barcode = "";
                    this.selectedPalletData = {};
                    this.palletRemaining = { quantity: 0, weight: 0 };
                    this.clearPalletLimits();
                    this.updateRemainingDisplay();
                }
            });
        }

        const quantityInput = document.querySelector('input[name="quantity"]');
        if (quantityInput) {
            quantityInput.addEventListener("input", () => this.updateRemainingDisplay());
        }

        const weightInput = document.querySelector('input[name="weight"]');
        if (weightInput) {
            weightInput.addEventListener("input", () => this.updateRemainingDisplay());
        }

        const mainCategorySelect = document.getElementById("main-category-select");
        if (mainCategorySelect) {
            mainCategorySelect.addEventListener("change", (e) => this.onMainCategoryChange(e.target.value));
        }

        const subCategorySelect = document.getElementById("sub-category-select");
        if (subCategorySelect) {
            subCategorySelect.addEventListener("change", (e) => this.onSubCategoryChange(e.target.value));
        }

        const subSubCategorySelect = document.getElementById("sub-sub-category-select");
        if (subSubCategorySelect) {
            subSubCategorySelect.addEventListener("change", (e) => this.onSubSubCategoryChange(e.target.value));
        }

        document.querySelectorAll(".next-step-btn").forEach((btn) => {
            btn.addEventListener("click", (e) => { e.preventDefault(); this.nextStep(); });
        });

        const saveBtn = document.getElementById("save-item-btn");
        if (saveBtn) saveBtn.addEventListener("click", (e) => { e.preventDefault(); this.saveItem(false); });

        const saveDraftBtn = document.getElementById("save-draft-item-btn");
        if (saveDraftBtn) saveDraftBtn.addEventListener("click", (e) => { e.preventDefault(); this.saveItem(true); });

        // Refurb / eBay product scan/gen serial buttons
        const ebayScanSerialBtn = document.getElementById("ebay-scan-serial-btn");
        if (ebayScanSerialBtn) {
            ebayScanSerialBtn.addEventListener("click", (e) => {
                e.preventDefault();
                if (typeof this.scanSerialFromCamera === "function") {
                    this.scanSerialFromCamera();
                }
            });
        }

        const ebayGenerateSerialBtn = document.getElementById("ebay-generate-serial-btn");
        if (ebayGenerateSerialBtn) {
            ebayGenerateSerialBtn.addEventListener("click", (e) => {
                e.preventDefault();
                const randomSerial = "SN" + Math.floor(1000000000 + Math.random() * 9000000000);
                const serialField = document.getElementById("serial-number-field");
                if (serialField) serialField.value = randomSerial;
            });
        }

        // Setup multiple serials event listeners
        const addSerialBtn = document.getElementById("add-serial-btn");
        if (addSerialBtn) {
            addSerialBtn.addEventListener("click", (e) => {
                e.preventDefault();
                this.addSerialNumberFromInput();
            });
        }

        const serialNumberInput = document.getElementById("serial-number-input");
        if (serialNumberInput) {
            serialNumberInput.addEventListener("keydown", (e) => {
                if (e.key === "Enter") {
                    e.preventDefault();
                    this.addSerialNumberFromInput();
                }
            });
        }

        const bulkSerialToggle = document.getElementById("bulk-serial-toggle");
        if (bulkSerialToggle) {
            bulkSerialToggle.addEventListener("click", (e) => {
                e.preventDefault();
                const bulkContainer = document.getElementById("bulk-serial-container");
                if (bulkContainer) bulkContainer.classList.toggle("hidden");
            });
        }

        const cancelBulkSerialBtn = document.getElementById("cancel-bulk-serial-btn");
        if (cancelBulkSerialBtn) {
            cancelBulkSerialBtn.addEventListener("click", (e) => {
                e.preventDefault();
                const bulkContainer = document.getElementById("bulk-serial-container");
                if (bulkContainer) bulkContainer.classList.add("hidden");
            });
        }

        const applyBulkSerialBtn = document.getElementById("apply-bulk-serial-btn");
        if (applyBulkSerialBtn) {
            applyBulkSerialBtn.addEventListener("click", (e) => {
                e.preventDefault();
                this.applyBulkSerialNumbers();
            });
        }

        const serialTagsContainer = document.getElementById("serial-tags-container");
        if (serialTagsContainer) {
            serialTagsContainer.addEventListener("click", (e) => {
                const removeBtn = e.target.closest(".remove-serial-tag");
                if (removeBtn) {
                    e.preventDefault();
                    const index = parseInt(removeBtn.dataset.index, 10);
                    this.removeSerialNumber(index);
                }
            });
        }

        // Data Destruction / IT Assets Item listeners
        const addDdItemBtn = document.getElementById("add-dd-item-btn");
        if (addDdItemBtn) {
            addDdItemBtn.addEventListener("click", (e) => {
                e.preventDefault();
                if (typeof this.addDataDestructionItem === "function") {
                    this.addDataDestructionItem();
                }
            });
        }

        const ddItemsList = document.getElementById("data-destruction-items-list");
        if (ddItemsList) {
            ddItemsList.addEventListener("click", (e) => {
                const removeBtn = e.target.closest(".remove-dd-item-btn");
                if (removeBtn) {
                    e.preventDefault();
                    const index = parseInt(removeBtn.dataset.index, 10);
                    if (typeof this.removeDataDestructionItem === "function") {
                        this.removeDataDestructionItem(index);
                    }
                }
            });
        }

        const backBtn = document.getElementById("back-btn");
        if (backBtn) backBtn.addEventListener("click", (e) => { e.preventDefault(); this.previousStep(); });

        const resetBtn = document.getElementById("reset-btn");
        if (resetBtn) resetBtn.addEventListener("click", (e) => { e.preventDefault(); this.resetForm(); });

        const addAnotherBtn = document.getElementById("add-another-btn");
        if (addAnotherBtn) {
            addAnotherBtn.addEventListener("click", (e) => { e.preventDefault(); window.location.reload(); });
        }
    }

    selectIntakeType(card) {
        document.querySelectorAll(".intake-type-card").forEach((c) => {
            c.style.borderColor = "#e5e7eb";
            c.style.backgroundColor = "#ffffff";
        });

        this.intakeType = card.dataset.type;
        this.updateIntakeEmployeeField();
        
        // Color adjustments based on selected type
        let activeColor = "#2563eb";
        let activeBg = "#f0f7ff";
        if (this.intakeType === "Data Destruction Intake") {
            activeColor = "#ef4444";
            activeBg = "#fef2f2";
        } else if (this.intakeType === "IT Assets Intake") {
            activeColor = "#8b5cf6";
            activeBg = "#f5f3ff";
        } else if (this.intakeType === "Repair Intake") {
            activeColor = "#f59e0b";
            activeBg = "#fffbeb";
        } else if (this.intakeType === "Refurbishing Intake") {
            activeColor = "#f59e0b";
            activeBg = "#fffbeb";
        } else if (this.intakeType === "Universal Waste Intake") {
            activeColor = "#10b981";
            activeBg = "#f0fdf4";
        }
        
        card.style.borderColor = activeColor;
        card.style.backgroundColor = activeBg;

        this.currentStep = 2;
        
        const typeDisplay = document.getElementById("active-type-display");
        if (typeDisplay) typeDisplay.textContent = (this.intakeType === "eBay Product Intake" || this.intakeType === "eBay Product" || this.intakeType === "Refurbished Intake" || this.intakeType === "Refurbished/ECommerce intake" || this.intakeType === "Refurb/E-commerce Intake") ? "Refurb/E-commerce Intake" : this.intakeType;

        const standardFlow = document.getElementById("standard-intake-flow");
        const repairFlow = document.getElementById("repair-intake-flow");

        if (this.intakeType === "Repair Intake") {
            if (standardFlow) standardFlow.classList.add("hidden");
            if (repairFlow) repairFlow.classList.remove("hidden");
            if (typeof this.initRepairIntake === "function") {
                this.initRepairIntake();
            }
            this.updateStepperUI();
            return;
        } else {
            if (standardFlow) standardFlow.classList.remove("hidden");
            if (repairFlow) repairFlow.classList.add("hidden");
        }
        
        this.serialNumbers = [];
        this.renderSerialTags();
        
        const isRefurb = (
            this.intakeType === "eBay Product Intake" ||
            this.intakeType === "eBay Product" ||
            this.intakeType === "Refurbished Intake" ||
            this.intakeType === "Refurbished/ECommerce intake" ||
            this.intakeType === "Refurb/E-commerce Intake"
        );

        if (this.intakeType === "Data Destruction Intake") {
            if (typeof this.applyDataDestructionUI === "function") {
                this.applyDataDestructionUI();
            }
        } else if (this.intakeType === "IT Assets Intake" || this.intakeType === "IT Assets") {
            if (typeof this.applyItAssetsUI === "function") {
                this.applyItAssetsUI();
            }
        } else if (this.intakeType === "Universal Waste Intake" || this.intakeType === "Universal Waste") {
            if (typeof this.applyUniversalWasteUI === "function") {
                this.applyUniversalWasteUI();
            }
        } else {
            // Standard / Refurb
            const standardFields = document.getElementById("standard-item-fields");
            const ddSection = document.getElementById("data-destruction-items-section");
            if (standardFields) standardFields.classList.remove("hidden");
            if (ddSection) ddSection.classList.add("hidden");
            this.ddItems = [];
            
            const standardItemName = document.querySelector('input[name="item_name"]');
            const standardBrand = document.querySelector('input[name="brand"]');
            const standardModel = document.querySelector('input[name="model"]');
            const standardSerial = document.querySelector('input[name="serial_number"]');
            const qtyInput = document.querySelector('input[name="quantity"]');
            
            if (standardItemName) standardItemName.value = "";
            if (standardBrand) standardBrand.value = "";
            if (standardModel) standardModel.value = "";
            if (standardSerial) standardSerial.value = "";
            
            if (qtyInput) {
                qtyInput.value = "";
                qtyInput.readOnly = false;
                qtyInput.style.backgroundColor = "";
            }
        }

        if (isRefurb && typeof this.applyRefurbUI === "function") {
            this.applyRefurbUI();
        } else if (!isRefurb) {
            const ebayFolderTypeWrapper = document.getElementById("ebay-folder-type-wrapper");
            const ebayPersonFieldWrapper = document.getElementById("ebay-person-field-wrapper");
            const ebayLinksSection = document.getElementById("ebay-links-section");
            const categorySelectionWrapper = document.getElementById("category-selection-wrapper");
            const brandWrapper = document.getElementById("brand-wrapper");
            const modelWrapper = document.getElementById("model-wrapper");
            const itemNameLabel = document.getElementById("item-name-label");
            const intakeEmployeeWrapper = document.getElementById("intake-employee-wrapper");
            const cameraBtnWrapper = document.getElementById("camera-capture-btn-wrapper");

            if (ebayFolderTypeWrapper) ebayFolderTypeWrapper.classList.add("hidden");
            if (ebayPersonFieldWrapper) ebayPersonFieldWrapper.classList.add("hidden");
            if (ebayLinksSection) ebayLinksSection.classList.add("hidden");
            if (categorySelectionWrapper) categorySelectionWrapper.classList.remove("hidden");
            if (brandWrapper) brandWrapper.classList.remove("hidden");
            if (modelWrapper) modelWrapper.classList.remove("hidden");
            if (intakeEmployeeWrapper) intakeEmployeeWrapper.classList.remove("hidden");
            if (itemNameLabel) itemNameLabel.textContent = "Item Name *";
            if (cameraBtnWrapper) cameraBtnWrapper.classList.add("hidden");
        }
        const saveDraftBtn = document.getElementById("save-draft-item-btn");
        if (saveDraftBtn) {
            saveDraftBtn.classList.remove("hidden");
        }

        this.restoreRememberedEmployee();
        this.updateStepperUI();
    }

    updateIntakeEmployeeField() {
        const label = document.getElementById("intake-employee-label");
        if (label) {
            label.textContent = "Intake Employee";
        }
        this.restoreRememberedEmployee();
    }

    toggleBarcodeMode(useExisting) {
        const generateMode = document.getElementById("generate-mode");
        const palletMode = document.getElementById("pallet-mode");
        
        if (useExisting) {
            generateMode.classList.add("hidden");
            palletMode.classList.remove("hidden");
        } else {
            generateMode.classList.remove("hidden");
            palletMode.classList.add("hidden");
        }
    }

    loadCategories() {
        const mainCategorySelect = document.getElementById("main-category-select");
        if (!mainCategorySelect) return;

        fetch(CATEGORIES_API, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": CSRF_TOKEN,
            },
        })
            .then(async (response) => {
                const text = await response.text();
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error("Invalid JSON response:", text);
                    throw new Error("Server returned invalid JSON");
                }
            })
            .then((data) => {
                if (data.success && data.categories) {
                    mainCategorySelect.innerHTML = '<option value="">Select Main Category</option>';
                    data.categories.forEach((category) => {
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.textContent = category.name;
                        mainCategorySelect.appendChild(option);
                    });
                    this.resetSubCategories();
                }
            })
            .catch((error) => console.error("Error loading categories:", error));
    }

    onMainCategoryChange(categoryId) {
        this.resetSubCategories();
        if (!categoryId) return;
        this.loadSubCategories(categoryId, "sub-category-select");
        const mainCategorySelect = document.getElementById("main-category-select");
        const selectedOption = mainCategorySelect?.options[mainCategorySelect.selectedIndex];
        document.getElementById("category-final").value = selectedOption ? selectedOption.textContent.trim() : "";
    }

    onSubCategoryChange(categoryId) {
        this.resetSubSubCategories();
        if (!categoryId) {
            const mainCategorySelect = document.getElementById("main-category-select");
            const selectedOption = mainCategorySelect?.options[mainCategorySelect.selectedIndex];
            document.getElementById("category-final").value = selectedOption ? selectedOption.textContent.trim() : "";
            return;
        }
        this.loadSubCategories(categoryId, "sub-sub-category-select");
        const subCategorySelect = document.getElementById("sub-category-select");
        const selectedOption = subCategorySelect?.options[subCategorySelect.selectedIndex];
        document.getElementById("category-final").value = selectedOption ? selectedOption.textContent.trim() : "";
    }

    onSubSubCategoryChange(categoryId) {
        if (!categoryId) {
            const subCategorySelect = document.getElementById("sub-category-select");
            const mainCategorySelect = document.getElementById("main-category-select");
            const subCategoryOption = subCategorySelect?.options[subCategorySelect.selectedIndex];
            const mainCategoryOption = mainCategorySelect?.options[mainCategorySelect.selectedIndex];
            const finalValue = subCategoryOption?.textContent?.trim() || mainCategoryOption?.textContent?.trim() || "";
            document.getElementById("category-final").value = finalValue;
            return;
        }
        const subSubCategorySelect = document.getElementById("sub-sub-category-select");
        const selectedOption = subSubCategorySelect?.options[subSubCategorySelect.selectedIndex];
        document.getElementById("category-final").value = selectedOption ? selectedOption.textContent.trim() : "";
    }

    loadSubCategories(parentId, targetSelectId) {
        const targetSelect = document.getElementById(targetSelectId);
        if (!targetSelect) return;

        fetch(SUB_CATEGORIES_API + "?parent_id=" + parentId, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": CSRF_TOKEN,
            },
        })
            .then((response) => response.ok ? response.json() : Promise.reject(response))
            .then((data) => {
                if (data.success && data.categories) {
                    const placeholder = targetSelectId === "sub-category-select" 
                        ? "Select Sub Category" 
                        : "Select Sub Sub Category";
                    targetSelect.innerHTML = `<option value="">${placeholder}</option>`;
                    
                    if (data.categories.length > 0) {
                        data.categories.forEach((category) => {
                            const option = document.createElement('option');
                            option.value = category.id;
                            option.textContent = category.name;
                            targetSelect.appendChild(option);
                        });
                        targetSelect.disabled = false;
                    } else {
                        targetSelect.disabled = true;
                    }
                }
            })
            .catch((error) => console.error("Error loading sub-categories:", error));
    }

    resetSubCategories() {
        const subCategorySelect = document.getElementById("sub-category-select");
        subCategorySelect.innerHTML = '<option value="">Select Sub Category</option>';
        subCategorySelect.disabled = true;
        this.resetSubSubCategories();
    }

    resetSubSubCategories() {
        const subSubCategorySelect = document.getElementById("sub-sub-category-select");
        subSubCategorySelect.innerHTML = '<option value="">Select Sub Sub Category</option>';
        subSubCategorySelect.disabled = true;
    }

    generateNewBarcode() {
        const isRefurb = (this.intakeType === "eBay Product Intake" || this.intakeType === "eBay Product" || this.intakeType === "Refurbishing Intake" || this.intakeType === "Refurbishing");
        const typePrefix = isRefurb ? "EB" : 
                          (this.intakeType === "Data Destruction Intake" ? "DD" : 
                          (this.intakeType === "IT Assets Intake" ? "IT" : "UW"));
        const url = INTAKE_API + "?type=" + typePrefix + "&intake_type=" + encodeURIComponent(this.intakeType);

        fetch(url, { method: "GET" })
            .then((response) => response.ok ? response.json() : Promise.reject(response))
            .then((data) => {
                if (data.success && data.barcode) {
                    this.barcode = data.barcode;
                    this.displayBarcode(this.barcode, data.barcode_image);
                } else {
                    alert(data.message || "Failed to generate barcode");
                }
            })
            .catch((error) => alert("Error: " + error.message));
    }

    displayBarcode(barcodeValue, barcodeImage) {
        const barcodeText = document.getElementById("barcode-text");
        if (barcodeText) barcodeText.textContent = barcodeValue;
        
        const barcodeImageDiv = document.getElementById("barcode-image");
        if (barcodeImageDiv && barcodeImage) barcodeImageDiv.innerHTML = barcodeImage;
        
        const barcodeDisplay = document.getElementById("barcode-display");
        if (barcodeDisplay) barcodeDisplay.classList.remove("hidden");
    }

    displayPalletBarcode(barcodeValue, barcodeImage) {
        const barcodeText = document.getElementById("pallet-barcode-text");
        if (barcodeText) barcodeText.textContent = barcodeValue;
        
        const barcodeImageDiv = document.getElementById("pallet-barcode-image");
        if (barcodeImageDiv && barcodeImage) barcodeImageDiv.innerHTML = barcodeImage;
        
        const barcodeDisplay = document.getElementById("pallet-barcode-display");
        if (barcodeDisplay) barcodeDisplay.classList.remove("hidden");
    }

    generateExistingBarcode() {
        if (!this.barcode) {
            alert("Please select a pallet");
            return;
        }

        const url = BARCODE_GEN_API + "?barcode=" + encodeURIComponent(this.barcode);
        fetch(url, { method: "GET" })
            .then((response) => response.ok ? response.json() : Promise.reject(response))
            .then((data) => {
                if (data.success && data.barcode) {
                    this.barcode = data.barcode;
                    this.displayPalletBarcode(this.barcode, data.barcode_image);
                } else {
                    alert(data.message || "Failed to generate barcode");
                }
            })
            .catch((error) => console.error("Error generating barcode:", error));
    }

    populatePalletDetails(palletId) {
        const apiUrl = PALLET_DETAILS_API.replace('__ID__', palletId);
        
        fetch(apiUrl, {
            method: "GET",
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include'
        })
            .then((response) => response.ok ? response.json() : Promise.reject(response))
            .then((data) => {
                if (data.success && data.data) {
                    const pallet = data.data;
                    const netQuantity = parseInt(pallet.remaining_quantity, 10) || 0;
                    const netWeight = parseFloat(pallet.remaining_weight) || 0;

                    this.barcode = pallet.barcode_number;
                    this.generateExistingBarcode();
                    this.selectedPalletData = {
                        weight: netWeight,
                        quantity: netQuantity
                    };
                    this.palletRemaining = {
                        quantity: netQuantity,
                        weight: netWeight,
                    };
                    if (pallet.receiving_employee) {
                        const recEmp = document.getElementById("receiving-employee-select");
                        if (recEmp) recEmp.value = pallet.receiving_employee;
                    }
                    if (pallet.processing_employee) {
                        const procEmp = document.getElementById("processing-employee-select");
                        if (procEmp) procEmp.value = pallet.processing_employee;
                    }
                    const intakeEmpVal = pallet.intake_employee || pallet.ecommerce_employee || pallet.refurbishing_employee || pallet.universal_waste_employee || pallet.data_destruction_employee;
                    if (intakeEmpVal) {
                        const intEmp = document.getElementById("intake-employee-select");
                        if (intEmp) intEmp.value = intakeEmpVal;
                    }
                    this.applyPalletLimits();
                    this.updateRemainingDisplay();
                }
            })
            .catch((error) => console.error("Error fetching pallet details:", error));
    }
    
    applyStoredPalletData() {
        const weightInput = document.querySelector('input[name="weight"]');
        const quantityInput = document.querySelector('input[name="quantity"]');
        
        if (weightInput) {
            weightInput.value = this.selectedPalletData.weight || 0;
            weightInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
        
        if (this.intakeType === "Data Destruction Intake" || this.intakeType === "IT Assets Intake") {
            if (quantityInput) {
                quantityInput.readOnly = false;
                quantityInput.style.backgroundColor = "";
            }
        } else if (quantityInput) {
            quantityInput.value = this.selectedPalletData.quantity || 0;
            quantityInput.dispatchEvent(new Event('change', { bubbles: true }));
        }

        this.updateRemainingDisplay();
    }

    applyPalletLimits() {
        const quantityInput = document.querySelector('input[name="quantity"]');
        const weightInput = document.querySelector('input[name="weight"]');

        if (quantityInput) {
            quantityInput.min = "0";
            quantityInput.max = String(this.palletRemaining.quantity || 0);
        }

        if (weightInput) {
            weightInput.min = "0";
            weightInput.max = String(this.palletRemaining.weight || 0);
        }
    }

    clearPalletLimits() {
        const quantityInput = document.querySelector('input[name="quantity"]');
        const weightInput = document.querySelector('input[name="weight"]');

        if (quantityInput) {
            quantityInput.removeAttribute("max");
        }

        if (weightInput) {
            weightInput.removeAttribute("max");
        }
    }

    updateRemainingDisplay() {
        const quantityInput = document.querySelector('input[name="quantity"]');
        const weightInput = document.querySelector('input[name="weight"]');
        const quantityRemainingText = document.getElementById("quantity-remaining-text");
        const weightRemainingText = document.getElementById("weight-remaining-text");
        const summaryWrap = document.getElementById("selected-pallet-summary");
        const summaryText = document.getElementById("selected-pallet-remaining-summary");

        if (!quantityRemainingText || !weightRemainingText || !summaryWrap || !summaryText) {
            return;
        }

        if (!this.selectedPalletId) {
            quantityRemainingText.textContent = "Remaining Quantity: --";
            weightRemainingText.textContent = "Remaining Weight: -- lbs";
            summaryText.textContent = "--";
            summaryWrap.classList.add("hidden");
            quantityRemainingText.classList.remove("text-red-600");
            weightRemainingText.classList.remove("text-red-600");
            return;
        }

        const enteredQuantity = Math.max(0, parseInt(quantityInput?.value, 10) || 0);
        const enteredWeight = Math.max(0, parseFloat(weightInput?.value) || 0);
        const remainingQuantityRaw = (this.palletRemaining.quantity || 0) - enteredQuantity;
        const remainingWeightRaw = (this.palletRemaining.weight || 0) - enteredWeight;
        const remainingQuantity = Math.max(0, remainingQuantityRaw);
        const remainingWeight = Math.max(0, remainingWeightRaw);
        const quantityOver = Math.max(0, Math.abs(Math.min(0, remainingQuantityRaw)));
        const weightOver = Math.max(0, Math.abs(Math.min(0, remainingWeightRaw)));

        quantityRemainingText.textContent = quantityOver > 0
            ? `Remaining Quantity: 0 (over by ${quantityOver})`
            : `Remaining Quantity: ${remainingQuantity}`;
        weightRemainingText.textContent = weightOver > 0
            ? `Remaining Weight: 0.00 lbs (over by ${weightOver.toFixed(2)} lbs)`
            : `Remaining Weight: ${remainingWeight.toFixed(2)} lbs`;
        summaryText.textContent = `${remainingQuantity} items, ${remainingWeight.toFixed(2)} lbs`;
        quantityRemainingText.classList.toggle("text-red-600", quantityOver > 0);
        weightRemainingText.classList.toggle("text-red-600", weightOver > 0);
        summaryWrap.classList.remove("hidden");
    }

    validatePalletAmounts() {
        if (!this.selectedPalletId) {
            return true;
        }

        const quantityInput = document.querySelector('input[name="quantity"]');
        const weightInput = document.querySelector('input[name="weight"]');
        const enteredQuantity = Math.max(0, parseInt(quantityInput?.value, 10) || 0);
        const enteredWeight = Math.max(0, parseFloat(weightInput?.value) || 0);
        const remainingQuantity = this.palletRemaining.quantity || 0;
        const remainingWeight = this.palletRemaining.weight || 0;

        if (enteredQuantity > remainingQuantity) {
            alert(`Quantity cannot be greater than the remaining quantity (${remainingQuantity}).`);
            quantityInput?.focus();
            this.updateRemainingDisplay();
            return false;
        }

        if (enteredWeight > remainingWeight + 0.0001) {
            alert(`Weight cannot be greater than the remaining weight (${remainingWeight.toFixed(2)} lbs).`);
            weightInput?.focus();
            this.updateRemainingDisplay();
            return false;
        }

        return true;
    }

    saveItem(isDraft = false) {
        if (!this.validateCurrentStep()) {
            alert("Please fill in all required fields");
            return;
        }

        if (!this.validatePalletAmounts()) {
            return;
        }

        const categoryFinalElement = document.querySelector('#category-final');
        const mainCategoryOption = document.querySelector('#main-category-select option:checked');
        const subCategorySelect = document.querySelector('#sub-category-select');
        const subSubCategorySelect = document.querySelector('#sub-sub-category-select');
        const getSelectedText = (selectElement) => {
            if (!selectElement || !selectElement.value) {
                return '';
            }

            const selectedOption = selectElement.options[selectElement.selectedIndex];
            if (!selectedOption) {
                return '';
            }

            const text = selectedOption.textContent.trim();
            return text.startsWith('Select ') ? '' : text;
        };
        const isMultiItemIntake = (this.intakeType === "Data Destruction Intake" || this.intakeType === "IT Assets Intake");
        const formInputs = {
            item_name: isMultiItemIntake ? "" : document.querySelector('input[name="item_name"]').value,
            brand: isMultiItemIntake ? "" : document.querySelector('input[name="brand"]').value,
            model: isMultiItemIntake ? "" : document.querySelector('input[name="model"]').value,
            serial_number: isMultiItemIntake ? "" : document.querySelector('input[name="serial_number"]').value,
            category: (this.intakeType === "eBay Product Intake" || this.intakeType === "eBay Product")
                ? (mainCategoryOption?.textContent.trim() || (categoryFinalElement ? categoryFinalElement.value.trim() : '') || 'eBay Product')
                : (mainCategoryOption?.textContent.trim() || (categoryFinalElement ? categoryFinalElement.value.trim() : '')),
            sub_category: getSelectedText(subCategorySelect),
            sub_sub_category: getSelectedText(subSubCategorySelect),
            condition: document.querySelector('select[name="condition"]').value,
            estimated_value: document.querySelector('input[name="estimated_value"]').value,
            weight: document.querySelector('input[name="weight"]').value,
            quantity: document.querySelector('input[name="quantity"]').value,
            notes: document.querySelector('textarea[name="notes"]').value,
            receiving_employee: document.querySelector('input[name="receiving_employee"]')?.value || document.querySelector('select[name="receiving_employee"]')?.value || "",
            processing_employee: document.querySelector('input[name="processing_employee"]')?.value || "",
            intake_employee: document.querySelector('select[name="intake_employee"]')?.value || document.querySelector('input[name="intake_employee"]')?.value || "",
        };

        if (formInputs.intake_employee) {
            localStorage.setItem("remembered_intake_employee", formInputs.intake_employee);
            this.addCustomEmployeeToDatalist(formInputs.intake_employee);
        }

        if (!isMultiItemIntake && !(this.intakeType === "eBay Product Intake" || this.intakeType === "eBay Product")) {
            if (!formInputs.brand || !formInputs.model || !formInputs.category || !formInputs.condition) {
                alert("Please fill in all required fields (Brand, Model, Category, Condition)");
                return;
            }
        } else if (this.intakeType === "eBay Product Intake" || this.intakeType === "eBay Product") {
            if (!formInputs.item_name || !formInputs.condition) {
                alert("Please fill in all required fields (Item Description, Condition)");
                return;
            }
        } else {
            if (!formInputs.category || !formInputs.condition) {
                alert("Please fill in all required fields (Category, Condition)");
                return;
            }
        }

        const formData = new FormData();
        formData.append("barcode", this.barcode);
        if (this.selectedPalletId && /^\d+$/.test(String(this.selectedPalletId))) {
            formData.append("selected_pallet_id", this.selectedPalletId);
        }
        formData.append("intake_type", this.intakeType);
        
        if (isMultiItemIntake) {
            const combinedNames = this.ddItems.map(i => (i.name || '').trim()).join(', ');
            const combinedBrands = this.ddItems.map(i => (i.brand || '').trim()).join(', ');
            const combinedModels = this.ddItems.map(i => (i.model || '').trim()).join(', ');
            const combinedSerials = this.ddItems.map(i => (i.serial || '').trim()).join(', ');

            formData.append("item_name", combinedNames);
            formData.append("brand", combinedBrands);
            formData.append("model", combinedModels);
            formData.append("serial_number", combinedSerials);
        } else {
            formData.append("item_name", formInputs.item_name);
            formData.append("brand", formInputs.brand || "N/A");
            formData.append("model", formInputs.model || "N/A");
            formData.append("serial_number", formInputs.serial_number);
        }

        formData.append("category", formInputs.category);
        formData.append("sub_category", formInputs.sub_category);
        formData.append("sub_sub_category", formInputs.sub_sub_category);
        formData.append("condition", formInputs.condition);
        
        if (formInputs.estimated_value !== "" && formInputs.estimated_value !== null && !isNaN(formInputs.estimated_value)) {
            formData.append("estimated_value", formInputs.estimated_value);
        }
        if (formInputs.weight !== "" && formInputs.weight !== null && !isNaN(formInputs.weight)) {
            formData.append("weight", formInputs.weight);
        }
        if (formInputs.quantity !== "" && formInputs.quantity !== null && !isNaN(formInputs.quantity)) {
            formData.append("quantity", formInputs.quantity);
        }

        formData.append("notes", formInputs.notes);
        formData.append("receiving_employee", formInputs.receiving_employee);
        formData.append("processing_employee", formInputs.processing_employee);
        formData.append("intake_employee", formInputs.intake_employee);

        const folderTypeVal = document.querySelector('select[name="folder_type"]')?.value || 'Ebay';
        const ebayPersonVal = document.querySelector('[name="ebay_person"]')?.value || formInputs.intake_employee || '';
        const putAwayLocationVal = document.querySelector('input[name="put_away_location"]')?.value || '';
        const ebayLinkVal = document.querySelector('input[name="ebay_link"]')?.value || '';
        const websiteLinkVal = document.querySelector('input[name="website_link"]')?.value || '';
        const socialMedia1Val = document.querySelector('input[name="social_media_1"]')?.value || '';

        formData.append("folder_type", folderTypeVal);
        formData.append("ebay_person", ebayPersonVal);
        formData.append("put_away_location", putAwayLocationVal);
        formData.append("ebay_link", ebayLinkVal);
        formData.append("website_link", websiteLinkVal);
        formData.append("social_media_1", socialMedia1Val);

        if (this.intakeType === "E-commerce Intake") {
            formData.append("ecommerce_employee", formInputs.intake_employee);
        } else if (this.intakeType === "eBay Product Intake" || this.intakeType === "eBay Product") {
            formData.append("ecommerce_employee", formInputs.intake_employee);
        } else if (this.intakeType === "Refurbishing Intake") {
            formData.append("refurbishing_employee", formInputs.intake_employee);
        } else if (this.intakeType === "Universal Waste Intake") {
            formData.append("universal_waste_employee", formInputs.intake_employee);
        } else if (this.intakeType === "Data Destruction Intake" || this.intakeType === "IT Assets Intake") {
            formData.append("data_destruction_employee", formInputs.intake_employee);
        }
        
        if (this.accumulatedFiles && this.accumulatedFiles.length > 0) {
            for (let i = 0; i < this.accumulatedFiles.length; i++) {
                formData.append("photos[]", this.accumulatedFiles[i]);
            }
        }

        if (isDraft) {
            formData.append("is_draft", "1");
            formData.append("status", "Draft");
        }

        this.showLoading(isDraft ? "Saving item as draft..." : "Saving item...");

        fetch(SAVE_API, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                this.hideLoading();
                if (data.success) {
                    let targetUrl = data.redirect_url;
                    if (!targetUrl) {
                        switch (this.intakeType) {
                            case "Repair Intake":
                            case "Repair":
                                targetUrl = "/admin/repair-intake";
                                break;
                            case "IT Assets Intake":
                            case "IT Assets":
                                targetUrl = "/admin/it-assets-entries";
                                break;
                            case "Universal Waste Intake":
                            case "Universal Waste":
                                targetUrl = "/admin/universal-waste-entries";
                                break;
                            case "Data Destruction Intake":
                            case "Data Destruction":
                                targetUrl = "/admin/data-destruction-entries";
                                break;
                            case "Refurbishing Intake":
                            case "Refurbishing":
                                targetUrl = "/admin/refurbishing";
                                break;
                            case "E-commerce Intake":
                                targetUrl = "/admin/ecommerce-entries";
                                break;
                            case "eBay Product Intake":
                            case "eBay Product":
                            default:
                                targetUrl = "/admin/ebay-products";
                                break;
                        }
                    }
                    window.location.href = targetUrl;
                } else {
                    alert("Error: " + (data.message || "Failed to save item"));
                }
            })
            .catch((error) => {
                this.hideLoading();
                alert("Failed to save item");
            });
    }

    validateCurrentStep() {
        switch (this.currentStep) {
            case 1:
                return this.intakeType !== "";
            case 2:
                const useExistingBarcode = document.getElementById("existing-barcode-checkbox")?.checked;
                if (useExistingBarcode) {
                    const selectedPallet = (document.getElementById("pallet-select")?.value || "").trim();
                    if (!selectedPallet) {
                        alert("Please select or scan a pallet barcode.");
                        return false;
                    }
                    this.selectedPalletId = selectedPallet;
                } else if (!(this.barcode || "").trim()) {
                    alert("Please generate a new barcode or check 'Existing barcode'.");
                    return false;
                }

                const isEbayIntake = (this.intakeType === "eBay Product Intake" || this.intakeType === "eBay Product");
                const category = (document.querySelector('#category-final').value.trim() || document.querySelector('#main-category-select option:checked')?.textContent.trim() || '');
                const condition = document.querySelector('select[name="condition"]').value.trim();
                
                if (!isEbayIntake && !category) { alert("Category is required"); return false; }
                if (!condition) { alert("Condition is required"); return false; }
                
                if (this.intakeType === "Data Destruction Intake" || this.intakeType === "IT Assets Intake") {
                    if (this.ddItems.length === 0) {
                        alert("Please add at least one item.");
                        return false;
                    }
                } else if (isEbayIntake) {
                    const itemName = document.querySelector('input[name="item_name"]').value.trim();
                    if (!itemName) { alert("Description is required"); return false; }
                } else {
                    const brand = document.querySelector('input[name="brand"]').value.trim();
                    const model = document.querySelector('input[name="model"]').value.trim();
                    if (!brand) { alert("Brand is required"); return false; }
                    if (!model) { alert("Model is required"); return false; }
                }
                if (!this.validatePalletAmounts()) { return false; }
                return true;
            default:
                return false;
        }
    }

    nextStep() {
        if (!this.validateCurrentStep()) {
            return;
        }

        if (this.currentStep < 2) {
            this.currentStep++;
            this.updateStepperUI();
            
            if (this.currentStep === 2 && Object.keys(this.selectedPalletData).length > 0) {
                setTimeout(() => this.applyStoredPalletData(), 300);
            }
        }
    }

    previousStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.updateStepperUI();
        }
    }

    updateStepperUI() {
        const containers = document.querySelectorAll(".step-container");
        containers.forEach((container) => {
            const stepNum = parseInt(container.id.replace("step-", ""));
            if (stepNum === this.currentStep) {
                container.classList.remove("hidden");
            } else {
                container.classList.add("hidden");
            }
        });

        const progressLine = document.getElementById("progress-line");
        if (progressLine) {
            const progress = ((this.currentStep - 1) / 1) * 100;
            progressLine.style.width = progress + "%";
        }

        document.querySelectorAll(".step-indicator").forEach((indicator) => {
            const stepNum = parseInt(indicator.dataset.step);
            const circle = indicator.querySelector('.step-circle');
            const number = indicator.querySelector('.step-number');
            const checkIcon = indicator.querySelector('.fa-check');
            const label = indicator.querySelector('.step-label');

            if (stepNum === this.currentStep) {
                circle.style.backgroundColor = "#2563eb";
                circle.style.borderColor = "#2563eb";
                circle.style.color = "white";
                number.style.display = "inline";
                if (checkIcon) checkIcon.style.display = "none";
                label.style.color = "#2563eb";
            } else if (stepNum < this.currentStep) {
                circle.style.backgroundColor = "#10b981";
                circle.style.borderColor = "#10b981";
                circle.style.color = "white";
                number.style.display = "none";
                if (checkIcon) checkIcon.style.display = "inline";
                label.style.color = "#10b981";
            } else {
                circle.style.backgroundColor = "#f8fafc";
                circle.style.borderColor = "#e2e8f0";
                circle.style.color = "#94a3b8";
                number.style.display = "inline";
                if (checkIcon) checkIcon.style.display = "none";
                label.style.color = "#cbd5e1";
            }
        });

        const backBtn = document.getElementById("back-btn");
        const nextBtn = document.getElementById("next-btn");

        if (backBtn) backBtn.style.display = this.currentStep > 1 ? "flex" : "none";
        if (nextBtn) nextBtn.style.display = "none";
    }

    showLoading(message = "Loading...") {
        let loader = document.getElementById("loading-spinner");
        if (!loader) {
            loader = document.createElement("div");
            loader.id = "loading-spinner";
            loader.innerHTML = `
                <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); z-index: 1000;">
                    <div style="text-align: center;">
                        <div style="border: 4px solid #e5e7eb; border-top: 4px solid #2563eb; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 10px;"></div>
                        <p>${message}</p>
                    </div>
                    <style>
                        @keyframes spin {
                            0% { transform: rotate(0deg); }
                            100% { transform: rotate(360deg); }
                        }
                    </style>
                </div>
            `;
            document.body.appendChild(loader);
        }
        loader.style.display = "block";
    }

    hideLoading() {
        const loader = document.getElementById("loading-spinner");
        if (loader) loader.style.display = "none";
    }

    showSuccessScreen(data) {
        const successScreen = document.getElementById("success-screen");
        const successBarcodeText = document.getElementById("success-barcode-text");
        const successBarcodeImage = document.getElementById("success-barcode-image");
        const successBarcodeDisplay = document.getElementById("success-barcode-display");

        const barcode = data.barcode || this.barcode;

        if (successBarcodeText) successBarcodeText.textContent = barcode;
        if (successBarcodeDisplay) successBarcodeDisplay.textContent = barcode;
        if (successBarcodeImage && data.barcode_image) successBarcodeImage.innerHTML = data.barcode_image;

        this.populatePrintLabel(barcode, data.barcodeBase64);

        if (successScreen) {
            successScreen.classList.remove("hidden");
            setTimeout(() => {
                successScreen.scrollIntoView({ behavior: "smooth", block: "start" });
            }, 100);
        }

        const footerActions = document.getElementById("footer-actions");
        if (footerActions) footerActions.style.display = "none";
    }

    populatePrintLabel(barcode, barcodeBase64) {
        const mainCategoryOption = document.querySelector('#main-category-select option:checked');
        const mainCategoryName = mainCategoryOption ? mainCategoryOption.textContent : "N/A";
        const condition = document.querySelector('select[name="condition"]').value || "N/A";
        const weight = document.querySelector('input[name="weight"]').value || "0";
        const estimatedValue = document.querySelector('input[name="estimated_value"]').value || "0";
        const barcodeImageSrc = barcodeBase64 ? `data:image/png;base64,${barcodeBase64}` : "";
        const dateStr = new Date().toLocaleDateString('en-US');
        this.latestBarcodeBase64 = barcodeBase64 || "";

        const isMultiItemIntake = (this.intakeType === "Data Destruction Intake" || this.intakeType === "IT Assets Intake");
        if (isMultiItemIntake) {
            const labels = this.ddItems.map((item) => {
                return this.generateProcessingStyleLabel({
                    barcode,
                    name: item.name || "N/A",
                    brand: item.brand || "N/A",
                    model: item.model || "N/A",
                    serial_number: item.serial || "N/A",
                    category: mainCategoryName,
                    condition,
                    quantity: 1,
                    weight: parseFloat(weight) / this.ddItems.length,
                    estimated_value: parseFloat(estimatedValue) / this.ddItems.length,
                    created_at: new Date().toISOString(),
                }, "Item Intake Label", barcodeImageSrc);
            });
            this.latestPrintLabelHtml = labels.join("");
        } else {
            const itemName = document.querySelector('input[name="item_name"]').value || "N/A";
            const brand = document.querySelector('input[name="brand"]').value || "N/A";
            const model = document.querySelector('input[name="model"]').value || "N/A";
            const serialNumber = document.querySelector('input[name="serial_number"]').value || "N/A";
            const quantity = document.querySelector('input[name="quantity"]').value || "0";

            this.latestPrintLabelHtml = this.generateProcessingStyleLabel({
                barcode,
                name: itemName,
                brand,
                model,
                serial_number: serialNumber,
                category: mainCategoryName,
                condition,
                quantity,
                weight,
                estimated_value: estimatedValue,
                created_at: new Date().toISOString(),
            }, "Item Intake Label", barcodeImageSrc);
        }

        const printableLabel = document.getElementById("printable-item-label");
        if (printableLabel) {
            printableLabel.innerHTML = this.latestPrintLabelHtml;
        }

        if (barcodeBase64) {
            const printBarcodeImage = document.getElementById("print-barcode-image");
            if (printBarcodeImage) {
                printBarcodeImage.innerHTML = `<img src="data:image/png;base64,${barcodeBase64}" alt="${barcode}" style="max-width: 300px; height: auto;">`;
            }
        }

        const printBarcodeText = document.getElementById("print-barcode-text");
        if (printBarcodeText) printBarcodeText.textContent = barcode;

        const printItemName = document.getElementById("print-item-name");
        if (printItemName) {
            printItemName.textContent = isMultiItemIntake 
                ? this.ddItems.map(i => i.name).join(", ") 
                : (document.querySelector('input[name="item_name"]').value || "N/A");
        }

        const printBrandModel = document.getElementById("print-brand-model");
        if (printBrandModel) {
            printBrandModel.textContent = isMultiItemIntake
                ? this.ddItems.map(i => `${i.brand} ${i.model}`).join(", ")
                : `${document.querySelector('input[name="brand"]').value || "N/A"} ${document.querySelector('input[name="model"]').value || "N/A"}`;
        }

        const printSerial = document.getElementById("print-serial");
        if (printSerial) {
            printSerial.textContent = isMultiItemIntake
                ? this.ddItems.map(i => i.serial).join(", ")
                : (document.querySelector('input[name="serial_number"]').value || "N/A");
        }

        const printCategory = document.getElementById("print-category");
        if (printCategory) printCategory.textContent = mainCategoryName;

        const printBarcodeFinal = document.getElementById("print-barcode-final");
        if (printBarcodeFinal) printBarcodeFinal.textContent = barcode;

        const printDate = document.getElementById("print-date");
        if (printDate) printDate.textContent = dateStr;
    }

    printLabel() {
        if (!this.latestPrintLabelHtml) {
            this.populatePrintLabel(this.barcode, this.latestBarcodeBase64);
        }

        this.openLabelPrintWindow(this.latestPrintLabelHtml);
    }

    generateProcessingStyleLabel(item, labelTitle = "Item Label", barcodeImageSrc = "") {
        const barcode = item.barcode || "N/A";
        const brandModel = [item.brand, item.model].filter(Boolean).join(" ") || "N/A";
        const createdDate = item.created_at ? new Date(item.created_at).toLocaleDateString() : new Date().toLocaleDateString();
        const barcodeBlock = barcodeImageSrc
            ? `<img src="${this.escapeHtml(barcodeImageSrc)}" alt="barcode">`
            : `<img src="https://bwipjs-api.metafloor.com/?bcid=code128&text=${encodeURIComponent(barcode)}&scale=2&height=12&includetext=false" alt="barcode">`;

        return `
            <section class="recycling-print-label">
                <div class="label-head">
                    <strong>IT Investment Recoveries</strong>
                    <span>${this.escapeHtml(labelTitle)}</span>
                </div>
                <div class="barcode-block">
                    ${barcodeBlock}
                    <div>${this.escapeHtml(barcode)}</div>
                </div>
                <div class="label-grid">
                    ${this.renderLabelRow("Item", item.name)}
                    ${this.renderLabelRow("Brand/Model", brandModel)}
                    ${this.renderLabelRow("Serial", item.serial_number || "N/A")}
                    ${this.renderLabelRow("Category", item.category || "N/A")}
                    ${this.renderLabelRow("Condition", item.condition || "N/A")}
                    ${this.renderLabelRow("Item Count", item.quantity || "0")}
                    ${this.renderLabelRow("Weight", `${this.numberFormat(item.weight, 2)} lbs`)}
                    ${this.renderLabelRow("Value", `$${this.numberFormat(item.estimated_value, 2)}`)}
                    ${this.renderLabelRow("Barcode", barcode)}
                    ${this.renderLabelRow("Date", createdDate)}
                </div>
                <div class="label-checks">
                   <span><i></i> Reuse</span>
                   <span><i></i> Universal E-waste</span>
                </div>
                <div class="label-foot">
                    ${this.escapeHtml(this.formatDenverDateTime())}
                </div>
            </section>
        `;
    }

    renderLabelRow(label, value) {
        return `
            <div class="label-row">
                <span>${this.escapeHtml(label)}</span>
                <strong>${this.escapeHtml(value === null || value === undefined || value === "" ? "N/A" : value)}</strong>
            </div>
        `;
    }

    openLabelPrintWindow(html) {
        const printWindow = window.open("", "_blank", "height=360,width=520");
        if (!printWindow) {
            alert("Print window was blocked");
            return;
        }

        printWindow.document.write(`
            <html>
                <head>
                    <title>Print Label</title>
                    <style>
                        ${this.getProcessingLabelPrintStyles()}
                    </style>
                </head>
                <body>
                    ${html}
                    <div class="print-actions"><button onclick="window.print()">Confirm Print</button></div>
                </body>
            </html>
        `);
        printWindow.document.close();
    }

    getProcessingLabelPrintStyles() {
        return `
            @page { size: 3.5in 2.25in; margin: 0; }
            html, body { margin: 0; width: 3.5in; min-height: 2.25in; background: #fff; color: #000; font-family: Arial, sans-serif; }
            .recycling-print-label { width: 3.5in; height: 2.25in; box-sizing: border-box; padding: 0.08in 0.12in; border: 1px solid #111827; overflow: hidden; page-break-after: always; }
            .label-head { display: flex; align-items: baseline; justify-content: space-between; border-bottom: 1px solid #111827; padding-bottom: 0.03in; margin-bottom: 0.04in; gap: 0.08in; }
            .label-head strong { font-size: 10px; font-weight: 900; text-transform: uppercase; white-space: nowrap; }
            .label-head span { font-size: 8px; font-weight: 800; text-transform: uppercase; text-align: right; }
            .barcode-block { text-align: center; margin-bottom: 0.04in; }
            .barcode-block img { max-width: 100%; width: 100%; height: 0.36in; object-fit: contain; }
            .barcode-block > div { font-family: monospace; font-size: 10px; font-weight: 900; margin-top: 0.01in; }
            .label-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.02in 0.08in; font-size: 6.8px; line-height: 1.15; }
            .label-row { min-width: 0; display: flex; justify-content: space-between; gap: 0.04in; border-bottom: 1px solid #e5e7eb; }
            .label-row span { color: #6b7280; font-weight: 800; text-transform: uppercase; white-space: nowrap; }
            .label-row strong { color: #111827; font-weight: 900; text-align: right; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .label-checks { display: flex; justify-content: center; gap: 0.18in; margin-top: 0.1in; font-size: 8px; font-weight: 900; text-transform: uppercase; }
            .label-checks span { display: inline-flex; align-items: center; gap: 0.04in; }
            .label-checks i { width: 0.09in; height: 0.09in; border: 1px solid #111827; display: inline-block; }
            .label-foot { margin-top: 0.03in; font-size: 6.5px; color: #4b5563; text-align: center; }
            .print-actions { margin-top: 16px; text-align: center; }
            .print-actions button { background: #111827; color: #fff; border: 0; border-radius: 10px; padding: 10px 20px; font-weight: 800; cursor: pointer; }
            @media screen { body { width: auto; min-height: auto; padding: 24px; background: #f1f5f9; display: flex; flex-direction: column; align-items: center; } .recycling-print-label { background: #fff; box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15); } }
            @media print { html, body { width: 3.5in; min-height: 2.25in; padding: 0; background: #fff; } .print-actions { display: none; } }
        `;
    }

    escapeHtml(value) {
        return String(value ?? "").replace(/[&<>"']/g, (character) => ({
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;",
        })[character]);
    }

    numberFormat(value, decimals = 0) {
        const number = parseFloat(value);
        return (Number.isFinite(number) ? number : 0).toLocaleString("en-US", {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals,
        });
    }

    formatDenverDateTime(date = new Date()) {
        return new Intl.DateTimeFormat("en-US", {
            timeZone: "America/Denver",
            year: "numeric",
            month: "short",
            day: "2-digit",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hour12: true,
        }).format(date);
    }

    resetForm() {
        if (typeof this.closeCameraModal === "function") {
            this.closeCameraModal();
        }
        this.accumulatedFiles = [];
        setTimeout(() => window.location.reload(), 2000);
    }

    toggleScanner() {
        this.showScanner = !this.showScanner;
        if (this.showScanner) {
            this.startScanner();
        } else {
            this.stopScanner();
        }
    }

    startScanner() {
        if (this.quaggaStarted) return;
        
        const targetDiv = document.querySelector("#interactive");
        if (!targetDiv || typeof Quagga === "undefined") return;

        Quagga.init(
            {
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: targetDiv,
                    constraints: { facingMode: "environment" },
                },
                decoder: {
                    readers: ["code_128_reader", "ean_reader", "code_39_reader", "upc_reader"],
                },
                locate: true,
            },
            (err) => {
                if (err) return;
                Quagga.start();
                this.quaggaStarted = true;
            }
        );

        Quagga.onDetected((data) => {
            const code = data.codeResult.code;
            this.processScan(code);
            this.stopScanner();
            if (window.navigator.vibrate) window.navigator.vibrate(100);
        });
    }

    stopScanner() {
        if (this.quaggaStarted) {
            Quagga.stop();
            this.quaggaStarted = false;
        }
        this.showScanner = false;
    }

    processScan(scannedValue) {
        if (this.currentStep === 2) {
            const checkbox = document.getElementById("existing-barcode-checkbox");
            if (checkbox && checkbox.checked) {
                const palletSelect = document.getElementById("pallet-select");
                if (palletSelect) {
                    const scannedBarcode = String(scannedValue || "").trim();
                    const matchingOption = Array.from(palletSelect.options).find((option) => {
                        const barcode = option.dataset.barcode || "";
                        const label = option.textContent.trim();

                        return barcode === scannedBarcode || label === scannedBarcode || label.startsWith(`${scannedBarcode} `);
                    });

                    if (!matchingOption) {
                        alert("Scanned pallet was not found in the pallet list.");
                        return;
                    }

                    this.selectedPalletId = matchingOption.value;
                    palletSelect.value = matchingOption.value;

                    if (window.jQuery) {
                        window.jQuery(palletSelect).trigger("change");
                    } else {
                        palletSelect.dispatchEvent(new Event("change", { bubbles: true }));
                    }
                }
            } else {
                this.barcode = scannedValue;
                this.displayBarcode(scannedValue, null);
            }
        }
    }

    processUsbScan(value) {
        if (value.length > 0) this.processScan(value);
    }

    addSerialNumberFromInput() {
        const input = document.getElementById("serial-number-input");
        if (!input) return;
        const val = input.value.trim();
        if (val) {
            if (this.serialNumbers.includes(val)) {
                alert("This serial number is already added.");
                return;
            }
            this.serialNumbers.push(val);
            input.value = "";
            this.renderSerialTags();
            this.updateQuantityFromSerials();
            input.focus();
        }
    }

    applyBulkSerialNumbers() {
        const textarea = document.getElementById("bulk-serial-input");
        if (!textarea) return;
        const text = textarea.value;
        const rawSerials = text.split(/[\n,]+/);
        let addedCount = 0;
        let duplicateCount = 0;

        rawSerials.forEach((raw) => {
            const val = raw.trim();
            if (val) {
                if (this.serialNumbers.includes(val)) {
                    duplicateCount++;
                } else {
                    this.serialNumbers.push(val);
                    addedCount++;
                }
            }
        });

        textarea.value = "";
        const bulkContainer = document.getElementById("bulk-serial-container");
        if (bulkContainer) bulkContainer.classList.add("hidden");

        this.renderSerialTags();
        this.updateQuantityFromSerials();
        
        if (duplicateCount > 0) {
            console.log(`Added ${addedCount} serials. Skipped ${duplicateCount} duplicates.`);
        }
    }

    removeSerialNumber(index) {
        if (index >= 0 && index < this.serialNumbers.length) {
            this.serialNumbers.splice(index, 1);
            this.renderSerialTags();
            this.updateQuantityFromSerials();
        }
    }

    renderSerialTags() {
        const container = document.getElementById("serial-tags-container");
        if (!container) return;

        if (!this.serialNumbers || this.serialNumbers.length === 0) {
            container.innerHTML = '<span class="text-[12px] text-slate-400 italic" id="no-serials-placeholder">No serial numbers added yet.</span>';
            return;
        }

        container.innerHTML = this.serialNumbers.map((sn, idx) => `
            <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 text-xs font-bold border border-blue-100 gap-1.5 shadow-sm animate-in fade-in zoom-in-95 duration-150">
                <span>${sn}</span>
                <button type="button" class="remove-serial-tag text-blue-400 hover:text-blue-600 transition-colors focus:outline-none" data-index="${idx}">
                    <i class="fas fa-times text-[10px]"></i>
                </button>
            </span>
        `).join("");
    }

    updateQuantityFromSerials() {
        if (this.intakeType === "Data Destruction Intake" || this.intakeType === "IT Assets Intake") {
            const qtyInput = document.querySelector('input[name="quantity"]');
            if (qtyInput) {
                qtyInput.value = this.serialNumbers.length;
                qtyInput.dispatchEvent(new Event('input', { bubbles: true }));
                qtyInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
    }
}

function setupModeSwitching() {
    const cameraModeDiv = document.getElementById('camera-mode');
    const usbModeDiv = document.getElementById('usb-mode');
    const scannerToggleBtn = document.getElementById('scanner-toggle-btn');
    const interactiveDiv = document.getElementById('interactive');

    const modeCameraBtn = document.querySelector('.mode-camera');
    if (modeCameraBtn) {
        modeCameraBtn.addEventListener('click', function() {
            window.itemIntakeManager.scanMode = 'camera';
            window.itemIntakeManager.stopScanner();
            if (cameraModeDiv) cameraModeDiv.style.display = 'block';
            if (usbModeDiv) usbModeDiv.style.display = 'none';
            modeCameraBtn.classList.add('bg-white', 'shadow-sm', 'text-blue-600');
            modeCameraBtn.classList.remove('text-slate-500');
            const usbBtn = document.querySelector('.mode-usb');
            if (usbBtn) {
                usbBtn.classList.remove('bg-white', 'shadow-sm', 'text-blue-600');
                usbBtn.classList.add('text-slate-500');
            }
        });
    }

    const modeUsbBtn = document.querySelector('.mode-usb');
    if (modeUsbBtn) {
        modeUsbBtn.addEventListener('click', function() {
            window.itemIntakeManager.scanMode = 'usb';
            window.itemIntakeManager.stopScanner();
            if (cameraModeDiv) cameraModeDiv.style.display = 'none';
            if (usbModeDiv) usbModeDiv.style.display = 'block';
            if (modeCameraBtn) {
                modeCameraBtn.classList.remove('bg-white', 'shadow-sm', 'text-blue-600');
                modeCameraBtn.classList.add('text-slate-500');
            }
            modeUsbBtn.classList.add('bg-white', 'shadow-sm', 'text-blue-600');
            modeUsbBtn.classList.remove('text-slate-500');
            const usbInput = document.getElementById('usb-input');
            if (usbInput) usbInput.focus();
        });
    }

    if (scannerToggleBtn) {
        scannerToggleBtn.addEventListener('click', function() {
            window.itemIntakeManager.toggleScanner();
            
            if (window.itemIntakeManager.showScanner) {
                scannerToggleBtn.classList.add('bg-red-50', 'text-red-600');
                scannerToggleBtn.classList.remove('bg-blue-600', 'text-white');
                scannerToggleBtn.innerHTML = '<i class="fas fa-times"></i> <span>Stop</span>';
                if (interactiveDiv) interactiveDiv.style.display = 'block';
            } else {
                scannerToggleBtn.classList.remove('bg-red-50', 'text-red-600');
                scannerToggleBtn.classList.add('bg-blue-600', 'text-white');
                scannerToggleBtn.innerHTML = '<i class="fas fa-camera"></i> <span>Scan Barcode</span>';
                if (interactiveDiv) interactiveDiv.style.display = 'none';
            }
        });
    }
}

function setupUsbInput() {
    const usbInput = document.getElementById('usb-input');
    if (usbInput) {
        setInterval(() => {
            const activeEl = document.activeElement;
            if (window.itemIntakeManager && window.itemIntakeManager.scanMode === 'usb' &&
                activeEl && activeEl.tagName !== 'INPUT' &&
                activeEl.tagName !== 'SELECT' &&
                activeEl.tagName !== 'TEXTAREA') {
                usbInput.focus();
            }
        }, 500);

        usbInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && this.value) {
                window.itemIntakeManager.processUsbScan(this.value);
                this.value = '';
            }
        });
    }
}

function renderPalletPhotoPreviews() {
    const photoPreviews = document.getElementById('photo-previews');
    if (!photoPreviews || !window.itemIntakeManager) return;

    photoPreviews.innerHTML = '';
    window.itemIntakeManager.accumulatedFiles.forEach((file, index) => {
        const isImage = file.type ? file.type.startsWith('image/') : /\.(jpg|jpeg|png|webp|gif|svg|bmp)$/i.test(file.name);
        const preview = document.createElement('div');
        preview.className = 'group relative w-20 h-20 md:w-24 md:h-24 rounded-lg overflow-hidden border-2 border-blue-100 shadow-sm bg-white flex flex-col items-center justify-center p-1';

        if (isImage) {
            const url = URL.createObjectURL(file);
            preview.innerHTML = `
                <img src="${url}" class="w-full h-full object-cover" />
                <button type="button" class="deletePalletPhoto absolute inset-0 bg-red-600/80 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center" data-index="${index}" title="Delete file">
                    <i class="fas fa-trash-alt text-white text-lg"></i>
                </button>
            `;
        } else {
            let iconClass = 'fa-file-alt', iconColor = 'text-blue-500';
            const fileName = file.name || 'file';
            if (fileName.endsWith('.pdf') || file.type === 'application/pdf') { iconClass = 'fa-file-pdf'; iconColor = 'text-red-500'; }
            else if (fileName.endsWith('.csv') || fileName.endsWith('.xlsx') || fileName.endsWith('.xls')) { iconClass = 'fa-file-excel'; iconColor = 'text-green-500'; }
            else if (fileName.endsWith('.doc') || fileName.endsWith('.docx')) { iconClass = 'fa-file-word'; iconColor = 'text-blue-600'; }

            preview.innerHTML = `
                <i class="fas ${iconClass} ${iconColor} text-2xl mb-1"></i>
                <span class="text-[9px] font-bold text-slate-600 truncate w-full text-center px-1" title="${fileName}">${fileName}</span>
                <button type="button" class="deletePalletPhoto absolute inset-0 bg-red-600/80 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center" data-index="${index}" title="Delete file">
                    <i class="fas fa-trash-alt text-white text-lg"></i>
                </button>
            `;
        }
        photoPreviews.appendChild(preview);
    });

    const badge = document.getElementById('photo-count-badge');
    if (badge) {
        const count = window.itemIntakeManager.accumulatedFiles.length;
        badge.textContent = `${count} attached`;
    }
}

function deletePalletPhoto(fileIndex) {
    if (!window.itemIntakeManager) return;
    window.itemIntakeManager.accumulatedFiles.splice(fileIndex, 1);
    renderPalletPhotoPreviews();
    if (typeof window.itemIntakeManager.updateCameraCounts === "function") {
        window.itemIntakeManager.updateCameraCounts();
    }
}

function compressIntakePhoto(file, maxDimension = 1920, quality = 0.82) {
    return new Promise((resolve) => {
        try {
            if (!file || !file.type || !file.type.startsWith('image/')) {
                resolve(file);
                return;
            }

            if (file.size <= 1024 * 1024) {
                resolve(file);
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const img = new Image();
                    img.onload = function() {
                        try {
                            let width = img.width;
                            let height = img.height;

                            if (width > maxDimension || height > maxDimension) {
                                if (width > height) {
                                    height = Math.round((height * maxDimension) / width);
                                    width = maxDimension;
                                } else {
                                    width = Math.round((width * maxDimension) / height);
                                    height = maxDimension;
                                }
                            }

                            const canvas = document.createElement('canvas');
                            canvas.width = width;
                            canvas.height = height;

                            const ctx = canvas.getContext('2d');
                            if (ctx && typeof canvas.toBlob === 'function') {
                                ctx.drawImage(img, 0, 0, width, height);
                                canvas.toBlob((blob) => {
                                    if (blob) {
                                        const newFileName = (file.name || 'photo').replace(/\.[^/.]+$/, "") + ".jpg";
                                        let compressedFile;
                                        try {
                                            compressedFile = new File([blob], newFileName, {
                                                type: 'image/jpeg',
                                                lastModified: Date.now()
                                            });
                                        } catch (err) {
                                            blob.name = newFileName;
                                            blob.lastModified = Date.now();
                                            compressedFile = blob;
                                        }
                                        resolve(compressedFile);
                                    } else {
                                        resolve(file);
                                    }
                                }, 'image/jpeg', quality);
                            } else {
                                resolve(file);
                            }
                        } catch (err) {
                            console.warn('Image canvas compression error:', err);
                            resolve(file);
                        }
                    };
                    img.onerror = () => resolve(file);
                    img.src = e.target.result;
                } catch (err) {
                    console.warn('Image load error during compression:', err);
                    resolve(file);
                }
            };
            reader.onerror = () => resolve(file);
            reader.readAsDataURL(file);
        } catch (err) {
            console.warn('Photo compression error:', err);
            resolve(file);
        }
    });
}

function setupPhotoPreview() {
    const photoInput = document.getElementById('photo-input');
    const photoPreviews = document.getElementById('photo-previews');

    if (photoInput && photoPreviews) {
        photoInput.addEventListener('change', async function(e) {
            const files = Array.from(this.files);
            this.value = '';

            for (const file of files) {
                let processedFile = file;
                if (file.type && file.type.startsWith('image/')) {
                    processedFile = await compressIntakePhoto(file);
                }
                const isDuplicate = window.itemIntakeManager.accumulatedFiles.some(f => 
                    f.name === processedFile.name && f.size === processedFile.size
                );
                if (!isDuplicate) {
                    window.itemIntakeManager.accumulatedFiles.push(processedFile);
                    renderPalletPhotoPreviews();
                }
            }
        });

        $(document).on('click', '.deletePalletPhoto', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const fileIndex = parseInt($(this).data('index'));
            deletePalletPhoto(fileIndex);
        });
    }
}

(function autoLoadIntakeParts() {
    const requiredParts = [
        { id: 'script-intake-refurb', src: '/js/admin/intake/item-intake-refurb.js', method: 'applyRefurbUI' },
        { id: 'script-intake-dd', src: '/js/admin/intake/item-intake-data-destruction.js', method: 'applyDataDestructionUI' },
        { id: 'script-intake-it', src: '/js/admin/intake/item-intake-it-assets.js', method: 'applyItAssetsUI' },
        { id: 'script-intake-repair', src: '/js/admin/intake/item-intake-repair.js', method: 'initRepairIntake' },
        { id: 'script-intake-uw', src: '/js/admin/intake/item-intake-universal-waste.js', method: 'applyUniversalWasteUI' }
    ];

    requiredParts.forEach(part => {
        if (typeof ItemIntakeManager.prototype[part.method] !== 'function' && !document.getElementById(part.id)) {
            const script = document.createElement('script');
            script.id = part.id;
            script.src = part.src;
            script.async = false;
            document.head.appendChild(script);
        }
    });
})();

document.addEventListener('DOMContentLoaded', function() {
    try {
        window.itemIntakeManager = new ItemIntakeManager();
        setupModeSwitching();
        setupUsbInput();
        setupPhotoPreview();
    } catch(error) {
        console.error('Error initializing ItemIntakeManager:', error);
    }
});
/* jshint ignore:end */
/* eslint-enable */
