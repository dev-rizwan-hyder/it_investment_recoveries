/* jshint ignore:start */
/* eslint-disable */
/**
 * Item Intake - Part 1: Refurb / E-commerce Intake
 * Handles: Refurb/E-commerce Intake, eBay Product Intake, Refurbished Intake
 */

(function () {
    if (typeof ItemIntakeManager === "undefined") {
        console.error("ItemIntakeManager base class not found. Ensure item-intake.js is loaded first.");
        return;
    }

    ItemIntakeManager.prototype.applyRefurbUI = function () {
        const ebayFolderTypeWrapper = document.getElementById("ebay-folder-type-wrapper");
        const ebayPersonFieldWrapper = document.getElementById("ebay-person-field-wrapper");
        const ebayLinksSection = document.getElementById("ebay-links-section");
        const categorySelectionWrapper = document.getElementById("category-selection-wrapper");
        const brandWrapper = document.getElementById("brand-wrapper");
        const modelWrapper = document.getElementById("model-wrapper");
        const itemNameLabel = document.getElementById("item-name-label");
        const intakeEmployeeWrapper = document.getElementById("intake-employee-wrapper");
        const cameraBtnWrapper = document.getElementById("camera-capture-btn-wrapper");

        if (ebayFolderTypeWrapper) ebayFolderTypeWrapper.classList.remove("hidden");
        if (ebayPersonFieldWrapper) ebayPersonFieldWrapper.classList.remove("hidden");
        if (ebayLinksSection) ebayLinksSection.classList.remove("hidden");
        if (categorySelectionWrapper) categorySelectionWrapper.classList.add("hidden");
        if (brandWrapper) brandWrapper.classList.add("hidden");
        if (modelWrapper) modelWrapper.classList.add("hidden");
        if (intakeEmployeeWrapper) intakeEmployeeWrapper.classList.remove("hidden");
        if (itemNameLabel) itemNameLabel.textContent = "Description *";
        if (cameraBtnWrapper) cameraBtnWrapper.classList.remove("hidden");
    };

    ItemIntakeManager.prototype.scanSerialFromCamera = function () {
        const serialField = document.getElementById("serial-number-field");
        if (!serialField) return;

        const existingOverlay = document.getElementById("serial-camera-overlay");
        if (existingOverlay) existingOverlay.remove();

        const overlay = document.createElement("div");
        overlay.id = "serial-camera-overlay";
        overlay.style.cssText = "position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.85);display:flex;flex-direction:column;align-items:center;justify-content:center;padding:20px;";
        overlay.innerHTML = `
            <div style="background:white;padding:24px;border-radius:16px;max-width:420px;width:100%;text-align:center;position:relative;box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
                <h3 style="font-weight:bold;font-size:18px;margin-bottom:12px;color:#1e293b;">Scan Serial Barcode</h3>
                <div id="serial-interactive" style="width:100%;height:240px;background:#0f172a;border-radius:12px;overflow:hidden;position:relative;margin-bottom:16px;display:flex;align-items:center;justify-content:center;color:white;">Scanning camera...</div>
                <div style="display:flex;gap:8px;">
                    <input type="text" id="serial-manual-input" placeholder="Or type serial number..." style="flex:1;padding:10px;border:1px solid #cbd5e1;border-radius:10px;font-size:14px;outline:none;">
                    <button type="button" id="serial-use-manual" style="background:#2563eb;color:white;padding:10px 16px;border-radius:10px;font-weight:bold;font-size:13px;">Save</button>
                </div>
                <button type="button" id="close-serial-camera" style="position:absolute;top:12px;right:16px;background:none;border:none;font-size:24px;color:#64748b;cursor:pointer;">&times;</button>
            </div>
        `;
        document.body.appendChild(overlay);

        const closeBtn = overlay.querySelector("#close-serial-camera");
        const useBtn = overlay.querySelector("#serial-use-manual");
        const manualInput = overlay.querySelector("#serial-manual-input");
        const targetDiv = overlay.querySelector("#serial-interactive");

        const cleanup = () => {
            if (typeof Quagga !== "undefined") {
                try { Quagga.stop(); } catch(e) {}
            }
            overlay.remove();
        };

        closeBtn.addEventListener("click", cleanup);
        useBtn.addEventListener("click", () => {
            if (manualInput.value.trim()) {
                serialField.value = manualInput.value.trim();
            }
            cleanup();
        });
        manualInput.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
                e.preventDefault();
                if (manualInput.value.trim()) {
                    serialField.value = manualInput.value.trim();
                }
                cleanup();
            }
        });

        if (typeof Quagga !== "undefined" && targetDiv) {
            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: targetDiv,
                    constraints: { facingMode: "environment" }
                },
                decoder: {
                    readers: ["code_128_reader", "code_39_reader", "ean_reader", "upc_reader"]
                },
                locate: true
            }, (err) => {
                if (!err) {
                    Quagga.start();
                    Quagga.onDetected((data) => {
                        if (data && data.codeResult && data.codeResult.code) {
                            serialField.value = data.codeResult.code;
                            if (window.navigator.vibrate) window.navigator.vibrate(100);
                            cleanup();
                        }
                    });
                }
            });
        }
    };

    ItemIntakeManager.prototype.initCameraFeatures = function () {
        const openCameraBtn = document.getElementById("open-camera-capture-btn");
        const shutterBtn = document.getElementById("intake-camera-shutter-btn");
        const closeXBtn = document.getElementById("camera-close-x-btn");
        const doneBtn = document.getElementById("intake-camera-done-btn");
        const switchBtn = document.getElementById("camera-switch-btn");
        const fallbackInput = document.getElementById("intake-camera-fallback-input");

        if (openCameraBtn) {
            openCameraBtn.addEventListener("click", (e) => {
                e.preventDefault();
                this.openCameraModal();
            });
        }

        if (shutterBtn) {
            shutterBtn.addEventListener("click", (e) => {
                e.preventDefault();
                this.captureCameraPhoto();
            });
        }

        if (closeXBtn) {
            closeXBtn.addEventListener("click", (e) => {
                e.preventDefault();
                this.closeCameraModal();
            });
        }

        if (doneBtn) {
            doneBtn.addEventListener("click", (e) => {
                e.preventDefault();
                this.closeCameraModal();
            });
        }

        const cancelBtn = document.getElementById("camera-cancel-btn");
        if (cancelBtn) {
            cancelBtn.addEventListener("click", (e) => {
                e.preventDefault();
                this.closeCameraModal();
            });
        }

        const cameraModalEl = document.getElementById("intake-camera-modal");
        if (cameraModalEl) {
            cameraModalEl.addEventListener("click", (e) => {
                if (e.target === cameraModalEl) {
                    this.closeCameraModal();
                }
            });
        }

        if (switchBtn) {
            switchBtn.addEventListener("click", (e) => {
                e.preventDefault();
                this.switchCamera();
            });
        }

        if (fallbackInput) {
            fallbackInput.addEventListener("change", async (e) => {
                const files = Array.from(e.target.files);
                e.target.value = "";
                for (const file of files) {
                    let processedFile = file;
                    if (file.type && file.type.startsWith("image/") && typeof compressIntakePhoto === "function") {
                        processedFile = await compressIntakePhoto(file);
                    }
                    this.accumulatedFiles.push(processedFile);
                    if (typeof renderPalletPhotoPreviews === "function") {
                        renderPalletPhotoPreviews();
                    }
                    const thumbUrl = URL.createObjectURL(processedFile);
                    this.addCameraThumbnail(processedFile, thumbUrl);
                    this.updateCameraCounts();
                }
            });
        }

        document.addEventListener("keydown", (e) => {
            const modal = document.getElementById("intake-camera-modal");
            if (modal && !modal.classList.contains("hidden")) {
                if (e.key === "Escape") {
                    e.preventDefault();
                    this.closeCameraModal();
                } else if (e.code === "Space" || e.key === "Enter") {
                    if (document.activeElement?.tagName !== "INPUT" && document.activeElement?.tagName !== "TEXTAREA") {
                        e.preventDefault();
                        this.captureCameraPhoto();
                    }
                }
            }
        });
    };

    ItemIntakeManager.prototype.openCameraModal = function () {
        if (typeof this.stopScanner === "function") {
            this.stopScanner();
        }

        const modal = document.getElementById("intake-camera-modal");
        if (modal) {
            modal.classList.remove("hidden");
            modal.style.display = "flex";
            document.body.style.overflow = "hidden";
        }

        this.renderAllCameraThumbnails();
        this.updateCameraCounts();
        this.startCameraStream();
    };

    ItemIntakeManager.prototype.startCameraStream = async function () {
        const video = document.getElementById("intake-camera-video");
        const errorBox = document.getElementById("intake-camera-error");
        const errorMsg = document.getElementById("intake-camera-error-msg");

        if (errorBox) errorBox.classList.add("hidden");

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            if (errorBox) {
                errorBox.classList.remove("hidden");
                if (errorMsg) errorMsg.textContent = "Live camera access is not supported by this browser. Use the device camera button below.";
            }
            return;
        }

        try {
            if (this.cameraStream) {
                this.cameraStream.getTracks().forEach((t) => t.stop());
                this.cameraStream = null;
            }

            const constraints = {
                video: {
                    facingMode: { ideal: this.cameraFacingMode },
                    width: { ideal: 1920 },
                    height: { ideal: 1080 }
                },
                audio: false
            };

            this.cameraStream = await navigator.mediaDevices.getUserMedia(constraints);
            if (video) {
                video.setAttribute("autoplay", "");
                video.setAttribute("muted", "");
                video.setAttribute("playsinline", "");
                video.setAttribute("webkit-playsinline", "");
                video.srcObject = this.cameraStream;
                try {
                    await video.play();
                } catch (playErr) {
                    console.log("Video play non-fatal:", playErr);
                }
            }
        } catch (err) {
            console.warn("Ideal camera mode failed, attempting fallback constraints:", err);
            try {
                this.cameraStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                if (video) {
                    video.setAttribute("autoplay", "");
                    video.setAttribute("muted", "");
                    video.setAttribute("playsinline", "");
                    video.setAttribute("webkit-playsinline", "");
                    video.srcObject = this.cameraStream;
                    try {
                        await video.play();
                    } catch (playErr) {
                        console.log("Video play non-fatal:", playErr);
                    }
                }
            } catch (fallbackErr) {
                console.error("Camera access failed:", fallbackErr);
                if (errorBox) {
                    errorBox.classList.remove("hidden");
                    if (errorMsg) {
                        if (fallbackErr.name === "NotAllowedError" || fallbackErr.name === "PermissionDeniedError") {
                            errorMsg.textContent = "Camera permission denied. Please allow camera permissions in your browser or use the button below.";
                        } else if (fallbackErr.name === "NotFoundError" || fallbackErr.name === "DevicesNotFoundError") {
                            errorMsg.textContent = "No camera found on this device. You can attach images using the button below.";
                        } else {
                            errorMsg.textContent = "Unable to start camera: " + (fallbackErr.message || "Unknown error") + ". You can use the button below.";
                        }
                    }
                }
            }
        }
    };

    ItemIntakeManager.prototype.captureCameraPhoto = function () {
        const video = document.getElementById("intake-camera-video");
        const canvas = document.getElementById("intake-camera-canvas");
        const flash = document.getElementById("intake-camera-flash");

        if (!video || !this.cameraStream || video.readyState < 2) {
            return;
        }

        if (flash) {
            flash.style.opacity = "0.85";
            setTimeout(() => { flash.style.opacity = "0"; }, 120);
        }

        if (window.navigator && window.navigator.vibrate) {
            try { window.navigator.vibrate(50); } catch (e) {}
        }

        const vw = video.videoWidth || 1280;
        const vh = video.videoHeight || 720;
        canvas.width = vw;
        canvas.height = vh;

        const ctx = canvas.getContext("2d");
        ctx.drawImage(video, 0, 0, vw, vh);

        canvas.toBlob(async (blob) => {
            if (!blob) return;

            const timestamp = Date.now();
            const rand = Math.floor(Math.random() * 1000);
            const filename = `camera_${timestamp}_${rand}.jpg`;
            const rawFile = new File([blob], filename, { type: "image/jpeg", lastModified: timestamp });

            let finalFile = rawFile;
            if (typeof compressIntakePhoto === "function") {
                try {
                    finalFile = await compressIntakePhoto(rawFile, 1920, 0.85);
                } catch (e) {
                    finalFile = rawFile;
                }
            }

            this.accumulatedFiles.push(finalFile);

            if (typeof renderPalletPhotoPreviews === "function") {
                renderPalletPhotoPreviews();
            }

            const thumbUrl = URL.createObjectURL(finalFile);
            const photoNum = this.accumulatedFiles.filter(f => f.type && f.type.startsWith("image/")).length;
            this.addCameraThumbnail(finalFile, thumbUrl, photoNum);
            this.updateCameraCounts();
        }, "image/jpeg", 0.88);
    };

    ItemIntakeManager.prototype.renderAllCameraThumbnails = function () {
        const container = document.getElementById("intake-camera-thumbs-container");
        if (!container) return;
        container.innerHTML = "";

        const imageFiles = this.accumulatedFiles.filter(f => f.type && f.type.startsWith("image/"));
        if (imageFiles.length === 0) {
            container.innerHTML = `
                <div id="camera-thumbs-empty" class="w-full text-center py-4 text-xs text-slate-500 italic flex items-center justify-center gap-2">
                    <i class="fas fa-camera text-slate-600"></i>
                    <span>Captured photos will appear here as you snap them...</span>
                </div>
            `;
        } else {
            imageFiles.forEach((file, idx) => {
                const thumbUrl = URL.createObjectURL(file);
                this.addCameraThumbnail(file, thumbUrl, idx + 1);
            });
        }
    };

    ItemIntakeManager.prototype.addCameraThumbnail = function (file, thumbUrl, photoNumber) {
        const container = document.getElementById("intake-camera-thumbs-container");
        if (!container) return;

        const emptyMsg = document.getElementById("camera-thumbs-empty");
        if (emptyMsg) emptyMsg.remove();

        const num = photoNumber || this.accumulatedFiles.filter(f => f.type && f.type.startsWith("image/")).length;

        const thumb = document.createElement("div");
        thumb.className = "relative flex-shrink-0 w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden border-2 border-emerald-400 shadow-lg group animate-scaleIn bg-slate-800";
        thumb.innerHTML = `
            <img src="${thumbUrl}" class="w-full h-full object-cover" />
            <span class="absolute bottom-1 left-1 bg-black/80 text-emerald-300 text-[10px] font-black px-1.5 py-0.5 rounded backdrop-blur-xs">#${num}</span>
            <button type="button" class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white w-5 h-5 rounded-full flex items-center justify-center text-[10px] shadow transition-colors" title="Delete photo">
                <i class="fas fa-times"></i>
            </button>
        `;

        thumb.querySelector("button").addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            const idx = this.accumulatedFiles.indexOf(file);
            if (idx > -1) {
                this.accumulatedFiles.splice(idx, 1);
                if (typeof renderPalletPhotoPreviews === "function") {
                    renderPalletPhotoPreviews();
                }
                this.renderAllCameraThumbnails();
                this.updateCameraCounts();
            }
        });

        container.appendChild(thumb);
        thumb.scrollIntoView({ behavior: "smooth", inline: "end" });
    };

    ItemIntakeManager.prototype.updateCameraCounts = function () {
        const count = this.accumulatedFiles.length;
        const counterText = document.getElementById("intake-camera-counter-text");
        if (counterText) {
            counterText.textContent = `${count} Captured`;
        }
        const carouselCount = document.getElementById("intake-camera-carousel-count");
        if (carouselCount) {
            carouselCount.textContent = count;
        }
        const badge = document.getElementById("photo-count-badge");
        if (badge) {
            badge.textContent = `${count} attached`;
        }
        const doneBtnCount = document.getElementById("camera-done-btn-count");
        if (doneBtnCount) {
            doneBtnCount.textContent = count;
        }
        const doneBtnSpan = document.querySelector("#intake-camera-done-btn span");
        if (doneBtnSpan && !doneBtnCount) {
            doneBtnSpan.textContent = count > 0 ? `Done (${count})` : "Done";
        }
    };

    ItemIntakeManager.prototype.switchCamera = function () {
        this.cameraFacingMode = this.cameraFacingMode === "environment" ? "user" : "environment";
        this.startCameraStream();
    };

    ItemIntakeManager.prototype.closeCameraModal = function () {
        if (this.cameraStream) {
            this.cameraStream.getTracks().forEach((track) => track.stop());
            this.cameraStream = null;
        }
        const video = document.getElementById("intake-camera-video");
        if (video) {
            video.pause();
            video.srcObject = null;
        }
        const modal = document.getElementById("intake-camera-modal");
        if (modal) {
            modal.classList.add("hidden");
            modal.style.display = "none";
        }
        document.body.style.overflow = "";

        if (typeof renderPalletPhotoPreviews === "function") {
            renderPalletPhotoPreviews();
        }
        this.updateCameraCounts();
    };
})();
/* jshint ignore:end */
/* eslint-enable */
