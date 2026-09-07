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
        <link href="https://fonts.googleapis.com/css2?family=Amaranth:ital,wght@0,400;0,700;1,400;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            emerald: {
                                950: '#002d1b',
                                900: '#004d2e',
                                800: '#005a36',
                                700: '#006c41',
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
        </style>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-white text-gray-900 font-['Plus_Jakarta_Sans',sans-serif] antialiased selection:bg-emerald-500 selection:text-white w-full overflow-x-hidden m-0 p-0">

        <!-- Fixed Bottom-Left Badge (Always Anchored to Bottom-Left) -->
        <x-events-badge />

        <!-- Section 1: Hero Section (Circuit Boards with Hard Drives Background) -->
        <section class="relative min-h-[75vh] sm:min-h-[85vh] lg:min-h-screen w-full flex flex-col justify-between overflow-hidden bg-black">
            <!-- Top Transparent Header Overlay -->
            <x-header active="services" />

            <!-- Background Image -->
            <div class="absolute inset-0 z-0 overflow-hidden w-full h-full">
                <img 
                    src="{{ asset('images/services/WhatsApp-Image-2026-03-03-at-2.48.49-AM.webp') }}" 
                    alt="IT Asset Management to the Next Level" 
                    class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000 ease-out"
                />
                <!-- Dark Gradient Overlay for Contrast -->
                <div class="absolute inset-0 z-10 bg-gradient-to-b from-black/75 via-black/50 to-black/80"></div>
            </div>

            <!-- Main Hero Center Content -->
            <div class="relative z-20 flex-grow flex flex-col items-center justify-center text-center px-4 sm:px-8 lg:px-12 pt-36 sm:pt-48 md:pt-56 pb-24 sm:pb-32 w-full">
                <div class="w-full max-w-5xl mx-auto space-y-5 sm:space-y-7">
                    
                    <!-- Main Title: IT Asset Management to the Next Level -->
                    <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-[80px] font-bold text-white tracking-tight leading-[1.1] font-['Amaranth',sans-serif] drop-shadow-[0_4px_20px_rgba(0,0,0,0.85)]">
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
                            class="inline-flex items-center justify-center gap-2.5 px-8 py-3.5 sm:px-10 sm:py-4 rounded-full bg-[#10b981] hover:bg-[#059669] text-white font-extrabold text-base sm:text-lg tracking-wide shadow-2xl transition duration-300 transform hover:scale-105 group"
                        >
                            <span>Arrange A Pickup</span>
                            <span class="text-xl transition-transform duration-200 group-hover:translate-x-1.5">→</span>
                        </a>
                    </div>

                </div>
            </div>
        </section>

        <!-- Section 2: Complete IT Asset Management Services Overview -->
        <section class="bg-gradient-to-b from-slate-50 via-white to-slate-50 text-gray-900 py-16 sm:py-20 px-4 sm:px-8 lg:px-16 w-full relative overflow-hidden border-b border-gray-100">
            <!-- Background Ambient Glow Accent -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-emerald-400/10 blur-[140px] pointer-events-none rounded-full"></div>

            <div class="w-full max-w-5xl mx-auto text-center space-y-8 relative z-10">
                
                <!-- Title -->
                <div class="space-y-3">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-100/80 border border-emerald-300/60 text-[#005a36] text-xs sm:text-sm font-extrabold uppercase tracking-wider">
                        <span>📊</span> Complete Lifecycle Solution
                    </div>
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
                        <span class="text-[#10b981] sm:text-[#34d399]">Complete IT Asset</span> 
                        <span class="text-gray-900 ml-2">Management Services</span>
                    </h2>
                </div>

                <!-- Executive Highlight Glass Card for Paragraphs -->
                <div class="bg-white/80 backdrop-blur-md rounded-3xl p-6 sm:p-10 border border-gray-200/90 shadow-xl space-y-4 max-w-4xl mx-auto text-left relative overflow-hidden">
                    <div class="absolute top-0 left-0 bottom-0 w-2 bg-gradient-to-b from-[#005a36] via-[#10b981] to-[#34d399]"></div>
                    <p class="text-gray-800 text-base sm:text-lg lg:text-xl font-medium leading-relaxed">
                        Our Complete IT Asset Management Services are designed for businesses that don't have the time or resources to inventory their IT equipment before e-waste recycling. We can inventory and document your IT Assets before they are processed for data destruction &amp; recycling.
                    </p>
                    <p class="text-gray-700 text-base sm:text-lg font-semibold leading-relaxed pt-2 border-t border-gray-100 text-[#005a36]">
                        You will receive detailed documentation of the equipment processed, including a Certificate of Data Destruction and E-Waste Recycling.
                    </p>
                </div>

            </div>
        </section>

        <!-- Section 3: How It Works? (4 Step Process Cards) -->
        <section class="bg-slate-50/50 text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100 relative">
            <div class="w-full max-w-7xl mx-auto space-y-12 sm:space-y-16">
                
                <!-- Section Title: How It Works? -->
                <div class="text-center space-y-3">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-normal">
                        <span class="text-[#10b981] sm:text-[#34d399]">How</span> 
                        <span class="text-gray-900 ml-2">It Works?</span>
                    </h2>
                    <p class="text-gray-600 text-base sm:text-lg font-medium max-w-2xl mx-auto">
                        Simple, transparent 4-step process for secure IT asset disposition
                    </p>
                </div>

                <!-- 4 Step Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                    
                    <!-- Card 1: STEP 01 -->
                    <div class="rounded-3xl border border-gray-200/90 bg-white shadow-xl hover:shadow-2xl hover:border-emerald-300 transition-all duration-300 transform hover:-translate-y-1.5 flex flex-col justify-between text-left group overflow-hidden">
                        <!-- Top Accent Stripe -->
                        <div class="h-2 w-full bg-gradient-to-r from-[#005a36] via-[#10b981] to-[#34d399]"></div>
                        
                        <div class="p-6 sm:p-7 space-y-5 flex-grow flex flex-col justify-between">
                            <div class="space-y-4">
                                <!-- Step Tag & Icon Row -->
                                <div class="flex items-center justify-between">
                                    <span class="px-4 py-1.5 rounded-full border border-emerald-500/30 text-[#005a36] font-extrabold text-xs tracking-wider uppercase bg-emerald-50/80">
                                        STEP 01
                                    </span>
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200/80 flex items-center justify-center text-[#005a36] shrink-0">
                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                            <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/>
                                        </svg>
                                    </div>
                                </div>
                                <!-- Title -->
                                <h3 class="text-xl font-extrabold text-gray-900 group-hover:text-[#005a36] transition-colors">
                                    Schedule or Drop Off
                                </h3>
                                <!-- Description -->
                                <p class="text-gray-600 text-sm leading-relaxed font-normal">
                                    Contact us to schedule a convenient pickup or bring your IT Assets directly to our facility.
                                </p>
                            </div>

                            <!-- Bottom Fee Table -->
                            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 flex items-center justify-between text-xs sm:text-sm font-bold text-gray-800">
                                <span class="text-gray-600">Drop-Off Fee</span>
                                <span class="text-[#10b981] font-black text-sm px-2.5 py-0.5 rounded-md bg-emerald-50 border border-emerald-200">FREE</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: STEP 02 -->
                    <div class="rounded-3xl border border-gray-200/90 bg-white shadow-xl hover:shadow-2xl hover:border-emerald-300 transition-all duration-300 transform hover:-translate-y-1.5 flex flex-col justify-between text-left group overflow-hidden">
                        <!-- Top Accent Stripe -->
                        <div class="h-2 w-full bg-gradient-to-r from-[#005a36] via-[#10b981] to-[#34d399]"></div>
                        
                        <div class="p-6 sm:p-7 space-y-5 flex-grow flex flex-col justify-between">
                            <div class="space-y-4">
                                <!-- Step Tag & Icon Row -->
                                <div class="flex items-center justify-between">
                                    <span class="px-4 py-1.5 rounded-full border border-emerald-500/30 text-[#005a36] font-extrabold text-xs tracking-wider uppercase bg-emerald-50/80">
                                        STEP 02
                                    </span>
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200/80 flex items-center justify-center text-[#005a36] shrink-0">
                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                            <path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm0 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
                                        </svg>
                                    </div>
                                </div>
                                <!-- Title -->
                                <h3 class="text-xl font-extrabold text-gray-900 group-hover:text-[#005a36] transition-colors">
                                    Pickup &amp; Recycling
                                </h3>
                                <!-- Description -->
                                <p class="text-gray-600 text-sm leading-relaxed font-normal">
                                    We charge a flat-rate pickup fee, with no additional recycling charges for general e-waste.
                                </p>
                            </div>

                            <!-- Bottom Fee Table -->
                            <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-200/80 space-y-2 text-xs font-bold text-gray-800">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 font-semibold">Flat Pickup Fee</span>
                                    <span class="text-[#005a36] font-black">$150.00</span>
                                </div>
                                <div class="flex items-center justify-between border-t border-slate-200/80 pt-2">
                                    <span class="text-gray-600 font-semibold">E-Waste Recycling</span>
                                    <span class="text-[#10b981] font-black px-2 py-0.5 rounded bg-emerald-50 border border-emerald-200">FREE</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: STEP 03 -->
                    <div class="rounded-3xl border border-gray-200/90 bg-white shadow-xl hover:shadow-2xl hover:border-emerald-300 transition-all duration-300 transform hover:-translate-y-1.5 flex flex-col justify-between text-left group overflow-hidden">
                        <!-- Top Accent Stripe -->
                        <div class="h-2 w-full bg-gradient-to-r from-[#005a36] via-[#10b981] to-[#34d399]"></div>
                        
                        <div class="p-6 sm:p-7 space-y-5 flex-grow flex flex-col justify-between">
                            <div class="space-y-4">
                                <!-- Step Tag & Icon Row -->
                                <div class="flex items-center justify-between">
                                    <span class="px-4 py-1.5 rounded-full border border-emerald-500/30 text-[#005a36] font-extrabold text-xs tracking-wider uppercase bg-emerald-50/80">
                                        STEP 03
                                    </span>
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200/80 flex items-center justify-center text-[#005a36] shrink-0">
                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                            <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                        </svg>
                                    </div>
                                </div>
                                <!-- Title -->
                                <h3 class="text-xl font-extrabold text-gray-900 group-hover:text-[#005a36] transition-colors">
                                    Asset Management
                                </h3>
                                <!-- Description -->
                                <p class="text-gray-600 text-sm leading-relaxed font-normal">
                                    Don't have time to inventory? We document manufacturer, model, serial number, asset tag, and status.
                                </p>
                            </div>

                            <!-- Bottom Fee Table -->
                            <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-200/80 space-y-2 text-xs font-bold text-gray-800">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 font-semibold">Off-Site Asset Mgmt</span>
                                    <span class="text-[#005a36] font-black text-right">$5.00 / item</span>
                                </div>
                                <div class="flex items-center justify-between border-t border-slate-200/80 pt-2">
                                    <span class="text-gray-600 font-semibold">On-Site Asset Mgmt</span>
                                    <span class="text-[#005a36] font-black text-right">$10.00 / item</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: STEP 04 -->
                    <div class="rounded-3xl border border-gray-200/90 bg-white shadow-xl hover:shadow-2xl hover:border-emerald-300 transition-all duration-300 transform hover:-translate-y-1.5 flex flex-col justify-between text-left group overflow-hidden">
                        <!-- Top Accent Stripe -->
                        <div class="h-2 w-full bg-gradient-to-r from-[#005a36] via-[#10b981] to-[#34d399]"></div>
                        
                        <div class="p-6 sm:p-7 space-y-5 flex-grow flex flex-col justify-between">
                            <div class="space-y-4">
                                <!-- Step Tag & Icon Row -->
                                <div class="flex items-center justify-between">
                                    <span class="px-4 py-1.5 rounded-full border border-emerald-500/30 text-[#005a36] font-extrabold text-xs tracking-wider uppercase bg-emerald-50/80">
                                        STEP 04
                                    </span>
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200/80 flex items-center justify-center text-[#005a36] shrink-0">
                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-5.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                                        </svg>
                                    </div>
                                </div>
                                <!-- Title -->
                                <h3 class="text-xl font-extrabold text-gray-900 group-hover:text-[#005a36] transition-colors">
                                    NIST Data Destruction
                                </h3>
                                <!-- Description -->
                                <p class="text-gray-600 text-sm leading-relaxed font-normal">
                                    For data-bearing devices, complete detailed reporting compliant with NIST SP 800-88 guidelines.
                                </p>
                            </div>

                            <!-- Bottom Fee Table -->
                            <div class="bg-slate-50 rounded-2xl p-3 border border-slate-200/80 space-y-1.5 text-xs font-bold text-gray-800">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 font-semibold">Off-Site NIST</span>
                                    <span class="text-[#005a36] font-black text-right">$5.00 / drive</span>
                                </div>
                                <div class="flex items-center justify-between border-t border-slate-200/80 pt-1.5">
                                    <span class="text-gray-600 font-semibold">On-Site NIST</span>
                                    <span class="text-[#005a36] font-black text-right">$10.00 / drive</span>
                                </div>
                                <div class="flex items-center justify-between border-t border-slate-200/80 pt-1.5">
                                    <span class="text-gray-600 font-semibold">Off-Site Non-NIST</span>
                                    <span class="text-[#10b981] font-black px-1.5 py-0.5 rounded bg-emerald-50 border border-emerald-200">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- Section 4: What You Recieve -->
        <section class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100">
            <div class="w-full max-w-7xl mx-auto space-y-12">
                
                <!-- Section Title & Subtitle -->
                <div class="text-center space-y-3">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-normal">
                        <span class="text-[#34d399]">What You</span> 
                        <span class="text-[#222222] ml-2">Recieve</span>
                    </h2>
                    <p class="text-gray-600 text-base sm:text-lg max-w-3xl mx-auto font-normal">
                        Depending on the services requested , your complete documentation package can include
                    </p>
                </div>

                <!-- 3 Benefit Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <!-- Benefit Card 1: Asset Tracking -->
                    <div class="rounded-[24px] border-2 border-emerald-500/40 p-8 bg-white shadow-lg hover:shadow-2xl transition duration-300 space-y-6 text-left">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-700">
                            <svg class="w-7 h-7 fill-current" viewBox="0 0 24 24">
                                <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-extrabold text-[#005a36]">
                            Asset Tracking
                        </h3>
                        <ul class="space-y-3 text-sm sm:text-base font-semibold text-gray-700">
                            <li class="flex items-start gap-2.5">
                                <span class="text-[#10b981] font-bold text-lg">✔</span>
                                <span>Detailed IT Asset Inventory Report</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="text-[#10b981] font-bold text-lg">✔</span>
                                <span>Asset Identification &amp; Tracking</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="text-[#10b981] font-bold text-lg">✔</span>
                                <span>Serial Numbers &amp; Asset Tags Captured</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Benefit Card 2: Data Compliance -->
                    <div class="rounded-[24px] border-2 border-emerald-500/40 p-8 bg-white shadow-lg hover:shadow-2xl transition duration-300 space-y-6 text-left">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-700">
                            <svg class="w-7 h-7 fill-current" viewBox="0 0 24 24">
                                <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-extrabold text-[#005a36]">
                            Data Compliance
                        </h3>
                        <ul class="space-y-3 text-sm sm:text-base font-semibold text-gray-700">
                            <li class="flex items-start gap-2.5">
                                <span class="text-[#10b981] font-bold text-lg">✔</span>
                                <span>NIST SP 800-88 Data Destruction Reports</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="text-[#10b981] font-bold text-lg">✔</span>
                                <span>Official Certificate of Data Destruction</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Benefit Card 3: Recycling & Final Reports -->
                    <div class="rounded-[24px] border-2 border-emerald-500/40 p-8 bg-white shadow-lg hover:shadow-2xl transition duration-300 space-y-6 text-left">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-700">
                            <svg class="w-7 h-7 fill-current" viewBox="0 0 24 24">
                                <path d="M12 2A10 10 0 1 0 22 12 10.011 10.011 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8.009 8.009 0 0 1-8 8zm-2-12.5l2.5 3 2.5-3h-2V6h-1v1.5zm-3.5 6l3.5 2.5V15h1.5v-1.5H10l-3.5-3.5zm9 0l-3.5-3.5v1.5H12.5V15H14l3.5-2.5z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-extrabold text-[#005a36]">
                            Recycling &amp; Final Reports
                        </h3>
                        <ul class="space-y-3 text-sm sm:text-base font-semibold text-gray-700">
                            <li class="flex items-start gap-2.5">
                                <span class="text-[#10b981] font-bold text-lg">✔</span>
                                <span>Official Certificate of E-Waste Recycling</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="text-[#10b981] font-bold text-lg">✔</span>
                                <span>Complete Final Processing Report</span>
                            </li>
                        </ul>
                    </div>

                </div>

                <!-- Callout Banner Box: No Cash Rebates — No Recycling Fees -->
                <div class="bg-[#004d2e] text-white p-8 sm:p-10 rounded-[28px] shadow-2xl flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="space-y-2 text-left">
                        <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                            No Cash Rebates — No Recycling Fees
                        </h3>
                        <p class="text-[#34d399] font-medium text-sm sm:text-base max-w-3xl leading-relaxed">
                            We do not offer cash-back payments for equipment. Instead, our focus is on providing convenient, secure, and properly documented IT Asset Management, data destruction, and e-waste recycling services.
                        </p>
                    </div>
                    <div class="shrink-0">
                        <a 
                            href="{{ url('/contact-us') }}" 
                            class="inline-block border-2 border-emerald-400 bg-emerald-800/80 hover:bg-white hover:text-[#004d2e] text-white font-extrabold text-sm sm:text-base px-8 py-3.5 rounded-full shadow-lg transition duration-300 whitespace-nowrap"
                        >
                            Transparent Service
                        </a>
                    </div>
                </div>

            </div>
        </section>

        <!-- Section 5: Our Satisfied Clients -->
        <section 
            id="testimonials" 
            class="bg-slate-50/50 text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100 relative"
            x-data="{ activeSlide: 0 }"
        >
            <div class="w-full max-w-7xl mx-auto space-y-12 sm:space-y-16">
                
                <!-- Section Title -->
                <div class="text-center">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-normal">
                        <span class="text-gray-900">Our</span> 
                        <span class="text-[#10b981] sm:text-[#34d399] ml-2">Satisfied Clients</span>
                    </h2>
                </div>

                <!-- 3 Testimonial Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 sm:gap-10">
                    
                    <!-- Card 1: David Jones -->
                    <div class="bg-white rounded-3xl border border-gray-200/90 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1.5 flex flex-col justify-between relative group overflow-hidden">
                        <!-- Top Accent Stripe -->
                        <div class="h-1.5 w-full bg-gradient-to-r from-[#005a36] via-[#10b981] to-[#34d399]"></div>

                        <div class="p-7 sm:p-8 space-y-6 flex-grow flex flex-col justify-between relative z-10">
                            <div class="space-y-4">
                                <!-- Star Rating (5 Stars out of 5) -->
                                <div class="flex items-center space-x-1 text-amber-400 text-lg">
                                    ★★★★★
                                </div>
                                <!-- Quote Text -->
                                <p class="text-gray-700 text-sm sm:text-base leading-relaxed font-normal italic">
                                    "I have to say, I'm really impressed by professionalism and efficiency of It-Investment Recoveries team. If you're looking for hassle-free recycling, I highly recommend them"
                                </p>
                            </div>

                            <!-- User Profile Info -->
                            <div class="flex items-center space-x-4 pt-5 border-t border-gray-100">
                                <img 
                                    src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80" 
                                    alt="David Jones" 
                                    class="w-13 h-13 rounded-full object-cover shadow-md border-2 border-emerald-100 shrink-0"
                                    style="width: 52px; height: 52px;"
                                />
                                <div>
                                    <div class="font-extrabold text-gray-900 text-base">David Jones</div>
                                    <div class="text-xs text-emerald-600 font-semibold">Verified Client</div>
                                </div>
                            </div>
                        </div>

                        <!-- Quote Background Watermark Accent -->
                        <div class="absolute bottom-4 right-5 text-emerald-100/60 font-serif text-7xl font-black select-none pointer-events-none">
                            ”
                        </div>
                    </div>

                    <!-- Card 2: David Martinelli -->
                    <div class="bg-white rounded-3xl border border-gray-200/90 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1.5 flex flex-col justify-between relative group overflow-hidden">
                        <!-- Top Accent Stripe -->
                        <div class="h-1.5 w-full bg-gradient-to-r from-[#005a36] via-[#10b981] to-[#34d399]"></div>

                        <div class="p-7 sm:p-8 space-y-6 flex-grow flex flex-col justify-between relative z-10">
                            <div class="space-y-4">
                                <!-- Star Rating (5 Stars out of 5) -->
                                <div class="flex items-center space-x-1 text-amber-400 text-lg">
                                    ★★★★★
                                </div>
                                <!-- Quote Text -->
                                <p class="text-gray-700 text-sm sm:text-base leading-relaxed font-normal italic">
                                    "I recently hired It-Investment Recoveries for computer recycling in Denver, and I couldn't be happier. Their team was punctual, professional, and took care of everything. Highly recommended!"
                                </p>
                            </div>

                            <!-- User Profile Info -->
                            <div class="flex items-center space-x-4 pt-5 border-t border-gray-100">
                                <img 
                                    src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&auto=format&fit=crop&q=80" 
                                    alt="David Martinelli" 
                                    class="w-13 h-13 rounded-full object-cover shadow-md border-2 border-emerald-100 shrink-0"
                                    style="width: 52px; height: 52px;"
                                />
                                <div>
                                    <div class="font-extrabold text-gray-900 text-base">David Martinelli</div>
                                    <div class="text-xs text-emerald-600 font-semibold">Verified Client</div>
                                </div>
                            </div>
                        </div>

                        <!-- Quote Background Watermark Accent -->
                        <div class="absolute bottom-4 right-5 text-emerald-100/60 font-serif text-7xl font-black select-none pointer-events-none">
                            ”
                        </div>
                    </div>

                    <!-- Card 3: Mark Richardson -->
                    <div class="bg-white rounded-3xl border border-gray-200/90 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1.5 flex flex-col justify-between relative group overflow-hidden">
                        <!-- Top Accent Stripe -->
                        <div class="h-1.5 w-full bg-gradient-to-r from-[#005a36] via-[#10b981] to-[#34d399]"></div>

                        <div class="p-7 sm:p-8 space-y-6 flex-grow flex flex-col justify-between relative z-10">
                            <div class="space-y-4">
                                <!-- Star Rating (5 Stars out of 5) -->
                                <div class="flex items-center space-x-1 text-amber-400 text-lg">
                                    ★★★★★
                                </div>
                                <!-- Quote Text -->
                                <p class="text-gray-700 text-sm sm:text-base leading-relaxed font-normal italic">
                                    "Their commitment to data security and environmentally responsible practices is commendable. A reliable choice for anyone looking to recycle electronics."
                                </p>
                            </div>

                            <!-- User Profile Info -->
                            <div class="flex items-center space-x-4 pt-5 border-t border-gray-100">
                                <img 
                                    src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&auto=format&fit=crop&q=80" 
                                    alt="Mark Richardson" 
                                    class="w-13 h-13 rounded-full object-cover shadow-md border-2 border-emerald-100 shrink-0"
                                    style="width: 52px; height: 52px;"
                                />
                                <div>
                                    <div class="font-extrabold text-gray-900 text-base">Mark Richardson</div>
                                    <div class="text-xs text-emerald-600 font-semibold">Verified Client</div>
                                </div>
                            </div>
                        </div>

                        <!-- Quote Background Watermark Accent -->
                        <div class="absolute bottom-4 right-5 text-emerald-100/60 font-serif text-7xl font-black select-none pointer-events-none">
                            ”
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- Section 6: Talk with an Expert Ratings Banner with Background Image Overlay -->
        <section class="relative bg-[#004d2e] text-white py-16 sm:py-24 px-6 sm:px-12 lg:px-16 w-full shadow-inner overflow-hidden">
            
            <!-- Subtle Background Cityscape Overlay -->
            <div class="absolute inset-0 z-0 pointer-events-none">
                <img 
                    src="{{ asset('images/home-banner.webp') }}" 
                    alt="Denver Background" 
                    class="w-full h-full object-cover opacity-25 mix-blend-overlay filter contrast-125 brightness-110 scale-105"
                />
                <div class="absolute inset-0 bg-gradient-to-r from-[#003d24]/90 via-[#0e8a55]/85 to-[#003d24]/90"></div>
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
                            class="bg-[#002d1b] hover:bg-white text-white hover:text-[#002d1b] border border-emerald-400/30 px-7 py-3 rounded-full font-extrabold text-sm sm:text-base shadow-lg transition duration-300"
                        >
                            Contact Us
                        </a>
                        <a 
                            href="tel:+13034724701" 
                            class="bg-[#002d1b] hover:bg-white text-white hover:text-[#002d1b] border border-emerald-400/30 px-7 py-3 rounded-full font-extrabold text-sm sm:text-base shadow-lg transition duration-300 flex items-center gap-2"
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
