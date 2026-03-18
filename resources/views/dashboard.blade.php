<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 p-8 rounded-[32px] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1 px-1">Total Stories</p>
                        <h4 class="text-[32px] font-black text-gray-900 dark:text-white leading-none">{{ $totalStories }}</h4>
                    </div>
                    <div class="absolute top-[-20px] right-[-20px] w-24 h-24 bg-zinc-50 dark:bg-zinc-900/40 rounded-full group-hover:scale-110 transition-transform"></div>
                </div>

                <!-- Script Tokens -->
                <div class="bg-white dark:bg-gray-800 p-8 rounded-[32px] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-[11px] font-black text-teal-500 uppercase tracking-widest mb-1 px-1">Script Balance</p>
                        <h4 class="text-[32px] font-black text-teal-600 dark:text-teal-400 leading-none">{{ number_format($creditsRemaining) }}</h4>
                    </div>
                    <div class="absolute top-[-20px] right-[-20px] w-24 h-24 bg-teal-50 dark:bg-teal-900/20 rounded-full group-hover:scale-110 transition-transform"></div>
                </div>

                <!-- Image Tokens -->
                <div class="bg-white dark:bg-gray-800 p-8 rounded-[32px] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-[11px] font-black text-rose-500 uppercase tracking-widest mb-1 px-1">Image Balance</p>
                        <h4 class="text-[32px] font-black text-rose-600 dark:text-rose-400 leading-none">{{ number_format($imageRemaining) }}</h4>
                    </div>
                    <div class="absolute top-[-20px] right-[-20px] w-24 h-24 bg-rose-50 dark:bg-rose-900/20 rounded-full group-hover:scale-110 transition-transform"></div>
                </div>

                <!-- Voice Tokens -->
                <div class="bg-white dark:bg-gray-800 p-8 rounded-[32px] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-[11px] font-black text-purple-500 uppercase tracking-widest mb-1 px-1">Voice Balance</p>
                        <h4 class="text-[32px] font-black text-purple-600 dark:text-purple-400 leading-none">{{ number_format($voiceRemaining) }}</h4>
                    </div>
                    <div class="absolute top-[-20px] right-[-20px] w-24 h-24 bg-purple-50 dark:bg-purple-900/20 rounded-full group-hover:scale-110 transition-transform"></div>
                </div>
            </div>

            <!-- Token Resource Hub -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-[40px] border border-gray-100 dark:border-gray-700 mb-8">
                <div class="p-10">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10 pb-6 border-b border-gray-50 dark:border-gray-700/50">
                        <div>
                            <h3 class="text-[28px] font-black text-gray-900 dark:text-white tracking-tight uppercase italic">Resource Ecosystem</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Monitoring your monthly creative energy and asset allocation.</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Active Plan</p>
                                <p class="text-sm font-black text-indigo-600 dark:text-indigo-400 uppercase">{{ $plan ? $plan->name : 'Manual / Pro' }}</p>
                            </div>
                            @if($usagePercentage >= 80 || $imagePercent >= 80 || $voicePercent >= 80)
                                <a href="{{ route('topup.index') }}" class="px-6 py-3 bg-gray-900 dark:bg-white dark:text-gray-900 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest hover:scale-105 transition shadow-lg shadow-gray-200 dark:shadow-none">
                                    Fuel Up
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        <!-- Script Engine -->
                        <div class="group">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-900/20 flex items-center justify-center text-teal-600 dark:text-teal-400 shadow-sm group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Script Engine</h4>
                                    <p class="text-xl font-black text-gray-900 dark:text-white leading-none tracking-tight">{{ number_format($creditsRemaining) }} <span class="text-[10px] text-gray-400 uppercase font-bold">Left</span></p>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <div class="flex justify-between text-[10px] font-black uppercase tracking-widest mb-2 px-1">
                                        <span class="text-teal-600">Fuel Level</span>
                                        <span class="text-gray-400">{{ round(100 - $usagePercentage) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 dark:bg-gray-700/50 rounded-full h-2 overflow-hidden">
                                        <div class="h-2 rounded-full bg-teal-500 transition-all duration-700" style="width: {{ 100 - $usagePercentage }}%"></div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-3 rounded-2xl bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Used</p>
                                        <p class="text-sm font-black text-gray-700 dark:text-gray-300">{{ number_format($creditsUsedThisMonth) }}</p>
                                    </div>
                                    <div class="p-3 rounded-2xl bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Est. Scripts</p>
                                        <p class="text-sm font-black text-gray-700 dark:text-gray-300">~{{ number_format($estimatedScriptsRemaining) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Visual Studio -->
                        <div class="group">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-900/20 flex items-center justify-center text-rose-600 dark:text-rose-400 shadow-sm group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Visual Studio</h4>
                                    <p class="text-xl font-black text-gray-900 dark:text-white leading-none tracking-tight">{{ number_format($imageRemaining) }} <span class="text-[10px] text-gray-400 uppercase font-bold">Left</span></p>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <div class="flex justify-between text-[10px] font-black uppercase tracking-widest mb-2 px-1">
                                        <span class="text-rose-600">Fuel Level</span>
                                        <span class="text-gray-400">{{ round(100 - $imagePercent) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 dark:bg-gray-700/50 rounded-full h-2 overflow-hidden">
                                        <div class="h-2 rounded-full bg-rose-500 transition-all duration-700" style="width: {{ 100 - $imagePercent }}%"></div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-3 rounded-2xl bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Used</p>
                                        <p class="text-sm font-black text-gray-700 dark:text-gray-300">{{ number_format($imageUsed) }}</p>
                                    </div>
                                    <div class="p-3 rounded-2xl bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Monthly Limit</p>
                                        <p class="text-sm font-black text-gray-700 dark:text-gray-300">{{ number_format($imageTotal) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Voice Laboratory -->
                        <div class="group">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-purple-600 dark:text-purple-400 shadow-sm group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Voice Laboratory</h4>
                                    <p class="text-xl font-black text-gray-900 dark:text-white leading-none tracking-tight">{{ number_format($voiceRemaining) }} <span class="text-[10px] text-gray-400 uppercase font-bold">Left</span></p>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <div class="flex justify-between text-[10px] font-black uppercase tracking-widest mb-2 px-1">
                                        <span class="text-purple-600">Fuel Level</span>
                                        <span class="text-gray-400">{{ round(100 - $voicePercent) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 dark:bg-gray-700/50 rounded-full h-2 overflow-hidden">
                                        <div class="h-2 rounded-full bg-purple-500 transition-all duration-700" style="width: {{ 100 - $voicePercent }}%"></div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-3 rounded-2xl bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Used</p>
                                        <p class="text-sm font-black text-gray-700 dark:text-gray-300">{{ number_format($voiceUsed) }}</p>
                                    </div>
                                    <div class="p-3 rounded-2xl bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50">
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Scenes Left</p>
                                        <p class="text-sm font-black text-gray-700 dark:text-gray-300">~{{ $voiceCostPerScene > 0 ? floor($voiceRemaining / $voiceCostPerScene) : 0 }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-[40px] border border-gray-100 dark:border-gray-700">
                <div class="p-10">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-[24px] font-black text-gray-900 dark:text-white tracking-tight">Recent Stories</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Your most recent video generations</p>
                        </div>
                        <a href="{{ route('projects.index') }}" class="text-sm font-black text-teal-600 dark:text-teal-400 uppercase tracking-widest hover:translate-x-1 transition-transform inline-flex items-center gap-2">
                            View All Stories
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>

                    <div class="space-y-4">
                        @forelse($recentProjects as $video)
                            @php $isReady = $video->isFullyReady(); @endphp
                            <a href="{{ route('projects.show', $video) }}" class="block p-6 bg-gray-50 dark:bg-gray-700/30 rounded-[28px] border border-transparent {{ $isReady ? 'hover:border-green-100 dark:hover:border-green-900/30' : 'hover:border-teal-100 dark:hover:border-teal-900/30' }} hover:bg-white dark:hover:bg-gray-700/50 transition-all group">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-6">
                                        <div class="w-14 h-14 rounded-2xl bg-white dark:bg-gray-800 flex items-center justify-center font-black {{ $isReady ? 'text-green-600 dark:text-green-400 shadow-green-500/10' : 'text-teal-600 dark:text-teal-400 shadow-sm' }} shadow-sm">
                                            {{ strtoupper(substr($video->niche, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h4 class="font-black text-[17px] text-gray-800 dark:text-gray-100 {{ $isReady ? 'group-hover:text-green-600' : 'group-hover:text-teal-600' }} transition-colors">
                                                {{ $video->selected_title ?? 'Untitled Generation' }}
                                            </h4>
                                            <div class="flex items-center gap-3 mt-1">
                                                <span class="text-[11px] font-black {{ $isReady ? 'text-green-600/70 border-green-500/20' : 'text-gray-400 dark:text-gray-500 border-gray-100 dark:border-zinc-800' }} uppercase tracking-widest bg-white dark:bg-gray-800 px-2 py-0.5 rounded-lg shadow-sm border">{{ $video->niche }}</span>
                                                <span class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">{{ $video->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest 
                                            {{ ($video->status === 'completed' || $isReady) ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                            {{ str_replace('_', ' ', $video->status) }}
                                        </div>
                                        <svg class="w-5 h-5 text-gray-300 dark:text-gray-600 group-hover:text-teal-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-12 bg-gray-50 dark:bg-gray-700/20 rounded-[40px] border-2 border-dashed border-gray-100 dark:border-gray-800">
                                <p class="text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest text-[11px] mb-4">No stories generated yet</p>
                                <a href="{{ route('projects.create') }}" class="inline-flex items-center gap-2 bg-teal-600 text-white px-8 py-3 rounded-2xl font-black text-[13px] uppercase tracking-widest hover:bg-teal-700 transition shadow-lg shadow-teal-100 dark:shadow-none">
                                    Create Your First Story
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
