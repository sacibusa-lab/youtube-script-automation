<x-app-layout>
    <div class="space-y-10">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 rounded-[32px] bg-rose-600 flex items-center justify-center text-white shadow-2xl shadow-rose-900/20">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="text-[10px] font-black text-rose-600 uppercase tracking-[0.3em] bg-rose-600/10 px-2 py-0.5 rounded">Creative Suite</span>
                        <span class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.3em]">v2.0</span>
                    </div>
                    <h1 class="text-[40px] font-black text-gray-900 dark:text-white leading-none tracking-tight italic">Production Studio</h1>
                    <p class="text-gray-500 dark:text-gray-400 font-medium mt-3">Select a project to enter the professional cinematic editor</p>
                </div>
            </div>
        </div>

        <!-- Active Projects for Studio -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @forelse($videos as $video)
                <div class="group relative bg-[#1a1a1a] rounded-[40px] border border-white/5 overflow-hidden transition-all duration-500 hover:border-rose-600/50 hover:shadow-2xl hover:shadow-rose-900/20">
                    <div class="flex h-full min-h-[220px]">
                        <!-- Visual Preview -->
                        <div class="w-1/3 relative bg-black overflow-hidden shrink-0">
                            @php
                                $firstApprovedScene = $video->chapters->flatMap->scenes->first(fn($s) => $s->image_url);
                            @endphp
                            @if($firstApprovedScene)
                                @php $src = Str::startsWith($firstApprovedScene->image_url, '/') ? $firstApprovedScene->image_url : '/' . $firstApprovedScene->image_url; @endphp
                                <img src="{{ $src }}" class="w-full h-full object-cover opacity-60 group-hover:opacity-100 group-hover:scale-110 transition-all duration-700">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#2a2a2a] to-[#0f0f0f]">
                                    <svg class="w-12 h-12 text-white/5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent to-[#1a1a1a]"></div>
                            
                            <!-- Duration Overlay -->
                            <div class="absolute bottom-4 left-4 flex items-center gap-2 px-2 py-1 bg-black/60 backdrop-blur-md rounded text-[9px] font-black text-white/80 uppercase tracking-widest border border-white/10">
                                <svg class="w-3 h-3 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                                {{ $video->duration_minutes }}m Production
                            </div>
                        </div>

                        <!-- Info Area -->
                        <div class="flex-1 p-8 flex flex-col">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-[9px] font-black text-rose-500 uppercase tracking-widest">{{ $video->niche }}</span>
                                <span class="text-[9px] font-black text-zinc-600 uppercase tracking-widest">{{ $video->created_at->diffForHumans() }}</span>
                            </div>
                            
                            <h3 class="text-xl font-black text-white mb-2 line-clamp-2 tracking-tight leading-tight group-hover:text-rose-500 transition-colors">
                                {{ $video->selected_title ?? $video->topic }}
                            </h3>
                            
                            <div class="flex items-center gap-4 mt-auto pt-6 border-t border-white/5">
                                <div class="flex flex-col">
                                    <span class="text-[8px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-1">Foundations</span>
                                    <div class="flex gap-1">
                                        @foreach($video->chapters->take(5) as $chapter)
                                            <div class="w-4 h-1 rounded-full {{ $chapter->status === 'approved' ? 'bg-green-500' : 'bg-zinc-700' }}"></div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="flex flex-col border-l border-white/5 pl-4">
                                    <span class="text-[8px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-1">Visuals</span>
                                    <span class="text-[10px] font-black text-white">{{ $video->chapters->flatMap->scenes->whereNotNull('image_url')->count() }} / {{ $video->chapters->flatMap->scenes->count() }}</span>
                                </div>
                                
                                <a href="{{ route('projects.studio', $video) }}" class="ml-auto bg-white/5 hover:bg-rose-600 text-white px-6 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all shadow-xl active:scale-95">
                                    Enter Studio
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-32 bg-[#1a1a1a] rounded-[48px] border-2 border-dashed border-white/5 text-center">
                    <div class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-8 border border-white/5">
                        <svg class="w-10 h-10 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-3 uppercase tracking-tight">Studio Dormant</h3>
                    <p class="text-zinc-500 font-medium max-w-md mx-auto mb-10">You need an active story generation in progress to unlock the Cinematic Studio workbench.</p>
                    <a href="{{ route('projects.create') }}" class="inline-flex items-center gap-3 bg-rose-600 text-white px-10 py-5 rounded-3xl font-black text-[15px] uppercase tracking-widest hover:bg-rose-700 transition shadow-2xl shadow-rose-900/40">
                        Initialize New Story
                    </a>
                </div>
            @endforelse
        </div>

        @if($videos->hasPages())
            <div class="mt-12">
                {{ $videos->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
