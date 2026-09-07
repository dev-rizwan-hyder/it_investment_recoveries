@props(['active' => 'home'])

<header 
    x-data="{ scrolled: false }"
    x-init="scrolled = (window.pageYOffset > 20)"
    @scroll.window="scrolled = (window.pageYOffset > 20)"
    :class="scrolled ? 'bg-[#004d2e] shadow-xl border-b border-emerald-800/40' : 'bg-transparent shadow-none border-b border-transparent'"
    class="w-full fixed top-0 left-0 right-0 z-50 text-white transition-all duration-300 ease-in-out font-['Plus_Jakarta_Sans',sans-serif]"
>
    <div class="w-full px-4 sm:px-8 lg:px-12">
        <div class="flex items-center justify-between h-16 sm:h-18 md:h-28 lg:h-32">
            <!-- Brand / Logo (Far Left) -->
            <a href="{{ url('/') }}" class="flex items-center group focus:outline-none rounded-md py-1">
                <img 
                    src="{{ asset('images/logo.png') }}" 
                    alt="IT Investment Recoveries" 
                    class="h-10 sm:h-12 md:h-24 lg:h-28 xl:h-32 max-h-full w-auto object-contain drop-shadow-xl transition-transform duration-200 hover:scale-105"
                />
            </a>

            <!-- Desktop Navigation (Far Right) -->
            <nav class="hidden md:flex items-center space-x-3 lg:space-x-8">
                <!-- Home -->
                <div class="relative py-2 px-3 sm:px-4">
                    <a href="{{ url('/') }}" class="{{ $active === 'home' ? 'text-[#10b981]' : 'text-white hover:text-emerald-300' }} font-bold text-lg lg:text-xl tracking-normal focus:outline-none transition-colors duration-200 drop-shadow">
                        Home
                    </a>
                    @if($active === 'home')
                        <!-- Active underline bar -->
                        <div class="absolute bottom-0 left-3 right-3 h-[3px] bg-white rounded-full"></div>
                    @endif
                </div>

                <!-- About Us -->
                <div class="relative py-2 px-3 sm:px-4">
                    <a href="{{ url('/about-us') }}" class="{{ $active === 'about' ? 'text-[#10b981]' : 'text-white hover:text-emerald-300' }} font-bold text-lg lg:text-xl tracking-normal focus:outline-none transition-colors duration-200 drop-shadow">
                        About Us
                    </a>
                    @if($active === 'about')
                        <!-- Active underline bar -->
                        <div class="absolute bottom-0 left-3 right-3 h-[3px] bg-white rounded-full"></div>
                    @endif
                </div>

                <!-- Services Dropdown -->
                <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <a 
                        href="{{ url('/services') }}" 
                        class="{{ $active === 'services' ? 'text-[#10b981]' : 'text-white hover:text-emerald-300' }} font-bold text-lg lg:text-xl px-3 sm:px-4 py-2 inline-flex items-center gap-2 focus:outline-none transition-colors duration-200 rounded-md drop-shadow"
                    >
                        <span>Services</span>
                        <svg class="w-4 h-4 fill-current transition-transform duration-200 group-hover:rotate-180" viewBox="0 0 20 20">
                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                        </svg>
                    </a>
                    @if($active === 'services')
                        <!-- Active underline bar -->
                        <div class="absolute bottom-0 left-3 right-3 h-[3px] bg-white rounded-full"></div>
                    @endif

                    <!-- Dropdown Menu Box -->
                    <div 
                        class="absolute right-0 sm:left-0 mt-2 w-80 bg-white rounded-xl shadow-2xl py-4 border border-gray-100 z-50 transition-all duration-200 origin-top-left"
                        x-show="open"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                        style="display: none;"
                        :style="open ? 'display: block;' : ''"
                    >
                        <a href="{{ url('/it-asset-management') }}" class="block px-6 py-3.5 {{ request()->is('it-asset-management*') ? 'text-[#005a36] bg-emerald-50/90' : 'text-[#1f2937] hover:text-[#005a36] hover:bg-emerald-50/90' }} font-bold text-base sm:text-lg transition-colors duration-150 border-b border-gray-50 last:border-b-0">
                            IT Assets Management
                        </a>
                        <a href="{{ url('/data-destruction') }}" class="block px-6 py-3.5 {{ request()->is('data-destruction*') ? 'text-[#005a36] bg-emerald-50/90' : 'text-[#1f2937] hover:text-[#005a36] hover:bg-emerald-50/90' }} font-bold text-base sm:text-lg transition-colors duration-150 border-b border-gray-50 last:border-b-0">
                            Data Destruction
                        </a>
                        <a href="{{ url('/it-disposal') }}" class="block px-6 py-3.5 {{ request()->is('it-disposal*') ? 'text-[#005a36] bg-emerald-50/90' : 'text-[#1f2937] hover:text-[#005a36] hover:bg-emerald-50/90' }} font-bold text-base sm:text-lg transition-colors duration-150 border-b border-gray-50 last:border-b-0">
                            E Waste/ IT Disposal
                        </a>
                        <a href="{{ url('/computer-liquidator-reseller') }}" class="block px-6 py-3.5 {{ request()->is('computer-liquidator*') ? 'text-[#005a36] bg-emerald-50/90' : 'text-[#1f2937] hover:text-[#005a36] hover:bg-emerald-50/90' }} font-bold text-base sm:text-lg transition-colors duration-150 border-b border-gray-50 last:border-b-0">
                            Computer Liquidator and Reseller
                        </a>
                        <a href="{{ url('/charity-donation') }}" class="block px-6 py-3.5 {{ request()->is('charity*') ? 'text-[#005a36] bg-emerald-50/90' : 'text-[#1f2937] hover:text-[#005a36] hover:bg-emerald-50/90' }} font-bold text-base sm:text-lg transition-colors duration-150 border-b border-gray-50 last:border-b-0">
                            Charity Donation
                        </a>
                        <a href="{{ url('/community-partners') }}" class="block px-6 py-3.5 {{ request()->is('community*') ? 'text-[#005a36] bg-emerald-50/90' : 'text-[#1f2937] hover:text-[#005a36] hover:bg-emerald-50/90' }} font-bold text-base sm:text-lg transition-colors duration-150 border-b border-gray-50 last:border-b-0">
                            Community/ Business Partners
                        </a>
                    </div>
                </div>

                <!-- Free E-Waste Recycling -->
                <div class="relative py-2 px-3 sm:px-4">
                    <a href="{{ url('/free-e-waste-recycling') }}" class="{{ $active === 'ewaste' ? 'text-[#10b981]' : 'text-white hover:text-emerald-300' }} font-bold text-lg lg:text-xl tracking-normal focus:outline-none transition-colors duration-200 drop-shadow">
                        Free E-Waste Recycling
                    </a>
                    @if($active === 'ewaste')
                        <!-- Active underline bar -->
                        <div class="absolute bottom-0 left-3 right-3 h-[3px] bg-white rounded-full"></div>
                    @endif
                </div>


                <!-- Contact Us -->
                <div class="relative py-2 px-3 sm:px-4">
                    <a href="{{ url('/contact-us') }}" class="{{ $active === 'contact' ? 'text-[#10b981]' : 'text-white hover:text-emerald-300' }} font-bold text-lg lg:text-xl tracking-normal focus:outline-none transition-colors duration-200 drop-shadow">
                        Contact Us
                    </a>
                    @if($active === 'contact')
                        <!-- Active underline bar -->
                        <div class="absolute bottom-0 left-3 right-3 h-[3px] bg-white rounded-full"></div>
                    @endif
                </div>
            </nav>

            <!-- Mobile Menu Toggle Button -->
            <div class="flex md:hidden">
                <button 
                    id="mobile-sidebar-toggle-btn"
                    type="button" 
                    class="text-white hover:text-emerald-200 focus:outline-none p-2.5 rounded-lg bg-black/30 backdrop-blur-md border border-white/20"
                    aria-label="Toggle navigation sidebar"
                >
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Sidebar Drawer -->
    <div id="mobile-sidebar-overlay" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm transition-opacity duration-300">
        <!-- Sidebar Container -->
        <div id="mobile-sidebar-panel" class="fixed inset-y-0 right-0 w-80 sm:w-96 max-w-full bg-[#004d2e] shadow-2xl flex flex-col justify-between transform translate-x-full transition-transform duration-300 ease-in-out border-l border-emerald-700">
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between p-6 border-b border-emerald-800 bg-[#003d24]">
                <img 
                    src="{{ asset('images/logo.png') }}" 
                    alt="IT Investment Recoveries" 
                    class="h-20 sm:h-22 w-auto object-contain"
                />
                <button 
                    id="mobile-sidebar-close-btn"
                    type="button" 
                    class="text-emerald-200 hover:text-white p-2 rounded-md focus:outline-none"
                    aria-label="Close sidebar"
                >
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Sidebar Navigation Links -->
            <div class="flex-grow overflow-y-auto px-6 py-6 space-y-4">
                <a href="{{ url('/') }}" class="block px-4 py-3 {{ $active === 'home' ? 'text-[#10b981] bg-[#003d24] border border-emerald-600/40' : 'text-white hover:text-emerald-300' }} font-extrabold text-xl rounded-xl transition duration-200">Home</a>
                <a href="{{ url('/about-us') }}" class="block px-4 py-3 {{ $active === 'about' ? 'text-[#10b981] bg-[#003d24] border border-emerald-600/40' : 'text-white hover:text-emerald-300' }} font-extrabold text-xl rounded-xl transition duration-200">About Us</a>
                
                <!-- Mobile Services Accordion (Closed by default: openServices: false) -->
                <div x-data="{ openServices: false }">
                    <button @click="openServices = !openServices" class="w-full flex items-center justify-between px-4 py-3 text-white hover:text-emerald-300 font-bold text-xl rounded-xl">
                        <span>Services</span>
                        <svg class="w-5 h-5 fill-current transform transition-transform" :class="openServices ? 'rotate-180' : ''" viewBox="0 0 20 20">
                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                        </svg>
                    </button>
                    <div 
                        x-show="openServices" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 max-h-0"
                        x-transition:enter-end="opacity-100 max-h-96"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 max-h-96"
                        x-transition:leave-end="opacity-0 max-h-0"
                        class="pl-4 mt-2 space-y-2 bg-[#003d24]/80 py-3 px-2 rounded-xl border border-emerald-800 overflow-hidden"
                        style="display: none;"
                    >
                        <a href="{{ url('/it-asset-management') }}" class="block px-3 py-2.5 text-gray-100 hover:text-emerald-300 font-bold text-base">IT Assets Management</a>
                        <a href="{{ url('/data-destruction') }}" class="block px-3 py-2.5 text-gray-100 hover:text-emerald-300 font-bold text-base">Data Destruction</a>
                        <a href="{{ url('/it-disposal') }}" class="block px-3 py-2.5 text-gray-100 hover:text-emerald-300 font-bold text-base">E Waste/ IT Disposal</a>
                        <a href="{{ url('/computer-liquidator-reseller') }}" class="block px-3 py-2.5 text-gray-100 hover:text-emerald-300 font-bold text-base">Computer Liquidator and Reseller</a>
                        <a href="{{ url('/charity-donation') }}" class="block px-3 py-2.5 text-gray-100 hover:text-emerald-300 font-bold text-base">Charity Donation</a>
                        <a href="{{ url('/community-partners') }}" class="block px-3 py-2.5 text-gray-100 hover:text-emerald-300 font-bold text-base">Community/ Business Partners</a>
                    </div>
                </div>

                <a href="{{ url('/free-e-waste-recycling') }}" class="block px-4 py-3 {{ $active === 'ewaste' ? 'text-[#10b981] bg-[#003d24] border border-emerald-600/40' : 'text-white hover:text-emerald-300' }} font-extrabold text-xl rounded-xl transition duration-200">Free E-Waste Recycling</a>
                <a href="{{ url('/contact-us') }}" class="block px-4 py-3 {{ $active === 'contact' ? 'text-[#10b981] bg-[#003d24] border border-emerald-600/40' : 'text-white hover:text-emerald-300' }} font-extrabold text-xl rounded-xl transition duration-200">Contact Us</a>
            </div>

            <!-- Sidebar Footer -->
            <div class="p-6 border-t border-emerald-800 bg-[#003d24]">
                <a href="{{ url('/contact-us') }}" class="block w-full py-3.5 text-center bg-[#10b981] hover:bg-[#059669] text-white font-bold text-lg rounded-xl shadow-lg transition">
                    Schedule A Consultation →
                </a>
            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('mobile-sidebar-toggle-btn');
        const closeBtn = document.getElementById('mobile-sidebar-close-btn');
        const overlay = document.getElementById('mobile-sidebar-overlay');
        const panel = document.getElementById('mobile-sidebar-panel');

        function openSidebar() {
            if (overlay && panel) {
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    panel.classList.remove('translate-x-full');
                }, 10);
            }
        }

        function closeSidebar() {
            if (overlay && panel) {
                panel.classList.add('translate-x-full');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                }, 300);
            }
        }

        if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (overlay) {
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) closeSidebar();
            });
        }
    });
</script>
