<x-app-layout>
    <div class="py-12 bg-white dark:bg-zinc-950 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-12 text-center">
                <h1 class="text-4xl font-black text-gray-950 dark:text-white mb-4 tracking-tight uppercase">Fuel Your Engine</h1>
                <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto font-medium">Purchase high-velocity token bundles to bypass monthly resets and maintain production speed.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                @foreach($packages as $package)
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[32px] p-10 shadow-sm relative overflow-hidden group hover:border-indigo-500/50 transition-all duration-500 hover:-translate-y-2">
                        <!-- Glow Effect -->
                        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/15 transition-all duration-500"></div>
                        
                        <div class="relative z-10 flex flex-col h-full">
                            <h3 class="text-indigo-600 dark:text-indigo-400 font-black uppercase tracking-widest text-[10px] mb-6 flex items-center">
                                <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full mr-2"></span>
                                {{ $package->name }}
                            </h3>
                            <div class="flex items-baseline mb-8">
                                <span class="text-5xl font-black text-gray-950 dark:text-white tracking-tighter italic">₦{{ number_format($package->price) }}</span>
                            </div>

                            <div class="space-y-4 mb-10 flex-grow">
                                <div class="flex items-center text-gray-700 dark:text-gray-300">
                                    <svg class="w-5 h-5 text-indigo-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="font-black text-sm uppercase tracking-tight">+ {{ number_format($package->credits) }} Units</span>
                                </div>
                                <div class="flex items-center text-gray-500 dark:text-gray-400 text-xs font-bold">
                                    <svg class="w-5 h-5 text-gray-300 dark:text-gray-700 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    No Expiration Policy
                                </div>
                                <div class="flex items-center text-gray-500 dark:text-gray-400 text-xs font-bold">
                                    <svg class="w-5 h-5 text-gray-300 dark:text-gray-700 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Immediate Dispatch
                                </div>
                            </div>

                            <form action="{{ route('payment.initialize.topup') }}" method="POST">
                                @csrf
                                <input type="hidden" name="topup_package_id" value="{{ $package->id }}">
                                <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-2xl font-bold shadow-lg shadow-indigo-600/20 transition-all duration-300 transform active:scale-95 flex items-center justify-center space-x-2">
                                    <span>Purchase Now</span>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- FAQ or Info Note -->
            <div class="bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-8 text-center">
                <p class="text-[10px] text-gray-500 dark:text-gray-400 font-black uppercase tracking-widest leading-loose max-w-xl mx-auto">
                    <span class="text-indigo-600 dark:text-indigo-400 mr-2 italic">Infrastructure Note:</span>
                    Top-up assets are appended to your active balance. They persist indefinitely and are utilized only after your primary tier allocations are exhausted.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
