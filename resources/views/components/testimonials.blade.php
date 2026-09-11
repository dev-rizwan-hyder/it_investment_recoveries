<!-- Section: Our Happy Clients (Redesigned Modern Testimonials Carousel) -->
<section 
    id="testimonials" 
    class="bg-gradient-to-b from-slate-50/70 via-white to-slate-50/70 text-gray-900 py-16 sm:py-24 px-4 sm:px-8 lg:px-16 w-full border-t border-gray-100 overflow-hidden relative"
    x-data="{
        activeSlide: 0,
        maxSlide: 4,
        autoplayTimer: null,
        reviews: [
            {
                name: 'David Jones',
                title: 'IT Operations Manager • Denver, CO',
                rating: 5,
                text: `I have to say, I'm really impressed by the professionalism and efficiency of the IT Investment Recoveries team. If you're looking for hassle-free e-waste recycling and serialized asset destruction, I highly recommend them!`,
                image: '{{ asset('images/home/reviews/1.webp') }}'
            },
            {
                name: 'David Martinelli',
                title: 'Corporate Logistics Director • Denver, CO',
                rating: 5,
                text: `I recently hired IT Investment Recoveries for full computer recycling in Denver, and I couldn't be happier. Their team was punctual, professional, and took care of everything seamlessly. Highly recommended!`,
                image: '{{ asset('images/home/reviews/2.webp') }}'
            },
            {
                name: 'Mark Richardson',
                title: 'Compliance Officer • Tech Sector',
                rating: 5,
                text: `Their commitment to NIST SP 800-88 data security and environmentally responsible practices is commendable. A reliable choice for anyone looking to recycle corporate electronics safely.`,
                image: '{{ asset('images/home/reviews/3.webp') }}'
            },
            {
                name: 'Michael Brown',
                title: 'Small Business Owner • Denver, CO',
                rating: 5,
                text: `Every business and resident in Denver should know about their electronics recycling services. They make the whole process fast, easy, secure, and fully documented.`,
                image: '{{ asset('images/home/reviews/4.webp') }}'
            },
            {
                name: 'Shams W. Pawel',
                title: 'Non-Profit Administrator',
                rating: 5,
                text: `With affordable and efficient computer recycling in Denver, they exceeded all my expectations. They offer reasonable rates and outstanding customer support!`,
                image: '{{ asset('images/home/reviews/5.webp') }}'
            },
            {
                name: 'Sarah Jenkins',
                title: 'Healthcare IT Director',
                rating: 5,
                text: `Extremely thorough data destruction reporting. Receiving official Certificates of Recycling and Data Destruction gave our compliance team complete peace of mind.`,
                image: '{{ asset('images/home/reviews/6.webp') }}'
            },
            {
                name: 'Robert Vance',
                title: 'Financial Services Tech Lead',
                rating: 5,
                text: `Punctual pickup, professional crew, and zero hassle. IT Investment Recoveries is our go-to partner for all enterprise IT asset disposition.`,
                image: '{{ asset('images/home/reviews/7.webp') }}'
            }
        ],
        next() {
            this.activeSlide = (this.activeSlide < this.maxSlide) ? this.activeSlide + 1 : 0;
        },
        prev() {
            this.activeSlide = (this.activeSlide > 0) ? this.activeSlide - 1 : this.maxSlide;
        },
        startAutoplay() {
            this.autoplayTimer = setInterval(() => { this.next(); }, 6000);
        },
        stopAutoplay() {
            if (this.autoplayTimer) clearInterval(this.autoplayTimer);
        }
    }"
    x-init="startAutoplay()"
    @mouseenter="stopAutoplay()"
    @mouseleave="startAutoplay()"
