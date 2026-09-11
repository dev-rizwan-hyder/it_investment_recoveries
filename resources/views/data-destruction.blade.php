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
        <section class="relative min-h-[75vh] sm:min-h-[85vh] lg:min-h-screen w-full flex flex-col justify-between overflow-hidden bg-transparent">
            <!-- Header Overlay -->
            <x-header active="services" />

            <!-- Background Image -->
            <div class="absolute inset-0 z-0 overflow-hidden w-full h-full">
                <img 
                    src="{{ asset('images/services/WhatsApp-Image-2026-03-03-at-2.48.49-AM.webp') }}" 
                    alt="Data Destruction in Denver" 
                    class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000 ease-out"
                />
                
            </div>

            <!-- Hero Center Content -->
            <div class="relative z-20 flex-grow flex flex-col items-center justify-center text-center px-4 sm:px-8 lg:px-12 pt-36 sm:pt-48 md:pt-56 pb-24 sm:pb-32 w-full">
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

        <!-- Section 2: Certified Data Destruction Professionals in Denver -->
        <section class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full overflow-hidden">
            <div class="w-full max-w-6xl mx-auto text-center space-y-10">
                
                <!-- Sliding Logos Marquee Slider (Same as Home Slider) -->
                <div class="relative w-full overflow-hidden py-4 border-b border-gray-100 mb-8">
                    <div class="animate-partner-marquee flex items-center space-x-12 sm:space-x-20 md:space-x-28">
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0">
                            <img src="{{ asset('images/home-slider/lruIoO-removebg-preview.webp') }}" alt="NIST Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0">
                            <img src="{{ asset('images/home-slider/khyuXt-removebg-preview-1.webp') }}" alt="Department of Defense Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0">
                            <img src="{{ asset('images/home-slider/AEfjkp-removebg-preview-1.webp') }}" alt="EPA Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>

                        <!-- Seamless Loop Duplicate -->
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0">
                            <img src="{{ asset('images/home-slider/lruIoO-removebg-preview.webp') }}" alt="NIST Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0">
                            <img src="{{ asset('images/home-slider/khyuXt-removebg-preview-1.webp') }}" alt="Department of Defense Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="flex items-center justify-center w-[180px] h-[180px] shrink-0">
                            <img src="{{ asset('images/home-slider/AEfjkp-removebg-preview-1.webp') }}" alt="EPA Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                    </div>
                </div>

                <!-- Title -->
                <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
                    <span class="text-[#035c43]">Certified Data Destruction</span> 
                    <span class="text-gray-900 block sm:inline ml-2">Professionals in Denver</span>
                </h2>

                <!-- Main Description -->
                <p class="text-gray-700 text-lg sm:text-xl lg:text-2xl font-medium leading-relaxed max-w-5xl mx-auto">
                    As a top data destruction company in Denver, we uses industry-approved techniques like disk wiping, onsite hardware shredding, and degaussing to securely eliminate sensitive data from computers, hard drives, tapes and other devices. We adhere to NAID and NIST standards, providing certified reporting for compliance. Our information security experts make data destruction smooth, convenient and affordable for local businesses.
                </p>

                <!-- Sub-Heading & Paragraph -->
                <div class="pt-6 space-y-5">
                    <h3 class="text-3xl sm:text-4xl font-black text-[#035c43]">
                        Contact for Hassle Free <span class="text-gray-900">Data Destruction</span>
                    </h3>
                    <p class="text-gray-800 text-lg sm:text-xl md:text-2xl max-w-5xl mx-auto leading-relaxed font-medium">
                        As a leader in data destruction in Denver, we make the process smooth and simple for our local clients. Contact us today to learn more about our affordable and reliable Denver data destruction services.
                    </p>
                    <div class="pt-3">
                        <a 
                            href="{{ url('/contact-us') }}" 
                            class="inline-flex items-center gap-2.5 px-9 py-4 rounded-full bg-[#035c43] hover:bg-[#024734] text-white font-extrabold text-lg shadow-xl transition duration-300 transform hover:scale-105"
                        >
                            <span>Book Consultation</span>
                            <span class="text-xl">→</span>
                        </a>
                    </div>
                </div>

            </div>
        </section>

        <!-- Section 3: What We Do? (3D Flip Cards Grid) -->
        <section class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100">
            <div class="w-full max-w-7xl mx-auto space-y-16">
                
                <!-- Section Heading -->
                <div class="text-center space-y-3">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-normal">
                        <span class="text-[#035c43]">What</span> 
                        <span class="text-gray-900 ml-2">We Do?</span>
                    </h2>
                    <p class="text-gray-600 text-base sm:text-lg max-w-2xl mx-auto font-medium">
                        Hover over any card below to explore our comprehensive data destruction solutions.
                    </p>
                </div>

                <!-- 6 3D Flip Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 sm:gap-10">
                    
                    <!-- Card 1: Hard Drive Shredding -->
                    <div class="group h-[420px] sm:h-[440px] [perspective:1000px] w-full cursor-pointer">
                        <div class="relative w-full h-full rounded-2xl shadow-xl transition-all duration-700 [transform-style:preserve-3d] group-hover:[transform:rotateY(180deg)]">
                            
                            <!-- Front Side -->
                            <div class="absolute inset-0 w-full h-full rounded-2xl overflow-hidden [backface-visibility:hidden] border border-gray-200 shadow-lg bg-gray-900 flex flex-col justify-end">
                                <img 
                                    src="{{ asset('images/services/WhatsApp-Image-2026-03-03-at-2.48.49-AM.webp') }}" 
                                    alt="Hard Drive Shredding" 
                                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 opacity-90"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-[#023e2d]/95 via-black/40 to-transparent"></div>
                                <div class="relative z-10 p-6 sm:p-8 space-y-2.5 text-left">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#035c43]/90 text-emerald-200 text-xs font-bold uppercase tracking-wider backdrop-blur-sm border border-emerald-500/30">
                                        <span>Hover to Reveal</span>
                                        <svg class="w-3.5 h-3.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </span>
                                    <h3 class="text-2xl sm:text-3xl font-black text-white leading-tight drop-shadow-md">
                                        Hard Drive Shredding
                                    </h3>
                                </div>
                            </div>

                            <!-- Back Side -->
                            <div class="absolute inset-0 w-full h-full rounded-2xl p-6 sm:p-8 bg-gradient-to-br from-[#023e2d] via-[#035c43] to-[#01281d] text-white [transform:rotateY(180deg)] [backface-visibility:hidden] border border-emerald-600/40 shadow-2xl flex flex-col justify-between text-left">
                                <div>
                                    <div class="flex items-center justify-between border-b border-emerald-500/30 pb-3 mb-4">
                                        <h3 class="text-xl sm:text-2xl font-black text-white tracking-wide">
                                            Hard Drive Shredding
                                        </h3>
                                        <span class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-emerald-300 shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                    </div>
                                    <p class="text-emerald-100 text-sm sm:text-base font-normal leading-relaxed overflow-y-auto max-h-[220px]">
                                        We use powerful industrial shredders to physically shred end-of-life hard drives into small particles that cannot be reconstructed. This ensures no data can ever be recovered. We scan serial numbers for tracking and can provide video recording for accountability. Hard drive shredding provides total data security.
                                    </p>
                                </div>
                                <div class="pt-4 border-t border-emerald-500/30">
                                    <a href="{{ url('/contact-us') }}" class="inline-flex items-center justify-center gap-2 w-full py-3 px-4 rounded-xl bg-white hover:bg-emerald-50 text-[#035c43] font-extrabold text-sm transition-all duration-300 shadow-md">
                                        <span>Arrange Service</span>
                                        <span class="text-base">→</span>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Card 2: Degaussing Services -->
                    <div class="group h-[420px] sm:h-[440px] [perspective:1000px] w-full cursor-pointer">
                        <div class="relative w-full h-full rounded-2xl shadow-xl transition-all duration-700 [transform-style:preserve-3d] group-hover:[transform:rotateY(180deg)]">
                            
                            <!-- Front Side -->
                            <div class="absolute inset-0 w-full h-full rounded-2xl overflow-hidden [backface-visibility:hidden] border border-gray-200 shadow-lg bg-gray-900 flex flex-col justify-end">
                                <img 
                                    src="{{ asset('images/services/Asset-Removal-Services-.webp') }}" 
                                    alt="Degaussing Services" 
                                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 opacity-90"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-[#023e2d]/95 via-black/40 to-transparent"></div>
                                <div class="relative z-10 p-6 sm:p-8 space-y-2.5 text-left">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#035c43]/90 text-emerald-200 text-xs font-bold uppercase tracking-wider backdrop-blur-sm border border-emerald-500/30">
                                        <span>Hover to Reveal</span>
                                        <svg class="w-3.5 h-3.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </span>
                                    <h3 class="text-2xl sm:text-3xl font-black text-white leading-tight drop-shadow-md">
                                        Degaussing Services
                                    </h3>
                                </div>
                            </div>

                            <!-- Back Side -->
                            <div class="absolute inset-0 w-full h-full rounded-2xl p-6 sm:p-8 bg-gradient-to-br from-[#023e2d] via-[#035c43] to-[#01281d] text-white [transform:rotateY(180deg)] [backface-visibility:hidden] border border-emerald-600/40 shadow-2xl flex flex-col justify-between text-left">
                                <div>
                                    <div class="flex items-center justify-between border-b border-emerald-500/30 pb-3 mb-4">
                                        <h3 class="text-xl sm:text-2xl font-black text-white tracking-wide">
                                            Degaussing Services
                                        </h3>
                                        <span class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-emerald-300 shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                    </div>
                                    <p class="text-emerald-100 text-sm sm:text-base font-normal leading-relaxed overflow-y-auto max-h-[220px]">
                                        We offer professional degaussing using powerful magnetic fields to completely erase and disrupt data stored on hard drives, tapes, floppy disks and other magnetic media. Our NSA approved HD-1T degausser securely wipes even classified and sensitive data beyond forensic recovery.
                                    </p>
                                </div>
                                <div class="pt-4 border-t border-emerald-500/30">
                                    <a href="{{ url('/contact-us') }}" class="inline-flex items-center justify-center gap-2 w-full py-3 px-4 rounded-xl bg-white hover:bg-emerald-50 text-[#035c43] font-extrabold text-sm transition-all duration-300 shadow-md">
                                        <span>Arrange Service</span>
                                        <span class="text-base">→</span>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Card 3: Disk Wiping & Sanitization -->
                    <div class="group h-[420px] sm:h-[440px] [perspective:1000px] w-full cursor-pointer">
                        <div class="relative w-full h-full rounded-2xl shadow-xl transition-all duration-700 [transform-style:preserve-3d] group-hover:[transform:rotateY(180deg)]">
                            
                            <!-- Front Side -->
                            <div class="absolute inset-0 w-full h-full rounded-2xl overflow-hidden [backface-visibility:hidden] border border-gray-200 shadow-lg bg-gray-900 flex flex-col justify-end">
                                <img 
                                    src="{{ asset('images/services/WhatsApp-Image-2026-03-03-at-2.59.30-AM-300x300.webp') }}" 
                                    alt="Disk Wiping & Sanitization" 
                                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 opacity-90"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-[#023e2d]/95 via-black/40 to-transparent"></div>
                                <div class="relative z-10 p-6 sm:p-8 space-y-2.5 text-left">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#035c43]/90 text-emerald-200 text-xs font-bold uppercase tracking-wider backdrop-blur-sm border border-emerald-500/30">
                                        <span>Hover to Reveal</span>
                                        <svg class="w-3.5 h-3.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </span>
                                    <h3 class="text-2xl sm:text-3xl font-black text-white leading-tight drop-shadow-md">
                                        Disk Wiping &amp; Sanitization
                                    </h3>
                                </div>
                            </div>

                            <!-- Back Side -->
                            <div class="absolute inset-0 w-full h-full rounded-2xl p-6 sm:p-8 bg-gradient-to-br from-[#023e2d] via-[#035c43] to-[#01281d] text-white [transform:rotateY(180deg)] [backface-visibility:hidden] border border-emerald-600/40 shadow-2xl flex flex-col justify-between text-left">
                                <div>
                                    <div class="flex items-center justify-between border-b border-emerald-500/30 pb-3 mb-4">
                                        <h3 class="text-xl sm:text-2xl font-black text-white tracking-wide">
                                            Disk Wiping &amp; Sanitization
                                        </h3>
                                        <span class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-emerald-300 shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                    </div>
                                    <p class="text-emerald-100 text-sm sm:text-base font-normal leading-relaxed overflow-y-auto max-h-[220px]">
                                        We utilize customized disk wiping and sanitization hardware/software that overwrite all sectors of a hard drive multiple times using DoD 5220.22-M algorithms. Disk wiping can perform up to 7 total passes with different data patterns to guarantee 100% data eradication. If drives cannot be wiped, we shred them.
                                    </p>
                                </div>
                                <div class="pt-4 border-t border-emerald-500/30">
                                    <a href="{{ url('/contact-us') }}" class="inline-flex items-center justify-center gap-2 w-full py-3 px-4 rounded-xl bg-white hover:bg-emerald-50 text-[#035c43] font-extrabold text-sm transition-all duration-300 shadow-md">
                                        <span>Arrange Service</span>
                                        <span class="text-base">→</span>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Card 4: Tape Destruction Services -->
                    <div class="group h-[420px] sm:h-[440px] [perspective:1000px] w-full cursor-pointer">
                        <div class="relative w-full h-full rounded-2xl shadow-xl transition-all duration-700 [transform-style:preserve-3d] group-hover:[transform:rotateY(180deg)]">
                            
                            <!-- Front Side -->
                            <div class="absolute inset-0 w-full h-full rounded-2xl overflow-hidden [backface-visibility:hidden] border border-gray-200 shadow-lg bg-gray-900 flex flex-col justify-end">
                                <img 
                                    src="{{ asset('images/about/download.webp') }}" 
                                    alt="Tape Destruction Services" 
                                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 opacity-90"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-[#023e2d]/95 via-black/40 to-transparent"></div>
                                <div class="relative z-10 p-6 sm:p-8 space-y-2.5 text-left">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#035c43]/90 text-emerald-200 text-xs font-bold uppercase tracking-wider backdrop-blur-sm border border-emerald-500/30">
                                        <span>Hover to Reveal</span>
                                        <svg class="w-3.5 h-3.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </span>
                                    <h3 class="text-2xl sm:text-3xl font-black text-white leading-tight drop-shadow-md">
                                        Tape Destruction Services
                                    </h3>
                                </div>
                            </div>

                            <!-- Back Side -->
                            <div class="absolute inset-0 w-full h-full rounded-2xl p-6 sm:p-8 bg-gradient-to-br from-[#023e2d] via-[#035c43] to-[#01281d] text-white [transform:rotateY(180deg)] [backface-visibility:hidden] border border-emerald-600/40 shadow-2xl flex flex-col justify-between text-left">
                                <div>
                                    <div class="flex items-center justify-between border-b border-emerald-500/30 pb-3 mb-4">
                                        <h3 class="text-xl sm:text-2xl font-black text-white tracking-wide">
                                            Tape Destruction Services
                                        </h3>
                                        <span class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-emerald-300 shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                    </div>
                                    <p class="text-emerald-100 text-sm sm:text-base font-normal leading-relaxed overflow-y-auto max-h-[220px]">
                                        For secure tape destruction, we use specialized disintegrators and shredders designed to physically shred computer backup tapes into small particles. Tape destruction eliminates any chance of compromised data while allowing for safe recycling of these legacy formats. We provide reporting on every tape erased.
                                    </p>
                                </div>
                                <div class="pt-4 border-t border-emerald-500/30">
                                    <a href="{{ url('/contact-us') }}" class="inline-flex items-center justify-center gap-2 w-full py-3 px-4 rounded-xl bg-white hover:bg-emerald-50 text-[#035c43] font-extrabold text-sm transition-all duration-300 shadow-md">
                                        <span>Arrange Service</span>
                                        <span class="text-base">→</span>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Card 5: Electronics Destruction -->
                    <div class="group h-[420px] sm:h-[440px] [perspective:1000px] w-full cursor-pointer">
                        <div class="relative w-full h-full rounded-2xl shadow-xl transition-all duration-700 [transform-style:preserve-3d] group-hover:[transform:rotateY(180deg)]">
                            
                            <!-- Front Side -->
                            <div class="absolute inset-0 w-full h-full rounded-2xl overflow-hidden [backface-visibility:hidden] border border-gray-200 shadow-lg bg-gray-900 flex flex-col justify-end">
                                <img 
                                    src="{{ asset('images/ewaste/electronics-destruction.png') }}" 
                                    alt="Electronics Destruction" 
                                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 opacity-90"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-[#023e2d]/95 via-black/40 to-transparent"></div>
                                <div class="relative z-10 p-6 sm:p-8 space-y-2.5 text-left">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#035c43]/90 text-emerald-200 text-xs font-bold uppercase tracking-wider backdrop-blur-sm border border-emerald-500/30">
                                        <span>Hover to Reveal</span>
                                        <svg class="w-3.5 h-3.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </span>
                                    <h3 class="text-2xl sm:text-3xl font-black text-white leading-tight drop-shadow-md">
                                        Electronics Destruction
                                    </h3>
                                </div>
                            </div>

                            <!-- Back Side -->
                            <div class="absolute inset-0 w-full h-full rounded-2xl p-6 sm:p-8 bg-gradient-to-br from-[#023e2d] via-[#035c43] to-[#01281d] text-white [transform:rotateY(180deg)] [backface-visibility:hidden] border border-emerald-600/40 shadow-2xl flex flex-col justify-between text-left">
                                <div>
                                    <div class="flex items-center justify-between border-b border-emerald-500/30 pb-3 mb-4">
                                        <h3 class="text-xl sm:text-2xl font-black text-white tracking-wide">
                                            Electronics Destruction
                                        </h3>
                                        <span class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-emerald-300 shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                    </div>
                                    <p class="text-emerald-100 text-sm sm:text-base font-normal leading-relaxed overflow-y-auto max-h-[220px]">
                                        On-location electronics destruction allows for the demolishing of old computers, servers, networking equipment and other end computing devices right at your facility using powerful shredding machinery. This mitigates transportation risk and guarantees a certificate of destruction for all equipment processed.
                                    </p>
                                </div>
                                <div class="pt-4 border-t border-emerald-500/30">
                                    <a href="{{ url('/contact-us') }}" class="inline-flex items-center justify-center gap-2 w-full py-3 px-4 rounded-xl bg-white hover:bg-emerald-50 text-[#035c43] font-extrabold text-sm transition-all duration-300 shadow-md">
                                        <span>Arrange Service</span>
                                        <span class="text-base">→</span>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Card 6: Onsite Data Destruction -->
                    <div class="group h-[420px] sm:h-[440px] [perspective:1000px] w-full cursor-pointer">
                        <div class="relative w-full h-full rounded-2xl shadow-xl transition-all duration-700 [transform-style:preserve-3d] group-hover:[transform:rotateY(180deg)]">
                            
                            <!-- Front Side -->
                            <div class="absolute inset-0 w-full h-full rounded-2xl overflow-hidden [backface-visibility:hidden] border border-gray-200 shadow-lg bg-gray-900 flex flex-col justify-end">
                                <img 
                                    src="{{ asset('images/ewaste/onsite-data-destruction.png') }}" 
                                    alt="Onsite Data Destruction" 
                                    class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 opacity-90"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-[#023e2d]/95 via-black/40 to-transparent"></div>
                                <div class="relative z-10 p-6 sm:p-8 space-y-2.5 text-left">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#035c43]/90 text-emerald-200 text-xs font-bold uppercase tracking-wider backdrop-blur-sm border border-emerald-500/30">
                                        <span>Hover to Reveal</span>
                                        <svg class="w-3.5 h-3.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </span>
                                    <h3 class="text-2xl sm:text-3xl font-black text-white leading-tight drop-shadow-md">
                                        Onsite Data Destruction
                                    </h3>
                                </div>
                            </div>

                            <!-- Back Side -->
                            <div class="absolute inset-0 w-full h-full rounded-2xl p-6 sm:p-8 bg-gradient-to-br from-[#023e2d] via-[#035c43] to-[#01281d] text-white [transform:rotateY(180deg)] [backface-visibility:hidden] border border-emerald-600/40 shadow-2xl flex flex-col justify-between text-left">
                                <div>
                                    <div class="flex items-center justify-between border-b border-emerald-500/30 pb-3 mb-4">
                                        <h3 class="text-xl sm:text-2xl font-black text-white tracking-wide">
                                            Onsite Data Destruction
                                        </h3>
                                        <span class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-emerald-300 shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                    </div>
                                    <p class="text-emerald-100 text-sm sm:text-base font-normal leading-relaxed overflow-y-auto max-h-[220px]">
                                        For ultimate chain-of-custody security, our mobile shredding trucks can visit your location in Denver with onboard shredding capabilities to destroy hard drives, tapes or electronics onsite before they ever leave your building. This end-to-end service ensures complete visibility and control.
                                    </p>
                                </div>
                                <div class="pt-4 border-t border-emerald-500/30">
                                    <a href="{{ url('/contact-us') }}" class="inline-flex items-center justify-center gap-2 w-full py-3 px-4 rounded-xl bg-white hover:bg-emerald-50 text-[#035c43] font-extrabold text-sm transition-all duration-300 shadow-md">
                                        <span>Arrange Service</span>
                                        <span class="text-base">→</span>
                                    </a>
                                </div>
                            </div>

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
