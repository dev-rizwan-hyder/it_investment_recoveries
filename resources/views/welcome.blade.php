<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="w-full overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>IT Investment Recoveries</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <!-- Google Fonts: Amaranth, Plus Jakarta Sans & Outfit -->
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
            @keyframes marqueeRight {
                0% {
                    transform: translateX(-50%);
                }
                100% {
                    transform: translateX(0%);
                }
            }
            .animate-partner-marquee {
                display: flex;
                width: max-content;
                animation: marqueeLeft 18s linear infinite;
            }
            .animate-marquee-left {
                display: flex;
                width: max-content;
                animation: marqueeLeft 22s linear infinite;
            }
            .animate-marquee-right {
                display: flex;
                width: max-content;
                animation: marqueeRight 22s linear infinite;
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

        <!-- Section 1: Main Hero Section (Full Height with Slideshow & Transparent Header Overlay) -->
        <section 
            class="relative min-h-screen w-full flex flex-col justify-between overflow-hidden bg-black"
            x-data="{
                slides: [
                    '{{ asset('images/home-banner.webp') }}',
                    '{{ asset('images/old-electronic-devices-world-globe-e-waste-recycling-concept_878453-6585.webp') }}',
                    '{{ asset('images/green-earth.webp') }}'
                ],
                activeSlide: 0,
                init() {
                    setInterval(() => {
                        this.activeSlide = (this.activeSlide + 1) % this.slides.length;
                    }, 5000);
                }
            }"
        >
            <!-- Top Transparent Header Overlay -->
            <x-header />

            <!-- Background Slideshow with Continuous Ken Burns Zoom-In -->
            <div class="absolute inset-0 z-0 overflow-hidden w-full h-full">
                <template x-for="(slide, index) in slides" :key="index">
                    <div 
                        class="absolute inset-0 w-full h-full transition-opacity duration-1000 ease-in-out"
                        :class="{
                            'opacity-100 z-10': activeSlide === index,
                            'opacity-0 z-0': activeSlide !== index
                        }"
                    >
                        <img 
                            :src="slide" 
                            alt="IT Recoveries Banner" 
                            class="w-full h-full object-cover transform transition-transform duration-[6500ms] ease-out"
                            :class="activeSlide === index ? 'scale-110' : 'scale-100'"
                        />
                    </div>
                </template>

                <!-- Gradient Overlay -->
                <div class="absolute inset-0 z-20 bg-gradient-to-b from-black/50 via-black/20 to-black/60"></div>
            </div>

            <!-- Main Hero Center Text Content -->
            <div class="relative z-30 flex-grow flex flex-col items-center justify-center text-center px-4 sm:px-8 lg:px-12 pt-28 sm:pt-40 md:pt-48 pb-12 w-full">
                <div class="w-full max-w-6xl mx-auto mt-6 sm:mt-16 lg:mt-20 space-y-5 sm:space-y-8">
                    
                    <!-- Exact Amaranth Font -->
                    <h1 class="text-3xl sm:text-5xl md:text-6xl lg:text-[80px] font-bold text-white tracking-normal leading-[1.15] sm:leading-[1.12] font-['Amaranth',sans-serif] drop-shadow-[0_2px_12px_rgba(0,0,0,0.6)]">
                        Denver's Top Computer &amp;<br /> Electronics Recycling Company
                    </h1>

                    <!-- Mobile-Optimized Leaf CTA Button -->
                    <div class="pt-2">
                        <a 
                            href="{{ url('/contact-us') }}" 
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 sm:px-9 sm:py-4 rounded-tl-[22px] rounded-bl-[22px] rounded-tr-[22px] rounded-br-none sm:rounded-tl-[32px] sm:rounded-bl-[32px] sm:rounded-tr-[32px] bg-gradient-to-r from-[#0e8a55] via-[#10b981] to-[#10b981] hover:bg-none hover:bg-white text-white hover:text-[#005a36] border-2 border-[#10b981] font-bold text-sm sm:text-lg md:text-xl tracking-wide shadow-2xl transition-all duration-300 group whitespace-nowrap max-w-full"
                        >
                            <span class="font-extrabold tracking-normal sm:tracking-wide">Schedule A Consultation</span>
                            <span class="text-base sm:text-xl transition-transform duration-200 group-hover:translate-x-1.5">→</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 2: IT INVESTMENT RECOVERIES Description Section -->
        <section 
            id="about-section" 
            class="bg-white text-gray-900 pt-16 sm:pt-24 pb-12 px-4 sm:px-8 lg:px-16 w-full overflow-hidden"
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
            <div class="w-full max-w-7xl mx-auto flex flex-col items-center text-center">
                
                <!-- Main Header: IT INVESTMENT RECOVERIES -->
                <div 
                    class="transform transition-all duration-700 ease-out mb-6"
                    :class="visible ? 'translate-x-0 opacity-100' : '-translate-x-16 opacity-0'"
                >
                    <h2 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-wide uppercase">
                        <span class="text-[#34d399] font-black">IT INVESTMENT</span> 
                        <span class="text-[#222222] font-black ml-2 sm:ml-3">RECOVERIES</span>
                    </h2>
                </div>

                <!-- Subheading: Computer & Electronics Recycling in Denver -->
                <div 
                    class="transform transition-all duration-700 delay-150 ease-out mb-8"
                    :class="visible ? 'translate-x-0 opacity-100' : 'translate-x-16 opacity-0'"
                >
                    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-[#005a36] tracking-tight">
                        Computer &amp; Electronics Recycling in Denver
                    </h3>
                </div>

                <!-- Paragraph Body Content -->
                <div 
                    class="transform transition-all duration-700 delay-300 ease-out w-full max-w-6xl mb-10 px-2 sm:px-4"
                    :class="visible ? 'translate-x-0 opacity-100' : '-translate-x-16 opacity-0'"
                >
                    <p class="text-gray-700 text-lg sm:text-xl lg:text-2xl leading-relaxed sm:leading-[1.75] text-center font-normal">
                        As the leading computer and electronics recycler in Denver, Colorado, We provide secure, responsible recycling services for businesses and residents. By properly recycling your outdated computers, laptops, servers, printers, phones, TVs and other electronics in Denver, we keep hazardous electronic waste out of landfills. Bring your end-of-life electronics to our Denver e-waste recycling facility for drop-off recycling. We will safely wipe all data, dismantle old electronic equipment, and recycle the components in an environmentally responsible manner. Contact us today to schedule an electronics pickup or learn more about our secure computer and electronics recycling services conveniently located in Denver.
                    </p>
                </div>

                <!-- CTA Button: Data Security -> -->
                <div 
                    class="transform transition-all duration-700 delay-450 ease-out mb-8"
                    :class="visible ? 'translate-y-0 opacity-100 scale-100' : 'translate-y-12 opacity-0 scale-95'"
                >
                    <a 
                        href="#services" 
                        class="inline-flex items-center justify-center gap-2.5 px-8 py-3.5 sm:px-10 sm:py-4 rounded-tl-[24px] rounded-bl-[24px] rounded-tr-[24px] rounded-br-none bg-[#10b981] hover:bg-white text-white hover:text-[#005a36] border-2 border-[#10b981] font-bold text-base sm:text-lg tracking-wide shadow-xl transition-all duration-300 group whitespace-nowrap"
                    >
                        <span class="font-extrabold tracking-wide">Data Security</span>
                        <span class="text-xl transition-transform duration-200 group-hover:translate-x-1.5">→</span>
                    </a>
                </div>

            </div>
        </section>

        <!-- Divider Style 1: Right-Tilted Skewed Parallelogram Accent Bar -->
        <div class="bg-white py-6 w-full flex items-center justify-center relative overflow-hidden">
            <div class="w-full max-w-7xl px-6 flex items-center justify-center relative">
                <div class="w-full h-[1px] bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
                <div class="absolute flex items-center gap-2">
                    <div class="w-48 sm:w-72 h-[4px] bg-gradient-to-r from-[#10b981] via-[#059669] to-[#004d2e] transform -skew-x-[45deg] shadow-sm rounded-full"></div>
                    <div class="w-2.5 h-2.5 rounded-full bg-[#10b981] shadow"></div>
                </div>
            </div>
        </div>

        <!-- Section 3: Certified Service Providers Slider Section -->
        <section class="bg-white text-gray-800 py-12 sm:py-20 px-4 sm:px-8 w-full overflow-hidden">
            <div class="w-full max-w-7xl mx-auto text-center">
                <!-- Title -->
                <div class="max-w-5xl mx-auto mb-14">
                    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-semibold text-gray-700 tracking-wide leading-snug">
                        We Support &amp; Do Business With Certified Service Providers
                    </h3>
                </div>

                <!-- Sliding Logos Marquee Slider -->
                <div class="relative w-full overflow-hidden py-6">
                    <div class="animate-partner-marquee flex items-center space-x-16 sm:space-x-24 md:space-x-32 pr-16 sm:pr-24">
                        <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                            <img src="{{ asset('images/home-slider/AEfjkp-removebg-preview-1.webp') }}" alt="EPA Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                            <img src="{{ asset('images/home-slider/lruIoO-removebg-preview.webp') }}" alt="NIST Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                            <img src="{{ asset('images/home-slider/khyuXt-removebg-preview-1.webp') }}" alt="Department of Defense Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>

                        <!-- Seamless Loop Duplicate -->
                        <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                            <img src="{{ asset('images/home-slider/AEfjkp-removebg-preview-1.webp') }}" alt="EPA Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                            <img src="{{ asset('images/home-slider/lruIoO-removebg-preview.webp') }}" alt="NIST Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                        <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                            <img src="{{ asset('images/home-slider/khyuXt-removebg-preview-1.webp') }}" alt="Department of Defense Certification" class="max-h-full max-w-full object-contain drop-shadow-md transition-transform duration-300 hover:scale-105" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Divider Style 2: Dashed Angled Slash Divider with Rotated Leaf Badge -->
        <div class="bg-white py-6 w-full flex items-center justify-center relative overflow-hidden">
            <div class="w-full max-w-7xl px-6 flex items-center justify-center relative">
                <div class="w-full border-t-2 border-dashed border-emerald-300/60"></div>
                <div class="absolute bg-white px-5 flex items-center gap-3">
                    <div class="w-12 h-1 bg-[#10b981] transform -rotate-12 rounded-full shadow"></div>
                    <div class="w-3 h-3 border-2 border-[#005a36] bg-emerald-400 rotate-45"></div>
                    <div class="w-12 h-1 bg-[#005a36] transform -rotate-12 rounded-full shadow"></div>
                </div>
            </div>
        </div>

        <!-- Section 4: Data Security Compliance & Continuous Auto-Looping Video Section -->
        <section class="bg-white text-gray-900 py-12 sm:py-20 px-4 sm:px-8 lg:px-16 w-full overflow-hidden">
            <div class="w-full max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">
                
                <!-- Left Side: Heading Text -->
                <div class="lg:col-span-6 space-y-6 text-left">
                    <h2 class="text-3xl sm:text-4xl md:text-[42px] font-bold text-[#005a36] leading-relaxed sm:leading-[1.55] tracking-normal">
                        From data destruction to certified recycling, We provide data security to the highest standards, in compliance with NIST, DoD, and NSA guidelines.
                    </h2>
                </div>

                <!-- Right Side: Auto-Looping Video Frame -->
                <div class="lg:col-span-6 w-full">
                    <div class="relative w-full rounded-[24px] sm:rounded-[30px] border-2 sm:border-[3px] border-[#005a36] overflow-hidden shadow-2xl bg-black aspect-video group">
                        
                        <!-- Top-Left Brand Logo Overlay -->
                        <div class="absolute top-4 left-4 z-20 pointer-events-none bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-xl shadow-md flex items-center gap-2">
                            <img 
                                src="{{ asset('images/logo.png') }}" 
                                alt="IT Investment Recoveries" 
                                class="h-6 sm:h-8 w-auto object-contain"
                            />
                        </div>

                        <!-- Auto-Looping Background HTML5 Video -->
                        <video 
                            class="w-full h-full object-cover absolute inset-0 z-10" 
                            autoplay 
                            loop 
                            muted 
                            playsinline 
                            controls
                            poster="{{ asset('images/degausser-video-poster.png') }}"
                        >
                            <source src="{{ asset('images/videos/Black-and-Blue-Simple-Countdown-Video-Collage-3.mp4') }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>

                    </div>
                </div>

            </div>
        </section>

        <!-- Section 5: Our Proud Community Partners Section -->
        <div class="relative w-full bg-white overflow-hidden py-10 sm:py-16">
            <!-- Slanted Deep Green Outer Section Container -->
            <section class="bg-[#004d2e] py-16 sm:py-24 transform -skew-y-2 origin-top-left shadow-2xl w-full">
                <!-- Inner Content Container -->
                <div class="transform skew-y-2 w-full max-w-7xl mx-auto px-4 sm:px-8 text-center space-y-12">
                    
                    <!-- Section Title: Our Proud Community Partners 🤝 -->
                    <div class="flex items-center justify-center gap-4 flex-wrap">
                        <h2 class="text-4xl sm:text-6xl md:text-7xl lg:text-[72px] font-black tracking-normal leading-none">
                            <span class="text-[#34d399]">Our Proud</span> 
                            <span class="text-white ml-2 sm:ml-4">Community Partners</span>
                        </h2>
                        <!-- Larger Handshake Icon -->
                        <div class="inline-flex items-center justify-center p-2 bg-white/10 rounded-2xl backdrop-blur-sm shadow-lg">
                            <svg class="w-10 h-10 sm:w-14 sm:h-14 md:w-16 md:h-16 text-white fill-current" viewBox="0 0 24 24">
                                <path d="M11.5 13.5L9.75 11.75C9.36 11.36 8.73 11.36 8.34 11.75C7.95 12.14 7.95 12.77 8.34 13.16L10.8 15.62C11.19 16.01 11.82 16.01 12.21 15.62L16.66 11.17C17.05 10.78 17.05 10.15 16.66 9.76C16.27 9.37 15.64 9.37 15.25 9.76L11.5 13.5Z" />
                                <path d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM19 19H5V5H19V19Z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Continuous Smooth Fast Marquee Track -->
                    <div class="relative w-full overflow-hidden py-4">
                        <div class="animate-partner-marquee flex items-center space-x-14 sm:space-x-20 md:space-x-28">
                            
                            <!-- First 8 Logos -->
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/partners/WhatsApp_Image_2026-06-04_at_9.43.37_PM-removebg-preview.webp') }}" alt="Community Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/partners/WhatsApp_Image_2026-06-04_at_9.43.37_PM__1_-removebg-preview.webp') }}" alt="Community Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/partners/WhatsApp_Image_2026-06-04_at_9.43.38_PM-removebg-preview.webp') }}" alt="Community Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/partners/WhatsApp_Image_2026-06-04_at_9.43.38_PM__1_-removebg-preview.webp') }}" alt="Community Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/partners/WhatsApp_Image_2026-06-04_at_9.43.38_PM__2_-removebg-preview.webp') }}" alt="Community Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/partners/WhatsApp_Image_2026-06-04_at_9.43.39_PM__1_-removebg-preview.webp') }}" alt="Community Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/partners/WhatsApp_Image_2026-06-04_at_9.43.39_PM__2_-removebg-preview.webp') }}" alt="Community Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/partners/WhatsApp_Image_2026-06-04_at_9.43.39_PM__3_-removebg-preview.webp') }}" alt="Community Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>

                            <!-- Duplicate 8 Logos for Infinite Smooth Loop -->
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/partners/WhatsApp_Image_2026-06-04_at_9.43.37_PM-removebg-preview.webp') }}" alt="Community Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/partners/WhatsApp_Image_2026-06-04_at_9.43.37_PM__1_-removebg-preview.webp') }}" alt="Community Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/partners/WhatsApp_Image_2026-06-04_at_9.43.38_PM-removebg-preview.webp') }}" alt="Community Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/partners/WhatsApp_Image_2026-06-04_at_9.43.38_PM__1_-removebg-preview.webp') }}" alt="Community Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/partners/WhatsApp_Image_2026-06-04_at_9.43.38_PM__2_-removebg-preview.webp') }}" alt="Community Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/partners/WhatsApp_Image_2026-06-04_at_9.43.39_PM__1_-removebg-preview.webp') }}" alt="Community Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/partners/WhatsApp_Image_2026-06-04_at_9.43.39_PM__2_-removebg-preview.webp') }}" alt="Community Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/partners/WhatsApp_Image_2026-06-04_at_9.43.39_PM__3_-removebg-preview.webp') }}" alt="Community Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                        </div>
                    </div>

                </div>
            </section>
        </div>

        <!-- Section 6: Our Services Section -->
        <section id="services" class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100">
            <div class="w-full max-w-7xl mx-auto">
                
                <!-- Section Title: Our Services -->
                <div class="text-center mb-12 sm:mb-16">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-normal">
                        <span class="text-[#34d399]">Our</span> 
                        <span class="text-[#222222] ml-2">Services</span>
                    </h2>
                </div>

                <!-- 6 Service Image Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    
                    <!-- Card 1: Data Destruction -->
                    <div class="rounded-[22px] p-2 bg-white border border-gray-200/90 shadow-md hover:shadow-xl transition-all duration-300 group">
                        <div class="rounded-[16px] overflow-hidden aspect-square relative w-full bg-gray-100">
                            <img 
                                src="{{ asset('images/services/WhatsApp-Image-2026-03-03-at-2.48.49-AM.webp') }}" 
                                alt="Data Destruction" 
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent flex items-end p-5">
                                <div class="flex items-center gap-3 text-white">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-white/20 border border-white/40 flex items-center justify-center shrink-0 backdrop-blur-sm">
                                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white fill-current" viewBox="0 0 24 24">
                                            <path d="M19 4h-3.5l-1-1h-5l-1 1H5v2h14M6 19a2 2 0 002 2h8a2 2 0 002-2V7H6v12zm3.88-9.41L12 11.71l2.12-2.12 1.41 1.41L13.41 13.12l2.12 2.12-1.41 1.41L12 14.53l-2.12 2.12-1.41-1.41 2.12-2.12-2.12-2.12 1.41-1.41z"/>
                                        </svg>
                                    </div>
                                    <div class="text-xl sm:text-2xl font-extrabold text-white leading-tight drop-shadow-md">
                                        Data<br />Destruction
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: E Waste Recycling -->
                    <div class="rounded-[22px] p-2 bg-white border border-gray-200/90 shadow-md hover:shadow-xl transition-all duration-300 group">
                        <div class="rounded-[16px] overflow-hidden aspect-square relative w-full bg-gray-100">
                            <img 
                                src="{{ asset('images/services/WhatsApp-Image-2026-03-03-at-2.59.30-AM-300x300.webp') }}" 
                                alt="E Waste Recycling" 
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent flex items-end p-5">
                                <div class="flex items-center gap-3 text-white">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-white/20 border border-white/40 flex items-center justify-center shrink-0 backdrop-blur-sm">
                                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white fill-current" viewBox="0 0 24 24">
                                            <path d="M19 4h-3.5l-1-1h-5l-1 1H5v2h14M6 19a2 2 0 002 2h8a2 2 0 002-2V7H6v12zm4-9h4v2h-4v-2zm0 4h4v2h-4v-2z"/>
                                        </svg>
                                    </div>
                                    <div class="text-xl sm:text-2xl font-extrabold text-white leading-tight drop-shadow-md">
                                        E Waste<br />Recycling
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Liquidation Reuse Resale -->
                    <div class="rounded-[22px] p-2 bg-white border border-gray-200/90 shadow-md hover:shadow-xl transition-all duration-300 group">
                        <div class="rounded-[16px] overflow-hidden aspect-square relative w-full bg-gray-100">
                            <img 
                                src="{{ asset('images/services/Asset-Removal-Services-.webp') }}" 
                                alt="Liquidation Reuse Resale" 
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent flex items-end p-5">
                                <div class="flex items-center gap-3 text-white">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-white/20 border border-white/40 flex items-center justify-center shrink-0 backdrop-blur-sm">
                                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white fill-current" viewBox="0 0 24 24">
                                            <path d="M21 2H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h7v2H8v2h8v-2h-2v-2h7c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H3V4h18v12z"/>
                                        </svg>
                                    </div>
                                    <div class="text-xl sm:text-2xl font-extrabold text-white leading-tight drop-shadow-md">
                                        Liquidation<br />Reuse Resale
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Charity Donation -->
                    <div class="rounded-[22px] p-2 bg-white border border-gray-200/90 shadow-md hover:shadow-xl transition-all duration-300 group">
                        <div class="rounded-[16px] overflow-hidden aspect-square relative w-full bg-gray-100">
                            <img 
                                src="{{ asset('images/services/business-partnership-infographics_1284-4534-e1705103864476.webp') }}" 
                                alt="Charity Donation" 
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent flex items-end p-5">
                                <div class="flex items-center gap-3 text-white">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-white/20 border border-white/40 flex items-center justify-center shrink-0 backdrop-blur-sm">
                                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white fill-current" viewBox="0 0 24 24">
                                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                        </svg>
                                    </div>
                                    <div class="text-xl sm:text-2xl font-extrabold text-white leading-tight drop-shadow-md">
                                        Charity<br />Donation
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 5: Partner & Service Provider -->
                    <div class="rounded-[22px] p-2 bg-white border border-gray-200/90 shadow-md hover:shadow-xl transition-all duration-300 group">
                        <div class="rounded-[16px] overflow-hidden aspect-square relative w-full bg-gray-100">
                            <img 
                                src="{{ asset('images/services/Partners-Service-Providers.webp') }}" 
                                alt="Partner & Service Provider" 
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent flex items-end p-5">
                                <div class="flex items-center gap-3 text-white">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-white/20 border border-white/40 flex items-center justify-center shrink-0 backdrop-blur-sm">
                                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white fill-current" viewBox="0 0 24 24">
                                            <path d="M11.5 13.5L9.75 11.75C9.36 11.36 8.73 11.36 8.34 11.75C7.95 12.14 7.95 12.77 8.34 13.16L10.8 15.62C11.19 16.01 11.82 16.01 12.21 15.62L16.66 11.17C17.05 10.78 17.05 10.15 16.66 9.76C16.27 9.37 15.64 9.37 15.25 9.76L11.5 13.5Z"/>
                                            <path d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM19 19H5V5H19V19Z"/>
                                        </svg>
                                    </div>
                                    <div class="text-xl sm:text-2xl font-extrabold text-white leading-tight drop-shadow-md">
                                        Partner &amp;<br />Service Provider
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 6: Rebate Reuse Price List -->
                    <div class="rounded-[22px] p-2 bg-white border border-gray-200/90 shadow-md hover:shadow-xl transition-all duration-300 group">
                        <div class="rounded-[16px] overflow-hidden aspect-square relative w-full bg-gray-100">
                            <img 
                                src="{{ asset('images/services/WhatsApp-Image-2026-03-03-at-2.48.49-AM-1-300x300.webp') }}" 
                                alt="Rebate Reuse Price List" 
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent flex items-end p-5">
                                <div class="flex items-center gap-3 text-white">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-white/20 border border-white/40 flex items-center justify-center shrink-0 backdrop-blur-sm">
                                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white fill-current" viewBox="0 0 24 24">
                                            <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                        </svg>
                                    </div>
                                    <div class="text-xl sm:text-2xl font-extrabold text-white leading-tight drop-shadow-md">
                                        Rebate Reuse<br />Price List
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- Section 7: Our Proud Business Partners Section -->
        <div class="relative w-full bg-white overflow-hidden py-10 sm:py-16 border-t border-gray-100">
            <!-- Slanted Deep Green Outer Section Container -->
            <section class="bg-[#004d2e] py-16 sm:py-24 transform -skew-y-2 origin-top-left shadow-2xl w-full">
                <!-- Inner Content Container -->
                <div class="transform skew-y-2 w-full max-w-7xl mx-auto px-4 sm:px-8 text-center space-y-12">
                    
                    <!-- Section Title: Our Proud Business Partners -->
                    <div class="flex items-center justify-center gap-4 flex-wrap">
                        <h2 class="text-4xl sm:text-6xl md:text-7xl lg:text-[72px] font-black tracking-normal leading-none">
                            <span class="text-[#34d399]">Our Proud</span> 
                            <span class="text-white ml-2 sm:ml-4">Business Partners</span>
                        </h2>
                    </div>

                    <!-- Dual Row Marquee Sliders Moving in OPPOSITE Directions -->
                    <div class="relative w-full overflow-hidden space-y-10 py-6">
                        
                        <!-- Row 1: Moving RIGHT to LEFT -->
                        <div class="animate-marquee-left flex items-center space-x-16 sm:space-x-24 md:space-x-32">
                            <!-- Row 1 Logos -->
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.23.42_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.23.54_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.23.56_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.23.56_AM__1_-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.23.56_AM__2_-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.23.57_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.23.58_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.23.58_AM__1_-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.23.59_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.00_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.00_AM__1_-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.01_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>

                            <!-- Row 1 Logos (Set 2 for Loop) -->
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.23.42_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.23.54_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.23.56_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.23.56_AM__1_-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.23.56_AM__2_-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.23.57_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.23.58_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.23.58_AM__1_-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.23.59_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.00_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.00_AM__1_-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.01_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                        </div>

                        <!-- Row 2: Moving LEFT to RIGHT -->
                        <div class="animate-marquee-right flex items-center space-x-16 sm:space-x-24 md:space-x-32">
                            <!-- Row 2 Logos -->
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.02_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.02_AM__1_-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.03_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.04_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.04_AM__1_-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.04_AM__2_-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.05_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.05_AM__1_-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.05_AM__2_-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.06_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.06_AM__1_-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.06_AM__2_-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>

                            <!-- Row 2 Logos (Set 2 for Loop) -->
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.02_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.02_AM__1_-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.03_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.04_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.04_AM__1_-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.04_AM__2_-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.05_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.05_AM__1_-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.05_AM__2_-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.06_AM-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.06_AM__1_-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                            <div class="flex items-center justify-center h-36 sm:h-48 md:h-56 w-72 sm:w-96 md:w-[420px] shrink-0">
                                <img src="{{ asset('images/services/WhatsApp_Image_2026-06-05_at_1.24.06_AM__2_-removebg-preview.webp') }}" alt="Business Partner" class="max-h-full max-w-full object-contain drop-shadow-md brightness-200" />
                            </div>
                        </div>

                    </div>

                </div>
            </section>
        </div>

        <!-- Section 8: Our Happy Clients Section -->
        <section class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100 overflow-hidden">
            <div class="w-full max-w-7xl mx-auto flex flex-col items-center text-center">
                
                <!-- Section Title: Our Happy Clients 🕴️ -->
                <div class="flex items-center justify-center gap-3 mb-12 sm:mb-16">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-normal">
                        <span class="text-[#222222]">Our</span> 
                        <span class="text-[#34d399] ml-2">Happy Clients</span>
                    </h2>
                    <span class="text-4xl sm:text-5xl">🕴️</span>
                </div>

                <!-- 3 Testimonial Cards Row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 w-full">
                    
                    <!-- Testimonial Card 1 -->
                    <div class="border-2 border-[#005a36] rounded-[22px] bg-white p-6 sm:p-8 shadow-md hover:shadow-xl transition-all duration-300 relative flex flex-col justify-between text-left h-full">
                        <div class="space-y-4">
                            <!-- Star Rating -->
                            <div class="flex items-center justify-center gap-1 text-amber-400 text-lg">
                                <span>★</span><span>★</span><span>★</span><span>★</span><span class="text-gray-300">☆</span>
                            </div>
                            <!-- Paragraph Body -->
                            <p class="text-gray-600 text-sm sm:text-base leading-relaxed text-center font-normal">
                                I have to say, I'm really impressed by professionalism and efficiency of IT-Investment Recoveries team. If you're looking for hassle-free recycling, I highly recommend them.
                            </p>
                        </div>

                        <!-- Card Footer Profile & Quotation Icon -->
                        <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-100">
                            <div class="flex items-center gap-3">
                                <img 
                                    src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80" 
                                    alt="David Jones" 
                                    class="w-12 h-12 rounded-full object-cover shadow-sm border border-gray-200"
                                />
                                <span class="font-bold text-gray-900 text-sm sm:text-base">David Jones</span>
                            </div>
                            <!-- Quote SVG -->
                            <div class="text-[#34d399]/40 font-serif text-5xl leading-none select-none">”</div>
                        </div>
                    </div>

                    <!-- Testimonial Card 2 -->
                    <div class="border-2 border-[#005a36] rounded-[22px] bg-white p-6 sm:p-8 shadow-md hover:shadow-xl transition-all duration-300 relative flex flex-col justify-between text-left h-full">
                        <div class="space-y-4">
                            <!-- Star Rating -->
                            <div class="flex items-center justify-center gap-1 text-amber-400 text-lg">
                                <span>★</span><span>★</span><span>★</span><span>★</span><span class="text-gray-300">☆</span>
                            </div>
                            <!-- Paragraph Body -->
                            <p class="text-gray-600 text-sm sm:text-base leading-relaxed text-center font-normal">
                                I recently hired IT-Investment Recoveries for computer recycling in Denver, and I couldn't be happier. Their team was punctual, professional, and took care of everything. Highly recommended!
                            </p>
                        </div>

                        <!-- Card Footer Profile & Quotation Icon -->
                        <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-100">
                            <div class="flex items-center gap-3">
                                <img 
                                    src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&auto=format&fit=crop&q=80" 
                                    alt="David Martinelli" 
                                    class="w-12 h-12 rounded-full object-cover shadow-sm border border-gray-200"
                                />
                                <span class="font-bold text-gray-900 text-sm sm:text-base">David Martinelli</span>
                            </div>
                            <!-- Quote SVG -->
                            <div class="text-[#34d399]/40 font-serif text-5xl leading-none select-none">”</div>
                        </div>
                    </div>

                    <!-- Testimonial Card 3 -->
                    <div class="border-2 border-[#005a36] rounded-[22px] bg-white p-6 sm:p-8 shadow-md hover:shadow-xl transition-all duration-300 relative flex flex-col justify-between text-left h-full">
                        <div class="space-y-4">
                            <!-- Star Rating -->
                            <div class="flex items-center justify-center gap-1 text-amber-400 text-lg">
                                <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                            </div>
                            <!-- Paragraph Body -->
                            <p class="text-gray-600 text-sm sm:text-base leading-relaxed text-center font-normal">
                                Their commitment to data security and environmentally responsible practices is commendable. A reliable choice for anyone looking to recycle electronics.
                            </p>
                        </div>

                        <!-- Card Footer Profile & Quotation Icon -->
                        <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-100">
                            <div class="flex items-center gap-3">
                                <img 
                                    src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&auto=format&fit=crop&q=80" 
                                    alt="Mark Richardson" 
                                    class="w-12 h-12 rounded-full object-cover shadow-sm border border-gray-200"
                                />
                                <span class="font-bold text-gray-900 text-sm sm:text-base">Mark Richardson</span>
                            </div>
                            <!-- Quote SVG -->
                            <div class="text-[#34d399]/40 font-serif text-5xl leading-none select-none">”</div>
                        </div>
                    </div>

                </div>

                <!-- Pagination Dots -->
                <div class="flex items-center justify-center gap-2 mt-10 mb-16">
                    <button class="w-3.5 h-3.5 rounded-full bg-[#3b82f6] shadow-sm"></button>
                    <button class="w-2.5 h-2.5 rounded-full bg-gray-300 hover:bg-gray-400 transition-colors"></button>
                    <button class="w-2.5 h-2.5 rounded-full bg-gray-300 hover:bg-gray-400 transition-colors"></button>
                    <button class="w-2.5 h-2.5 rounded-full bg-gray-300 hover:bg-gray-400 transition-colors"></button>
                    <button class="w-2.5 h-2.5 rounded-full bg-gray-300 hover:bg-gray-400 transition-colors"></button>
                </div>

            </div>
        </section>

        <!-- Section 9: Recycle Your Electronics Securely Section -->
        <section class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100">
            <div class="w-full max-w-5xl mx-auto flex flex-col items-center text-center space-y-6">
                
                <!-- Section Title: RECYCLE YOUR ELECTRONICS SECURELY -->
                <h2 class="text-3xl sm:text-5xl lg:text-6xl font-black uppercase tracking-tight">
                    <span class="text-[#222222]">RECYCLE YOUR</span> 
                    <span class="text-[#34d399] ml-2 sm:ml-3">ELECTRONICS SECURELY</span>
                </h2>

                <!-- Paragraph Description -->
                <p class="max-w-3xl text-gray-600 text-base sm:text-lg leading-relaxed font-normal">
                    With experience across public, private, nonprofit, and a vast array of industries, we can efficiently meet the needs of any organization. Let us know how we can help your specific business recycle electronics securely and sustainably while saving resources.
                </p>

                <!-- CTA Leaf Pill Button: Schedule A Pickup -> -->
                <div class="pt-4">
                    <a 
                        href="{{ url('/contact-us') }}#pickup-inquiry" 
                        class="inline-flex items-center justify-center gap-2.5 px-8 py-3.5 sm:px-10 sm:py-4 rounded-tl-[24px] rounded-bl-[24px] rounded-tr-[24px] rounded-br-none bg-[#10b981] hover:bg-white text-white hover:text-[#005a36] border-2 border-[#10b981] font-bold text-base sm:text-lg tracking-wide shadow-xl transition-all duration-300 group whitespace-nowrap"
                    >
                        <span class="font-extrabold tracking-wide">Schedule A Pickup</span>
                        <span class="text-xl transition-transform duration-200 group-hover:translate-x-1.5">→</span>
                    </a>
                </div>

            </div>
        </section>

        <!-- Section 10: Talk with an Expert Ratings Banner with Background Image Overlay -->
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

        <!-- Section 11: Subscribe to Our Newsletter Section -->
        <div class="relative w-full bg-white overflow-hidden py-10 sm:py-16">
            <!-- Slanted Deep Green Outer Section Container -->
            <section class="bg-[#004d2e] py-16 sm:py-24 transform -skew-y-2 origin-top-left shadow-2xl w-full">
                <!-- Inner Content Container -->
                <div class="transform skew-y-2 w-full max-w-7xl mx-auto px-4 sm:px-8 flex flex-col lg:flex-row items-center justify-between gap-8 text-center lg:text-left">
                    
                    <!-- Left Title -->
                    <div class="space-y-1">
                        <h2 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-white">
                            Subscribe to <span class="text-[#34d399]">Our</span>
                        </h2>
                        <h2 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-[#34d399]">
                            Newsletter
                        </h2>
                    </div>

                    <!-- Right Input Form -->
                    <form class="flex items-center gap-3 w-full max-w-md flex-wrap sm:flex-nowrap justify-center lg:justify-end" @submit.prevent>
                        <input 
                            type="email" 
                            placeholder="Your E-mail Address" 
                            class="bg-white text-gray-800 placeholder-gray-400 px-6 py-3.5 sm:py-4 rounded-full w-full sm:w-80 shadow-md border-0 focus:outline-none focus:ring-2 focus:ring-[#34d399] font-medium text-sm sm:text-base"
                            required
                        />
                        <button 
                            type="submit" 
                            class="bg-[#34d399] hover:bg-white text-[#004d2e] hover:text-[#004d2e] font-extrabold px-8 py-3.5 sm:py-4 rounded-full shadow-md transition duration-300 text-sm sm:text-base whitespace-nowrap"
                        >
                            Subscribe
                        </button>
                    </form>

                </div>
            </section>
        </div>

        <!-- Full Width Main Site Footer Component -->
        <x-footer />
    </body>
</html>
