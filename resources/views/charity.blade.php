<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="w-full overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Donate Used Computers &amp; Electronics in Denver, CO | IT Recoveries</title>
        <meta name="description" content="Donate used computers and electronics in Denver to support schools, nonprofits, and low-income families by responsibly donating laptops, PCs, phones, and more.">

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
                        },
                        animation: {
                            'partner-marquee': 'marquee 30s linear infinite',
                        },
                        keyframes: {
                            marquee: {
                                '0%': { transform: 'translateX(0%)' },
                                '100%': { transform: 'translateX(-50%)' },
                            }
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

        <!-- Fixed Bottom-Left Badge -->
        <x-events-badge />

        <!-- Section 1: Hero Banner -->
        <section class="relative min-h-[70vh] sm:min-h-[80vh] lg:min-h-[88vh] w-full flex flex-col justify-between overflow-hidden bg-transparent">
            <!-- Header Overlay -->
            <x-header active="services" />

            <!-- Background Image -->
            <div class="absolute inset-0 z-0 overflow-hidden w-full h-full">
                <img 
                    src="{{ asset('images/charity/ChatGPT-Image-Jun-20-2026-01_38_35-AM.webp') }}" 
                    alt="Donate Used Computers &amp; Electronics in Denver, CO" 
                    class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000 ease-out"
                />
                
            </div>

            <!-- Hero Center Content -->
            <div class="relative z-20 flex-grow flex flex-col items-center justify-center text-center px-4 sm:px-8 lg:px-12 pt-36 sm:pt-48 md:pt-52 pb-20 sm:pb-28 w-full">
                <div class="w-full max-w-5xl mx-auto space-y-6">
                    
                    <!-- Title -->
                    <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-[76px] font-bold text-white tracking-tight leading-[1.15] font-['Albert_Sans',sans-serif] drop-shadow-[0_4px_25px_rgba(0,0,0,0.85)] max-w-4xl mx-auto">
                        Charity / Donation
                    </h1>

                    <!-- CTA Button -->
                    <div class="pt-4">
                        <a 
                            href="{{ url('/contact-us') }}" 
                            class="inline-flex items-center gap-2.5 px-10 py-3.5 rounded-full bg-[#035c43] hover:bg-[#024734] text-white font-extrabold text-base sm:text-lg shadow-2xl transition duration-300 transform hover:scale-105"
                        >
                            <span>Schedule A Donation Pickup</span>
                            <span class="text-xl">→</span>
                        </a>
                    </div>

                </div>
            </div>
        </section>

        <!-- Section 2: Donate Used Computers & Electronics in Denver, CO -->
        <section class="bg-white text-gray-900 pt-16 sm:pt-24 pb-12 px-4 sm:px-8 lg:px-16 w-full">
            <div class="w-full max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">
                
                <!-- Left Side: Text -->
                <div class="lg:col-span-7 space-y-6 text-left">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
                        <span class="text-[#035c43]">Donate Used Computers</span><br />
                        <span class="text-[#035c43]">&amp; Electronics</span>
                        <span class="text-gray-900 ml-2">in Denver, CO</span>
                    </h2>
                    <p class="text-gray-700 text-lg sm:text-xl md:text-2xl font-medium leading-relaxed max-w-2xl">
                        Donate used computers and electronics in Denver to Support schools, nonprofits, and low-income families by responsibly donating laptops, PCs, phones, and more. Make a difference in our community.
                    </p>
                </div>

                <!-- Right Side: Laptop Donation Graphic -->
                <div class="lg:col-span-5 flex items-center justify-center">
                    <img 
                        src="{{ asset('images/charity/equipment/image-removebg-preview-10.webp') }}" 
                        alt="Donate Used Computers &amp; Electronics in Denver, CO" 
                        class="w-full max-w-md h-auto object-contain filter drop-shadow-xl transform hover:scale-105 transition duration-500"
                    />
                </div>

            </div>
        </section>

        <!-- Slanted Full-Width Dark Green Band -->
        <div class="bg-[#035c43] transform -skew-y-1 py-5 w-full shadow-md overflow-hidden"></div>

        <!-- 5 Charity Logos Row (On White Background) -->
        <section class="bg-white py-8 px-4 w-full border-b border-gray-100">
            <div class="w-full max-w-6xl mx-auto flex flex-wrap items-center justify-center gap-8 sm:gap-12 md:gap-16">
                <img src="{{ asset('images/charity/slider/image-removebg-preview-4.webp') }}" alt="American Red Cross" class="h-10 sm:h-14 object-contain filter drop-shadow-sm" />
                <img src="{{ asset('images/charity/slider/image-removebg-preview-11.webp') }}" alt="National Alliance to End Homelessness" class="h-10 sm:h-14 object-contain filter drop-shadow-sm" />
                <img src="{{ asset('images/charity/slider/image-removebg-preview-10-1.webp') }}" alt="MALALA FUND" class="h-10 sm:h-14 object-contain filter drop-shadow-sm" />
                <img src="{{ asset('images/charity/slider/image-removebg-preview-1-1.webp') }}" alt="Senior Assistance Center" class="h-10 sm:h-14 object-contain filter drop-shadow-sm" />
                <img src="{{ asset('images/charity/slider/image-removebg-preview-2-1.webp') }}" alt="Children's Hospital Colorado" class="h-10 sm:h-14 object-contain filter drop-shadow-sm" />
            </div>
        </section>

        <!-- Section 3: Used Computer Donation Services in Denver -->
        <section class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full border-t border-emerald-200">
            <div class="w-full max-w-5xl mx-auto text-center space-y-8">
                
                <!-- Title -->
                <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
                    <span class="text-gray-900">Used Computer Donation</span> 
                    <span class="text-[#035c43] ml-2">Services in Denver</span>
                </h2>

                <!-- Paragraph -->
                <p class="text-gray-700 text-lg sm:text-xl md:text-2xl font-medium leading-relaxed max-w-4xl mx-auto">
                    We specialize in computer donation in Denver, accepting donations of used computers, laptops, monitors, printers and other electronics from individuals and businesses. These donated computers and electronics are redistributed to schools, nonprofits, students or sold for parts, keeping e-waste out of landfills. If you have older models, broken devices with salvageable parts or a surplus of computers and electronics you no longer need, our Denver computer donation program welcomes your tax-deductible equipment and parts donations.
                </p>

                <!-- 8 Charity Logos Grid (2 Rows of 4 Logos) -->
                <div class="pt-8 max-w-5xl mx-auto">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 items-center justify-center">
                        
                        <!-- Logo 1: Senior Assistance Center -->
                        <div class="flex justify-center items-center p-2">
                            <img 
                                src="{{ asset('images/charity/slider/image-removebg-preview-1-1.webp') }}" 
                                alt="Senior Assistance Center" 
                                class="h-16 sm:h-20 max-w-full object-contain filter drop-shadow-sm hover:scale-105 transition duration-300"
                            />
                        </div>

                        <!-- Logo 2: Children's Hospital Colorado -->
                        <div class="flex justify-center items-center p-2">
                            <img 
                                src="{{ asset('images/charity/slider/image-removebg-preview-2-1.webp') }}" 
                                alt="Children's Hospital Colorado" 
                                class="h-16 sm:h-20 max-w-full object-contain filter drop-shadow-sm hover:scale-105 transition duration-300"
                            />
                        </div>

                        <!-- Logo 3: American Red Cross -->
                        <div class="flex justify-center items-center p-2">
                            <img 
                                src="{{ asset('images/charity/slider/image-removebg-preview-4.webp') }}" 
                                alt="American Red Cross" 
                                class="h-16 sm:h-20 max-w-full object-contain filter drop-shadow-sm hover:scale-105 transition duration-300"
                            />
                        </div>

                        <!-- Logo 4: unicef -->
                        <div class="flex justify-center items-center p-2">
                            <img 
                                src="{{ asset('images/charity/slider/image-removebg-preview-9-2.webp') }}" 
                                alt="unicef" 
                                class="h-16 sm:h-20 max-w-full object-contain filter drop-shadow-sm hover:scale-105 transition duration-300"
                            />
                        </div>

                        <!-- Logo 5: MALALA FUND -->
                        <div class="flex justify-center items-center p-2">
                            <img 
                                src="{{ asset('images/charity/slider/image-removebg-preview-10-1.webp') }}" 
                                alt="MALALA FUND" 
                                class="h-16 sm:h-20 max-w-full object-contain filter drop-shadow-sm hover:scale-105 transition duration-300"
                            />
                        </div>

                        <!-- Logo 6: National Alliance to End Homelessness -->
                        <div class="flex justify-center items-center p-2">
                            <img 
                                src="{{ asset('images/charity/slider/image-removebg-preview-11.webp') }}" 
                                alt="National Alliance to End Homelessness" 
                                class="h-16 sm:h-20 max-w-full object-contain filter drop-shadow-sm hover:scale-105 transition duration-300"
                            />
                        </div>

                        <!-- Logo 7: Disabled Veterans National Foundation -->
                        <div class="flex justify-center items-center p-2">
                            <img 
                                src="{{ asset('images/charity/slider/image-removebg-preview-12.webp') }}" 
                                alt="Disabled Veterans National Foundation" 
                                class="h-16 sm:h-20 max-w-full object-contain filter drop-shadow-sm hover:scale-105 transition duration-300"
                            />
                        </div>

                        <!-- Logo 8: American Cancer Society -->
                        <div class="flex justify-center items-center p-2">
                            <img 
                                src="{{ asset('images/charity/slider/cancer-removebg-preview.webp') }}" 
                                alt="American Cancer Society" 
                                class="h-16 sm:h-20 max-w-full object-contain filter drop-shadow-sm hover:scale-105 transition duration-300"
                            />
                        </div>

                    </div>
                </div>

            </div>
        </section>

        <!-- Section 4: 2 Slanted Service Cards (Dark Green Full-Width Banner) -->
        <section class="bg-white text-gray-900 py-6 sm:py-10 w-full overflow-hidden">
            <div class="bg-[#035c43] text-white transform -skew-y-2 py-16 sm:py-24 w-full overflow-hidden shadow-2xl">
                <div class="transform skew-y-2 w-full max-w-7xl mx-auto px-6 sm:px-12 grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-16">
                    
                    <!-- Card 1: Computer Donation Services -->
                    <div class="flex flex-col items-center text-center space-y-6 group">
                        <div class="w-full max-w-md h-64 sm:h-72 rounded-2xl border-4 border-white shadow-2xl overflow-hidden transform group-hover:scale-105 transition duration-500 bg-gray-900">
                            <img 
                                src="{{ asset('images/charity/image-VuY_O2B7Y-transformed.webp') }}" 
                                alt="Computer Donation Services" 
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <h3 class="text-3xl sm:text-4xl font-extrabold text-white pt-2">
                            Computer Donation Services
                        </h3>
                        <p class="text-emerald-100 text-lg sm:text-xl md:text-2xl font-medium leading-relaxed max-w-lg">
                            We make it simple for Denver businesses and individuals to donate working computers, laptops, and servers to local non-profits, schools, and families in need. All data is sanitized to DoD/NIST standards.
                        </p>
                    </div>

                    <!-- Card 2: Electronics Recycling for Charity -->
                    <div class="flex flex-col items-center text-center space-y-6 group">
                        <div class="w-full max-w-md h-64 sm:h-72 rounded-2xl border-4 border-white shadow-2xl overflow-hidden transform group-hover:scale-105 transition duration-500 bg-gray-900 p-4 flex items-center justify-center">
                            <img 
                                src="{{ asset('images/charity/equipment/Used_desktop_and_laptop_computers__2_-removebg-preview.webp') }}" 
                                alt="Electronics Recycling for Charity" 
                                class="w-full h-full object-contain filter drop-shadow-md"
                            />
                        </div>
                        <h3 class="text-3xl sm:text-4xl font-extrabold text-white pt-2">
                            Electronics Recycling for Charity
                        </h3>
                        <p class="text-emerald-100 text-lg sm:text-xl md:text-2xl font-medium leading-relaxed max-w-lg">
                            We accept networking gear, monitors, printers, gaming consoles, and mobile devices. Non-working components are recycled with zero-landfill guarantee to fund local community programs.
                        </p>
                    </div>

                </div>
            </div>
        </section>

        <!-- Section 6: Donation of Equipment We Accept (7 Equipment Image Cards) -->
        <section class="bg-white text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100">
            <div class="w-full max-w-7xl mx-auto space-y-14">
                
                <!-- Title -->
                <div class="text-center">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
                        <span class="text-[#035c43]">Donation of Equipment</span> 
                        <span class="text-gray-900 ml-2">We Accept</span>
                    </h2>
                </div>

                <!-- 7 Equipment Items Grid -->
                <div class="space-y-12">
                    <!-- Top Row: 4 Items -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-10">
                        
                        <!-- Item 1 -->
                        <div class="flex flex-col items-center text-center space-y-4 group">
                            <div class="h-44 sm:h-48 w-full flex items-center justify-center p-2">
                                <img 
                                    src="{{ asset('images/charity/equipment/Used_desktop_and_laptop_computers__2_-removebg-preview.webp') }}" 
                                    alt="Used desktop and laptop computers" 
                                    class="max-h-full max-w-full object-contain filter drop-shadow-md group-hover:scale-105 transition duration-300"
                                />
                            </div>
                            <h3 class="text-base sm:text-lg font-bold text-[#035c43] max-w-xs leading-snug">
                                Used desktop and laptop computers
                            </h3>
                        </div>

                        <!-- Item 2 -->
                        <div class="flex flex-col items-center text-center space-y-4 group">
                            <div class="h-44 sm:h-48 w-full flex items-center justify-center p-2">
                                <img 
                                    src="{{ asset('images/charity/equipment/Untitled-design-1.webp') }}" 
                                    alt="LED/LCD monitors and displays" 
                                    class="max-h-full max-w-full object-contain filter drop-shadow-md group-hover:scale-105 transition duration-300"
                                />
                            </div>
                            <h3 class="text-base sm:text-lg font-bold text-[#035c43] max-w-xs leading-snug">
                                LED/LCD monitors and displays
                            </h3>
                        </div>

                        <!-- Item 3 -->
                        <div class="flex flex-col items-center text-center space-y-4 group">
                            <div class="h-44 sm:h-48 w-full flex items-center justify-center p-2">
                                <img 
                                    src="{{ asset('images/charity/equipment/Networking_equipment_like_routers_and_switches-removebg-preview.webp') }}" 
                                    alt="Networking equipments like routers and switches" 
                                    class="max-h-full max-w-full object-contain filter drop-shadow-md group-hover:scale-105 transition duration-300"
                                />
                            </div>
                            <h3 class="text-base sm:text-lg font-bold text-[#035c43] max-w-xs leading-snug">
                                Networking equipments like routers and switches
                            </h3>
                        </div>

                        <!-- Item 4 -->
                        <div class="flex flex-col items-center text-center space-y-4 group">
                            <div class="h-44 sm:h-48 w-full flex items-center justify-center p-2">
                                <img 
                                    src="{{ asset('images/charity/equipment/Printers__scanners__fax_machines-removebg-preview.webp') }}" 
                                    alt="Printers, scanners, fax machines" 
                                    class="max-h-full max-w-full object-contain filter drop-shadow-md group-hover:scale-105 transition duration-300"
                                />
                            </div>
                            <h3 class="text-base sm:text-lg font-bold text-[#035c43] max-w-xs leading-snug">
                                Printers, scanners, fax machines
                            </h3>
                        </div>

                    </div>

                    <!-- Bottom Row: 3 Items Centered -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 md:gap-10 max-w-5xl mx-auto">
                        
                        <!-- Item 5 -->
                        <div class="flex flex-col items-center text-center space-y-4 group">
                            <div class="h-44 sm:h-48 w-full flex items-center justify-center p-2">
                                <img 
                                    src="{{ asset('images/charity/equipment/Gaming_consoles_and_controllers-removebg-preview.webp') }}" 
                                    alt="Gaming consoles and controllers" 
                                    class="max-h-full max-w-full object-contain filter drop-shadow-md group-hover:scale-105 transition duration-300"
                                />
                            </div>
                            <h3 class="text-base sm:text-lg font-bold text-[#035c43] max-w-xs leading-snug">
                                Gaming consoles and controllers
                            </h3>
                        </div>

                        <!-- Item 6 -->
                        <div class="flex flex-col items-center text-center space-y-4 group">
                            <div class="h-44 sm:h-48 w-full flex items-center justify-center p-2">
                                <img 
                                    src="{{ asset('images/charity/equipment/Mobile_devices-removebg-preview.webp') }}" 
                                    alt="Mobile devices" 
                                    class="max-h-full max-w-full object-contain filter drop-shadow-md group-hover:scale-105 transition duration-300"
                                />
                            </div>
                            <h3 class="text-base sm:text-lg font-bold text-[#035c43] max-w-xs leading-snug">
                                Mobile devices
                            </h3>
                        </div>

                        <!-- Item 7 -->
                        <div class="flex flex-col items-center text-center space-y-4 group">
                            <div class="h-44 sm:h-48 w-full flex items-center justify-center p-2">
                                <img 
                                    src="{{ asset('images/IT Disposal/image-removebg-preview-3.webp') }}" 
                                    alt="Other electronics, functioning or broken" 
                                    class="max-h-full max-w-full object-contain filter drop-shadow-md group-hover:scale-105 transition duration-300"
                                />
                            </div>
                            <h3 class="text-base sm:text-lg font-bold text-[#035c43] max-w-xs leading-snug">
                                Other electronics, functioning or broken
                            </h3>
                        </div>

                    </div>
                </div>

            </div>
        </section>

        <!-- Section 5: Our Happy Clients Section (Matching Home Page) -->
        <x-testimonials />

        <!-- Small Green Recycling Icon Divider -->
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

        <!-- Section 7: Donate Used Computers in Denver -->
        <section class="bg-white text-gray-900 py-12 sm:py-16 px-4 sm:px-8 w-full">
            <div class="w-full max-w-5xl mx-auto text-center space-y-5">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight">
                    <span class="text-gray-900">Donate Used Computers</span> 
                    <span class="text-[#035c43] ml-2">in Denver</span>
                </h2>
                <p class="text-gray-700 text-lg sm:text-xl md:text-2xl max-w-4xl mx-auto font-medium leading-relaxed">
                    We make electronics donations easy. Our team handles all the packaging and transportation logistics at absolutely no cost to you. Call now or schedule a pickup to get started today and coordinate with our electronics donation pickup specialists. We look forward to putting your used equipment to good use!
                </p>
            </div>
        </section>

        <!-- Section 8: Talk with an Expert Ratings Banner -->
        <section class="relative bg-[#035c43] text-white py-16 sm:py-24 px-6 sm:px-12 lg:px-16 w-full shadow-inner overflow-hidden border-t border-emerald-800">
            
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
                    <p class="text-emerald-100 text-lg sm:text-xl md:text-2xl max-w-xl font-normal leading-relaxed">
                        Everyone Can Help Create a Full Circle. Let's Recycle Together. Call or email us to talk with an expert.
                    </p>
                    <div class="flex items-center gap-4 flex-wrap pt-2">
                        <a 
                            href="{{ url('/contact-us') }}" 
                            class="bg-[#035c43] hover:bg-white text-white hover:text-[#035c43] border border-emerald-400/30 px-7 py-3 rounded-full font-extrabold text-sm sm:text-base shadow-lg transition duration-300"
                        >
                            Make An Impact
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
