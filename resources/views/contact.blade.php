<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="w-full overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Contact Us | Denver &amp; Toronto Electronics Recycling &amp; IT Asset Disposition | IT Investment Recoveries</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
        <meta name="description" content="Contact IT Investment Recoveries in Denver and Toronto for certified electronics recycling, IT asset disposition, data destruction, and equipment pickup inquiries.">

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

        <!-- Section 1: Hero Section (DENVER / TORONTO Split Image) -->
        <section class="relative min-h-[45vh] sm:min-h-[55vh] lg:min-h-[65vh] xl:min-h-[70vh] w-full flex flex-col justify-between overflow-hidden bg-transparent">
            <!-- Top Transparent Header Overlay -->
            <x-header active="contact" />

            <!-- Background Image (Hero Banner with Denver & Toronto diagonal split) -->
            <div class="absolute inset-0 z-0 overflow-hidden w-full h-full">
                <img 
                    src="{{ asset('images/contact/hero.webp') }}" 
                    alt="Denver and Toronto Electronics Recycling Headquarters" 
                    class="w-full h-full object-cover object-center transform scale-100 transition-transform duration-700"
                />
                
            </div>

            <!-- Spacer for flex-grow balance -->
            <div class="relative z-20 flex-grow pt-24 sm:pt-32"></div>
        </section>

        <!-- Section 2: Inquiry Forms Section (General Inquiry OR Pickup Inquiry) -->
        <section class="bg-white text-gray-900 py-12 sm:py-20 px-4 sm:px-8 lg:px-12 w-full">
            <div class="w-full max-w-7xl mx-auto">

                <!-- Two Forms Grid Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-6 items-start">

                    <!-- Left Column: Submit a General Inquiry -->
                    <div id="general-inquiry" class="lg:col-span-5 flex flex-col items-center scroll-mt-24">
                        
                        <!-- Heading Above Left Card -->
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-[#1f2937] tracking-tight mb-6 text-center">
                            Submit a General Inquiry
                        </h2>

                        <!-- Dark Green Outer Container Card Frame -->
                        <div class="w-full bg-[#035c43] p-3 sm:p-4 rounded-[28px] shadow-2xl">
                            <!-- Inner Light Gray Card Body -->
                            <div class="w-full bg-[#f4f5f7] rounded-[20px] p-6 sm:p-8 space-y-5">
                                <form action="{{ route('contact.submit') }}#general-inquiry" method="POST" class="space-y-5">
                                    @csrf
                                    <input type="hidden" name="form_type" value="general">

                                    <!-- Field 1: Name -->
                                    <div>
                                        <input 
                                            type="text" 
                                            name="name" 
                                            placeholder="Enter your name" 
                                            required 
                                            class="w-full bg-white text-gray-800 placeholder-gray-400 px-4 py-3.5 sm:py-4 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#035c43]/30 focus:border-[#035c43] transition text-sm sm:text-base font-medium shadow-sm"
                                        />
                                    </div>

                                    <!-- Field 2: Email -->
                                    <div>
                                        <input 
                                            type="email" 
                                            name="email" 
                                            placeholder="Enter your email" 
                                            required 
                                            class="w-full bg-white text-gray-800 placeholder-gray-400 px-4 py-3.5 sm:py-4 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#035c43]/30 focus:border-[#035c43] transition text-sm sm:text-base font-medium shadow-sm"
                                        />
                                    </div>

                                    <!-- Field 3: Phone -->
                                    <div>
                                        <input 
                                            type="tel" 
                                            name="phone" 
                                            placeholder="Enter your phone number" 
                                            class="w-full bg-white text-gray-800 placeholder-gray-400 px-4 py-3.5 sm:py-4 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#035c43]/30 focus:border-[#035c43] transition text-sm sm:text-base font-medium shadow-sm"
                                        />
                                    </div>

                                    <!-- Field 4: Message Textarea -->
                                    <div>
                                        <textarea 
                                            name="message" 
                                            rows="5" 
                                            placeholder="Type your message here" 
                                            required 
                                            class="w-full bg-white text-gray-800 placeholder-gray-400 px-4 py-3.5 sm:py-4 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#035c43]/30 focus:border-[#035c43] transition text-sm sm:text-base font-medium shadow-sm resize-y"
                                        ></textarea>
                                    </div>

                                    <!-- Submit Button: Send Inquiry -->
                                    <div class="pt-2">
                                        <button 
                                            type="submit" 
                                            class="bg-[#035c43] hover:bg-[#024734] active:bg-[#035c43] text-white font-bold text-base sm:text-lg px-8 py-3 rounded-full shadow-lg transition duration-200 transform hover:scale-[1.02] focus:outline-none"
                                        >
                                            Send Inquiry
                                        </button>
                                    </div>

                                    <!-- Success Message Below Button -->
                                    @if(session('success') && session('submitted_form', 'general') === 'general')
                                        <div class="mt-4 p-4 rounded-xl bg-emerald-100 border border-emerald-400 text-emerald-900 font-bold text-sm sm:text-base flex items-start gap-2.5 shadow-sm">
                                            <svg class="w-5 h-5 text-emerald-700 fill-current shrink-0 mt-0.5" viewBox="0 0 24 24">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                            </svg>
                                            <span>{{ session('success') }}</span>
                                        </div>
                                    @endif
                                </form>
                            </div>
                        </div>

                    </div>

                    <!-- Center Column: OR Divider -->
                    <div class="lg:col-span-2 flex items-center justify-center py-4 lg:py-0 self-center">
                        <span class="text-3xl sm:text-4xl font-extrabold text-[#374151] tracking-wider uppercase">
                            OR
                        </span>
                    </div>

                    <!-- Right Column: Submit a Pickup Inquiry -->
                    <div id="pickup-inquiry" class="lg:col-span-5 flex flex-col items-center scroll-mt-24">
                        
                        <!-- Dark Green Outer Container Card Frame -->
                        <div class="w-full bg-[#035c43] p-3 sm:p-4 rounded-[28px] shadow-2xl">
                            
                            <!-- Header Title inside Card Header Banner -->
                            <div class="py-3 px-4 text-center">
                                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white tracking-tight">
                                    Submit a Pickup Inquiry
                                </h2>
                            </div>

                            <!-- Inner Light Gray Card Body -->
                            <div class="w-full bg-[#f4f5f7] rounded-[20px] p-6 sm:p-8 space-y-4 text-left">
                                <form action="{{ route('contact.submit') }}#pickup-inquiry" method="POST" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="form_type" value="pickup">

                                    <!-- Field 1: Name -->
                                    <div class="space-y-1">
                                        <label class="block text-xs sm:text-sm font-bold text-gray-700">Name</label>
                                        <input 
                                            type="text" 
                                            name="name" 
                                            placeholder="Enter your name" 
                                            required 
                                            class="w-full bg-white text-gray-800 placeholder-gray-400 px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#035c43]/30 focus:border-[#035c43] transition text-sm font-medium shadow-sm"
                                        />
                                    </div>

                                    <!-- Field 2: Email -->
                                    <div class="space-y-1">
                                        <label class="block text-xs sm:text-sm font-bold text-gray-700">Email</label>
                                        <input 
                                            type="email" 
                                            name="email" 
                                            placeholder="Enter your email" 
                                            required 
                                            class="w-full bg-white text-gray-800 placeholder-gray-400 px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#035c43]/30 focus:border-[#035c43] transition text-sm font-medium shadow-sm"
                                        />
                                    </div>

                                    <!-- Field 3: Phone -->
                                    <div class="space-y-1">
                                        <label class="block text-xs sm:text-sm font-bold text-gray-700">Phone</label>
                                        <input 
                                            type="tel" 
                                            name="phone" 
                                            placeholder="Enter your phone number" 
                                            class="w-full bg-white text-gray-800 placeholder-gray-400 px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#035c43]/30 focus:border-[#035c43] transition text-sm font-medium shadow-sm"
                                        />
                                    </div>

                                    <!-- Field 4: Business Name -->
                                    <div class="space-y-1">
                                        <label class="block text-xs sm:text-sm font-bold text-gray-700">Business Name</label>
                                        <input 
                                            type="text" 
                                            name="business_name" 
                                            placeholder="Enter your Business name" 
                                            class="w-full bg-white text-gray-800 placeholder-gray-400 px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#035c43]/30 focus:border-[#035c43] transition text-sm font-medium shadow-sm"
                                        />
                                    </div>

                                    <!-- Field 5: What services are you looking for? -->
                                    <div class="space-y-1">
                                        <label class="block text-xs sm:text-sm font-bold text-gray-800">What services are you looking for?</label>
                                        <select 
                                            name="service_type" 
                                            class="w-full bg-white text-gray-600 px-4 py-3 rounded-xl border-2 border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition text-sm font-medium shadow-sm cursor-pointer"
                                        >
                                            <option value="">—Please choose an option—</option>
                                            <option value="Data Destruction">Data Destruction</option>
                                            <option value="Electronics Recycling">Electronics Recycling</option>
                                            <option value="IT Asset Disposition">IT Asset Disposition</option>
                                        </select>
                                    </div>

                                    <!-- Field 6: Do you require onsite media destruction? -->
                                    <div class="space-y-1 pt-1">
                                        <label class="block text-xs sm:text-sm font-bold text-gray-800">
                                            Do you require onsite media destruction? <span class="text-gray-500 font-normal text-[11px] sm:text-xs">(Hard drive/ memory array wiping)</span>
                                        </label>
                                        <div class="flex items-center gap-6 pt-1 text-xs sm:text-sm font-medium text-gray-800">
                                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="onsite_destruction" value="Yes" class="w-4 h-4 text-[#035c43] focus:ring-[#035c43]">
                                                <span>Yes</span>
                                            </label>
                                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="onsite_destruction" value="No" checked class="w-4 h-4 text-[#035c43] focus:ring-[#035c43]">
                                                <span>No</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Field 7: Electronics have storage media that need to be destroyed? -->
                                    <div class="space-y-1 pt-1">
                                        <label class="block text-xs sm:text-sm font-bold text-gray-800 leading-snug">
                                            Electronics have storage media that need to be destroyed?
                                        </label>
                                        <div class="space-y-1.5 pt-1 text-xs sm:text-sm font-medium text-gray-800">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="destruction_type" value="Certified Destruction" class="w-4 h-4 text-[#035c43] focus:ring-[#035c43]">
                                                <span>Certified Destruction</span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="destruction_type" value="Uncertified Destruction" class="w-4 h-4 text-[#035c43] focus:ring-[#035c43]">
                                                <span>Uncertified Destruction</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Field 8: Describe your IT Asset Disposition/ E-Waste Recycling -->
                                    <div class="space-y-1 pt-1">
                                        <label class="block text-xs sm:text-sm font-bold text-gray-800 leading-snug">
                                            Describe your IT Asset Disposition/ E-Waste Recycling
                                        </label>
                                        <textarea 
                                            name="description" 
                                            rows="4" 
                                            class="w-full bg-white text-gray-800 placeholder-gray-400 px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#035c43]/30 focus:border-[#035c43] transition text-sm font-medium shadow-sm resize-y"
                                        ></textarea>
                                    </div>

                                    <!-- Submit Button: Send Inquiry -->
                                    <div class="pt-2">
                                        <button 
                                            type="submit" 
                                            class="bg-[#035c43] hover:bg-[#024734] active:bg-[#035c43] text-white font-bold text-base sm:text-lg px-8 py-3 rounded-full shadow-lg transition duration-200 transform hover:scale-[1.02] focus:outline-none"
                                        >
                                            Send Inquiry
                                        </button>
                                    </div>

                                    <!-- Success Message Below Button -->
                                    @if(session('success') && session('submitted_form') === 'pickup')
                                        <div class="mt-4 p-4 rounded-xl bg-emerald-100 border border-emerald-400 text-emerald-900 font-bold text-sm sm:text-base flex items-start gap-2.5 shadow-sm">
                                            <svg class="w-5 h-5 text-emerald-700 fill-current shrink-0 mt-0.5" viewBox="0 0 24 24">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                            </svg>
                                            <span>{{ session('success') }}</span>
                                        </div>
                                    @endif
                                </form>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </section>

        <!-- Section 3: Get In Touch & Worldwide Headquarters (Slanted Deep Green Section) -->
        <div class="relative w-full bg-white overflow-hidden pt-6 pb-12 sm:pb-20">
            <!-- Slanted Outer Deep Green Container -->
            <section class="bg-[#035c43] py-16 sm:py-24 transform -skew-y-2 origin-top-left shadow-2xl w-full">
                <!-- Inner Content Container -->
                <div class="transform skew-y-2 w-full max-w-7xl mx-auto px-6 sm:px-12 lg:px-16 grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">

                    <!-- Left Column: Get In Touch -->
                    <div class="lg:col-span-6 space-y-6 text-left">
                        
                        <!-- Heading: Get In Touch -->
                        <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-normal leading-tight">
                            <span class="text-white">Get In</span> 
                            <span class="text-white ml-2">Touch</span>
                        </h2>

                        <!-- Paragraph Description -->
                        <p class="text-gray-100 text-sm sm:text-base leading-relaxed max-w-xl font-normal">
                            If you have any questions &amp; would like information on how to coordinate the electronics recycling, Liquidation or IT Donation schedule pick up, please submit the contact form &amp; we will respond within 24 to 48 hours thank you.
                        </p>

                        <!-- Phone Call Pill Button -->
                        <div class="pt-2">
                            <a 
                                href="tel:+13034724701" 
                                class="inline-flex items-center gap-3 bg-white text-[#035c43] hover:bg-emerald-50 px-6 py-3 rounded-full shadow-xl font-extrabold text-sm sm:text-base border border-emerald-200 transition duration-300 transform hover:scale-105"
                            >
                                <svg class="w-5 h-5 text-[#035c43] fill-current" viewBox="0 0 24 24">
                                    <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                                </svg>
                                <span>+1-303-472-4701</span>
                            </a>
                        </div>

                    </div>

                    <!-- Right Column: Worldwide Headquarters -->
                    <div class="lg:col-span-6 space-y-6 text-left">
                        
                        <!-- Heading: Worldwide Headquarters -->
                        <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-normal leading-tight">
                            <span class="text-white">Worldwide</span> 
                            <span class="text-white ml-2">Headquarters</span>
                        </h2>

                        <!-- Location Cards List -->
                        <div class="space-y-6 pt-2">
                            
                            <!-- Location 1: Denver (USA Flag) -->
                            <div class="flex items-center gap-4 text-white text-sm sm:text-base font-semibold">
                                <!-- US Flag SVG -->
                                <svg class="w-12 h-8 sm:w-14 sm:h-9 rounded shadow-md border border-white/30 shrink-0 overflow-hidden" viewBox="0 0 36 24">
                                    <rect width="36" height="24" fill="#b22234"/>
                                    <path d="M0 2.77h36M0 6.46h36M0 10.15h36M0 13.85h36M0 17.54h36M0 21.23h36" stroke="#ffffff" stroke-width="1.85"/>
                                    <rect width="14.4" height="12.92" fill="#3c3b6e"/>
                                    <g fill="#ffffff">
                                        <circle cx="1.2" cy="1.3" r="0.45"/><circle cx="3.6" cy="1.3" r="0.45"/><circle cx="6" cy="1.3" r="0.45"/><circle cx="8.4" cy="1.3" r="0.45"/><circle cx="10.8" cy="1.3" r="0.45"/><circle cx="13.2" cy="1.3" r="0.45"/>
                                        <circle cx="2.4" cy="2.6" r="0.45"/><circle cx="4.8" cy="2.6" r="0.45"/><circle cx="7.2" cy="2.6" r="0.45"/><circle cx="9.6" cy="2.6" r="0.45"/><circle cx="12" cy="2.6" r="0.45"/>
                                        <circle cx="1.2" cy="3.9" r="0.45"/><circle cx="3.6" cy="3.9" r="0.45"/><circle cx="6" cy="3.9" r="0.45"/><circle cx="8.4" cy="3.9" r="0.45"/><circle cx="10.8" cy="3.9" r="0.45"/><circle cx="13.2" cy="3.9" r="0.45"/>
                                        <circle cx="2.4" cy="5.2" r="0.45"/><circle cx="4.8" cy="5.2" r="0.45"/><circle cx="7.2" cy="5.2" r="0.45"/><circle cx="9.6" cy="5.2" r="0.45"/><circle cx="12" cy="5.2" r="0.45"/>
                                        <circle cx="1.2" cy="6.5" r="0.45"/><circle cx="3.6" cy="6.5" r="0.45"/><circle cx="6" cy="6.5" r="0.45"/><circle cx="8.4" cy="6.5" r="0.45"/><circle cx="10.8" cy="6.5" r="0.45"/><circle cx="13.2" cy="6.5" r="0.45"/>
                                        <circle cx="2.4" cy="7.8" r="0.45"/><circle cx="4.8" cy="7.8" r="0.45"/><circle cx="7.2" cy="7.8" r="0.45"/><circle cx="9.6" cy="7.8" r="0.45"/><circle cx="12" cy="7.8" r="0.45"/>
                                        <circle cx="1.2" cy="9.1" r="0.45"/><circle cx="3.6" cy="9.1" r="0.45"/><circle cx="6" cy="9.1" r="0.45"/><circle cx="8.4" cy="9.1" r="0.45"/><circle cx="10.8" cy="9.1" r="0.45"/><circle cx="13.2" cy="9.1" r="0.45"/>
                                        <circle cx="2.4" cy="10.4" r="0.45"/><circle cx="4.8" cy="10.4" r="0.45"/><circle cx="7.2" cy="10.4" r="0.45"/><circle cx="9.6" cy="10.4" r="0.45"/><circle cx="12" cy="10.4" r="0.45"/>
                                        <circle cx="1.2" cy="11.7" r="0.45"/><circle cx="3.6" cy="11.7" r="0.45"/><circle cx="6" cy="11.7" r="0.45"/><circle cx="8.4" cy="11.7" r="0.45"/><circle cx="10.8" cy="11.7" r="0.45"/><circle cx="13.2" cy="11.7" r="0.45"/>
                                    </g>
                                </svg>
                                <span>5765 W 52<sup>nd</sup> Ave Denver Co 80212 USA</span>
                            </div>

                            <!-- Location 2: Toronto (Canada Flag) -->
                            <div class="flex items-center gap-4 text-white text-sm sm:text-base font-semibold">
                                <!-- Canada Flag SVG -->
                                <svg class="w-12 h-8 sm:w-14 sm:h-9 rounded shadow-md border border-white/30 shrink-0 overflow-hidden" viewBox="0 0 36 24">
                                    <rect width="36" height="24" fill="#d80027"/>
                                    <rect x="9" width="18" height="24" fill="#ffffff"/>
                                    <path d="M18 4.2l.9 3.2 2.6-1.4-1 2.8 3.2.4-2.3 1.8 2.3 2.3-3.2-.4.4 3.2-2.8-1.8v2.7h-1v-2.7l-2.8 1.8.4-3.2-3.2.4 2.3-2.3-2.3-1.8 3.2-.4-1-2.8 2.6 1.4z" fill="#d80027"/>
                                </svg>
                                <span>100 Sheppard Avenue East Suite 800 Toronto ON Canada</span>
                            </div>

                        </div>

                    </div>

                </div>
            </section>
        </div>

        <!-- Full Width Main Site Footer Component -->
        <x-footer />
    </body>
</html>
