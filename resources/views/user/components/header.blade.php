<header
    class="h-20 bg-white/70 backdrop-blur-xl border-b border-slate-100 flex items-center justify-between px-4 md:px-10 sticky top-0 z-30 transition-all duration-300">

    <div class="flex items-center gap-4 md:gap-6">
        <button
            id="sidebarToggle"
            type="button"
            class="lg:hidden inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-blue-200 hover:text-blue-600"
            aria-label="Open navigation"
            aria-controls="sidebar"
            aria-expanded="false">
            <i class="fa-solid fa-bars"></i>
        </button>

        <div class="flex flex-col">
            <h2 class="text-slate-900 text-lg md:text-xl font-extrabold tracking-tight leading-none">
                USER PANEL
            </h2>
            <div class="flex items-center gap-2 mt-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shadow-[0_0_8px_#3b82f6]"></span>
                <span
                    class="text-[9px] md:text-[10px] text-slate-400 font-bold uppercase tracking-[0.15em] md:tracking-[0.2em]">System
                    Active</span>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3 md:gap-8">
        <a href="{{ Route::has('store.index') ? route('store.index') : url('/') }}" class="px-3 py-2 md:px-5 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-[12px] md:text-[13px] font-semibold rounded-lg transition-all duration-300 shadow-md hover:shadow-lg">
            <i class="fa-solid fa-store md:mr-2"></i><span class="hidden md:inline">View Store</span>
        </a>
        
        <div class="hidden sm:block h-8 w-px bg-slate-200/60"></div>

        <div
            class="flex items-center gap-3 md:gap-4 group cursor-pointer p-1 pr-2 md:pr-3 rounded-2xl hover:bg-slate-50 transition-all duration-300">
            <div class="relative flex-shrink-0">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0f172a&color=fff"
                    class="w-9 h-9 md:w-10 md:h-10 rounded-xl object-cover shadow-md group-hover:shadow-blue-200 transition-all"
                    alt="User">
                <div
                    class="absolute -bottom-1 -right-1 w-3 h-3 md:w-3.5 md:h-3.5 bg-emerald-500 border-2 border-white rounded-full">
                </div>
            </div>

            <div class="hidden xs:flex flex-col overflow-hidden max-w-[100px] md:max-w-[200px]">
                <span class="text-slate-900 text-[12px] md:text-[13px] font-bold leading-none truncate">
                    {{ Auth::user()->name }}
                </span>
                <span class="text-slate-400 text-[10px] md:text-[11px] font-medium mt-1 tracking-tight truncate">
                    {{ Auth::user()->email }}
                </span>
            </div>
        </div>
    </div>
</header>

<style>
    @media (min-width: 1024px) {
        .lg\:ml-68 {
            margin-left: 17rem;
        }
    }

    @media (max-width: 380px) {
        .xs\:flex {
            display: none !important;
        }
    }
</style>
