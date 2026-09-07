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
    <body class="bg-white text-gray-900 font-['Plus_Jakarta_Sans',sans-serif] antialiased selection:bg-emerald-500 selection:text-white w-full overflow-x-hidden m-0 p-0">

        <!-- Fixed Bottom-Left Badge -->
        <x-events-badge />

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

        <!-- Section 4: Our Satisfied Clients Section (Matching Home Page) -->
        <section 
            id="testimonials" 
            class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100 overflow-hidden relative"
            x-data="{
                activeSlide: 0,
                maxSlide: 4,
                reviews: [
                    {
                        name: 'David Jones',
                        rating: 4,
                        text: `I have to say, I'm really impressed by professionalism and efficiency of It-Investment Recoveries team. If you're looking for hassle-free recycling, I highly recommend them`,
                        image: '{{ asset('images/home/reviews/1.webp') }}'
                    },
                    {
                        name: 'David Martinelli',
                        rating: 3,
                        text: `I recently hired It-Investment Recoveries for computer recycling in Denver, and I couldn't be happier. Their team was punctual, professional, and took care of everything. Highly recommended!`,
                        image: '{{ asset('images/home/reviews/2.webp') }}'
                    },
                    {
                        name: 'Mark Richardson',
                        rating: 5,
                        text: `Their commitment to data security and environmentally responsible practices is commendable. A reliable choice for anyone looking to recycle electronics.`,
                        image: '{{ asset('images/home/reviews/3.webp') }}'
                    },
                    {
                        name: 'Michael Brown',
                        rating: 4,
                        text: `Every business and resident in Denver should know about their electronics recycling services. They make the whole process fast, easy and secure.`,
                        image: '{{ asset('images/home/reviews/4.webp') }}'
                    },
                    {
                        name: 'Shams W.Pawel',
                        rating: 3,
                        text: `With affordable and efficient Computer recycling in Denver they exceeded my expectations. They offer reasonable rates, making recycling accessible to all on a budget.`,
                        image: '{{ asset('images/home/reviews/5.webp') }}'
                    },
                    {
                        name: 'David Jones',
                        rating: 4,
                        text: `I have to say, I'm really impressed by professionalism and efficiency of It-Investment Recoveries team. If you're looking for hassle-free recycling, I highly recommend them`,
                        image: '{{ asset('images/home/reviews/6.webp') }}'
                    },
                    {
                        name: 'David Martinelli',
                        rating: 3,
                        text: `I recently hired It-Investment Recoveries for computer recycling in Denver, and I couldn't be happier. Their team was punctual, professional, and took care of everything. Highly recommended!`,
                        image: '{{ asset('images/home/reviews/7.webp') }}'
                    }
                ]
            }"
        >
            <div class="w-full max-w-7xl mx-auto flex flex-col items-center text-center relative z-10 space-y-12">
                
                <!-- Section Title: Our Happy Clients 🕴️ -->
                <div class="flex items-center justify-center gap-3">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-normal font-['Plus_Jakarta_Sans',sans-serif]">
                        <span class="text-[#222222]">Our</span> 
                        <span class="text-[#34d399] ml-2">Happy Clients</span>
                    </h2>
                    <span class="text-4xl sm:text-5xl">🕴️</span>
                </div>

                <!-- Testimonials Slider Container -->
                <div class="relative w-full overflow-hidden py-2">
                    <div 
                        class="flex transition-transform duration-500 ease-out"
                        :style="`transform: translateX(-${activeSlide * (100 / (window.innerWidth >= 1024 ? 3 : window.innerWidth >= 768 ? 2 : 1))}%)`"
                    >
                        <template x-for="(review, index) in reviews" :key="index">
                            <div class="w-full md:w-1/2 lg:w-1/3 shrink-0 px-3 sm:px-4 flex">
                                <div class="bg-white rounded-2xl border-2 border-[#005a36] p-7 sm:p-9 shadow-md hover:shadow-xl transition-all duration-300 flex flex-col justify-between text-center w-full relative group overflow-hidden">
                                    
                                    <!-- Stars Rating -->
                                    <div class="space-y-4 pt-2">
                                        <div class="flex items-center justify-center gap-1 text-amber-400 text-lg sm:text-xl">
                                            <template x-for="star in 5" :key="star">
                                                <span x-text="star <= review.rating ? '★' : '☆'" :class="star <= review.rating ? 'text-amber-400' : 'text-amber-300/60'"></span>
                                            </template>
                                        </div>

                                        <!-- Review Body Text -->
                                        <p class="text-gray-600 text-base sm:text-lg leading-relaxed font-medium py-2" x-text="review.text"></p>
                                    </div>

                                    <!-- Card Footer Profile & Quotation Icon -->
                                    <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-100/80 relative">
                                        <div class="flex items-center gap-4 shrink-0 text-left">
                                            <img 
                                                :src="review.image" 
                                                :alt="review.name" 
                                                class="w-14 h-14 sm:w-16 sm:h-16 rounded-full object-cover shadow-sm border-2 border-white ring-2 ring-gray-100 shrink-0"
                                            />
                                            <div class="font-extrabold text-gray-900 text-lg sm:text-xl" x-text="review.name"></div>
                                        </div>
                                        
                                        <!-- Quotation Icon -->
                                        <div class="text-[#a5f3fc] font-serif text-5xl sm:text-6xl leading-none select-none opacity-80 pl-2">”</div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Pagination Dots Controls -->
                <div class="flex items-center justify-center gap-2.5 mt-8">
                    <template x-for="dot in 5" :key="dot">
                        <button 
                            @click="activeSlide = dot - 1" 
                            class="h-3 rounded-full transition-all duration-300 focus:outline-none"
                            :class="activeSlide === (dot - 1) ? 'bg-[#2563eb] w-3 shadow-md' : 'bg-gray-300 w-3 hover:bg-gray-400'"
                            :aria-label="`Go to review slide ${dot}`"
                        ></button>
                    </template>
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
