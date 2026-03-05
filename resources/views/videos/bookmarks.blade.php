<x-app-layout>
    <div class="max-w-7xl mx-auto py-6">
        
        <!-- Header -->
        <div class="flex items-center justify-between mb-10">
            <div>
                <h1 class="text-3xl font-black text-gray-800 dark:text-white mb-2 uppercase tracking-tight">Saved Concepts</h1>
                <p class="text-gray-500 dark:text-gray-400 font-medium text-sm">Your private collection of viral angles and explosive hooks.</p>
            </div>
            
            <div class="bg-white dark:bg-gray-800 px-6 py-3 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm flex items-center gap-4">
                <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Total Saved</span>
                <span class="text-2xl font-black text-teal-600 dark:text-teal-400">{{ $bookmarks->total() }}</span>
            </div>
        </div>

        @if($bookmarks->isEmpty())
            <div class="bg-white dark:bg-gray-800 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-[40px] p-20 text-center">
                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 20V4z"></path></svg>
                </div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-2">No saved concepts yet</h2>
                <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-sm mx-auto">Bookmark your favorite AI-generated titles and hooks to see them here for later use.</p>
                <a href="{{ route('projects.create') }}" class="inline-flex items-center gap-2 bg-teal-600 text-white px-8 py-4 rounded-2xl font-black uppercase tracking-widest text-sm hover:bg-teal-700 transition shadow-lg shadow-teal-600/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                    New Story
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($bookmarks as $bookmark)
                    <div class="group bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-[32px] overflow-hidden flex flex-col shadow-sm hover:shadow-xl transition-all duration-500 hover:-translate-y-1"
                         x-data="{ 
                            isSaved: @js($bookmark->is_saved),
                            async toggle() {
                                const res = await fetch(`/projects/titles/{{ $bookmark->id }}/toggle-bookmark`, {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                });
                                const data = await res.json();
                                if (data.success) this.isSaved = data.is_saved;
                            },
                            copyToClipboard(text, type) {
                                navigator.clipboard.writeText(text);
                            }
                         }">
                        
                        <!-- Top: Context & Actions -->
                        <div class="p-6 pb-0 flex justify-between items-start">
                            <span class="bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-[10px] font-black px-2.5 py-1 rounded-lg uppercase tracking-wider">
                                {{ $bookmark->video->niche }}
                            </span>
                            
                            <button @click="toggle()" class="focus:outline-none transition-transform hover:scale-110 active:scale-95">
                                <svg class="w-6 h-6 transition-colors" 
                                    :class="isSaved ? 'text-teal-500 fill-current' : 'text-gray-300 dark:text-gray-600'"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 20V4z"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="p-6 flex-1">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white leading-tight mb-4 group-hover:text-teal-600 dark:group-hover:text-teal-400 transition-colors">
                                {{ $bookmark->title }}
                            </h3>

                            <div class="space-y-4">
                                <!-- Thumbnail Concept -->
                                @if(isset($bookmark->metadata['thumbnail_concept']))
                                    <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-4 border border-gray-100 dark:border-gray-700/50">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-[9px] font-black text-red-500 uppercase tracking-widest">Thumbnail</span>
                                            <button @click="copyToClipboard(@js($bookmark->metadata['thumbnail_concept']), 'Thumbnail')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                            </button>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 italic">"{{ $bookmark->metadata['thumbnail_concept'] }}"</p>
                                    </div>
                                @endif

                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="p-6 pt-0 border-t border-gray-50 dark:border-gray-700/50 mt-auto flex items-center justify-between">
                            <a href="{{ route('projects.show', $bookmark->video_id) }}" class="text-[10px] font-black text-gray-400 hover:text-teal-600 uppercase tracking-widest transition-colors">
                                View Original Story
                            </a>
                            <span class="text-[10px] font-bold text-gray-300 dark:text-gray-600">{{ $bookmark->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $bookmarks->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
