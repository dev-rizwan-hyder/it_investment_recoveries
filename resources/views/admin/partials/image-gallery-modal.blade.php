{{-- IMAGE GALLERY & FILE VIEWER MODAL WITH ZOOM & PAN --}}
<div id="imageGalleryModal" class="fixed inset-0 bg-black/95 z-[120] flex items-center justify-center p-2 sm:p-4 hidden animate-in fade-in duration-200 select-none" onclick="closeImageGallery()">
    {{-- Header / Toolbar --}}
    <div class="absolute top-4 left-4 right-4 flex items-center justify-between z-20" onclick="event.stopPropagation()">
        <div class="flex items-center gap-2 bg-black/50 backdrop-blur-md px-3 py-1.5 rounded-xl border border-white/10">
            <span id="imageGalleryTitle" class="text-white text-xs font-bold truncate max-w-[200px] sm:max-w-[300px]">Image Gallery</span>
        </div>

        {{-- Zoom & Rotate Controls --}}
        <div id="imageGalleryZoomToolbar" class="flex items-center gap-1.5 bg-black/50 backdrop-blur-md p-1.5 rounded-xl border border-white/10">
            <button type="button" onclick="imageGalleryZoomOut()" title="Zoom Out (-)" class="h-8 w-8 rounded-lg bg-white/10 hover:bg-white/20 text-white font-bold flex items-center justify-center transition-all active:scale-95">
                <i class="fas fa-search-minus text-xs"></i>
            </button>
            <span id="imageGalleryZoomBadge" class="px-2.5 py-1 text-xs font-mono font-bold text-white bg-white/10 rounded-lg min-w-[50px] text-center">100%</span>
            <button type="button" onclick="imageGalleryZoomIn()" title="Zoom In (+)" class="h-8 w-8 rounded-lg bg-white/10 hover:bg-white/20 text-white font-bold flex items-center justify-center transition-all active:scale-95">
                <i class="fas fa-search-plus text-xs"></i>
            </button>
            <button type="button" onclick="imageGalleryResetZoom()" title="Reset Zoom" class="h-8 px-2.5 rounded-lg bg-white/10 hover:bg-white/20 text-white font-bold text-xs flex items-center gap-1.5 transition-all active:scale-95">
                <i class="fas fa-compress text-xs"></i>
                <span class="hidden sm:inline">Reset</span>
            </button>
            <button type="button" onclick="imageGalleryRotate()" title="Rotate 90°" class="h-8 px-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center gap-1.5 transition-all active:scale-95">
                <i class="fas fa-redo text-xs"></i>
                <span class="hidden sm:inline">Rotate</span>
            </button>
        </div>

        <div class="flex items-center gap-2">
            <a id="imageGalleryDownloadBtn" href="#" target="_blank" download class="h-9 px-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1.5 transition-all shadow-lg active:scale-95" title="Download / Open File">
                <i class="fas fa-download text-xs"></i>
                <span class="hidden sm:inline">Download</span>
            </a>
            <button onclick="closeImageGallery(); event.stopPropagation();" class="text-white/80 hover:text-white bg-white/10 hover:bg-white/20 w-10 h-10 rounded-full flex items-center justify-center transition-all">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    </div>

    {{-- Main Container (Wheel Zoom + Drag Pan) --}}
    <div id="imageGalleryContainer" class="relative w-full h-full flex items-center justify-center overflow-hidden" onclick="event.stopPropagation()">
        <img id="mainGalleryImage" src="" alt="Full Screen View" class="rounded-lg shadow-2xl transition-transform duration-150 ease-out select-none" style="max-width: 90vw; max-height: 85vh; object-fit: contain; cursor: grab;">

        {{-- PDF Viewer Container --}}
        <div id="pdfGalleryViewer" class="w-full h-full pt-16 pb-4 px-4 sm:px-8 flex flex-col items-center justify-center hidden">
            <iframe id="mainGalleryPdfIframe" src="" class="w-full h-full rounded-2xl border border-white/10 shadow-2xl bg-white"></iframe>
        </div>

        {{-- Non-Image/Non-PDF Document Preview Container --}}
        <div id="docGalleryViewer" class="w-full h-full pt-16 pb-4 px-4 flex flex-col items-center justify-center hidden">
            <div class="bg-slate-900/90 backdrop-blur-xl border border-white/15 p-8 sm:p-10 rounded-3xl max-w-lg w-full text-center shadow-2xl flex flex-col items-center gap-4">
                <div id="docGalleryIconContainer" class="w-20 h-20 rounded-2xl flex items-center justify-center text-4xl shadow-inner"></div>
                <div>
                    <h3 id="docGalleryFileName" class="text-white text-base font-extrabold break-all"></h3>
                    <p id="docGalleryFileType" class="text-slate-400 text-xs font-bold uppercase mt-1 tracking-wider"></p>
                </div>
                <div class="flex items-center gap-3 w-full mt-2">
                    <a id="docGalleryOpenBtn" href="#" target="_blank" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-bold text-xs flex items-center justify-center gap-2 shadow-lg transition-all">
                        <i class="fas fa-external-link-alt text-xs"></i> Open File
                    </a>
                    <a id="docGalleryDownloadBtn" href="#" download class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-bold text-xs flex items-center justify-center gap-2 shadow-lg transition-all">
                        <i class="fas fa-download text-xs"></i> Download
                    </a>
                </div>
            </div>
        </div>

        {{-- Navigation Arrows --}}
        <button id="prevImageBtn" onclick="previousImage(); event.stopPropagation();" class="absolute left-4 top-1/2 -translate-y-1/2 bg-black/60 hover:bg-black/80 text-white w-11 h-11 rounded-full flex items-center justify-center shadow-lg transition-all border border-white/20 z-10" style="display: none;">
            <i class="fas fa-chevron-left text-sm"></i>
        </button>
        <button id="nextImageBtn" onclick="nextImage(); event.stopPropagation();" class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/60 hover:bg-black/80 text-white w-11 h-11 rounded-full flex items-center justify-center shadow-lg transition-all border border-white/20 z-10" style="display: none;">
            <i class="fas fa-chevron-right text-sm"></i>
        </button>
    </div>
