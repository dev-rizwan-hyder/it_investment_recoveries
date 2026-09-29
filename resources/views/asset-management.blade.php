<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="w-full overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>IT Asset Management | Complete IT Asset Disposition &amp; Recycling | IT Investment Recoveries</title>
        <meta name="description" content="Complete IT Asset Management Services in Denver & Toronto. We audit, inventory, destroy data, and recycle IT equipment at scale with full serialized reporting and certificates.">

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
                                950: '#035c43',
                                900: '#035c43',
                                800: '#035c43',
                                700: '#035c43',
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
        </style>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-white text-gray-900 font-['Albert_Sans',sans-serif] antialiased selection:bg-[#035c43] selection:text-white w-full overflow-x-hidden m-0 p-0">

        <!-- Fixed Bottom-Left Badge (Always Anchored to Bottom-Left) -->
        <x-events-badge />

        <!-- Section 1: Hero Section (Circuit Boards with Hard Drives Background) -->
        <section class="relative min-h-[85vh] sm:min-h-[90vh] lg:min-h-screen w-full flex flex-col justify-between overflow-hidden bg-transparent">
            <!-- Top Transparent Header Overlay -->
            <x-header active="services" />

            <!-- Background Image -->
            <div class="absolute inset-0 z-0 overflow-hidden w-full h-full">
                <img 
                    src="{{ asset('images/services/it-asset-management-hero.webp') }}" 
                    alt="IT Asset Management to the Next Level" 
                    class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000 ease-out brightness-95 contrast-105"
                />
                <div class="absolute inset-0 bg-gradient-to-b from-slate-950/75 via-[#01281d]/30 to-slate-950/85"></div>
            </div>

            <!-- Main Hero Center Content -->
            <div class="relative z-20 flex-grow flex flex-col items-center justify-center text-center px-4 sm:px-8 lg:px-12 pt-48 sm:pt-60 md:pt-64 pb-32 sm:pb-40 w-full">
                <div class="w-full max-w-5xl mx-auto space-y-5 sm:space-y-7">
                    
                    <!-- Main Title: IT Asset Management to the Next Level -->
                    <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-[80px] font-bold text-white tracking-tight leading-[1.1] font-['Albert_Sans',sans-serif] drop-shadow-[0_4px_20px_rgba(0,0,0,0.85)]">
                        IT Asset Management to the Next Level
                    </h1>

                    <!-- Paragraph Subtitle -->
                    <p class="text-gray-100 text-base sm:text-lg md:text-xl lg:text-2xl font-medium max-w-3xl mx-auto leading-relaxed drop-shadow-md">
                        Don't Have Time to Inventory Your IT Assets No worries, we can help!
                    </p>

                    <!-- CTA Button: Arrange A Pickup -> -->
                    <div class="pt-4">
                        <a 
                            href="{{ url('/contact-us') }}" 
                            class="inline-flex items-center justify-center gap-2.5 px-8 py-3.5 sm:px-10 sm:py-4 rounded-full bg-[#035c43] hover:bg-[#024734] text-white font-extrabold text-base sm:text-lg tracking-wide shadow-2xl transition duration-300 transform hover:scale-105 group"
                        >
                            <span>Arrange A Pickup</span>
                            <span class="text-xl transition-transform duration-200 group-hover:translate-x-1.5">→</span>
                        </a>
                    </div>

                </div>
            </div>
        </section>

        <!-- Section 2: Complete IT Asset Management Services Overview -->
        <section class="bg-gradient-to-b from-slate-50 via-white to-slate-50/80 text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full relative overflow-hidden border-b border-gray-100">
            <!-- Background Ambient Glow Accents -->
            <div class="absolute top-1/2 left-1/4 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[350px] bg-emerald-400/10 blur-[130px] pointer-events-none rounded-full"></div>
            <div class="absolute top-1/2 right-1/4 translate-x-1/2 -translate-y-1/2 w-[500px] h-[300px] bg-teal-400/10 blur-[120px] pointer-events-none rounded-full"></div>

            <div class="w-full max-w-7xl mx-auto relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                    
                    <!-- Left Column: Heading & Content Cards -->
                    <div class="lg:col-span-7 space-y-8 text-left">
                        
                        <!-- Main Title (Exact Wording) -->
                        <h2 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-gray-900 leading-[1.15] font-['Albert_Sans',sans-serif]">
                            <span>Complete IT Asset</span> 
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#035c43] via-emerald-600 to-[#035c43] block mt-1 sm:mt-2">Management Services</span>
                        </h2>

                        <!-- Main Content Container with Left Border Highlight -->
                        <div class="space-y-6">
                            <!-- Paragraph 1 (Exact Original Wording) -->
                            <div class="relative pl-5 sm:pl-6 border-l-4 border-[#035c43]">
                                <p class="text-gray-700 text-base sm:text-lg lg:text-xl font-medium leading-relaxed">
                                    Our Complete IT Asset Management Services are designed for businesses that don't have the time or resources to inventory their IT equipment before e-waste recycling. We can inventory and document your IT Assets before they are processed for data destruction &amp; recycling.
                                </p>
                            </div>

                            <!-- Executive Highlight Box for Paragraph 2 (Exact Original Wording) -->
                            <div class="bg-gradient-to-r from-emerald-50/90 via-slate-50/80 to-emerald-50/40 rounded-2xl p-6 sm:p-7 border border-emerald-200/80 shadow-sm hover:shadow-md transition-shadow duration-300 relative overflow-hidden flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-[#035c43] text-white flex items-center justify-center shrink-0 shadow-md mt-0.5">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                        <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm-2 14l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-gray-900 text-base sm:text-lg font-bold leading-relaxed">
                                        You will receive detailed documentation of the equipment processed, including a Certificate of Data Destruction and E-Waste Recycling.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Professional High-Tech Image Showcase -->
                    <div class="lg:col-span-5 relative">
                        <!-- Backdrop Decorative Glow Card -->
                        <div class="absolute -inset-2 bg-gradient-to-r from-[#035c43]/20 via-emerald-500/20 to-teal-400/20 rounded-3xl blur-xl opacity-75 group-hover:opacity-100 transition duration-500"></div>

                        <div class="relative rounded-3xl overflow-hidden border border-gray-200/90 shadow-[0_20px_50px_rgba(3,92,67,0.12)] group bg-white">
                            <!-- Image -->
                            <img 
                                src="{{ asset('images/services/asset-management-overview.png') }}" 
                                alt="Complete IT Asset Management Services" 
                                class="w-full h-[380px] sm:h-[440px] object-cover object-center transform group-hover:scale-105 transition-transform duration-700 ease-out"
                            />
                            
                            <!-- Bottom Overlay Shadow for contrast -->
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-slate-950/20 to-transparent"></div>

                            <!-- Bottom Floating Tag Badge -->
                            <div class="absolute bottom-5 left-5 right-5 p-4 rounded-2xl bg-white/90 backdrop-blur-md border border-white/40 shadow-lg flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></div>
                                    <span class="text-xs sm:text-sm font-extrabold text-gray-900 tracking-wide">Full Serialized Audit &amp; Documentation</span>
                                </div>
                                <span class="text-emerald-700 font-bold text-xs bg-emerald-100/80 px-2.5 py-1 rounded-md border border-emerald-200">Verified</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Section 3: How It Works? (4 Step Connected Process Flow) -->
        <section class="bg-gradient-to-b from-slate-50 via-white to-slate-50/70 text-gray-900 py-20 sm:py-28 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100 relative overflow-hidden">
            <!-- Background Ambient Glow Accents -->
            <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] bg-emerald-400/5 blur-[150px] pointer-events-none rounded-full"></div>

            <div class="w-full max-w-7xl mx-auto space-y-14 sm:space-y-20 relative z-10">
                
                <!-- Section Title: How It Works? -->
                <div class="text-center space-y-4 max-w-3xl mx-auto">
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 border border-emerald-200/90 text-[#035c43] font-bold text-xs uppercase tracking-wider shadow-sm">
                        Seamless 4-Step Workflow
                    </span>
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-gray-900 leading-tight font-['Albert_Sans',sans-serif]">
                        <span class="text-[#035c43]">How</span> 
                        <span class="text-gray-900 ml-2">It Works?</span>
                    </h2>
                    <p class="text-gray-600 text-base sm:text-lg lg:text-xl font-medium leading-relaxed">
                        Simple, transparent 4-step process for secure IT asset disposition
                    </p>
                </div>

                <!-- 4 Step Connected Cards Flow Grid -->
                <div class="relative">
                    
                    <!-- Desktop Connecting Track Line Behind Cards -->
                    <div class="hidden lg:block absolute top-[110px] left-[10%] right-[10%] h-1 bg-gradient-to-r from-emerald-200 via-emerald-400 to-teal-300 z-0 rounded-full opacity-60"></div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8 relative z-10">
                        
                        <!-- Card 1: STEP 01 -->
                        <div class="bg-white rounded-3xl border border-gray-200/90 shadow-[0_12px_35px_rgba(0,0,0,0.04)] hover:shadow-[0_25px_50px_rgba(3,92,67,0.15)] hover:border-emerald-400 transition-all duration-500 transform hover:-translate-y-2 flex flex-col justify-between overflow-hidden group relative">
                            <!-- Top Step Indicator Accent -->
                            <div class="h-2 w-full bg-gradient-to-r from-[#035c43] via-emerald-500 to-teal-400"></div>
                            
                            <!-- Watermark Background Step Number -->
                            <div class="absolute top-4 right-4 text-7xl font-black text-slate-100 select-none pointer-events-none group-hover:text-emerald-50 transition-colors duration-300">
                                01
                            </div>

                            <div class="p-7 space-y-6 flex-grow flex flex-col justify-between relative z-10">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span class="px-3.5 py-1.5 rounded-full border border-emerald-500/30 text-[#035c43] font-extrabold text-xs tracking-wider uppercase bg-emerald-50/90 shadow-sm">
                                            STEP 01
                                        </span>
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200/80 flex items-center justify-center text-[#035c43] shrink-0 group-hover:bg-[#035c43] group-hover:text-white transition-all duration-300 shadow-md">
                                            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                                <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <h3 class="text-xl font-extrabold text-gray-900 group-hover:text-[#035c43] transition-colors leading-snug">
                                        Schedule or Drop Off
                                    </h3>
                                    <p class="text-gray-600 text-sm leading-relaxed font-normal">
                                        Contact us to schedule a convenient pickup or bring your IT Assets directly to our facility.
                                    </p>
                                </div>

                                <!-- Pricing Box -->
                                <div class="bg-slate-50/90 rounded-2xl p-4 border border-slate-200/80 flex items-center justify-between text-xs font-bold text-gray-800 shadow-inner">
                                    <span class="text-gray-600 font-semibold">Drop-Off Fee</span>
                                    <span class="text-[#035c43] font-black text-xs px-3 py-1 rounded-lg bg-emerald-100/80 border border-emerald-300/80 shadow-sm">FREE</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: STEP 02 -->
                        <div class="bg-white rounded-3xl border border-gray-200/90 shadow-[0_12px_35px_rgba(0,0,0,0.04)] hover:shadow-[0_25px_50px_rgba(3,92,67,0.15)] hover:border-emerald-400 transition-all duration-500 transform hover:-translate-y-2 flex flex-col justify-between overflow-hidden group relative">
                            <!-- Top Step Indicator Accent -->
                            <div class="h-2 w-full bg-gradient-to-r from-[#035c43] via-emerald-500 to-teal-400"></div>

                            <!-- Watermark Background Step Number -->
                            <div class="absolute top-4 right-4 text-7xl font-black text-slate-100 select-none pointer-events-none group-hover:text-emerald-50 transition-colors duration-300">
                                02
                            </div>

                            <div class="p-7 space-y-6 flex-grow flex flex-col justify-between relative z-10">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span class="px-3.5 py-1.5 rounded-full border border-emerald-500/30 text-[#035c43] font-extrabold text-xs tracking-wider uppercase bg-emerald-50/90 shadow-sm">
                                            STEP 02
                                        </span>
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200/80 flex items-center justify-center text-[#035c43] shrink-0 group-hover:bg-[#035c43] group-hover:text-white transition-all duration-300 shadow-md">
                                            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                                <path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm0 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <h3 class="text-xl font-extrabold text-gray-900 group-hover:text-[#035c43] transition-colors leading-snug">
                                        Pickup &amp; Recycling
                                    </h3>
                                    <p class="text-gray-600 text-sm leading-relaxed font-normal">
                                        We charge a flat-rate pickup fee, with no additional recycling charges for general e-waste.
                                    </p>
                                </div>

                                <!-- Pricing Box -->
                                <div class="bg-slate-50/90 rounded-2xl p-4 border border-slate-200/80 space-y-2 text-xs font-bold text-gray-800 shadow-inner">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600 font-semibold">Flat Pickup Fee</span>
                                        <span class="text-[#035c43] font-black text-sm">$150.00</span>
                                    </div>
                                    <div class="flex items-center justify-between border-t border-slate-200/80 pt-2">
                                        <span class="text-gray-600 font-semibold">E-Waste Recycling</span>
                                        <span class="text-[#035c43] font-black px-2.5 py-0.5 rounded-md bg-emerald-100/80 border border-emerald-300/80 shadow-sm">FREE</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3: STEP 03 -->
                        <div class="bg-white rounded-3xl border border-gray-200/90 shadow-[0_12px_35px_rgba(0,0,0,0.04)] hover:shadow-[0_25px_50px_rgba(3,92,67,0.15)] hover:border-emerald-400 transition-all duration-500 transform hover:-translate-y-2 flex flex-col justify-between overflow-hidden group relative">
                            <!-- Top Step Indicator Accent -->
                            <div class="h-2 w-full bg-gradient-to-r from-[#035c43] via-emerald-500 to-teal-400"></div>

                            <!-- Watermark Background Step Number -->
                            <div class="absolute top-4 right-4 text-7xl font-black text-slate-100 select-none pointer-events-none group-hover:text-emerald-50 transition-colors duration-300">
                                03
                            </div>

                            <div class="p-7 space-y-6 flex-grow flex flex-col justify-between relative z-10">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span class="px-3.5 py-1.5 rounded-full border border-emerald-500/30 text-[#035c43] font-extrabold text-xs tracking-wider uppercase bg-emerald-50/90 shadow-sm">
                                            STEP 03
                                        </span>
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200/80 flex items-center justify-center text-[#035c43] shrink-0 group-hover:bg-[#035c43] group-hover:text-white transition-all duration-300 shadow-md">
                                            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                                <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <h3 class="text-xl font-extrabold text-gray-900 group-hover:text-[#035c43] transition-colors leading-snug">
                                        Asset Management
                                    </h3>
                                    <p class="text-gray-600 text-sm leading-relaxed font-normal">
                                        Don't have time to inventory? We document manufacturer, model, serial number, asset tag, and status.
                                    </p>
                                </div>

                                <!-- Pricing Box -->
                                <div class="bg-slate-50/90 rounded-2xl p-4 border border-slate-200/80 space-y-2 text-xs font-bold text-gray-800 shadow-inner">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600 font-semibold">Off-Site Asset Mgmt</span>
                                        <span class="text-[#035c43] font-black text-xs px-2 py-0.5 rounded bg-emerald-50 border border-emerald-200">$5.00 / item</span>
                                    </div>
                                    <div class="flex items-center justify-between border-t border-slate-200/80 pt-2">
                                        <span class="text-gray-600 font-semibold">On-Site Asset Mgmt</span>
                                        <span class="text-[#035c43] font-black text-xs px-2 py-0.5 rounded bg-emerald-50 border border-emerald-200">$10.00 / item</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 4: STEP 04 -->
                        <div class="bg-white rounded-3xl border border-gray-200/90 shadow-[0_12px_35px_rgba(0,0,0,0.04)] hover:shadow-[0_25px_50px_rgba(3,92,67,0.15)] hover:border-emerald-400 transition-all duration-500 transform hover:-translate-y-2 flex flex-col justify-between overflow-hidden group relative">
                            <!-- Top Step Indicator Accent -->
                            <div class="h-2 w-full bg-gradient-to-r from-[#035c43] via-emerald-500 to-teal-400"></div>

                            <!-- Watermark Background Step Number -->
                            <div class="absolute top-4 right-4 text-7xl font-black text-slate-100 select-none pointer-events-none group-hover:text-emerald-50 transition-colors duration-300">
                                04
                            </div>

                            <div class="p-7 space-y-6 flex-grow flex flex-col justify-between relative z-10">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span class="px-3.5 py-1.5 rounded-full border border-emerald-500/30 text-[#035c43] font-extrabold text-xs tracking-wider uppercase bg-emerald-50/90 shadow-sm">
                                            STEP 04
                                        </span>
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200/80 flex items-center justify-center text-[#035c43] shrink-0 group-hover:bg-[#035c43] group-hover:text-white transition-all duration-300 shadow-md">
                                            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-5.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <h3 class="text-xl font-extrabold text-gray-900 group-hover:text-[#035c43] transition-colors leading-snug">
                                        NIST Data Destruction
                                    </h3>
                                    <p class="text-gray-600 text-sm leading-relaxed font-normal">
                                        For data-bearing devices, complete detailed reporting compliant with NIST SP 800-88 guidelines.
                                    </p>
                                </div>

                                <!-- Pricing Box -->
                                <div class="bg-slate-50/90 rounded-2xl p-3.5 border border-slate-200/80 space-y-1.5 text-xs font-bold text-gray-800 shadow-inner">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600 font-semibold">Off-Site NIST</span>
                                        <span class="text-[#035c43] font-black text-xs px-2 py-0.5 rounded bg-emerald-50 border border-emerald-200">$5.00 / drive</span>
                                    </div>
                                    <div class="flex items-center justify-between border-t border-slate-200/80 pt-1">
                                        <span class="text-gray-600 font-semibold">On-Site NIST</span>
                                        <span class="text-[#035c43] font-black text-xs px-2 py-0.5 rounded bg-emerald-50 border border-emerald-200">$10.00 / drive</span>
                                    </div>
                                    <div class="flex items-center justify-between border-t border-slate-200/80 pt-1">
                                        <span class="text-gray-600 font-semibold">Off-Site Non-NIST</span>
                                        <span class="text-[#035c43] font-black px-2 py-0.5 rounded-md bg-emerald-100/80 border border-emerald-300/80 shadow-sm">$0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </section>

        <!-- Section 4: What You Recieve (Executive Light Deliverables Suite) -->
        <section class="bg-gradient-to-b from-white via-slate-50/80 to-white text-gray-900 py-24 sm:py-32 px-4 sm:px-8 lg:px-16 w-full border-t border-slate-200/80 relative overflow-hidden" x-data="{ activeCard: 2 }">
            <!-- Subtle Background Soft Glow Orbs -->
            <div class="absolute top-1/3 -left-32 w-[600px] h-[600px] bg-emerald-500/5 rounded-full blur-[160px] pointer-events-none"></div>
            <div class="absolute bottom-1/3 -right-32 w-[600px] h-[600px] bg-teal-500/5 rounded-full blur-[160px] pointer-events-none"></div>

            <div class="w-full max-w-7xl mx-auto space-y-16 relative z-10">
                
                <!-- Section Header -->
                <div class="text-center space-y-5 max-w-3xl mx-auto">
                    <div class="inline-flex items-center gap-2.5 px-4.5 py-1.5 rounded-full bg-emerald-50 border border-emerald-200/80 text-[#035c43] font-bold text-xs uppercase tracking-widest shadow-sm">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#035c43]"></span>
                        </span>
                        <span>Verified Documentation Package</span>
                    </div>

                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight font-['Albert_Sans',sans-serif]">
                        <span class="text-[#035c43]">What You</span> 
                        <span class="text-gray-900 ml-2">Recieve</span>
                    </h2>

                    <p class="text-gray-600 text-base sm:text-lg lg:text-xl font-normal leading-relaxed">
                        Depending on the services requested , your complete documentation package can include
                    </p>
                </div>

                <!-- 3 Executive Benefit Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <!-- Benefit Card 1: Asset Tracking -->
                    <div 
                        @mouseenter="activeCard = 1"
                        class="rounded-3xl border transition-all duration-500 p-8 sm:p-9 text-left relative overflow-hidden group cursor-pointer bg-white flex flex-col justify-between"
                        :class="activeCard === 1 ? 'border-emerald-500/80 shadow-[0_20px_50px_rgba(3,92,67,0.15)] -translate-y-2 ring-1 ring-emerald-500/30' : 'border-slate-200/90 shadow-[0_10px_30px_rgba(0,0,0,0.04)] hover:border-emerald-400/60 hover:-translate-y-1'"
                    >
                        <!-- Top Accent Shimmer Line -->
                        <div class="h-1.5 w-full bg-gradient-to-r from-[#035c43] via-emerald-500 to-teal-400 absolute top-0 left-0"></div>
                        <div class="absolute inset-0 w-1/2 h-full bg-gradient-to-r from-transparent via-emerald-50/50 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-[350%] transition-transform duration-1000 ease-in-out pointer-events-none"></div>

                        <div class="space-y-6">
                            <!-- Top Badge & Icon Row -->
                            <div class="flex items-center justify-between">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200/80 flex items-center justify-center text-[#035c43] shadow-md group-hover:scale-110 group-hover:bg-[#035c43] group-hover:text-white transition-all duration-500">
                                    <svg class="w-8 h-8 fill-current transition-transform duration-500 group-hover:rotate-6" viewBox="0 0 24 24">
                                        <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                    </svg>
                                </div>
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-[#035c43] border border-emerald-200/80">
                                    AUDIT READY
                                </span>
                            </div>

                            <h3 class="text-2xl font-extrabold text-gray-900 group-hover:text-[#035c43] transition-colors">
                                Asset Tracking
                            </h3>

                            <ul class="space-y-4 text-sm sm:text-base font-medium text-gray-700">
                                <li class="flex items-start gap-3 group-hover:translate-x-1.5 transition-transform duration-300">
                                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-[#035c43] flex items-center justify-center text-xs font-black shrink-0 shadow-sm mt-0.5 group-hover:bg-[#035c43] group-hover:text-white transition-all duration-300">✓</span>
                                    <span class="leading-snug">Detailed IT Asset Inventory Report</span>
                                </li>
                                <li class="flex items-start gap-3 group-hover:translate-x-1.5 transition-transform duration-300 delay-75">
                                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-[#035c43] flex items-center justify-center text-xs font-black shrink-0 shadow-sm mt-0.5 group-hover:bg-[#035c43] group-hover:text-white transition-all duration-300">✓</span>
                                    <span class="leading-snug">Asset Identification &amp; Tracking</span>
                                </li>
                                <li class="flex items-start gap-3 group-hover:translate-x-1.5 transition-transform duration-300 delay-150">
                                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-[#035c43] flex items-center justify-center text-xs font-black shrink-0 shadow-sm mt-0.5 group-hover:bg-[#035c43] group-hover:text-white transition-all duration-300">✓</span>
                                    <span class="leading-snug">Serial Numbers &amp; Asset Tags Captured</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Benefit Card 2: Data Compliance -->
                    <div 
                        @mouseenter="activeCard = 2"
                        class="rounded-3xl border transition-all duration-500 p-8 sm:p-9 text-left relative overflow-hidden group cursor-pointer bg-white flex flex-col justify-between"
                        :class="activeCard === 2 ? 'border-emerald-500/80 shadow-[0_20px_50px_rgba(3,92,67,0.15)] -translate-y-2 ring-1 ring-emerald-500/30' : 'border-slate-200/90 shadow-[0_10px_30px_rgba(0,0,0,0.04)] hover:border-emerald-400/60 hover:-translate-y-1'"
                    >
                        <!-- Top Accent Shimmer Line -->
                        <div class="h-1.5 w-full bg-gradient-to-r from-[#035c43] via-emerald-500 to-teal-400 absolute top-0 left-0"></div>
                        <div class="absolute inset-0 w-1/2 h-full bg-gradient-to-r from-transparent via-emerald-50/50 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-[350%] transition-transform duration-1000 ease-in-out pointer-events-none"></div>

                        <div class="space-y-6">
                            <!-- Top Badge & Icon Row -->
                            <div class="flex items-center justify-between">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200/80 flex items-center justify-center text-[#035c43] shadow-md group-hover:scale-110 group-hover:bg-[#035c43] group-hover:text-white transition-all duration-500">
                                    <svg class="w-8 h-8 fill-current transition-transform duration-500 group-hover:rotate-6" viewBox="0 0 24 24">
                                        <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                                    </svg>
                                </div>
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-[#035c43] border border-emerald-200/80">
                                    NIST SP 800-88
                                </span>
                            </div>

                            <h3 class="text-2xl font-extrabold text-gray-900 group-hover:text-[#035c43] transition-colors">
                                Data Compliance
                            </h3>

                            <ul class="space-y-4 text-sm sm:text-base font-medium text-gray-700">
                                <li class="flex items-start gap-3 group-hover:translate-x-1.5 transition-transform duration-300">
                                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-[#035c43] flex items-center justify-center text-xs font-black shrink-0 shadow-sm mt-0.5 group-hover:bg-[#035c43] group-hover:text-white transition-all duration-300">✓</span>
                                    <span class="leading-snug">NIST SP 800-88 Data Destruction Reports</span>
                                </li>
                                <li class="flex items-start gap-3 group-hover:translate-x-1.5 transition-transform duration-300 delay-75">
                                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-[#035c43] flex items-center justify-center text-xs font-black shrink-0 shadow-sm mt-0.5 group-hover:bg-[#035c43] group-hover:text-white transition-all duration-300">✓</span>
                                    <span class="leading-snug">Official Certificate of Data Destruction</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Benefit Card 3: Recycling & Final Reports -->
                    <div 
                        @mouseenter="activeCard = 3"
                        class="rounded-3xl border transition-all duration-500 p-8 sm:p-9 text-left relative overflow-hidden group cursor-pointer bg-white flex flex-col justify-between"
                        :class="activeCard === 3 ? 'border-emerald-500/80 shadow-[0_20px_50px_rgba(3,92,67,0.15)] -translate-y-2 ring-1 ring-emerald-500/30' : 'border-slate-200/90 shadow-[0_10px_30px_rgba(0,0,0,0.04)] hover:border-emerald-400/60 hover:-translate-y-1'"
                    >
                        <!-- Top Accent Shimmer Line -->
                        <div class="h-1.5 w-full bg-gradient-to-r from-[#035c43] via-emerald-500 to-teal-400 absolute top-0 left-0"></div>
                        <div class="absolute inset-0 w-1/2 h-full bg-gradient-to-r from-transparent via-emerald-50/50 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-[350%] transition-transform duration-1000 ease-in-out pointer-events-none"></div>

                        <div class="space-y-6">
                            <!-- Top Badge & Icon Row -->
                            <div class="flex items-center justify-between">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200/80 flex items-center justify-center text-[#035c43] shadow-md group-hover:scale-110 group-hover:bg-[#035c43] group-hover:text-white transition-all duration-500">
                                    <svg class="w-8 h-8 fill-current transition-transform duration-500 group-hover:rotate-6" viewBox="0 0 24 24">
                                        <path d="M12 2A10 10 0 1 0 22 12 10.011 10.011 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8.009 8.009 0 0 1-8 8zm-2-12.5l2.5 3 2.5-3h-2V6h-1v1.5zm-3.5 6l3.5 2.5V15h1.5v-1.5H10l-3.5-3.5zm9 0l-3.5-3.5v1.5H12.5V15H14l3.5-2.5z"/>
                                    </svg>
                                </div>
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-[#035c43] border border-emerald-200/80">
                                    R2 &amp; EPA CERTIFIED
                                </span>
                            </div>

                            <h3 class="text-2xl font-extrabold text-gray-900 group-hover:text-[#035c43] transition-colors">
                                Recycling &amp; Final Reports
                            </h3>

                            <ul class="space-y-4 text-sm sm:text-base font-medium text-gray-700">
                                <li class="flex items-start gap-3 group-hover:translate-x-1.5 transition-transform duration-300">
                                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-[#035c43] flex items-center justify-center text-xs font-black shrink-0 shadow-sm mt-0.5 group-hover:bg-[#035c43] group-hover:text-white transition-all duration-300">✓</span>
                                    <span class="leading-snug">Official Certificate of E-Waste Recycling</span>
                                </li>
                                <li class="flex items-start gap-3 group-hover:translate-x-1.5 transition-transform duration-300 delay-75">
                                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-[#035c43] flex items-center justify-center text-xs font-black shrink-0 shadow-sm mt-0.5 group-hover:bg-[#035c43] group-hover:text-white transition-all duration-300">✓</span>
                                    <span class="leading-snug">Complete Final Processing Report</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- Section 4.5: Full-Width Policy Banner (No Cash Rebates — No Recycling Fees) -->
        <section class="w-full bg-gradient-to-r from-[#01281d] via-[#035c43] to-[#023e2d] text-white py-16 sm:py-20 px-6 sm:px-12 lg:px-16 relative overflow-hidden border-y border-emerald-500/30 group shadow-2xl">
            <!-- Animated Ambient Glow Accents -->
            <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-emerald-400/20 rounded-full blur-3xl pointer-events-none group-hover:scale-125 transition-transform duration-1000"></div>
            <div class="absolute -left-20 -top-20 w-80 h-80 bg-teal-400/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute inset-0 w-1/2 h-full bg-gradient-to-r from-transparent via-white/10 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-[350%] transition-transform duration-1000 ease-in-out pointer-events-none"></div>

            <div class="w-full max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-8 relative z-10">
                
                <div class="space-y-3.5 text-left max-w-3xl">
                    <span class="px-3.5 py-1 rounded-full bg-white/10 border border-white/20 text-emerald-200 font-extrabold text-xs tracking-wider uppercase backdrop-blur-md">
                        Clear &amp; Upfront Policy
                    </span>
                    <h3 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight font-['Albert_Sans',sans-serif]">
                        No Cash Rebates — No Recycling Fees
                    </h3>
                    <p class="text-emerald-100/90 text-base sm:text-lg lg:text-xl font-medium leading-relaxed">
                        We do not offer cash-back payments for equipment. Instead, our focus is on providing convenient, secure, and properly documented IT Asset Management, data destruction, and e-waste recycling services.
                    </p>
                </div>

                <div class="shrink-0">
                    <a 
                        href="{{ url('/contact-us') }}" 
                        class="inline-flex items-center gap-2.5 bg-white text-[#035c43] hover:bg-emerald-50 font-extrabold text-base sm:text-lg px-8 py-4 sm:px-10 sm:py-4.5 rounded-full shadow-2xl transition duration-300 whitespace-nowrap transform hover:scale-105 group-hover:shadow-emerald-900/50"
                    >
                        <span>Transparent Service</span>
                        <span class="text-xl transition-transform duration-200 group-hover:translate-x-1.5">→</span>
                    </a>
                </div>

            </div>
        </section>

        <!-- Section 5: Our Satisfied Clients Section (Matching Home Page) -->
        <x-testimonials />

        <!-- Section 6: Talk with an Expert Ratings Banner with Background Image Overlay -->
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
                <div class="lg:col-span-7 space-y-5 text-left">
                    <h2 class="text-3xl sm:text-5xl font-black text-white tracking-normal">
                        Talk with an Expert
                    </h2>
                    <p class="text-emerald-100 text-base sm:text-lg max-w-xl font-normal leading-relaxed">
                        Everyone Can Help Create a Full Circle. Let's Recycle Together. Call or email us to talk with an expert.
                    </p>
                    <div class="flex items-center gap-4 flex-wrap pt-2">
                        <a 
                            href="{{ url('/contact-us') }}" 
                            class="bg-[#035c43] hover:bg-white text-white hover:text-[#035c43] border border-emerald-400/30 px-7 py-3 rounded-full font-extrabold text-sm sm:text-base shadow-lg transition duration-300"
                        >
                            Contact Us
                        </a>
                        <a 
                            href="tel:+13034724701" 
                            class="bg-[#035c43] hover:bg-white text-white hover:text-[#035c43] border border-emerald-400/30 px-7 py-3 rounded-full font-extrabold text-sm sm:text-base shadow-lg transition duration-300 flex items-center gap-2"
                        >
                            <span>📞</span>
                            <span>+1-303-472-4701</span>
                        </a>
                    </div>
                </div>

                <!-- Right Side: Google & BBB Ratings -->
                <div class="lg:col-span-5 flex items-center justify-start lg:justify-end gap-10 sm:gap-16 pt-6 lg:pt-0">
                    
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

        <!-- Full Width Main Site Footer Component -->
        <x-footer />
    </body>
</html>
