<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- CSRF Token for AJAX --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Internal System Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    @livewireStyles

    <style>
        body { font-family: 'Inter', sans-serif; }

        :root {
            --sidebar-width: 18rem; /* 288px */
        }

        #main-sidebar,
        .w-68 {
            width: var(--sidebar-width) !important;
            min-width: var(--sidebar-width) !important;
            max-width: var(--sidebar-width) !important;
            box-sizing: border-box !important;
        }

        @media (min-width: 1024px) {
            .lg\:ml-68 {
                margin-left: var(--sidebar-width) !important;
            }
        }

        /* Global Drag Scroll Styles */
        .global-drag-scroll.cursor-grab * { cursor: grab; }
        .global-drag-scroll.active-grabbing * { cursor: grabbing !important; }
        .global-drag-scroll.cursor-grab input,
        .global-drag-scroll.cursor-grab select,
        .global-drag-scroll.cursor-grab button,
        .global-drag-scroll.cursor-grab a,
        .global-drag-scroll.cursor-grab label,
        .global-drag-scroll.cursor-grab .cursor-pointer,
        .global-drag-scroll.active-grabbing input,
        .global-drag-scroll.active-grabbing select,
        .global-drag-scroll.active-grabbing button,
        .global-drag-scroll.active-grabbing a,
        .global-drag-scroll.active-grabbing label,
        .global-drag-scroll.active-grabbing .cursor-pointer { cursor: auto; }
        .global-drag-scroll.cursor-grab [data-action] { cursor: pointer; }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    {{-- 1. STACK FOR CUSTOM CSS (IMPORTANT) --}}
    @stack('styles')
</head>

<body class="bg-[#f8fafc] overflow-x-hidden">
    <div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden lg:hidden"></div>

    <div class="flex">
        @include('admin.partials.sidebar')
        <div class="flex-1 lg:ml-68 min-h-screen min-w-0 flex flex-col transition-all duration-300">

            <header class="lg:hidden bg-[#0a0f1d] p-4 flex items-center justify-between sticky top-0 z-30">
                <h1 class="text-white font-bold">Inventory<span class="text-blue-500">Pro</span></h1>
                <button id="sidebar-open" class="text-white text-2xl"><i class="fas fa-bars"></i></button>
            </header>

            @include('admin.partials.navbar')

            <main class="p-4 md:p-8 flex-1">
                @yield('content')
                @isset($slot) {{ $slot }} @endisset
            </main>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            const $sidebar = $('#main-sidebar');
            const $overlay = $('#sidebar-overlay');

            $('#sidebar-open').on('click', function() {
                $sidebar.removeClass('-translate-x-full').addClass('translate-x-0 z-50');
                $overlay.removeClass('hidden');
            });

            $('#sidebar-close, #sidebar-overlay').on('click', function() {
                $sidebar.addClass('-translate-x-full').removeClass('translate-x-0 z-50');
                $overlay.addClass('hidden');
            });

            // Global horizontal drag-to-scroll for tables
            document.querySelectorAll(".global-drag-scroll").forEach(slider => {
                let isDown = false;
                let startX;
                let scrollLeft;

                slider.addEventListener("mousedown", (e) => {
                    if (e.target.closest("input, select, button, a, label, .cursor-pointer")) return;
                    isDown = true;
                    slider.classList.add("active-grabbing");
                    startX = e.pageX - slider.offsetLeft;
                    scrollLeft = slider.scrollLeft;
                });
                slider.addEventListener("mouseleave", () => { isDown = false; slider.classList.remove("active-grabbing"); });
                slider.addEventListener("mouseup", () => { isDown = false; slider.classList.remove("active-grabbing"); });
                slider.addEventListener("mousemove", (e) => {
                    if (!isDown) return;
                    e.preventDefault();
                    const x = e.pageX - slider.offsetLeft;
                    const walk = (x - startX) * 1.5;
                    slider.scrollLeft = scrollLeft - walk;
                });
            });
        });
    </script>

    @livewireScripts

    {{-- 3. STACK FOR CUSTOM SCRIPTS (IMPORTANT) --}}
    @stack('scripts')
</body>
</html>