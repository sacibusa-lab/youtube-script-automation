<div 
    x-data="{ 
        open: {{ session('insufficient_credits') ? 'true' : 'false' }},
        packages: @json($topupPackages ?? [])
    }" 
    x-show="open"
    x-on:open-credit-modal.window="open = true"
    x-on:close-credit-modal.window="open = false"
    @keydown.escape.window="open = false"
    class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/90 backdrop-blur-md"
    x-cloak
>
    <!-- Modal Container -->
    <div 
        @click.outside="open = false" 
        class="bg-zinc-900 border border-zinc-800 rounded-[32px] w-full max-w-lg overflow-hidden shadow-2xl relative"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
    >
        <!-- Top-right Close Trigger -->
        <button type="button" @click="open = false" class="absolute top-6 right-6 text-zinc-500 hover:text-white transition-colors z-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Futuristic Glow Background -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-1 bg-gradient-to-r from-transparent via-teal-500 to-transparent blur-sm"></div>

        <div class="p-8 text-center">
            <!-- Icon -->
            <div class="w-16 h-16 bg-teal-500/10 rounded-full flex items-center justify-center mx-auto mb-6 border border-teal-500/20">
                <svg class="w-8 h-8 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>

            <h2 class="text-2xl font-black text-white tracking-tight uppercase italic mb-2">Credits Exhausted</h2>
            <p class="text-zinc-400 text-sm font-bold uppercase tracking-widest leading-relaxed">Your Mission Engine has reached 0% fuel. Top up to resume production.</p>
        </div>

        <div class="px-8 pb-8 space-y-4">
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] text-center">Fuel Resupply Options</p>
            
            <div class="grid grid-cols-1 gap-3">
                <template x-for="pkg in packages" :key="pkg.id">
                    <a 
                        :href="'{{ route('payment.initialize.topup') }}?package_id=' + pkg.id"
                        class="group bg-zinc-800/50 hover:bg-zinc-800 border border-zinc-700/50 hover:border-teal-500/50 p-4 rounded-2xl flex items-center justify-between transition-all"
                    >
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-teal-500/10 rounded-xl flex items-center justify-center text-teal-400 group-hover:bg-teal-500 group-hover:text-white transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="text-left">
                                <p class="text-white font-black uppercase text-xs tracking-widest" x-text="pkg.name"></p>
                                <p class="text-zinc-500 text-[10px]" x-text="Number(pkg.credits).toLocaleString() + ' Credits'"></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-teal-400 font-black text-sm" x-text="'$' + Number(pkg.price).toFixed(2)"></p>
                        </div>
                    </a>
                </template>
            </div>

            <button 
                type="button"
                @click="open = false" 
                class="w-full py-4 text-[10px] font-black text-zinc-500 uppercase tracking-widest hover:text-white transition-colors"
            >
                Dismiss / Close
            </button>
        </div>
    </div>
</div>
