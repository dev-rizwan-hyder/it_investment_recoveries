<!-- Sidebar -->
<aside id="sidebar"
    class="hidden lg:flex fixed lg:sticky top-0 lg:top-20 bottom-0 left-0 z-40 w-72 max-w-[86vw] lg:w-64 lg:max-w-none bg-white border-r border-gray-200 h-screen lg:h-[calc(100vh-5rem)] flex-col shadow-xl lg:shadow-none overflow-hidden">

    <div class="p-6 space-y-2 flex-1 overflow-y-auto">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all font-bold text-gray-700 {{ request()->routeIs('dashboard') ? 'sidebar-active' : 'hover:bg-gray-50' }}">
            <i class="fas fa-chart-line w-5"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('user.received-intake.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all font-bold text-gray-700 {{ request()->routeIs('user.received-intake.*') ? 'sidebar-active' : 'hover:bg-gray-50' }}">
            <i class="fas fa-boxes w-5"></i>
            <span>Received Intake</span>
        </a>

        <a href="{{ route('user.data-destruction.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all font-bold text-gray-700 {{ request()->routeIs('user.data-destruction.*') ? 'sidebar-active' : 'hover:bg-gray-50' }}">
            <i class="fas fa-eraser w-5"></i>
            <span>Data Destruction</span>
        </a>

        <a href="{{ route('user.it-assets.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all font-bold text-gray-700 {{ request()->routeIs('user.it-assets.*') ? 'sidebar-active' : 'hover:bg-gray-50' }}">
            <i class="fas fa-laptop-code w-5"></i>
            <span>IT Assets</span>
        </a>

        <a href="{{ route('user.orders.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-all font-bold text-gray-700 {{ request()->routeIs('user.orders.*') ? 'sidebar-active' : 'hover:bg-gray-50' }}">
            <i class="fas fa-shopping-bag w-5"></i>
            <span>Order</span>
        </a>

        <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-50 transition-all font-bold text-gray-700">
            <i class="fas fa-cog w-5"></i>
            <span>Settings</span>
        </a>
           <!-- Logout Section -->
    <div class="p-6 border-t border-gray-200 flex-shrink-0">
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        <a href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
            class="flex items-center justify-center w-full py-2.5 rounded-lg bg-red-50 text-red-500 text-[11px] font-bold hover:bg-red-500 hover:text-white transition-all duration-300 group">
            <i class="fas fa-sign-out-alt mr-2"></i> LOGOUT
        </a>
    </div>
    </div>
</aside>
