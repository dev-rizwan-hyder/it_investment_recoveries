<!-- Full Width Main Site Footer Component -->
<footer class="w-full bg-[#035c43] text-white font-['Albert_Sans',sans-serif]">
    
    <!-- Top Main Footer Body Container -->
    <div class="w-full px-6 sm:px-12 lg:px-16 py-12 sm:py-16 grid grid-cols-1 md:grid-cols-12 gap-10 lg:gap-12 items-start">
        
        <!-- Column 1: Brand Logo & Description (Left) -->
        <div class="md:col-span-5 space-y-4 text-left">
            <a href="{{ url('/') }}" class="inline-block focus:outline-none py-1">
                <img 
                    src="{{ asset('images/new_logo.png') }}" 
                    alt="IT Investment Recoveries" 
                    class="h-24 sm:h-32 md:h-36 lg:h-40 w-auto object-contain drop-shadow-xl transition-transform duration-200 hover:scale-105"
                />
            </a>
            <p class="text-gray-200 text-sm sm:text-base leading-relaxed max-w-md font-normal">
                We are minority owned company that's been in business since 2006, we help organizations to Liquidate, Donate, or Recycle their I.T Assets.
            </p>
        </div>

        <!-- Column 2: Quick Links (Center) -->
        <div class="md:col-span-3 space-y-4 text-left">
            <h3 class="text-xl sm:text-2xl font-bold text-[#035c43] tracking-wide">
                Quick Links
            </h3>
            <ul class="space-y-3 text-sm sm:text-base font-semibold text-gray-100">
                <li><a href="{{ url('/') }}" class="hover:text-[#035c43] transition-colors duration-200">Home</a></li>
                <li><a href="{{ url('/about-us') }}" class="hover:text-[#035c43] transition-colors duration-200">About</a></li>
                <li><a href="{{ url('/') }}#services" class="hover:text-[#035c43] transition-colors duration-200">Services</a></li>
                <li><a href="{{ url('/contact-us') }}" class="hover:text-[#035c43] transition-colors duration-200">Get Started</a></li>
            </ul>
        </div>

        <!-- Column 3: Contact Details (Right) -->
        <div class="md:col-span-4 space-y-4 text-left">
            <h3 class="text-xl sm:text-2xl font-bold text-[#035c43] tracking-wide">
                Contact Details
            </h3>
            <ul class="space-y-4 text-sm sm:text-base font-semibold text-gray-100">
                <li class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-white shrink-0 fill-current" viewBox="0 0 24 24">
                        <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                    </svg>
                    <a href="tel:+13034724701" class="hover:text-[#035c43] transition-colors duration-200">+1-303-472-4701</a>
                </li>
                <li class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-white shrink-0 fill-current" viewBox="0 0 24 24">
                        <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 9h12v2H6V9zm8 5H6v-2h8v2zm4-6H6V6h12v2z"/>
                    </svg>
                    <a href="{{ url('/contact-us') }}" class="hover:text-[#035c43] transition-colors duration-200">Send a Message</a>
                </li>
                <li class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-white shrink-0 fill-current mt-0.5" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    <span>5765 W 52nd Ave Denver Co 80212 USA</span>
                </li>
            </ul>
        </div>

    </div>

    <!-- Bottom Sub-Footer Bar (Full Width Copyright & Developer Info) -->
    <div class="w-full bg-[#1c1c1c] py-4 px-6 sm:px-12 lg:px-16 border-t border-gray-800 text-xs sm:text-sm text-gray-300">
        <div class="w-full flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left relative">
            <div>
                Copyright &copy; {{ date('Y') }} IT Investment Recoveries
            </div>
            <div class="pr-12 sm:pr-14">
                Developed By <a href="https://thebiztechsolutions.com" target="_blank" rel="noopener noreferrer" class="text-[#035c43] hover:underline font-bold">TheBizTechSolutions</a>
            </div>

            <!-- Scroll To Top Button (Anchored Bottom Right) -->
            <button 
                onclick="window.scrollTo({ top: 0, behavior: 'smooth' })" 
                class="absolute right-0 bottom-0 bg-[#035c43] hover:bg-[#024734] text-white p-2.5 rounded transition duration-300 shadow-md flex items-center justify-center"
                title="Scroll to Top"
            >
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"/>
                </svg>
            </button>
        </div>
    </div>

</footer>
