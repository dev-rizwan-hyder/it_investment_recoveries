<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="w-full overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>E-Waste &amp; IT Disposal in Denver | Computer &amp; Electronics Recycling</title>
        <meta name="description" content="E-Waste &amp; IT Disposal services in Denver. Secure computer recycling, electronics disposal, hard drive shredding, and asset recovery for businesses and residents.">

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
        </style>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-white text-gray-900 font-['Plus_Jakarta_Sans',sans-serif] antialiased selection:bg-emerald-500 selection:text-white w-full overflow-x-hidden m-0 p-0">

        <!-- Fixed Bottom-Left Badge -->
        <x-events-badge />

        <!-- Section 1: Hero Banner -->
        <section class="relative min-h-[65vh] sm:min-h-[75vh] lg:min-h-[85vh] w-full flex flex-col justify-between overflow-hidden bg-[#1b5e3a]">
            <!-- Header Overlay -->
            <x-header active="services" />

            <!-- Background Image -->
            <div class="absolute inset-0 z-0 overflow-hidden w-full h-full">
                <img 
                    src="{{ asset('images/IT Disposal/hero.webp') }}" 
                    alt="E-Waste / IT Disposal" 
                    class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000 ease-out"
                />
                <div class="absolute inset-0 z-10 bg-black/25"></div>
            </div>

            <!-- Hero Center Content -->
            <div class="relative z-20 flex-grow flex flex-col items-center justify-center text-center px-4 sm:px-8 lg:px-12 pt-36 sm:pt-48 md:pt-52 pb-20 sm:pb-28 w-full">
                <div class="w-full max-w-5xl mx-auto space-y-4">
                    
                    <!-- Title -->
                    <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-[84px] font-bold text-white tracking-tight leading-[1.1] font-['Amaranth',sans-serif] drop-shadow-[0_4px_25px_rgba(0,0,0,0.85)]">
                        E-Waste / IT Disposal
                    </h1>

                </div>
            </div>
        </section>

        <!-- Section 2: E Waste Recycling in Denver -->
        <section class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full">
            <div class="w-full max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Left Side: Text & Button -->
                <div class="lg:col-span-7 space-y-6 text-left">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
                        <span class="text-[#34d399]">E Waste</span> 
                        <span class="text-gray-900 ml-2">Recycling</span><br />
                        <span class="text-gray-900">in Denver</span>
                    </h2>
                    <p class="text-gray-700 text-lg sm:text-xl md:text-2xl font-medium leading-relaxed max-w-2xl">
                        Trusted e-waste recycling in Denver – We sustainably and securely disposes of your old electronics like computers and laptops. Affordable, tailored e-waste solutions.
                    </p>
                    <div class="pt-2">
                        <a 
                            href="{{ url('/contact-us') }}" 
                            class="inline-flex items-center gap-2.5 px-9 py-4 rounded-full bg-[#005a36] hover:bg-[#10b981] text-white font-extrabold text-lg shadow-xl transition duration-300 transform hover:scale-105"
                        >
                            <span>Schedule A Pickup</span>
                            <span class="text-xl">→</span>
                        </a>
                    </div>
                </div>

                <!-- Right Side: Laptop Image with Green Frame -->
                <div class="lg:col-span-5 flex justify-center">
                    <div class="rounded-[30px] border-4 border-emerald-500/40 p-6 bg-white shadow-2xl max-w-md w-full flex items-center justify-center transform hover:scale-105 transition duration-500">
                        <img 
                            src="{{ asset('images/IT Disposal/image-removebg-preview-2.webp') }}" 
                            alt="E Waste Recycling Laptop" 
                            class="w-full h-auto object-contain max-h-72"
                        />
                    </div>
                </div>

            </div>
        </section>

        <!-- Small Green Recycling Icon Divider -->
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

        <!-- Section 3: Denver E-Waste Recycling Experts -->
        <section class="bg-white text-gray-900 py-10 sm:py-16 px-4 sm:px-8 lg:px-16 w-full">
            <div class="w-full max-w-5xl mx-auto text-center space-y-6">
                
                <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
                    <span class="text-[#34d399]">Denver E-Waste</span> 
                    <span class="text-gray-900 ml-2">Recycling Experts</span>
                </h2>

                <p class="text-gray-700 text-base sm:text-lg lg:text-xl font-medium leading-relaxed max-w-4xl mx-auto">
                    We provide premier e waste recycling services to businesses and residents across Denver, Colorado. For over 10 years, we have worked with companies large and small to securely and sustainably dispose of electronics in an eco-friendly way. As experts in data destruction, hardware recycling, and asset recovery, we make the process convenient while keeping green standards top of mind.
                </p>

            </div>
        </section>

        <!-- Section 4: Our Services (Full-Width Slanted Banners) -->
        <section class="bg-white text-gray-900 py-16 sm:py-24 w-full overflow-hidden border-t border-gray-100">
            <div class="w-full space-y-16">
                
                <!-- Section Title -->
                <div class="text-center px-4">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-normal">
                        <span class="text-[#34d399]">Our</span> 
                        <span class="text-gray-900 ml-2">Services</span>
                    </h2>
                </div>

                <!-- 3 Full-Width Slanted Banners -->
                <div class="space-y-16">
                    
                    <!-- Banner 1: Secure Data Destruction (Dark Green Slanted) -->
                    <div class="bg-[#004d2e] text-white transform -skew-y-2 py-16 sm:py-20 w-full overflow-hidden shadow-2xl">
                        <div class="transform skew-y-2 w-full max-w-7xl mx-auto px-6 sm:px-12 flex flex-col md:flex-row items-center justify-between gap-10">
                            <div class="space-y-6 text-left max-w-2xl">
                                <h3 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white">
                                    Secure Data Destruction
                                </h3>
                                <p class="text-gray-100 text-lg sm:text-xl md:text-2xl font-medium leading-relaxed">
                                    We securely destroy data on hard drives and other storage media through onsite shredding. You receive documentation providing chain of custody and certificates confirming proper data destruction.
                                </p>
                                <ul class="space-y-3 text-lg sm:text-xl font-bold text-gray-100 pl-2">
                                    <li class="flex items-center gap-3">
                                        <span class="text-[#34d399] font-black text-2xl">•</span>
                                        <span>Onsite drive shredding</span>
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <span class="text-[#34d399] font-black text-2xl">•</span>
                                        <span>Full documentation provided</span>
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <span class="text-[#34d399] font-black text-2xl">•</span>
                                        <span>Compliance reporting</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="shrink-0 max-w-xs sm:max-w-md">
                                <img 
                                    src="{{ asset('images/IT Disposal/Hard_Drive_Shredding_-removebg-preview.webp') }}" 
                                    alt="Secure Data Destruction" 
                                    class="w-full h-auto max-h-80 object-contain filter drop-shadow-2xl transform hover:scale-105 transition duration-500"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Banner 2: Electronics Recycling (Dark Gray Left Block, White Right Side) -->
                    <div class="w-full overflow-hidden my-12">
                        <div class="w-full max-w-7xl mx-auto flex flex-col md:flex-row items-stretch justify-between gap-8 sm:gap-12">
                            <!-- Left Dark Gray Block with Image -->
                            <div class="md:w-1/2 bg-[#444645] p-8 sm:p-12 flex items-center justify-center rounded-2xl shadow-xl">
                                <img 
                                    src="{{ asset('images/IT Disposal/image-removebg-preview-3.webp') }}" 
                                    alt="Electronics Recycling" 
                                    class="w-full h-auto max-h-80 object-contain filter drop-shadow-2xl transform hover:scale-105 transition duration-500"
                                />
                            </div>
                            <!-- Right White Side with Text -->
                            <div class="md:w-1/2 space-y-5 text-left flex flex-col justify-center py-4 px-2 sm:px-6">
                                <h3 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900">
                                    Electronics Recycling
                                </h3>
                                <p class="text-gray-700 text-base sm:text-lg font-medium leading-relaxed">
                                    We properly recycle computers, mobile devices, peripherals and network equipment through ecologically sound methods. This includes transportation logistics.
                                </p>
                                <ul class="space-y-2.5 text-sm sm:text-base font-semibold text-gray-800 list-disc list-inside pt-1">
                                    <li>Computer and server recycling</li>
                                    <li>Mobile phones &amp; tablet recycling</li>
                                    <li>Printers, copiers &amp; monitors</li>
                                    <li>Networking equipment</li>
                                    <li>Scheduled pickup &amp; transport</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Banner 3: Asset Recovery & Resales (Dark Green Banner) -->
                    <div class="bg-[#004d2e] text-white transform -skew-y-2 py-14 sm:py-20 w-full overflow-hidden shadow-2xl">
                        <div class="transform skew-y-2 w-full max-w-7xl mx-auto px-6 sm:px-12 flex flex-col md:flex-row items-center justify-between gap-10">
                            <div class="space-y-5 text-left max-w-2xl">
                                <h3 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white">
                                    Asset Recovery &amp; Resales
                                </h3>
                                <p class="text-gray-100 text-base sm:text-lg font-medium leading-relaxed">
                                    We identify equipment that still has value to be resold or refurbished for reuse. For items we resell, you receive a portion of the revenue.
                                </p>
                                <ul class="space-y-2.5 text-sm sm:text-base font-semibold text-gray-100 list-disc list-inside pt-1">
                                    <li>Data wiping for resale</li>
                                    <li>Refurbished equipment sales</li>
                                    <li>Revenue remittance</li>
                                </ul>
                            </div>
                            <div class="shrink-0 max-w-xs sm:max-w-md">
                                <img 
                                    src="{{ asset('images/IT Disposal/iStock-1337403225-removebg-preview.webp') }}" 
                                    alt="Asset Recovery &amp; Resales" 
                                    class="w-full h-auto max-h-80 object-contain filter drop-shadow-2xl transform hover:scale-105 transition duration-500"
                                />
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- Small Green Recycling Icon Divider -->
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

        <!-- Section 5: Why Choose Us (4 Column Feature Cards) -->
        <section class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100">
            <div class="w-full max-w-7xl mx-auto space-y-12">
                
                <!-- Title -->
                <div class="text-center">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-normal">
                        <span class="text-[#34d399]">Why</span> 
                        <span class="text-gray-900 ml-2">Choose Us</span>
                    </h2>
                </div>

                <!-- 4 Columns Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    
                    <!-- Column 1 -->
                    <div class="text-center space-y-3 px-2">
                        <div class="w-12 h-12 text-gray-800 mx-auto flex items-center justify-center">
                            <svg class="w-10 h-10 fill-current" viewBox="0 0 24 24">
                                <path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.58 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-[#005a36]">
                            Guaranteed Secure Destruction
                        </h3>
                        <p class="text-gray-600 text-xs sm:text-sm font-normal leading-relaxed">
                            Our team shreds hardware and wipes data fully to rigorous standards, with certificates of destruction.
                        </p>
                    </div>

                    <!-- Column 2 -->
                    <div class="text-center space-y-3 px-2">
                        <div class="w-12 h-12 text-gray-800 mx-auto flex items-center justify-center">
                            <svg class="w-10 h-10 fill-current" viewBox="0 0 24 24">
                                <path d="M11.5 13.5L9.75 11.75C9.36 11.36 8.73 11.36 8.34 11.75C7.95 12.14 7.95 12.77 8.34 13.16L10.8 15.62C11.19 16.01 11.82 16.01 12.21 15.62L16.66 11.17C17.05 10.78 17.05 10.15 16.66 9.76C16.27 9.37 15.64 9.37 15.25 9.76L11.5 13.5Z"/>
                                <path d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM19 19H5V5H19V19Z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-[#005a36]">
                            Hassle-Free Process
                        </h3>
                        <p class="text-gray-600 text-xs sm:text-sm font-normal leading-relaxed">
                            We handle scheduling, logistics, transportation and processing - making electronics recycling smooth and simple for clients.
                        </p>
                    </div>

                    <!-- Column 3 -->
                    <div class="text-center space-y-3 px-2">
                        <div class="w-12 h-12 text-gray-800 mx-auto flex items-center justify-center">
                            <svg class="w-10 h-10 fill-current" viewBox="0 0 24 24">
                                <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2zM6 14h6v2H6z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-[#005a36]">
                            Maximum Financial Returns
                        </h3>
                        <p class="text-gray-600 text-xs sm:text-sm font-normal leading-relaxed">
                            From asset recovery re-sales to qualified tax deductions, we maximize savings and value recovery from old equipment.
                        </p>
                    </div>

                    <!-- Column 4 -->
                    <div class="text-center space-y-3 px-2">
                        <div class="w-12 h-12 text-gray-800 mx-auto flex items-center justify-center">
                            <svg class="w-10 h-10 fill-current" viewBox="0 0 24 24">
                                <path d="M10 21h4v-3h3l-5-7h3l-5-7-5 7h3l-5 7h3z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-[#005a36]">
                            Eco-Friendly Practices
                        </h3>
                        <p class="text-gray-600 text-xs sm:text-sm font-normal leading-relaxed">
                            We adhere to strict zero landfill policies while recovering tonnes of electronics every year - positively impacting our environment.
                        </p>
                    </div>

                </div>

            </div>
        </section>

        <!-- Section 6: Our Satisfied Clients (Dark Green Section with Background Image) -->
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
                        <span class="text-[#34d399]">Our</span> 
                        <span class="text-white ml-2">Satisfied Clients</span>
                    </h2>
                </div>

                <!-- 3 Testimonial Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 sm:gap-10">
                    
                    <!-- Card 1 -->
                    <div class="bg-white text-gray-900 rounded-[22px] border border-emerald-500/40 p-6 sm:p-8 shadow-xl hover:shadow-2xl transition-all duration-300 flex flex-col justify-between relative group overflow-hidden">
                        <div class="space-y-4 relative z-10">
                            <div class="flex items-center space-x-1 text-amber-400 text-lg justify-center">
                                ★★★★<span class="text-amber-300">☆</span>
                            </div>
                            <p class="text-gray-700 text-sm sm:text-base leading-relaxed text-center font-normal">
                                "I have to say, I'm really impressed by professionalism and efficiency of It-Investment Recoveries team. If you're looking for hassle-free recycling, I highly recommend them."
                            </p>
                        </div>

                        <div class="flex items-center justify-center space-x-4 pt-6 border-t border-gray-100 mt-6 relative z-10">
                            <img 
                                src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80" 
                                alt="David Jones" 
                                class="w-12 h-12 rounded-full object-cover shadow-sm border border-gray-200"
                            />
                            <span class="font-bold text-gray-900 text-base">David Jones</span>
                        </div>

                        <div class="absolute bottom-4 right-4 text-emerald-100/60 font-serif text-7xl font-black select-none pointer-events-none">
                            ”
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white text-gray-900 rounded-[22px] border border-emerald-500/40 p-6 sm:p-8 shadow-xl hover:shadow-2xl transition-all duration-300 flex flex-col justify-between relative group overflow-hidden">
                        <div class="space-y-4 relative z-10">
                            <div class="flex items-center space-x-1 text-amber-400 text-lg justify-center">
                                ★★★<span class="text-amber-300">☆☆</span>
                            </div>
                            <p class="text-gray-700 text-sm sm:text-base leading-relaxed text-center font-normal">
                                "I recently hired It-Investment Recoveries for computer recycling in Denver, and I couldn't be happier. Their team was punctual, professional, and took care of everything. Highly recommended!"
                            </p>
                        </div>

                        <div class="flex items-center justify-center space-x-4 pt-6 border-t border-gray-100 mt-6 relative z-10">
                            <img 
                                src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&auto=format&fit=crop&q=80" 
                                alt="David Martinelli" 
                                class="w-12 h-12 rounded-full object-cover shadow-sm border border-gray-200"
                            />
                            <span class="font-bold text-gray-900 text-base">David Martinelli</span>
                        </div>

                        <div class="absolute bottom-4 right-4 text-emerald-100/60 font-serif text-7xl font-black select-none pointer-events-none">
                            ”
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white text-gray-900 rounded-[22px] border border-emerald-500/40 p-6 sm:p-8 shadow-xl hover:shadow-2xl transition-all duration-300 flex flex-col justify-between relative group overflow-hidden">
                        <div class="space-y-4 relative z-10">
                            <div class="flex items-center space-x-1 text-amber-400 text-lg justify-center">
                                ★★★★★
                            </div>
                            <p class="text-gray-700 text-sm sm:text-base leading-relaxed text-center font-normal">
                                "Their commitment to data security and environmentally responsible practices is commendable. A reliable choice for anyone looking to recycle electronics."
                            </p>
                        </div>

                        <div class="flex items-center justify-center space-x-4 pt-6 border-t border-gray-100 mt-6 relative z-10">
                            <img 
                                src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&auto=format&fit=crop&q=80" 
                                alt="Mark Richardson" 
                                class="w-12 h-12 rounded-full object-cover shadow-sm border border-gray-200"
                            />
                            <span class="font-bold text-gray-900 text-base">Mark Richardson</span>
                        </div>

                        <div class="absolute bottom-4 right-4 text-emerald-100/60 font-serif text-7xl font-black select-none pointer-events-none">
                            ”
                        </div>
                    </div>

                </div>

                <!-- Carousel Dots Indicator -->
                <div class="flex flex-col items-center space-y-4 pt-4">
                    <div class="flex items-center space-x-2">
                        <button class="w-3.5 h-3.5 rounded-full bg-[#3b82f6] shadow-sm"></button>
                        <button class="w-2.5 h-2.5 rounded-full bg-emerald-800 hover:bg-emerald-700 transition"></button>
                        <button class="w-2.5 h-2.5 rounded-full bg-emerald-800 hover:bg-emerald-700 transition"></button>
                        <button class="w-2.5 h-2.5 rounded-full bg-emerald-800 hover:bg-emerald-700 transition"></button>
                        <button class="w-2.5 h-2.5 rounded-full bg-emerald-800 hover:bg-emerald-700 transition"></button>
                    </div>
                </div>

            </div>
        </section>

        <!-- Small Green Recycling Icon Divider -->
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

        <!-- Section 7: Get a Free Denver E-Waste Recycling Quote -->
        <section class="bg-white py-12 sm:py-16 px-4 sm:px-8 w-full">
            <div class="w-full max-w-5xl mx-auto text-center space-y-5">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight">
                    <span class="text-[#34d399]">Get a Free</span> 
                    <span class="text-gray-900 ml-2">Denver E-Waste Recycling Quote</span>
                </h2>
                <p class="text-gray-700 text-base sm:text-lg max-w-3xl mx-auto font-medium leading-relaxed">
                    Contact us today to learn more about customizing an e-waste recycling program for your company's needs and goals. We provide transparent pricing and tailor solutions to match your sustainability objectives, data security protocols, and budget.
                </p>
                <div class="pt-3">
                    <a 
                        href="{{ url('/contact-us') }}" 
                        class="inline-flex items-center gap-2.5 px-9 py-4 rounded-full bg-[#10b981] hover:bg-[#059669] text-white font-extrabold text-lg shadow-xl transition duration-300 transform hover:scale-105"
                    >
                        <span>Book A Pickup</span>
                        <span class="text-xl">→</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- Section 8: Talk with an Expert Ratings Banner -->
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
