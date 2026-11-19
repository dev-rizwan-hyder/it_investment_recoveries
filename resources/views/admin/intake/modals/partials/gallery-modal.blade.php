{{-- Gallery Modal for viewing multiple images & files with Zoom & Pan --}}
<div id="galleryModal" class="hidden fixed inset-0 z-[120] bg-black/80 backdrop-blur-md flex items-center justify-center p-2 sm:p-4 animate-in fade-in duration-300">
    <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full max-h-[95vh] overflow-hidden flex flex-col animate-in zoom-in-95 duration-300">
        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between p-4 sm:p-5 border-b border-slate-200 bg-slate-50 gap-3">
            <div>
                <h2 class="text-lg sm:text-xl font-black text-slate-900">Attachment Gallery</h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5"><span id="galleryImageNumber" class="font-bold text-slate-800">1</span> of <span id="galleryTotalImages" class="font-bold text-slate-800">0</span></p>
            </div>

            {{-- Zoom & Rotate Toolbar --}}
            <div id="galleryZoomToolbar" class="flex items-center gap-1.5 bg-slate-200/70 p-1.5 rounded-xl border border-slate-300/50">
                <button type="button" onclick="galleryZoomOut()" title="Zoom Out (-)" class="h-8 w-8 rounded-lg bg-white hover:bg-slate-100 text-slate-700 font-bold flex items-center justify-center shadow-sm transition-all active:scale-95">
                    <i class="fas fa-search-minus text-xs"></i>
                </button>

                <span id="galleryZoomBadge" class="px-2.5 py-1 text-xs font-mono font-bold text-slate-800 bg-white rounded-lg shadow-sm min-w-[50px] text-center select-none">100%</span>

                <button type="button" onclick="galleryZoomIn()" title="Zoom In (+)" class="h-8 w-8 rounded-lg bg-white hover:bg-slate-100 text-slate-700 font-bold flex items-center justify-center shadow-sm transition-all active:scale-95">
                    <i class="fas fa-search-plus text-xs"></i>
                </button>

                <button type="button" onclick="galleryResetZoom()" title="Reset Zoom & Position" class="h-8 px-2.5 rounded-lg bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all active:scale-95">
                    <i class="fas fa-compress text-xs"></i>
                    <span class="hidden sm:inline">Reset</span>
                </button>

                <button type="button" onclick="galleryRotate()" title="Rotate 90° Clockwise" class="h-8 px-2.5 rounded-lg bg-white hover:bg-slate-100 text-blue-600 font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all active:scale-95">
                    <i class="fas fa-redo text-xs"></i>
                    <span class="hidden sm:inline">Rotate</span>
                </button>
            </div>

            <button type="button" onclick="closeGalleryModal()" class="h-9 w-9 rounded-full bg-slate-200/80 hover:bg-slate-300 text-slate-600 flex items-center justify-center transition-all">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        {{-- Image & File Display Area --}}
        <div id="galleryImageContainer" class="flex-1 bg-slate-950 relative overflow-hidden flex items-center justify-center select-none" style="height: 520px; max-height: 60vh;">
            <img id="galleryMainImage" src="" alt="Gallery Image" class="max-w-full max-h-full object-contain rounded-md shadow-2xl select-none" style="transform-origin: center center; cursor: grab; user-select: none; -webkit-user-drag: none;">

            {{-- PDF Viewer Container --}}
            <div id="galleryPdfViewer" class="w-full h-full p-4 sm:p-6 flex flex-col items-center justify-center hidden">
                <iframe id="galleryPdfIframe" src="" class="w-full h-full rounded-xl border border-slate-700 shadow-2xl bg-white"></iframe>
            </div>

            {{-- Non-Image/Non-PDF Document Preview Container --}}
            <div id="galleryDocViewer" class="w-full h-full p-6 flex flex-col items-center justify-center hidden">
                <div class="bg-slate-900/90 backdrop-blur-xl border border-slate-700/60 p-8 rounded-3xl max-w-lg w-full text-center shadow-2xl flex flex-col items-center gap-4">
                    <div id="galleryDocIconContainer" class="w-20 h-20 rounded-2xl flex items-center justify-center text-4xl shadow-inner"></div>
                    <div>
                        <h3 id="galleryDocFileName" class="text-white text-base font-extrabold break-all"></h3>
                        <p id="galleryDocFileType" class="text-slate-400 text-xs font-bold uppercase mt-1 tracking-wider"></p>
                    </div>
                    <div class="flex items-center gap-3 w-full mt-2">
                        <a id="galleryDocOpenBtn" href="#" target="_blank" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-bold text-xs flex items-center justify-center gap-2 shadow-lg transition-all">
                            <i class="fas fa-external-link-alt text-xs"></i> Open File
                        </a>
                        <a id="galleryDocDownloadBtn" href="#" download class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-bold text-xs flex items-center justify-center gap-2 shadow-lg transition-all">
                            <i class="fas fa-download text-xs"></i> Download
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Navigation & Thumbnails --}}
        <div class="flex items-center justify-between p-4 sm:p-5 border-t border-slate-200 bg-slate-50 gap-2">
            <button type="button" onclick="galleryPrev()" class="px-4 py-2.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-xl font-bold text-xs flex items-center shadow-sm transition-all active:scale-95">
                <i class="fas fa-chevron-left mr-2 text-xs"></i> Previous
            </button>

            {{-- Thumbnails Scroll --}}
            <div class="flex-1 mx-2 overflow-x-auto">
                <div id="galleryThumbnails" class="flex gap-2 py-1 justify-center">
                    {{-- Thumbnails injected dynamically --}}
                </div>
            </div>

            <button type="button" onclick="galleryNext()" class="px-4 py-2.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-xl font-bold text-xs flex items-center shadow-sm transition-all active:scale-95">
                Next <i class="fas fa-chevron-right ml-2 text-xs"></i>
            </button>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between px-5 py-3 bg-slate-100 border-t border-slate-200">
            <a id="galleryDownloadBtn" href="#" target="_blank" download class="text-blue-600 hover:text-blue-800 font-extrabold text-xs flex items-center gap-1.5 transition-colors">
                <i class="fas fa-download text-xs"></i> Download Original File
            </a>
            <button type="button" onclick="closeGalleryModal()" class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl font-bold text-xs transition-all">
                Close
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    let currentGalleryImages = [];
    let currentGalleryIndex = 0;
    let galleryZoomScale = 1;
    let galleryTranslateX = 0;
    let galleryTranslateY = 0;
    let galleryRotation = 0;
    let isPanning = false;
    let startX = 0;
    let startY = 0;

    function getFileTypeInfo(urlOrPath) {
        if (!urlOrPath) return { isImage: false, isPdf: false, isDoc: false };
        const str = String(urlOrPath).trim();
        
        if (str.startsWith('data:image/')) return { isImage: true };
        if (str.startsWith('data:application/pdf')) return { isImage: false, isPdf: true };
        if (str.startsWith('data:')) return { isImage: false, isPdf: false, isDoc: true, icon: 'fa-file-alt', color: 'text-indigo-400', bg: 'bg-indigo-500/20', label: 'ATTACHMENT' };

        const cleanUrl = str.split('?')[0].split('#')[0];
        const ext = cleanUrl.split('.').pop().toLowerCase();

        if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico', 'heic', 'avif'].includes(ext)) {
            return { isImage: true };
        }
        if (ext === 'pdf') {
            return { isImage: false, isPdf: true };
        }
        if (['doc', 'docx'].includes(ext)) {
            return { isImage: false, isPdf: false, isDoc: true, icon: 'fa-file-word', color: 'text-blue-400', bg: 'bg-blue-500/20', label: 'WORD DOCUMENT' };
        }
        if (['xls', 'xlsx', 'csv'].includes(ext)) {
            return { isImage: false, isPdf: false, isDoc: true, icon: 'fa-file-excel', color: 'text-emerald-400', bg: 'bg-emerald-500/20', label: 'EXCEL SPREADSHEET' };
        }
        if (['zip', 'rar', '7z', 'tar', 'gz'].includes(ext)) {
            return { isImage: false, isPdf: false, isDoc: true, icon: 'fa-file-archive', color: 'text-amber-400', bg: 'bg-amber-500/20', label: 'ARCHIVE' };
        }
        if (['txt', 'rtf', 'log', 'json', 'xml'].includes(ext)) {
            return { isImage: false, isPdf: false, isDoc: true, icon: 'fa-file-alt', color: 'text-slate-400', bg: 'bg-slate-500/20', label: 'TEXT FILE' };
        }

        return { isImage: false, isPdf: false, isDoc: true, icon: 'fa-file', color: 'text-indigo-400', bg: 'bg-indigo-500/20', label: ext.toUpperCase() || 'FILE' };
    }

    window.openGallery = function(photos, startIndex = 0) {
        if (typeof photos === 'string') {
            try { photos = JSON.parse(photos); } catch(e) { photos = [photos]; }
        }
        if (!Array.isArray(photos)) {
            photos = [photos];
        }
        
        currentGalleryImages = photos;
        currentGalleryIndex = (startIndex >= 0 && startIndex < photos.length) ? startIndex : 0;
        
        const modal = document.getElementById('galleryModal');
        if (modal) {
            modal.classList.remove('hidden');
            window.galleryResetZoom();
            renderGallery();
        }
    };

    window.closeGalleryModal = function() {
        const modal = document.getElementById('galleryModal');
        if (modal) {
            modal.classList.add('hidden');
            window.galleryResetZoom();
        }
    };

    function renderGallery() {
        if (!currentGalleryImages || currentGalleryImages.length === 0) return;
        
        const rawImage = currentGalleryImages[currentGalleryIndex];
        let imageUrl = rawImage;
        if (typeof rawImage === 'object' && rawImage !== null) {
            imageUrl = rawImage.url || rawImage.src || rawImage.filename || '';
        }
        if (imageUrl && !imageUrl.startsWith('http') && !imageUrl.startsWith('/') && !imageUrl.startsWith('data:')) {
            imageUrl = '{{ asset("") }}' + imageUrl;
        }

        const fileInfo = getFileTypeInfo(imageUrl);
        const mainImg = document.getElementById('galleryMainImage');
        const pdfViewer = document.getElementById('galleryPdfViewer');
        const pdfIframe = document.getElementById('galleryPdfIframe');
        const docViewer = document.getElementById('galleryDocViewer');
        const zoomToolbar = document.getElementById('galleryZoomToolbar');

        const numEl = document.getElementById('galleryImageNumber');
        const totEl = document.getElementById('galleryTotalImages');
        const downBtn = document.getElementById('galleryDownloadBtn');

        if (numEl) numEl.textContent = currentGalleryIndex + 1;
        if (totEl) totEl.textContent = currentGalleryImages.length;
        if (downBtn) downBtn.href = imageUrl;
        
        window.galleryResetZoom();

        if (fileInfo.isImage) {
            if (mainImg) {
                mainImg.src = imageUrl;
                mainImg.style.display = 'block';
            }
            if (pdfViewer) pdfViewer.classList.add('hidden');
            if (docViewer) docViewer.classList.add('hidden');
            if (zoomToolbar) zoomToolbar.style.display = 'flex';
        } else if (fileInfo.isPdf) {
            if (mainImg) mainImg.style.display = 'none';
            if (docViewer) docViewer.classList.add('hidden');
            if (pdfViewer) {
                pdfViewer.classList.remove('hidden');
                if (pdfIframe) pdfIframe.src = imageUrl;
            }
            if (zoomToolbar) zoomToolbar.style.display = 'none';
        } else {
            if (mainImg) mainImg.style.display = 'none';
            if (pdfViewer) pdfViewer.classList.add('hidden');
            if (docViewer) {
                docViewer.classList.remove('hidden');
                const cleanStr = String(imageUrl).split('?')[0].split('#')[0];
                const filename = cleanStr.split('/').pop() || 'Attachment';
                const nameEl = document.getElementById('galleryDocFileName');
                const typeEl = document.getElementById('galleryDocFileType');
                const iconContainer = document.getElementById('galleryDocIconContainer');
                const openBtn = document.getElementById('galleryDocOpenBtn');
                const docDownloadBtn = document.getElementById('galleryDocDownloadBtn');

                if (nameEl) nameEl.textContent = decodeURIComponent(filename);
                if (typeEl) typeEl.textContent = fileInfo.label;
                if (iconContainer) {
                    iconContainer.className = `w-20 h-20 rounded-2xl flex items-center justify-center text-4xl shadow-inner ${fileInfo.bg}`;
                    iconContainer.innerHTML = `<i class="fas ${fileInfo.icon} ${fileInfo.color}"></i>`;
                }
                if (openBtn) openBtn.href = imageUrl;
                if (docDownloadBtn) docDownloadBtn.href = imageUrl;
            }
            if (zoomToolbar) zoomToolbar.style.display = 'none';
        }

        // Render Thumbnails
        const thumbnailsContainer = document.getElementById('galleryThumbnails');
        if (thumbnailsContainer) {
            thumbnailsContainer.innerHTML = '';
            currentGalleryImages.forEach((photo, index) => {
                let thumbUrl = photo;
                if (typeof photo === 'object' && photo !== null) {
                    thumbUrl = photo.url || photo.src || photo.filename || '';
                }
                if (thumbUrl && !thumbUrl.startsWith('http') && !thumbUrl.startsWith('/') && !thumbUrl.startsWith('data:')) {
                    thumbUrl = '{{ asset("") }}' + thumbUrl;
                }

                const thumbInfo = getFileTypeInfo(thumbUrl);
                const isSelected = index === currentGalleryIndex;
                const borderClass = isSelected ? 'border-blue-600 ring-2 ring-blue-500 scale-105' : 'border-slate-300 opacity-70 hover:opacity-100 hover:border-blue-400';

                if (thumbInfo.isImage) {
                    const thumb = document.createElement('img');
                    thumb.src = thumbUrl;
                    thumb.alt = `Thumbnail ${index + 1}`;
                    thumb.className = `w-14 h-14 rounded-lg cursor-pointer border-2 object-cover transition-all flex-shrink-0 ${borderClass}`;
                    thumb.onclick = () => {
                        currentGalleryIndex = index;
                        renderGallery();
                    };
                    thumbnailsContainer.appendChild(thumb);
                } else {
                    const thumbDiv = document.createElement('div');
                    thumbDiv.className = `w-14 h-14 rounded-lg cursor-pointer border-2 bg-slate-900 flex flex-col items-center justify-center transition-all flex-shrink-0 ${borderClass}`;
                    let iconClass = 'fa-file-pdf';
                    let iconColor = 'text-red-400';
                    if (thumbInfo.isDoc) {
                        iconClass = thumbInfo.icon;
                        iconColor = thumbInfo.color;
                    }
                    thumbDiv.innerHTML = `<i class="fas ${iconClass} ${iconColor} text-lg"></i><span class="text-[7px] font-bold text-slate-300 uppercase mt-0.5">${thumbInfo.isPdf ? 'PDF' : 'DOC'}</span>`;
                    thumbDiv.onclick = () => {
                        currentGalleryIndex = index;
                        renderGallery();
                    };
                    thumbnailsContainer.appendChild(thumbDiv);
                }
            });
        }
    }

    function applyGalleryTransform() {
        const mainImg = document.getElementById('galleryMainImage');
        const badge = document.getElementById('galleryZoomBadge');
        if (!mainImg) return;
        
        mainImg.style.transform = `translate(${galleryTranslateX}px, ${galleryTranslateY}px) scale(${galleryZoomScale}) rotate(${galleryRotation}deg)`;
        mainImg.style.transition = isPanning ? 'none' : 'transform 0.15s ease-out';
        
        if (badge) {
            badge.textContent = Math.round(galleryZoomScale * 100) + '%';
        }
        
        if (galleryZoomScale > 1) {
            mainImg.style.cursor = isPanning ? 'grabbing' : 'grab';
        } else {
            mainImg.style.cursor = 'default';
        }
    }

    window.galleryZoomIn = function() {
        galleryZoomScale = Math.min(5.0, +(galleryZoomScale + 0.25).toFixed(2));
        applyGalleryTransform();
    };

    window.galleryZoomOut = function() {
        galleryZoomScale = Math.max(0.5, +(galleryZoomScale - 0.25).toFixed(2));
        if (galleryZoomScale <= 1) {
            galleryTranslateX = 0;
            galleryTranslateY = 0;
        }
        applyGalleryTransform();
    };

    window.galleryResetZoom = function() {
        galleryZoomScale = 1;
        galleryTranslateX = 0;
        galleryTranslateY = 0;
        galleryRotation = 0;
        applyGalleryTransform();
    };

    window.galleryRotate = function() {
        galleryRotation = (galleryRotation + 90) % 360;
        applyGalleryTransform();
    };

    window.galleryNext = function() {
        if (!currentGalleryImages || currentGalleryImages.length === 0) return;
        currentGalleryIndex = (currentGalleryIndex + 1) % currentGalleryImages.length;
        renderGallery();
    };

    window.galleryPrev = function() {
        if (!currentGalleryImages || currentGalleryImages.length === 0) return;
        currentGalleryIndex = (currentGalleryIndex - 1 + currentGalleryImages.length) % currentGalleryImages.length;
        renderGallery();
    };

    // Attach Wheel, Mouse Drag & Keyboard Handlers
    function setupInteractiveZoom() {
        const container = document.getElementById('galleryImageContainer');
        const mainImg = document.getElementById('galleryMainImage');
        if (!container || !mainImg) return;

        // Wheel Zoom
        container.addEventListener('wheel', (e) => {
            const modal = document.getElementById('galleryModal');
            if (!modal || modal.classList.contains('hidden')) return;

            const rawImage = currentGalleryImages[currentGalleryIndex];
            if (!rawImage || !getFileTypeInfo(rawImage).isImage) return;

            e.preventDefault();
            if (e.deltaY < 0) {
                window.galleryZoomIn();
            } else {
                window.galleryZoomOut();
            }
        }, { passive: false });

        // Double Click to Toggle Zoom
        container.addEventListener('dblclick', () => {
            const modal = document.getElementById('galleryModal');
            if (!modal || modal.classList.contains('hidden')) return;

            const rawImage = currentGalleryImages[currentGalleryIndex];
            if (!rawImage || !getFileTypeInfo(rawImage).isImage) return;

            if (galleryZoomScale > 1) {
                window.galleryResetZoom();
            } else {
                galleryZoomScale = 2.5;
                applyGalleryTransform();
            }
        });

        // Mouse Drag / Pan
        container.addEventListener('mousedown', (e) => {
            const modal = document.getElementById('galleryModal');
            if (!modal || modal.classList.contains('hidden')) return;
            if (galleryZoomScale <= 1) return;

            const rawImage = currentGalleryImages[currentGalleryIndex];
            if (!rawImage || !getFileTypeInfo(rawImage).isImage) return;

            isPanning = true;
            startX = e.clientX - galleryTranslateX;
            startY = e.clientY - galleryTranslateY;
            applyGalleryTransform();
        });

        window.addEventListener('mousemove', (e) => {
            if (!isPanning) return;
            galleryTranslateX = e.clientX - startX;
            galleryTranslateY = e.clientY - startY;
            applyGalleryTransform();
        });

        window.addEventListener('mouseup', () => {
            if (isPanning) {
                isPanning = false;
                applyGalleryTransform();
            }
        });
    }

    // Keyboard Shortcuts
    document.addEventListener('keydown', (e) => {
        const modal = document.getElementById('galleryModal');
        if (!modal || modal.classList.contains('hidden')) return;

        if (e.key === 'ArrowRight') window.galleryNext();
        if (e.key === 'ArrowLeft') window.galleryPrev();
        if (e.key === 'Escape') window.closeGalleryModal();
        if (e.key === '+' || e.key === '=') window.galleryZoomIn();
        if (e.key === '-' || e.key === '_') window.galleryZoomOut();
        if (e.key === '0' || e.key === 'r' || e.key === 'R') window.galleryResetZoom();
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupInteractiveZoom);
    } else {
        setupInteractiveZoom();
    }
})();
</script>
