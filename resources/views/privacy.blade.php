<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="w-full overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Privacy Policy | IT Investment Recoveries</title>
        <link rel="icon" type="image/png" href="{{ asset('/images/recoveries.png') }}">
        <meta name="description" content="Privacy Policy of IT Investment Recoveries. Learn how we collect, use, disclose, and safeguard your information, including our eBay marketplace integrations and account deletion policy.">

        <!-- Google Fonts: Albert Sans & Amaranth -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Albert+Sans:ital,wght@0,100..900;1,100..900&family=Amaranth:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            emerald: {
                                950: '#023829',
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
    <body class="bg-[#f8faf9] text-gray-900 font-['Albert_Sans',sans-serif] antialiased selection:bg-[#035c43] selection:text-white w-full overflow-x-hidden m-0 p-0">

        <!-- Fixed Bottom-Left Badge -->
        <x-events-badge />

        <!-- Header Hero Banner -->
        <section class="relative min-h-[36vh] sm:min-h-[42vh] w-full flex flex-col justify-between overflow-hidden bg-[#035c43]">
            <!-- Top Navigation Header -->
            <x-header active="privacy" />

            <!-- Gradient Background Overlay -->
            <div class="absolute inset-0 z-0 bg-gradient-to-r from-[#01281d] via-[#035c43] to-[#024432] opacity-95"></div>
            <div class="absolute inset-0 z-0 opacity-10 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:20px_20px]"></div>

            <!-- Hero Center Title -->
            <div class="relative z-20 flex-grow flex flex-col items-center justify-center text-center px-4 sm:px-8 lg:px-12 pt-32 sm:pt-40 pb-12 sm:pb-16 w-full">
                <div class="w-full max-w-4xl mx-auto space-y-3 sm:space-y-4">
                    <span class="inline-flex items-center gap-1.5 py-1 px-3.5 rounded-full text-xs sm:text-sm font-semibold tracking-wider uppercase bg-white/10 text-emerald-200 border border-white/20 backdrop-blur-sm">
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd"/>
                        </svg>
                        Security & Compliance
                    </span>
                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight drop-shadow-md">
                        Privacy Policy
                    </h1>
                    <p class="text-emerald-100/90 text-sm sm:text-base max-w-2xl mx-auto font-normal">
                        Last Updated: August 2, 2026
                    </p>
                </div>
            </div>
        </section>

        <!-- Main Content Section -->
        <main class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
            <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 p-6 sm:p-10 lg:p-12 space-y-8">

                <!-- Welcome Note -->
                <div class="space-y-3">
                    <p class="text-gray-700 text-base sm:text-lg leading-relaxed">
                        Welcome to <strong>IT Investment Recoveries</strong>. We value your privacy and are committed to protecting your personal data. This Privacy Policy outlines how we collect, use, disclose, and safeguard your information when you visit our website or interact with our services, including our eBay integrations.
                    </p>
                </div>

                <hr class="border-gray-100">

                <!-- 1. Information We Collect -->
                <section class="space-y-4">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <span class="text-[#035c43]">1.</span> Information We Collect
                    </h2>
                    <p class="text-gray-700 text-sm sm:text-base leading-relaxed">
                        We may collect information about you in a variety of ways, including:
                    </p>
                    <ul class="space-y-3 text-gray-700 text-sm sm:text-base">
                        <li class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <strong class="text-gray-900 block mb-1">Personal Data:</strong>
                            Name, shipping address, email address, and phone number provided when placing orders or contacting support.
                        </li>
                        <li class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <strong class="text-gray-900 block mb-1">Third-Party Integration Data (eBay):</strong>
                            OAuth authentication tokens, user IDs, and inventory/listing details authorized through official eBay APIs.
                        </li>
                        <li class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <strong class="text-gray-900 block mb-1">System Data:</strong>
                            Technical logs such as IP addresses, browser types, and access timestamps.
                        </li>
                    </ul>
                </section>

                <hr class="border-gray-100">

                <!-- 2. How We Use Your Information -->
                <section class="space-y-4">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <span class="text-[#035c43]">2.</span> How We Use Your Information
                    </h2>
                    <p class="text-gray-700 text-sm sm:text-base leading-relaxed">
                        We use the collected information for purposes including:
                    </p>
                    <ul class="space-y-2 text-gray-700 text-sm sm:text-base list-disc list-inside">
                        <li>Fulfilling and managing orders, services, and inventory syncing.</li>
                        <li>Communicating with you regarding updates, inquiries, or support requests.</li>
                        <li>Ensuring technical compliance with marketplace platforms such as eBay.</li>
                    </ul>
                </section>

                <hr class="border-gray-100">

                <!-- 3. Data Protection & eBay Account Deletion Policy -->
                <section class="space-y-4">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <span class="text-[#035c43]">3.</span> Data Protection & eBay Account Deletion Policy
                    </h2>
                    <div class="bg-emerald-950 text-white p-5 sm:p-6 rounded-2xl space-y-3">
                        <p class="text-sm sm:text-base text-gray-200 leading-relaxed">
                            We strictly comply with marketplace data protection policies, including <strong>eBay Marketplace Account Deletion & User Data Protection requirements</strong>.
                        </p>
                        <p class="text-sm sm:text-base text-gray-200 leading-relaxed">
                            When an eBay user requests account deletion, eBay notifies our system via webhooks. Upon receiving a verified account deletion notification, we automatically delete or anonymize all associated personal user data stored within our databases in accordance with eBay policies and applicable privacy laws.
                        </p>
                    </div>
                </section>

                <hr class="border-gray-100">

                <!-- 4. Data Sharing & Disclosure -->
                <section class="space-y-3">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <span class="text-[#035c43]">4.</span> Data Sharing & Disclosure
                    </h2>
                    <p class="text-gray-700 text-sm sm:text-base leading-relaxed">
                        We do not sell, trade, or rent your personal information to third parties. We may share information only with trusted service providers necessary to operate our business (e.g., shipping carriers, payment processors) or when required by law.
                    </p>
                </section>

                <hr class="border-gray-100">

                <!-- 5. Security of Your Information -->
                <section class="space-y-3">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <span class="text-[#035c43]">5.</span> Security of Your Information
                    </h2>
                    <p class="text-gray-700 text-sm sm:text-base leading-relaxed">
                        We use administrative, technical, and physical security measures to protect your personal data against unauthorized access, alteration, or destruction.
                    </p>
                </section>

                <hr class="border-gray-100">

                <!-- 6. Contact Us -->
                <section class="space-y-4">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <span class="text-[#035c43]">6.</span> Contact Us
                    </h2>
                    <p class="text-gray-700 text-sm sm:text-base leading-relaxed">
                        If you have any questions or concerns about this Privacy Policy or data requests, please contact us:
                    </p>
                    
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200/80 space-y-3 text-sm sm:text-base text-gray-800">
                        <p class="font-bold text-base sm:text-lg text-[#035c43]">IT Investment Recoveries</p>
                        
                        <p class="flex items-center gap-2">
                            <span class="font-semibold text-gray-900">Website:</span>
                            <a href="https://it-investmentrecoveries.com" class="text-[#035c43] hover:underline font-medium">it-investmentrecoveries.com</a>
                        </p>

                        <p class="flex items-center gap-2">
                            <span class="font-semibold text-gray-900">Location:</span>
                            <span>Denver, CO (5765 W 52nd Ave, Denver, CO 80212, USA)</span>
                        </p>

                        <p class="flex items-center gap-2">
                            <span class="font-semibold text-gray-900">Phone:</span>
                            <a href="tel:+13034724701" class="hover:text-[#035c43] hover:underline font-medium">+1-303-472-4701</a>
                        </p>

                        <p class="flex items-center gap-2">
                            <span class="font-semibold text-gray-900">Email:</span>
                            <a href="mailto:customerservice@it-investmentrecoveries.com" class="hover:text-[#035c43] hover:underline font-medium">customerservice@it-investmentrecoveries.com</a>
                        </p>
                    </div>
                </section>

            </div>
        </main>

        <!-- Full Width Main Site Footer Component -->
        <x-footer />
    </body>
</html>
