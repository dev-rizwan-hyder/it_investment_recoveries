<!-- Fixed Bottom-Left Badge with Heartbeat Zoom In/Out & Blinking Effect -->
<div class="fixed bottom-4 left-4 sm:bottom-8 sm:left-8 z-40">
    <a href="{{ url('/community-partners') }}" class="block cursor-pointer group" aria-label="Community & Business Events Available">
        <div class="badge-heartbeat w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 lg:w-28 lg:h-28 rounded-full bg-black/95 border-2 border-dashed border-emerald-400 p-0.5 sm:p-1 md:p-1.5 shadow-2xl flex flex-col items-center justify-center text-center transition-all duration-300">
            <div class="w-full h-full rounded-full border border-emerald-500/40 flex flex-col items-center justify-center p-0.5 sm:p-1 md:p-1.5 bg-gradient-to-b from-black/80 via-black/95 to-[#022f22]/70">
                <span class="text-[5px] sm:text-[6px] md:text-[8px] lg:text-[9px] font-bold text-gray-200 uppercase tracking-wider leading-tight">COMMUNITY</span>
                <span class="text-[4px] sm:text-[5px] md:text-[7px] lg:text-[8px] font-extrabold uppercase tracking-wide leading-tight my-0.5 text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 via-green-400 to-teal-300 inline-block drop-shadow-sm" style="-webkit-background-clip: text; -webkit-text-fill-color: transparent;">&amp; BUSINESS</span>
                <span class="text-[5px] sm:text-[6px] md:text-[8px] lg:text-[9px] font-bold text-gray-200 uppercase tracking-wider leading-tight">EVENTS</span>
                <span class="text-[5px] sm:text-[6px] md:text-[8px] lg:text-[10px] font-extrabold text-white uppercase tracking-widest leading-tight mt-0.5 border-t border-emerald-500/40 pt-0.5 w-4/5">AVAILABLE</span>
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
            transform: scale(1.1);
            filter: drop-shadow(0 0 16px rgba(52, 211, 153, 0.75)) brightness(1.15);
        }
        28% {
            transform: scale(1);
            filter: drop-shadow(0 4px 14px rgba(0, 0, 0, 0.5));
        }
        42% {
            transform: scale(1.18);
            filter: drop-shadow(0 0 24px rgba(16, 185, 129, 0.95)) brightness(1.25);
        }
        70% {
            transform: scale(1);
            filter: drop-shadow(0 4px 14px rgba(0, 0, 0, 0.5));
        }
    }

    .badge-heartbeat {
        animation: heartbeatBadge 2.2s ease-in-out infinite;
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
