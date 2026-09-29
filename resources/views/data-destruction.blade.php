<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="w-full overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Data Destruction – IT Investment Recoveries</title>
        <meta name="description" content="Secure data destruction in Denver. Hard drive shredding, degaussing, and NIST 800-88 / DoD compliant wiping with full serialized certificates of destruction.">

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
                    src="{{ asset('images/services/data-destruction-hero.webp') }}" 
                    alt="Data Destruction in Denver" 
                    class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000 ease-out brightness-95 contrast-105"
                />
                <div class="absolute inset-0 bg-gradient-to-b from-slate-950/75 via-[#01281d]/30 to-slate-950/85"></div>
            </div>

            <!-- Hero Center Content -->
            <div class="relative z-20 flex-grow flex flex-col items-center justify-center text-center px-4 sm:px-8 lg:px-12 pt-48 sm:pt-60 md:pt-64 pb-32 sm:pb-40 w-full">
                <div class="w-full max-w-5xl mx-auto space-y-5 sm:space-y-7">
                    
                    <!-- Title -->
                    <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-[80px] font-bold text-white tracking-tight leading-[1.1] font-['Albert_Sans',sans-serif] drop-shadow-[0_4px_20px_rgba(0,0,0,0.85)]">
                        Data Destruction in Denver
                    </h1>

                    <!-- Subtitle -->
                    <p class="text-gray-100 text-lg sm:text-xl md:text-2xl lg:text-3xl font-semibold max-w-4xl mx-auto leading-relaxed drop-shadow-md">
                        Top data destroyers in Denver securely eliminating business data to strict compliance standards with certified reporting.
                    </p>

                    <!-- CTA Button -->
                    <div class="pt-4">
                        <a 
                            href="{{ url('/contact-us') }}" 
                            class="inline-flex items-center justify-center gap-2.5 px-9 py-4 sm:px-11 sm:py-4.5 rounded-full bg-[#035c43] hover:bg-[#024734] text-white font-extrabold text-lg sm:text-xl tracking-wide shadow-2xl transition duration-300 transform hover:scale-105 group"
                        >
                            <span>Arrange A Pickup</span>
                            <span class="text-2xl transition-transform duration-200 group-hover:translate-x-1.5">→</span>
                        </a>
                    </div>

                </div>
            </div>
        </section>

        <!-- Section 2: Certified Data Destruction Professionals in Denver (Executive Light Photorealistic Suite) -->
        <section class="bg-gradient-to-b from-white via-slate-50/80 to-white text-gray-900 py-24 sm:py-32 px-4 sm:px-8 lg:px-16 w-full border-t border-slate-200/80 relative overflow-hidden">
            <!-- Ambient Background Glows -->
            <div class="absolute top-1/4 -left-36 w-[600px] h-[600px] bg-emerald-500/5 rounded-full blur-[160px] pointer-events-none"></div>
            <div class="absolute bottom-1/4 -right-36 w-[600px] h-[600px] bg-teal-500/5 rounded-full blur-[160px] pointer-events-none"></div>

            <div class="w-full max-w-7xl mx-auto space-y-16 relative z-10">
                
                <!-- Compliance Logos Light Glass Marquee (Increased Logo Dimensions) -->
                <div class="relative w-full overflow-hidden py-8 sm:py-10 bg-white/95 border border-slate-200 shadow-lg rounded-3xl backdrop-blur-xl group">
                    <div class="absolute inset-y-0 left-0 w-32 bg-gradient-to-r from-white via-white/90 to-transparent z-10 pointer-events-none"></div>
                    <div class="absolute inset-y-0 right-0 w-32 bg-gradient-to-l from-white via-white/90 to-transparent z-10 pointer-events-none"></div>
                    
                    <div class="animate-partner-marquee flex items-center space-x-12 sm:space-x-20 md:space-x-28">
                        <div class="flex items-center justify-center w-[220px] sm:w-[280px] h-[120px] sm:h-[150px] shrink-0 p-2">
                            <img src="{{ asset('images/home-slider/lruIoO-removebg-preview.webp') }}" alt="NIST Certification" class="w-full h-full object-contain filter drop-shadow-md transition-all duration-500 hover:scale-110" />
                        </div>
                        <div class="flex items-center justify-center w-[220px] sm:w-[280px] h-[120px] sm:h-[150px] shrink-0 p-2">
                            <img src="{{ asset('images/home-slider/khyuXt-removebg-preview-1.webp') }}" alt="Department of Defense Certification" class="w-full h-full object-contain filter drop-shadow-md transition-all duration-500 hover:scale-110" />
                        </div>
                        <div class="flex items-center justify-center w-[220px] sm:w-[280px] h-[120px] sm:h-[150px] shrink-0 p-2">
                            <img src="{{ asset('images/home-slider/AEfjkp-removebg-preview-1.webp') }}" alt="EPA Certification" class="w-full h-full object-contain filter drop-shadow-md transition-all duration-500 hover:scale-110" />
                        </div>

                        <!-- Seamless Loop Duplicate -->
                        <div class="flex items-center justify-center w-[220px] sm:w-[280px] h-[120px] sm:h-[150px] shrink-0 p-2">
                            <img src="{{ asset('images/home-slider/lruIoO-removebg-preview.webp') }}" alt="NIST Certification" class="w-full h-full object-contain filter drop-shadow-md transition-all duration-500 hover:scale-110" />
                        </div>
                        <div class="flex items-center justify-center w-[220px] sm:w-[280px] h-[120px] sm:h-[150px] shrink-0 p-2">
                            <img src="{{ asset('images/home-slider/khyuXt-removebg-preview-1.webp') }}" alt="Department of Defense Certification" class="w-full h-full object-contain filter drop-shadow-md transition-all duration-500 hover:scale-110" />
                        </div>
                        <div class="flex items-center justify-center w-[220px] sm:w-[280px] h-[120px] sm:h-[150px] shrink-0 p-2">
                            <img src="{{ asset('images/home-slider/AEfjkp-removebg-preview-1.webp') }}" alt="EPA Certification" class="w-full h-full object-contain filter drop-shadow-md transition-all duration-500 hover:scale-110" />
                        </div>
                    </div>
                </div>

                <!-- Photorealistic Visual Split Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                    
                    <!-- Left Side: Photorealistic Tech Media Frame -->
                    <div class="lg:col-span-5 relative group">
                        <div class="absolute -inset-1.5 bg-gradient-to-r from-emerald-500/30 via-teal-400/30 to-emerald-600/30 rounded-3xl blur-md opacity-50 group-hover:opacity-100 transition duration-700"></div>
                        <div class="relative rounded-3xl overflow-hidden border border-slate-200/90 shadow-[0_20px_50px_rgba(0,0,0,0.08)] bg-slate-900 aspect-[4/3] sm:aspect-[16/11] lg:aspect-square">
                            <img 
                                src="{{ asset('images/services/data-destruction-hero.png') }}" 
                                alt="Certified Data Destruction Professionals" 
                                class="w-full h-full object-cover object-center transform scale-105 group-hover:scale-110 transition-transform duration-700 ease-out brightness-105 contrast-105"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent opacity-60 group-hover:opacity-40 transition-opacity duration-500"></div>
                            
                            <!-- Shimmer Line -->
                            <div class="absolute inset-0 w-1/2 h-full bg-gradient-to-r from-transparent via-white/25 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-[350%] transition-transform duration-1000 ease-in-out pointer-events-none"></div>
                        </div>
                    </div>

                    <!-- Right Side: Primary Content Executive Card -->
                    <div class="lg:col-span-7 bg-white border border-slate-200/90 rounded-3xl p-8 sm:p-12 shadow-[0_20px_60px_rgba(3,92,67,0.08)] relative overflow-hidden space-y-6 text-left group">
                        <!-- Top Accent Shimmer Line -->
                        <div class="h-1.5 w-full bg-gradient-to-r from-[#035c43] via-emerald-500 to-teal-400 absolute top-0 left-0"></div>
                        <div class="absolute inset-0 w-1/2 h-full bg-gradient-to-r from-transparent via-emerald-50/60 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-[350%] transition-transform duration-1000 ease-in-out pointer-events-none"></div>

                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight font-['Albert_Sans',sans-serif]">
                            <span class="text-[#035c43]">Certified Data Destruction</span> 
                            <span class="text-gray-900 block sm:inline ml-1">Professionals in Denver</span>
                        </h2>

                        <p class="text-gray-700 text-base sm:text-lg lg:text-xl font-normal leading-relaxed">
                            As a top data destruction company in Denver, we uses industry-approved techniques like disk wiping, onsite hardware shredding, and degaussing to securely eliminate sensitive data from computers, hard drives, tapes and other devices. We adhere to NAID and NIST standards, providing certified reporting for compliance. Our information security experts make data destruction smooth, convenient and affordable for local businesses.
                        </p>
                    </div>

                </div>

                <!-- Action Section: Contact Callout Panel -->
                <div class="bg-gradient-to-br from-emerald-50/90 via-slate-50 to-teal-50/90 border border-emerald-200/80 rounded-3xl p-8 sm:p-12 lg:p-14 shadow-xl relative overflow-hidden group text-center space-y-8">
                    <!-- Ambient Soft Glow -->
                    <div class="absolute top-0 right-0 w-80 h-80 bg-emerald-400/10 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute inset-0 w-1/2 h-full bg-gradient-to-r from-transparent via-white/70 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-[350%] transition-transform duration-1000 ease-in-out pointer-events-none"></div>

                    <h3 class="text-3xl sm:text-4xl lg:text-5xl font-black text-[#035c43] tracking-tight leading-tight font-['Albert_Sans',sans-serif] max-w-4xl mx-auto">
                        Contact for Hassle Free <span class="text-gray-900">Data Destruction</span>
                    </h3>

                    <p class="text-gray-800 text-base sm:text-lg lg:text-xl font-medium leading-relaxed max-w-4xl mx-auto">
                        As a leader in data destruction in Denver, we make the process smooth and simple for our local clients. Contact us today to learn more about our affordable and reliable Denver data destruction services.
                    </p>

                    <div class="pt-2">
                        <a 
                            href="{{ url('/contact-us') }}" 
                            class="inline-flex items-center gap-3 px-10 py-4.5 rounded-full bg-[#035c43] hover:bg-[#024734] text-white font-extrabold text-lg sm:text-xl shadow-2xl transition-all duration-300 transform hover:scale-105 group/btn"
                        >
                            <span>Book Consultation</span>
                            <span class="text-2xl transition-transform duration-200 group-hover/btn:translate-x-1.5">→</span>
                        </a>
                    </div>
                </div>

            </div>
        </section>

        <!-- Section 3: What We Do? (Simplified Image & Heading Grid) -->
        <section class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100">
            <div class="w-full max-w-7xl mx-auto space-y-12">
                
                <!-- Section Heading -->
                <div class="text-center">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-normal">
                        <span class="text-[#035c43]">What</span> 
                        <span class="text-gray-900 ml-2">We Do?</span>
                    </h2>
                </div>

                <!-- 6 Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 sm:gap-10">
                    
                    <!-- Card 1: Hard Drive Shredding -->
                    <div class="relative w-full h-[380px] sm:h-[420px] rounded-2xl overflow-hidden shadow-lg border border-gray-200 group transition-all duration-300 hover:shadow-2xl">
                        <img 
                            src="{{ asset('images/services/hard-drive-shredding-card.png') }}" 
                            alt="Hard Drive Shredding" 
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-[#023e2d]/90 via-black/30 to-transparent"></div>
                        <div class="absolute bottom-0 inset-x-0 p-6 sm:p-8">
                            <h3 class="text-2xl sm:text-3xl font-black text-white leading-tight drop-shadow-md">
                                Hard Drive Shredding
                            </h3>
                        </div>
                    </div>

                    <!-- Card 2: Degaussing Services -->
                    <div class="relative w-full h-[380px] sm:h-[420px] rounded-2xl overflow-hidden shadow-lg border border-gray-200 group transition-all duration-300 hover:shadow-2xl">
                        <img 
                            src="{{ asset('images/services/degaussing-services-card.png') }}" 
                            alt="Degaussing Services" 
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-[#023e2d]/90 via-black/30 to-transparent"></div>
                        <div class="absolute bottom-0 inset-x-0 p-6 sm:p-8">
                            <h3 class="text-2xl sm:text-3xl font-black text-white leading-tight drop-shadow-md">
                                Degaussing Services
                            </h3>
                        </div>
                    </div>

                    <!-- Card 3: Disk Wiping & Sanitization -->
                    <div class="relative w-full h-[380px] sm:h-[420px] rounded-2xl overflow-hidden shadow-lg border border-gray-200 group transition-all duration-300 hover:shadow-2xl">
                        <img 
                            src="{{ asset('images/services/disk-wiping-sanitization-card.png') }}" 
                            alt="Disk Wiping & Sanitization" 
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-[#023e2d]/90 via-black/30 to-transparent"></div>
                        <div class="absolute bottom-0 inset-x-0 p-6 sm:p-8">
                            <h3 class="text-2xl sm:text-3xl font-black text-white leading-tight drop-shadow-md">
                                Disk Wiping &amp; Sanitization
                            </h3>
                        </div>
                    </div>

                    <!-- Card 4: Tape Destruction Services -->
                    <div class="relative w-full h-[380px] sm:h-[420px] rounded-2xl overflow-hidden shadow-lg border border-gray-200 group transition-all duration-300 hover:shadow-2xl">
                        <img 
                            src="{{ asset('images/services/tape-destruction-services-card.png') }}" 
                            alt="Tape Destruction Services" 
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-[#023e2d]/90 via-black/30 to-transparent"></div>
                        <div class="absolute bottom-0 inset-x-0 p-6 sm:p-8">
                            <h3 class="text-2xl sm:text-3xl font-black text-white leading-tight drop-shadow-md">
                                Tape Destruction Services
                            </h3>
                        </div>
                    </div>

                    <!-- Card 5: Electronics Destruction -->
                    <div class="relative w-full h-[380px] sm:h-[420px] rounded-2xl overflow-hidden shadow-lg border border-gray-200 group transition-all duration-300 hover:shadow-2xl">
                        <img 
                            src="{{ asset('images/services/electronics-destruction-card.png') }}" 
                            alt="Electronics Destruction" 
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-[#023e2d]/90 via-black/30 to-transparent"></div>
                        <div class="absolute bottom-0 inset-x-0 p-6 sm:p-8">
                            <h3 class="text-2xl sm:text-3xl font-black text-white leading-tight drop-shadow-md">
                                Electronics Destruction
                            </h3>
                        </div>
                    </div>

                    <!-- Card 6: Onsite Data Destruction -->
                    <div class="relative w-full h-[380px] sm:h-[420px] rounded-2xl overflow-hidden shadow-lg border border-gray-200 group transition-all duration-300 hover:shadow-2xl">
                        <img 
                            src="{{ asset('images/services/onsite-data-destruction-card.png') }}" 
                            alt="Onsite Data Destruction" 
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-[#023e2d]/90 via-black/30 to-transparent"></div>
                        <div class="absolute bottom-0 inset-x-0 p-6 sm:p-8">
                            <h3 class="text-2xl sm:text-3xl font-black text-white leading-tight drop-shadow-md">
                                Onsite Data Destruction
                            </h3>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- Section 4: Our Satisfied Clients Section (Matching Home Page) -->
        <x-testimonials />

        <!-- Section 5: Talk with an Expert Ratings Banner -->
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

        <!-- Full Width Main Site Footer Component -->
        <x-footer />
    </body>
</html>
