<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="w-full overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>About Us | Denver's Top Computer &amp; Electronics Recycling Company | IT Investment Recoveries</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <!-- Google Fonts: Amaranth & Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Amaranth:ital,wght@0,400;0,700;1,400;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            emerald: {
                                900: '#004d2e',
                                800: '#005a36',
                                600: '#059669',
                                500: '#10b981',
                                400: '#34d399',
                            }
                        },
                        fontFamily: {
                            sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                            amaranth: ['"Amaranth"', 'sans-serif'],
                        }
                    }
                }
            }
        </script>

        <!-- Alpine JS -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            [x-cloak] { display: none !important; }
            @keyframes marqueeLeft {
                0% {
                    transform: translateX(0%);
                }
                100% {
                    transform: translateX(-50%);
                }
            }
            .animate-partner-marquee {
                display: flex;
                width: max-content;
                animation: marqueeLeft 18s linear infinite;
            }
        </style>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-[#002d1b] text-white font-['Plus_Jakarta_Sans',sans-serif] antialiased selection:bg-emerald-500 selection:text-white w-full overflow-x-hidden m-0 p-0">

        <!-- Fixed Bottom-Left Badge (Always Anchored to Bottom-Left) -->
        <div class="fixed bottom-4 left-4 sm:bottom-8 sm:left-8 z-40">
            <div class="w-28 h-28 sm:w-36 sm:h-36 rounded-full bg-black/90 border-2 border-dashed border-emerald-400 p-1.5 sm:p-2 shadow-2xl flex flex-col items-center justify-center text-center transform hover:scale-105 transition-transform duration-300">
                <div class="w-full h-full rounded-full border border-emerald-500/30 flex flex-col items-center justify-center p-1.5 sm:p-2">
                    <span class="text-[9px] sm:text-xs font-bold text-gray-200 uppercase tracking-wider leading-tight">COMMUNITY</span>
                    <span class="text-[8px] sm:text-[10px] font-semibold text-emerald-400 uppercase tracking-wide leading-tight my-0.5">&amp; BUSINESS</span>
                    <span class="text-[9px] sm:text-xs font-bold text-gray-200 uppercase tracking-wider leading-tight">EVENTS</span>
                    <span class="text-[10px] sm:text-xs font-extrabold text-white uppercase tracking-widest leading-tight mt-0.5 border-t border-emerald-500/40 pt-0.5 w-4/5">AVAILABLE</span>
                </div>
            </div>
        </div>

        <!-- Section 1: Hero Section (Using public/images/about.webp as Hero Background) -->
        <section class="relative min-h-[75vh] sm:min-h-[85vh] lg:min-h-screen w-full flex flex-col justify-between overflow-hidden bg-black">
            <!-- Top Transparent Header Overlay -->
            <x-header active="about" />

            <!-- Background Image with Ken Burns Subtle Scale Effect -->
            <div class="absolute inset-0 z-0 overflow-hidden w-full h-full">
                <img 
                    src="{{ asset('images/about.webp') }}" 
                    alt="About Our Company - IT Investment Recoveries Team" 
                    class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000 ease-out"
                />
                <!-- Gradient Dark Overlay for Crisp Contrast -->
                <div class="absolute inset-0 z-10 bg-gradient-to-b from-black/70 via-black/45 to-black/75"></div>
            </div>

            <!-- Main Hero Center Text Content -->
            <div class="relative z-20 flex-grow flex flex-col items-center justify-center text-center px-4 sm:px-8 lg:px-12 pt-28 sm:pt-40 md:pt-48 pb-16 w-full">
                <div class="w-full max-w-5xl mx-auto space-y-4 sm:space-y-6">
                    
                    <!-- Main Title: About Our Company -->
                    <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-[84px] font-bold text-white tracking-tight leading-[1.1] font-['Amaranth',sans-serif] drop-shadow-[0_4px_20px_rgba(0,0,0,0.85)]">
                        About Our Company
                    </h1>

                </div>
            </div>
        </section>

        <!-- Section 2: Below Hero Section (Circular Image & Company Description) -->
        <section 
            id="about-overview" 
            class="bg-white text-[#004d2e] py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full overflow-hidden"
            x-data="{ visible: false }"
            x-init="
                const observer = new IntersectionObserver(([entry]) => {
                    if (entry.isIntersecting) {
                        visible = true;
                    }
                }, { threshold: 0.15 });
                observer.observe($el);
            "
        >
            <div class="w-full max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                
                <!-- Left Column: Circular Image (Hands holding soil & plant) -->
                <div 
                    class="lg:col-span-5 flex justify-center lg:justify-start transform transition-all duration-700 ease-out"
                    :class="visible ? 'translate-x-0 opacity-100' : '-translate-x-16 opacity-0'"
                >
                    <div class="relative w-[300px] h-[300px] sm:w-[380px] sm:h-[380px] lg:w-[460px] lg:h-[460px] rounded-full overflow-hidden shadow-2xl border-4 border-white/60">
                        <img 
                            src="{{ asset('images/about/aaaaaaaaaaaaaaaaaaaaaaa.webp') }}" 
                            alt="IT Investment Recoveries Environmental Responsibility" 
                            class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-700"
                        />
                    </div>
                </div>

                <!-- Right Column: Centered Green Text Content -->
                <div 
                    class="lg:col-span-7 flex flex-col items-center justify-center text-center space-y-6 sm:space-y-8 px-2 sm:px-6 transform transition-all duration-700 delay-200 ease-out"
                    :class="visible ? 'translate-x-0 opacity-100' : 'translate-x-16 opacity-0'"
                >
                    <p class="text-[#004d2e] text-lg sm:text-xl lg:text-2xl leading-relaxed sm:leading-[1.8] font-normal tracking-wide">
                        At <strong class="font-bold">IT Investment Recoveries</strong> , we help businesses, schools, government organizations, nonprofits, and other organizations responsibly manage their unwanted and end-of-life IT equipment.
                    </p>

                    <p class="text-[#004d2e] text-lg sm:text-xl lg:text-2xl leading-relaxed sm:leading-[1.8] font-normal tracking-wide">
                        We understand that retiring IT equipment can be a time-consuming process. From inventory and asset tracking to data destruction, reuse, refurbishment, and recycling, there are many steps involved in properly managing technology at the end of its useful life.
                    </p>

                    <p class="text-[#004d2e] text-lg sm:text-xl lg:text-2xl leading-relaxed sm:leading-[1.8] font-normal tracking-wide">
                        Our goal is simple: to make IT asset disposition easier, more secure, and more environmentally responsible for our customers.
                    </p>
                </div>

            </div>
        </section>

        <!-- Section 3: Recovery Specialists Parallax Section (Reduced Height Fixed Background) -->
        <section 
            class="relative w-full py-10 sm:py-14 lg:py-16 px-4 sm:px-8 lg:px-16 flex items-center justify-end overflow-hidden bg-center bg-cover bg-no-repeat"
            style="background-image: url('{{ asset('images/about/download.webp') }}'); background-attachment: fixed;"
        >
            <!-- Background Subtle Contrast Dark Overlay -->
            <div class="absolute inset-0 bg-black/35 pointer-events-none"></div>

            <!-- Content Container (Right Aligned Light Gray Rounded Card) -->
            <div class="relative z-10 w-full max-w-7xl mx-auto flex justify-end">
                <div class="w-full max-w-lg bg-[#d5d8de]/95 backdrop-blur-md p-6 sm:p-8 md:p-9 shadow-2xl border border-white/50 text-left space-y-3 rounded-[24px]">
                    <h2 class="text-3xl sm:text-4xl md:text-[40px] font-bold text-[#044b2d] leading-[1.12] tracking-tight">
                        We Are<br />
                        Recovery<br />
                        Specialists
                    </h2>
                    <p class="text-[#1e293b] font-medium text-sm sm:text-base leading-relaxed">
                        We provide specialized solutions to maximize the value of your IT assets, ensuring efficiency and sustainability.
                    </p>
                </div>
            </div>
        </section>

        <!-- Section 4: Certified Service Providers Section (Exact Home Page Style) -->
        <section class="bg-white text-gray-800 py-14 sm:py-20 px-4 sm:px-8 w-full overflow-hidden border-t border-gray-100">
            <div class="w-full max-w-7xl mx-auto text-center">
                <!-- Title matching screenshot exactly -->
                <div class="max-w-5xl mx-auto mb-10 sm:mb-14 space-y-2">
                    <h3 class="text-3xl sm:text-4xl lg:text-[46px] font-bold text-[#10b981] sm:text-[#34d399] tracking-normal">
                        We Support &amp; Do Business With
                    </h3>
                    <h2 class="text-4xl sm:text-5xl lg:text-[54px] font-black text-[#222222] tracking-tight">
                        Certified Service Providers
                    </h2>
                </div>

                <!-- Sliding Logos Marquee Slider -->
                <div class="relative w-full overflow-hidden py-6">
                    <div class="animate-partner-marquee flex items-center space-x-16 sm:space-x-24 md:space-x-32 pr-16 sm:pr-24">
                        <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                            <img src="{{ asset('images/home-slider/khyuXt-removebg-preview-1.webp') }}" alt="Department of Defense Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                            <img src="{{ asset('images/home-slider/AEfjkp-removebg-preview-1.webp') }}" alt="EPA Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                            <img src="{{ asset('images/home-slider/lruIoO-removebg-preview.webp') }}" alt="NIST Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>

                        <!-- Seamless Loop Duplicate -->
                        <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                            <img src="{{ asset('images/home-slider/khyuXt-removebg-preview-1.webp') }}" alt="Department of Defense Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                            <img src="{{ asset('images/home-slider/AEfjkp-removebg-preview-1.webp') }}" alt="EPA Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                            <img src="{{ asset('images/home-slider/lruIoO-removebg-preview.webp') }}" alt="NIST Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Full Width Main Site Footer Component -->
        <x-footer />
    </body>
</html>
