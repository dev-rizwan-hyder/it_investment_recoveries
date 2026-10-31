<!-- Fixed Bottom-Left Badge with Heartbeat Zoom In/Out & Blinking Effect -->
<div class="fixed bottom-4 left-4 sm:bottom-8 sm:left-8 z-40">
    <a href="{{ url('/commercial-business-pickup') }}" class="block cursor-pointer group" aria-label="Office & Business Cleanouts Start at $100">
        <div class="badge-heartbeat w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 lg:w-32 lg:h-32 xl:w-36 xl:h-36 rounded-full bg-black/95 border-2 border-dashed border-emerald-400 p-1 sm:p-1.5 shadow-2xl flex flex-col items-center justify-center text-center transition-all duration-300">
            <div class="w-full h-full rounded-full border border-emerald-500/40 flex flex-col items-center justify-center p-1 sm:p-1.5 md:p-2 lg:p-2.5 bg-gradient-to-b from-black/80 via-black/95 to-[#022f22]/70">
                <span class="text-[7.5px] sm:text-[8.5px] md:text-[9.5px] lg:text-[10.5px] xl:text-[11.5px] font-extrabold text-white uppercase tracking-wider leading-none">OFFICE</span>
                <span class="text-[7.5px] sm:text-[8.5px] md:text-[9.5px] lg:text-[10.5px] xl:text-[11.5px] font-extrabold text-white uppercase tracking-wider leading-none mt-1.5 sm:mt-2 md:mt-2.5">&amp; BUSINESS</span>
                <span class="text-[7.5px] sm:text-[8.5px] md:text-[9.5px] lg:text-[10.5px] xl:text-[11.5px] font-extrabold text-white uppercase tracking-wider leading-none mt-1.5 sm:mt-2 md:mt-2.5">CLEANOUTS</span>
                <div class="w-10 sm:w-12 md:w-14 lg:w-16 border-t border-emerald-500/50 mt-1 sm:mt-1 md:mt-1.5 mb-1 sm:mb-1 md:mb-1.5"></div>
                <span class="text-[7.5px] sm:text-[8.5px] md:text-[9.5px] lg:text-[10.5px] xl:text-[11.5px] font-extrabold text-white uppercase tracking-wider leading-none whitespace-nowrap">START AT $100</span>
            </div>
        </div>
    </a>
</div>

<style>
    @keyframes heartbeatBadge {
        0%, 100% {
            transform: scale(1);
            filter: drop-shadow(0 4px 14px rgba(0, 0, 0, 0.5));
        }
        14% {
            transform: scale(1.08);
            filter: drop-shadow(0 0 16px rgba(52, 211, 153, 0.75)) brightness(1.15);
        }
        28% {
            transform: scale(1);
            filter: drop-shadow(0 4px 14px rgba(0, 0, 0, 0.5));
        }
        42% {
            transform: scale(1.15);
            filter: drop-shadow(0 0 24px rgba(16, 185, 129, 0.95)) brightness(1.22);
        }
        70% {
            transform: scale(1);
            filter: drop-shadow(0 4px 14px rgba(0, 0, 0, 0.5));
        }
    }

    .badge-heartbeat {
        animation: heartbeatBadge 3.2s ease-in-out infinite;
        transform-origin: center center;
        will-change: transform, filter;
    }

    .badge-heartbeat:hover {
        animation-play-state: paused;
        transform: scale(1.12);
        filter: drop-shadow(0 0 20px rgba(16, 185, 129, 0.85));
    }

    @media (prefers-reduced-motion: reduce) {
        .badge-heartbeat {
            animation: none !important;
        }
    }
</style>
