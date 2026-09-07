<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="w-full overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Computer Liquidator and Reseller in Denver | IT Asset Liquidation</title>
        <meta name="description" content="Denver's leading computer liquidator and reseller. We buy, sell, and liquidate laptops, desktops, servers, monitors, and networking equipment at affordable prices.">

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

        <!-- Fixed Bottom-Left Badge -->
        <x-events-badge />

        <!-- Section 1: Hero Banner -->
        <section class="relative min-h-[70vh] sm:min-h-[80vh] lg:min-h-[88vh] w-full flex flex-col justify-between overflow-hidden bg-[#1b5e3a]">
            <!-- Header Overlay -->
            <x-header active="services" />

            <!-- Background Image -->
            <div class="absolute inset-0 z-0 overflow-hidden w-full h-full">
                <img 
                    src="{{ asset('images/Liquidator/computer.webp') }}" 
                    alt="Computer Liquidator and Reseller in Denver" 
                    class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000 ease-out"
                />
                <div class="absolute inset-0 z-10 bg-gradient-to-b from-black/60 via-black/45 to-black/70"></div>
            </div>

            <!-- Hero Center Content -->
            <div class="relative z-20 flex-grow flex flex-col items-center justify-center text-center px-4 sm:px-8 lg:px-12 pt-36 sm:pt-48 md:pt-52 pb-20 sm:pb-28 w-full">
                <div class="w-full max-w-5xl mx-auto space-y-6">
                    
                    <!-- Title -->
                    <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-[76px] font-bold text-white tracking-tight leading-[1.15] font-['Amaranth',sans-serif] drop-shadow-[0_4px_25px_rgba(0,0,0,0.85)] max-w-4xl mx-auto">
                        Computer Liquidator and<br />
                        Reseller in Denver
                    </h1>

                    <!-- Sub-headline -->
                    <p class="text-gray-200 text-base sm:text-lg md:text-xl font-medium max-w-3xl mx-auto leading-relaxed drop-shadow-md">
                        Denver's leading computer liquidator selling quality laptops, desktops, servers &amp; networking equipment for businesses at affordable prices.
                    </p>

                    <!-- CTA Button -->
                    <div class="pt-4">
                        <a 
                            href="{{ url('/contact-us') }}" 
                            class="inline-flex items-center gap-2.5 px-10 py-3.5 rounded-full bg-[#10b981] hover:bg-[#059669] text-white font-extrabold text-base sm:text-lg shadow-2xl transition duration-300 transform hover:scale-105"
                        >
                            <span>Request Pickup</span>
                            <span class="text-xl">→</span>
                        </a>
                    </div>

                </div>
            </div>
        </section>

        <!-- Section 2: Computer Liquidation Services in Denver -->
        <section class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full">
            <div class="w-full max-w-5xl mx-auto text-center space-y-6">
                
                <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
                    <span class="text-[#34d399]">Computer Liquidation</span> 
                    <span class="text-gray-900 ml-2">Services in Denver</span>
                </h2>

                <p class="text-gray-700 text-base sm:text-lg font-medium leading-relaxed max-w-5xl mx-auto">
                    Our experienced team makes it easy for companies in Denver undergoing office relocation, downsizing, or closure to liquidate their surplus computer assets. We handle all aspects of the computer liquidation process efficiently and responsibly, including de-installation, transportation, and storage services. This includes liquidating computers, laptops, servers, monitors, printers, networking equipment, and other IT assets. We then resell operational equipment to businesses, schools, and individuals at discounted prices compared to new equipment.
                </p>

            </div>
        </section>

        <!-- Solid Dark Green Full-Width Band Divider -->
        <div class="bg-[#004d2e] h-8 sm:h-12 w-full"></div>

        <!-- Section 3: We Offer Heading -->
        <section class="bg-white text-gray-900 py-10 sm:py-14 px-4 w-full">
            <div class="w-full max-w-4xl mx-auto text-center">
                <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-normal flex items-center justify-center gap-3">
                    <span class="text-gray-900">We</span> 
                    <span class="text-[#34d399]">Offer</span>
                    <span class="text-[#34d399] text-2xl sm:text-3xl font-bold">▼</span>
                </h2>
            </div>
        </section>

        <!-- Section 4: 2 Slanted Service Cards (Dark Green Full-Width Banner) -->
        <section class="bg-white text-gray-900 py-4 sm:py-8 w-full overflow-hidden">
            <div class="bg-[#004d2e] text-white transform -skew-y-2 py-16 sm:py-24 w-full overflow-hidden shadow-2xl">
                <div class="transform skew-y-2 w-full max-w-7xl mx-auto px-6 sm:px-12 grid grid-cols-1 md:grid-cols-2 gap-10 lg:gap-14">
                    
                    <!-- Card 1: Asset Removal Services -->
                    <div class="flex flex-col items-center text-center space-y-5 group">
                        <div class="w-full max-w-md h-60 sm:h-68 rounded-2xl border-4 border-white shadow-2xl overflow-hidden transform group-hover:scale-105 transition duration-500 bg-gray-900">
                            <img 
                                src="{{ asset('images/Liquidator/download.webp') }}" 
                                alt="Asset Removal Services" 
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white pt-1">
                            Asset Removal Services
                        </h3>
                        <p class="text-emerald-100 text-xs sm:text-sm md:text-base font-normal leading-relaxed max-w-lg">
                            Our experienced team handles de-installation, transportation, and secure storage of your surplus IT assets. We remove laptops, computers, servers, printers, networking equipment, monitors, and other IT assets to safely liquidate them.
                        </p>
                    </div>

                    <!-- Card 2: Computer Resale Services -->
                    <div class="flex flex-col items-center text-center space-y-5 group">
                        <div class="w-full max-w-md h-60 sm:h-68 rounded-2xl border-4 border-white shadow-2xl overflow-hidden transform group-hover:scale-105 transition duration-500 bg-gray-900">
                            <img 
                                src="{{ asset('images/Liquidator/Untitled-design-8.webp') }}" 
                                alt="Computer Resale Services" 
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white pt-1">
                            Computer Resale Services
                        </h3>
                        <p class="text-emerald-100 text-xs sm:text-sm md:text-base font-normal leading-relaxed max-w-lg">
                            We resell quality desktop and laptop computers, printers, networking and server equipment, and monitors to businesses, schools, and individuals at discounted prices compared to new equipment.
                        </p>
                    </div>

                </div>
            </div>
        </section>

        <!-- Section 5: Our Satisfied Clients (White Background Section) -->
        <section 
            id="testimonials" 
            class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full"
            x-data="{ activeSlide: 0 }"
        >
            <div class="w-full max-w-7xl mx-auto space-y-12">
                
                <!-- Section Title -->
                <div class="text-center">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-normal">
                        <span class="text-[#34d399]">Our</span> 
                        <span class="text-gray-900 ml-2">Satisfied Clients</span>
                    </h2>
                </div>

                <!-- 3 Testimonial Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 sm:gap-10">
                    
                    <!-- Card 1 -->
                    <div class="bg-white text-gray-900 rounded-[22px] border border-emerald-300/80 p-6 sm:p-8 shadow-lg hover:shadow-xl transition-all duration-300 flex flex-col justify-between relative group overflow-hidden">
                        <div class="space-y-4 relative z-10">
                            <div class="flex items-center space-x-1 text-amber-400 text-lg justify-center">
                                ★★★★<span class="text-amber-300">☆</span>
                            </div>
                            <p class="text-gray-700 text-xs sm:text-sm leading-relaxed text-center font-normal">
                                "I have to say, I'm really impressed by professionalism and efficiency of It-Investment Recoveries team. If you're looking for hassle-free recycling, I highly recommend them."
                            </p>
                        </div>

                        <div class="flex items-center justify-center space-x-4 pt-6 border-t border-gray-100 mt-6 relative z-10">
                            <img 
                                src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80" 
                                alt="David Jones" 
                                class="w-12 h-12 rounded-full object-cover shadow-sm border border-gray-200"
                            />
                            <span class="font-bold text-gray-900 text-base">David Jones</span>
                        </div>

                        <div class="absolute bottom-4 right-4 text-emerald-100/80 font-serif text-7xl font-black select-none pointer-events-none">
                            ”
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white text-gray-900 rounded-[22px] border border-emerald-300/80 p-6 sm:p-8 shadow-lg hover:shadow-xl transition-all duration-300 flex flex-col justify-between relative group overflow-hidden">
                        <div class="space-y-4 relative z-10">
                            <div class="flex items-center space-x-1 text-amber-400 text-lg justify-center">
                                ★★★<span class="text-amber-300">☆☆</span>
                            </div>
                            <p class="text-gray-700 text-xs sm:text-sm leading-relaxed text-center font-normal">
                                "I recently hired It-Investment Recoveries for computer recycling in Denver, and I couldn't be happier. Their team was punctual, professional, and took care of everything. Highly recommended!"
                            </p>
                        </div>

                        <div class="flex items-center justify-center space-x-4 pt-6 border-t border-gray-100 mt-6 relative z-10">
                            <img 
                                src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&auto=format&fit=crop&q=80" 
                                alt="David Martinelli" 
                                class="w-12 h-12 rounded-full object-cover shadow-sm border border-gray-200"
                            />
                            <span class="font-bold text-gray-900 text-base">David Martinelli</span>
                        </div>

                        <div class="absolute bottom-4 right-4 text-emerald-100/80 font-serif text-7xl font-black select-none pointer-events-none">
                            ”
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white text-gray-900 rounded-[22px] border border-emerald-300/80 p-6 sm:p-8 shadow-lg hover:shadow-xl transition-all duration-300 flex flex-col justify-between relative group overflow-hidden">
                        <div class="space-y-4 relative z-10">
                            <div class="flex items-center space-x-1 text-amber-400 text-lg justify-center">
                                ★★★★★
                            </div>
                            <p class="text-gray-700 text-xs sm:text-sm leading-relaxed text-center font-normal">
                                "Their commitment to data security and environmentally responsible practices is commendable. A reliable choice for anyone looking to recycle electronics."
                            </p>
                        </div>

                        <div class="flex items-center justify-center space-x-4 pt-6 border-t border-gray-100 mt-6 relative z-10">
                            <img 
                                src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&auto=format&fit=crop&q=80" 
                                alt="Mark Richardson" 
                                class="w-12 h-12 rounded-full object-cover shadow-sm border border-gray-200"
                            />
                            <span class="font-bold text-gray-900 text-base">Mark Richardson</span>
                        </div>

                        <div class="absolute bottom-4 right-4 text-emerald-100/80 font-serif text-7xl font-black select-none pointer-events-none">
                            ”
                        </div>
                    </div>

                </div>

                <!-- Carousel Dots Indicator -->
                <div class="flex flex-col items-center space-y-4 pt-4">
                    <div class="flex items-center space-x-2">
                        <button class="w-3.5 h-3.5 rounded-full bg-[#3b82f6] shadow-sm"></button>
                        <button class="w-2.5 h-2.5 rounded-full bg-gray-300 hover:bg-gray-400 transition"></button>
                        <button class="w-2.5 h-2.5 rounded-full bg-gray-300 hover:bg-gray-400 transition"></button>
                        <button class="w-2.5 h-2.5 rounded-full bg-gray-300 hover:bg-gray-400 transition"></button>
                        <button class="w-2.5 h-2.5 rounded-full bg-gray-300 hover:bg-gray-400 transition"></button>
                    </div>
                </div>

            </div>
        </section>

        <!-- Section 6: Why Choose Us for Computer Liquidation in Denver? -->
        <section class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100">
            <div class="w-full max-w-7xl mx-auto space-y-12">
                
                <!-- Title -->
                <div class="text-center">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight max-w-4xl mx-auto leading-tight">
                        <span class="text-[#34d399]">Why Choose Us for Computer</span> 
                        <span class="text-gray-900 ml-2">Liquidation in</span><br />
                        <span class="text-gray-900">Denver?</span>
                    </h2>
                </div>

                <!-- 4 Columns Grid with Yellow Star Icons -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    
                    <!-- Column 1 -->
                    <div class="text-center space-y-3 px-2">
                        <div class="text-amber-400 text-4xl font-bold mx-auto">
                            ⭐
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-[#005a36] leading-snug">
                            Premium Used Computers and Electronic
                        </h3>
                    </div>

                    <!-- Column 2 -->
                    <div class="text-center space-y-3 px-2">
                        <div class="text-amber-400 text-4xl font-bold mx-auto">
                            ⭐
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-[#005a36] leading-snug">
                            Trust Over 18 Years of Experience
                        </h3>
                    </div>

                    <!-- Column 3 -->
                    <div class="text-center space-y-3 px-2">
                        <div class="text-amber-400 text-4xl font-bold mx-auto">
                            ⭐
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-[#005a36] leading-snug">
                            Hassle Free Office Liquidation
                        </h3>
                    </div>

                    <!-- Column 4 -->
                    <div class="text-center space-y-3 px-2">
                        <div class="text-amber-400 text-4xl font-bold mx-auto">
                            ⭐
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-[#005a36] leading-snug">
                            Transparent Pricing and Operations
                        </h3>
                    </div>

                </div>

            </div>
        </section>

        <!-- Section 7: Liquidate Your Electronic Quickly and Responsibly -->
        <section class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 w-full border-t border-gray-100">
            <div class="w-full max-w-4xl mx-auto text-center space-y-6">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight">
                    <span class="text-[#34d399]">Liquidate Your Electronic</span> 
                    <span class="text-gray-900 ml-2">Quickly and Responsibly</span>
                </h2>
                <p class="text-gray-700 text-sm sm:text-base md:text-lg font-normal leading-relaxed max-w-3xl mx-auto">
                    Contact us today to schedule a walkthrough and receive a no-obligation quote! We make computer liquidation and reselling stress-free. Call now to liquidate your electronic assets quickly and responsibly in Denver.
                </p>
                <div class="flex items-center justify-center gap-4 flex-wrap pt-4">
                    <a 
                        href="{{ url('/contact-us') }}" 
                        class="px-8 py-3.5 rounded-full bg-[#10b981] hover:bg-[#059669] text-white font-extrabold text-sm sm:text-base shadow-lg transition duration-300 transform hover:scale-105"
                    >
                        Request Pickup
                    </a>
                    <a 
                        href="{{ url('/contact-us') }}" 
                        class="px-8 py-3.5 rounded-full bg-[#10b981] hover:bg-[#059669] text-white font-extrabold text-sm sm:text-base shadow-lg transition duration-300 transform hover:scale-105"
                    >
                        Book Consultation
                    </a>
                </div>
            </div>
        </section>

        <!-- Section 8: Talk with an Expert Ratings Banner -->
        <section class="relative bg-[#004d2e] text-white py-16 sm:py-24 px-6 sm:px-12 lg:px-16 w-full shadow-inner overflow-hidden">
            
            <!-- Background Overlay -->
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

        <!-- Full Width Main Site Footer Component -->
        <x-footer />

    </body>
</html>
