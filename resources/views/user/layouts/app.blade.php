<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Customer Portal') | IT Investment Recoveries</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS & FontAwesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .portal-glass {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .portal-card {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.03), 0 2px 6px -1px rgba(15, 23, 42, 0.02);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .portal-card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.08), 0 4px 12px -2px rgba(15, 23, 42, 0.04);
            border-color: rgba(59, 130, 246, 0.3);
        }

        .sidebar-active {
            background-color: #eff6ff !important;
            border-left: 4px solid #2563eb !important;
            color: #1d4ed8 !important;
            font-weight: 700 !important;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f8fafc;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-full bg-slate-50 font-sans text-slate-900 antialiased flex flex-col selection:bg-blue-500 selection:text-white">
    <!-- Header -->
    @include('user.components.header')

    <!-- Mobile Navigation Overlay -->
    <div id="sidebarOverlay" class="hidden fixed inset-0 z-30 bg-slate-950/40 backdrop-blur-sm lg:hidden transition-opacity duration-300"></div>

    <!-- Main Container -->
    <div class="flex flex-1 min-h-[calc(100vh-5rem)]">
        <!-- Sidebar Navigation -->
        @include('user.components.sidebar')

        <!-- Content Area -->
        <main class="min-w-0 flex-1 bg-slate-50/70 pb-16">
            @yield('content')
        </main>
    </div>

    <!-- Toast Notification Container -->
    <div id="toastContainer" class="fixed bottom-5 right-5 z-50 flex flex-col gap-3 max-w-sm pointer-events-none"></div>

    <!-- Global Image Lightbox Modal -->
    <div id="imageLightboxModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/90 backdrop-blur-md p-4 transition-opacity duration-300">
        <div class="relative max-w-5xl w-full max-h-[90vh] flex flex-col items-center justify-center">
            <!-- Top Toolbar -->
            <div class="w-full flex items-center justify-between pb-3 text-white">
                <span id="lightboxTitle" class="text-sm font-extrabold truncate max-w-md">Image Preview</span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="window.openImageInNewTab()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-bold text-white transition border border-white/20">
                        <i class="fa-solid fa-up-right-from-square text-xs"></i> Open New Tab
                    </button>
                    <button type="button" onclick="window.closeImageModal()" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 text-white text-base font-bold transition border border-white/20">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
            <!-- Image Container -->
            <div class="relative overflow-hidden rounded-2xl border border-white/10 bg-black/60 flex items-center justify-center max-h-[80vh] w-full p-2">
                <img id="lightboxImage" src="" alt="Full view" class="max-h-[78vh] max-w-full object-contain rounded-lg transition-transform duration-200">
            </div>
        </div>
    </div>

    <script>
        // Sidebar Toggle Handler
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        const setSidebarOpen = (isOpen) => {
            if (!sidebar) return;
            if (isOpen) {
                sidebar.classList.remove('hidden');
                sidebar.classList.add('flex');
            } else {
                sidebar.classList.add('hidden');
                sidebar.classList.remove('flex');
            }
            sidebarOverlay?.classList.toggle('hidden', !isOpen);
            sidebarToggle?.setAttribute('aria-expanded', String(isOpen));
        };

        sidebarToggle?.addEventListener('click', () => {
            const isHidden = sidebar?.classList.contains('hidden');
            setSidebarOpen(isHidden);
        });

        sidebarOverlay?.addEventListener('click', () => setSidebarOpen(false));

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                sidebarOverlay?.classList.add('hidden');
                if (sidebar) {
                    sidebar.classList.remove('hidden');
                    sidebar.classList.add('flex');
                }
            }
        });

        // Global Toast Notification Helper
        window.showToast = (message, type = 'info') => {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            const bgClass = type === 'success' ? 'bg-emerald-900 border-emerald-700 text-emerald-100' :
                           type === 'error' ? 'bg-rose-900 border-rose-700 text-rose-100' :
                           'bg-slate-900 border-slate-700 text-slate-100';
            const icon = type === 'success' ? 'fa-circle-check text-emerald-400' :
                         type === 'error' ? 'fa-triangle-exclamation text-rose-400' :
                         'fa-circle-info text-blue-400';

            toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-2xl border shadow-xl text-sm font-semibold transition-all transform translate-y-2 opacity-0 ${bgClass}`;
            toast.innerHTML = `<i class="fa-solid ${icon} text-lg shrink-0"></i> <span>${message}</span>`;

            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            }, 10);

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        };

        // Clipboard Copy Helper
        window.copyToClipboard = (text, label = 'Item') => {
            navigator.clipboard.writeText(text).then(() => {
                window.showToast(`${label} copied to clipboard!`, 'success');
            }).catch(() => {
                window.showToast('Failed to copy', 'error');
            });
        };

        // Lightbox Modal Helper
        let currentLightboxSrc = '';
        let currentLightboxTitle = '';

        window.openImageModal = (src, title = 'Photo Preview') => {
            currentLightboxSrc = src;
            currentLightboxTitle = title;
            const modal = document.getElementById('imageLightboxModal');
            const img = document.getElementById('lightboxImage');
            const titleEl = document.getElementById('lightboxTitle');
            if (!modal || !img) return;
            
            img.src = src;
            if (titleEl) titleEl.textContent = title;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        };

        window.closeImageModal = () => {
            const modal = document.getElementById('imageLightboxModal');
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        };

        window.openImageInNewTab = () => {
            if (!currentLightboxSrc) return;
            
            if (currentLightboxSrc.startsWith('data:')) {
                fetch(currentLightboxSrc)
                    .then(res => res.blob())
                    .then(blob => {
                        const blobUrl = URL.createObjectURL(blob);
                        window.open(blobUrl, '_blank');
                    })
                    .catch(() => {
                        const popup = window.open('', '_blank');
                        if (popup) {
                            popup.document.write(`<html><head><title>${currentLightboxTitle || 'Image Preview'}</title></head><body style="margin:0;background:#0f172a;display:flex;justify-content:center;align-items:center;min-height:100vh;"><img src="${currentLightboxSrc}" style="max-width:100%;max-height:100vh;object-fit:contain;"></body></html>`);
                            popup.document.close();
                        }
                    });
            } else {
                window.open(currentLightboxSrc, '_blank');
            }
        };

        document.getElementById('imageLightboxModal')?.addEventListener('click', (e) => {
            if (e.target.id === 'imageLightboxModal') {
                window.closeImageModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                window.closeImageModal();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>

