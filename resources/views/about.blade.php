<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="w-full overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>About – IT Investment Recoveries</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <!-- Google Fonts: Amaranth & Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Albert+Sans:ital,wght@0,100..900;1,100..900&family=Amaranth:ital,wght@0,400;0,700;1,400;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            emerald: {
                                900: '#035c43',
                                800: '#035c43',
                                600: '#035c43',
                                500: '#035c43',
                                400: '#035c43',
                            }
                        },
                        fontFamily: {
                            sans: ['"Albert Sans"', 'Helvetica', 'Arial', 'sans-serif'],
                            amaranth: ['"Albert Sans"', 'sans-serif'],
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
                animation: marqueeLeft 28s linear infinite;
            }
            .animate-partner-marquee:hover {
                animation-play-state: paused;
            }
        </style>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-[#035c43] text-white font-['Albert_Sans',sans-serif] antialiased selection:bg-[#035c43] selection:text-white w-full overflow-x-hidden m-0 p-0">

        <!-- Fixed Bottom-Left Badge (Always Anchored to Bottom-Left) -->
        <x-events-badge />

        <!-- Section 1: Hero Section (Using public/images/about/about-hero.webp as Hero Background) -->
        <section class="relative min-h-[85vh] sm:min-h-[90vh] lg:min-h-screen w-full flex flex-col justify-between overflow-hidden bg-transparent">
            <!-- Top Transparent Header Overlay -->
            <x-header active="about" />

            <!-- Background Image with Ken Burns Subtle Scale Effect -->
            <div class="absolute inset-0 z-0 overflow-hidden w-full h-full">
                <img 
                    src="{{ asset('images/about/about-hero.webp') }}" 
                    alt="About Our Company - IT Investment Recoveries Team" 
                    class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000 ease-out brightness-95 contrast-105"
                />
                <div class="absolute inset-0 bg-gradient-to-b from-slate-950/75 via-[#01281d]/30 to-slate-950/85"></div>
            </div>

            <!-- Main Hero Center Text Content -->
            <div class="relative z-20 flex-grow flex flex-col items-center justify-center text-center px-4 sm:px-8 lg:px-12 pt-48 sm:pt-60 md:pt-64 pb-32 sm:pb-40 w-full">
                <div class="w-full max-w-5xl mx-auto space-y-4 sm:space-y-6">
                    
                    <!-- Main Title: About Our Company -->
                    <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-[84px] font-bold text-white tracking-tight leading-[1.1] font-['Albert_Sans',sans-serif] drop-shadow-[0_4px_20px_rgba(0,0,0,0.85)]">
                        About Our Company
                    </h1>

                </div>
            </div>
        </section>

        <!-- Section 2: Below Hero Section (Executive Clean Overview) -->
        <section 
            id="about-overview" 
            class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full overflow-hidden border-b border-gray-100 relative"
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
                    :class="visible ? 'translate-x-0 opacity-100' : '-translate-x-12 opacity-0'"
                >
                    <div class="relative w-[280px] h-[280px] sm:w-[360px] sm:h-[360px] lg:w-[440px] lg:h-[440px] rounded-full overflow-hidden shadow-2xl border-4 border-emerald-500/20 ring-8 ring-emerald-50/60 group">
                        <img 
                            src="{{ asset('images/about/aaaaaaaaaaaaaaaaaaaaaaa.webp') }}" 
                            alt="IT Investment Recoveries Environmental Responsibility" 
                            class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700"
                        />
                    </div>
                </div>

                <!-- Right Column: Clean & Uncluttered Text Content -->
                <div 
                    class="lg:col-span-7 flex flex-col justify-center text-left space-y-6 sm:space-y-7 px-2 sm:px-4 transform transition-all duration-700 delay-200 ease-out"
                    :class="visible ? 'translate-x-0 opacity-100' : 'translate-x-12 opacity-0'"
                >
                    <!-- Paragraph 1 -->
                    <p class="text-[#035c43] text-lg sm:text-xl lg:text-2xl leading-relaxed sm:leading-[1.75] font-medium tracking-normal">
                        At <strong class="font-extrabold text-[#035c43]">IT Investment Recoveries</strong>, we help businesses, schools, government organizations, nonprofits, and other organizations responsibly manage their unwanted and end-of-life IT equipment.
                    </p>

                    <!-- Paragraph 2 -->
                    <p class="text-gray-700 text-base sm:text-lg lg:text-xl leading-relaxed sm:leading-[1.75] font-normal">
                        We understand that retiring IT equipment can be a time-consuming process. From inventory and asset tracking to data destruction, reuse, refurbishment, and recycling, there are many steps involved in properly managing technology at the end of its useful life.
                    </p>

                    <!-- Paragraph 3 with Emerald Left Stripe -->
                    <div class="border-l-4 border-[#035c43] bg-emerald-50/50 p-5 rounded-r-2xl shadow-sm border-y border-r border-emerald-100/60">
                        <p class="text-gray-900 text-base sm:text-lg lg:text-xl leading-relaxed sm:leading-[1.75] font-semibold">
                            Our goal is simple: to make IT asset disposition easier, more secure, and more environmentally responsible for our customers.
                        </p>
                    </div>
                </div>

            </div>
        </section>

        <!-- Section 3: Recovery Specialists Banner Section (Parallax Background) -->
        <section 
            class="relative w-full py-20 sm:py-28 lg:py-32 px-4 sm:px-8 lg:px-16 flex items-center justify-end overflow-hidden bg-fixed bg-cover bg-center bg-no-repeat"
            style="background-image: url('{{ asset('images/about/download.webp') }}');"
        >
            <!-- Dimming Overlay for Parallax Contrast -->
            <div class="absolute inset-0 bg-black/40 pointer-events-none"></div>

            <!-- Content Container (Right Aligned Clean Glass Card) -->
            <div class="relative z-10 w-full max-w-7xl mx-auto flex justify-end">
                <div class="w-full max-w-lg bg-white/95 backdrop-blur-md p-8 sm:p-12 border border-white/80 shadow-2xl rounded-2xl text-left">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-[#035c43] leading-tight tracking-tight mb-5 font-['Albert_Sans',sans-serif]">
                        We Are<br />
                        Recovery<br />
                        Specialists
                    </h2>
                    <p class="text-gray-900 font-bold text-sm sm:text-base leading-relaxed">
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
                    <h3 class="text-3xl sm:text-4xl lg:text-[46px] font-bold text-[#035c43] sm:text-[#035c43] tracking-normal">
                        We Support &amp; Do Business With
                    </h3>
                    <h2 class="text-4xl sm:text-5xl lg:text-[54px] font-black text-[#222222] tracking-tight">
                        Certified Service Providers
                    </h2>
                </div>

                <!-- Sliding Logos Marquee Slider -->
                <div class="relative w-full overflow-hidden py-6">
                    <div class="animate-partner-marquee flex items-center space-x-8 sm:space-x-12 md:space-x-16 pr-8 sm:pr-12">
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0 p-5 rounded-2xl bg-white rounded-2xl shadow-sm border border-gray-100 transition-all duration-300 transform hover:scale-105 group">
                            <img src="{{ asset('images/home-slider/khyuXt-removebg-preview-1.webp') }}" alt="Department of Defense Certification" class="max-h-full max-w-full object-contain filter drop-shadow-sm transition-transform duration-300" />
                        </div>
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0 p-5 rounded-2xl bg-white rounded-2xl shadow-sm border border-gray-100 transition-all duration-300 transform hover:scale-105 group">
                            <img src="{{ asset('images/home-slider/AEfjkp-removebg-preview-1.webp') }}" alt="EPA Certification" class="max-h-full max-w-full object-contain filter drop-shadow-sm transition-transform duration-300" />
                        </div>
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0 p-5 rounded-2xl bg-white rounded-2xl shadow-sm border border-gray-100 transition-all duration-300 transform hover:scale-105 group">
                            <img src="{{ asset('images/home-slider/lruIoO-removebg-preview.webp') }}" alt="NIST Certification" class="max-h-full max-w-full object-contain filter drop-shadow-sm transition-transform duration-300" />
                        </div>

                        <!-- Seamless Loop Duplicate -->
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0 p-5 rounded-2xl bg-white rounded-2xl shadow-sm border border-gray-100 transition-all duration-300 transform hover:scale-105 group">
                            <img src="{{ asset('images/home-slider/khyuXt-removebg-preview-1.webp') }}" alt="Department of Defense Certification" class="max-h-full max-w-full object-contain filter drop-shadow-sm transition-transform duration-300" />
                        </div>
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0 p-5 rounded-2xl bg-white rounded-2xl shadow-sm border border-gray-100 transition-all duration-300 transform hover:scale-105 group">
                            <img src="{{ asset('images/home-slider/AEfjkp-removebg-preview-1.webp') }}" alt="EPA Certification" class="max-h-full max-w-full object-contain filter drop-shadow-sm transition-transform duration-300" />
                        </div>
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0 p-5 rounded-2xl bg-white rounded-2xl shadow-sm border border-gray-100 transition-all duration-300 transform hover:scale-105 group">
                            <img src="{{ asset('images/home-slider/lruIoO-removebg-preview.webp') }}" alt="NIST Certification" class="max-h-full max-w-full object-contain filter drop-shadow-sm transition-transform duration-300" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Full Width Main Site Footer Component -->
        <x-footer />
    </body>
</html>
