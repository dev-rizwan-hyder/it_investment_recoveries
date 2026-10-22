<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="w-full overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Computer Liquidator and Reseller in Denver – IT Investment Recoveries</title>
        <meta name="description" content="Denver's leading computer liquidator and reseller. We buy, sell, and liquidate laptops, desktops, servers, monitors, and networking equipment at affordable prices.">

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

        <!-- Fixed Bottom-Left Badge -->
        <x-events-badge />

        <!-- Section 1: Hero Banner -->
        <section class="relative min-h-[85vh] sm:min-h-[90vh] lg:min-h-screen w-full flex flex-col justify-between overflow-hidden bg-transparent">
            <!-- Header Overlay -->
            <x-header active="services" />

            <!-- Background Image -->
            <div class="absolute inset-0 z-0 overflow-hidden w-full h-full">
                <img 
                    src="{{ asset('images/Liquidator/liquidator-hero.webp') }}" 
                    alt="Computer Liquidator and Reseller in Denver" 
                    class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000 ease-out brightness-95 contrast-105"
                />
                <div class="absolute inset-0 bg-gradient-to-b from-slate-950/75 via-[#01281d]/30 to-slate-950/85"></div>
            </div>

            <!-- Hero Center Content -->
            <div class="relative z-20 flex-grow flex flex-col items-center justify-center text-center px-4 sm:px-8 lg:px-12 pt-48 sm:pt-60 md:pt-64 pb-32 sm:pb-40 w-full">
                <div class="w-full max-w-5xl mx-auto space-y-6">
                    
                    <!-- Title -->
                    <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-[76px] font-bold text-white tracking-tight leading-[1.15] font-['Albert_Sans',sans-serif] drop-shadow-[0_4px_25px_rgba(0,0,0,0.85)] max-w-4xl mx-auto">
                        Computer Liquidator and<br />
                        Reseller in Denver
                    </h1>

                    <!-- Sub-headline -->
                    <p class="text-gray-100 text-lg sm:text-xl md:text-2xl font-semibold max-w-4xl mx-auto leading-relaxed drop-shadow-md">
                        Denver's leading computer liquidator selling quality laptops, desktops, servers &amp; networking equipment for businesses at affordable prices.
                    </p>

                    <!-- CTA Button -->
                    <div class="pt-4">
                        <a 
                            href="{{ url('/contact-us') }}" 
                            class="inline-flex items-center gap-2.5 px-10 py-3.5 rounded-full bg-[#035c43] hover:bg-[#024734] text-white font-extrabold text-base sm:text-lg shadow-2xl transition duration-300 transform hover:scale-105"
                        >
                            <span>Request Pickup</span>
                            <span class="text-xl">→</span>
                        </a>
                    </div>

                </div>
            </div>
        </section>

        <!-- Section 2: Computer Liquidation Services in Denver -->
        <section class="bg-gradient-to-b from-slate-50 via-white to-slate-50/80 text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full relative overflow-hidden border-b border-gray-100">
            <!-- Background Ambient Glow Accents -->
            <div class="absolute top-1/2 left-1/4 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[350px] bg-emerald-400/10 blur-[130px] pointer-events-none rounded-full"></div>
            <div class="absolute top-1/2 right-1/4 translate-x-1/2 -translate-y-1/2 w-[500px] h-[300px] bg-teal-400/10 blur-[120px] pointer-events-none rounded-full"></div>

            <div class="w-full max-w-7xl mx-auto relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                    
                    <!-- Left Column: Title & Verbatim Original Content Only -->
                    <div class="lg:col-span-7 space-y-6 text-left">
                        
                        <!-- Main Title (Exact Original Wording) -->
                        <h2 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-gray-900 leading-[1.15] font-['Albert_Sans',sans-serif]">
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#035c43] via-emerald-700 to-[#035c43]">Computer Liquidation</span>
                            <span class="text-gray-900 block mt-1 sm:mt-2">Services in Denver</span>
                        </h2>

                        <!-- Decorative Accent Bar (No text) -->
                        <div class="w-20 h-1.5 bg-gradient-to-r from-[#035c43] to-emerald-400 rounded-full"></div>

                        <!-- Content Cards (Strictly Verbatim Original Content - No Additions) -->
                        <div class="space-y-5 pt-2">
                            
                            <!-- Paragraph Lead with Left Accent Border -->
                            <div class="relative pl-5 sm:pl-6 border-l-4 border-[#035c43]">
                                <p class="text-gray-800 text-lg sm:text-xl font-semibold leading-relaxed">
                                    Our experienced team makes it easy for companies in Denver undergoing office relocation, downsizing, or closure to liquidate their surplus computer assets.
                                </p>
                            </div>

                            <!-- Executive Highlight Box -->
                            <div class="bg-gradient-to-r from-emerald-50/90 via-slate-50/80 to-emerald-50/40 rounded-2xl p-6 sm:p-7 border border-emerald-200/80 shadow-sm flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-[#035c43] text-white flex items-center justify-center shrink-0 shadow-md mt-0.5">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                        <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm-2 14l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-900 text-base sm:text-lg font-bold leading-relaxed">
                                    We handle all aspects of the computer liquidation process efficiently and responsibly, including de-installation, transportation, and storage services.
                                </p>
                            </div>

                            <!-- Dual Cards for Assets & Resale (No added text labels) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-gray-200/80 shadow-sm flex items-start gap-3.5">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-[#035c43] flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                            <path d="M20 18c1.1 0 1.99-.9 1.99-2L22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2H0v2h24v-2h-4zM4 6h16v10H4V6z"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-700 text-sm sm:text-base font-semibold leading-relaxed">
                                        This includes liquidating computers, laptops, servers, monitors, printers, networking equipment, and other IT assets.
                                    </p>
                                </div>

                                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-gray-200/80 shadow-sm flex items-start gap-3.5">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-[#035c43] flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                            <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-700 text-sm sm:text-base font-semibold leading-relaxed">
                                        We then resell operational equipment to businesses, schools, and individuals at discounted prices compared to new equipment.
                                    </p>
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- Right Column: Professional Hardware Showcase (Real-Life Facility Photo) -->
                    <div class="lg:col-span-5 relative">
                        <div class="relative rounded-3xl overflow-hidden border border-gray-200 shadow-2xl group bg-gray-50">
                            <img 
                                src="{{ asset('images/IT Disposal/ewaste-recycling-hero.webp') }}" 
                                alt="Computer Liquidation Services in Denver" 
                                class="w-full h-[440px] sm:h-[500px] object-cover object-center transform group-hover:scale-105 transition-transform duration-700 ease-out"
                            />
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Section 3 & 4: We Offer (Modern Executive Service Cards) -->
        <section class="relative bg-gradient-to-br from-[#013526] via-[#035c43] to-[#01251a] text-white py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full overflow-hidden border-y border-emerald-800/40 shadow-2xl">
            <!-- Subtle Architectural Grid Pattern Overlay with Soft Radial Fade -->
            <div class="absolute inset-0 pointer-events-none opacity-30" style="background-image: linear-gradient(to right, rgba(255, 255, 255, 0.12) 1px, transparent 1px), linear-gradient(to bottom, rgba(255, 255, 255, 0.12) 1px, transparent 1px); background-size: 52px 52px; -webkit-mask-image: radial-gradient(ellipse 85% 70% at 50% 50%, #000 45%, transparent 100%); mask-image: radial-gradient(ellipse 85% 70% at 50% 50%, #000 45%, transparent 100%);"></div>

            <!-- Ambient Lighting Glows -->
            <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-[800px] h-[320px] bg-emerald-400/15 blur-[130px] pointer-events-none rounded-full"></div>
            <div class="absolute -bottom-24 right-1/4 w-[550px] h-[260px] bg-teal-400/10 blur-[120px] pointer-events-none rounded-full"></div>

            <div class="w-full max-w-7xl mx-auto relative z-10 space-y-12 sm:space-y-16">
                
                <!-- We Offer ▼ Heading (Exact Original Text) -->
                <div class="text-center">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-normal flex items-center justify-center gap-3 font-['Albert_Sans',sans-serif]">
                        <span class="text-white">We</span> 
                        <span class="text-emerald-300">Offer</span>
                        <span class="text-emerald-300 text-2xl sm:text-3xl font-bold">▼</span>
                    </h2>
                </div>

                <!-- 2 Service Cards Grid (Real-Life Photos & Exact Original Text) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 max-w-6xl mx-auto">
                    
                    <!-- Card 1: Asset Removal Services -->
                    <div class="bg-white text-gray-900 rounded-3xl overflow-hidden shadow-2xl border border-white/20 flex flex-col justify-between group hover:shadow-[0_25px_60px_rgba(0,0,0,0.35)] transition-all duration-500">
                        <div class="w-full h-64 sm:h-72 overflow-hidden relative bg-gray-900">
                            <img 
                                src="{{ asset('images/Liquidator/asset-removal-service.webp') }}" 
                                alt="Asset Removal Services" 
                                class="w-full h-full object-cover object-center transform group-hover:scale-105 transition-transform duration-700 ease-out"
                            />
                        </div>
                        <div class="p-8 sm:p-10 flex-grow flex flex-col justify-start space-y-4 text-left">
                            <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-950 group-hover:text-[#035c43] transition-colors duration-300">
                                Asset Removal Services
                            </h3>
                            <p class="text-gray-600 text-base sm:text-lg font-medium leading-relaxed">
                                Our experienced team handles de-installation, transportation, and secure storage of your surplus IT assets. We remove laptops, computers, servers, printers, networking equipment, monitors, and other IT assets to safely liquidate them.
                            </p>
                        </div>
                    </div>

                    <!-- Card 2: Computer Resale Services -->
                    <div class="bg-white text-gray-900 rounded-3xl overflow-hidden shadow-2xl border border-white/20 flex flex-col justify-between group hover:shadow-[0_25px_60px_rgba(0,0,0,0.35)] transition-all duration-500">
                        <div class="w-full h-64 sm:h-72 overflow-hidden relative bg-gray-900">
                            <img 
                                src="{{ asset('images/Liquidator/computer-resale-service.webp') }}" 
                                alt="Computer Resale Services" 
                                class="w-full h-full object-cover object-center transform group-hover:scale-105 transition-transform duration-700 ease-out"
                            />
                        </div>
                        <div class="p-8 sm:p-10 flex-grow flex flex-col justify-start space-y-4 text-left">
                            <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-950 group-hover:text-[#035c43] transition-colors duration-300">
                                Computer Resale Services
                            </h3>
                            <p class="text-gray-600 text-base sm:text-lg font-medium leading-relaxed">
                                We resell quality desktop and laptop computers, printers, networking and server equipment, and monitors to businesses, schools, and individuals at discounted prices compared to new equipment.
                            </p>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- Section 5: Our Happy Clients Section (Matching Home Page) -->
        <x-testimonials />

        <!-- Section 6: Why Choose Us for Computer Liquidation in Denver? -->
        <section class="bg-gradient-to-b from-slate-50/70 via-white to-slate-50/50 text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100 relative overflow-hidden">
            <!-- Subtle Ambient Background Accents -->
            <div class="absolute top-1/3 left-10 w-96 h-96 bg-emerald-400/5 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-10 right-10 w-96 h-96 bg-teal-400/5 rounded-full blur-3xl pointer-events-none"></div>

            <div class="w-full max-w-7xl mx-auto space-y-12 sm:space-y-16 relative z-10">
                
                <!-- Title -->
                <div class="text-center space-y-4">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-gray-900 leading-tight font-['Albert_Sans',sans-serif] max-w-4xl mx-auto">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#035c43] via-emerald-700 to-[#035c43]">Why Choose Us for Computer</span> 
                        <span class="text-gray-900 block sm:inline sm:ml-2">Liquidation in Denver?</span>
                    </h2>
                    <div class="w-20 h-1.5 bg-gradient-to-r from-[#035c43] to-emerald-400 rounded-full mx-auto"></div>
                </div>

                <!-- 4 Columns Grid with Modern Feature Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                    
                    <!-- Column 1 -->
                    <div class="bg-white rounded-3xl p-8 border border-gray-200/80 shadow-sm hover:shadow-xl hover:border-emerald-300 hover:-translate-y-1.5 transition-all duration-300 flex flex-col items-center justify-center text-center group relative overflow-hidden min-h-[220px]">
                        <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-[#035c43] to-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 text-[#035c43] group-hover:bg-[#035c43] group-hover:text-white transition-all duration-300 flex items-center justify-center shadow-sm mb-6">
                            <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24">
                                <path d="M20 18c1.1 0 1.99-.9 1.99-2L22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2H0v2h24v-2h-4zM4 6h16v10H4V6z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-[#035c43] group-hover:text-gray-950 transition-colors leading-snug">
                            Premium Used Computers and Electronic
                        </h3>
                    </div>

                    <!-- Column 2 -->
                    <div class="bg-white rounded-3xl p-8 border border-gray-200/80 shadow-sm hover:shadow-xl hover:border-emerald-300 hover:-translate-y-1.5 transition-all duration-300 flex flex-col items-center justify-center text-center group relative overflow-hidden min-h-[220px]">
                        <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-[#035c43] to-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 text-[#035c43] group-hover:bg-[#035c43] group-hover:text-white transition-all duration-300 flex items-center justify-center shadow-sm mb-6">
                            <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24">
                                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-[#035c43] group-hover:text-gray-950 transition-colors leading-snug">
                            Trust Over 18 Years of Experience
                        </h3>
                    </div>

                    <!-- Column 3 -->
                    <div class="bg-white rounded-3xl p-8 border border-gray-200/80 shadow-sm hover:shadow-xl hover:border-emerald-300 hover:-translate-y-1.5 transition-all duration-300 flex flex-col items-center justify-center text-center group relative overflow-hidden min-h-[220px]">
                        <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-[#035c43] to-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 text-[#035c43] group-hover:bg-[#035c43] group-hover:text-white transition-all duration-300 flex items-center justify-center shadow-sm mb-6">
                            <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24">
                                <path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-[#035c43] group-hover:text-gray-950 transition-colors leading-snug">
                            Hassle Free Office Liquidation
                        </h3>
                    </div>

                    <!-- Column 4 -->
                    <div class="bg-white rounded-3xl p-8 border border-gray-200/80 shadow-sm hover:shadow-xl hover:border-emerald-300 hover:-translate-y-1.5 transition-all duration-300 flex flex-col items-center justify-center text-center group relative overflow-hidden min-h-[220px]">
                        <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-[#035c43] to-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 text-[#035c43] group-hover:bg-[#035c43] group-hover:text-white transition-all duration-300 flex items-center justify-center shadow-sm mb-6">
                            <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24">
                                <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-[#035c43] group-hover:text-gray-950 transition-colors leading-snug">
                            Transparent Pricing and Operations
                        </h3>
                    </div>

                </div>

            </div>
        </section>

        <!-- Section 8: Talk with an Expert Ratings Banner -->
        <section class="relative bg-[#035c43] text-white py-16 sm:py-24 px-6 sm:px-12 lg:px-16 w-full shadow-inner overflow-hidden">
            
            <!-- Background Overlay -->
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
                
                <!-- Left Side -->
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

                <!-- Right Side Ratings -->
                <div class="lg:col-span-5 flex items-center justify-start lg:justify-end gap-10 sm:gap-16 pt-6 lg:pt-0">
                    <div class="text-center space-y-1">
                        <div class="text-4xl sm:text-6xl font-black text-emerald-100 leading-none">4.8</div>
                        <div class="flex items-center justify-center gap-0.5 text-amber-300 text-sm">
                            <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                        </div>
                        <div class="text-xs sm:text-sm font-semibold text-emerald-100">2,394 Ratings</div>
                        <div class="text-xs font-bold text-white uppercase tracking-wider">Google Reviews</div>
                    </div>

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

        <!-- Section 7: Liquidate Your Electronic Quickly and Responsibly (Full-Width Parallax Background) -->
        <section 
            class="relative w-full py-12 sm:py-16 px-4 sm:px-8 lg:px-16 overflow-hidden bg-fixed bg-cover bg-center bg-no-repeat text-gray-900 border-t border-gray-200/80 shadow-inner"
            style="background-image: url('{{ asset('images/ewaste/fLATIRONS.webp') }}');"
        >
            <!-- Soft White Overlay for readability & smooth parallax depth -->
            <div class="absolute inset-0 bg-white/80 backdrop-blur-[1px] pointer-events-none"></div>

            <!-- Foreground Content -->
            <div class="relative z-10 w-full max-w-5xl mx-auto text-center space-y-5">
                
                <!-- Main Section Heading (Verbatim Original Text) -->
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-gray-900 leading-tight font-['Albert_Sans',sans-serif] max-w-4xl mx-auto">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#035c43] via-emerald-700 to-[#035c43]">Liquidate Your Electronic</span> 
                    <span class="text-gray-900 block sm:inline sm:ml-2">Quickly and Responsibly</span>
                </h2>

                <!-- Decorative Accent Bar -->
                <div class="w-20 h-1.5 bg-gradient-to-r from-[#035c43] to-emerald-400 rounded-full mx-auto"></div>

                <!-- Main Body Paragraph (Verbatim Original Text) -->
                <p class="text-gray-800 text-base sm:text-lg md:text-xl font-medium leading-relaxed max-w-3xl mx-auto">
                    Contact us today to schedule a walkthrough and receive a no-obligation quote! We make computer liquidation and reselling stress-free. Call now to liquidate your electronic assets quickly and responsibly in Denver.
                </p>

                <!-- Call To Action Buttons (Verbatim Original Text, Professional Styling) -->
                <div class="flex items-center justify-center gap-4 flex-wrap pt-2">
                    <a 
                        href="{{ url('/contact-us') }}" 
                        class="px-9 py-3.5 sm:py-4 rounded-full bg-[#035c43] hover:bg-[#024734] text-white font-extrabold text-base sm:text-lg shadow-xl hover:shadow-2xl transition duration-300 transform hover:scale-105"
                    >
                        Request Pickup
                    </a>
                    <a 
                        href="{{ url('/contact-us') }}" 
                        class="px-9 py-3.5 sm:py-4 rounded-full bg-white hover:bg-emerald-50 text-[#035c43] font-extrabold text-base sm:text-lg border-2 border-[#035c43] shadow-md transition duration-300 transform hover:scale-105"
                    >
                        Book Consultation
                    </a>
                </div>

            </div>
        </section>

        <!-- Full Width Main Site Footer Component -->
        <x-footer />

    </body>
</html>
