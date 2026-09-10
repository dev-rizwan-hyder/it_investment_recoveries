<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="w-full overflow-x-hidden scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>IT Investment Recoveries | Computer & Electronics Recycling in Denver</title>
        <meta name="description" content="Denver's top computer and electronics recycling company. Secure data destruction, e-waste recycling, liquidation, and drop-off services in Denver, Colorado.">
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <!-- Google Fonts: Amaranth, Plus Jakarta Sans & Outfit -->
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
                                950: '#035c43',
                                900: '#035c43',
                                800: '#035c43',
                                700: '#035c43',
                                600: '#035c43',
                                500: '#035c43',
                                400: '#035c43',
                                300: '#035c43',
                            }
                        },
                        fontFamily: {
                            sans: ['"Albert Sans"', 'Helvetica', 'Arial', 'sans-serif'],
                            amaranth: ['"Albert Sans"', 'sans-serif'],
                            outfit: ['"Albert Sans"', 'sans-serif'],
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
                0% { transform: translateX(0%); }
                100% { transform: translateX(-50%); }
            }
            @keyframes marqueeRight {
                0% { transform: translateX(-50%); }
                100% { transform: translateX(0%); }
            }
            .animate-partner-marquee {
                display: flex;
                width: max-content;
                animation: marqueeLeft 28s linear infinite;
            }
            .animate-partner-marquee:hover {
                animation-play-state: paused;
            }
            .animate-marquee-left {
                display: flex;
                width: max-content;
                animation: marqueeLeft 28s linear infinite;
            }
            .animate-marquee-left:hover {
                animation-play-state: paused;
            }
            .animate-marquee-right {
                display: flex;
                width: max-content;
                animation: marqueeRight 28s linear infinite;
            }
            .animate-marquee-right:hover {
                animation-play-state: paused;
            }
        </style>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-white text-gray-900 font-['Albert_Sans',sans-serif] antialiased selection:bg-[#035c43] selection:text-white w-full overflow-x-hidden m-0 p-0">

        <!-- Fixed Bottom-Left Badge (Always Anchored to Bottom-Left) -->
        <x-events-badge />

        <!-- Section 1: Main Hero Section (Full Height with Video Background & Transparent Header Overlay) -->
        <section class="relative min-h-screen w-full flex flex-col justify-between overflow-hidden bg-transparent">
            <!-- Top Transparent Header Overlay -->
            <x-header />

            <!-- Background Video with Continuous Loop & Multi-layer Overlay -->
            <div class="absolute inset-0 z-0 overflow-hidden w-full h-full">
                <video 
                    autoplay 
                    loop 
                    muted 
                    playsinline 
                    onloadeddata="this.playbackRate = 0.8;"
                    onplay="this.playbackRate = 0.8;"
                    class="w-full h-full object-cover"
                    poster="{{ asset('images/home-banner.webp') }}"
                >
                    <source src="{{ asset('videos/hero.mp4') }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>

                <!-- Subtle Light Dark Shade Overlay -->
                <div class="absolute inset-0 z-10 bg-black/25"></div>
            </div>

            <!-- Main Hero Center Text Content -->
            <div class="relative z-30 flex-grow flex flex-col items-center justify-center text-center px-4 sm:px-8 lg:px-12 pt-28 sm:pt-36 md:pt-44 pb-16 w-full">
                <div class="w-full max-w-6xl mx-auto space-y-6 sm:space-y-8">
                    
                    <!-- Hero Main Heading with Exact Amaranth Font -->
                    <h1 class="text-3xl sm:text-5xl md:text-6xl lg:text-[76px] font-bold text-white tracking-normal leading-[1.12] font-['Albert_Sans',sans-serif] drop-shadow-[0_4px_20px_rgba(0,0,0,0.8)]">
                        Denver's Top Computer &amp;<br /> Electronics Recycling Company
                    </h1>

                    <!-- Mobile-Optimized Leaf CTA Button -->
                    <div class="pt-2 flex flex-col items-center gap-4">
                        <a 
                            href="{{ url('/contact-us') }}" 
                            class="inline-flex items-center justify-center gap-3 px-7 py-3.5 sm:px-10 sm:py-4 rounded-tl-[22px] rounded-bl-[22px] rounded-tr-[22px] rounded-br-none bg-gradient-to-r from-[#035c43] via-[#035c43] to-[#035c43] hover:bg-none hover:bg-white text-white hover:text-[#035c43] border-2 border-[#035c43] font-bold text-base sm:text-xl tracking-wide shadow-2xl transition-all duration-300 group whitespace-nowrap max-w-full transform hover:scale-105"
                        >
                            <span class="font-extrabold tracking-wide">Schedule A Consultation</span>
                            <span class="text-xl sm:text-2xl transition-transform duration-300 group-hover:translate-x-2">→</span>
                        </a>
                    </div>

                </div>
            </div>

            <!-- Slide Indicator Dots Bar -->
            <div class="relative z-30 flex items-center justify-center gap-2 pb-6">
                @foreach([0, 1, 2] as $idx)
                    <button 
                        @click="prevSlide = activeSlide; activeSlide = {{ $idx }}" 
                        class="h-2 rounded-full transition-all duration-300"
                        :class="activeSlide === {{ $idx }} ? 'w-8 bg-[#035c43] shadow-[0_0_10px_#035c43]' : 'w-2 bg-white/40 hover:bg-white/70'"
                        aria-label="Go to slide {{ $idx + 1 }}"
                    ></button>
                @endforeach
            </div>
        </section>

        <!-- Section 2: IT INVESTMENT RECOVERIES Redesigned Section -->
        <section 
            id="about-section" 
            class="relative bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full overflow-hidden"
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
            <!-- Background Watermark 1: Left Faded Recycling Loop & Circuit Pattern -->
            <div class="absolute top-1/2 -left-12 -translate-y-1/2 w-72 h-72 sm:w-96 sm:h-96 pointer-events-none opacity-10 sm:opacity-15 z-0">
                <svg viewBox="0 0 500 500" class="w-full h-full text-emerald-600 fill-current">
                    <g transform="translate(100, 100) scale(0.6)">
                        <path d="M 250 40 C 350 40 430 120 430 220 L 480 220 L 400 320 L 320 220 L 370 220 C 370 150 320 100 250 100 C 210 100 175 120 150 150 L 105 105 C 140 65 190 40 250 40 Z" />
                        <path d="M 460 250 C 460 350 380 430 280 430 L 280 480 L 180 400 L 280 320 L 280 370 C 350 370 400 320 400 250 C 400 210 380 175 350 150 L 395 105 C 435 140 460 190 460 250 Z" />
                        <path d="M 40 250 C 40 150 120 70 220 70 L 220 20 L 320 100 L 220 180 L 220 130 C 150 130 100 180 100 250 C 100 290 120 325 150 350 L 105 395 C 65 360 40 310 40 250 Z" />
                    </g>
                </svg>
            </div>

            <!-- Background Watermark 2: Right Top Faded Recycling Loop -->
            <div class="absolute top-10 -right-16 w-80 h-80 sm:w-96 sm:h-96 pointer-events-none opacity-10 sm:opacity-15 z-0">
                <svg viewBox="0 0 500 500" class="w-full h-full text-emerald-600 fill-current">
                    <g transform="translate(50, 50) scale(0.8)">
                        <path d="M 250 40 C 350 40 430 120 430 220 L 480 220 L 400 320 L 320 220 L 370 220 C 370 150 320 100 250 100 C 210 100 175 120 150 150 L 105 105 C 140 65 190 40 250 40 Z" />
                        <path d="M 460 250 C 460 350 380 430 280 430 L 280 480 L 180 400 L 280 320 L 280 370 C 350 370 400 320 400 250 C 400 210 380 175 350 150 L 395 105 C 435 140 460 190 460 250 Z" />
                        <path d="M 40 250 C 40 150 120 70 220 70 L 220 20 L 320 100 L 220 180 L 220 130 C 150 130 100 180 100 250 C 100 290 120 325 150 350 L 105 395 C 65 360 40 310 40 250 Z" />
                    </g>
                </svg>
            </div>

            <!-- Background Illustration: Bottom-Right 3D Isometric IT Equipment -->
            <div class="absolute bottom-0 right-0 max-w-[240px] sm:max-w-[320px] lg:max-w-[380px] pointer-events-none z-10 opacity-90 sm:opacity-100 hidden md:block">
                <img 
                    src="{{ asset('images/isometric-servers.png') }}" 
                    alt="3D IT Equipment Servers Hardware" 
                    class="w-full h-auto object-contain transform translate-x-4 translate-y-4 filter drop-shadow-xl"
                />
            </div>

            <!-- Section Content Container -->
            <div class="relative z-20 w-full max-w-7xl mx-auto flex flex-col items-center">
                
                <!-- Main Title: IT INVESTMENT RECOVERIES -->
                <div 
                    class="transform transition-all duration-700 ease-out text-center mb-2"
                    :class="visible ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0'"
                >
                    <h2 class="text-3xl sm:text-5xl lg:text-[54px] font-black tracking-tight uppercase leading-tight font-['Albert_Sans',sans-serif]">
                        <span class="text-[#035c43]">IT INVESTMENT</span> 
                        <span class="text-[#111827] ml-2 sm:ml-3">RECOVERIES</span>
                    </h2>
                </div>

                <!-- Subheading: Computer & Electronics Recycling in Denver -->
                <div 
                    class="transform transition-all duration-700 delay-150 ease-out text-center mb-10 sm:mb-14"
                    :class="visible ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0'"
                >
                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-[#035c43] tracking-tight">
                        Computer &amp; Electronics Recycling in Denver
                    </h3>
                </div>

                <!-- Paragraph Description Container -->
                <div 
                    class="transform transition-all duration-700 delay-300 ease-out w-full max-w-5xl mb-10 text-center"
                    :class="visible ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'"
                >
                    <p class="text-gray-700 text-lg sm:text-xl lg:text-2xl leading-relaxed sm:leading-[1.75] font-normal">
                        As the leading computer and electronics recycler in Denver, Colorado, We provide secure, responsible recycling services for businesses and residents. By properly recycling your outdated computers, laptops, servers, printers, phones, TVs and other electronics in Denver, we keep hazardous electronic waste out of landfills. Bring your end-of-life electronics to our Denver e-waste recycling facility for drop-off recycling. We will safely wipe all data, dismantle old electronic equipment, and recycle the components in an environmentally responsible manner. Contact us today to schedule an electronics pickup or learn more about our secure computer and electronics recycling services conveniently located in Denver.
                    </p>
                </div>

                <!-- Bottom Center Pill CTA Button: Data Security -> -->
                <div 
                    class="transform transition-all duration-700 delay-450 ease-out"
                    :class="visible ? 'translate-y-0 opacity-100 scale-100' : 'translate-y-12 opacity-0 scale-95'"
                >
                    <a 
                        href="#services" 
                        class="inline-flex items-center justify-center gap-2.5 px-9 py-3.5 rounded-full bg-[#035c43] hover:bg-[#024734] text-white font-bold text-base sm:text-lg tracking-wide shadow-xl transition-all duration-300 group whitespace-nowrap transform hover:scale-105"
                    >
                        <span class="font-extrabold">Data Security</span>
                        <span class="text-xl transition-transform duration-200 group-hover:translate-x-1.5">→</span>
                    </a>
                </div>

            </div>
        </section>

        <!-- Clean Subtle Section Separator -->
        <div class="bg-white py-4 sm:py-6 w-full flex items-center justify-center">
            <div class="w-full max-w-5xl px-6 border-t border-gray-100"></div>
        </div>

        <!-- Section 3: Certified Service Providers Slider Section -->
        <section class="relative bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 w-full overflow-hidden">
            
            <!-- Left Background Watermark -->
            <div class="absolute top-1/2 -left-12 -translate-y-1/2 w-72 h-72 sm:w-96 sm:h-96 pointer-events-none opacity-5 sm:opacity-10 z-0">
                <svg viewBox="0 0 500 500" class="w-full h-full text-emerald-600 fill-current">
                    <g transform="translate(100, 100) scale(0.6)">
                        <path d="M 250 40 C 350 40 430 120 430 220 L 480 220 L 400 320 L 320 220 L 370 220 C 370 150 320 100 250 100 C 210 100 175 120 150 150 L 105 105 C 140 65 190 40 250 40 Z" />
                        <path d="M 460 250 C 460 350 380 430 280 430 L 280 480 L 180 400 L 280 320 L 280 370 C 350 370 400 320 400 250 C 400 210 380 175 350 150 L 395 105 C 435 140 460 190 460 250 Z" />
                        <path d="M 40 250 C 40 150 120 70 220 70 L 220 20 L 320 100 L 220 180 L 220 130 C 150 130 100 180 100 250 C 100 290 120 325 150 350 L 105 395 C 65 360 40 310 40 250 Z" />
                    </g>
                </svg>
            </div>

            <!-- Right Background Watermark -->
            <div class="absolute top-10 -right-16 w-80 h-80 sm:w-96 sm:h-96 pointer-events-none opacity-5 sm:opacity-10 z-0">
                <svg viewBox="0 0 500 500" class="w-full h-full text-emerald-600 fill-current">
                    <g transform="translate(50, 50) scale(0.8)">
                        <path d="M 250 40 C 350 40 430 120 430 220 L 480 220 L 400 320 L 320 220 L 370 220 C 370 150 320 100 250 100 C 210 100 175 120 150 150 L 105 105 C 140 65 190 40 250 40 Z" />
                        <path d="M 460 250 C 460 350 380 430 280 430 L 280 480 L 180 400 L 280 320 L 280 370 C 350 370 400 320 400 250 C 400 210 380 175 350 150 L 395 105 C 435 140 460 190 460 250 Z" />
                        <path d="M 40 250 C 40 150 120 70 220 70 L 220 20 L 320 100 L 220 180 L 220 130 C 150 130 100 180 100 250 C 100 290 120 325 150 350 L 105 395 C 65 360 40 310 40 250 Z" />
                    </g>
                </svg>
            </div>

            <div class="relative z-10 w-full max-w-7xl mx-auto text-center space-y-10 sm:space-y-12">
                <!-- Title -->
                <div class="max-w-5xl mx-auto space-y-2">
                    <h3 class="text-3xl sm:text-4xl lg:text-[46px] font-bold text-[#035c43] sm:text-[#035c43] tracking-normal">
                        We Support &amp; Do Business With
                    </h3>
                    <h2 class="text-4xl sm:text-5xl lg:text-[54px] font-black text-[#222222] tracking-tight">
                        Certified Service Providers
                    </h2>
                </div>

                <!-- Continuous Marquee Slider -->
                <div class="relative w-full overflow-hidden py-4">

                    <div class="animate-partner-marquee flex items-center space-x-16 sm:space-x-24 md:space-x-32 pr-16 sm:pr-24">
                        <!-- Department of Defense -->
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0 border border-gray-100 bg-white rounded-2xl p-4 shadow-sm hover:shadow-lg transition-shadow">
                            <img src="{{ asset('images/home-slider/khyuXt-removebg-preview-1.webp') }}" alt="Department of Defense Certification" class="max-h-full max-w-full object-contain filter drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <!-- NIST -->
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0 border border-gray-100 bg-white rounded-2xl p-4 shadow-sm hover:shadow-lg transition-shadow">
                            <img src="{{ asset('images/home-slider/lruIoO-removebg-preview.webp') }}" alt="NIST Certification" class="max-h-full max-w-full object-contain filter drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <!-- EPA -->
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0 border border-gray-100 bg-white rounded-2xl p-4 shadow-sm hover:shadow-lg transition-shadow">
                            <img src="{{ asset('images/home-slider/AEfjkp-removebg-preview-1.webp') }}" alt="EPA Certification" class="max-h-full max-w-full object-contain filter drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>

                        <!-- Seamless Loop Duplicate -->
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0 border border-gray-100 bg-white rounded-2xl p-4 shadow-sm hover:shadow-lg transition-shadow">
                            <img src="{{ asset('images/home-slider/khyuXt-removebg-preview-1.webp') }}" alt="Department of Defense Certification" class="max-h-full max-w-full object-contain filter drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0 border border-gray-100 bg-white rounded-2xl p-4 shadow-sm hover:shadow-lg transition-shadow">
                            <img src="{{ asset('images/home-slider/lruIoO-removebg-preview.webp') }}" alt="NIST Certification" class="max-h-full max-w-full object-contain filter drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0 border border-gray-100 bg-white rounded-2xl p-4 shadow-sm hover:shadow-lg transition-shadow">
                            <img src="{{ asset('images/home-slider/AEfjkp-removebg-preview-1.webp') }}" alt="EPA Certification" class="max-h-full max-w-full object-contain filter drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 4: Data Security Compliance & Continuous Auto-Looping Video Section -->
        <section class="bg-white text-gray-900 py-12 sm:py-20 px-4 sm:px-8 lg:px-16 w-full overflow-hidden">
            <div class="w-full max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">
                
                <!-- Left Side: Heading Text -->
                <div class="lg:col-span-6 space-y-6 text-left">
                    <h2 class="text-3xl sm:text-4xl md:text-[42px] font-bold text-[#035c43] leading-relaxed sm:leading-[1.55] tracking-normal">
                        From data destruction to certified recycling, We provide data security to the highest standards, in compliance with NIST, DoD, and NSA guidelines.
                    </h2>
                </div>

                <!-- Right Side: Auto-Looping Video Frame -->
                <div class="lg:col-span-6 w-full">
                    <div class="relative w-full rounded-[24px] sm:rounded-[30px] border-2 sm:border-[3px] border-[#035c43] overflow-hidden shadow-2xl bg-black aspect-video group">
                        
                        <!-- Top-Left Brand Logo Overlay -->
                        <div class="absolute top-4 left-4 z-20 pointer-events-none bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-xl shadow-md flex items-center gap-2">
                            <img 
                                src="{{ asset('images/logo.png') }}" 
                                alt="IT Investment Recoveries" 
                                class="h-6 sm:h-8 w-auto object-contain"
                            />
                        </div>

                        <!-- Auto-Looping Background HTML5 Video -->
                        <video 
                            class="w-full h-full object-cover absolute inset-0 z-10" 
                            autoplay 
                            loop 
                            muted 
                            playsinline 
                            controls
                            poster="{{ asset('images/degausser-video-poster.png') }}"
                        >
                            <source src="{{ asset('images/videos/Black-and-Blue-Simple-Countdown-Video-Collage-3.mp4') }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>

                    </div>
                </div>

            </div>
        </section>

        <!-- Section 5: Our Proud Community Partners Section -->
        <section class="relative bg-[#035c43] py-16 sm:py-24 px-4 sm:px-8 w-full overflow-hidden shadow-2xl">
            <!-- Background Circuit Pattern Overlay -->
            <div class="absolute inset-0 z-0 opacity-15 pointer-events-none">
                <svg class="w-full h-full text-emerald-300 stroke-current" viewBox="0 0 1200 600" fill="none" stroke-width="1.5">
                    <path d="M0 100h200l50 50h300l50-50h400" />
                    <path d="M100 200h150l30 30h200l40-40h300" />
                    <path d="M50 400h300l60-60h250l30 30h300" />
                    <circle cx="250" cy="150" r="5" fill="currentColor" />
                    <circle cx="600" cy="100" r="5" fill="currentColor" />
                    <circle cx="510" cy="230" r="5" fill="currentColor" />
                    <circle cx="610" cy="340" r="5" fill="currentColor" />
                </svg>
            </div>

            <div class="relative z-10 w-full max-w-7xl mx-auto text-center space-y-12 sm:space-y-16">
                <!-- Section Title: Our Proud Community Partners [✓] -->
                <div class="flex items-center justify-center gap-3 sm:gap-4 flex-wrap">
                    <h2 class="text-3xl sm:text-5xl md:text-6xl lg:text-7xl font-black tracking-normal leading-tight font-['Albert_Sans',sans-serif]">
                        <span class="text-white">Our Proud</span> 
                        <span class="text-white ml-2 sm:ml-4">Community Partners</span>
                    </h2>
                    <!-- Glass Checkbox Icon Badge -->
                    <div class="inline-flex items-center justify-center p-2.5 bg-white/10 border border-white/20 rounded-xl backdrop-blur-md shadow-lg shrink-0">
                        <svg class="w-7 h-7 sm:w-9 sm:h-9 text-white fill-none stroke-current" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="4" />
                            <path d="M9 12l2 2 4-4" />
                        </svg>
                    </div>
                </div>

                <!-- Continuous Marquee Slider for Partner Logos -->
                <div class="relative w-full overflow-hidden py-4">
                    <!-- Left & Right Gradient Fade Edges -->
                    <div class="absolute left-0 top-0 bottom-0 w-16 sm:w-28 bg-gradient-to-r from-[#035c43] to-transparent z-10 pointer-events-none"></div>
                    <div class="absolute right-0 top-0 bottom-0 w-16 sm:w-28 bg-gradient-to-l from-[#035c43] to-transparent z-10 pointer-events-none"></div>

                    <div class="animate-partner-marquee flex items-center space-x-8 sm:space-x-12 md:space-x-16 pr-8 sm:pr-12">
                        @foreach([
                            'WhatsApp_Image_2026-06-04_at_9.43.37_PM-removebg-preview.webp',
                            'WhatsApp_Image_2026-06-04_at_9.43.37_PM__1_-removebg-preview.webp',
                            'WhatsApp_Image_2026-06-04_at_9.43.38_PM-removebg-preview.webp',
                            'WhatsApp_Image_2026-06-04_at_9.43.38_PM__1_-removebg-preview.webp',
                            'WhatsApp_Image_2026-06-04_at_9.43.38_PM__2_-removebg-preview.webp',
                            'WhatsApp_Image_2026-06-04_at_9.43.39_PM__1_-removebg-preview.webp',
                            'WhatsApp_Image_2026-06-04_at_9.43.39_PM__2_-removebg-preview.webp',
                            'WhatsApp_Image_2026-06-04_at_9.43.39_PM__3_-removebg-preview.webp'
                        ] as $img)
                            <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0 p-5 rounded-2xl bg-transparent border-0 shadow-none transition-all duration-300 transform hover:scale-105 group">
                                <img src="{{ asset('images/partners/' . $img) }}" alt="Community Partner" class="max-h-full max-w-full object-contain filter drop-shadow-sm transition-transform duration-300 brightness-200" />
                            </div>
                        @endforeach

                        <!-- Duplicate Set for Seamless Infinite Loop -->
                        @foreach([
                            'WhatsApp_Image_2026-06-04_at_9.43.37_PM-removebg-preview.webp',
                            'WhatsApp_Image_2026-06-04_at_9.43.37_PM__1_-removebg-preview.webp',
                            'WhatsApp_Image_2026-06-04_at_9.43.38_PM-removebg-preview.webp',
                            'WhatsApp_Image_2026-06-04_at_9.43.38_PM__1_-removebg-preview.webp',
                            'WhatsApp_Image_2026-06-04_at_9.43.38_PM__2_-removebg-preview.webp',
                            'WhatsApp_Image_2026-06-04_at_9.43.39_PM__1_-removebg-preview.webp',
                            'WhatsApp_Image_2026-06-04_at_9.43.39_PM__2_-removebg-preview.webp',
                            'WhatsApp_Image_2026-06-04_at_9.43.39_PM__3_-removebg-preview.webp'
                        ] as $img)
                            <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0 p-5 rounded-2xl bg-transparent border-0 shadow-none transition-all duration-300 transform hover:scale-105 group">
                                <img src="{{ asset('images/partners/' . $img) }}" alt="Community Partner" class="max-h-full max-w-full object-contain filter drop-shadow-sm transition-transform duration-300 brightness-200" />
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </section>

        <!-- Section 6: Our Services Section -->
        <section id="services" class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100">
            <div class="w-full max-w-7xl mx-auto">
                
                <!-- Section Title: Our Services -->
                <div class="text-center mb-12 sm:mb-16">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-normal">
                        <span class="text-[#035c43]">Our</span> 
                        <span class="text-[#222222] ml-2">Services</span>
                    </h2>
                </div>

                <!-- 6 Service Image Cards Bento Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    
                    <!-- Card 1: Data Destruction -->
                    <a href="{{ url('/data-destruction') }}" class="block rounded-[22px] p-2 bg-white border border-gray-200/90 shadow-md hover:shadow-xl transition-all duration-300 group">
                        <div class="rounded-[16px] overflow-hidden aspect-square relative w-full bg-gray-100">
                            <img 
                                src="{{ asset('images/services/WhatsApp-Image-2026-03-03-at-2.48.49-AM.webp') }}" 
                                alt="Data Destruction" 
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent flex items-end p-5">
                                <div class="flex items-center gap-3 text-white">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-white/20 border border-white/40 flex items-center justify-center shrink-0 backdrop-blur-sm">
                                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white fill-current" viewBox="0 0 24 24">
                                            <path d="M19 4h-3.5l-1-1h-5l-1 1H5v2h14M6 19a2 2 0 002 2h8a2 2 0 002-2V7H6v12zm3.88-9.41L12 11.71l2.12-2.12 1.41 1.41L13.41 13.12l2.12 2.12-1.41 1.41L12 14.53l-2.12 2.12-1.41-1.41 2.12-2.12-2.12-2.12 1.41-1.41z"/>
                                        </svg>
                                    </div>
                                    <div class="text-xl sm:text-2xl font-extrabold text-white leading-tight drop-shadow-md">
                                        Data<br />Destruction
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Card 2: E Waste Recycling -->
                    <a href="{{ url('/e-waste-recycling') }}" class="block rounded-[22px] p-2 bg-white border border-gray-200/90 shadow-md hover:shadow-xl transition-all duration-300 group">
                        <div class="rounded-[16px] overflow-hidden aspect-square relative w-full bg-gray-100">
                            <img 
                                src="{{ asset('images/services/WhatsApp-Image-2026-03-03-at-2.59.30-AM-300x300.webp') }}" 
                                alt="E Waste Recycling" 
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent flex items-end p-5">
                                <div class="flex items-center gap-3 text-white">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-white/20 border border-white/40 flex items-center justify-center shrink-0 backdrop-blur-sm">
                                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white fill-current" viewBox="0 0 24 24">
                                            <path d="M19 4h-3.5l-1-1h-5l-1 1H5v2h14M6 19a2 2 0 002 2h8a2 2 0 002-2V7H6v12zm4-9h4v2h-4v-2zm0 4h4v2h-4v-2z"/>
                                        </svg>
                                    </div>
                                    <div class="text-xl sm:text-2xl font-extrabold text-white leading-tight drop-shadow-md">
                                        E Waste<br />Recycling
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Card 3: Liquidation Reuse Resale -->
                    <a href="{{ url('/it-asset-management') }}" class="block rounded-[22px] p-2 bg-white border border-gray-200/90 shadow-md hover:shadow-xl transition-all duration-300 group">
                        <div class="rounded-[16px] overflow-hidden aspect-square relative w-full bg-gray-100">
                            <img 
                                src="{{ asset('images/services/Asset-Removal-Services-.webp') }}" 
                                alt="Liquidation Reuse Resale" 
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent flex items-end p-5">
                                <div class="flex items-center gap-3 text-white">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-white/20 border border-white/40 flex items-center justify-center shrink-0 backdrop-blur-sm">
                                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white fill-current" viewBox="0 0 24 24">
                                            <path d="M21 2H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h7v2H8v2h8v-2h-2v-2h7c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H3V4h18v12z"/>
                                        </svg>
                                    </div>
                                    <div class="text-xl sm:text-2xl font-extrabold text-white leading-tight drop-shadow-md">
                                        Liquidation<br />Reuse Resale
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Card 4: Charity Donation -->
                    <a href="{{ url('/charity-donations') }}" class="block rounded-[22px] p-2 bg-white border border-gray-200/90 shadow-md hover:shadow-xl transition-all duration-300 group">
                        <div class="rounded-[16px] overflow-hidden aspect-square relative w-full bg-gray-100">
                            <img 
                                src="{{ asset('images/services/business-partnership-infographics_1284-4534-e1705103864476.webp') }}" 
                                alt="Charity Donation" 
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent flex items-end p-5">
                                <div class="flex items-center gap-3 text-white">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-white/20 border border-white/40 flex items-center justify-center shrink-0 backdrop-blur-sm">
                                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white fill-current" viewBox="0 0 24 24">
                                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                        </svg>
                                    </div>
                                    <div class="text-xl sm:text-2xl font-extrabold text-white leading-tight drop-shadow-md">
                                        Charity<br />Donation
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Card 5: Partner & Service Provider -->
                    <a href="{{ url('/community-partners') }}" class="block rounded-[22px] p-2 bg-white border border-gray-200/90 shadow-md hover:shadow-xl transition-all duration-300 group">
                        <div class="rounded-[16px] overflow-hidden aspect-square relative w-full bg-gray-100">
                            <img 
                                src="{{ asset('images/services/Partners-Service-Providers.webp') }}" 
                                alt="Partner & Service Provider" 
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent flex items-end p-5">
                                <div class="flex items-center gap-3 text-white">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-white/20 border border-white/40 flex items-center justify-center shrink-0 backdrop-blur-sm">
                                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white fill-current" viewBox="0 0 24 24">
                                            <path d="M11.5 13.5L9.75 11.75C9.36 11.36 8.73 11.36 8.34 11.75C7.95 12.14 7.95 12.77 8.34 13.16L10.8 15.62C11.19 16.01 11.82 16.01 12.21 15.62L16.66 11.17C17.05 10.78 17.05 10.15 16.66 9.76C16.27 9.37 15.64 9.37 15.25 9.76L11.5 13.5Z"/>
                                            <path d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM19 19H5V5H19V19Z"/>
                                        </svg>
                                    </div>
                                    <div class="text-xl sm:text-2xl font-extrabold text-white leading-tight drop-shadow-md">
                                        Partner &amp;<br />Service Provider
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Card 6: Rebate Reuse Price List -->
                    <a href="{{ url('/e-waste-recycling') }}" class="block rounded-[22px] p-2 bg-white border border-gray-200/90 shadow-md hover:shadow-xl transition-all duration-300 group">
                        <div class="rounded-[16px] overflow-hidden aspect-square relative w-full bg-gray-100">
                            <img 
                                src="{{ asset('images/services/WhatsApp-Image-2026-03-03-at-2.48.49-AM-1-300x300.webp') }}" 
                                alt="Rebate Reuse Price List" 
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent flex items-end p-5">
                                <div class="flex items-center gap-3 text-white">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-white/20 border border-white/40 flex items-center justify-center shrink-0 backdrop-blur-sm">
                                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white fill-current" viewBox="0 0 24 24">
                                            <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                        </svg>
                                    </div>
                                    <div class="text-xl sm:text-2xl font-extrabold text-white leading-tight drop-shadow-md">
                                        Rebate Reuse<br />Price List
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                </div>

            </div>
        </section>

        <!-- Section 7: Our Proud Business Partners Section -->
        <div class="relative w-full bg-white overflow-hidden py-10 sm:py-16 border-t border-gray-100">
            <!-- Slanted Deep Green Outer Section Container -->
            <section class="bg-[#035c43] py-16 sm:py-24 transform -skew-y-2 origin-top-left shadow-2xl w-full">
                <!-- Inner Content Container -->
                <div class="transform skew-y-2 w-full max-w-7xl mx-auto px-4 sm:px-8 text-center space-y-12">
                    
                    <!-- Section Title: Our Proud Business Partners -->
                    <div class="flex items-center justify-center gap-4 flex-wrap">
                        <h2 class="text-4xl sm:text-6xl md:text-7xl lg:text-[72px] font-black tracking-normal leading-none">
                            <span class="text-white">Our Proud</span> 
                            <span class="text-white ml-2 sm:ml-4">Business Partners</span>
                        </h2>
                    </div>

                    <!-- Dual Row Marquee Sliders Moving in OPPOSITE Directions -->
                    <div class="relative w-full overflow-hidden space-y-10 py-6">
                        
                        <!-- Row 1: Moving RIGHT to LEFT -->
                        <div class="animate-marquee-left flex items-center space-x-8 sm:space-x-12 md:space-x-16">
                            <!-- Row 1 Logos -->
                            @foreach([
                                'WhatsApp_Image_2026-06-05_at_1.23.42_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.23.54_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.23.56_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.23.56_AM__1_-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.23.56_AM__2_-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.23.57_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.23.58_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.23.58_AM__1_-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.23.59_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.00_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.00_AM__1_-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.01_AM-removebg-preview.webp'
                            ] as $bImg)
                                <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0 p-5 rounded-2xl bg-transparent border-0 shadow-none transition-all duration-300 transform hover:scale-105 group">
                                    <img src="{{ asset('images/services/' . $bImg) }}" alt="Business Partner" class="max-h-full max-w-full object-contain filter drop-shadow-sm transition-transform duration-300 brightness-200" />
                                </div>
                            @endforeach

                            <!-- Row 1 Logos (Set 2 for Loop) -->
                            @foreach([
                                'WhatsApp_Image_2026-06-05_at_1.23.42_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.23.54_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.23.56_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.23.56_AM__1_-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.23.56_AM__2_-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.23.57_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.23.58_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.23.58_AM__1_-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.23.59_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.00_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.00_AM__1_-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.01_AM-removebg-preview.webp'
                            ] as $bImg)
                                <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0 p-5 rounded-2xl bg-transparent border-0 shadow-none transition-all duration-300 transform hover:scale-105 group">
                                    <img src="{{ asset('images/services/' . $bImg) }}" alt="Business Partner" class="max-h-full max-w-full object-contain filter drop-shadow-sm transition-transform duration-300 brightness-200" />
                                </div>
                            @endforeach
                        </div>

                        <!-- Row 2: Moving LEFT to RIGHT -->
                        <div class="animate-marquee-right flex items-center space-x-8 sm:space-x-12 md:space-x-16">
                            <!-- Row 2 Logos -->
                            @foreach([
                                'WhatsApp_Image_2026-06-05_at_1.24.02_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.02_AM__1_-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.03_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.04_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.04_AM__1_-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.04_AM__2_-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.05_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.05_AM__1_-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.05_AM__2_-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.06_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.06_AM__1_-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.06_AM__2_-removebg-preview.webp'
                            ] as $bImg2)
                                <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0 p-5 rounded-2xl bg-transparent border-0 shadow-none transition-all duration-300 transform hover:scale-105 group">
                                    <img src="{{ asset('images/services/' . $bImg2) }}" alt="Business Partner" class="max-h-full max-w-full object-contain filter drop-shadow-sm transition-transform duration-300 brightness-200" />
                                </div>
                            @endforeach

                            <!-- Row 2 Logos (Set 2 for Loop) -->
                            @foreach([
                                'WhatsApp_Image_2026-06-05_at_1.24.02_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.02_AM__1_-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.03_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.04_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.04_AM__1_-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.04_AM__2_-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.05_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.05_AM__1_-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.05_AM__2_-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.06_AM-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.06_AM__1_-removebg-preview.webp',
                                'WhatsApp_Image_2026-06-05_at_1.24.06_AM__2_-removebg-preview.webp'
                            ] as $bImg2)
                                <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0 p-5 rounded-2xl bg-transparent border-0 shadow-none transition-all duration-300 transform hover:scale-105 group">
                                    <img src="{{ asset('images/services/' . $bImg2) }}" alt="Business Partner" class="max-h-full max-w-full object-contain filter drop-shadow-sm transition-transform duration-300 brightness-200" />
                                </div>
                            @endforeach
                        </div>

                    </div>

            </section>
        </div>

        <!-- Section 8: Our Happy Clients Section -->
        <section 
            class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100 overflow-hidden relative"
            x-data="{
                activeSlide: 0,
                maxSlide: 4,
                reviews: [
                    {
                        name: 'David Jones',
                        rating: 4,
                        text: `I have to say, I'm really impressed by professionalism and efficiency of It-Investment Recoveries team. If you're looking for hassle-free recycling, I highly recommend them`,
                        image: '{{ asset('images/home/reviews/1.webp') }}'
                    },
                    {
                        name: 'David Martinelli',
                        rating: 3,
                        text: `I recently hired It-Investment Recoveries for computer recycling in Denver, and I couldn't be happier. Their team was punctual, professional, and took care of everything. Highly recommended!`,
                        image: '{{ asset('images/home/reviews/2.webp') }}'
                    },
                    {
                        name: 'Mark Richardson',
                        rating: 5,
                        text: `Their commitment to data security and environmentally responsible practices is commendable. A reliable choice for anyone looking to recycle electronics.`,
                        image: '{{ asset('images/home/reviews/3.webp') }}'
                    },
                    {
                        name: 'Michael Brown',
                        rating: 4,
                        text: `Every business and resident in Denver should know about their electronics recycling services. They make the whole process fast, easy and secure.`,
                        image: '{{ asset('images/home/reviews/4.webp') }}'
                    },
                    {
                        name: 'Shams W.Pawel',
                        rating: 3,
                        text: `With affordable and efficient Computer recycling in Denver they exceeded my expectations. They offer reasonable rates, making recycling accessible to all on a budget.`,
                        image: '{{ asset('images/home/reviews/5.webp') }}'
                    },
                    {
                        name: 'David Jones',
                        rating: 4,
                        text: `I have to say, I'm really impressed by professionalism and efficiency of It-Investment Recoveries team. If you're looking for hassle-free recycling, I highly recommend them`,
                        image: '{{ asset('images/home/reviews/6.webp') }}'
                    },
                    {
                        name: 'David Martinelli',
                        rating: 3,
                        text: `I recently hired It-Investment Recoveries for computer recycling in Denver, and I couldn't be happier. Their team was punctual, professional, and took care of everything. Highly recommended!`,
                        image: '{{ asset('images/home/reviews/7.webp') }}'
                    }
                ]
            }"
        >
            <div class="w-full max-w-7xl mx-auto flex flex-col items-center text-center relative z-10 space-y-12">
                
                <!-- Section Title: Our Happy Clients 🕴️ -->
                <div class="flex items-center justify-center gap-3">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-normal font-['Albert_Sans',sans-serif]">
                        <span class="text-[#222222]">Our</span> 
                        <span class="text-[#035c43] ml-2">Happy Clients</span>
                    </h2>
                    <span class="text-4xl sm:text-5xl">🕴️</span>
                </div>

                <!-- Testimonials Slider Container -->
                <div class="relative w-full overflow-hidden py-2">
                    <div 
                        class="flex transition-transform duration-500 ease-out"
                        :style="`transform: translateX(-${activeSlide * (100 / (window.innerWidth >= 1024 ? 3 : window.innerWidth >= 768 ? 2 : 1))}%)`"
                    >
                        <template x-for="(review, index) in reviews" :key="index">
                            <div class="w-full md:w-1/2 lg:w-1/3 shrink-0 px-3 sm:px-4 flex">
                                <div class="bg-white rounded-2xl border-2 border-[#035c43] p-7 sm:p-9 shadow-md hover:shadow-xl transition-all duration-300 flex flex-col justify-between text-center w-full relative group overflow-hidden">
                                    
                                    <!-- Stars Rating -->
                                    <div class="space-y-4 pt-2">
                                        <div class="flex items-center justify-center gap-1 text-amber-400 text-lg sm:text-xl">
                                            <template x-for="star in 5" :key="star">
                                                <span x-text="star <= review.rating ? '★' : '☆'" :class="star <= review.rating ? 'text-amber-400' : 'text-amber-300/60'"></span>
                                            </template>
                                        </div>

                                        <!-- Review Body Text -->
                                        <p class="text-gray-600 text-base sm:text-lg leading-relaxed font-medium py-2" x-text="review.text"></p>
                                    </div>

                                    <!-- Card Footer Profile & Quotation Icon -->
                                    <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-100/80 relative">
                                        <div class="flex items-center gap-4 shrink-0 text-left">
                                            <img 
                                                :src="review.image" 
                                                :alt="review.name" 
                                                class="w-14 h-14 sm:w-16 sm:h-16 rounded-full object-cover shadow-sm border-2 border-white ring-2 ring-gray-100 shrink-0"
                                            />
                                            <div class="font-extrabold text-gray-900 text-lg sm:text-xl" x-text="review.name"></div>
                                        </div>
                                        
                                        <!-- Quotation Icon -->
                                        <div class="text-[#a5f3fc] font-serif text-5xl sm:text-6xl leading-none select-none opacity-80 pl-2">”</div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Pagination Dots Controls -->
                <div class="flex items-center justify-center gap-2.5 mt-8">
                    <template x-for="dot in 5" :key="dot">
                        <button 
                            @click="activeSlide = dot - 1" 
                            class="h-3 rounded-full transition-all duration-300 focus:outline-none"
                            :class="activeSlide === (dot - 1) ? 'bg-[#2563eb] w-3 shadow-md' : 'bg-gray-300 w-3 hover:bg-gray-400'"
                            :aria-label="`Go to review slide ${dot}`"
                        ></button>
                    </template>
                </div>

            </div>
        </section>

        <!-- Section Divider Banner with Organization IT Asset Recovery Statement -->
        <section class="bg-gradient-to-r from-[#035c43] via-[#035c43] to-[#035c43] text-white py-12 px-6 sm:px-12 w-full text-center shadow-lg border-y border-emerald-600/30">
            <div class="max-w-6xl mx-auto space-y-3">
                <p class="text-xl sm:text-2xl lg:text-3xl font-extrabold leading-snug tracking-tight text-emerald-300 font-['Albert_Sans',sans-serif]">
                    We Help Government/Private Organizations Liquidate, Donate, Or Recycle Their IT and Electronic Assets Securely and Responsibly.
                </p>
            </div>
        </section>

        <!-- Section 9: Recycle Your Electronics Securely Section (Redesigned Card Banner) -->
        <section class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-6 lg:px-8 w-full border-t border-gray-100 relative overflow-hidden">
            
            <!-- Ambient Background Glow -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[900px] h-[400px] bg-emerald-400/10 blur-[120px] pointer-events-none rounded-full"></div>

            <!-- Elevated Feature Banner Card Container (Increased Width to max-w-7xl) -->
            <div class="w-full max-w-7xl mx-auto p-8 sm:p-14 lg:p-16 rounded-[32px] bg-gradient-to-br from-emerald-50/90 via-white to-emerald-50/50 border-2 border-emerald-500/20 shadow-2xl relative text-center flex flex-col items-center space-y-7 z-10">
                
                <!-- Glowing E-Waste Shield / Recycling Icon Graphic Accent (No Extra Text) -->
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br from-[#035c43] to-[#035c43] border-2 border-emerald-400/50 flex items-center justify-center text-white shadow-xl shadow-emerald-500/30 transform hover:rotate-6 transition-transform duration-300 shrink-0">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white fill-current" viewBox="0 0 24 24">
                        <path d="M19 4h-3.5l-1-1h-5l-1 1H5v2h14M6 19a2 2 0 002 2h8a2 2 0 002-2V7H6v12zm4-9h4v2h-4v-2zm0 4h4v2h-4v-2z"/>
                    </svg>
                </div>

                <!-- Section Title: RECYCLE YOUR ELECTRONICS SECURELY -->
                <h2 class="text-3xl sm:text-5xl lg:text-6xl font-black uppercase tracking-tight text-gray-900 leading-tight font-['Albert_Sans',sans-serif]">
                    <span>RECYCLE YOUR</span> 
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#035c43] via-[#035c43] to-[#035c43] ml-2 sm:ml-3">ELECTRONICS SECURELY</span>
                </h2>

                <!-- Paragraph Description (Expanded Width to max-w-5xl) -->
                <p class="max-w-5xl text-gray-700 text-base sm:text-lg lg:text-xl leading-relaxed font-semibold">
                    We Help Government/Private Organizations Liquidate, Donate, Or Recycle Their      IT and Electronic Assets Securely and Responsibly. With experience across public, private, nonprofit, and a vast array of industries, we can efficiently meet the needs of any organization.
                </p>

                <!-- CTA Leaf Pill Button: Schedule A Pickup -> -->
                <div class="pt-3">
                    <a 
                        href="{{ url('/contact-us') }}#pickup-inquiry" 
                        class="inline-flex items-center justify-center gap-3 px-9 py-4 sm:px-11 sm:py-4.5 rounded-tl-[28px] rounded-bl-[28px] rounded-tr-[28px] rounded-br-none bg-gradient-to-r from-[#035c43] via-[#035c43] to-[#035c43] hover:from-white hover:to-white text-white hover:text-[#035c43] border-2 border-[#035c43] font-bold text-base sm:text-xl tracking-wide shadow-xl shadow-emerald-500/30 transition-all duration-300 group whitespace-nowrap transform hover:scale-105"
                    >
                        <span class="font-extrabold tracking-wide">Schedule A Pickup</span>
                        <span class="text-xl sm:text-2xl transition-transform duration-300 group-hover:translate-x-2">→</span>
                    </a>
                </div>

            </div>
        </section>

        <!-- Section 10: Talk with an Expert Ratings Banner with Background Image Overlay -->
        <section class="relative bg-[#035c43] text-white py-16 sm:py-24 px-6 sm:px-12 lg:px-16 w-full shadow-inner overflow-hidden">
            
            <!-- Subtle Background Cityscape Overlay -->
            <div class="absolute inset-0 z-0 pointer-events-none">
                <img 
                    src="{{ asset('images/home-banner.webp') }}" 
                    alt="Denver Background" 
                    class="w-full h-full object-cover opacity-25 mix-blend-overlay filter contrast-125 brightness-110 scale-105"
                />
                <div class="absolute inset-0 bg-gradient-to-r from-[#035c43]/90 via-[#035c43]/85 to-[#035c43]/90"></div>
            </div>

            <!-- Foreground Content Container -->
            <div class="relative z-10 w-full max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                
                <!-- Left Side: Title, Description, and Contact Buttons -->
                <div class="lg:col-span-7 space-y-6 text-left">
                    <h2 class="text-3xl sm:text-5xl font-black text-white tracking-normal">
                        Talk with an Expert
                    </h2>
                    <p class="text-emerald-100 text-base sm:text-lg max-w-xl font-normal leading-relaxed">
                        Everyone Can Help Create a Full Circle. Let's Recycle Together. Call or email us to talk with an expert.
                    </p>
                    <div class="flex items-center gap-4 flex-wrap pt-2">
                        <a 
                            href="{{ url('/contact-us') }}" 
                            class="bg-[#035c43] hover:bg-white text-white hover:text-[#035c43] border border-emerald-400/40 px-8 py-3.5 rounded-full font-extrabold text-sm sm:text-base shadow-lg transition duration-300 transform hover:scale-105"
                        >
                            Contact Us
                        </a>
                        <a 
                            href="tel:+13034724701" 
                            class="bg-[#035c43] hover:bg-white text-white hover:text-[#035c43] border border-emerald-400/40 px-8 py-3.5 rounded-full font-extrabold text-sm sm:text-base shadow-lg transition duration-300 flex items-center gap-2 transform hover:scale-105"
                        >
                            <span>📞</span>
                            <span>+1-303-472-4701</span>
                        </a>
                    </div>
                </div>

                <!-- Right Side: Google & BBB Ratings Cards -->
                <div class="lg:col-span-5 flex items-center justify-start lg:justify-end gap-6 sm:gap-10 pt-6 lg:pt-0">
                    
                    <!-- Rating 1: Google Reviews (4.8) -->
                    <div class="text-center space-y-1">
                        <div class="text-4xl sm:text-6xl font-black text-emerald-100 leading-none">4.8</div>
                        <div class="flex items-center justify-center gap-0.5 text-amber-300 text-sm">
                            <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                        </div>
                        <div class="text-xs sm:text-sm font-semibold text-emerald-100">2,394 Ratings</div>
                        <div class="text-xs font-bold text-white uppercase tracking-wider">Google Reviews</div>
                    </div>

                    <!-- Rating 2: BBB Rating (A+) -->
                    <div class="text-center space-y-1">
                        <div class="text-4xl sm:text-6xl font-black text-emerald-100 leading-none">A+</div>
                        <div class="flex items-center justify-center gap-0.5 text-amber-300 text-sm">
                            <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                        </div>
                        <div class="text-xs sm:text-sm font-semibold text-emerald-100">125 Client Reviews</div>
                        <div class="text-xs font-bold text-white uppercase tracking-wider">BBB Rating</div>
                    </div>

                </div>

            </div>
        </section>

        <!-- Section 11: Subscribe to Our Newsletter Section (Light Card Style for Visual Contrast with Section 10) -->
        <section class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 w-full border-t border-gray-100 overflow-hidden">
            <!-- Inner Content Card Container -->
            <div class="w-full max-w-7xl mx-auto p-8 sm:p-12 lg:p-16 rounded-[32px] bg-gradient-to-br from-emerald-50/80 via-white to-emerald-50/40 border border-emerald-100 shadow-xl flex flex-col lg:flex-row items-center justify-between gap-10 lg:gap-16 relative overflow-hidden">
                
                <!-- Background Decorative Pattern -->
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-emerald-400/10 rounded-full blur-3xl pointer-events-none"></div>

                <!-- Left Text Section -->
                <div class="space-y-4 text-center lg:text-left max-w-2xl relative z-10">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-100 border border-emerald-200/80 text-emerald-800 text-xs sm:text-sm font-bold uppercase tracking-wider">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>Stay Informed &amp; Eco-Compliant</span>
                    </div>
                    <h2 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight text-gray-900 font-['Albert_Sans',sans-serif]">
                        Subscribe to <span class="text-[#035c43]">Our Newsletter</span>
                    </h2>
                    <p class="text-gray-600 text-base sm:text-lg font-normal leading-relaxed">
                        Get the latest e-waste recycling insights, data security best practices, and Denver recycling pickup schedules delivered to your inbox.
                    </p>
                </div>

                <!-- Right Newsletter Input Form -->
                <div class="w-full max-w-md lg:max-w-lg shrink-0 relative z-10">
                    <form class="relative flex items-center bg-white p-2 rounded-full shadow-lg border-2 border-emerald-500/20 focus-within:border-emerald-500 transition-all duration-300" @submit.prevent>
                        <div class="pl-4 pr-2 text-emerald-600">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input 
                            type="email" 
                            placeholder="Enter your email address" 
                            class="w-full bg-transparent text-gray-800 placeholder-gray-400 px-2 py-3 sm:py-3.5 focus:outline-none font-medium text-sm sm:text-base"
                            required
                        />
                        <button 
                            type="submit" 
                            class="bg-gradient-to-r from-[#035c43] to-[#035c43] hover:from-[#035c43] hover:to-[#035c43] text-white font-extrabold px-6 sm:px-8 py-3 sm:py-3.5 rounded-full shadow-md transition-all duration-300 text-sm sm:text-base shrink-0 flex items-center gap-2 group transform hover:scale-105"
                        >
                            <span>Subscribe</span>
                            <span class="text-lg transition-transform duration-200 group-hover:translate-x-1">→</span>
                        </button>
                    </form>
                    <p class="text-xs text-gray-500 text-center lg:text-left mt-3 font-medium">
                        🔒 We respect your privacy. Unsubscribe at any time with 1-click.
                    </p>
                </div>

            </div>
        </section>

        <!-- Full Width Main Site Footer Component -->
        <x-footer />
    </body>
</html>
