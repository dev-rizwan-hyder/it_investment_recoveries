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

            @keyframes certifiedTrack {
                0% { transform: translateX(0); }
                100% { transform: translateX(-50%); }
            }

            .certified-provider-marquee {
                display: flex;
                width: max-content;
                animation: certifiedTrack 36s linear infinite;
                will-change: transform;
            }

            .certified-provider-marquee:hover {
                animation-play-state: paused;
            }

            .certified-logo-card {
                position: relative;
                isolation: isolate;
                overflow: hidden;
            }

            .certified-logo-card::after {
                content: "";
                position: absolute;
                inset: -120% -55%;
                background: linear-gradient(90deg, transparent, rgba(3, 92, 67, .08), rgba(255, 255, 255, .8), transparent);
                animation: certifiedSweep 5.6s ease-in-out infinite;
                z-index: 0;
            }

            .certified-logo-card img {
                position: relative;
                z-index: 1;
            }

            .certified-logo-card span {
                z-index: 1;
            }

            @keyframes certifiedSweep {
                0%, 55% { transform: translateX(-120%) rotate(16deg); opacity: 0; }
                68% { opacity: 1; }
                100% { transform: translateX(120%) rotate(16deg); opacity: 0; }
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
        <section class="relative min-h-[85vh] sm:min-h-[90vh] lg:min-h-screen w-full flex flex-col justify-between overflow-hidden bg-transparent">
            <!-- Header Overlay -->
            <x-header active="services" />

            <!-- Background Image -->
            <div class="absolute inset-0 z-0 overflow-hidden w-full h-full">
                <img 
                    src="{{ asset('images/charity/charity-hero.webp') }}" 
                    alt="Donate Used Computers &amp; Electronics in Denver, CO" 
                    class="w-full h-full object-cover object-center transform scale-105 transition-transform duration-1000 ease-out brightness-95 contrast-105"
                />
                <div class="absolute inset-0 bg-gradient-to-b from-slate-950/75 via-[#01281d]/30 to-slate-950/85"></div>
            </div>

            <!-- Hero Center Content -->
            <div class="relative z-20 flex-grow flex flex-col items-center justify-center text-center px-4 sm:px-8 lg:px-12 pt-48 sm:pt-60 md:pt-64 pb-32 sm:pb-40 w-full">
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
        <section class="bg-gradient-to-b from-slate-50 via-white to-slate-50/80 text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full relative overflow-hidden border-b border-gray-100">
            <!-- Background Ambient Glow Accents -->
            <div class="absolute top-1/2 left-1/4 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[350px] bg-emerald-400/10 blur-[130px] pointer-events-none rounded-full"></div>
            <div class="absolute top-1/2 right-1/4 translate-x-1/2 -translate-y-1/2 w-[500px] h-[300px] bg-teal-400/10 blur-[120px] pointer-events-none rounded-full"></div>

            <div class="w-full max-w-7xl mx-auto relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                    
                    <!-- Left Column: Title & Verbatim Original Content Only -->
                    <div class="lg:col-span-7 space-y-6 text-left">
                        
                        <!-- Main Title (Exact Original Wording) -->
                        <h2 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-gray-900 leading-[1.15] font-['Albert_Sans',sans-serif]">
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#035c43] via-emerald-700 to-[#035c43]">Donate Used Computers</span><br />
                            <span class="text-[#035c43]">&amp; Electronics</span>
                            <span class="text-gray-900 ml-2">in Denver, CO</span>
                        </h2>

                        <!-- Decorative Accent Bar (No text) -->
                        <div class="w-20 h-1.5 bg-gradient-to-r from-[#035c43] to-emerald-400 rounded-full"></div>

                        <!-- Content Cards (Strictly Verbatim Original Content - No Additions) -->
                        <div class="space-y-5 pt-2">
                            
                            <!-- Paragraph Lead with Left Accent Border -->
                            <div class="relative pl-5 sm:pl-6 border-l-4 border-[#035c43]">
                                <p class="text-gray-800 text-lg sm:text-xl font-semibold leading-relaxed">
                                    Donate used computers and electronics in Denver to Support schools, nonprofits, and low-income families by responsibly donating laptops, PCs, phones, and more.
                                </p>
                            </div>

                            <!-- Community Impact Highlight Box -->
                            <div class="bg-gradient-to-r from-emerald-50/90 via-slate-50/80 to-emerald-50/40 rounded-2xl p-6 sm:p-7 border border-emerald-200/80 shadow-sm flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-[#035c43] text-white flex items-center justify-center shrink-0 shadow-md mt-0.5">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-900 text-base sm:text-lg font-bold leading-relaxed">
                                    Make a difference in our community.
                                </p>
                            </div>

                        </div>

                    </div>

                    <!-- Right Column: Professional Charity Hardware Showcase (Real-Life Photo) -->
                    <div class="lg:col-span-5 relative">
                        <div class="relative rounded-3xl overflow-hidden border border-gray-200 shadow-2xl group bg-gray-50">
                            <img 
                                src="{{ asset('images/charity/donate-computers-denver.webp') }}" 
                                alt="Donate Used Computers &amp; Electronics in Denver, CO" 
                                class="w-full h-[400px] sm:h-[480px] object-cover object-center transform group-hover:scale-105 transition-transform duration-700 ease-out"
                            />
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Section 3: Used Computer Donation Services in Denver & Charity Partners Slider -->
        <section class="bg-gradient-to-b from-slate-50/70 via-white to-slate-50/50 text-gray-900 py-20 sm:py-28 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100 relative overflow-hidden">
            <!-- Subtle Ambient Background Accents -->
            <div class="absolute top-1/3 left-10 w-96 h-96 bg-emerald-400/5 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-10 right-10 w-96 h-96 bg-teal-400/5 rounded-full blur-3xl pointer-events-none"></div>

            <div class="w-full max-w-7xl mx-auto text-center space-y-12 sm:space-y-14 relative z-10">
                
                <!-- Title & Lead Description -->
                <div class="max-w-7xl mx-auto space-y-5">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-gray-900 leading-tight font-['Albert_Sans',sans-serif]">
                        <span class="text-gray-900">Used Computer Donation</span> 
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#035c43] via-emerald-700 to-[#035c43] block sm:inline sm:ml-2">Services in Denver</span>
                    </h2>
                    <div class="w-20 h-1.5 bg-gradient-to-r from-[#035c43] to-emerald-400 rounded-full mx-auto"></div>

                    <!-- Verbatim Original Paragraph in Executive Card -->
                    <div class="bg-gradient-to-br from-emerald-50/60 via-white to-teal-50/40 rounded-3xl p-7 sm:p-10 border border-emerald-100/90 shadow-sm mt-6">
                        <p class="text-gray-700 text-lg sm:text-xl md:text-2xl font-medium leading-relaxed">
                            We specialize in computer donation in Denver, accepting donations of used computers, laptops, monitors, printers and other electronics from individuals and businesses. These donated computers and electronics are redistributed to schools, nonprofits, students or sold for parts, keeping e-waste out of landfills. If you have older models, broken devices with salvageable parts or a surplus of computers and electronics you no longer need, our Denver computer donation program welcomes your tax-deductible equipment and parts donations.
                        </p>
                    </div>
                </div>

                <!-- Continuous Marquee Slider (Matching Home Page Slider) -->
                <div class="relative mx-auto w-full overflow-hidden py-6 sm:py-8">
                    <!-- Left & Right Gradient Fade Edges -->
                    <div class="absolute left-0 top-0 bottom-0 w-16 sm:w-28 lg:w-40 bg-gradient-to-r from-white via-white/95 to-transparent z-20 pointer-events-none"></div>
                    <div class="absolute right-0 top-0 bottom-0 w-16 sm:w-28 lg:w-40 bg-gradient-to-l from-white via-white/95 to-transparent z-20 pointer-events-none"></div>
                    <div class="absolute left-[8%] right-[8%] top-1/2 h-px bg-gradient-to-r from-transparent via-[#035c43]/20 to-transparent pointer-events-none"></div>

                    @php
                        $charityLogos = [
                            ['file' => 'image-removebg-preview-9-2.webp', 'name' => 'UNICEF'],
                            ['file' => 'image-removebg-preview-4.webp', 'name' => 'American Red Cross'],
                            ['file' => 'image-removebg-preview-10-1.webp', 'name' => 'MALALA FUND'],
                            ['file' => 'image-removebg-preview-11.webp', 'name' => 'American Addiction Centers'],
                            ['file' => 'image-removebg-preview-12.webp', 'name' => 'Disabled Veterans National Foundation (DVNF)'],
                            ['file' => 'image-removebg-preview-2-1.webp', 'name' => "Children's Hospital Colorado"],
                            ['file' => 'image-removebg-preview-1-1.webp', 'name' => 'Senior Assistance Center'],
                            ['file' => 'cancer-removebg-preview.webp', 'name' => 'American Cancer Society'],
                        ];
                    @endphp

                    <div class="certified-provider-marquee items-center">
                        @foreach([1, 2] as $trackCopy)
                            <div class="flex items-center gap-8 pr-8 sm:gap-12 sm:pr-12 lg:gap-16 lg:pr-16">
                                @foreach($charityLogos as $logo)
                                    <div class="certified-logo-card group flex h-[130px] w-[210px] shrink-0 items-center justify-center border border-gray-200/80 bg-white p-6 shadow-[0_10px_30px_rgba(0,0,0,0.06)] transition-all duration-300 hover:-translate-y-1.5 hover:border-[#035c43]/45 hover:shadow-[0_20px_45px_rgba(3,92,67,0.18)] rounded-2xl">
                                        <span class="absolute inset-x-5 top-0 h-px bg-gradient-to-r from-transparent via-[#035c43]/55 to-transparent"></span>
                                        <span class="absolute bottom-3 left-1/2 h-2 w-[70%] -translate-x-1/2 rounded-full bg-[#111827]/5 blur-sm"></span>
                                        <img 
                                            src="{{ asset('images/charity/slider/' . $logo['file']) }}" 
                                            alt="{{ $logo['name'] }}" 
                                            class="max-h-full max-w-full object-contain filter drop-shadow-sm transition-transform duration-300 group-hover:scale-105" 
                                        />
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </section>

        <!-- Section 4: Redesigned Donation & Recycling Pillars Section -->
        <section class="relative isolate bg-[#035c43] text-white py-20 sm:py-28 px-4 sm:px-8 lg:px-16 w-full overflow-hidden shadow-2xl">
            <!-- Background Circuit Grid & Lighting Accents -->
            <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
                <div class="absolute inset-0 opacity-[0.06]" style="background-image: linear-gradient(rgba(255,255,255,.22) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.22) 1px, transparent 1px); background-size: 44px 44px;"></div>
                <div class="absolute top-0 left-1/4 -translate-x-1/2 w-[550px] h-[350px] bg-emerald-400/10 blur-[130px] rounded-full"></div>
                <div class="absolute bottom-0 right-1/4 translate-x-1/2 w-[500px] h-[300px] bg-teal-300/10 blur-[120px] rounded-full"></div>
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/35 to-transparent"></div>
                <div class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
            </div>

            <div class="relative z-10 w-full max-w-7xl mx-auto space-y-14 sm:space-y-16">

                <!-- 2 Modern Bento Cards -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-stretch">
                    
                    <!-- Card 1: Computer Donation Services -->
                    <div class="group relative rounded-3xl bg-white/[0.08] hover:bg-white/[0.12] border border-white/20 hover:border-emerald-300/50 p-6 sm:p-10 backdrop-blur-md shadow-[0_20px_60px_rgba(0,0,0,0.3)] hover:shadow-[0_28px_70px_rgba(0,0,0,0.4)] hover:-translate-y-2 transition-all duration-500 flex flex-col justify-between overflow-hidden">
                        <!-- Top Accent Glow Line -->
                        <span class="absolute inset-x-8 top-0 h-px bg-gradient-to-r from-transparent via-emerald-300/60 to-transparent pointer-events-none"></span>
                        
                        <!-- Ambient Hover Glow -->
                        <div class="absolute -right-16 -bottom-16 w-56 h-56 bg-emerald-400/15 rounded-full blur-3xl pointer-events-none group-hover:scale-125 transition-transform duration-700"></div>

                        <div class="space-y-6">
                            <!-- Visual Showcase Box -->
                            <div class="relative w-full h-64 sm:h-72 rounded-2xl overflow-hidden bg-gradient-to-b from-slate-950/70 to-slate-900/90 border border-white/15 p-4 flex items-center justify-center shadow-inner group-hover:border-emerald-400/35 transition-all duration-500">
                                <img 
                                    src="{{ asset('images/charity/image-VuY_O2B7Y-transformed.webp') }}" 
                                    alt="Computer Donation Services" 
                                    class="w-full h-full object-contain filter drop-shadow-[0_12px_24px_rgba(0,0,0,0.5)] transform group-hover:scale-105 transition-transform duration-700 ease-out"
                                />
                            </div>

                            <!-- Card Title -->
                            <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white tracking-tight pt-2">
                                Computer Donation Services
                            </h3>

                            <!-- Verbatim Description Paragraph -->
                            <p class="text-emerald-100 text-base sm:text-lg lg:text-xl font-normal leading-relaxed">
                                We make it simple for Denver businesses and individuals to donate working computers, laptops, and servers to local non-profits, schools, and families in need. All data is sanitized to DoD/NIST standards.
                            </p>

                            <!-- Key Highlights Checklist -->
                            <ul class="space-y-2.5 pt-2 text-sm sm:text-base text-emerald-100 font-medium">
                                <li class="flex items-center gap-2.5">
                                    <span class="w-5 h-5 rounded-full bg-emerald-400/20 text-emerald-300 flex items-center justify-center shrink-0 text-xs font-bold">✓</span>
                                    <span>Certified DoD 5220.22-M &amp; NIST 800-88 data sanitization</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <span class="w-5 h-5 rounded-full bg-emerald-400/20 text-emerald-300 flex items-center justify-center shrink-0 text-xs font-bold">✓</span>
                                    <span>Redistributed to Denver schools, charities &amp; low-income families</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <span class="w-5 h-5 rounded-full bg-emerald-400/20 text-emerald-300 flex items-center justify-center shrink-0 text-xs font-bold">✓</span>
                                    <span>Official tax-deductible donation receipts provided</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Card CTA Button -->
                        <div class="pt-8">
                            <a 
                                href="{{ url('/contact-us') }}" 
                                class="inline-flex items-center justify-center gap-2.5 w-full py-4 px-6 rounded-2xl bg-white hover:bg-emerald-50 text-[#035c43] font-black text-base sm:text-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform group-hover:scale-[1.01]"
                            >
                                <span>Donate Computers Today</span>
                                <span class="text-xl">→</span>
                            </a>
                        </div>
                    </div>

                    <!-- Card 2: Electronics Recycling for Charity -->
                    <div class="group relative rounded-3xl bg-white/[0.08] hover:bg-white/[0.12] border border-white/20 hover:border-emerald-300/50 p-6 sm:p-10 backdrop-blur-md shadow-[0_20px_60px_rgba(0,0,0,0.3)] hover:shadow-[0_28px_70px_rgba(0,0,0,0.4)] hover:-translate-y-2 transition-all duration-500 flex flex-col justify-between overflow-hidden">
                        <!-- Top Accent Glow Line -->
                        <span class="absolute inset-x-8 top-0 h-px bg-gradient-to-r from-transparent via-emerald-300/60 to-transparent pointer-events-none"></span>
                        
                        <!-- Ambient Hover Glow -->
                        <div class="absolute -right-16 -bottom-16 w-56 h-56 bg-emerald-400/15 rounded-full blur-3xl pointer-events-none group-hover:scale-125 transition-transform duration-700"></div>

                        <div class="space-y-6">
                            <!-- Visual Showcase Box -->
                            <div class="relative w-full h-64 sm:h-72 rounded-2xl overflow-hidden bg-gradient-to-b from-slate-950/70 to-slate-900/90 border border-white/15 p-4 flex items-center justify-center shadow-inner group-hover:border-emerald-400/35 transition-all duration-500">
                                <img 
                                    src="{{ asset('images/charity/equipment/Used_desktop_and_laptop_computers__2_-removebg-preview.webp') }}" 
                                    alt="Electronics Recycling for Charity" 
                                    class="w-full h-full object-contain filter drop-shadow-[0_12px_24px_rgba(0,0,0,0.5)] transform group-hover:scale-105 transition-transform duration-700 ease-out"
                                />
                            </div>

                            <!-- Card Title -->
                            <h3 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white tracking-tight pt-2">
                                Electronics Recycling for Charity
                            </h3>

                            <!-- Verbatim Description Paragraph -->
                            <p class="text-emerald-100 text-base sm:text-lg lg:text-xl font-normal leading-relaxed">
                                We accept networking gear, monitors, printers, gaming consoles, and mobile devices. Non-working components are recycled with zero-landfill guarantee to fund local community programs.
                            </p>

                            <!-- Key Highlights Checklist -->
                            <ul class="space-y-2.5 pt-2 text-sm sm:text-base text-emerald-100 font-medium">
                                <li class="flex items-center gap-2.5">
                                    <span class="w-5 h-5 rounded-full bg-emerald-400/20 text-emerald-300 flex items-center justify-center shrink-0 text-xs font-bold">✓</span>
                                    <span>Guaranteed 100% zero-landfill, eco-friendly recycling</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <span class="w-5 h-5 rounded-full bg-emerald-400/20 text-emerald-300 flex items-center justify-center shrink-0 text-xs font-bold">✓</span>
                                    <span>Accepts monitors, network gear, consoles, printers &amp; phones</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <span class="w-5 h-5 rounded-full bg-emerald-400/20 text-emerald-300 flex items-center justify-center shrink-0 text-xs font-bold">✓</span>
                                    <span>Proceeds directly fund local veteran &amp; community causes</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Card CTA Button -->
                        <div class="pt-8">
                            <a 
                                href="{{ url('/contact-us') }}" 
                                class="inline-flex items-center justify-center gap-2.5 w-full py-4 px-6 rounded-2xl bg-emerald-500/25 hover:bg-emerald-500/35 border border-emerald-300/40 text-white font-black text-base sm:text-lg shadow-xl hover:shadow-2xl transition-all duration-300 transform group-hover:scale-[1.01] backdrop-blur-md"
                            >
                                <span>Schedule Recycling Pickup</span>
                                <span class="text-xl">→</span>
                            </a>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- Section 6: Donation of Equipment We Accept (7 Equipment Image Cards) -->
        <section 
            class="relative w-full py-20 sm:py-28 px-4 sm:px-8 lg:px-16 overflow-hidden bg-fixed bg-cover bg-center bg-no-repeat text-gray-900 border-t border-b border-gray-200/80 shadow-inner"
            style="background-image: url('{{ asset('images/IT Disposal/ewaste-recycling-hero.webp') }}');"
        >
            <!-- Overlay for contrast, readability & smooth scrolling parallax depth -->
            <div class="absolute inset-0 bg-white/85 backdrop-blur-[1px] pointer-events-none"></div>

            <!-- Subtle Emerald Glow Highlights & Architectural Grid -->
            <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
                <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[900px] h-[400px] bg-emerald-400/20 blur-[140px] rounded-full"></div>
                <div class="absolute -bottom-32 left-10 w-[500px] h-[400px] bg-teal-400/15 blur-[130px] rounded-full"></div>
                <div class="absolute -bottom-32 right-10 w-[500px] h-[400px] bg-emerald-300/15 blur-[130px] rounded-full"></div>
                <!-- Architectural Grid Texture with Radial Mask -->
                <div class="absolute inset-0 opacity-[0.04]" style="background-image: linear-gradient(to right, #035c43 1px, transparent 1px), linear-gradient(to bottom, #035c43 1px, transparent 1px); background-size: 36px 36px; -webkit-mask-image: radial-gradient(ellipse 85% 75% at 50% 50%, #000 60%, transparent 100%); mask-image: radial-gradient(ellipse 85% 75% at 50% 50%, #000 60%, transparent 100%);"></div>
            </div>

            <div class="w-full max-w-7xl mx-auto space-y-12 sm:space-y-16 relative z-10">
                
                <!-- Title -->
                <div class="text-center space-y-4 max-w-4xl mx-auto">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight font-['Albert_Sans',sans-serif]">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#035c43] via-emerald-700 to-[#035c43]">Donation of Equipment</span> 
                        <span class="text-gray-900 ml-2">We Accept</span>
                    </h2>
                    <div class="w-24 h-1.5 bg-gradient-to-r from-[#035c43] to-emerald-400 rounded-full mx-auto shadow-sm"></div>
                </div>

                <!-- 7 Equipment Items Grid -->
                <div class="space-y-6 sm:space-y-8">
                    <!-- Top Row: 4 Items -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                        
                        <!-- Item 1 -->
                        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-200/80 shadow-sm hover:shadow-xl hover:border-emerald-300 hover:-translate-y-1.5 transition-all duration-300 flex flex-col items-center justify-between group relative overflow-hidden text-center min-h-[280px]">
                            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-[#035c43] to-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="w-full h-44 sm:h-48 rounded-2xl bg-gradient-to-b from-slate-50 to-emerald-50/30 p-4 border border-gray-100 group-hover:border-emerald-200/60 flex items-center justify-center transition-colors shadow-inner">
                                <img 
                                    src="{{ asset('images/charity/equipment/Used_desktop_and_laptop_computers__2_-removebg-preview.webp') }}" 
                                    alt="Used desktop and laptop computers" 
                                    class="max-h-full max-w-full object-contain filter drop-shadow-md group-hover:scale-105 transition-transform duration-500 ease-out"
                                />
                            </div>
                            <h3 class="text-base sm:text-lg font-extrabold text-[#035c43] group-hover:text-gray-950 transition-colors leading-snug pt-4">
                                Used desktop and laptop computers
                            </h3>
                        </div>

                        <!-- Item 2 -->
                        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-200/80 shadow-sm hover:shadow-xl hover:border-emerald-300 hover:-translate-y-1.5 transition-all duration-300 flex flex-col items-center justify-between group relative overflow-hidden text-center min-h-[280px]">
                            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-[#035c43] to-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="w-full h-44 sm:h-48 rounded-2xl bg-gradient-to-b from-slate-50 to-emerald-50/30 p-4 border border-gray-100 group-hover:border-emerald-200/60 flex items-center justify-center transition-colors shadow-inner">
                                <img 
                                    src="{{ asset('images/charity/equipment/Untitled-design-1.webp') }}" 
                                    alt="LED/LCD monitors and displays" 
                                    class="max-h-full max-w-full object-contain filter drop-shadow-md group-hover:scale-105 transition-transform duration-500 ease-out"
                                />
                            </div>
                            <h3 class="text-base sm:text-lg font-extrabold text-[#035c43] group-hover:text-gray-950 transition-colors leading-snug pt-4">
                                LED/LCD monitors and displays
                            </h3>
                        </div>

                        <!-- Item 3 -->
                        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-200/80 shadow-sm hover:shadow-xl hover:border-emerald-300 hover:-translate-y-1.5 transition-all duration-300 flex flex-col items-center justify-between group relative overflow-hidden text-center min-h-[280px]">
                            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-[#035c43] to-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="w-full h-44 sm:h-48 rounded-2xl bg-gradient-to-b from-slate-50 to-emerald-50/30 p-4 border border-gray-100 group-hover:border-emerald-200/60 flex items-center justify-center transition-colors shadow-inner">
                                <img 
                                    src="{{ asset('images/charity/equipment/Networking_equipment_like_routers_and_switches-removebg-preview.webp') }}" 
                                    alt="Networking equipments like routers and switches" 
                                    class="max-h-full max-w-full object-contain filter drop-shadow-md group-hover:scale-105 transition-transform duration-500 ease-out"
                                />
                            </div>
                            <h3 class="text-base sm:text-lg font-extrabold text-[#035c43] group-hover:text-gray-950 transition-colors leading-snug pt-4">
                                Networking equipments like routers and switches
                            </h3>
                        </div>

                        <!-- Item 4 -->
                        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-200/80 shadow-sm hover:shadow-xl hover:border-emerald-300 hover:-translate-y-1.5 transition-all duration-300 flex flex-col items-center justify-between group relative overflow-hidden text-center min-h-[280px]">
                            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-[#035c43] to-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="w-full h-44 sm:h-48 rounded-2xl bg-gradient-to-b from-slate-50 to-emerald-50/30 p-4 border border-gray-100 group-hover:border-emerald-200/60 flex items-center justify-center transition-colors shadow-inner">
                                <img 
                                    src="{{ asset('images/charity/equipment/Printers__scanners__fax_machines-removebg-preview.webp') }}" 
                                    alt="Printers, scanners, fax machines" 
                                    class="max-h-full max-w-full object-contain filter drop-shadow-md group-hover:scale-105 transition-transform duration-500 ease-out"
                                />
                            </div>
                            <h3 class="text-base sm:text-lg font-extrabold text-[#035c43] group-hover:text-gray-950 transition-colors leading-snug pt-4">
                                Printers, scanners, fax machines
                            </h3>
                        </div>

                    </div>

                    <!-- Bottom Row: 3 Items Centered -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 lg:gap-8 max-w-5xl mx-auto">
                        
                        <!-- Item 5 -->
                        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-200/80 shadow-sm hover:shadow-xl hover:border-emerald-300 hover:-translate-y-1.5 transition-all duration-300 flex flex-col items-center justify-between group relative overflow-hidden text-center min-h-[280px]">
                            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-[#035c43] to-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="w-full h-44 sm:h-48 rounded-2xl bg-gradient-to-b from-slate-50 to-emerald-50/30 p-4 border border-gray-100 group-hover:border-emerald-200/60 flex items-center justify-center transition-colors shadow-inner">
                                <img 
                                    src="{{ asset('images/charity/equipment/Gaming_consoles_and_controllers-removebg-preview.webp') }}" 
                                    alt="Gaming consoles and controllers" 
                                    class="max-h-full max-w-full object-contain filter drop-shadow-md group-hover:scale-105 transition-transform duration-500 ease-out"
                                />
                            </div>
                            <h3 class="text-base sm:text-lg font-extrabold text-[#035c43] group-hover:text-gray-950 transition-colors leading-snug pt-4">
                                Gaming consoles and controllers
                            </h3>
                        </div>

                        <!-- Item 6 -->
                        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-200/80 shadow-sm hover:shadow-xl hover:border-emerald-300 hover:-translate-y-1.5 transition-all duration-300 flex flex-col items-center justify-between group relative overflow-hidden text-center min-h-[280px]">
                            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-[#035c43] to-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="w-full h-44 sm:h-48 rounded-2xl bg-gradient-to-b from-slate-50 to-emerald-50/30 p-4 border border-gray-100 group-hover:border-emerald-200/60 flex items-center justify-center transition-colors shadow-inner">
                                <img 
                                    src="{{ asset('images/charity/equipment/Mobile_devices-removebg-preview.webp') }}" 
                                    alt="Mobile devices" 
                                    class="max-h-full max-w-full object-contain filter drop-shadow-md group-hover:scale-105 transition-transform duration-500 ease-out"
                                />
                            </div>
                            <h3 class="text-base sm:text-lg font-extrabold text-[#035c43] group-hover:text-gray-950 transition-colors leading-snug pt-4">
                                Mobile devices
                            </h3>
                        </div>

                        <!-- Item 7 -->
                        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-200/80 shadow-sm hover:shadow-xl hover:border-emerald-300 hover:-translate-y-1.5 transition-all duration-300 flex flex-col items-center justify-between group relative overflow-hidden text-center min-h-[280px]">
                            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-[#035c43] to-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="w-full h-44 sm:h-48 rounded-2xl bg-gradient-to-b from-slate-50 to-emerald-50/30 p-4 border border-gray-100 group-hover:border-emerald-200/60 flex items-center justify-center transition-colors shadow-inner">
                                <img 
                                    src="{{ asset('images/IT Disposal/image-removebg-preview-3.webp') }}" 
                                    alt="Other electronics, functioning or broken" 
                                    class="max-h-full max-w-full object-contain filter drop-shadow-md group-hover:scale-105 transition-transform duration-500 ease-out"
                                />
                            </div>
                            <h3 class="text-base sm:text-lg font-extrabold text-[#035c43] group-hover:text-gray-950 transition-colors leading-snug pt-4">
                                Other electronics, functioning or broken
                            </h3>
                        </div>

                    </div>
                </div>

            </div>
        </section>

        <!-- Section 5: Our Happy Clients Section (Matching Home Page) -->
        <x-testimonials />


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

        <!-- Section 7: Donate Used Computers in Denver (Full-Width Parallax Background) -->
        <section 
            class="relative w-full py-16 sm:py-20 px-4 sm:px-8 lg:px-16 overflow-hidden bg-fixed bg-cover bg-center bg-no-repeat text-gray-900 border-t border-b border-gray-200/80 shadow-inner"
            style="background-image: url('{{ asset('images/ewaste/fLATIRONS.webp') }}');"
        >
            <!-- Soft White Overlay for readability & smooth parallax depth -->
            <div class="absolute inset-0 bg-white/80 backdrop-blur-[1px] pointer-events-none"></div>

            <div class="relative z-10 w-full max-w-5xl mx-auto text-center space-y-5">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-gray-900 leading-tight font-['Albert_Sans',sans-serif] max-w-4xl mx-auto">
                    <span class="text-gray-900">Donate Used Computers</span> 
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#035c43] via-emerald-700 to-[#035c43] ml-2">in Denver</span>
                </h2>
                <div class="w-20 h-1.5 bg-gradient-to-r from-[#035c43] to-emerald-400 rounded-full mx-auto"></div>
                <p class="text-gray-800 text-lg sm:text-xl md:text-2xl max-w-4xl mx-auto font-medium leading-relaxed">
                    We make electronics donations easy. Our team handles all the packaging and transportation logistics at absolutely no cost to you. Call now or schedule a pickup to get started today and coordinate with our electronics donation pickup specialists. We look forward to putting your used equipment to good use!
                </p>
            </div>
        </section>

        <!-- Full Width Main Site Footer Component -->
        <x-footer />
    </body>
</html>
