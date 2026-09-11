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

            @keyframes aboutRecycleSpin {
                0% { transform: rotate(0deg) scale(1); }
                50% { transform: rotate(180deg) scale(1.04); }
                100% { transform: rotate(360deg) scale(1); }
            }

            @keyframes aboutFloat {
                0%, 100% { transform: translate3d(0, 0, 0); }
                50% { transform: translate3d(0, -18px, 0); }
            }

            @keyframes aboutCircuitDash {
                0% { stroke-dashoffset: 180; }
                100% { stroke-dashoffset: 0; }
            }

            @keyframes aboutScan {
                0% { transform: translateY(-100%); opacity: 0; }
                15%, 70% { opacity: 1; }
                100% { transform: translateY(820px); opacity: 0; }
            }

            @keyframes aboutPulse {
                0%, 100% { opacity: .16; transform: scale(.96); }
                50% { opacity: .32; transform: scale(1.04); }
            }

            @keyframes aboutShine {
                0%, 58% { transform: translateX(-135%) rotate(18deg); opacity: 0; }
                70% { opacity: 1; }
                100% { transform: translateX(135%) rotate(18deg); opacity: 0; }
            }

            @keyframes certifiedTrack {
                0% { transform: translateX(0); }
                100% { transform: translateX(-50%); }
            }

            @keyframes certifiedGridShift {
                0% { background-position: 0 0; }
                100% { background-position: 44px 44px; }
            }

            @keyframes certifiedSweep {
                0%, 55% { transform: translateX(-120%) rotate(16deg); opacity: 0; }
                68% { opacity: 1; }
                100% { transform: translateX(120%) rotate(16deg); opacity: 0; }
            }

            @keyframes securityOrbit {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            @keyframes securitySignal {
                0%, 100% { opacity: .14; transform: scale(.96); }
                50% { opacity: .28; transform: scale(1.05); }
            }

            @keyframes securityMediaFloat {
                0%, 100% { transform: translate3d(0, 0, 0); }
                50% { transform: translate3d(0, -10px, 0); }
            }

            @keyframes securityLineMove {
                0% { stroke-dashoffset: 160; }
                100% { stroke-dashoffset: 0; }
            }

            @keyframes communityPartnerTrack {
                0% { transform: translateX(0); }
                100% { transform: translateX(-50%); }
            }

            @keyframes communityPartnerGlow {
                0%, 100% { opacity: .14; transform: scale(.94); }
                50% { opacity: .28; transform: scale(1.04); }
            }

            @keyframes communityPartnerSweep {
                0%, 62% { transform: translateX(-120%) rotate(14deg); opacity: 0; }
                74% { opacity: 1; }
                100% { transform: translateX(120%) rotate(14deg); opacity: 0; }
            }

            .about-recycle-spin {
                animation: aboutRecycleSpin 34s linear infinite;
                transform-origin: center;
            }

            .about-hardware-float {
                animation: aboutFloat 6.5s ease-in-out infinite;
            }

            .about-circuit-line {
                stroke-dasharray: 14 18;
                animation: aboutCircuitDash 18s linear infinite;
            }

            .about-scan-line {
                animation: aboutScan 6s ease-in-out infinite;
            }

            .about-pulse {
                animation: aboutPulse 4.5s ease-in-out infinite;
            }

            .about-cta-shine {
                position: relative;
                overflow: hidden;
            }

            .about-cta-shine::after {
                content: "";
                position: absolute;
                inset: -90% -35%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .34), transparent);
                animation: aboutShine 3.8s ease-in-out infinite;
            }

            .about-cta-shine > * {
                position: relative;
                z-index: 1;
            }

            .certified-provider-marquee {
                display: flex;
                width: max-content;
                animation: certifiedTrack 48s linear infinite;
                will-change: transform;
            }

            .certified-provider-marquee:hover {
                animation-play-state: paused;
            }

            .certified-grid-motion {
                animation: certifiedGridShift 18s linear infinite;
            }

            .certified-logo-card {
                position: relative;
                isolation: isolate;
                overflow: hidden;
                border-radius: 8px;
            }

            .certified-logo-card::after {
                content: "";
                position: absolute;
                inset: -120% -55%;
                background: linear-gradient(90deg, transparent, rgba(3, 92, 67, .08), rgba(255, 255, 255, .8), transparent);
                animation: certifiedSweep 5.6s ease-in-out infinite;
                z-index: 0;
            }

            .certified-logo-card img {
                position: relative;
                z-index: 1;
            }

            .certified-logo-card span {
                z-index: 1;
            }

            .security-orbit {
                animation: securityOrbit 38s linear infinite;
                transform-origin: center;
            }

            .security-signal {
                animation: securitySignal 4.8s ease-in-out infinite;
            }

            .security-media-float {
                animation: securityMediaFloat 6.8s ease-in-out infinite;
            }

            .security-line {
                stroke-dasharray: 12 16;
                animation: securityLineMove 16s linear infinite;
            }

            .community-partner-marquee {
                display: flex;
                width: max-content;
                animation: communityPartnerTrack 38s linear infinite;
                will-change: transform;
            }

            .community-partner-marquee:hover {
                animation-play-state: paused;
            }

            .community-partner-glow {
                animation: communityPartnerGlow 5s ease-in-out infinite;
            }

            .community-logo-tile {
                position: relative;
                isolation: isolate;
                overflow: hidden;
                border-radius: 8px;
            }

            .community-logo-tile::after {
                content: "";
                position: absolute;
                inset: -120% -55%;
                z-index: 0;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .16), transparent);
                animation: communityPartnerSweep 6.5s ease-in-out infinite;
            }

            .community-logo-tile img {
                position: relative;
                z-index: 1;
            }

            .community-logo-tile span {
                z-index: 1;
            }

            @media (prefers-reduced-motion: reduce) {
                .about-recycle-spin,
                .about-hardware-float,
                .about-circuit-line,
                .about-scan-line,
                .about-pulse,
                .about-cta-shine::after,
                .certified-provider-marquee,
                .certified-grid-motion,
                .certified-logo-card::after,
                .security-orbit,
                .security-signal,
                .security-media-float,
                .security-line,
                .community-partner-marquee,
                .community-partner-glow,
                .community-logo-tile::after {
                    animation: none !important;
                }
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
                <div class="w-full max-w-7xl mx-auto space-y-6 sm:space-y-8">

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
            class="relative isolate bg-white text-gray-900 py-16 sm:py-20 lg:py-24 px-4 sm:px-8 lg:px-16 w-full overflow-hidden"
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
            <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
                <div
                    class="absolute inset-0 opacity-[0.055]"
                    style="background-image: linear-gradient(rgba(3, 92, 67, .22) 1px, transparent 1px), linear-gradient(90deg, rgba(3, 92, 67, .22) 1px, transparent 1px); background-size: 44px 44px;"
                ></div>
                <div class="absolute left-1/2 top-0 h-32 w-[88vw] -translate-x-1/2 bg-gradient-to-b from-[#035c43]/10 to-transparent"></div>
                <div class="about-scan-line absolute left-0 top-0 h-28 w-full bg-gradient-to-b from-transparent via-[#035c43]/10 to-transparent"></div>
                <div class="absolute -bottom-20 left-0 h-48 w-full bg-gradient-to-t from-[#035c43]/[0.06] to-transparent"></div>
            </div>

            <!-- Background Watermark 1: Left Faded Recycling Loop & Circuit Pattern -->
            <div class="absolute top-1/2 -left-20 -translate-y-1/2 w-72 h-72 sm:w-[430px] sm:h-[430px] pointer-events-none opacity-10 sm:opacity-15 z-0">
                <svg viewBox="0 0 500 500" class="about-recycle-spin w-full h-full text-emerald-600 fill-current">
                    <g transform="translate(100, 100) scale(0.6)">
                        <path d="M 250 40 C 350 40 430 120 430 220 L 480 220 L 400 320 L 320 220 L 370 220 C 370 150 320 100 250 100 C 210 100 175 120 150 150 L 105 105 C 140 65 190 40 250 40 Z" />
                        <path d="M 460 250 C 460 350 380 430 280 430 L 280 480 L 180 400 L 280 320 L 280 370 C 350 370 400 320 400 250 C 400 210 380 175 350 150 L 395 105 C 435 140 460 190 460 250 Z" />
                        <path d="M 40 250 C 40 150 120 70 220 70 L 220 20 L 320 100 L 220 180 L 220 130 C 150 130 100 180 100 250 C 100 290 120 325 150 350 L 105 395 C 65 360 40 310 40 250 Z" />
                    </g>
                </svg>
            </div>

            <!-- Background Watermark 2: Right Top Faded Recycling Loop -->
            <div class="absolute -top-8 -right-20 w-80 h-80 sm:w-[470px] sm:h-[470px] pointer-events-none opacity-10 sm:opacity-15 z-0">
                <svg viewBox="0 0 500 500" class="about-recycle-spin w-full h-full text-emerald-600 fill-current [animation-direction:reverse]">
                    <g transform="translate(50, 50) scale(0.8)">
                        <path d="M 250 40 C 350 40 430 120 430 220 L 480 220 L 400 320 L 320 220 L 370 220 C 370 150 320 100 250 100 C 210 100 175 120 150 150 L 105 105 C 140 65 190 40 250 40 Z" />
                        <path d="M 460 250 C 460 350 380 430 280 430 L 280 480 L 180 400 L 280 320 L 280 370 C 350 370 400 320 400 250 C 400 210 380 175 350 150 L 395 105 C 435 140 460 190 460 250 Z" />
                        <path d="M 40 250 C 40 150 120 70 220 70 L 220 20 L 320 100 L 220 180 L 220 130 C 150 130 100 180 100 250 C 100 290 120 325 150 350 L 105 395 C 65 360 40 310 40 250 Z" />
                    </g>
                </svg>
            </div>

            <svg class="absolute right-0 top-1/2 z-0 hidden h-[520px] w-[620px] -translate-y-1/2 pointer-events-none opacity-25 lg:block" viewBox="0 0 620 520" fill="none" aria-hidden="true">
                <path class="about-circuit-line" d="M48 95 H190 C226 95 232 134 265 134 H386 C421 134 426 174 462 174 H588" stroke="#035c43" stroke-width="2" stroke-linecap="round" />
                <path class="about-circuit-line" d="M0 262 H132 C168 262 174 224 210 224 H312 C350 224 354 266 390 266 H620" stroke="#035c43" stroke-width="2" stroke-linecap="round" />
                <path class="about-circuit-line" d="M84 408 H222 C255 408 260 366 294 366 H420 C455 366 462 326 494 326 H606" stroke="#035c43" stroke-width="2" stroke-linecap="round" />
                <circle cx="190" cy="95" r="5" fill="#035c43" />
                <circle cx="386" cy="134" r="5" fill="#035c43" />
                <circle cx="132" cy="262" r="5" fill="#035c43" />
                <circle cx="420" cy="366" r="5" fill="#035c43" />
            </svg>

            <!-- Section Content Container -->
            <div class="relative z-20 w-full max-w-7xl mx-auto">
                <div class="grid items-center gap-10 lg:grid-cols-[minmax(0,1fr)_minmax(330px,430px)] lg:gap-16">
                    <div class="relative text-center lg:text-left">
                        <div class="absolute -left-6 top-0 hidden h-full w-px bg-gradient-to-b from-transparent via-[#035c43]/35 to-transparent lg:block"></div>

                        <!-- Main Title: IT INVESTMENT RECOVERIES -->
                        <div
                            class="transform transition-all duration-700 ease-out"
                            :class="visible ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0'"
                        >
                            <h2 class="text-4xl sm:text-5xl lg:text-6xl xl:text-[64px] font-black tracking-normal uppercase leading-[0.98] font-['Albert_Sans',sans-serif]">
                                <span class="text-[#035c43]">IT INVESTMENT</span>
                                <span class="text-[#111827] ml-0 sm:ml-2 lg:block lg:ml-0">RECOVERIES</span>
                            </h2>
                        </div>

                        <!-- Subheading: Computer & Electronics Recycling in Denver -->
                        <div
                            class="transform transition-all duration-700 delay-150 ease-out mt-5"
                            :class="visible ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0'"
                        >
                            <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-[#035c43] tracking-normal">
                                Computer &amp; Electronics Recycling in Denver
                            </h3>
                            <div class="mx-auto mt-5 h-1 w-24 rounded-full bg-[#035c43] shadow-[0_0_24px_rgba(3,92,67,.35)] lg:mx-0"></div>
                        </div>

                        <!-- Paragraph Description Container -->
                        <div
                            class="transform transition-all duration-700 delay-300 ease-out w-full mt-8 sm:mt-10"
                            :class="visible ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'"
                        >
                            <p class="mx-auto max-w-4xl text-gray-700 text-base sm:text-lg lg:text-xl leading-[1.85] font-normal lg:mx-0">
                                As the leading computer and electronics recycler in Denver, Colorado, We provide secure, responsible recycling services for businesses and residents. By properly recycling your outdated computers, laptops, servers, printers, phones, TVs and other electronics in Denver, we keep hazardous electronic waste out of landfills. Bring your end-of-life electronics to our Denver e-waste recycling facility for drop-off recycling. We will safely wipe all data, dismantle old electronic equipment, and recycle the components in an environmentally responsible manner. Contact us today to schedule an electronics pickup or learn more about our secure computer and electronics recycling services conveniently located in Denver.
                            </p>
                        </div>

                        <!-- Bottom Center Pill CTA Button: Data Security -> -->
                        <div
                            class="transform transition-all duration-700 delay-[450ms] ease-out mt-9 sm:mt-10"
                            :class="visible ? 'translate-y-0 opacity-100 scale-100' : 'translate-y-12 opacity-0 scale-95'"
                        >
                            <a
                                href="#services"
                                class="about-cta-shine inline-flex items-center justify-center gap-2.5 px-9 py-3.5 rounded-full bg-[#035c43] hover:bg-[#024734] text-white font-bold text-base sm:text-lg tracking-normal shadow-[0_18px_35px_rgba(3,92,67,.25)] transition-all duration-300 group whitespace-nowrap transform hover:-translate-y-1 hover:scale-105"
                            >
                                <span class="font-extrabold">Data Security</span>
                                <span class="text-xl transition-transform duration-200 group-hover:translate-x-1.5">→</span>
                            </a>
                        </div>
                    </div>

                    <!-- Background Illustration: Bottom-Right 3D Isometric IT Equipment -->
                    <div
                        class="relative hidden min-h-[430px] transform items-center justify-center transition-all duration-700 delay-300 ease-out md:flex lg:min-h-[520px]"
                        :class="visible ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-10'"
                    >
                        <div class="about-pulse absolute h-[310px] w-[310px] rounded-full border border-[#035c43]/25 bg-[#035c43]/5 sm:h-[360px] sm:w-[360px]"></div>
                        <div class="absolute h-[230px] w-[230px] rounded-full border border-[#035c43]/20 sm:h-[285px] sm:w-[285px]"></div>
                        <div class="absolute bottom-10 h-8 w-[78%] rounded-full bg-[#111827]/10 blur-xl"></div>
                        <img
                            src="{{ asset('images/isometric-servers.png') }}"
                            alt="3D IT Equipment Servers Hardware"
                            class="about-hardware-float relative z-10 w-full max-w-[370px] object-contain filter drop-shadow-2xl lg:max-w-[430px]"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- Clean Subtle Section Separator -->
        <div class="bg-white py-4 sm:py-6 w-full flex items-center justify-center">
            <div class="w-full max-w-5xl px-6 border-t border-gray-100"></div>
        </div>

        <!-- Section 3: Certified Service Providers Slider Section -->
        <section class="relative isolate bg-white text-gray-900 py-16 sm:py-20 lg:py-24 px-4 sm:px-8 w-full overflow-hidden border-y border-gray-100">
            <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
                <div
                    class="certified-grid-motion absolute inset-0 opacity-[0.045]"
                    style="background-image: linear-gradient(rgba(3, 92, 67, .22) 1px, transparent 1px), linear-gradient(90deg, rgba(3, 92, 67, .22) 1px, transparent 1px); background-size: 44px 44px;"
                ></div>
                <div class="absolute left-1/2 top-0 h-28 w-[84vw] -translate-x-1/2 bg-gradient-to-b from-[#035c43]/10 to-transparent"></div>
                <div class="absolute bottom-0 left-1/2 h-36 w-[92vw] -translate-x-1/2 bg-gradient-to-t from-[#035c43]/[0.06] to-transparent"></div>
            </div>

            <!-- Left Background Watermark -->
            <div class="absolute top-1/2 -left-20 -translate-y-1/2 w-72 h-72 sm:w-[420px] sm:h-[420px] pointer-events-none opacity-[0.07] sm:opacity-10 z-0">
                <svg viewBox="0 0 500 500" class="about-recycle-spin w-full h-full text-emerald-600 fill-current">
                    <g transform="translate(100, 100) scale(0.6)">
                        <path d="M 250 40 C 350 40 430 120 430 220 L 480 220 L 400 320 L 320 220 L 370 220 C 370 150 320 100 250 100 C 210 100 175 120 150 150 L 105 105 C 140 65 190 40 250 40 Z" />
                        <path d="M 460 250 C 460 350 380 430 280 430 L 280 480 L 180 400 L 280 320 L 280 370 C 350 370 400 320 400 250 C 400 210 380 175 350 150 L 395 105 C 435 140 460 190 460 250 Z" />
                        <path d="M 40 250 C 40 150 120 70 220 70 L 220 20 L 320 100 L 220 180 L 220 130 C 150 130 100 180 100 250 C 100 290 120 325 150 350 L 105 395 C 65 360 40 310 40 250 Z" />
                    </g>
                </svg>
            </div>

            <!-- Right Background Watermark -->
            <div class="absolute -top-12 -right-24 w-80 h-80 sm:w-[460px] sm:h-[460px] pointer-events-none opacity-[0.07] sm:opacity-10 z-0">
                <svg viewBox="0 0 500 500" class="about-recycle-spin w-full h-full text-emerald-600 fill-current [animation-direction:reverse]">
                    <g transform="translate(50, 50) scale(0.8)">
                        <path d="M 250 40 C 350 40 430 120 430 220 L 480 220 L 400 320 L 320 220 L 370 220 C 370 150 320 100 250 100 C 210 100 175 120 150 150 L 105 105 C 140 65 190 40 250 40 Z" />
                        <path d="M 460 250 C 460 350 380 430 280 430 L 280 480 L 180 400 L 280 320 L 280 370 C 350 370 400 320 400 250 C 400 210 380 175 350 150 L 395 105 C 435 140 460 190 460 250 Z" />
                        <path d="M 40 250 C 40 150 120 70 220 70 L 220 20 L 320 100 L 220 180 L 220 130 C 150 130 100 180 100 250 C 100 290 120 325 150 350 L 105 395 C 65 360 40 310 40 250 Z" />
                    </g>
                </svg>
            </div>

            <svg class="absolute left-1/2 top-[54%] z-0 hidden h-[250px] w-[1060px] -translate-x-1/2 -translate-y-1/2 pointer-events-none opacity-20 lg:block" viewBox="0 0 1060 250" fill="none" aria-hidden="true">
                <path class="about-circuit-line" d="M20 128 H184 C228 128 242 82 286 82 H430 C476 82 488 128 532 128 H1040" stroke="#035c43" stroke-width="2" stroke-linecap="round" />
                <path class="about-circuit-line" d="M92 192 H258 C300 192 318 152 360 152 H512 C552 152 568 194 610 194 H948" stroke="#035c43" stroke-width="2" stroke-linecap="round" />
                <circle cx="286" cy="82" r="5" fill="#035c43" />
                <circle cx="532" cy="128" r="5" fill="#035c43" />
                <circle cx="610" cy="194" r="5" fill="#035c43" />
            </svg>

            <div class="relative z-10 w-full max-w-7xl mx-auto text-center space-y-10 sm:space-y-12">
                <!-- Title -->
                <div class="max-w-7xl mx-auto space-y-3">
                    <h3 class="text-3xl sm:text-4xl lg:text-[46px] font-bold text-[#035c43] tracking-normal leading-tight">
                        We Support &amp; Do Business With
                    </h3>
                    <h2 class="text-4xl sm:text-5xl lg:text-[56px] font-black text-[#222222] tracking-normal leading-tight">
                        Certified Service Providers
                    </h2>
                    <div class="mx-auto h-1 w-28 rounded-full bg-[#035c43] shadow-[0_0_24px_rgba(3,92,67,.35)]"></div>
                </div>

                <!-- Continuous Marquee Slider -->
                <div class="relative mx-auto w-full overflow-hidden py-6 sm:py-8">
                    <div class="absolute left-0 top-0 bottom-0 w-16 sm:w-28 lg:w-40 bg-gradient-to-r from-white via-white/95 to-transparent z-20 pointer-events-none"></div>
                    <div class="absolute right-0 top-0 bottom-0 w-16 sm:w-28 lg:w-40 bg-gradient-to-l from-white via-white/95 to-transparent z-20 pointer-events-none"></div>
                    <div class="absolute left-[8%] right-[8%] top-1/2 h-px bg-gradient-to-r from-transparent via-[#035c43]/25 to-transparent pointer-events-none"></div>

                    @php
                        $certifiedProviderImages = [];

                        foreach (['webp', 'png', 'jpg', 'jpeg', 'avif', 'svg'] as $extension) {
                            foreach (glob(public_path("images/home-slider/*.{$extension}")) ?: [] as $image) {
                                $certifiedProviderImages[] = basename($image);
                            }
                        }

                        natcasesort($certifiedProviderImages);
                        $certifiedProviderImages = array_values(array_unique($certifiedProviderImages));

                        $certifiedProviders = array_map(fn ($image) => [
                            'image' => $image,
                            'alt' => 'Certified Service Provider',
                        ], $certifiedProviderImages);

                        $certifiedProviderTrack = $certifiedProviders;

                        if (count($certifiedProviders) > 2) {
                            $certifiedProviderTrack = array_merge(
                                $certifiedProviders,
                                array_slice(array_reverse($certifiedProviders), 1, -1)
                            );
                        }
                    @endphp

                    <div class="certified-provider-marquee items-center">
                        @foreach([1, 2] as $trackCopy)
                            <div class="flex items-center gap-14 pr-14 sm:gap-20 sm:pr-20 lg:gap-28 lg:pr-28">
                                @foreach($certifiedProviderTrack as $provider)
                                    <div class="certified-logo-card group flex h-[150px] w-[180px] shrink-0 items-center justify-center border border-gray-200/80 bg-white p-8 shadow-[0_12px_35px_rgba(17,24,39,.08)] transition-all duration-300 hover:-translate-y-1 hover:border-[#035c43]/40 hover:shadow-[0_22px_50px_rgba(3,92,67,.16)] sm:h-[178px] sm:w-[208px] sm:p-9 lg:h-[190px] lg:w-[230px]">
                                        <span class="absolute inset-x-5 top-0 h-px bg-gradient-to-r from-transparent via-[#035c43]/55 to-transparent"></span>
                                        <span class="absolute bottom-4 left-1/2 h-2 w-[70%] -translate-x-1/2 rounded-full bg-[#111827]/5 blur-sm"></span>
                                        <img src="{{ asset('images/home-slider/' . $provider['image']) }}" alt="{{ $provider['alt'] }}" class="max-h-full max-w-full object-contain filter drop-shadow-md transition-transform duration-300 group-hover:scale-105" />
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 4: Data Security Compliance & Continuous Auto-Looping Video Section -->
        <section
            class="relative isolate bg-white text-gray-900 py-16 sm:py-20 lg:py-24 px-4 sm:px-8 lg:px-16 w-full overflow-hidden border-y border-gray-100"
            x-data="{ visible: false }"
            x-init="
                const observer = new IntersectionObserver(([entry]) => {
                    if (entry.isIntersecting) {
                        visible = true;
                    }
                }, { threshold: 0.18 });
                observer.observe($el);
            "
        >
            <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
                <div
                    class="certified-grid-motion absolute inset-0 opacity-[0.035]"
                    style="background-image: linear-gradient(rgba(3, 92, 67, .22) 1px, transparent 1px), linear-gradient(90deg, rgba(3, 92, 67, .22) 1px, transparent 1px); background-size: 44px 44px;"
                ></div>
                <div class="absolute left-0 top-0 h-full w-full" style="background: radial-gradient(circle at 20% 35%, rgba(3, 92, 67, .12), transparent 30%), radial-gradient(circle at 80% 65%, rgba(17, 24, 39, .08), transparent 28%);"></div>
                <div class="absolute left-0 top-0 hidden h-full w-[42%] bg-[#035c43] lg:block"></div>
                <div class="absolute left-[39%] top-0 hidden h-full w-28 skew-x-[-10deg] bg-gradient-to-r from-[#035c43] to-transparent lg:block"></div>
            </div>

            <svg class="absolute left-1/2 top-1/2 z-0 hidden h-[520px] w-[1140px] -translate-x-1/2 -translate-y-1/2 pointer-events-none opacity-20 lg:block" viewBox="0 0 1140 520" fill="none" aria-hidden="true">
                <path class="security-line" d="M0 104 H168 C216 104 228 158 276 158 H468 C514 158 526 102 572 102 H1140" stroke="#035c43" stroke-width="2" stroke-linecap="round" />
                <path class="security-line" d="M76 402 H246 C290 402 306 346 352 346 H568 C612 346 628 402 672 402 H1060" stroke="#035c43" stroke-width="2" stroke-linecap="round" />
                <path class="security-line" d="M184 260 H402 C446 260 462 216 506 216 H702 C746 216 762 260 806 260 H980" stroke="#035c43" stroke-width="2" stroke-linecap="round" />
                <circle cx="276" cy="158" r="5" fill="#035c43" />
                <circle cx="572" cy="102" r="5" fill="#035c43" />
                <circle cx="506" cy="216" r="5" fill="#035c43" />
                <circle cx="672" cy="402" r="5" fill="#035c43" />
            </svg>

            <div class="relative z-10 w-full max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-[0.86fr_1.14fr] gap-10 lg:gap-12 xl:gap-16 items-center">

                <!-- Left Side: Heading Text -->
                <div
                    class="relative transform overflow-hidden rounded-lg bg-[#035c43] p-7 text-left shadow-[0_24px_60px_rgba(3,92,67,.22)] transition-all duration-700 ease-out sm:p-10 lg:p-12 xl:p-14"
                    :class="visible ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0'"
                >
                    <div class="absolute inset-0 opacity-[0.13]" style="background-image: linear-gradient(rgba(255,255,255,.25) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.25) 1px, transparent 1px); background-size: 36px 36px;"></div>
                    <svg class="absolute -right-24 -top-24 h-72 w-72 text-white/10 fill-current" viewBox="0 0 500 500" aria-hidden="true">
                        <g class="security-orbit" transform="translate(50, 50) scale(0.8)">
                            <path d="M 250 40 C 350 40 430 120 430 220 L 480 220 L 400 320 L 320 220 L 370 220 C 370 150 320 100 250 100 C 210 100 175 120 150 150 L 105 105 C 140 65 190 40 250 40 Z" />
                            <path d="M 460 250 C 460 350 380 430 280 430 L 280 480 L 180 400 L 280 320 L 280 370 C 350 370 400 320 400 250 C 400 210 380 175 350 150 L 395 105 C 435 140 460 190 460 250 Z" />
                            <path d="M 40 250 C 40 150 120 70 220 70 L 220 20 L 320 100 L 220 180 L 220 130 C 150 130 100 180 100 250 C 100 290 120 325 150 350 L 105 395 C 65 360 40 310 40 250 Z" />
                        </g>
                    </svg>
                    <div class="absolute left-0 top-0 h-1 w-full bg-gradient-to-r from-white/0 via-white/60 to-white/0"></div>
                    <h2 class="relative text-3xl sm:text-4xl lg:text-[41px] xl:text-[46px] font-black text-white leading-[1.2] sm:leading-[1.18] tracking-normal">
                        From data destruction to certified recycling, We provide data security to the highest standards, in compliance with NIST, DoD, and NSA guidelines.
                    </h2>
                </div>

                <!-- Right Side: Auto-Looping Video Frame -->
                <div
                    class="relative w-full transform transition-all duration-700 delay-200 ease-out"
                    :class="visible ? 'translate-y-0 opacity-100 scale-100' : 'translate-y-10 opacity-0 scale-95'"
                >
                    <div class="absolute -right-24 top-1/2 hidden h-[430px] w-[430px] -translate-y-1/2 rounded-full border border-[#035c43]/10 bg-[#035c43]/[0.035] lg:block"></div>
                    <svg class="about-recycle-spin absolute -right-28 top-1/2 z-0 hidden h-[360px] w-[360px] -translate-y-1/2 text-[#035c43]/10 fill-current lg:block" viewBox="0 0 500 500" aria-hidden="true">
                        <g transform="translate(50, 50) scale(0.8)">
                            <path d="M 250 40 C 350 40 430 120 430 220 L 480 220 L 400 320 L 320 220 L 370 220 C 370 150 320 100 250 100 C 210 100 175 120 150 150 L 105 105 C 140 65 190 40 250 40 Z" />
                            <path d="M 460 250 C 460 350 380 430 280 430 L 280 480 L 180 400 L 280 320 L 280 370 C 350 370 400 320 400 250 C 400 210 380 175 350 150 L 395 105 C 435 140 460 190 460 250 Z" />
                            <path d="M 40 250 C 40 150 120 70 220 70 L 220 20 L 320 100 L 220 180 L 220 130 C 150 130 100 180 100 250 C 100 290 120 325 150 350 L 105 395 C 65 360 40 310 40 250 Z" />
                        </g>
                    </svg>
                    <div class="security-orbit absolute -right-2 -top-8 hidden h-28 w-28 rounded-full border border-dashed border-[#035c43]/30 lg:block"></div>
                    <div class="absolute left-8 top-8 hidden h-10 w-10 border-l-2 border-t-2 border-[#035c43]/35 lg:block"></div>
                    <div class="absolute right-20 bottom-6 hidden h-12 w-12 border-b-2 border-r-2 border-[#035c43]/30 lg:block"></div>
                    <svg class="absolute -left-12 top-1/2 z-0 hidden h-44 w-72 -translate-y-1/2 pointer-events-none opacity-30 lg:block" viewBox="0 0 288 176" fill="none" aria-hidden="true">
                        <path class="security-line" d="M0 88 H58 C86 88 90 44 118 44 H188 C216 44 222 88 250 88 H288" stroke="#035c43" stroke-width="2" stroke-linecap="round" />
                        <path class="security-line" d="M26 132 H92 C118 132 124 98 150 98 H214 C240 98 246 132 272 132" stroke="#035c43" stroke-width="2" stroke-linecap="round" />
                        <circle cx="118" cy="44" r="4" fill="#035c43" />
                        <circle cx="250" cy="88" r="4" fill="#035c43" />
                        <circle cx="150" cy="98" r="4" fill="#035c43" />
                    </svg>

                    <div class="security-media-float relative mx-auto w-full max-w-4xl lg:-ml-3">
                        <div class="security-signal absolute -inset-6 rounded-lg bg-[#035c43]/15 blur-2xl"></div>
                        <div class="absolute -right-5 -top-5 h-24 w-24 border-t-2 border-r-2 border-[#035c43]"></div>
                        <div class="absolute -left-5 -bottom-5 h-24 w-24 border-b-2 border-l-2 border-[#035c43]"></div>
                        <div class="absolute -bottom-8 left-10 right-10 h-12 rounded-full bg-[#111827]/18 blur-2xl"></div>

                        <div class="relative w-full overflow-hidden rounded-lg border-2 border-[#035c43] bg-black shadow-[0_30px_80px_rgba(3,92,67,.28)] aspect-video group">
                            <div class="absolute inset-0 z-20 pointer-events-none ring-1 ring-white/25"></div>
                            <div class="absolute left-0 top-0 z-20 h-1 w-full bg-gradient-to-r from-transparent via-[#035c43] to-transparent"></div>
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

            </div>
        </section>

        <!-- Section 5: Our Proud Community Partners Section -->
        <section class="relative isolate bg-[#035c43] py-16 sm:py-20 lg:py-24 px-4 sm:px-8 w-full overflow-hidden shadow-2xl">
            <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
                <div class="absolute inset-0 opacity-[0.08]" style="background-image: linear-gradient(rgba(255,255,255,.22) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.22) 1px, transparent 1px); background-size: 44px 44px;"></div>
                <div class="absolute left-1/2 top-0 h-28 w-[88vw] -translate-x-1/2 bg-gradient-to-b from-white/10 to-transparent"></div>
                <div class="absolute -left-28 -bottom-32 h-[420px] w-[420px] rounded-full border border-white/10 bg-white/[0.035]"></div>
                <div class="community-partner-glow absolute -right-32 top-1/2 h-[470px] w-[470px] -translate-y-1/2 rounded-full border border-white/10 bg-white/[0.045]"></div>
            </div>

            <svg class="about-recycle-spin absolute -right-24 -top-24 z-0 h-[360px] w-[360px] text-white/10 fill-current pointer-events-none sm:h-[460px] sm:w-[460px]" viewBox="0 0 500 500" aria-hidden="true">
                <g transform="translate(50, 50) scale(0.8)">
                    <path d="M 250 40 C 350 40 430 120 430 220 L 480 220 L 400 320 L 320 220 L 370 220 C 370 150 320 100 250 100 C 210 100 175 120 150 150 L 105 105 C 140 65 190 40 250 40 Z" />
                    <path d="M 460 250 C 460 350 380 430 280 430 L 280 480 L 180 400 L 280 320 L 280 370 C 350 370 400 320 400 250 C 400 210 380 175 350 150 L 395 105 C 435 140 460 190 460 250 Z" />
                    <path d="M 40 250 C 40 150 120 70 220 70 L 220 20 L 320 100 L 220 180 L 220 130 C 150 130 100 180 100 250 C 100 290 120 325 150 350 L 105 395 C 65 360 40 310 40 250 Z" />
                </g>
            </svg>

            <svg class="absolute left-1/2 top-1/2 z-0 hidden h-[390px] w-[1180px] -translate-x-1/2 -translate-y-1/2 pointer-events-none opacity-20 lg:block" viewBox="0 0 1180 390" fill="none" aria-hidden="true">
                <path class="security-line" d="M0 104 H164 C212 104 226 148 274 148 H462 C510 148 524 104 572 104 H1180" stroke="white" stroke-width="2" stroke-linecap="round" />
                <path class="security-line" d="M84 282 H258 C306 282 320 234 366 234 H560 C608 234 622 282 670 282 H1088" stroke="white" stroke-width="2" stroke-linecap="round" />
                <circle cx="274" cy="148" r="5" fill="white" />
                <circle cx="572" cy="104" r="5" fill="white" />
                <circle cx="670" cy="282" r="5" fill="white" />
            </svg>

            <div class="relative z-10 w-full max-w-[1650px] mx-auto text-center px-4 sm:px-8">
                <!-- Section Title: Our Proud Community Partners [✓] -->
                <div class="mb-10 sm:mb-12 lg:mb-14 flex items-center justify-center gap-3 sm:gap-4 flex-wrap">
                    <h2 class="text-3xl sm:text-5xl md:text-6xl lg:text-[64px] font-black tracking-normal leading-tight font-['Albert_Sans',sans-serif]">
                        <span class="text-white">Our Proud</span>
                        <span class="text-white ml-2 sm:ml-4">Community Partners</span>
                    </h2>
                    <!-- Glass Checkbox Icon Badge -->
                    <div class="inline-flex items-center justify-center p-2 bg-white/10 border border-white/25 rounded-lg backdrop-blur-md shadow-[0_12px_35px_rgba(0,0,0,.18)] shrink-0">
                        <svg class="w-7 h-7 sm:w-8 sm:h-8 text-white fill-none stroke-current" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="4" />
                            <path d="M9 12l2 2 4-4" />
                        </svg>
                    </div>
                </div>

                <div class="mx-auto mb-12 h-px w-full max-w-7xl bg-gradient-to-r from-transparent via-white/40 to-transparent"></div>

                <!-- Continuous Marquee Slider for Partner Logos -->
                <div class="relative mx-auto w-full overflow-hidden rounded-lg border border-white/15 bg-white/[0.055] py-8 shadow-[0_28px_80px_rgba(0,0,0,.18)] backdrop-blur-sm sm:py-10">
                    <!-- Left & Right Gradient Fade Edges -->
                    <div class="absolute left-0 top-0 bottom-0 w-16 sm:w-28 lg:w-40 bg-gradient-to-r from-[#035c43] via-[#035c43]/95 to-transparent z-20 pointer-events-none"></div>
                    <div class="absolute right-0 top-0 bottom-0 w-16 sm:w-28 lg:w-40 bg-gradient-to-l from-[#035c43] via-[#035c43]/95 to-transparent z-20 pointer-events-none"></div>
                    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/45 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-white/25 to-transparent"></div>

                    @php
                        $communityPartnerImages = [];

                        foreach (['webp', 'png', 'jpg', 'jpeg', 'avif', 'svg'] as $extension) {
                            foreach (glob(public_path("images/partners/*.{$extension}")) ?: [] as $image) {
                                $communityPartnerImages[] = basename($image);
                            }
                        }

                        natcasesort($communityPartnerImages);
                        $communityPartnerImages = array_values(array_unique($communityPartnerImages));
                    @endphp

                    <div class="community-partner-marquee items-center">
                        @foreach([1, 2] as $trackCopy)
                            <div class="flex items-center gap-8 pr-8 sm:gap-12 sm:pr-12 lg:gap-16 lg:pr-16">
                                @foreach($communityPartnerImages as $img)
                                    <div class="community-logo-tile group flex h-[138px] w-[250px] shrink-0 items-center justify-center border border-white/15 bg-black/10 p-3 shadow-[0_18px_45px_rgba(0,0,0,.16)] transition-all duration-300 hover:-translate-y-1 hover:border-white/35 hover:bg-white/[0.08] hover:shadow-[0_24px_55px_rgba(0,0,0,.22)] sm:h-[156px] sm:w-[300px] sm:p-4 lg:h-[170px] lg:w-[340px] lg:p-5 rounded-xl overflow-hidden text-center">
                                        <span class="!absolute inset-x-5 top-0 h-px bg-gradient-to-r from-transparent via-white/60 to-transparent pointer-events-none"></span>
                                        <span class="!absolute bottom-2 left-1/2 h-2 w-[68%] -translate-x-1/2 rounded-full bg-black/20 blur-sm pointer-events-none"></span>
                                        <img src="{{ asset('images/partners/' . $img) }}" alt="Community Partner" class="relative z-10 mx-auto my-auto max-h-[98px] max-w-[92%] object-contain object-center filter drop-shadow-[0_8px_16px_rgba(0,0,0,.3)] brightness-200 transition-transform duration-300 group-hover:scale-105 sm:max-h-[118px] lg:max-h-[132px]" />
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </section>

        <!-- Section 6: Our Services Section -->
        <section id="services" class="relative isolate bg-[#f8fafc] text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100 overflow-hidden">
            <!-- Background Circuit Grid & Lighting Accents -->
            <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
                <div class="absolute inset-0 opacity-[0.035]" style="background-image: linear-gradient(rgba(3, 92, 67, .25) 1px, transparent 1px), linear-gradient(90deg, rgba(3, 92, 67, .25) 1px, transparent 1px); background-size: 44px 44px;"></div>
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[75vw] h-48 bg-gradient-to-b from-[#035c43]/10 via-[#035c43]/5 to-transparent blur-3xl"></div>
            </div>

            <div class="relative z-10 w-full max-w-7xl mx-auto space-y-12">

                <!-- Section Header -->
                <div class="text-center max-w-3xl mx-auto">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-normal leading-tight font-['Albert_Sans',sans-serif]">
                        <span class="text-[#035c43]">Our</span>
                        <span class="text-[#111827] ml-2">Services</span>
                    </h2>
                    <div class="mx-auto mt-4 h-1 w-24 rounded-full bg-[#035c43] shadow-[0_0_18px_rgba(3,92,67,.3)]"></div>
                </div>

                <!-- 6 Service Image Cards Bento Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7 sm:gap-8">

                    <!-- Card 1: Data Destruction -->
                    <a href="{{ url('/data-destruction') }}" class="group relative block rounded-[26px] p-2 bg-white border border-gray-200/90 shadow-[0_10px_30px_rgba(0,0,0,0.06)] hover:shadow-[0_24px_50px_rgba(3,92,67,0.2)] hover:-translate-y-2 hover:border-[#035c43]/40 transition-all duration-500 overflow-hidden">
                        <div class="relative w-full aspect-[4/3] rounded-[20px] overflow-hidden bg-slate-950">
                            <img
                                src="{{ asset('images/services/WhatsApp-Image-2026-03-03-at-2.48.49-AM.webp') }}"
                                alt="Data Destruction"
                                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-out"
                            />
                            <!-- Deep Gradient Overlay for High Text Contrast -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/45 to-transparent"></div>

                            <!-- Top-Right Hover Action Button -->
                            <div class="absolute top-4 right-4 z-20">
                                <div class="w-10 h-10 rounded-full bg-white/20 border border-white/40 text-white flex items-center justify-center backdrop-blur-md opacity-0 group-hover:opacity-100 group-hover:translate-x-0 translate-x-3 transition-all duration-300 shadow-md">
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Bottom Content: Icon + Title -->
                            <div class="absolute inset-x-0 bottom-0 z-20 p-6 flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-[#035c43] border border-white/30 flex items-center justify-center shrink-0 shadow-lg group-hover:scale-110 group-hover:bg-[#024734] transition-all duration-300">
                                    <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 24 24">
                                        <path d="M19 4h-3.5l-1-1h-5l-1 1H5v2h14M6 19a2 2 0 002 2h8a2 2 0 002-2V7H6v12zm3.88-9.41L12 11.71l2.12-2.12 1.41 1.41L13.41 13.12l2.12 2.12-1.41 1.41L12 14.53l-2.12 2.12-1.41-1.41 2.12-2.12-2.12-2.12 1.41-1.41z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl sm:text-2xl font-extrabold text-white leading-tight drop-shadow-[0_2px_8px_rgba(0,0,0,0.85)]">
                                    Data<br />Destruction
                                </h3>
                            </div>
                        </div>
                    </a>

                    <!-- Card 2: E Waste Recycling -->
                    <a href="{{ url('/e-waste-recycling') }}" class="group relative block rounded-[26px] p-2 bg-white border border-gray-200/90 shadow-[0_10px_30px_rgba(0,0,0,0.06)] hover:shadow-[0_24px_50px_rgba(3,92,67,0.2)] hover:-translate-y-2 hover:border-[#035c43]/40 transition-all duration-500 overflow-hidden">
                        <div class="relative w-full aspect-[4/3] rounded-[20px] overflow-hidden bg-slate-950">
                            <img
                                src="{{ asset('images/services/WhatsApp-Image-2026-03-03-at-2.59.30-AM-300x300.webp') }}"
                                alt="E Waste Recycling"
                                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-out"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/45 to-transparent"></div>

                            <div class="absolute top-4 right-4 z-20">
                                <div class="w-10 h-10 rounded-full bg-white/20 border border-white/40 text-white flex items-center justify-center backdrop-blur-md opacity-0 group-hover:opacity-100 group-hover:translate-x-0 translate-x-3 transition-all duration-300 shadow-md">
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </div>
                            </div>

                            <div class="absolute inset-x-0 bottom-0 z-20 p-6 flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-[#035c43] border border-white/30 flex items-center justify-center shrink-0 shadow-lg group-hover:scale-110 group-hover:bg-[#024734] transition-all duration-300">
                                    <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 24 24">
                                        <path d="M19 4h-3.5l-1-1h-5l-1 1H5v2h14M6 19a2 2 0 002 2h8a2 2 0 002-2V7H6v12zm4-9h4v2h-4v-2zm0 4h4v2h-4v-2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl sm:text-2xl font-extrabold text-white leading-tight drop-shadow-[0_2px_8px_rgba(0,0,0,0.85)]">
                                    E Waste<br />Recycling
                                </h3>
                            </div>
                        </div>
                    </a>

                    <!-- Card 3: Liquidation Reuse Resale -->
                    <a href="{{ url('/it-asset-management') }}" class="group relative block rounded-[26px] p-2 bg-white border border-gray-200/90 shadow-[0_10px_30px_rgba(0,0,0,0.06)] hover:shadow-[0_24px_50px_rgba(3,92,67,0.2)] hover:-translate-y-2 hover:border-[#035c43]/40 transition-all duration-500 overflow-hidden">
                        <div class="relative w-full aspect-[4/3] rounded-[20px] overflow-hidden bg-slate-950">
                            <img
                                src="{{ asset('images/services/Asset-Removal-Services-.webp') }}"
                                alt="Liquidation Reuse Resale"
                                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-out"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/45 to-transparent"></div>

                            <div class="absolute top-4 right-4 z-20">
                                <div class="w-10 h-10 rounded-full bg-white/20 border border-white/40 text-white flex items-center justify-center backdrop-blur-md opacity-0 group-hover:opacity-100 group-hover:translate-x-0 translate-x-3 transition-all duration-300 shadow-md">
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </div>
                            </div>

                            <div class="absolute inset-x-0 bottom-0 z-20 p-6 flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-[#035c43] border border-white/30 flex items-center justify-center shrink-0 shadow-lg group-hover:scale-110 group-hover:bg-[#024734] transition-all duration-300">
                                    <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 24 24">
                                        <path d="M21 2H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h7v2H8v2h8v-2h-2v-2h7c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H3V4h18v12z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl sm:text-2xl font-extrabold text-white leading-tight drop-shadow-[0_2px_8px_rgba(0,0,0,0.85)]">
                                    Liquidation<br />Reuse Resale
                                </h3>
                            </div>
                        </div>
                    </a>

                    <!-- Card 4: Charity Donation -->
                    <a href="{{ url('/charity-donations') }}" class="group relative block rounded-[26px] p-2 bg-white border border-gray-200/90 shadow-[0_10px_30px_rgba(0,0,0,0.06)] hover:shadow-[0_24px_50px_rgba(3,92,67,0.2)] hover:-translate-y-2 hover:border-[#035c43]/40 transition-all duration-500 overflow-hidden">
                        <div class="relative w-full aspect-[4/3] rounded-[20px] overflow-hidden bg-slate-950">
                            <img
                                src="{{ asset('images/services/business-partnership-infographics_1284-4534-e1705103864476.webp') }}"
                                alt="Charity Donation"
                                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-out"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/45 to-transparent"></div>

                            <div class="absolute top-4 right-4 z-20">
                                <div class="w-10 h-10 rounded-full bg-white/20 border border-white/40 text-white flex items-center justify-center backdrop-blur-md opacity-0 group-hover:opacity-100 group-hover:translate-x-0 translate-x-3 transition-all duration-300 shadow-md">
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </div>
                            </div>

                            <div class="absolute inset-x-0 bottom-0 z-20 p-6 flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-[#035c43] border border-white/30 flex items-center justify-center shrink-0 shadow-lg group-hover:scale-110 group-hover:bg-[#024734] transition-all duration-300">
                                    <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl sm:text-2xl font-extrabold text-white leading-tight drop-shadow-[0_2px_8px_rgba(0,0,0,0.85)]">
                                    Charity<br />Donation
                                </h3>
                            </div>
                        </div>
                    </a>

                    <!-- Card 5: Partner & Service Provider -->
                    <a href="{{ url('/community-partners') }}" class="group relative block rounded-[26px] p-2 bg-white border border-gray-200/90 shadow-[0_10px_30px_rgba(0,0,0,0.06)] hover:shadow-[0_24px_50px_rgba(3,92,67,0.2)] hover:-translate-y-2 hover:border-[#035c43]/40 transition-all duration-500 overflow-hidden">
                        <div class="relative w-full aspect-[4/3] rounded-[20px] overflow-hidden bg-slate-950">
                            <img
                                src="{{ asset('images/services/Partners-Service-Providers.webp') }}"
                                alt="Partner & Service Provider"
                                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-out"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/45 to-transparent"></div>

                            <div class="absolute top-4 right-4 z-20">
                                <div class="w-10 h-10 rounded-full bg-white/20 border border-white/40 text-white flex items-center justify-center backdrop-blur-md opacity-0 group-hover:opacity-100 group-hover:translate-x-0 translate-x-3 transition-all duration-300 shadow-md">
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </div>
                            </div>

                            <div class="absolute inset-x-0 bottom-0 z-20 p-6 flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-[#035c43] border border-white/30 flex items-center justify-center shrink-0 shadow-lg group-hover:scale-110 group-hover:bg-[#024734] transition-all duration-300">
                                    <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 24 24">
                                        <path d="M11.5 13.5L9.75 11.75C9.36 11.36 8.73 11.36 8.34 11.75C7.95 12.14 7.95 12.77 8.34 13.16L10.8 15.62C11.19 16.01 11.82 16.01 12.21 15.62L16.66 11.17C17.05 10.78 17.05 10.15 16.66 9.76C16.27 9.37 15.64 9.37 15.25 9.76L11.5 13.5Z"/>
                                        <path d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM19 19H5V5H19V19Z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl sm:text-2xl font-extrabold text-white leading-tight drop-shadow-[0_2px_8px_rgba(0,0,0,0.85)]">
                                    Partner &amp;<br />Service Provider
                                </h3>
                            </div>
                        </div>
                    </a>

                    <!-- Card 6: Rebate Reuse Price List -->
                    <a href="{{ url('/e-waste-recycling') }}" class="group relative block rounded-[26px] p-2 bg-white border border-gray-200/90 shadow-[0_10px_30px_rgba(0,0,0,0.06)] hover:shadow-[0_24px_50px_rgba(3,92,67,0.2)] hover:-translate-y-2 hover:border-[#035c43]/40 transition-all duration-500 overflow-hidden">
                        <div class="relative w-full aspect-[4/3] rounded-[20px] overflow-hidden bg-slate-950">
                            <img
                                src="{{ asset('images/services/WhatsApp-Image-2026-03-03-at-2.48.49-AM-1-300x300.webp') }}"
                                alt="Rebate Reuse Price List"
                                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-out"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/45 to-transparent"></div>

                            <div class="absolute top-4 right-4 z-20">
                                <div class="w-10 h-10 rounded-full bg-white/20 border border-white/40 text-white flex items-center justify-center backdrop-blur-md opacity-0 group-hover:opacity-100 group-hover:translate-x-0 translate-x-3 transition-all duration-300 shadow-md">
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </div>
                            </div>

                            <div class="absolute inset-x-0 bottom-0 z-20 p-6 flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-[#035c43] border border-white/30 flex items-center justify-center shrink-0 shadow-lg group-hover:scale-110 group-hover:bg-[#024734] transition-all duration-300">
                                    <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 24 24">
                                        <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl sm:text-2xl font-extrabold text-white leading-tight drop-shadow-[0_2px_8px_rgba(0,0,0,0.85)]">
                                    Rebate Reuse<br />Price List
                                </h3>
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
                <div class="transform skew-y-2 w-full max-w-[1650px] mx-auto px-4 sm:px-8 text-center space-y-12">

                    <!-- Section Title: Our Proud Business Partners -->
                    <div class="flex items-center justify-center gap-4 flex-wrap">
                        <h2 class="text-4xl sm:text-6xl md:text-7xl lg:text-[72px] font-black tracking-normal leading-none font-['Albert_Sans',sans-serif]">
                            <span class="text-white">Our Proud</span>
                            <span class="text-white ml-2 sm:ml-4">Business Partners</span>
                        </h2>
                    </div>

                    <!-- Continuous Marquee Slider inside Glass Container -->
                    <div class="relative mx-auto w-full overflow-hidden rounded-2xl border border-white/15 bg-white/[0.055] py-8 shadow-[0_28px_80px_rgba(0,0,0,.18)] backdrop-blur-sm sm:py-10 space-y-6 sm:space-y-8">
                        <!-- Left & Right Gradient Fade Edges -->
                        <div class="absolute left-0 top-0 bottom-0 w-16 sm:w-28 lg:w-40 bg-gradient-to-r from-[#035c43] via-[#035c43]/95 to-transparent z-20 pointer-events-none"></div>
                        <div class="absolute right-0 top-0 bottom-0 w-16 sm:w-28 lg:w-40 bg-gradient-to-l from-[#035c43] via-[#035c43]/95 to-transparent z-20 pointer-events-none"></div>
                        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/45 to-transparent"></div>
                        <div class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-white/25 to-transparent"></div>

                        <!-- Row 1: Moving RIGHT to LEFT -->
                        <div class="animate-marquee-left items-center">
                            <div class="flex items-center gap-8 pr-8 sm:gap-12 sm:pr-12 lg:gap-16 lg:pr-16">
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
                                    <div class="community-logo-tile group flex h-[138px] w-[250px] shrink-0 items-center justify-center border border-white/15 bg-black/10 p-3 shadow-[0_18px_45px_rgba(0,0,0,.16)] transition-all duration-300 hover:-translate-y-1 hover:border-white/35 hover:bg-white/[0.08] hover:shadow-[0_24px_55px_rgba(0,0,0,.22)] sm:h-[156px] sm:w-[300px] sm:p-4 lg:h-[170px] lg:w-[340px] lg:p-5 rounded-xl overflow-hidden text-center">
                                        <span class="!absolute inset-x-5 top-0 h-px bg-gradient-to-r from-transparent via-white/60 to-transparent pointer-events-none"></span>
                                        <span class="!absolute bottom-2 left-1/2 h-2 w-[68%] -translate-x-1/2 rounded-full bg-black/20 blur-sm pointer-events-none"></span>
                                        <img src="{{ asset('images/services/' . $bImg) }}" alt="Business Partner" class="relative z-10 mx-auto my-auto max-h-[98px] max-w-[92%] object-contain object-center filter drop-shadow-[0_8px_16px_rgba(0,0,0,.3)] brightness-200 transition-transform duration-300 group-hover:scale-105 sm:max-h-[118px] lg:max-h-[132px]" />
                                    </div>
                                @endforeach
                            </div>
                            <div class="flex items-center gap-8 pr-8 sm:gap-12 sm:pr-12 lg:gap-16 lg:pr-16">
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
                                    <div class="community-logo-tile group flex h-[138px] w-[250px] shrink-0 items-center justify-center border border-white/15 bg-black/10 p-3 shadow-[0_18px_45px_rgba(0,0,0,.16)] transition-all duration-300 hover:-translate-y-1 hover:border-white/35 hover:bg-white/[0.08] hover:shadow-[0_24px_55px_rgba(0,0,0,.22)] sm:h-[156px] sm:w-[300px] sm:p-4 lg:h-[170px] lg:w-[340px] lg:p-5 rounded-xl overflow-hidden text-center">
                                        <span class="!absolute inset-x-5 top-0 h-px bg-gradient-to-r from-transparent via-white/60 to-transparent pointer-events-none"></span>
                                        <span class="!absolute bottom-2 left-1/2 h-2 w-[68%] -translate-x-1/2 rounded-full bg-black/20 blur-sm pointer-events-none"></span>
                                        <img src="{{ asset('images/services/' . $bImg) }}" alt="Business Partner" class="relative z-10 mx-auto my-auto max-h-[98px] max-w-[92%] object-contain object-center filter drop-shadow-[0_8px_16px_rgba(0,0,0,.3)] brightness-200 transition-transform duration-300 group-hover:scale-105 sm:max-h-[118px] lg:max-h-[132px]" />
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Row 2: Moving LEFT to RIGHT -->
                        <div class="animate-marquee-right items-center">
                            <div class="flex items-center gap-8 pr-8 sm:gap-12 sm:pr-12 lg:gap-16 lg:pr-16">
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
                                    <div class="community-logo-tile group flex h-[138px] w-[250px] shrink-0 items-center justify-center border border-white/15 bg-black/10 p-3 shadow-[0_18px_45px_rgba(0,0,0,.16)] transition-all duration-300 hover:-translate-y-1 hover:border-white/35 hover:bg-white/[0.08] hover:shadow-[0_24px_55px_rgba(0,0,0,.22)] sm:h-[156px] sm:w-[300px] sm:p-4 lg:h-[170px] lg:w-[340px] lg:p-5 rounded-xl overflow-hidden text-center">
                                        <span class="!absolute inset-x-5 top-0 h-px bg-gradient-to-r from-transparent via-white/60 to-transparent pointer-events-none"></span>
                                        <span class="!absolute bottom-2 left-1/2 h-2 w-[68%] -translate-x-1/2 rounded-full bg-black/20 blur-sm pointer-events-none"></span>
                                        <img src="{{ asset('images/services/' . $bImg2) }}" alt="Business Partner" class="relative z-10 mx-auto my-auto max-h-[98px] max-w-[92%] object-contain object-center filter drop-shadow-[0_8px_16px_rgba(0,0,0,.3)] brightness-200 transition-transform duration-300 group-hover:scale-105 sm:max-h-[118px] lg:max-h-[132px]" />
                                    </div>
                                @endforeach
                            </div>
                            <div class="flex items-center gap-8 pr-8 sm:gap-12 sm:pr-12 lg:gap-16 lg:pr-16">
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
                                    <div class="community-logo-tile group flex h-[138px] w-[250px] shrink-0 items-center justify-center border border-white/15 bg-black/10 p-3 shadow-[0_18px_45px_rgba(0,0,0,.16)] transition-all duration-300 hover:-translate-y-1 hover:border-white/35 hover:bg-white/[0.08] hover:shadow-[0_24px_55px_rgba(0,0,0,.22)] sm:h-[156px] sm:w-[300px] sm:p-4 lg:h-[170px] lg:w-[340px] lg:p-5 rounded-xl overflow-hidden text-center">
                                        <span class="!absolute inset-x-5 top-0 h-px bg-gradient-to-r from-transparent via-white/60 to-transparent pointer-events-none"></span>
                                        <span class="!absolute bottom-2 left-1/2 h-2 w-[68%] -translate-x-1/2 rounded-full bg-black/20 blur-sm pointer-events-none"></span>
                                        <img src="{{ asset('images/services/' . $bImg2) }}" alt="Business Partner" class="relative z-10 mx-auto my-auto max-h-[98px] max-w-[92%] object-contain object-center filter drop-shadow-[0_8px_16px_rgba(0,0,0,.3)] brightness-200 transition-transform duration-300 group-hover:scale-105 sm:max-h-[118px] lg:max-h-[132px]" />
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>

            </section>
        </div>

        <!-- Section 8: Our Happy Clients Section -->
        <x-testimonials />

        <!-- Section 9: Recycle Your Electronics Securely (White Background Enterprise Banner) -->
        <section class="bg-white text-gray-900 py-16 sm:py-24 px-6 sm:px-12 lg:px-16 w-full text-center border-t border-gray-100 relative overflow-hidden">
            
            <!-- Soft Ambient Glow Accent -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[300px] bg-emerald-400/10 blur-[130px] pointer-events-none rounded-full"></div>

            <div class="max-w-5xl mx-auto flex flex-col items-center space-y-6 sm:space-y-8 relative z-10">
                
                <!-- Trust Badge Pill -->
                <div class="inline-flex items-center gap-2 px-4.5 py-2 rounded-full bg-emerald-50 border border-emerald-200/90 text-[#035c43] text-xs sm:text-sm font-extrabold uppercase tracking-widest shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-[#035c43]"></span>
                    <span>ENTERPRISE IT ASSET RECOVERY & RECYCLING</span>
                </div>

                <!-- Section Title: High-Contrast Dark & Deep Emerald Text on White -->
                <h2 class="text-3xl sm:text-5xl lg:text-6xl font-black uppercase tracking-tight text-gray-900 leading-tight font-['Albert_Sans',sans-serif]">
                    <span>RECYCLE YOUR</span>
                    <span class="text-[#035c43] ml-2 sm:ml-3">ELECTRONICS SECURELY</span>
                </h2>

                <!-- Concise Description (Exact Original Wording) -->
                <p class="max-w-4xl text-gray-700 text-base sm:text-lg lg:text-xl leading-relaxed font-semibold">
                    We Help Government/Private Organizations Liquidate, Donate, Or Recycle Their IT and Electronic Assets Securely and Responsibly. With experience across public, private, nonprofit, and a vast array of industries, we can efficiently meet the needs of any organization.
                </p>

                <!-- Primary Action Button (Deep Emerald Button on White) -->
                <div class="pt-3">
                    <a
                        href="{{ url('/contact-us') }}#pickup-inquiry"
                        class="inline-flex items-center justify-center gap-3 px-10 py-4.5 sm:px-12 sm:py-5 rounded-full bg-[#035c43] hover:bg-[#024733] text-white font-extrabold text-base sm:text-xl tracking-wide shadow-xl shadow-emerald-900/20 transition-all duration-300 group whitespace-nowrap transform hover:scale-105"
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

        <!-- Section 11: Subscribe to Our Newsletter Section -->
        <section class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 w-full border-t border-gray-100 overflow-hidden">
            <!-- Inner Content Card Container (max-w-7xl) -->
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
