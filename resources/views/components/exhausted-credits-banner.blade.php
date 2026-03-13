@php
    $user = Auth::user();
    $scriptFuel = $user->scriptCreditsFuelPercentage();
    $imageFuel = $user->imageTokensFuelPercentage();
    $minFuel = min($scriptFuel, $imageFuel);
    
    // Explicitly check for 0 balance as well to be safe
    $isExhausted = ($user->scriptCreditsBalance() <= 0) || ($user->imageTokensBalance() <= 0);
    $isLow = $user->isLowOnCredits();
    
    $bannerColor = $isExhausted ? 'red' : 'amber';
    $bannerTitle = $isExhausted ? 'Credits Exhausted' : 'Low Credit Warning';
    $bannerMessage = $isExhausted 
        ? 'Your Mission Engine has reached 0% fuel. Top up to resume production.' 
        : "Your Mission Engine is running low (Remaining: " . number_format($minFuel, 1) . "%). Top up soon to avoid interruption.";
@endphp

<div 
    id="credit-exhaustion-banner"
    x-data="{ 
        open: @json(session('insufficient_credits') || $isLow)
    }"
    x-show="open"
    x-cloak
    @if(!(session('insufficient_credits') || $isLow))
        style="display: none !important;"
    @endif
    x-on:open-credit-modal.window="open = true"
    x-on:close-credit-modal.window="open = false"
    class="relative z-[100] bg-white border-b-2 border-{{ $bannerColor }}-100 py-3 overflow-hidden shadow-md"
>
    <div class="flex items-center relative">
        <div class="whitespace-nowrap flex animate-marquee">
            @for($i = 0; $i < 3; $i++)
            <div class="flex items-center gap-6 px-4" @if($i > 0) aria-hidden="true" @endif>
                <span class="text-{{ $bannerColor }}-600 font-extrabold uppercase italic tracking-tighter flex items-center gap-2 text-base">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                    </svg>
                    {{ $bannerTitle }}
                </span>
                <span class="text-{{ $bannerColor }}-500 font-bold text-sm uppercase tracking-[0.2em]">
                    {{ $bannerMessage }}
                </span>
                <a href="{{ route('topup.index') }}" class="inline-flex items-center bg-black text-white text-xs font-black px-6 py-2 rounded-full hover:bg-{{ $bannerColor }}-700 transition-all hover:scale-105 active:scale-95 uppercase tracking-widest shadow-lg shadow-{{ $bannerColor }}-500/20">
                    Top Up Now
                </a>
            </div>
            @endfor
        </div>

        <!-- Close Button -->
        <button 
            @click="open = false" 
            class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 backdrop-blur-sm p-1.5 rounded-full text-{{ $bannerColor }}-400 hover:text-{{ $bannerColor }}-600 transition-colors shadow-sm z-10"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

<!-- Marquee styles moved to layout or kept here with improved target -->