</div>

<script>
(function() {
    let galleryImages = [];
    let currentImageIndex = 0;
    let imgZoomScale = 1;
    let imgTranslateX = 0;
    let imgTranslateY = 0;
    let imgRotation = 0;
    let isPanningImg = false;
    let panStartX = 0;
    let panStartY = 0;

    function getFileTypeInfo(urlOrPath) {
        if (!urlOrPath) return { isImage: false, isPdf: false, isDoc: false };
        const str = String(urlOrPath).trim();
        
        if (str.startsWith('data:image/')) return { isImage: true };
        if (str.startsWith('data:application/pdf')) return { isImage: false, isPdf: true };

        let ext = '';
        const nameMatch = str.match(/;name=([^;]+);/i);
        if (nameMatch) {
            try {
                ext = decodeURIComponent(nameMatch[1]).split('.').pop().toLowerCase();
            } catch(e) {
                ext = nameMatch[1].split('.').pop().toLowerCase();
            }
        }
        if (!ext && !str.startsWith('data:')) {
            const cleanUrl = str.split('?')[0].split('#')[0];
            ext = cleanUrl.split('.').pop().toLowerCase();
        }

        if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico', 'heic', 'avif'].includes(ext)) {
            return { isImage: true };
        }
        if (ext === 'pdf') {
            return { isImage: false, isPdf: true };
        }
        if (['doc', 'docx'].includes(ext) || str.includes('officedocument.word') || str.includes('msword')) {
            return { isImage: false, isPdf: false, isDoc: true, icon: 'fa-file-word', color: 'text-blue-400', bg: 'bg-blue-500/20', label: 'WORD DOCUMENT' };
        }
        if (['xls', 'xlsx', 'csv'].includes(ext) || str.includes('officedocument.spreadsheet') || str.includes('ms-excel') || str.includes('csv')) {
            return { isImage: false, isPdf: false, isDoc: true, icon: 'fa-file-excel', color: 'text-emerald-400', bg: 'bg-emerald-500/20', label: 'EXCEL SPREADSHEET' };
        }
        if (['zip', 'rar', '7z', 'tar', 'gz'].includes(ext) || str.includes('zip') || str.includes('compressed')) {
            return { isImage: false, isPdf: false, isDoc: true, icon: 'fa-file-archive', color: 'text-amber-400', bg: 'bg-amber-500/20', label: 'ARCHIVE' };
        }
        if (['txt', 'rtf', 'log', 'json', 'xml'].includes(ext) || str.startsWith('data:text/')) {
            return { isImage: false, isPdf: false, isDoc: true, icon: 'fa-file-alt', color: 'text-slate-400', bg: 'bg-slate-500/20', label: 'TEXT FILE' };
        }

        return { isImage: false, isPdf: false, isDoc: true, icon: 'fa-file', color: 'text-indigo-400', bg: 'bg-indigo-500/20', label: ext.toUpperCase() || 'FILE' };
    }

    window.openImageGallery = function(images, title = "Attachments") {
        if (!Array.isArray(images)) {
            images = [images];
        }
        
        galleryImages = images;
        currentImageIndex = 0;
        
        const titleEl = document.getElementById("imageGalleryTitle");
        if (titleEl) titleEl.textContent = title;

        imageGalleryResetZoom();
        renderImageGallery();
        document.getElementById("imageGalleryModal").classList.remove("hidden");
    };

    if (typeof window.openGallery === "undefined") {
        window.openGallery = window.openImageGallery;
    }

    window.closeImageGallery = function() {
        document.getElementById("imageGalleryModal").classList.add("hidden");
        imageGalleryResetZoom();
    };

    function renderImageGallery() {
        if (!galleryImages || galleryImages.length === 0) return;

        const currentImage = galleryImages[currentImageIndex];
        const fileInfo = getFileTypeInfo(currentImage);

        let extractedFilename = '';
        const nameMatch = String(currentImage).match(/;name=([^;]+);/i);
        if (nameMatch) {
            try {
                extractedFilename = decodeURIComponent(nameMatch[1]);
            } catch(e) {
                extractedFilename = nameMatch[1];
            }
        } else if (!String(currentImage).startsWith('data:')) {
            const cleanStr = String(currentImage).split('?')[0].split('#')[0];
            extractedFilename = cleanStr.split('/').pop() || '';
        }

        const mainImg = document.getElementById("mainGalleryImage");
        const pdfViewer = document.getElementById("pdfGalleryViewer");
        const pdfIframe = document.getElementById("mainGalleryPdfIframe");
        const docViewer = document.getElementById("docGalleryViewer");
        const zoomToolbar = document.getElementById("imageGalleryZoomToolbar");
        const downloadBtn = document.getElementById("imageGalleryDownloadBtn");

        if (downloadBtn) {
            downloadBtn.href = currentImage;
            if (extractedFilename) {
                downloadBtn.setAttribute('download', extractedFilename);
            } else if (fileInfo.isImage) {
                downloadBtn.setAttribute('download', 'image.jpg');
            } else if (fileInfo.isPdf) {
                downloadBtn.setAttribute('download', 'document.pdf');
            } else {
                downloadBtn.setAttribute('download', 'attachment');
            }
            downloadBtn.style.display = "flex";
        }

        imageGalleryResetZoom();

        if (fileInfo.isImage) {
            if (mainImg) {
                // If it's a data URI with ;name=...;, strip the name parameter for the img tag
                let imgSrc = currentImage;
                if (String(imgSrc).startsWith('data:image/') && /;name=[^;]+;/i.test(imgSrc)) {
                    imgSrc = imgSrc.replace(/;name=[^;]+;/i, ';');
                }
                mainImg.src = imgSrc;
                mainImg.style.display = "block";
            }
            if (pdfViewer) pdfViewer.classList.add("hidden");
            if (docViewer) docViewer.classList.add("hidden");
            if (zoomToolbar) zoomToolbar.style.display = "flex";
        } else if (fileInfo.isPdf) {
            if (mainImg) mainImg.style.display = "none";
            if (docViewer) docViewer.classList.add("hidden");
            if (pdfViewer) {
                pdfViewer.classList.remove("hidden");
                if (pdfIframe) pdfIframe.src = currentImage;
            }
            if (zoomToolbar) zoomToolbar.style.display = "none";
        } else {
            if (mainImg) mainImg.style.display = "none";
            if (pdfViewer) pdfViewer.classList.add("hidden");
            if (docViewer) {
                docViewer.classList.remove("hidden");
                const displayName = extractedFilename || (fileInfo.label ? fileInfo.label.toLowerCase() : 'Attachment');
                const nameEl = document.getElementById("docGalleryFileName");
                const typeEl = document.getElementById("docGalleryFileType");
                const iconContainer = document.getElementById("docGalleryIconContainer");
                const openBtn = document.getElementById("docGalleryOpenBtn");
                const docDownloadBtn = document.getElementById("docGalleryDownloadBtn");

                if (nameEl) nameEl.textContent = displayName;
                if (typeEl) typeEl.textContent = fileInfo.label;
                if (iconContainer) {
                    iconContainer.className = `w-20 h-20 rounded-2xl flex items-center justify-center text-4xl shadow-inner ${fileInfo.bg}`;
                    iconContainer.innerHTML = `<i class="fas ${fileInfo.icon} ${fileInfo.color}"></i>`;
                }
                if (openBtn) openBtn.href = currentImage;
                if (docDownloadBtn) {
                    docDownloadBtn.href = currentImage;
                    if (extractedFilename) docDownloadBtn.setAttribute('download', extractedFilename);
                }
            }
            if (zoomToolbar) zoomToolbar.style.display = "none";
        }

        // Update arrow visibility
        const prevBtn = document.getElementById("prevImageBtn");
        const nextBtn = document.getElementById("nextImageBtn");
        if (prevBtn) prevBtn.style.display = galleryImages.length > 1 && currentImageIndex > 0 ? "flex" : "none";
        if (nextBtn) nextBtn.style.display = galleryImages.length > 1 && currentImageIndex < galleryImages.length - 1 ? "flex" : "none";
    }

    function applyImgTransform() {
        const mainImg = document.getElementById("mainGalleryImage");
        const badge = document.getElementById("imageGalleryZoomBadge");
        if (!mainImg) return;

        mainImg.style.transform = `translate(${imgTranslateX}px, ${imgTranslateY}px) scale(${imgZoomScale}) rotate(${imgRotation}deg)`;
        mainImg.style.transition = isPanningImg ? 'none' : 'transform 0.15s ease-out';

        if (badge) badge.textContent = Math.round(imgZoomScale * 100) + '%';
        mainImg.style.cursor = imgZoomScale > 1 ? (isPanningImg ? 'grabbing' : 'grab') : 'default';
    }

    window.imageGalleryZoomIn = function() {
        imgZoomScale = Math.min(5.0, +(imgZoomScale + 0.25).toFixed(2));
        applyImgTransform();
    };

    window.imageGalleryZoomOut = function() {
        imgZoomScale = Math.max(0.5, +(imgZoomScale - 0.25).toFixed(2));
        if (imgZoomScale <= 1) {
            imgTranslateX = 0;
            imgTranslateY = 0;
        }
        applyImgTransform();
    };

    window.imageGalleryResetZoom = function() {
        imgZoomScale = 1;
        imgTranslateX = 0;
        imgTranslateY = 0;
        imgRotation = 0;
        applyImgTransform();
    };

    window.imageGalleryRotate = function() {
        imgRotation = (imgRotation + 90) % 360;
        applyImgTransform();
    };

    window.previousImage = function() {
        if (currentImageIndex > 0) {
            currentImageIndex--;
            renderImageGallery();
        }
    };

    window.nextImage = function() {
        if (currentImageIndex < galleryImages.length - 1) {
            currentImageIndex++;
            renderImageGallery();
        }
    };

    function setupImgInteractiveZoom() {
        const container = document.getElementById("imageGalleryContainer");
        if (!container) return;

        container.addEventListener("wheel", (e) => {
            const modal = document.getElementById("imageGalleryModal");
            if (!modal || modal.classList.contains("hidden")) return;

            const currentImage = galleryImages[currentImageIndex];
            if (!currentImage || !getFileTypeInfo(currentImage).isImage) return;

            e.preventDefault();
            if (e.deltaY < 0) {
                window.imageGalleryZoomIn();
            } else {
                window.imageGalleryZoomOut();
            }
        }, { passive: false });

        container.addEventListener("dblclick", () => {
            const modal = document.getElementById("imageGalleryModal");
            if (!modal || modal.classList.contains("hidden")) return;

            const currentImage = galleryImages[currentImageIndex];
            if (!currentImage || !getFileTypeInfo(currentImage).isImage) return;

            if (imgZoomScale > 1) {
                window.imageGalleryResetZoom();
            } else {
                imgZoomScale = 2.5;
                applyImgTransform();
            }
        });

        container.addEventListener("mousedown", (e) => {
            const modal = document.getElementById("imageGalleryModal");
            if (!modal || modal.classList.contains("hidden")) return;
            if (imgZoomScale <= 1) return;

            const currentImage = galleryImages[currentImageIndex];
            if (!currentImage || !getFileTypeInfo(currentImage).isImage) return;

            isPanningImg = true;
            panStartX = e.clientX - imgTranslateX;
            panStartY = e.clientY - imgTranslateY;
            applyImgTransform();
        });

        window.addEventListener("mousemove", (e) => {
            if (!isPanningImg) return;
            imgTranslateX = e.clientX - panStartX;
            imgTranslateY = e.clientY - panStartY;
            applyImgTransform();
        });

        window.addEventListener("mouseup", () => {
            if (isPanningImg) {
                isPanningImg = false;
                applyImgTransform();
            }
        });
    }

    document.addEventListener("keydown", (e) => {
        const modal = document.getElementById("imageGalleryModal");
        if (modal && !modal.classList.contains("hidden")) {
            if (e.key === "ArrowLeft") window.previousImage();
            if (e.key === "ArrowRight") window.nextImage();
            if (e.key === "Escape") window.closeImageGallery();
            if (e.key === "+" || e.key === "=") window.imageGalleryZoomIn();
            if (e.key === "-" || e.key === "_") window.imageGalleryZoomOut();
            if (e.key === "0" || e.key === "r" || e.key === "R") window.imageGalleryResetZoom();
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupImgInteractiveZoom);
    } else {
        setupImgInteractiveZoom();
    }
})();
</script>
