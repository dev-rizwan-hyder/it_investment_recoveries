<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="w-full overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Community/ Business Partners | IT Investment Recoveries</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
        <meta name="description" content="Explore our community and business partners supporting eco-friendly electronics recycling, data destruction, and IT asset disposition in Denver, Colorado.">

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

            /* Continuous Smooth Infinite Marquee Animations */
            @keyframes marqueeRight {
                0% { transform: translateX(-50%); }
                100% { transform: translateX(0%); }
            }

            @keyframes marqueeLeft {
                0% { transform: translateX(0%); }
                100% { transform: translateX(-50%); }
            }

            .animate-marquee-right {
                display: flex;
                width: max-content;
                animation: marqueeRight 35s linear infinite;
            }

            .animate-marquee-left {
                display: flex;
                width: max-content;
                animation: marqueeLeft 35s linear infinite;
            }

            .animate-marquee-right:hover,
            .animate-marquee-left:hover {
                animation-play-state: paused;
            }
        </style>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-white text-gray-900 font-['Plus_Jakarta_Sans',sans-serif] antialiased selection:bg-emerald-500 selection:text-white w-full overflow-x-hidden m-0 p-0">

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

        <!-- Section 1: Hero Section (Matching Screenshot) -->
        <section class="relative min-h-[85vh] sm:min-h-[90vh] lg:min-h-[95vh] w-full flex flex-col justify-between overflow-hidden bg-black">
            <!-- Top Transparent Header Overlay -->
            <x-header active="services" />

            <!-- Background Image -->
            <div class="absolute inset-0 z-0 overflow-hidden w-full h-full">
                <img 
                    src="{{ asset('images/about.webp') }}" 
                    alt="Community / Business Partners Header Background" 
                    class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000 ease-out brightness-90 filter"
                />
                <!-- Dark Overlay -->
                <div class="absolute inset-0 z-10 bg-black/60"></div>
            </div>

            <!-- Main Hero Center Content -->
            <div class="relative z-20 flex-grow flex flex-col items-center justify-center text-center px-4 sm:px-8 lg:px-12 pt-48 sm:pt-60 pb-28 sm:pb-36 w-full">
                <div class="w-full max-w-4xl mx-auto">
                    <!-- Title: Community/ Business Partners -->
                    <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-[80px] font-bold text-white tracking-tight leading-none font-['Amaranth',sans-serif] drop-shadow-[0_4px_20px_rgba(0,0,0,0.85)]">
                        Community/ Business Partners
                    </h1>
                </div>
            </div>
        </section>

        <!-- Section 2: Small Green Recycling Icon Divider Line -->
        <div class="bg-white py-6 w-full flex items-center justify-center relative overflow-hidden">
            <div class="w-full max-w-7xl px-6 flex items-center justify-center relative">
                <div class="w-full border-t border-emerald-300"></div>
                <div class="absolute bg-white px-4 text-[#10b981]">
                    <svg class="w-6 h-6 fill-current mx-auto" viewBox="0 0 24 24">
                        <path d="M12 2A10 10 0 1 0 22 12 10.011 10.011 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8.009 8.009 0 0 1-8 8zm-2-12.5l2.5 3 2.5-3h-2V6h-1v1.5zm-3.5 6l3.5 2.5V15h1.5v-1.5H10l-3.5-3.5zm9 0l-3.5-3.5v1.5H12.5V15H14l3.5-2.5z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Section 3: Our Proud Community Partners -->
        <section class="bg-white text-gray-900 pb-12 pt-6 w-full">
            <div class="w-full space-y-8">
                
                <!-- Section Title: Our Proud Community Partners -->
                <div class="text-center px-4">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
                        <span class="text-gray-900">Our Proud</span> 
                        <span class="text-[#34d399] ml-2">Community Partners</span>
                    </h2>
                </div>

                <!-- Full Width Dark Green Container -->
                <div class="bg-[#004d2e] w-full py-12 sm:py-16 shadow-2xl space-y-12">
                    
                    <!-- Slider 1 (Sliding Right inside max-w-6xl) -->
                    <div class="max-w-6xl mx-auto overflow-hidden relative">
                        <div class="animate-marquee-right space-x-12 sm:space-x-20 items-center">
                            <!-- Duplicate Set 1 -->
                            <img src="{{ asset('images/community/slider 1/WhatsApp_Image_2026-06-04_at_9.43.38_PM__2_-removebg-preview.webp') }}" alt="Dare 2 Share" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 1/WhatsApp_Image_2026-06-04_at_9.43.39_PM__1_-removebg-preview.webp') }}" alt="Northglenn Police" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 1/WhatsApp_Image_2026-06-04_at_9.43.39_PM__2_-removebg-preview.webp') }}" alt="Graland" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 1/WhatsApp_Image_2026-06-04_at_9.43.39_PM__3_-removebg-preview.webp') }}" alt="ScrumAlliance" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 1/WhatsApp_Image_2026-08-02_at_12.57.31_AM-removebg-preview.webp') }}" alt="WallChild" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />

                            <!-- Duplicate Set 2 for Seamless Loop -->
                            <img src="{{ asset('images/community/slider 1/WhatsApp_Image_2026-06-04_at_9.43.38_PM__2_-removebg-preview.webp') }}" alt="Dare 2 Share" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 1/WhatsApp_Image_2026-06-04_at_9.43.39_PM__1_-removebg-preview.webp') }}" alt="Northglenn Police" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 1/WhatsApp_Image_2026-06-04_at_9.43.39_PM__2_-removebg-preview.webp') }}" alt="Graland" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 1/WhatsApp_Image_2026-06-04_at_9.43.39_PM__3_-removebg-preview.webp') }}" alt="ScrumAlliance" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 1/WhatsApp_Image_2026-08-02_at_12.57.31_AM-removebg-preview.webp') }}" alt="WallChild" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                        </div>
                    </div>

                    <!-- Slider 2 (Sliding Left inside max-w-6xl) -->
                    <div class="max-w-6xl mx-auto overflow-hidden relative">
                        <div class="animate-marquee-left space-x-12 sm:space-x-20 items-center">
                            <!-- Duplicate Set 1 -->
                            <img src="{{ asset('images/community/slider 2/WhatsApp_Image_2026-06-04_at_9.43.37_PM-removebg-preview.webp') }}" alt="Community College of Aurora" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 2/WhatsApp_Image_2026-06-04_at_9.43.37_PM__1_-removebg-preview.webp') }}" alt="St. Vincent De Paul" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 2/WhatsApp_Image_2026-06-04_at_9.43.38_PM-removebg-preview.webp') }}" alt="Colorado Bar Association" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 2/WhatsApp_Image_2026-06-04_at_9.43.38_PM__1_-removebg-preview.webp') }}" alt="Community Partner" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />

                            <!-- Duplicate Set 2 for Seamless Loop -->
                            <img src="{{ asset('images/community/slider 2/WhatsApp_Image_2026-06-04_at_9.43.37_PM-removebg-preview.webp') }}" alt="Community College of Aurora" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 2/WhatsApp_Image_2026-06-04_at_9.43.37_PM__1_-removebg-preview.webp') }}" alt="St. Vincent De Paul" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 2/WhatsApp_Image_2026-06-04_at_9.43.38_PM-removebg-preview.webp') }}" alt="Colorado Bar Association" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 2/WhatsApp_Image_2026-06-04_at_9.43.38_PM__1_-removebg-preview.webp') }}" alt="Community Partner" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- Section 4: Our Proud Business Partners -->
        <section class="bg-white text-gray-900 pb-20 pt-6 w-full">
            <div class="w-full space-y-8">
                
                <!-- Section Title: Our Proud Business Partners -->
                <div class="text-center px-4">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
                        <span class="text-gray-900">Our Proud</span> 
                        <span class="text-[#34d399] ml-2">Business Partners</span>
                    </h2>
                </div>

                <!-- Full Width Dark Green Container -->
                <div class="bg-[#004d2e] w-full py-12 sm:py-16 shadow-2xl space-y-12">
                    
                    <!-- Slider 3 (Sliding Right inside max-w-6xl) -->
                    <div class="max-w-6xl mx-auto overflow-hidden relative">
                        <div class="animate-marquee-right space-x-12 sm:space-x-20 items-center">
                            <!-- Duplicate Set 1 -->
                            <img src="{{ asset('images/community/slider 3/ChatGPT_Image_Aug_2__2026__12_25_11_AM-removebg-preview.webp') }}" alt="Cator Ruma" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 3/ChatGPT_Image_Aug_2__2026__12_34_34_AM-removebg-preview.webp') }}" alt="Netirio" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.42_AM-removebg-preview.webp') }}" alt="Kleen Tech" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.54_AM-removebg-preview.webp') }}" alt="Beumer Group" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />

                            <!-- Duplicate Set 2 for Seamless Loop -->
                            <img src="{{ asset('images/community/slider 3/ChatGPT_Image_Aug_2__2026__12_25_11_AM-removebg-preview.webp') }}" alt="Cator Ruma" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 3/ChatGPT_Image_Aug_2__2026__12_34_34_AM-removebg-preview.webp') }}" alt="Netirio" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.42_AM-removebg-preview.webp') }}" alt="Kleen Tech" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.54_AM-removebg-preview.webp') }}" alt="Beumer Group" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                        </div>
                    </div>

                    <!-- Slider 4 (Sliding Left inside max-w-6xl) -->
                    <div class="max-w-6xl mx-auto overflow-hidden relative">
                        <div class="animate-marquee-left space-x-12 sm:space-x-20 items-center">
                            <!-- Duplicate Set 1 -->
                            <img src="{{ asset('images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.55_AM-removebg-preview.webp') }}" alt="Kanchi Technologies" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.56_AM-removebg-preview.webp') }}" alt="Vivacity" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.56_AM__1_-removebg-preview.webp') }}" alt="Equus Software" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.56_AM__2_-removebg-preview.webp') }}" alt="Four Seasons" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />

                            <!-- Duplicate Set 2 for Seamless Loop -->
                            <img src="{{ asset('images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.55_AM-removebg-preview.webp') }}" alt="Kanchi Technologies" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.56_AM-removebg-preview.webp') }}" alt="Vivacity" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.56_AM__1_-removebg-preview.webp') }}" alt="Equus Software" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.56_AM__2_-removebg-preview.webp') }}" alt="Four Seasons" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                        </div>
                    </div>

                    <!-- Slider 5 (Sliding Right inside max-w-6xl) -->
                    <div class="max-w-6xl mx-auto overflow-hidden relative">
                        <div class="animate-marquee-right space-x-12 sm:space-x-20 items-center">
                            <!-- Duplicate Set 1 -->
                            <img src="{{ asset('images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.57_AM-removebg-preview.webp') }}" alt="Jlteig" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.57_AM__1_-removebg-preview.webp') }}" alt="Streamline Organizing" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.59_AM-removebg-preview.webp') }}" alt="Endodontics Of Cherry Creek" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.24.00_AM-removebg-preview.webp') }}" alt="ComplyAI" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />

                            <!-- Duplicate Set 2 for Seamless Loop -->
                            <img src="{{ asset('images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.57_AM-removebg-preview.webp') }}" alt="Jlteig" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.57_AM__1_-removebg-preview.webp') }}" alt="Streamline Organizing" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.59_AM-removebg-preview.webp') }}" alt="Endodontics Of Cherry Creek" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                            <img src="{{ asset('images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.24.00_AM-removebg-preview.webp') }}" alt="ComplyAI" class="h-24 sm:h-32 md:h-40 max-h-40 max-w-none object-contain filter drop-shadow-md brightness-200" />
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- Full Width Main Site Footer Component -->
        <x-footer />
    </body>
</html>
