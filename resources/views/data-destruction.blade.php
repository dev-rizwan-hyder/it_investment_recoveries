<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="w-full overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Data Destruction in Denver | Certified Hard Drive Shredding &amp; Sanitization</title>
        <meta name="description" content="Secure data destruction in Denver. Hard drive shredding, degaussing, and NIST 800-88 / DoD compliant wiping with full serialized certificates of destruction.">

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
    <body class="bg-white text-gray-900 font-['Plus_Jakarta_Sans',sans-serif] antialiased selection:bg-emerald-500 selection:text-white w-full overflow-x-hidden m-0 p-0">

        <!-- Fixed Bottom-Left Badge -->
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

        <!-- Section 1: Hero Banner -->
        <section class="relative min-h-[75vh] sm:min-h-[85vh] lg:min-h-screen w-full flex flex-col justify-between overflow-hidden bg-black">
            <!-- Header Overlay -->
            <x-header active="services" />

            <!-- Background Image -->
            <div class="absolute inset-0 z-0 overflow-hidden w-full h-full">
                <img 
                    src="{{ asset('images/services/WhatsApp-Image-2026-03-03-at-2.48.49-AM.webp') }}" 
                    alt="Data Destruction in Denver" 
                    class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000 ease-out"
                />
                <div class="absolute inset-0 z-10 bg-gradient-to-b from-black/75 via-black/50 to-black/80"></div>
            </div>

            <!-- Hero Center Content -->
            <div class="relative z-20 flex-grow flex flex-col items-center justify-center text-center px-4 sm:px-8 lg:px-12 pt-36 sm:pt-48 md:pt-56 pb-24 sm:pb-32 w-full">
                <div class="w-full max-w-5xl mx-auto space-y-5 sm:space-y-7">
                    
                    <!-- Title -->
                    <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-[80px] font-bold text-white tracking-tight leading-[1.1] font-['Amaranth',sans-serif] drop-shadow-[0_4px_20px_rgba(0,0,0,0.85)]">
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
                            class="inline-flex items-center justify-center gap-2.5 px-9 py-4 sm:px-11 sm:py-4.5 rounded-full bg-[#10b981] hover:bg-[#059669] text-white font-extrabold text-lg sm:text-xl tracking-wide shadow-2xl transition duration-300 transform hover:scale-105 group"
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
                        <div class="flex items-center justify-center h-20 sm:h-28 w-48 sm:w-64 shrink-0">
                            <img src="{{ asset('images/home-slider/lruIoO-removebg-preview.webp') }}" alt="NIST Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="flex items-center justify-center h-20 sm:h-28 w-48 sm:w-64 shrink-0">
                            <img src="{{ asset('images/home-slider/khyuXt-removebg-preview-1.webp') }}" alt="Department of Defense Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="flex items-center justify-center h-20 sm:h-28 w-48 sm:w-64 shrink-0">
                            <img src="{{ asset('images/home-slider/AEfjkp-removebg-preview-1.webp') }}" alt="EPA Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>

                        <!-- Seamless Loop Duplicate -->
                        <div class="flex items-center justify-center h-20 sm:h-28 w-48 sm:w-64 shrink-0">
                            <img src="{{ asset('images/home-slider/lruIoO-removebg-preview.webp') }}" alt="NIST Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="flex items-center justify-center h-20 sm:h-28 w-48 sm:w-64 shrink-0">
                            <img src="{{ asset('images/home-slider/khyuXt-removebg-preview-1.webp') }}" alt="Department of Defense Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="flex items-center justify-center h-20 sm:h-28 w-48 sm:w-64 shrink-0">
                            <img src="{{ asset('images/home-slider/AEfjkp-removebg-preview-1.webp') }}" alt="EPA Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                    </div>
                </div>

                <!-- Title -->
                <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
                    <span class="text-[#34d399]">Certified Data Destruction</span> 
                    <span class="text-gray-900 block sm:inline ml-2">Professionals in Denver</span>
                </h2>

                <!-- Main Description -->
                <p class="text-gray-700 text-lg sm:text-xl lg:text-2xl font-medium leading-relaxed max-w-5xl mx-auto">
                    As a top data destruction company in Denver, we uses industry-approved techniques like disk wiping, onsite hardware shredding, and degaussing to securely eliminate sensitive data from computers, hard drives, tapes and other devices. We adhere to NAID and NIST standards, providing certified reporting for compliance. Our information security experts make data destruction smooth, convenient and affordable for local businesses.
                </p>

                <!-- Sub-Heading & Paragraph -->
                <div class="pt-6 space-y-5">
                    <h3 class="text-3xl sm:text-4xl font-black text-[#34d399]">
                        Contact for Hassle Free <span class="text-gray-900">Data Destruction</span>
                    </h3>
                    <p class="text-gray-800 text-lg sm:text-xl md:text-2xl max-w-5xl mx-auto leading-relaxed font-medium">
                        As a leader in data destruction in Denver, we make the process smooth and simple for our local clients. Contact us today to learn more about our affordable and reliable Denver data destruction services.
                    </p>
                    <div class="pt-3">
                        <a 
                            href="{{ url('/contact-us') }}" 
                            class="inline-flex items-center gap-2.5 px-9 py-4 rounded-full bg-[#005a36] hover:bg-[#10b981] text-white font-extrabold text-lg shadow-xl transition duration-300 transform hover:scale-105"
                        >
                            <span>Book Consultation</span>
                            <span class="text-xl">→</span>
                        </a>
                    </div>
                </div>

            </div>
        </section>

        <!-- Section 3: What We Do? (6 Circular Cards Grid) -->
        <section class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100">
            <div class="w-full max-w-7xl mx-auto space-y-16">
                
                <!-- Section Heading -->
                <div class="text-center">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-normal">
                        <span class="text-[#34d399]">What</span> 
                        <span class="text-gray-900 ml-2">We Do?</span>
                    </h2>
                </div>

                <!-- 6 Circular Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-12 sm:gap-16">
                    
                    <!-- Circular Card 1: Hard Drive Shredding -->
                    <div class="flex flex-col items-center text-center space-y-4 group">
                        <div class="w-56 h-56 sm:w-64 sm:h-64 rounded-full border-4 border-white shadow-xl overflow-hidden transform group-hover:scale-105 transition duration-500 shrink-0 bg-gray-100">
                            <img 
                                src="{{ asset('images/services/WhatsApp-Image-2026-03-03-at-2.48.49-AM.webp') }}" 
                                alt="Hard Drive Shredding" 
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-black text-[#005a36] pt-2">
                            Hard Drive Shredding
                        </h3>
                        <p class="text-gray-800 text-base sm:text-lg font-normal leading-relaxed max-w-md">
                            We use powerful industrial shredders to physically shred end-of-life hard drives into small particles that cannot be reconstructed. This ensures no data can ever be recovered. We scan serial numbers for tracking and can provide video recording for accountability. Hard drive shredding provides total data security.
                        </p>
                    </div>

                    <!-- Circular Card 2: Degaussing Services -->
                    <div class="flex flex-col items-center text-center space-y-4 group">
                        <div class="w-56 h-56 sm:w-64 sm:h-64 rounded-full border-4 border-white shadow-xl overflow-hidden transform group-hover:scale-105 transition duration-500 shrink-0 bg-gray-100">
                            <img 
                                src="{{ asset('images/services/Asset-Removal-Services-.webp') }}" 
                                alt="Degaussing Services" 
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-black text-[#005a36] pt-2">
                            Degaussing Services
                        </h3>
                        <p class="text-gray-800 text-base sm:text-lg font-normal leading-relaxed max-w-md">
                            We offer professional degaussing using powerful magnetic fields to completely erase and disrupt data stored on hard drives, tapes, floppy disks and other magnetic media. Our NSA approved HD-1T degausser securely wipes even classified and sensitive data beyond forensic recovery.
                        </p>
                    </div>

                    <!-- Circular Card 3: Disk Wiping & Sanitization -->
                    <div class="flex flex-col items-center text-center space-y-4 group">
                        <div class="w-56 h-56 sm:w-64 sm:h-64 rounded-full border-4 border-white shadow-xl overflow-hidden transform group-hover:scale-105 transition duration-500 shrink-0 bg-gray-100">
                            <img 
                                src="{{ asset('images/services/WhatsApp-Image-2026-03-03-at-2.59.30-AM-300x300.webp') }}" 
                                alt="Disk Wiping &amp; Sanitization" 
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-black text-[#005a36] pt-2">
                            Disk Wiping &amp; Sanitization
                        </h3>
                        <p class="text-gray-800 text-base sm:text-lg font-normal leading-relaxed max-w-md">
                            We utilize customized disk wiping and sanitization hardware/software that overwrite all sectors of a hard drive multiple times using DoD 5220.22-M algorithms. Disk wiping can perform up to 7 total passes with different data patterns to guarantee 100% data eradication. If drives cannot be wiped, we shred them.
                        </p>
                    </div>

                    <!-- Circular Card 4: Tape Destruction Services -->
                    <div class="flex flex-col items-center text-center space-y-4 group">
                        <div class="w-56 h-56 sm:w-64 sm:h-64 rounded-full border-4 border-white shadow-xl overflow-hidden transform group-hover:scale-105 transition duration-500 shrink-0 bg-gray-100">
                            <img 
                                src="{{ asset('images/about/download.webp') }}" 
                                alt="Tape Destruction Services" 
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-black text-[#005a36] pt-2">
                            Tape Destruction Services
                        </h3>
                        <p class="text-gray-800 text-base sm:text-lg font-normal leading-relaxed max-w-md">
                            For secure tape destruction, we use specialized disintegrators and shredders designed to physically shred computer backup tapes into small particles. Tape destruction eliminates any chance of compromised data while allowing for safe recycling of these legacy formats. We provide reporting on every tape erased.
                        </p>
                    </div>

                    <!-- Circular Card 5: Electronics Destruction -->
                    <div class="flex flex-col items-center text-center space-y-4 group">
                        <div class="w-56 h-56 sm:w-64 sm:h-64 rounded-full border-4 border-white shadow-xl overflow-hidden transform group-hover:scale-105 transition duration-500 shrink-0 bg-gray-100">
                            <img 
                                src="{{ asset('images/ewaste/ewaste.webp') }}" 
                                alt="Electronics Destruction" 
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-black text-[#005a36] pt-2">
                            Electronics Destruction
                        </h3>
                        <p class="text-gray-800 text-base sm:text-lg font-normal leading-relaxed max-w-md">
                            On-location electronics destruction allows for the demolishing of old computers, servers, networking equipment and other end computing devices right at your facility using powerful shredding machinery. This mitigates transportation risk and guarantees a certificate of destruction for all equipment processed.
                        </p>
                    </div>

                    <!-- Circular Card 6: Onsite Data Destruction -->
                    <div class="flex flex-col items-center text-center space-y-4 group">
                        <div class="w-56 h-56 sm:w-64 sm:h-64 rounded-full border-4 border-white shadow-xl overflow-hidden transform group-hover:scale-105 transition duration-500 shrink-0 bg-gray-100">
                            <img 
                                src="{{ asset('images/ewaste/fLATIRONS.webp') }}" 
                                alt="Onsite Data Destruction" 
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-black text-[#005a36] pt-2">
                            Onsite Data Destruction
                        </h3>
                        <p class="text-gray-800 text-base sm:text-lg font-normal leading-relaxed max-w-md">
                            For ultimate chain-of-custody security, our mobile shredding trucks can visit your location in Denver with onboard shredding capabilities to destroy hard drives, tapes or electronics onsite before they ever leave your building. This end-to-end service ensures complete visibility and control.
                        </p>
                    </div>

                </div>

            </div>
        </section>

        <!-- Section 4: Our Satisfied Clients (Dark Green Section with Background Image) -->
        <section 
            id="testimonials" 
            class="relative bg-[#004d2e] text-white py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full shadow-2xl overflow-hidden"
            x-data="{ activeSlide: 0 }"
        >
            <!-- Background Image Overlay -->
            <div class="absolute inset-0 z-0 pointer-events-none">
                <img 
                    src="{{ asset('images/home-banner.webp') }}" 
                    alt="Denver Background" 
                    class="w-full h-full object-cover opacity-25 mix-blend-overlay filter contrast-125 brightness-110 scale-105"
                />
                <div class="absolute inset-0 bg-gradient-to-r from-[#003d24]/90 via-[#0e8a55]/85 to-[#003d24]/90"></div>
            </div>

            <div class="relative z-10 w-full max-w-7xl mx-auto space-y-12">
                
                <!-- Section Title -->
                <div class="text-center">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-normal text-white">
                        <span>Our</span> 
                        <span class="text-[#34d399] ml-2">Satisfied Clients</span>
                    </h2>
                </div>

                <!-- 3 Testimonial Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 sm:gap-10">
                    
                    <!-- Card 1 -->
                    <div class="bg-white rounded-[22px] border border-emerald-500/40 p-6 sm:p-8 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col justify-between relative group overflow-hidden">
                        <div class="space-y-4 relative z-10">
                            <!-- Star Rating -->
                            <div class="flex items-center space-x-1 text-amber-400 text-lg justify-center">
                                ★★★★★
                            </div>
                            <!-- Quote Text -->
                            <p class="text-gray-700 text-sm sm:text-base leading-relaxed text-center font-normal">
                                "IT Investment Recoveries helped us process and inventory over 500 retired workstations seamlessly. The data destruction certificates gave our compliance team complete peace of mind."
                            </p>
                        </div>

                        <!-- User Profile Info -->
                        <div class="flex items-center justify-center space-x-4 pt-6 border-t border-gray-100 mt-6 relative z-10">
                            <img 
                                src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80" 
                                alt="David Jones" 
                                class="w-12 h-12 rounded-full object-cover shadow-sm border border-gray-200"
                            />
                            <span class="font-bold text-gray-900 text-base">David Jones</span>
                        </div>

                        <!-- Quote Background Accent -->
                        <div class="absolute bottom-4 right-4 text-emerald-100/60 font-serif text-7xl font-black select-none pointer-events-none">
                            ”
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white rounded-[22px] border border-emerald-500/40 p-6 sm:p-8 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col justify-between relative group overflow-hidden">
                        <div class="space-y-4 relative z-10">
                            <!-- Star Rating -->
                            <div class="flex items-center space-x-1 text-amber-400 text-lg justify-center">
                                ★★★★★
                            </div>
                            <!-- Quote Text -->
                            <p class="text-gray-700 text-sm sm:text-base leading-relaxed text-center font-normal">
                                "I recently hired IT-Investment Recoveries for computer recycling in Denver, and I couldn't be happier. Their team was punctual, professional, and took care of everything. Highly recommended!"
                            </p>
                        </div>

                        <!-- User Profile Info -->
                        <div class="flex items-center justify-center space-x-4 pt-6 border-t border-gray-100 mt-6 relative z-10">
                            <img 
                                src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&auto=format&fit=crop&q=80" 
                                alt="David Martinelli" 
                                class="w-12 h-12 rounded-full object-cover shadow-sm border border-gray-200"
                            />
                            <span class="font-bold text-gray-900 text-base">David Martinelli</span>
                        </div>

                        <!-- Quote Background Accent -->
                        <div class="absolute bottom-4 right-4 text-emerald-100/60 font-serif text-7xl font-black select-none pointer-events-none">
                            ”
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white rounded-[22px] border border-emerald-500/40 p-6 sm:p-8 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col justify-between relative group overflow-hidden">
                        <div class="space-y-4 relative z-10">
                            <!-- Star Rating -->
                            <div class="flex items-center space-x-1 text-amber-400 text-lg justify-center">
                                ★★★★★
                            </div>
                            <!-- Quote Text -->
                            <p class="text-gray-700 text-sm sm:text-base leading-relaxed text-center font-normal">
                                "Their commitment to data security and environmentally responsible practices is commendable. A reliable choice for anyone looking to recycle electronics."
                            </p>
                        </div>

                        <!-- User Profile Info -->
                        <div class="flex items-center justify-center space-x-4 pt-6 border-t border-gray-100 mt-6 relative z-10">
                            <img 
                                src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&auto=format&fit=crop&q=80" 
                                alt="Mark Richardson" 
                                class="w-12 h-12 rounded-full object-cover shadow-sm border border-gray-200"
                            />
                            <span class="font-bold text-gray-900 text-base">Mark Richardson</span>
                        </div>

                        <!-- Quote Background Accent -->
                        <div class="absolute bottom-4 right-4 text-emerald-100/60 font-serif text-7xl font-black select-none pointer-events-none">
                            ”
                        </div>
                    </div>

                </div>

                <!-- Carousel Dots Indicator + Small Recycling Icon -->
                <div class="flex flex-col items-center space-y-4 pt-4">
                    <div class="flex items-center space-x-2">
                        <button class="w-3.5 h-3.5 rounded-full bg-[#3b82f6] shadow-sm"></button>
                        <button class="w-2.5 h-2.5 rounded-full bg-gray-300 hover:bg-gray-400 transition"></button>
                        <button class="w-2.5 h-2.5 rounded-full bg-gray-300 hover:bg-gray-400 transition"></button>
                        <button class="w-2.5 h-2.5 rounded-full bg-gray-300 hover:bg-gray-400 transition"></button>
                        <button class="w-2.5 h-2.5 rounded-full bg-gray-300 hover:bg-gray-400 transition"></button>
                    </div>
                    <div class="text-emerald-500 pt-2">
                        <svg class="w-5 h-5 fill-current mx-auto" viewBox="0 0 24 24">
                            <path d="M12 2A10 10 0 1 0 22 12 10.011 10.011 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8.009 8.009 0 0 1-8 8zm-2-12.5l2.5 3 2.5-3h-2V6h-1v1.5zm-3.5 6l3.5 2.5V15h1.5v-1.5H10l-3.5-3.5zm9 0l-3.5-3.5v1.5H12.5V15H14l3.5-2.5z"/>
                        </svg>
                    </div>
                </div>

            </div>
        </section>

        <!-- Section 5: Talk with an Expert Ratings Banner -->
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
