<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="w-full overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Our Services | Electronics Recycling &amp; IT Asset Disposition | IT Investment Recoveries</title>
        <meta name="description" content="Explore our core IT services including Data Destruction, E-Waste Recycling, Computer Liquidation, Charity Donation, and Partner Service Solutions in Denver and Toronto.">

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
        </style>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-white text-gray-900 font-['Albert_Sans',sans-serif] antialiased selection:bg-[#035c43] selection:text-white w-full overflow-x-hidden m-0 p-0">

        <!-- Fixed Bottom-Left Badge (Always Anchored to Bottom-Left) -->
        <x-events-badge />

        <!-- Section 1: Hero Section (Globe held over circuit boards background) -->
        <section class="relative min-h-[75vh] sm:min-h-[85vh] lg:min-h-screen w-full flex flex-col justify-between overflow-hidden bg-transparent">
            <!-- Top Transparent Header Overlay -->
            <x-header active="services" />

            <!-- Background Image -->
            <div class="absolute inset-0 z-0 overflow-hidden w-full h-full">
                <img 
                    src="{{ asset('images/old-electronic-devices-world-globe-e-waste-recycling-concept_878453-6585.webp') }}" 
                    alt="Our Services - IT Investment Recoveries Globe E-Waste" 
                    class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000 ease-out"
                />
                
            </div>

            <!-- Main Hero Center Content -->
            <div class="relative z-20 flex-grow flex flex-col items-center justify-center text-center px-4 sm:px-8 lg:px-12 pt-36 sm:pt-48 md:pt-56 pb-24 sm:pb-32 w-full">
                <div class="w-full max-w-4xl mx-auto space-y-5 sm:space-y-7">
                    
                    <!-- Main Title: Our Services -->
                    <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-[84px] font-bold text-white tracking-tight leading-none font-['Albert_Sans',sans-serif] drop-shadow-[0_4px_20px_rgba(0,0,0,0.85)]">
                        Our Services
                    </h1>

                    <!-- Paragraph Subtitle -->
                    <p class="text-gray-100 text-base sm:text-lg md:text-xl lg:text-2xl font-medium max-w-2xl mx-auto leading-relaxed drop-shadow-md">
                        From data destruction to certified recycling, explore how we can help manage your unused computers, electronics &amp; equipment.
                    </p>

                    <!-- CTA Button: Schedule A Consultation -> -->
                    <div class="pt-4">
                        <a 
                            href="{{ url('/contact-us') }}" 
                            class="inline-flex items-center justify-center gap-2.5 px-8 py-3.5 sm:px-10 sm:py-4 rounded-full bg-[#035c43] hover:bg-[#024734] text-white font-extrabold text-base sm:text-lg tracking-wide shadow-2xl transition duration-300 transform hover:scale-105 group"
                        >
                            <span>Schedule A Consultation</span>
                            <span class="text-xl transition-transform duration-200 group-hover:translate-x-1.5">→</span>
                        </a>
                    </div>

                </div>
            </div>
        </section>

        <!-- Section 2: Our Core Services (6 Cards Grid) -->
        <section class="bg-white text-gray-900 py-12 sm:py-20 px-4 sm:px-8 lg:px-16 w-full">
            <div class="w-full max-w-7xl mx-auto">
                
                <!-- Section Title: Our Core Services -->
                <div class="text-center mb-12 sm:mb-16">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-normal">
                        <span class="text-[#035c43]">Our Core</span> 
                        <span class="text-[#222222] ml-2">Services</span>
                    </h2>
                </div>

                <!-- 6 Service Cards Grid (3 Columns x 2 Rows) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 sm:gap-10">
                    
                    <!-- Card 1: Data Destruction -->
                    <div class="rounded-[28px] p-2.5 bg-white border-4 border-white shadow-xl hover:shadow-2xl transition duration-500 transform hover:-translate-y-1.5 group">
                        <div class="rounded-[20px] overflow-hidden aspect-square relative w-full bg-gray-100">
                            <img 
                                src="{{ asset('images/services/WhatsApp-Image-2026-03-03-at-2.48.49-AM.webp') }}" 
                                alt="Data Destruction" 
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700"
                            />
                            <!-- Dark Bottom Overlay with Icon & Title -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent flex items-end p-6">
                                <div class="flex items-center gap-3.5 text-white">
                                    <div class="w-12 h-12 rounded-xl bg-white/20 border border-white/40 flex items-center justify-center shrink-0 backdrop-blur-sm">
                                        <svg class="w-7 h-7 text-white fill-current" viewBox="0 0 24 24">
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
                    <div class="rounded-[28px] p-2.5 bg-white border-4 border-white shadow-xl hover:shadow-2xl transition duration-500 transform hover:-translate-y-1.5 group">
                        <div class="rounded-[20px] overflow-hidden aspect-square relative w-full bg-gray-100">
                            <img 
                                src="{{ asset('images/services/WhatsApp-Image-2026-03-03-at-2.59.30-AM-300x300.webp') }}" 
                                alt="E Waste Recycling" 
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700"
                            />
                            <!-- Dark Bottom Overlay with Icon & Title -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent flex items-end p-6">
                                <div class="flex items-center gap-3.5 text-white">
                                    <div class="w-12 h-12 rounded-xl bg-white/20 border border-white/40 flex items-center justify-center shrink-0 backdrop-blur-sm">
                                        <svg class="w-7 h-7 text-white fill-current" viewBox="0 0 24 24">
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
                    <div class="rounded-[28px] p-2.5 bg-white border-4 border-white shadow-xl hover:shadow-2xl transition duration-500 transform hover:-translate-y-1.5 group">
                        <div class="rounded-[20px] overflow-hidden aspect-square relative w-full bg-gray-100">
                            <img 
                                src="{{ asset('images/services/Asset-Removal-Services-.webp') }}" 
                                alt="Liquidation Reuse Resale" 
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700"
                            />
                            <!-- Dark Bottom Overlay with Icon & Title -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent flex items-end p-6">
                                <div class="flex items-center gap-3.5 text-white">
                                    <div class="w-12 h-12 rounded-xl bg-white/20 border border-white/40 flex items-center justify-center shrink-0 backdrop-blur-sm">
                                        <svg class="w-7 h-7 text-white fill-current" viewBox="0 0 24 24">
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
                    <div class="rounded-[28px] p-2.5 bg-white border-4 border-white shadow-xl hover:shadow-2xl transition duration-500 transform hover:-translate-y-1.5 group">
                        <div class="rounded-[20px] overflow-hidden aspect-square relative w-full bg-gray-100">
                            <img 
                                src="{{ asset('images/services/business-partnership-infographics_1284-4534-e1705103864476.webp') }}" 
                                alt="Charity Donation" 
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700"
                            />
                            <!-- Dark Bottom Overlay with Icon & Title -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent flex items-end p-6">
                                <div class="flex items-center gap-3.5 text-white">
                                    <div class="w-12 h-12 rounded-xl bg-white/20 border border-white/40 flex items-center justify-center shrink-0 backdrop-blur-sm">
                                        <svg class="w-7 h-7 text-white fill-current" viewBox="0 0 24 24">
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
                    <div class="rounded-[28px] p-2.5 bg-white border-4 border-white shadow-xl hover:shadow-2xl transition duration-500 transform hover:-translate-y-1.5 group">
                        <div class="rounded-[20px] overflow-hidden aspect-square relative w-full bg-gray-100">
                            <img 
                                src="{{ asset('images/services/Partners-Service-Providers.webp') }}" 
                                alt="Partner & Service Provider" 
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700"
                            />
                            <!-- Dark Bottom Overlay with Icon & Title -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent flex items-end p-6">
                                <div class="flex items-center gap-3.5 text-white">
                                    <div class="w-12 h-12 rounded-xl bg-white/20 border border-white/40 flex items-center justify-center shrink-0 backdrop-blur-sm">
                                        <svg class="w-7 h-7 text-white fill-current" viewBox="0 0 24 24">
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
                    <div class="rounded-[28px] p-2.5 bg-white border-4 border-white shadow-xl hover:shadow-2xl transition duration-500 transform hover:-translate-y-1.5 group">
                        <div class="rounded-[20px] overflow-hidden aspect-square relative w-full bg-gray-100">
                            <img 
                                src="{{ asset('images/services/WhatsApp-Image-2026-03-03-at-2.48.49-AM-1-300x300.webp') }}" 
                                alt="Rebate Reuse Price List" 
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700"
                            />
                            <!-- Dark Bottom Overlay with Icon & Title -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent flex items-end p-6">
                                <div class="flex items-center gap-3.5 text-white">
                                    <div class="w-12 h-12 rounded-xl bg-white/20 border border-white/40 flex items-center justify-center shrink-0 backdrop-blur-sm">
                                        <svg class="w-7 h-7 text-white fill-current" viewBox="0 0 24 24">
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

        <!-- Section 3: Schedule a Dropoff or Pickup (Circuit Board fLATIRONS.webp Background) -->
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
                            href="{{ url('/contact-us') }}" 
                            class="px-7 py-3 rounded-full bg-[#035c43] hover:bg-[#024734] text-white font-bold text-sm sm:text-base shadow-md transition-all duration-300"
                        >
                            Book Consultation
                        </a>

                        <!-- Schedule Pickup -->
                        <a 
                            href="{{ url('/contact-us') }}" 
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
