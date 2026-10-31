<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="w-full overflow-x-hidden">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Commercial/Business Pickup | Denver's Top Computer &amp; Electronics Recycling | IT Investment Recoveries
    </title>
    <meta name="description"
        content="Free E-Waste Recycling & Drop-Off Services in Denver CO. Residential & Business pickup, secure data destruction, and certified electronics recycling by IT Investment Recoveries.">

    <!-- Google Fonts: Amaranth & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Albert+Sans:ital,wght@0,100..900;1,100..900&family=Amaranth:ital,wght@0,400;0,700;1,400;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

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
        [x-cloak] {
            display: none !important;
        }

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

<body
    class="bg-[#035c43] text-white font-['Albert_Sans',sans-serif] antialiased selection:bg-[#035c43] selection:text-white w-full overflow-x-hidden m-0 p-0">

    <!-- Fixed Bottom-Left Badge (Always Anchored to Bottom-Left) -->
    <x-events-badge />

    <!-- Section 1: Hero Section -->
    <section
        class="relative min-h-[85vh] sm:min-h-[90vh] lg:min-h-screen w-full flex flex-col justify-between overflow-hidden bg-transparent">
        <!-- Top Transparent Header Overlay -->
        <x-header active="business" />

        <!-- Background Image with Ken Burns Subtle Scale Effect -->
        <div class="absolute inset-0 z-0 overflow-hidden w-full h-full">
            <img src="{{ asset('images/ewaste/business-hero.webp') }}"
                alt="Commercial &amp; Business Pickup - E-Waste Recycling Denver"
                class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000 ease-out brightness-95 contrast-105" />
            <div class="absolute inset-0 bg-gradient-to-b from-slate-950/75 via-[#01281d]/30 to-slate-950/85"></div>
        </div>

        <!-- Main Hero Center Text Content -->
        <div
            class="relative z-20 flex-grow flex flex-col items-center justify-center text-center px-4 sm:px-8 lg:px-12 pt-48 sm:pt-60 md:pt-64 pb-32 sm:pb-40 w-full">
            <div class="w-full max-w-7xl mx-auto space-y-6 sm:space-y-8">

                <!-- Main Title: Commercial & Business Pickup -->
                <h1
                    class="text-5xl sm:text-6xl md:text-7xl lg:text-[84px] font-bold text-white tracking-tight leading-[1.1] font-['Albert_Sans',sans-serif] drop-shadow-[0_4px_20px_rgba(0,0,0,0.85)]">
                    Commercial / Business Pickup
                </h1>

                <!-- Green CTA Button -->
                <div class="pt-2">
                    <a href="/contact-us"
                        class="inline-flex items-center justify-center gap-2 px-7 py-3 sm:px-9 sm:py-4 rounded-tl-[24px] rounded-bl-[24px] rounded-tr-[24px] rounded-br-none bg-gradient-to-r from-[#035c43] via-[#035c43] to-[#035c43] hover:bg-none hover:bg-white text-white hover:text-[#035c43] border-2 border-[#035c43] font-bold text-base sm:text-xl tracking-wide shadow-2xl transition-all duration-300 group">
                        <span class="font-extrabold tracking-wide">Request Quote</span>
                        <span class="text-xl transition-transform duration-200 group-hover:translate-x-1.5">→</span>
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- Section 2: IT INVESTMENT RECOVERIES Header & Pick Up Fees -->
    <section id="pickup-fees"
        class="bg-white text-[#222222] py-14 sm:py-20 px-4 sm:px-8 lg:px-16 w-full overflow-hidden"
        x-data="{ visible: false }" x-init="const observer = new IntersectionObserver(([entry]) => {
            if (entry.isIntersecting) {
                visible = true;
            }
        }, { threshold: 0.15 });
        observer.observe($el);">
        <div class="w-full max-w-5xl mx-auto flex flex-col items-center text-center space-y-6">

            <!-- Main Header: Logo Icon & IT INVESTMENT RECOVERIES -->
            <div class="flex flex-col items-center justify-center space-y-4 transform transition-all duration-700 ease-out"
                :class="visible ? 'translate-y-0 opacity-100' : '-translate-y-12 opacity-0'">

                <h2
                    class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-wide uppercase font-['Albert_Sans',sans-serif]">
                    <span class="text-[#222222]">IT INVESTMENT</span>
                    <span class="text-[#035c43] ml-2 sm:ml-3">RECOVERIES</span>
                </h2>
            </div>

            <!-- Fees List -->
            <div class="space-y-8 text-center transform transition-all duration-700 delay-150 ease-out pt-4"
                :class="visible ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'">
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
                    <span
                        class="text-3xl sm:text-4xl font-black text-[#035c43] uppercase tracking-widest px-6 py-2 rounded-full bg-emerald-50 border-2 border-emerald-300/80 inline-block shadow-sm">
                        OR
                    </span>
                </div>

                <!-- Free Drop Off Info -->
                <div class="space-y-4">
                    <p class="text-xl sm:text-3xl md:text-4xl font-extrabold text-[#035c43] leading-snug">
                        Free Drop Off Location 5765 W 52nd Ave, Denver, CO 80212.
                    </p>
                    <p class="text-lg sm:text-2xl md:text-3xl font-bold text-gray-800 leading-relaxed">
                        Please Call <a href="tel:3034724701"
                            class="text-[#035c43] font-extrabold underline hover:text-[#035c43]">303-472-4701</a> for
                        Scheduling a Free Drop Off Recycling or Donation.
                    </p>
                    <p class="text-lg sm:text-2xl font-bold text-[#035c43] italic pt-2">
                        <a href="#schedule-pickup" class="hover:underline">Free IT Asset Disposition and Free E-Waste
                            Recycling</a>
                    </p>
                </div>
            </div>

        </div>
    </section>

    <!-- Section 3: Accepted Items & Fees Box -->
    <section id="accepted-items"
        class="bg-white text-gray-900 pb-16 sm:pb-24 px-4 sm:px-8 lg:px-16 w-full overflow-hidden border-t border-gray-100"
        x-data="{ visible: false }" x-init="const observer = new IntersectionObserver(([entry]) => {
            if (entry.isIntersecting) {
                visible = true;
            }
        }, { threshold: 0.1 });
        observer.observe($el);">
        <div class="w-full max-w-5xl mx-auto">

            <!-- Main Container Box matching exact layout style -->
            <div class="bg-white rounded-[24px] border-2 border-emerald-600/30 p-6 sm:p-12 shadow-xl relative transform transition-all duration-700 ease-out"
                :class="visible ? 'scale-100 opacity-100' : 'scale-95 opacity-0'">
                <!-- Top Green Accent Line -->
                <div
                    class="w-full h-1.5 bg-gradient-to-r from-emerald-400 via-[#035c43] to-emerald-400 rounded-full mb-8">
                </div>

                <!-- Title -->
                <div class="text-center mb-8">
                    <h2
                        class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-[#035c43] font-['Albert_Sans',sans-serif] tracking-tight">
                        Accepted Items &amp; Fees:
                    </h2>
                </div>

                <!-- Bottom Accent Line under Title -->
                <div class="w-full h-1 bg-emerald-200/80 rounded-full mb-8"></div>

                <!-- Items List (Large text layout) -->
                <div class="space-y-4 text-center text-lg sm:text-2xl md:text-3xl font-bold text-gray-800 leading-snug">
                    <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                        Off-Site NIST Data Destruction Per Hard Drive: <strong
                            class="text-[#035c43] font-extrabold">$5.00</strong>
                    </p>
                    <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                        On-Site NIST Data Destruction Per Hard Drive: <strong
                            class="text-[#035c43] font-extrabold">$10.00</strong>
                    </p>
                    <p class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors">
                        Off-Site NON NIST Physical Data Destruction Per Hard Drive: <strong
                            class="text-[#035c43] font-extrabold">$0.00</strong>
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
                    <p
                        class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors font-extrabold text-emerald-950">
                        OLD CRT TUBE TVS &amp; MONITORS: <strong class="text-[#035c43] font-extrabold">$35.00</strong>
                    </p>
                    <p
                        class="hover:bg-emerald-50/60 py-2 px-3 rounded-lg transition-colors font-extrabold text-emerald-950">
                        ALL LCD/ LED TVS ONLY: <strong class="text-[#035c43] font-extrabold">$35.00</strong>
                    </p>
                </div>

                <!-- Bottom Accent Line -->
                <div
                    class="w-full h-1.5 bg-gradient-to-r from-emerald-400 via-[#035c43] to-emerald-400 rounded-full mt-8">
                </div>
            </div>
        </div>
    </section>

    <!-- Section 4.5: Full-Width Policy Banner (No Cash Rebates — No Recycling Fees) -->
    <section
        class="w-full bg-gradient-to-r from-[#01281d] via-[#035c43] to-[#023e2d] text-white py-16 sm:py-20 px-6 sm:px-12 lg:px-16 relative overflow-hidden border-y border-emerald-500/30 group shadow-2xl">
        <!-- Animated Ambient Glow Accents -->
        <div
            class="absolute -right-20 -bottom-20 w-96 h-96 bg-emerald-400/20 rounded-full blur-3xl pointer-events-none group-hover:scale-125 transition-transform duration-1000">
        </div>
        <div class="absolute -left-20 -top-20 w-80 h-80 bg-teal-400/15 rounded-full blur-3xl pointer-events-none">
        </div>
        <div
            class="absolute inset-0 w-1/2 h-full bg-gradient-to-r from-transparent via-white/10 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-[350%] transition-transform duration-1000 ease-in-out pointer-events-none">
        </div>

        <div
            class="w-full max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-8 relative z-10">

            <div class="space-y-3.5 text-left max-w-3xl">
                <span
                    class="px-3.5 py-1 rounded-full bg-white/10 border border-white/20 text-emerald-200 font-extrabold text-xs tracking-wider uppercase backdrop-blur-md">
                    Clear &amp; Upfront Policy
                </span>
                <h3
                    class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight font-['Albert_Sans',sans-serif]">
                    No Cash Rebates — No Recycling Fees
                </h3>
                <p class="text-emerald-100/90 text-base sm:text-lg lg:text-xl font-medium leading-relaxed">
                    We do not offer cash-back payments for equipment. Instead, our focus is on providing convenient,
                    secure, and properly documented IT Asset Management, data destruction, and e-waste recycling
                    services.
                </p>
            </div>

            <div class="shrink-0">
                <a href="{{ url('/contact-us') }}"
                    class="inline-flex items-center gap-2.5 bg-white text-[#035c43] hover:bg-emerald-50 font-extrabold text-base sm:text-lg px-8 py-4 sm:px-10 sm:py-4.5 rounded-full shadow-2xl transition duration-300 whitespace-nowrap transform hover:scale-105 group-hover:shadow-emerald-900/50">
                    <span>Transparent Service</span>
                    <span class="text-xl transition-transform duration-200 group-hover:translate-x-1.5">→</span>
                </a>
            </div>

        </div>
    </section>

    <!-- Section 5: Our Happy Clients Section (Matching Home Page) -->
    <x-testimonials />

    <!-- Section 6: Recycle Your Electronics Securely -->
    <section
        class="bg-white text-gray-900 py-14 sm:py-20 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100 text-center">
        <div class="w-full max-w-5xl mx-auto space-y-6">

            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight uppercase">
                <span class="text-[#222222]">RECYCLE YOUR</span>
                <span class="text-[#035c43] ml-2">ELECTRONICS SECURELY</span>
            </h2>

            <p class="text-gray-700 text-base sm:text-lg md:text-xl leading-relaxed max-w-4xl mx-auto font-normal">
                We provide secure, responsible electronics recycling and data destruction services for businesses and
                individuals throughout Denver and surrounding areas. Contact us to learn more about our automotive
                products and recycling solutions.
            </p>

            <div class="pt-4">
                <a href="/contact-us"
                    class="inline-flex items-center justify-center gap-2.5 px-8 py-3.5 rounded-tl-[24px] rounded-bl-[24px] rounded-tr-[24px] rounded-br-none bg-[#035c43] hover:bg-[#024734] text-white font-bold text-base sm:text-lg tracking-wide shadow-xl transition-all duration-300 group">
                    <span class="font-extrabold tracking-wide">Book a Pickup</span>
                    <span class="text-xl transition-transform duration-200 group-hover:translate-x-1.5">→</span>
                </a>
            </div>

        </div>
    </section>

    <!-- Full Width Main Site Footer Component -->
    <x-footer />

</body>

</html>
