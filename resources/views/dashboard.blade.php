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
                <!-- Total Stories -->
                <div class="bg-white dark:bg-gray-800 p-8 rounded-[32px] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1 px-1">Total Stories</p>
                        <h4 class="text-[32px] font-black text-gray-900 dark:text-white leading-none">{{ $totalStories }}</h4>
                    </div>
                    <div class="absolute top-[-20px] right-[-20px] w-24 h-24 bg-teal-50 dark:bg-teal-900/10 rounded-full group-hover:scale-110 transition-transform"></div>
                </div>

                <!-- Generating -->
                <div class="bg-white dark:bg-gray-800 p-8 rounded-[32px] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1 px-1">In Progress</p>
                        <h4 class="text-[32px] font-black text-orange-600 leading-none">{{ $generatingCount }}</h4>
                    </div>
                    <div class="absolute top-[-20px] right-[-20px] w-24 h-24 bg-orange-50 dark:bg-orange-900/10 rounded-full group-hover:scale-110 transition-transform"></div>
                </div>

                <!-- Completed -->
                <div class="bg-white dark:bg-gray-800 p-8 rounded-[32px] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1 px-1">Completed</p>
                        <h4 class="text-[32px] font-black text-green-600 leading-none">{{ $completedCount }}</h4>
                    </div>
                    <div class="absolute top-[-20px] right-[-20px] w-24 h-24 bg-green-50 dark:bg-green-900/10 rounded-full group-hover:scale-110 transition-transform"></div>
                </div>

                <!-- Tokens Used -->
                <div class="bg-white dark:bg-gray-800 p-8 rounded-[32px] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1 px-1">Tokens Used</p>
                        <h4 class="text-[32px] font-black text-teal-600 dark:text-teal-400 leading-none">{{ number_format($totalTokens) }}</h4>
                    </div>
                    <div class="absolute top-[-20px] right-[-20px] w-24 h-24 bg-blue-50 dark:bg-blue-900/10 rounded-full group-hover:scale-110 transition-transform"></div>
                </div>
            </div>

            <!-- Token Usage Widget -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-[40px] border border-gray-100 dark:border-gray-700 mb-8">
                <div class="p-10">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-[24px] font-black text-gray-900 dark:text-white tracking-tight">Token Usage</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Your monthly plan token allocation and consumption</p>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Plan: {{ $plan ? $plan->name : 'No Plan' }}</span>
                            @if($usagePercentage >= 80)
                                <a href="#" class="text-xs font-black text-red-600 uppercase tracking-widest hover:underline mt-1">Upgrade Plan</a>
                            @endif
                        </div>
                    </div>

                    {{-- Script Tokens --}}
                    <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2">Script Tokens</p>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 mb-4 overflow-hidden relative">
                        <div class="h-4 rounded-full transition-all duration-500 {{ $usagePercentage >= 90 ? 'bg-red-500' : ($usagePercentage >= 80 ? 'bg-orange-500' : 'bg-teal-500') }}" style="width: {{ min(100, $usagePercentage) }}%"></div>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center mb-8">
                        <div>
                            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-1">Script Tokens Used</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($creditsUsedThisMonth) }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-1">Remaining Script Tokens</p>
                            <p class="text-lg font-bold {{ $creditsRemaining < 50000 ? 'text-red-500' : 'text-gray-900 dark:text-white' }}">{{ number_format($creditsRemaining) }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-1">Est. Scripts Left</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">~{{ number_format($estimatedScriptsRemaining) }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-1">Avg Tokens / Script</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($averageTokensPerScript) }}</p>
                        </div>
                    </div>

                    {{-- Image Tokens --}}
                    @php
                        $imageTotal = Auth::user()->total_image_tokens;
                        $imageUsed = Auth::user()->used_image_tokens;
                        $imageRemaining = $imageTotal - $imageUsed;
                        $imagePercent = $imageTotal > 0 ? min(100, ($imageUsed / $imageTotal) * 100) : 0;
                    @endphp
                    <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2">Image Tokens</p>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 mb-4 overflow-hidden">
                        <div class="h-4 rounded-full transition-all duration-500 {{ $imagePercent >= 90 ? 'bg-red-500' : ($imagePercent >= 80 ? 'bg-orange-500' : 'bg-purple-500') }}" style="width: {{ $imagePercent }}%"></div>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-1">Image Tokens Used</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($imageUsed) }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-1">Remaining Image Tokens</p>
                            <p class="text-lg font-bold {{ $imageRemaining < 10000 ? 'text-red-500' : 'text-gray-900 dark:text-white' }}">{{ number_format($imageRemaining) }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Monthly Image</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($imageTotal) }}</p>
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
                            <a href="{{ route('projects.show', $video) }}" class="block p-6 bg-gray-50 dark:bg-gray-700/30 rounded-[28px] border border-transparent hover:border-teal-100 dark:hover:border-teal-900/30 hover:bg-white dark:hover:bg-gray-700/50 transition-all group">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-6">
                                        <div class="w-14 h-14 rounded-2xl bg-white dark:bg-gray-800 flex items-center justify-center font-black text-teal-600 dark:text-teal-400 shadow-sm">
                                            {{ strtoupper(substr($video->niche, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h4 class="font-black text-[17px] text-gray-800 dark:text-gray-100 group-hover:text-teal-600 transition-colors">
                                                {{ $video->selected_title ?? 'Untitled Generation' }}
                                            </h4>
                                            <div class="flex items-center gap-3 mt-1">
                                                <span class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest bg-white dark:bg-gray-800 px-2 py-0.5 rounded-lg shadow-sm">{{ $video->niche }}</span>
                                                <span class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">{{ $video->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest 
                                            {{ $video->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
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
