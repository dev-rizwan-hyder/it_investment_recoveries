<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="w-full overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Commercial/Business Pickup | Denver's Top Computer &amp; Electronics Recycling | IT Investment Recoveries</title>
        <meta name="description" content="Free E-Waste Recycling & Drop-Off Services in Denver CO. Residential & Business pickup, secure data destruction, and certified electronics recycling by IT Investment Recoveries.">

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

        <!-- Section 1: Hero Section -->
        <section class="relative min-h-[85vh] sm:min-h-[90vh] lg:min-h-screen w-full flex flex-col justify-between overflow-hidden bg-transparent">
            <!-- Top Transparent Header Overlay -->
            <x-header active="business" />

            <!-- Background Image with Ken Burns Subtle Scale Effect -->
            <div class="absolute inset-0 z-0 overflow-hidden w-full h-full">
                <img 
                    src="{{ asset('images/ewaste/business-hero.webp') }}" 
                    alt="Commercial &amp; Business Pickup - E-Waste Recycling Denver" 
                    class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000 ease-out brightness-95 contrast-105"
                />
                <div class="absolute inset-0 bg-gradient-to-b from-slate-950/75 via-[#01281d]/30 to-slate-950/85"></div>
            </div>

            <!-- Main Hero Center Text Content -->
            <div class="relative z-20 flex-grow flex flex-col items-center justify-center text-center px-4 sm:px-8 lg:px-12 pt-48 sm:pt-60 md:pt-64 pb-32 sm:pb-40 w-full">
                <div class="w-full max-w-7xl mx-auto space-y-6 sm:space-y-8">
                    
                    <!-- Main Title: Commercial & Business Pickup -->
                    <h1 class="text-5xl sm:text-6xl md:text-7xl lg:text-[84px] font-bold text-white tracking-tight leading-[1.1] font-['Albert_Sans',sans-serif] drop-shadow-[0_4px_20px_rgba(0,0,0,0.85)]">
                        Commercial / Business Pickup
                    </h1>

                    <!-- Green CTA Button -->
                    <div class="pt-2">
                        <a 
                            href="#schedule-pickup" 
                            class="inline-flex items-center justify-center gap-2 px-7 py-3 sm:px-9 sm:py-4 rounded-tl-[24px] rounded-bl-[24px] rounded-tr-[24px] rounded-br-none bg-gradient-to-r from-[#035c43] via-[#035c43] to-[#035c43] hover:bg-none hover:bg-white text-white hover:text-[#035c43] border-2 border-[#035c43] font-bold text-base sm:text-xl tracking-wide shadow-2xl transition-all duration-300 group"
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

                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-wide uppercase font-['Albert_Sans',sans-serif]">
                        <span class="text-[#222222]">IT INVESTMENT</span> 
                        <span class="text-[#035c43] ml-2 sm:ml-3">RECOVERIES</span>
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
                            Business Van Load Pick Up Fee
                        </p>
                        <p class="text-4xl sm:text-5xl md:text-6xl font-black text-[#035c43] tracking-tight">
                            $100.00
                        </p>
                    </div>

                    <!-- Residential or Business Truck Load Pick Up Fee -->
                    <div class="space-y-2 pt-2">
                        <p class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 tracking-tight leading-snug">
                            Business Truck Load Pick Up Fee
                        </p>
                        <p class="text-4xl sm:text-5xl md:text-6xl font-black text-[#035c43] tracking-tight">
                            $150.00
                        </p>
                    </div>

                    <!-- OR Separator -->
                    <div class="py-3">
                        <span class="text-3xl sm:text-4xl font-black text-[#035c43] uppercase tracking-widest px-6 py-2 rounded-full bg-emerald-50 border-2 border-emerald-300/80 inline-block shadow-sm">
                            OR
                        </span>
                    </div>

                    <!-- Free Drop Off Info -->
                    <div class="space-y-4">
                        <p class="text-xl sm:text-3xl md:text-4xl font-extrabold text-[#035c43] leading-snug">
                            Free Drop Off Location 5765 W 52nd Ave, Denver, CO 80212.
                        </p>
                        <p class="text-lg sm:text-2xl md:text-3xl font-bold text-gray-800 leading-relaxed">
                            Please Call <a href="tel:3034724701" class="text-[#035c43] font-extrabold underline hover:text-[#035c43]">303-472-4701</a> for Scheduling a Free Drop Off Recycling or Donation.
                        </p>
                        <p class="text-lg sm:text-2xl font-bold text-[#035c43] italic pt-2">
                            <a href="#schedule-pickup" class="hover:underline">Free IT Asset Disposition and Free E-Waste Recycling</a>
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
                    <div class="w-full h-1.5 bg-gradient-to-r from-emerald-400 via-[#035c43] to-emerald-400 rounded-full mb-8"></div>

                    <!-- Title -->
                    <div class="text-center mb-8">
                        <h2 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-[#035c43] font-['Albert_Sans',sans-serif] tracking-tight">
                            Accepted Items &amp; Fees:
                        </h2>
                    </div>

                    <!-- Bottom Accent Line under Title -->
                    <div class="w-full h-1 bg-emerald-200/80 rounded-full mb-8"></div>

                    <!-- Items List (Large text layout) -->
                    <div class="space-y-4 text-center text-lg sm:text-2xl md:text-3xl font-bold text-gray-800 leading-snug">
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Off-Site NIST Data Destruction Per Hard Drive: <strong class="text-[#035c43] font-extrabold">$5.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            On-Site NIST Data Destruction Per Hard Drive: <strong class="text-[#035c43] font-extrabold">$10.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Off-Site NON NIST Physical Data Destruction Per Hard Drive: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Adapters/cables: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Audio/Video Equipment: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Cable/satellite receivers: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            CD/DVD/Blu-Ray players: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Cell Phones: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Computers: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Computer speakers: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Connected home devices: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Digital cameras: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Digital projectors: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            eReaders: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Fax machines: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Flash drives: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Gaming consoles: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Hard drives &amp; SSD’s: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            iPod®/MP3 players: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Keyboards &amp; mice: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Laptops: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Computer LCD/ LED Monitors: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Mainframe: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Modems: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Printers: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Routers: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Scanners: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Shredders: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Servers: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Stereo receivers: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            Tablets: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                            UPS Backup &amp; Battery: <strong class="text-[#035c43] font-extrabold">$0.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors font-extrabold text-emerald-950">
                            OLD CRT TUBE TVS &amp; MONITORS: <strong class="text-[#035c43] font-extrabold">$35.00</strong>
                        </p>
                        <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors font-extrabold text-emerald-950">
                            ALL LCD/ LED TVS ONLY: <strong class="text-[#035c43] font-extrabold">$35.00</strong>
                        </p>
                    </div>

                    <!-- Bottom Accent Line -->
                    <div class="w-full h-1.5 bg-gradient-to-r from-emerald-400 via-[#035c43] to-emerald-400 rounded-full mt-8"></div>
                </div>

                <!-- Green Button below list -->
                <div class="text-center mt-10">
                    <a 
                        href="#schedule-pickup" 
                        class="inline-flex items-center justify-center gap-2.5 px-8 py-3.5 rounded-tl-[24px] rounded-bl-[24px] rounded-tr-[24px] rounded-br-none bg-[#035c43] hover:bg-[#024734] text-white font-bold text-lg tracking-wide shadow-xl transition-all duration-300 group"
                    >
                        <span class="font-extrabold tracking-wide">Book a Pickup</span>
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
                    <h3 class="text-3xl sm:text-4xl lg:text-[46px] font-bold text-[#035c43] sm:text-[#035c43] tracking-normal">
                        We Support &amp; Do Business With
                    </h3>
                    <h2 class="text-4xl sm:text-5xl lg:text-[54px] font-black text-[#222222] tracking-tight">
                        Certified Service Providers
                    </h2>
                </div>

                <!-- Sliding Logos Marquee Slider -->
                <div class="relative w-full overflow-hidden py-6">
                    <div class="animate-partner-marquee flex items-center space-x-16 sm:space-x-24 md:space-x-32 pr-16 sm:pr-24">
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0">
                            <img src="{{ asset('images/home-slider/khyuXt-removebg-preview-1.webp') }}" alt="Department of Defense Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0">
                            <img src="{{ asset('images/home-slider/AEfjkp-removebg-preview-1.webp') }}" alt="EPA Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0">
                            <img src="{{ asset('images/home-slider/lruIoO-removebg-preview.webp') }}" alt="NIST Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>

                        <!-- Seamless Loop Duplicate -->
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0">
                            <img src="{{ asset('images/home-slider/khyuXt-removebg-preview-1.webp') }}" alt="Department of Defense Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0">
                            <img src="{{ asset('images/home-slider/AEfjkp-removebg-preview-1.webp') }}" alt="EPA Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0">
                            <img src="{{ asset('images/home-slider/lruIoO-removebg-preview.webp') }}" alt="NIST Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 5: Our Happy Clients Section (Matching Home Page) -->
        <x-testimonials />

        <!-- Section 6: Recycle Your Electronics Securely -->
        <section class="bg-white text-gray-900 py-14 sm:py-20 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100 text-center">
            <div class="w-full max-w-5xl mx-auto space-y-6">
                
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight uppercase">
                    <span class="text-[#222222]">RECYCLE YOUR</span> 
                    <span class="text-[#035c43] ml-2">ELECTRONICS SECURELY</span>
                </h2>

                <p class="text-gray-700 text-base sm:text-lg md:text-xl leading-relaxed max-w-4xl mx-auto font-normal">
                    We provide secure, responsible electronics recycling and data destruction services for businesses and individuals throughout Denver and surrounding areas. Contact us to learn more about our automotive products and recycling solutions.
                </p>

                <div class="pt-4">
                    <a 
                        href="#schedule-pickup" 
                        class="inline-flex items-center justify-center gap-2.5 px-8 py-3.5 rounded-tl-[24px] rounded-bl-[24px] rounded-tr-[24px] rounded-br-none bg-[#035c43] hover:bg-[#024734] text-white font-bold text-base sm:text-lg tracking-wide shadow-xl transition-all duration-300 group"
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
            class="relative w-full py-20 px-6 sm:px-12 lg:px-20 overflow-hidden bg-fixed bg-cover bg-center bg-no-repeat text-gray-900 border-t-4 border-[#035c43]"
            style="background-image: url('{{ asset('images/ewaste/fLATIRONS.webp') }}');"
        >
            <!-- Soft White Overlay for readability -->
            <div class="absolute inset-0 bg-white/70 backdrop-blur-[1px] pointer-events-none"></div>

            <!-- Content Area (Left-Aligned) -->
            <div class="relative z-10 w-full max-w-7xl mx-auto">
                <div class="max-w-2xl space-y-6 text-left">
                    
                    <!-- Heading -->
                    <h2 class="text-4xl sm:text-5xl lg:text-[56px] font-black text-[#222222] tracking-tight leading-[1.12] font-['Albert_Sans',sans-serif]">
                        <span class="text-[#035c43] font-bold">Schedule a</span> Dropoff or<br />Pickup
                    </h2>

                    <!-- Paragraph -->
                    <p class="text-[#333333] text-sm sm:text-base lg:text-lg leading-relaxed font-normal max-w-xl">
                        Whether you're recycling a single device or an entire office full of equipment, we're here to make the process simple, secure, and environmentally responsible—because every device recycled is a step toward a healthier planet.
                    </p>

                    <!-- Contact Details -->
                    <div class="space-y-3.5 pt-2 text-sm sm:text-base font-semibold text-[#2d3748]">
                        <!-- Phone -->
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-[#035c43] shrink-0 fill-current" viewBox="0 0 24 24">
                                <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                            </svg>
                            <a href="tel:13034724701" class="font-bold text-[#2d3748] hover:text-[#035c43] transition-colors">+1-303-472-4701</a>
                        </div>

                        <!-- Email -->
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-[#035c43] shrink-0 fill-current" viewBox="0 0 24 24">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                            <a href="mailto:Support@itinvestmentrecoveries.com" class="font-bold text-[#2d3748] hover:text-[#035c43] transition-colors">Support@itinvestmentrecoveries.com</a>
                        </div>

                        <!-- Location -->
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-[#035c43] shrink-0 fill-current" viewBox="0 0 24 24">
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
                            class="px-7 py-3 rounded-full bg-[#035c43] hover:bg-[#024734] text-white font-bold text-sm sm:text-base shadow-md transition-all duration-300"
                        >
                            Book Consultation
                        </a>

                        <!-- Schedule Pickup -->
                        <a 
                            href="tel:13034724701" 
                            class="px-7 py-3 rounded-full bg-white/80 hover:bg-[#024734] text-[#035c43] hover:text-white border-2 border-[#035c43] font-bold text-sm sm:text-base shadow-sm transition-all duration-300"
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
