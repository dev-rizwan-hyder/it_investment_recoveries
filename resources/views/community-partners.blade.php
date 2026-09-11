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
                will-change: transform;
            }

            .animate-marquee-left {
                display: flex;
                width: max-content;
                animation: marqueeLeft 35s linear infinite;
                will-change: transform;
            }

            .animate-marquee-right:hover,
            .animate-marquee-left:hover {
                animation-play-state: paused;
            }

            .community-logo-tile {
                position: relative;
                isolation: isolate;
                overflow: hidden;
                border-radius: 12px;
            }

            .community-logo-tile::after {
                content: "";
                position: absolute;
                inset: -120% -55%;
                z-index: 0;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .16), transparent);
                animation: communityPartnerSweep 6.5s ease-in-out infinite;
            }

            @keyframes communityPartnerSweep {
                0%, 70% { transform: translateX(-100%) rotate(25deg); }
                100% { transform: translateX(200%) rotate(25deg); }
            }

            .community-logo-tile img {
                position: relative;
                z-index: 1;
            }

            .community-logo-tile span {
                z-index: 1;
            }
        </style>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-white text-gray-900 font-['Albert_Sans',sans-serif] antialiased selection:bg-[#035c43] selection:text-white w-full overflow-x-hidden m-0 p-0">

        <!-- Fixed Bottom-Left Badge (Always Anchored to Bottom-Left) -->
        <x-events-badge />

        <!-- Section 1: Hero Section (Matching Screenshot) -->
        <section class="relative min-h-[85vh] sm:min-h-[90vh] lg:min-h-screen w-full flex flex-col justify-between overflow-hidden bg-transparent">
            <!-- Top Transparent Header Overlay -->
            <x-header active="services" />

            <!-- Background Image -->
            <div class="absolute inset-0 z-0 overflow-hidden w-full h-full">
                <img 
                    src="{{ asset('images/community/community-hero.webp') }}" 
                    alt="Community / Business Partners Header Background" 
                    class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000 ease-out brightness-95 contrast-105"
                />
                <div class="absolute inset-0 bg-gradient-to-b from-slate-950/75 via-[#01281d]/30 to-slate-950/85"></div>
            </div>

            <!-- Main Hero Center Content -->
            <div class="relative z-20 flex-grow flex flex-col items-center justify-center text-center px-4 sm:px-8 lg:px-12 pt-48 sm:pt-60 md:pt-64 pb-32 sm:pb-40 w-full">
                <div class="w-full max-w-7xl mx-auto">
                    <!-- Title: Community/ Business Partners -->
                    <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-[80px] font-bold text-white tracking-tight leading-none font-['Albert_Sans',sans-serif] drop-shadow-[0_4px_20px_rgba(0,0,0,0.85)]">
                        Community/ Business Partners
                    </h1>
                </div>
            </div>
        </section>

        <!-- Section 2: Small Green Recycling Icon Divider Line -->
        <div class="bg-white py-6 w-full flex items-center justify-center relative overflow-hidden">
            <div class="w-full max-w-7xl px-6 flex items-center justify-center relative">
                <div class="w-full border-t border-emerald-300"></div>
                <div class="absolute bg-white px-4 text-[#035c43]">
                    <svg class="w-6 h-6 fill-current mx-auto" viewBox="0 0 24 24">
                        <path d="M12 2A10 10 0 1 0 22 12 10.011 10.011 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8.009 8.009 0 0 1-8 8zm-2-12.5l2.5 3 2.5-3h-2V6h-1v1.5zm-3.5 6l3.5 2.5V15h1.5v-1.5H10l-3.5-3.5zm9 0l-3.5-3.5v1.5H12.5V15H14l3.5-2.5z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Section 3: Our Proud Community Partners -->
        <section class="bg-white text-gray-900 pb-16 pt-6 w-full">
            <div class="w-full space-y-10">
                
                <!-- Section Title: Our Proud Community Partners -->
                <div class="text-center px-4">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
                        <span class="text-gray-900">Our Proud</span> 
                        <span class="text-[#035c43] ml-2">Community Partners</span>
                    </h2>
                </div>

                <!-- Full Width Dark Green Container -->
                <div class="relative isolate bg-[#035c43] w-full py-14 sm:py-20 shadow-2xl overflow-hidden">
                    <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
                        <div class="absolute inset-0 opacity-[0.08]" style="background-image: linear-gradient(rgba(255,255,255,.22) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.22) 1px, transparent 1px); background-size: 44px 44px;"></div>
                        <div class="absolute left-1/2 top-0 h-28 w-[88vw] -translate-x-1/2 bg-gradient-to-b from-white/10 to-transparent"></div>
                    </div>
                    
                    <div class="relative z-10 w-full max-w-[1650px] mx-auto px-4 space-y-8">
                        <!-- Continuous Marquee Glass Container -->
                        <div class="relative mx-auto w-full overflow-hidden rounded-2xl border border-white/15 bg-white/[0.055] py-8 shadow-[0_28px_80px_rgba(0,0,0,.18)] backdrop-blur-sm sm:py-10 space-y-6 sm:space-y-8">
                            <!-- Left & Right Gradient Fade Edges -->
                            <div class="absolute left-0 top-0 bottom-0 w-16 sm:w-28 lg:w-40 bg-gradient-to-r from-[#035c43] via-[#035c43]/95 to-transparent z-20 pointer-events-none"></div>
                            <div class="absolute right-0 top-0 bottom-0 w-16 sm:w-28 lg:w-40 bg-gradient-to-l from-[#035c43] via-[#035c43]/95 to-transparent z-20 pointer-events-none"></div>
                            <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/45 to-transparent"></div>
                            <div class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-white/25 to-transparent"></div>

                            @php
                                $communitySlider1 = [
                                    ['img' => 'images/community/slider 1/WhatsApp_Image_2026-06-04_at_9.43.38_PM__2_-removebg-preview.webp', 'alt' => 'Dare 2 Share'],
                                    ['img' => 'images/community/slider 1/WhatsApp_Image_2026-06-04_at_9.43.39_PM__1_-removebg-preview.webp', 'alt' => 'Northglenn Police'],
                                    ['img' => 'images/community/slider 1/WhatsApp_Image_2026-06-04_at_9.43.39_PM__2_-removebg-preview.webp', 'alt' => 'Graland'],
                                    ['img' => 'images/community/slider 1/WhatsApp_Image_2026-06-04_at_9.43.39_PM__3_-removebg-preview.webp', 'alt' => 'ScrumAlliance'],
                                    ['img' => 'images/community/slider 1/WhatsApp_Image_2026-08-02_at_12.57.31_AM-removebg-preview.webp', 'alt' => 'WallChild'],
                                ];

                                $communitySlider2 = [
                                    ['img' => 'images/community/slider 2/WhatsApp_Image_2026-06-04_at_9.43.37_PM-removebg-preview.webp', 'alt' => 'Community College of Aurora'],
                                    ['img' => 'images/community/slider 2/WhatsApp_Image_2026-06-04_at_9.43.37_PM__1_-removebg-preview.webp', 'alt' => 'St. Vincent De Paul'],
                                    ['img' => 'images/community/slider 2/WhatsApp_Image_2026-06-04_at_9.43.38_PM-removebg-preview.webp', 'alt' => 'Colorado Bar Association'],
                                    ['img' => 'images/community/slider 2/WhatsApp_Image_2026-06-04_at_9.43.38_PM__1_-removebg-preview.webp', 'alt' => 'Community Partner'],
                                ];
                            @endphp

                            <!-- Slider 1 (Sliding Right) -->
                            <div class="animate-marquee-right items-center">
                                @foreach([1, 2] as $trackCopy)
                                    <div class="flex items-center gap-8 pr-8 sm:gap-12 sm:pr-12 lg:gap-16 lg:pr-16">
                                        @foreach($communitySlider1 as $partner)
                                            <div class="community-logo-tile group flex h-[138px] w-[250px] shrink-0 items-center justify-center border border-white/15 bg-black/10 p-3 shadow-[0_18px_45px_rgba(0,0,0,.16)] transition-all duration-300 hover:-translate-y-1 hover:border-white/35 hover:bg-white/[0.08] hover:shadow-[0_24px_55px_rgba(0,0,0,.22)] sm:h-[156px] sm:w-[300px] sm:p-4 lg:h-[170px] lg:w-[340px] lg:p-5 rounded-xl overflow-hidden text-center">
                                                <span class="!absolute inset-x-5 top-0 h-px bg-gradient-to-r from-transparent via-white/60 to-transparent pointer-events-none"></span>
                                                <span class="!absolute bottom-2 left-1/2 h-2 w-[68%] -translate-x-1/2 rounded-full bg-black/20 blur-sm pointer-events-none"></span>
                                                <img src="{{ asset($partner['img']) }}" alt="{{ $partner['alt'] }}" class="relative z-10 mx-auto my-auto max-h-[98px] max-w-[92%] object-contain object-center filter drop-shadow-[0_8px_16px_rgba(0,0,0,.3)] brightness-200 transition-transform duration-300 group-hover:scale-105 sm:max-h-[118px] lg:max-h-[132px]" />
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>

                            <!-- Slider 2 (Sliding Left) -->
                            <div class="animate-marquee-left items-center">
                                @foreach([1, 2] as $trackCopy)
                                    <div class="flex items-center gap-8 pr-8 sm:gap-12 sm:pr-12 lg:gap-16 lg:pr-16">
                                        @foreach($communitySlider2 as $partner)
                                            <div class="community-logo-tile group flex h-[138px] w-[250px] shrink-0 items-center justify-center border border-white/15 bg-black/10 p-3 shadow-[0_18px_45px_rgba(0,0,0,.16)] transition-all duration-300 hover:-translate-y-1 hover:border-white/35 hover:bg-white/[0.08] hover:shadow-[0_24px_55px_rgba(0,0,0,.22)] sm:h-[156px] sm:w-[300px] sm:p-4 lg:h-[170px] lg:w-[340px] lg:p-5 rounded-xl overflow-hidden text-center">
                                                <span class="!absolute inset-x-5 top-0 h-px bg-gradient-to-r from-transparent via-white/60 to-transparent pointer-events-none"></span>
                                                <span class="!absolute bottom-2 left-1/2 h-2 w-[68%] -translate-x-1/2 rounded-full bg-black/20 blur-sm pointer-events-none"></span>
                                                <img src="{{ asset($partner['img']) }}" alt="{{ $partner['alt'] }}" class="relative z-10 mx-auto my-auto max-h-[98px] max-w-[92%] object-contain object-center filter drop-shadow-[0_8px_16px_rgba(0,0,0,.3)] brightness-200 transition-transform duration-300 group-hover:scale-105 sm:max-h-[118px] lg:max-h-[132px]" />
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- Section 4: Our Proud Business Partners -->
        <section class="bg-white text-gray-900 pb-20 pt-6 w-full">
            <div class="w-full space-y-10">
                
                <!-- Section Title: Our Proud Business Partners -->
                <div class="text-center px-4">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
                        <span class="text-gray-900">Our Proud</span> 
                        <span class="text-[#035c43] ml-2">Business Partners</span>
                    </h2>
                </div>

                <!-- Full Width Dark Green Container -->
                <div class="relative isolate bg-[#035c43] w-full py-14 sm:py-20 shadow-2xl overflow-hidden">
                    <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
                        <div class="absolute inset-0 opacity-[0.08]" style="background-image: linear-gradient(rgba(255,255,255,.22) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.22) 1px, transparent 1px); background-size: 44px 44px;"></div>
                        <div class="absolute left-1/2 top-0 h-28 w-[88vw] -translate-x-1/2 bg-gradient-to-b from-white/10 to-transparent"></div>
                    </div>

                    <div class="relative z-10 w-full max-w-[1650px] mx-auto px-4 space-y-8">
                        <!-- Continuous Marquee Glass Container -->
                        <div class="relative mx-auto w-full overflow-hidden rounded-2xl border border-white/15 bg-white/[0.055] py-8 shadow-[0_28px_80px_rgba(0,0,0,.18)] backdrop-blur-sm sm:py-10 space-y-6 sm:space-y-8">
                            <!-- Left & Right Gradient Fade Edges -->
                            <div class="absolute left-0 top-0 bottom-0 w-16 sm:w-28 lg:w-40 bg-gradient-to-r from-[#035c43] via-[#035c43]/95 to-transparent z-20 pointer-events-none"></div>
                            <div class="absolute right-0 top-0 bottom-0 w-16 sm:w-28 lg:w-40 bg-gradient-to-l from-[#035c43] via-[#035c43]/95 to-transparent z-20 pointer-events-none"></div>
                            <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/45 to-transparent"></div>
                            <div class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-white/25 to-transparent"></div>

                            @php
                                $businessSlider1 = [
                                    ['img' => 'images/community/slider 3/ChatGPT_Image_Aug_2__2026__12_25_11_AM-removebg-preview.webp', 'alt' => 'Cator Ruma'],
                                    ['img' => 'images/community/slider 3/ChatGPT_Image_Aug_2__2026__12_34_34_AM-removebg-preview.webp', 'alt' => 'Netirio'],
                                    ['img' => 'images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.42_AM-removebg-preview.webp', 'alt' => 'Kleen Tech'],
                                    ['img' => 'images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.54_AM-removebg-preview.webp', 'alt' => 'Beumer Group'],
                                ];

                                $businessSlider2 = [
                                    ['img' => 'images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.55_AM-removebg-preview.webp', 'alt' => 'Kanchi Technologies'],
                                    ['img' => 'images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.56_AM-removebg-preview.webp', 'alt' => 'Vivacity'],
                                    ['img' => 'images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.56_AM__1_-removebg-preview.webp', 'alt' => 'Equus Software'],
                                    ['img' => 'images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.56_AM__2_-removebg-preview.webp', 'alt' => 'Four Seasons'],
                                ];

                                $businessSlider3 = [
                                    ['img' => 'images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.57_AM-removebg-preview.webp', 'alt' => 'Jlteig'],
                                    ['img' => 'images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.57_AM__1_-removebg-preview.webp', 'alt' => 'Streamline Organizing'],
                                    ['img' => 'images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.23.59_AM-removebg-preview.webp', 'alt' => 'Endodontics Of Cherry Creek'],
                                    ['img' => 'images/community/slider 3/WhatsApp_Image_2026-06-05_at_1.24.00_AM-removebg-preview.webp', 'alt' => 'ComplyAI'],
                                ];
                            @endphp

                            <!-- Slider 3 (Sliding Right) -->
                            <div class="animate-marquee-right items-center">
                                @foreach([1, 2] as $trackCopy)
                                    <div class="flex items-center gap-8 pr-8 sm:gap-12 sm:pr-12 lg:gap-16 lg:pr-16">
                                        @foreach($businessSlider1 as $partner)
                                            <div class="community-logo-tile group flex h-[138px] w-[250px] shrink-0 items-center justify-center border border-white/15 bg-black/10 p-3 shadow-[0_18px_45px_rgba(0,0,0,.16)] transition-all duration-300 hover:-translate-y-1 hover:border-white/35 hover:bg-white/[0.08] hover:shadow-[0_24px_55px_rgba(0,0,0,.22)] sm:h-[156px] sm:w-[300px] sm:p-4 lg:h-[170px] lg:w-[340px] lg:p-5 rounded-xl overflow-hidden text-center">
                                                <span class="!absolute inset-x-5 top-0 h-px bg-gradient-to-r from-transparent via-white/60 to-transparent pointer-events-none"></span>
                                                <span class="!absolute bottom-2 left-1/2 h-2 w-[68%] -translate-x-1/2 rounded-full bg-black/20 blur-sm pointer-events-none"></span>
                                                <img src="{{ asset($partner['img']) }}" alt="{{ $partner['alt'] }}" class="relative z-10 mx-auto my-auto max-h-[98px] max-w-[92%] object-contain object-center filter drop-shadow-[0_8px_16px_rgba(0,0,0,.3)] brightness-200 transition-transform duration-300 group-hover:scale-105 sm:max-h-[118px] lg:max-h-[132px]" />
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>

                            <!-- Slider 4 (Sliding Left) -->
                            <div class="animate-marquee-left items-center">
                                @foreach([1, 2] as $trackCopy)
                                    <div class="flex items-center gap-8 pr-8 sm:gap-12 sm:pr-12 lg:gap-16 lg:pr-16">
                                        @foreach($businessSlider2 as $partner)
                                            <div class="community-logo-tile group flex h-[138px] w-[250px] shrink-0 items-center justify-center border border-white/15 bg-black/10 p-3 shadow-[0_18px_45px_rgba(0,0,0,.16)] transition-all duration-300 hover:-translate-y-1 hover:border-white/35 hover:bg-white/[0.08] hover:shadow-[0_24px_55px_rgba(0,0,0,.22)] sm:h-[156px] sm:w-[300px] sm:p-4 lg:h-[170px] lg:w-[340px] lg:p-5 rounded-xl overflow-hidden text-center">
                                                <span class="!absolute inset-x-5 top-0 h-px bg-gradient-to-r from-transparent via-white/60 to-transparent pointer-events-none"></span>
                                                <span class="!absolute bottom-2 left-1/2 h-2 w-[68%] -translate-x-1/2 rounded-full bg-black/20 blur-sm pointer-events-none"></span>
                                                <img src="{{ asset($partner['img']) }}" alt="{{ $partner['alt'] }}" class="relative z-10 mx-auto my-auto max-h-[98px] max-w-[92%] object-contain object-center filter drop-shadow-[0_8px_16px_rgba(0,0,0,.3)] brightness-200 transition-transform duration-300 group-hover:scale-105 sm:max-h-[118px] lg:max-h-[132px]" />
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>

                            <!-- Slider 5 (Sliding Right) -->
                            <div class="animate-marquee-right items-center">
                                @foreach([1, 2] as $trackCopy)
                                    <div class="flex items-center gap-8 pr-8 sm:gap-12 sm:pr-12 lg:gap-16 lg:pr-16">
                                        @foreach($businessSlider3 as $partner)
                                            <div class="community-logo-tile group flex h-[138px] w-[250px] shrink-0 items-center justify-center border border-white/15 bg-black/10 p-3 shadow-[0_18px_45px_rgba(0,0,0,.16)] transition-all duration-300 hover:-translate-y-1 hover:border-white/35 hover:bg-white/[0.08] hover:shadow-[0_24px_55px_rgba(0,0,0,.22)] sm:h-[156px] sm:w-[300px] sm:p-4 lg:h-[170px] lg:w-[340px] lg:p-5 rounded-xl overflow-hidden text-center">
                                                <span class="!absolute inset-x-5 top-0 h-px bg-gradient-to-r from-transparent via-white/60 to-transparent pointer-events-none"></span>
                                                <span class="!absolute bottom-2 left-1/2 h-2 w-[68%] -translate-x-1/2 rounded-full bg-black/20 blur-sm pointer-events-none"></span>
                                                <img src="{{ asset($partner['img']) }}" alt="{{ $partner['alt'] }}" class="relative z-10 mx-auto my-auto max-h-[98px] max-w-[92%] object-contain object-center filter drop-shadow-[0_8px_16px_rgba(0,0,0,.3)] brightness-200 transition-transform duration-300 group-hover:scale-105 sm:max-h-[118px] lg:max-h-[132px]" />
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- Full Width Main Site Footer Component -->
        <x-footer />
    </body>
</html>