>
    <!-- Background Ambient Glow Accents -->
    <div class="absolute top-1/2 left-1/4 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[350px] bg-emerald-400/10 blur-[130px] pointer-events-none rounded-full"></div>
    <div class="absolute top-1/2 right-1/4 translate-x-1/2 -translate-y-1/2 w-[500px] h-[300px] bg-teal-400/10 blur-[120px] pointer-events-none rounded-full"></div>

    <div class="w-full max-w-[1650px] mx-auto flex flex-col items-center text-center relative z-10 space-y-10 sm:space-y-14">

        <!-- Header Section -->
        <div class="space-y-4 max-w-5xl mx-auto px-4">
            <!-- Trust Badge Pill -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 border border-emerald-200/90 text-[#035c43] text-xs sm:text-sm font-extrabold uppercase tracking-wider shadow-sm">
                <span class="text-amber-400 flex gap-0.5 text-sm">★★★★★</span>
                <span>TRUSTED BY 2,000+ DENVER CLIENTS</span>
            </div>

            <!-- Main Heading -->
            <h2 class="text-2xl sm:text-4xl lg:text-5xl xl:text-6xl font-black tracking-tight text-gray-900 font-['Albert_Sans',sans-serif] leading-tight whitespace-nowrap">
                <span>What Our</span>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#035c43] via-emerald-600 to-[#035c43] ml-1.5 sm:ml-3">Happy Clients Say</span>
            </h2>
        </div>

        <!-- Carousel Track Wrapper with Arrows Container -->
        <div class="relative w-full px-2 sm:px-6">
            
            <!-- Left Arrow Control Button -->
            <button 
                @click="prev()" 
                class="absolute -left-2 sm:left-1 top-1/2 -translate-y-1/2 z-20 w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-white/90 border border-gray-200 text-gray-700 hover:text-white hover:bg-[#035c43] hover:border-[#035c43] shadow-lg hover:shadow-xl backdrop-blur-md flex items-center justify-center transition-all duration-300 transform hover:scale-110 focus:outline-none"
                aria-label="Previous Slide"
            >
                <svg class="w-5 h-5 sm:w-6 sm:h-6 fill-none stroke-current" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M15 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <!-- Right Arrow Control Button -->
            <button 
                @click="next()" 
                class="absolute -right-2 sm:right-1 top-1/2 -translate-y-1/2 z-20 w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-white/90 border border-gray-200 text-gray-700 hover:text-white hover:bg-[#035c43] hover:border-[#035c43] shadow-lg hover:shadow-xl backdrop-blur-md flex items-center justify-center transition-all duration-300 transform hover:scale-110 focus:outline-none"
                aria-label="Next Slide"
            >
                <svg class="w-5 h-5 sm:w-6 sm:h-6 fill-none stroke-current" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <!-- Overflow Hidden Slider -->
            <div class="w-full overflow-hidden py-4">
                <div 
                    class="flex transition-transform duration-500 ease-out"
                    :style="`transform: translateX(-${activeSlide * (100 / (window.innerWidth >= 1024 ? 3 : window.innerWidth >= 768 ? 2 : 1))}%)`"
                >
                    <template x-for="(review, index) in reviews" :key="index">
                        <div class="w-full md:w-1/2 lg:w-1/3 shrink-0 px-3 sm:px-4 flex">
                            <!-- Card Container -->
                            <div class="bg-white rounded-3xl border border-gray-200/90 shadow-[0_12px_40px_rgba(0,0,0,0.06)] hover:shadow-[0_22px_55px_rgba(3,92,67,0.14)] hover:border-emerald-400/60 transition-all duration-300 transform hover:-translate-y-2 flex flex-col justify-between p-7 sm:p-9 w-full relative group overflow-hidden text-left min-h-[340px]">
                                
                                <!-- Top Gradient Accent Line -->
                                <div class="h-1.5 w-full bg-gradient-to-r from-[#035c43] via-emerald-500 to-emerald-300 absolute top-0 left-0"></div>

                                <!-- Background Decorative Quote Watermark -->
                                <div class="absolute right-4 top-4 text-emerald-500/10 text-7xl font-serif select-none pointer-events-none group-hover:text-emerald-500/20 transition-colors">“</div>

                                <!-- Card Content -->
                                <div class="space-y-4 pt-2 relative z-10 flex-grow flex flex-col justify-between">
                                    <div class="space-y-3">
                                        <!-- Top Row: Rating Stars & Verified Pill -->
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex items-center gap-1 text-amber-400 text-lg sm:text-xl">
                                                <template x-for="star in 5" :key="star">
                                                    <span x-text="star <= review.rating ? '★' : '☆'" :class="star <= review.rating ? 'text-amber-400' : 'text-amber-300/40'"></span>
                                                </template>
                                            </div>
                                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-[#035c43] text-xs font-extrabold border border-emerald-200/60 inline-flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 fill-current text-[#035c43]" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>Verified</span>
                                            </span>
                                        </div>

                                        <!-- Review Quote Text -->
                                        <p class="text-gray-700 text-base sm:text-[17px] leading-relaxed font-normal italic py-1" x-text="review.text"></p>
                                    </div>
                                </div>

                                <!-- Card Footer: Author Profile & Location -->
                                <div class="flex items-center justify-between pt-5 mt-6 border-t border-gray-100 relative z-10">
                                    <div class="flex items-center gap-3.5 shrink-0">
                                        <!-- Profile Picture Container with Verified Badge Overlay -->
                                        <div class="relative shrink-0">
                                            <img 
                                                :src="review.image" 
                                                :alt="review.name" 
                                                class="w-14 h-14 sm:w-16 sm:h-16 w-[56px] h-[56px] sm:w-[64px] sm:h-[64px] rounded-full object-cover shadow-md border-2 border-white ring-2 ring-emerald-500/30 shrink-0"
                                                style="width: 56px; height: 56px; max-width: 64px; max-height: 64px;"
                                            />
                                            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-[#035c43] text-white rounded-full flex items-center justify-center text-[10px] border border-white shadow">
                                                ✓
                                            </div>
                                        </div>

                                        <!-- Author Info -->
                                        <div class="space-y-0.5 text-left">
                                            <div class="font-black text-gray-900 text-base sm:text-lg leading-snug" x-text="review.name"></div>
                                            <div class="text-xs font-semibold text-emerald-700 leading-snug" x-text="review.title"></div>
                                        </div>
                                    </div>

                                    <!-- Bottom Quote Icon Accent -->
                                    <div class="text-[#035c43]/20 font-serif text-4xl sm:text-5xl leading-none select-none shrink-0 pl-2">”</div>
                                </div>

                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        <!-- Modern Pagination Dots Controls -->
        <div class="flex items-center justify-center gap-2.5 pt-2">
            <template x-for="dot in 5" :key="dot">
                <button 
                    @click="activeSlide = dot - 1" 
                    class="h-3 rounded-full transition-all duration-300 focus:outline-none"
                    :class="activeSlide === (dot - 1) ? 'bg-[#035c43] w-9 shadow-md' : 'bg-gray-300 w-3 hover:bg-gray-400'"
                    :aria-label="`Go to review slide ${dot}`"
                ></button>
            </template>
        </div>

    </div>
</section>
