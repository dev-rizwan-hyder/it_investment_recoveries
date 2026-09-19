<!-- Sidebar Navigation -->
<aside id="sidebar"
    class="hidden lg:flex fixed lg:sticky top-0 lg:top-20 bottom-0 left-0 z-40 w-72 max-w-[86vw] lg:w-64 lg:max-w-none bg-white border-r border-slate-200/80 h-screen lg:h-[calc(100vh-5rem)] flex-col shadow-xl lg:shadow-none overflow-hidden transition-all duration-300">

    <div class="px-4 py-6 space-y-1.5 flex-1 overflow-y-auto">
        <p class="px-4 pb-2 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Navigation</p>

        <a href="{{ route('dashboard') }}" 
           class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all font-bold text-sm text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 group {{ request()->routeIs('dashboard') ? 'sidebar-active' : '' }}">
            <i class="fas fa-chart-line w-5 text-center text-slate-400 group-hover:text-blue-600 transition-colors"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('user.received-intake.index') }}" 
           class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all font-bold text-sm text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 group {{ request()->routeIs('user.received-intake.*') ? 'sidebar-active' : '' }}">
            <i class="fas fa-boxes-stacked w-5 text-center text-slate-400 group-hover:text-blue-600 transition-colors"></i>
            <span>Received Intake</span>
        </a>

        <a href="{{ route('user.data-destruction.index') }}" 
           class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all font-bold text-sm text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 group {{ request()->routeIs('user.data-destruction.*') ? 'sidebar-active' : '' }}">
            <i class="fas fa-shield-halved w-5 text-center text-slate-400 group-hover:text-rose-600 transition-colors"></i>
            <span>Data Destruction</span>
        </a>

        <a href="{{ route('user.it-assets.index') }}" 
           class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all font-bold text-sm text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 group {{ request()->routeIs('user.it-assets.*') ? 'sidebar-active' : '' }}">
            <i class="fas fa-laptop-code w-5 text-center text-slate-400 group-hover:text-indigo-600 transition-colors"></i>
            <span>IT Assets</span>
        </a>

        <a href="{{ route('user.orders.index') }}" 
           class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all font-bold text-sm text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 group {{ request()->routeIs('user.orders.*') ? 'sidebar-active' : '' }}">
            <i class="fas fa-shopping-bag w-5 text-center text-slate-400 group-hover:text-amber-600 transition-colors"></i>
            <span>My Orders</span>
        </a>

        <div class="pt-2">
            <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();"
               class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all font-bold text-sm text-rose-600 hover:bg-rose-50 hover:text-rose-700 group">
                <i class="fas fa-right-from-bracket w-5 text-center text-rose-500 group-hover:scale-110 transition-transform"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</aside>

