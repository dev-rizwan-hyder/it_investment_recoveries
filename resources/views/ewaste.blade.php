<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="w-full overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Free E-Waste Recycling | Denver's Top Computer &amp; Electronics Recycling | IT Investment Recoveries</title>
        <meta name="description" content="Free E-Waste Recycling & Drop-Off Services in Denver CO. Residential & Business pickup, secure data destruction, and certified electronics recycling by IT Investment Recoveries.">

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

        <!-- Section 1: Hero Section (Using public/images/ewaste/fLATIRONS.webp) -->
        <section class="relative min-h-[75vh] sm:min-h-[85vh] lg:min-h-screen w-full flex flex-col justify-between overflow-hidden bg-black">
            <!-- Top Transparent Header Overlay -->
            <x-header active="ewaste" />

            <!-- Background Image with Ken Burns Subtle Scale Effect -->
            <div class="absolute inset-0 z-0 overflow-hidden w-full h-full">
                <img 
                    src="{{ asset('images/ewaste/ewaste.webp') }}" 
                    alt="Community & Business Events Available - E-Waste Recycling Denver" 
                    class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000 ease-out"
                />
                <!-- Dark Gradient Overlay for High Contrast Text -->
                <div class="absolute inset-0 z-10 bg-gradient-to-b from-black/65 via-black/40 to-black/75"></div>
            </div>

            <!-- Main Hero Center Text Content -->
            <div class="relative z-20 flex-grow flex flex-col items-center justify-center text-center px-4 sm:px-8 lg:px-12 pt-28 sm:pt-40 md:pt-48 pb-16 w-full">
                <div class="w-full max-w-7xl mx-auto space-y-6 sm:space-y-8">
                    
                    <!-- Main Title: Community & Business Events Available -->
                    <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-[84px] font-bold text-white tracking-tight leading-[1.1] font-['Amaranth',sans-serif] drop-shadow-[0_4px_20px_rgba(0,0,0,0.85)]">
                        Community &amp; Business Events<br class="hidden sm:inline" /> Available
                    </h1>

                    <!-- Green CTA Button -->
                    <div class="pt-2">
                        <a 
                            href="#schedule-pickup" 
                            class="inline-flex items-center justify-center gap-2 px-7 py-3 sm:px-9 sm:py-4 rounded-tl-[24px] rounded-bl-[24px] rounded-tr-[24px] rounded-br-none bg-gradient-to-r from-[#0e8a55] via-[#10b981] to-[#10b981] hover:bg-none hover:bg-white text-white hover:text-[#005a36] border-2 border-[#10b981] font-bold text-base sm:text-xl tracking-wide shadow-2xl transition-all duration-300 group"
                        >
                            <span class="font-extrabold tracking-wide">Request Quote</span>
                            <span class="text-xl transition-transform duration-200 group-hover:translate-x-1.5">→</span>
                        </a>
                    </div>

                </div>
            </div>
        </section>

        <!-- Section 2: IT INVESTMENT RECOVERIES Header & Pick Up Fees -->
        <section 
            id="pickup-fees" 
            class="bg-white text-[#222222] py-14 sm:py-20 px-4 sm:px-8 lg:px-16 w-full overflow-hidden"
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
            <div class="w-full max-w-5xl mx-auto flex flex-col items-center text-center space-y-6">
                
                <!-- Main Header: Logo Icon & IT INVESTMENT RECOVERIES -->
                <div 
                    class="flex flex-col items-center justify-center space-y-4 transform transition-all duration-700 ease-out"
                    :class="visible ? 'translate-y-0 opacity-100' : '-translate-y-12 opacity-0'"
                >

                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-wide uppercase font-['Plus_Jakarta_Sans',sans-serif]">
                        <span class="text-[#222222]">IT INVESTMENT</span> 
                        <span class="text-[#34d399] ml-2 sm:ml-3">RECOVERIES</span>
                    </h2>
                </div>

                <!-- Fees List -->
                <div 
                    class="space-y-8 text-center transform transition-all duration-700 delay-150 ease-out pt-4"
                    :class="visible ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'"
                >
                    <!-- Residential or Business Van Load Pick Up Fee -->
                    <div class="space-y-2">
                        <p class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 tracking-tight leading-snug">
                            Residential or Business Van Load Pick Up Fee
                        </p>
                        <p class="text-4xl sm:text-5xl md:text-6xl font-black text-[#005a36] tracking-tight">
                            $100.00
                        </p>
                    </div>

                    <!-- Residential or Business Truck Load Pick Up Fee -->
                    <div class="space-y-2 pt-2">
                        <p class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 tracking-tight leading-snug">
                            Residential or Business Truck Load Pick Up Fee
                        </p>
                        <p class="text-4xl sm:text-5xl md:text-6xl font-black text-[#005a36] tracking-tight">
                            $150.00
                        </p>
                    </div>

                    <!-- OR Separator -->
                    <div class="py-3">
                        <span class="text-3xl sm:text-4xl font-black text-[#10b981] uppercase tracking-widest px-6 py-2 rounded-full bg-emerald-50 border-2 border-emerald-300/80 inline-block shadow-sm">
                            OR
                        </span>
                    </div>

                    <!-- Free Drop Off Info -->
                    <div class="space-y-4">
                        <p class="text-xl sm:text-3xl md:text-4xl font-extrabold text-[#005a36] leading-snug">
                            Free Drop Off Location: 9735 E Colfax Ave Denver CO 80220
                        </p>
                        <p class="text-lg sm:text-2xl md:text-3xl font-bold text-gray-800 leading-relaxed">
                            Phone: <a href="tel:7203728408" class="text-[#005a36] font-extrabold underline hover:text-[#10b981]">720-372-8408</a> or Contact us for Free Drop Off Recycling Details...
                        </p>
                        <p class="text-lg sm:text-2xl font-bold text-[#10b981] italic pt-2">
                            <a href="#schedule-pickup" class="hover:underline">Schedule Dropoff or Pickup for your IT Equipment Today!</a>
                        </p>
                    </div>
                </div>

            </div>
        </section>

        <!-- Section 3: Accepted Items & Fees Box -->
        <section 
            id="accepted-items" 
            class="bg-white text-gray-900 pb-16 sm:pb-24 px-4 sm:px-8 lg:px-16 w-full overflow-hidden border-t border-gray-100"
            x-data="{ visible: false }"
            x-init="
                const observer = new IntersectionObserver(([entry]) => {
                    if (entry.isIntersecting) {
                        visible = true;
                    }
                }, { threshold: 0.1 });
                observer.observe($el);
            "
        >
            <div class="w-full max-w-5xl mx-auto">
                
                <!-- Main Container Box matching exact layout style -->
                <div 
                    class="bg-white rounded-[24px] border-2 border-emerald-600/30 p-6 sm:p-12 shadow-xl relative transform transition-all duration-700 ease-out"
                    :class="visible ? 'scale-100 opacity-100' : 'scale-95 opacity-0'"
                >
                    <!-- Top Green Accent Line -->
                    <div class="w-full h-1.5 bg-gradient-to-r from-emerald-400 via-[#005a36] to-emerald-400 rounded-full mb-8"></div>

                    <!-- Title -->
                    <div class="text-center mb-8">
                        <h2 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-[#005a36] font-['Amaranth',sans-serif] tracking-tight">
                            Accepted Items &amp; Fees:
                        </h2>
                    </div>

                    <!-- Bottom Accent Line under Title -->
                    <div class="w-full h-1 bg-emerald-200/80 rounded-full mb-8"></div>

                    <!-- Items List (Large text layout) -->
                    <div class="space-y-4 text-center text-lg sm:text-2xl md:text-3xl font-bold text-gray-800 leading-snug">
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            On Site Data Destruction Drop-off per Hard Drive: <strong class="text-[#005a36] font-extrabold">$15.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            On Site Hard Drive Destruction for 1 Hard Drive: <strong class="text-[#005a36] font-extrabold">$45.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Off Site Hard Drive Shredding - Data Destruction Certificate per Hard Drive: <strong class="text-[#005a36] font-extrabold">$15.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Air Conditioners: <strong class="text-[#005a36] font-extrabold">$20.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Auto Batteries / Copiers: <strong class="text-[#005a36] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Commercial Ice Machines: <strong class="text-[#005a36] font-extrabold">$50.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            32-50 CRT Cable TV / Large Screens: <strong class="text-[#005a36] font-extrabold">$90.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Flat Screens: <strong class="text-[#005a36] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Computers: <strong class="text-[#005a36] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Commercial Appliances: <strong class="text-[#005a36] font-extrabold">$50.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Commercial Printers / Large Plotters: <strong class="text-[#005a36] font-extrabold">$50.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Digital Cameras: <strong class="text-[#005a36] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Dry Mount Press: <strong class="text-[#005a36] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            E-Readers: <strong class="text-[#005a36] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Fitness Gear: <strong class="text-[#005a36] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Food Warmers: <strong class="text-[#005a36] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Garbage Disposals: <strong class="text-[#005a36] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            H/D Wooden Speakers: <strong class="text-[#005a36] font-extrabold">$10.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Household Trash Appliances: <strong class="text-[#005a36] font-extrabold">$5.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Keyboards &amp; Mice: <strong class="text-[#005a36] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Laptops: <strong class="text-[#005a36] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Lawn Mowers / Lawn Tractors: <strong class="text-[#005a36] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Microwave: <strong class="text-[#005a36] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Monitors: <strong class="text-[#005a36] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Plotters: <strong class="text-[#005a36] font-extrabold">$25.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Printers: <strong class="text-[#005a36] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Projection TV: <strong class="text-[#005a36] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Refrigerators: <strong class="text-[#005a36] font-extrabold">$50.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Servers: <strong class="text-[#005a36] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Small Appliances: <strong class="text-[#005a36] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Tablets: <strong class="text-[#005a36] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            UPS Battery Backups: <strong class="text-[#005a36] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors font-extrabold text-emerald-950">
                            OLD CRT TUBE TV'S &amp; MONITORS: <strong class="text-[#005a36] font-extrabold">$65.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors font-extrabold text-emerald-950">
                            27-50 CRT TUBE TV'S &amp; MONITORS: <strong class="text-[#005a36] font-extrabold">$85.00</strong>
                        </p>
                    </div>

                    <!-- Bottom Accent Line -->
                    <div class="w-full h-1.5 bg-gradient-to-r from-emerald-400 via-[#005a36] to-emerald-400 rounded-full mt-8"></div>
                </div>

                <!-- Green Button below list -->
                <div class="text-center mt-10">
                    <a 
                        href="#schedule-pickup" 
                        class="inline-flex items-center justify-center gap-2.5 px-8 py-3.5 rounded-tl-[24px] rounded-bl-[24px] rounded-tr-[24px] rounded-br-none bg-[#005a36] hover:bg-[#10b981] text-white font-bold text-lg tracking-wide shadow-xl transition-all duration-300 group"
                    >
                        <span class="font-extrabold tracking-wide">Check All Items</span>
                        <span class="text-xl transition-transform duration-200 group-hover:translate-x-1.5">→</span>
                    </a>
                </div>

            </div>
        </section>

        <!-- Section 4: Certified Service Providers Slider Section -->
        <section class="bg-white text-gray-800 py-14 sm:py-20 px-4 sm:px-8 w-full overflow-hidden border-t border-gray-100">
            <div class="w-full max-w-7xl mx-auto text-center">
                <!-- Title matching home page & screenshot -->
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

        <!-- Section 5: Our Satisfied Clients -->
        <section 
            id="testimonials" 
            class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100"
            x-data="{ activeSlide: 0 }"
        >
            <div class="w-full max-w-7xl mx-auto space-y-12">
                
                <!-- Section Title -->
                <div class="text-center">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-normal">
                        <span class="text-[#34d399]">Our</span> 
                        <span class="text-[#222222] ml-2">Satisfied Clients</span>
                    </h2>
                </div>

                <!-- 3 Testimonial Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 sm:gap-10">
                    
                    <!-- Card 1 -->
                    <div class="bg-white rounded-[22px] border border-emerald-500/40 p-6 sm:p-8 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col justify-between relative group overflow-hidden">
                        <div class="space-y-4 relative z-10">
                            <!-- Star Rating -->
                            <div class="flex items-center space-x-1 text-amber-400 text-lg">
                                ★★★★★
                            </div>
                            <!-- Quote Text -->
                            <p class="text-gray-700 text-base sm:text-lg leading-relaxed font-medium">
                                "IT Investment Recoveries provided exceptional service for our office cleanout. The team was prompt, professional, and took care of all our electronic waste safely and securely."
                            </p>
                        </div>

                        <!-- User Profile Info -->
                        <div class="flex items-center space-x-4 pt-6 border-t border-gray-100 mt-6 relative z-10">
                            <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-emerald-400 bg-emerald-100 shrink-0 flex items-center justify-center font-bold text-emerald-800 text-lg">
                                RC
                            </div>
                            <div>
                                <h4 class="font-extrabold text-gray-900 text-base sm:text-lg">Robert Chen</h4>
                                <p class="text-xs sm:text-sm text-gray-500 font-semibold">IT Operations Manager</p>
                            </div>
                        </div>

                        <!-- Quote Background Graphic Accent -->
                        <div class="absolute bottom-4 right-4 text-emerald-100/60 font-serif text-8xl font-black select-none pointer-events-none group-hover:text-emerald-200/50 transition-colors">
                            ”
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white rounded-[22px] border border-emerald-500/40 p-6 sm:p-8 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col justify-between relative group overflow-hidden">
                        <div class="space-y-4 relative z-10">
                            <!-- Star Rating -->
                            <div class="flex items-center space-x-1 text-amber-400 text-lg">
                                ★★★★★
                            </div>
                            <!-- Quote Text -->
                            <p class="text-gray-700 text-base sm:text-lg leading-relaxed font-medium">
                                "Great experience working with this company! Free drop off was straightforward, and they provided data destruction certificates for our hard drives. Highly recommend!"
                            </p>
                        </div>

                        <!-- User Profile Info -->
                        <div class="flex items-center space-x-4 pt-6 border-t border-gray-100 mt-6 relative z-10">
                            <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-emerald-400 bg-emerald-100 shrink-0 flex items-center justify-center font-bold text-emerald-800 text-lg">
                                SJ
                            </div>
                            <div>
                                <h4 class="font-extrabold text-gray-900 text-base sm:text-lg">Sarah Jenkins</h4>
                                <p class="text-xs sm:text-sm text-gray-500 font-semibold">Small Business Owner</p>
                            </div>
                        </div>

                        <!-- Quote Background Graphic Accent -->
                        <div class="absolute bottom-4 right-4 text-emerald-100/60 font-serif text-8xl font-black select-none pointer-events-none group-hover:text-emerald-200/50 transition-colors">
                            ”
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white rounded-[22px] border border-emerald-500/40 p-6 sm:p-8 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col justify-between relative group overflow-hidden">
                        <div class="space-y-4 relative z-10">
                            <!-- Star Rating -->
                            <div class="flex items-center space-x-1 text-amber-400 text-lg">
                                ★★★★★
                            </div>
                            <!-- Quote Text -->
                            <p class="text-gray-700 text-base sm:text-lg leading-relaxed font-medium">
                                "We've used them for both business truck load pickups and regular electronics recycling. Always reliable, certified, and environmentally compliant."
                            </p>
                        </div>

                        <!-- User Profile Info -->
                        <div class="flex items-center space-x-4 pt-6 border-t border-gray-100 mt-6 relative z-10">
                            <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-emerald-400 bg-emerald-100 shrink-0 flex items-center justify-center font-bold text-emerald-800 text-lg">
                                MV
                            </div>
                            <div>
                                <h4 class="font-extrabold text-gray-900 text-base sm:text-lg">Marcus Vance</h4>
                                <p class="text-xs sm:text-sm text-gray-500 font-semibold">Facility Director</p>
                            </div>
                        </div>

                        <!-- Quote Background Graphic Accent -->
                        <div class="absolute bottom-4 right-4 text-emerald-100/60 font-serif text-8xl font-black select-none pointer-events-none group-hover:text-emerald-200/50 transition-colors">
                            ”
                        </div>
                    </div>

                </div>

                <!-- Carousel Dots Indicator -->
                <div class="flex items-center justify-center space-x-2 pt-4">
                    <button class="w-3 h-3 rounded-full bg-[#10b981]"></button>
                    <button class="w-2.5 h-2.5 rounded-full bg-gray-300 hover:bg-gray-400 transition"></button>
                    <button class="w-2.5 h-2.5 rounded-full bg-gray-300 hover:bg-gray-400 transition"></button>
                    <button class="w-2.5 h-2.5 rounded-full bg-gray-300 hover:bg-gray-400 transition"></button>
                </div>

            </div>
        </section>

        <!-- Section 6: Recycle Your Electronics Securely -->
        <section class="bg-white text-gray-900 py-14 sm:py-20 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100 text-center">
            <div class="w-full max-w-5xl mx-auto space-y-6">
                
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight uppercase">
                    <span class="text-[#222222]">RECYCLE YOUR</span> 
                    <span class="text-[#34d399] ml-2">ELECTRONICS SECURELY</span>
                </h2>

                <p class="text-gray-700 text-base sm:text-lg md:text-xl leading-relaxed max-w-4xl mx-auto font-normal">
                    We provide secure, responsible electronics recycling and data destruction services for businesses and individuals throughout Denver and surrounding areas. Contact us to learn more about our automotive products and recycling solutions.
                </p>

                <div class="pt-4">
                    <a 
                        href="#schedule-pickup" 
                        class="inline-flex items-center justify-center gap-2.5 px-8 py-3.5 rounded-tl-[24px] rounded-bl-[24px] rounded-tr-[24px] rounded-br-none bg-[#005a36] hover:bg-[#10b981] text-white font-bold text-base sm:text-lg tracking-wide shadow-xl transition-all duration-300 group"
                    >
                        <span class="font-extrabold tracking-wide">Automotive &amp; Products</span>
                        <span class="text-xl transition-transform duration-200 group-hover:translate-x-1.5">→</span>
                    </a>
                </div>

            </div>
        </section>

        <!-- Section 7: Schedule a Dropoff or Pickup (Circuit Board fLATIRONS.webp Background) -->
        <section 
            id="schedule-pickup" 
            class="relative w-full py-20 px-6 sm:px-12 lg:px-20 overflow-hidden bg-center bg-cover bg-no-repeat text-gray-900 border-t-4 border-[#004d2e]"
            style="background-image: url('{{ asset('images/ewaste/fLATIRONS.webp') }}');"
        >
            <!-- Soft White Overlay for readability -->
            <div class="absolute inset-0 bg-white/70 backdrop-blur-[1px] pointer-events-none"></div>

            <!-- Content Area (Left-Aligned) -->
            <div class="relative z-10 w-full max-w-7xl mx-auto">
                <div class="max-w-2xl space-y-6 text-left">
                    
                    <!-- Heading -->
                    <h2 class="text-4xl sm:text-5xl lg:text-[56px] font-black text-[#222222] tracking-tight leading-[1.12] font-['Plus_Jakarta_Sans',sans-serif]">
                        <span class="text-[#34d399] font-bold">Schedule a</span> Dropoff or<br />Pickup
                    </h2>

                    <!-- Paragraph -->
                    <p class="text-[#333333] text-sm sm:text-base lg:text-lg leading-relaxed font-normal max-w-xl">
                        Whether you're recycling a single device or an entire office full of equipment, we're here to make the process simple, secure, and environmentally responsible—because every device recycled is a step toward a healthier planet.
                    </p>

                    <!-- Contact Details -->
                    <div class="space-y-3.5 pt-2 text-sm sm:text-base font-semibold text-[#2d3748]">
                        <!-- Phone -->
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-[#34d399] shrink-0 fill-current" viewBox="0 0 24 24">
                                <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                            </svg>
                            <a href="tel:13034724701" class="font-bold text-[#2d3748] hover:text-[#005a36] transition-colors">+1-303-472-4701</a>
                        </div>

                        <!-- Email -->
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-[#34d399] shrink-0 fill-current" viewBox="0 0 24 24">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                            <a href="mailto:Support@itinvestmentrecoveries.com" class="font-bold text-[#2d3748] hover:text-[#005a36] transition-colors">Support@itinvestmentrecoveries.com</a>
                        </div>

                        <!-- Location -->
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-[#34d399] shrink-0 fill-current" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                            <span class="font-bold text-[#2d3748]">5765 W 52nd Ave Denver Co 80212 USA</span>
                        </div>
                    </div>

                    <!-- Pill Buttons -->
                    <div class="flex items-center gap-4 pt-4 flex-wrap">
                        <!-- Book Consultation -->
                        <a 
                            href="tel:13034724701" 
                            class="px-7 py-3 rounded-full bg-[#004d2e] hover:bg-[#005a36] text-white font-bold text-sm sm:text-base shadow-md transition-all duration-300"
                        >
                            Book Consultation
                        </a>

                        <!-- Schedule Pickup -->
                        <a 
                            href="tel:7203728408" 
                            class="px-7 py-3 rounded-full bg-white/80 hover:bg-[#004d2e] text-[#004d2e] hover:text-white border-2 border-[#004d2e] font-bold text-sm sm:text-base shadow-sm transition-all duration-300"
                        >
                            Schedule Pickup
                        </a>
                    </div>

                </div>
            </div>
        </section>

        <!-- Full Width Main Site Footer Component -->
        <x-footer />

    </body>
</html>
