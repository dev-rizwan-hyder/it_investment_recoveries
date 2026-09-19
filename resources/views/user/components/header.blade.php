<header class="h-20 portal-glass border-b border-slate-200/70 flex items-center justify-between px-4 md:px-8 sticky top-0 z-30 transition-all duration-300">
    <div class="flex items-center gap-4 md:gap-6">
        <button
            id="sidebarToggle"
            type="button"
            class="lg:hidden inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:border-blue-300 hover:bg-blue-50/50 hover:text-blue-600 focus:outline-none"
            aria-label="Toggle navigation menu"
            aria-controls="sidebar"
            aria-expanded="false">
            <i class="fa-solid fa-bars text-base"></i>
        </button>

        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-500/20">
                <i class="fa-solid fa-cube text-lg"></i>
            </div>
            <div class="flex flex-col">
                <div class="flex items-center gap-2">
                    <h2 class="text-slate-900 text-base md:text-lg font-extrabold tracking-tight leading-none">
                        Customer Portal
                    </h2>
                    <span class="hidden sm:inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200/80 text-[10px] font-bold text-emerald-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Active
                    </span>
                </div>
                <span class="text-[10px] md:text-[11px] text-slate-400 font-semibold tracking-wider uppercase mt-0.5">
                    IT Investment Recoveries
                </span>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3 md:gap-6">
        <!-- User Dropdown Menu -->
        <div class="relative" id="userMenuDropdown">
            <button type="button" 
                    id="userMenuButton"
                    class="flex items-center gap-3 p-1.5 rounded-2xl border border-transparent hover:border-slate-200 hover:bg-slate-100/70 transition-all duration-200 group focus:outline-none"
                    aria-expanded="false"
                    aria-haspopup="true">
                <div class="relative shrink-0">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=1e293b&color=fff&font-size=0.4"
                        class="w-9 h-9 md:w-10 md:h-10 rounded-xl object-cover shadow-sm ring-2 ring-white group-hover:ring-blue-200 transition-all"
                        alt="{{ Auth::user()->name }}">
                    <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></span>
                </div>

                <div class="hidden md:flex flex-col text-left max-w-[150px]">
                    <span class="text-slate-900 text-xs font-extrabold truncate group-hover:text-blue-600 transition-colors">
                        {{ Auth::user()->name }}
                    </span>
                    <span class="text-slate-400 text-[10px] font-medium truncate">
                        {{ Auth::user()->email }}
                    </span>
                </div>

                <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200 hidden md:inline" id="userMenuChevron"></i>
            </button>

            <!-- Dropdown Card -->
            <div id="userMenuContent" 
                 class="hidden absolute right-0 mt-3 w-64 rounded-2xl bg-white border border-slate-200/90 shadow-2xl p-2 z-50 transform origin-top-right transition-all">
                <div class="px-4 py-3 border-b border-slate-100">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Signed in as</p>
                    <p class="text-sm font-black text-slate-900 truncate mt-0.5">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-500 truncate mt-0.5">{{ Auth::user()->email }}</p>
                </div>

                <div class="py-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-bold text-slate-700 rounded-xl hover:bg-slate-100 transition-colors">
                        <i class="fa-solid fa-chart-line text-blue-600 w-4 text-center"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('user.orders.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-bold text-slate-700 rounded-xl hover:bg-slate-100 transition-colors">
                        <i class="fa-solid fa-shopping-bag text-indigo-600 w-4 text-center"></i>
                        <span>My Orders</span>
                    </a>
                </div>

                <div class="pt-1 border-t border-slate-100">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-bold text-rose-600 rounded-xl hover:bg-rose-50 transition-colors text-left">
                            <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                            <span>Sign Out</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('userMenuButton');
        const menu = document.getElementById('userMenuContent');
        const chevron = document.getElementById('userMenuChevron');

        if (btn && menu) {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isHidden = menu.classList.contains('hidden');
                menu.classList.toggle('hidden', !isHidden);
                chevron?.classList.toggle('rotate-180', isHidden);
                btn.setAttribute('aria-expanded', String(isHidden));
            });

            document.addEventListener('click', (e) => {
                if (!menu.contains(e.target) && !btn.contains(e.target)) {
                    menu.classList.add('hidden');
                    chevron?.classList.remove('rotate-180');
                    btn.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });
</script>

