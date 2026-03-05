<x-app-layout>
    <div class="space-y-10">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-[40px] font-black text-gray-900 dark:text-white leading-none tracking-tight">Project Library</h1>
                <p class="text-gray-500 dark:text-gray-400 font-medium mt-3">Manage and track your viral video generations</p>
            </div>
            <a href="{{ route('projects.create') }}" class="inline-flex items-center gap-3 bg-teal-600 hover:bg-teal-700 text-white px-8 py-4 rounded-2xl font-black text-[15px] uppercase tracking-widest transition-all shadow-lg shadow-teal-100 dark:shadow-none hover:-translate-y-1 group">
                <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                Launch New Story
            </a>
        </div>

        <!-- Global Performance Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-[32px] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1 px-1">Total Library</p>
                    <h4 class="text-[32px] font-black text-gray-900 dark:text-white leading-none">{{ $videos->total() }}</h4>
                </div>
                <div class="absolute top-[-20px] right-[-20px] w-24 h-24 bg-teal-50 dark:bg-teal-900/10 rounded-full group-hover:scale-110 transition-transform"></div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-8 rounded-[32px] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1 px-1">Stories Completed</p>
                    <h4 class="text-[32px] font-black text-green-600 leading-none">{{ $totalCompleted }}</h4>
                </div>
                <div class="absolute top-[-20px] right-[-20px] w-24 h-24 bg-green-50 dark:bg-green-900/10 rounded-full group-hover:scale-110 transition-transform"></div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-8 rounded-[32px] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1 px-1">Tokens Consumed</p>
                    <h4 class="text-[32px] font-black text-teal-600 dark:text-teal-400 leading-none">{{ number_format($totalTokens) }}</h4>
                </div>
                <div class="absolute top-[-20px] right-[-20px] w-24 h-24 bg-blue-50 dark:bg-blue-900/10 rounded-full group-hover:scale-110 transition-transform"></div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-8 rounded-[32px] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1 px-1">Infrastructure Cost</p>
                    <h4 class="text-[32px] font-black text-gray-900 dark:text-white leading-none">${{ number_format($totalCost, 4) }}</h4>
                </div>
            </div>
        </div>

        <!-- Stories Grid -->
        <div class="space-y-8">
            <div class="flex items-center gap-4">
                <div class="w-1.5 h-10 bg-gradient-to-b from-teal-500 to-teal-700 rounded-full"></div>
                <h2 class="text-[24px] font-black text-gray-900 dark:text-white tracking-tight">Active Generations</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @forelse($videos as $video)
                    <!-- Antigravity Card -->
                    <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6 relative group overflow-hidden transition-all duration-300 hover:border-zinc-700 hover:shadow-2xl hover:shadow-red-900/10">
                        
                        <!-- Header: Badges -->
                        <div class="flex justify-end items-start gap-2 mb-8">
                            <span class="bg-zinc-800 text-zinc-400 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider">
                                {{ strtoupper($video->tier1_country ?? 'USA') }}
                            </span>
                            <span class="bg-red-600 text-white text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider shadow-[0_0_10px_rgba(220,38,38,0.4)]">
                                {{ strtoupper($video->niche ?? 'Mystery') }}
                            </span>
                        </div>

                        <!-- Title -->
                        <a href="{{ route('projects.show', $video) }}" class="block mb-6 relative z-10">
                            <h3 class="font-bold text-[18px] text-white leading-snug line-clamp-3 hover:text-red-500 transition-colors">
                                {{ $video->selected_title ?? $video->topic }}
                            </h3>
                        </a>
                        
                        <!-- Meta: Date & Toggles -->
                        <div class="flex items-center justify-between mb-8">
                            <span class="text-zinc-500 text-[11px] font-medium">
                                {{ $video->created_at->format('m/d/Y') }}
                            </span>
                            
                            <!-- Visual Toggles Decoration -->
                            <div class="flex items-center gap-1">
                                <div class="w-3 h-3 rounded-full bg-zinc-800 border border-zinc-700"></div>
                                <div class="w-3 h-3 rounded-full bg-zinc-700 border border-zinc-600"></div>
                                <div class="w-3 h-3 rounded-full bg-zinc-600 border border-zinc-500"></div>
                            </div>
                        </div>

                        <!-- Footer: Action & Progress -->
                        <div class="flex items-end justify-between">
                            <div class="flex flex-col gap-2 w-full max-w-[60%]">
                                <a href="{{ route('projects.show', $video) }}" class="text-[10px] font-bold text-zinc-500 uppercase tracking-[0.2em] hover:text-white transition-colors">
                                    View Story
                                </a>
                                <!-- Red Progress Bar -->
                                <div class="h-1.5 w-full bg-zinc-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-red-600 rounded-full shadow-[0_0_8px_rgba(220,38,38,0.6)]" 
                                         style="width: @if($video->status === 'completed') 100% @elseif($video->status === 'failed') 100% @else 45% @endif"></div>
                                </div>
                            </div>

                            <!-- Delete Action -->
                            <form action="{{ route('projects.destroy', $video) }}" method="POST" onsubmit="return confirm('Delete this story?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-zinc-600 hover:text-red-500 transition-colors p-2 -mr-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>

                    </div>
                @empty
                    <div class="col-span-full text-center py-24 bg-white dark:bg-gray-800 rounded-[40px] border-2 border-dashed border-gray-100 dark:border-gray-700">
                        <div class="w-20 h-20 bg-teal-50 dark:bg-teal-900/10 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2 uppercase tracking-tight">The library is empty</h3>
                        <p class="text-gray-500 dark:text-gray-400 font-medium mb-8">Start your journey by creating your first AI-powered story.</p>
                        <a href="{{ route('projects.create') }}" class="inline-flex items-center gap-3 bg-teal-600 text-white px-10 py-4 rounded-2xl font-black text-[14px] uppercase tracking-widest hover:bg-teal-700 transition shadow-lg shadow-teal-100 dark:shadow-none">
                            Launch Intelligent Generation
                        </a>
                    </div>
                @endforelse
            </div>

            <div class="mt-12">
                {{ $videos->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
