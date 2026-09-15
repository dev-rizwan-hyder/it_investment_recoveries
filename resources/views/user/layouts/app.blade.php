<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'User Panel') | IT Investment</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .sidebar-active {
            background-color: #eff6ff;
            border-left: 4px solid #2563eb;
            color: #2563eb;
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900">
    @include('user.components.header')

    <div id="sidebarOverlay" class="hidden fixed inset-0 z-30 bg-slate-900/50 backdrop-blur-sm lg:hidden"></div>

    <div class="flex">
        @include('user.components.sidebar')

        <main class="min-w-0 flex-1">
            @yield('content')
        </main>
    </div>

    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        const setSidebarOpen = (isOpen) => {
            sidebar?.classList.toggle('hidden', !isOpen);
            sidebarOverlay?.classList.toggle('hidden', !isOpen);
            sidebarToggle?.setAttribute('aria-expanded', String(isOpen));
            document.body.classList.toggle('overflow-hidden', isOpen);
        };

        sidebarToggle?.addEventListener('click', () => {
            setSidebarOpen(sidebar?.classList.contains('hidden'));
        });

        sidebarOverlay?.addEventListener('click', () => setSidebarOpen(false));

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                setSidebarOpen(false);
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
