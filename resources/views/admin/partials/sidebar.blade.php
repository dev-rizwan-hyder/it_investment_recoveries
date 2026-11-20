<aside id="main-sidebar"
    class="fixed inset-y-0 left-0 w-68 bg-white text-slate-600 flex flex-col z-[20] h-screen overflow-hidden border-r border-slate-200 shadow-xl transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0"
    style="box-shadow: 10px 0 30px -15px rgba(0,0,0,0.05);">

    <div class="p-4 flex items-center justify-center border-b border-slate-100 relative" style="height: 150px;">

        <div class="absolute -top-10 -left-10 w-32 h-32 bg-blue-100/50 blur-[40px] rounded-full"></div>

        <img src="{{ asset('images/it_logo.png') }}" alt="IT Logo"
            class="relative z-10 w-[85%] max-h-[180px] object-contain transition-transform duration-300 hover:scale-105">

        <button id="sidebar-close"
            class="p-2 lg:hidden text-slate-400 hover:text-slate-900 transition-colors focus:outline-none absolute right-4 top-4">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>

    <nav id="sidebar-nav" class="flex-1 overflow-y-auto py-6 space-y-1 px-4"
        style="scrollbar-width: thin; scrollbar-color: rgba(0,0,0,0.05) transparent;">
        @php
            $menus = [
                ['icon' => 'fa-th-large', 'label' => 'Dashboard', 'route' => 'admin.dashboard'],
                ['icon' => 'fa-recycle', 'label' => 'Receiving Intake', 'route' => 'admin.recycling.intake'],
                ['icon' => 'fa-microchip', 'label' => 'Processing Intake', 'route' => 'admin.processing-intake.index'],
                ['icon' => 'fa-boxes', 'label' => 'Items / Lot/ Repair Intake', 'route' => 'admin.items-lot.intake'],
                ['icon' => 'fa-box-open', 'label' => 'Refurb / E-commerce Intake', 'route' => 'admin.ebay-products.index'],
                ['icon' => 'fa-eraser', 'label' => 'Data Destruction Intake', 'route' => 'admin.data-destruction-entries.index'],
                ['icon' => 'fa-desktop', 'label' => 'IT Assets Intake', 'route' => 'admin.it-assets-entries.index'],
                ['icon' => 'fa-wrench', 'label' => 'Repair Intake', 'route' => 'admin.repair-intake.index'],
                ['icon' => 'fa-dumpster', 'label' => 'Universal Waste Intake', 'route' => 'admin.universal-waste-entries.index'],
                ['icon' => 'fa-warehouse', 'label' => 'Inventory', 'route' => 'admin.inventory.index'],
                [
                    'icon' => 'fa-sitemap',
                    'label' => 'Categories',
                    'is_dropdown' => true,
                    'submenu' => [
                        ['label' => 'Category Management', 'route' => 'admin.categories.index'],                    ],
                ],
                [
                    'icon' => 'fa-tags',
                    'label' => 'Invoices',
                    'is_dropdown' => true,
                    'submenu' => [
                        ['label' => 'All Invoices', 'route' => 'admin.sales.invoices'],
                        ['label' => 'Create Invoice', 'route' => 'admin.sales.invoices.create'],
                    ],
                ],
                ['icon' => 'fa-shopping-cart', 'label' => 'Orders', 'route' => 'admin.orders.index'],
                ['icon' => 'fa-file-alt', 'label' => 'Reports', 'route' => 'admin.report.index'],

                // HR Management - Submenu setup
                [
                    'icon' => 'fa-user-shield',
                    'label' => 'HR Management',
                    'is_dropdown' => true,
                    'submenu' => [
                        ['label' => 'Employees', 'route' => 'admin.employees.index'],
                        ['label' => 'Attendance Sheet', 'route' => 'admin.attendance'],
                        ['label' => 'Attendance Detail', 'route' => 'admin.attendance.list'],
                        ['label' => 'Departments', 'route' => 'admin.departments.index'],
                    ],
                ],

                // Policies - Dropdown setup b/w HR and Time Tracking
                [
                    'icon' => 'fa-file-contract',
                    'label' => 'Policies',
                    'is_dropdown' => true,
                    'submenu' => [
                        ['label' => 'Company Policy', 'route' => 'admin.policies.index', 'params' => ['type' => 'company']],
                        ['label' => 'Company Procedure', 'route' => 'admin.policies.index', 'params' => ['type' => 'procedure']],
                        ['label' => 'Company Policy & Procedure', 'route' => 'admin.policies.index', 'params' => ['type' => 'policy_procedure']],
                    ],
                ],

                ['icon' => 'fa-clock', 'label' => 'Time Tracking', 'route' => 'admin.time-tracking'],
                ['icon' => 'fa-users', 'label' => 'Clients', 'route' => 'admin.clients'],
                ['icon' => 'fa-wallet', 'label' => 'Expenses', 'route' => 'admin.expenses'],
                ['icon' => 'fa-trash-alt', 'label' => 'Trash', 'route' => 'admin.trash.index'],
                ['icon' => 'fa-cog', 'label' => 'Settings', 'route' => 'admin.settings'],
            ];
        @endphp

        @foreach ($menus as $menu)
    @if (isset($menu['is_dropdown']) && $menu['is_dropdown'])
        {{-- Dropdown Menu Logic --}}
        @php 
            $isSubActive = false;
            foreach($menu['submenu'] as $sub) {
                $itemActive = request()->routeIs($sub['route']);
                if ($itemActive && isset($sub['params']['type'])) {
                    $itemActive = request('type') === $sub['params']['type'];
                }
                if ($itemActive) $isSubActive = true;
            }
        @endphp
        <div x-data="{ open: {{ $isSubActive ? 'true' : 'false' }} }" class="space-y-1">
            {{-- Parent Item --}}
            <button @click="open = !open"
                @if ($isSubActive) data-sidebar-active-parent="true" @endif
                class="w-full group flex items-center px-4 py-3 rounded-xl transition-all duration-200 
                {{ $isSubActive ? 'bg-slate-50 text-slate-900' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }} font-semibold">
                
                <div class="w-8 flex items-center justify-center">
                    <i class="fas {{ $menu['icon'] }} text-base {{ $isSubActive ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500' }}"></i>
                </div>
                
                <span class="ml-3 text-[13.5px] tracking-wide flex-1 text-left">{{ $menu['label'] }}</span>
                
                <i class="fas fa-chevron-right text-[10px] transition-transform duration-300 transform" 
                   :class="open ? 'rotate-90' : ''"></i>
            </button>

            {{-- Submenu Container --}}
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-cloak 
                 class="relative ml-8 border-l-2 border-slate-100 space-y-1 my-1">
                
                @foreach ($menu['submenu'] as $sub)
                    @php 
                        $subActive = request()->routeIs($sub['route']);
                        if ($subActive && isset($sub['params']['type'])) {
                            $subActive = request('type') === $sub['params']['type'];
                        }
                    @endphp
                    <a href="{{ route($sub['route'], $sub['params'] ?? []) }}"
                        @if ($subActive) data-sidebar-active="true" @endif
                        class="group flex items-center py-2 px-4 text-[13px] transition-all duration-200 relative
                        {{ $subActive ? 'text-blue-600 font-bold' : 'text-slate-400 hover:text-slate-800' }}">
                        
                        {{-- Dot Indicator --}}
                        <span class="absolute -left-[1.35rem] w-2 h-2 rounded-full border-2 border-white 
                             {{ $subActive ? 'bg-blue-600' : 'bg-slate-200 group-hover:bg-slate-400' }}">
                        </span>
                        
                        {{ $sub['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    @else
        {{-- Normal Link Logic --}}
        @php $isActive = request()->routeIs($menu['route']); @endphp
        <a href="{{ route($menu['route']) }}"
            @if ($isActive) data-sidebar-active="true" @endif
            class="group relative flex items-center px-4 py-3 rounded-xl transition-all duration-200 ease-out 
           {{ $isActive
               ? 'bg-blue-50 text-blue-600 shadow-sm font-bold'
               : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900 hover:translate-x-1 font-semibold' }}">

            <div class="w-8 flex items-center justify-center">
                <i class="fas {{ $menu['icon'] }} text-base transition-transform duration-300 group-hover:scale-110 
                   {{ $isActive ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-500' }}"></i>
            </div>

            <span class="ml-3 text-[13.5px] tracking-wide">{{ $menu['label'] }}</span>

            @if ($isActive)
                <div class="absolute left-0 w-1.5 h-6 bg-blue-600 rounded-r-full"></div>
            @endif
        </a>
    @endif
@endforeach
    </nav>
    <script>
        (() => {
            const scrollActiveSidebarItem = () => {
                const sidebarNav = document.getElementById('sidebar-nav');
                const activeItem = sidebarNav?.querySelector('[data-sidebar-active="true"]')
                    || sidebarNav?.querySelector('[data-sidebar-active-parent="true"]');

                if (!sidebarNav || !activeItem) {
                    return;
                }

                requestAnimationFrame(() => {
                    const navRect = sidebarNav.getBoundingClientRect();
                    const itemRect = activeItem.getBoundingClientRect();
                    const offset = itemRect.top - navRect.top - (navRect.height / 2) + (itemRect.height / 2);

                    sidebarNav.scrollTo({
                        top: Math.max(0, sidebarNav.scrollTop + offset),
                        behavior: 'auto',
                    });
                });
            };

            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(scrollActiveSidebarItem, 75);
                setTimeout(scrollActiveSidebarItem, 275);
            });
            document.addEventListener('alpine:initialized', () => {
                setTimeout(scrollActiveSidebarItem, 75);
            });
            window.addEventListener('load', () => {
                setTimeout(scrollActiveSidebarItem, 75);
            }, { once: true });
        })();
    </script>
    <div class="p-4 mt-auto border-t border-slate-100 bg-slate-50/50">
        <div class="p-3 rounded-2xl bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3 mb-3">
                <div class="relative flex-shrink-0">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()?->name ?? 'User') }}&background=2563eb&color=fff"
                        class="w-9 h-9 rounded-xl border border-slate-100" alt="avatar">
                    <span
                        class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></span>
                </div>
                <div class="flex flex-col overflow-hidden">
                    <p class="text-slate-900 font-bold text-[12px] truncate">{{ Auth::user()?->name ?? 'User' }}</p>
                    <p class="text-[9px] text-blue-600 font-black tracking-widest mt-0.5 uppercase">
                        {{ Auth::user()?->role ?? 'ADMIN' }}</p>
                </div>
            </div>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                class="flex items-center justify-center w-full py-2.5 rounded-lg bg-red-50 text-red-500 text-[11px] font-bold hover:bg-red-500 hover:text-white transition-all duration-300 group">
                <i class="fas fa-sign-out-alt mr-2"></i> LOGOUT
            </a>
        </div>
    </div>
</aside>
