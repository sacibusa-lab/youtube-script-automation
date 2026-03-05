<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8 font-outfit">

        {{-- STORY TITLE BAR — always visible --}}
        <div class="max-w-screen-2xl mx-auto mb-6 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('projects.index') }}" class="text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <div class="min-w-0" x-data="{ currentTitle: @js($project->selected_title ?? $project->topic) }" @title-updated.window="currentTitle = $event.detail">
                    <p class="text-[10px] font-black text-rose-500 uppercase tracking-[0.3em]" x-text="currentTitle === @js($project->selected_title) ? 'Active Mission' : 'Concept Preview'"></p>
                    <h1 class="text-lg font-black text-zinc-900 dark:text-white truncate tracking-tight leading-tight" x-text="currentTitle">
                    </h1>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="hidden sm:inline-flex px-3 py-1.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400 text-[10px] font-black uppercase tracking-widest rounded-lg border border-zinc-200 dark:border-zinc-700">{{ $project->niche }}</span>
                <span class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest border
                    {{ $project->status === 'waiting_for_strategy_selection' ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800' : '' }}
                    {{ in_array($project->status, ['generating_concepts','generating_strategies','architecting_chapters','generating_structure']) ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800' : '' }}
                    {{ $project->status === 'waiting_for_strategy_selection' || in_array($project->status, ['generating_concepts','generating_strategies','architecting_chapters','generating_structure']) ? '' : 'bg-teal-50 dark:bg-teal-900/20 text-teal-600 dark:text-teal-400 border-teal-200 dark:border-teal-800' }}
                ">{{ str_replace('_', ' ', $project->status) }}</span>

                @if(in_array($project->status, ['approved', 'completed']))
                <a href="{{ route('projects.studio', $project) }}" class="flex items-center gap-2 px-4 py-1.5 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-rose-600 dark:hover:bg-rose-500 transition shadow-lg">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.477.859h4z"></path></svg>
                    <span>Open Studio</span>
                </a>
                @else
                <div class="flex items-center gap-2 px-4 py-1.5 bg-zinc-200 dark:bg-zinc-800 text-zinc-400 text-[10px] font-black uppercase tracking-widest rounded-lg border border-zinc-300 dark:border-zinc-700 cursor-not-allowed group relative">
                    <svg class="w-3.5 h-3.5 opacity-50" fill="currentColor" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"></path></svg>
                    <span>Studio Locked</span>
                    <div class="absolute bottom-full mb-2 hidden group-hover:block bg-black text-white text-[8px] p-2 rounded whitespace-nowrap z-50">Chapters must be finalized/approved first.</div>
                </div>
                @endif
            </div>
        </div>

        @if(($project->status === 'waiting_for_strategy_selection' || request('concept')) && $project->generatedTitles->count() > 0)
        @php
            $initialIndex = 0;
            if (request('concept')) {
                $foundIndex = $project->generatedTitles->search(fn($t) => $t->id == request('concept'));
                if ($foundIndex !== false) $initialIndex = $foundIndex;
            }
        @endphp
        <div class="max-w-7xl mx-auto mb-20" x-data="{ 
            selectedStrategyIndex: @js($initialIndex),
            strategies: @js($project->generatedTitles),
            bookmarks: @js($project->generatedTitles->mapWithKeys(fn($t) => [$t->title => ['id' => $t->id, 'is_saved' => $t->is_saved, 'thumbnail_url' => $t->thumbnail_url, 'thumbnail_status' => $t->thumbnail_status]])),
            get currentStrategy() { return this.strategies[this.selectedStrategyIndex] || {}; },
            get currentThumbnail() { return this.currentStrategy.thumbnail_concept || ''; },
            get currentMegaHook() { return this.currentStrategy.mega_hook || ''; },
            get currentThumbnailUrl() { return this.currentStrategy.thumbnail_url || null; },
            get currentThumbnailStatus() { return this.currentStrategy.thumbnail_status || 'pending'; },
            isRegeneratingHook: false,
            isRegeneratingThumbnail: false,
            copyNotify: false,
            copyType: '',
            async toggleBookmark(title) {
                const bookmark = this.bookmarks[title];
                if (!bookmark) return;

                try {
                    const res = await fetch(`/projects/titles/${bookmark.id}/toggle-bookmark`, {
                        method: 'POST',
                        headers: { 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.bookmarks[title].is_saved = data.is_saved;
                    }
                } catch (e) {
                    console.error('Bookmark toggle failed', e);
                }
            },
            copyToClipboard(text, type) {
                if (!text) return;
                navigator.clipboard.writeText(text).then(() => {
                    this.copyNotify = true;
                    this.copyType = type;
                    setTimeout(() => this.copyNotify = false, 2000);
                });
            },
            async regenerateHook(title) {
                if (!title) return;
                const bookmark = this.bookmarks[title];
                if (!bookmark) return;

                this.isRegeneratingHook = true;
                try {
                    const res = await fetch(`/projects/{{ $project->id }}/regenerate-hook`, {
                        method: 'POST',
                        headers: { 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ title_id: bookmark.id })
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.pollStatus(bookmark.id, 'hook');
                    } else {
                        this.isRegeneratingHook = false;
                    }
                } catch (e) {
                    console.error('Hook regeneration failed', e);
                    this.isRegeneratingHook = false;
                }
            },
            async regenerateThumbnail(title) {
                if (!title) return;
                const bookmark = this.bookmarks[title];
                if (!bookmark) return;

                this.isRegeneratingThumbnail = true;
                try {
                    const res = await fetch(`/projects/{{ $project->id }}/regenerate-thumbnail`, {
                        method: 'POST',
                        headers: { 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ title_id: bookmark.id })
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.pollStatus(bookmark.id, 'thumbnail');
                    } else {
                        this.isRegeneratingThumbnail = false;
                    }
                } catch (e) {
                    console.error('Thumbnail regeneration failed', e);
                    this.isRegeneratingThumbnail = false;
                }
            },
            async generateImage(titleId) {
                if (!titleId) return;
                
                const strategy = this.strategies.find(s => s.id === titleId);
                if (strategy) {
                    strategy.thumbnail_status = 'generating';
                }

                try {
                    const res = await fetch(`/projects/titles/${titleId}/generate-image`, {
                        method: 'POST',
                        headers: { 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.pollStatus(titleId, 'thumbnail');
                    }
                } catch (e) {
                    console.error('Image generation failed', e);
                    if (strategy) strategy.thumbnail_status = 'failed';
                }
            },
            async pollStatus(titleId, type) {
                const maxAttempts = 30;
                let attempts = 0;
                
                const interval = setInterval(async () => {
                    attempts++;
                    if (attempts > maxAttempts) {
                        clearInterval(interval);
                        if (type === 'hook') this.isRegeneratingHook = false;
                        if (type === 'thumbnail') this.isRegeneratingThumbnail = false;
                        return;
                    }

                    try {
                        const res = await fetch(`/projects/titles/${titleId}/status`);
                        const data = await res.json();
                        
                        if (data.success) {
                            const strategy = this.strategies.find(s => s.id === titleId);
                            if (strategy) {
                                if (type === 'hook' && data.mega_hook !== strategy.mega_hook) {
                                    strategy.mega_hook = data.mega_hook;
                                    this.isRegeneratingHook = false;
                                    clearInterval(interval);
                                }
                                
                                if (type === 'thumbnail') {
                                    if (data.thumbnail_status !== strategy.thumbnail_status || 
                                        data.thumbnail_url !== strategy.thumbnail_url || 
                                        data.thumbnail_concept !== strategy.thumbnail_concept) {
                                        
                                        strategy.thumbnail_status = data.thumbnail_status;
                                        strategy.thumbnail_url = data.thumbnail_url;
                                        strategy.thumbnail_concept = data.thumbnail_concept;
                                    }

                                    if (data.thumbnail_status === 'completed' || data.thumbnail_status === 'failed') {
                                        this.isRegeneratingThumbnail = false;
                                        clearInterval(interval);
                                    }
                                }
                            }
                        }
                    } catch (e) {
                        console.error('Status poll failed', e);
                    }
                }, 3000);
            },
            init() {
                this.$watch('selectedStrategyIndex', (val) => {
                    const title = this.strategies[val]?.title || @js($project->selected_title ?? $project->topic);
                    window.dispatchEvent(new CustomEvent('title-updated', { detail: title }));
                });
                // Initial update
                const initialTitle = this.strategies[this.selectedStrategyIndex]?.title || @js($project->selected_title ?? $project->topic);
                window.dispatchEvent(new CustomEvent('title-updated', { detail: initialTitle }));
            }
        }">
            <!-- Strategy Selection Center -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <div class="lg:col-span-12 mb-8">
                    <h2 class="text-3xl font-extrabold mb-2">Viral Concept selection</h2>
                    <p class="text-zinc-500 dark:text-zinc-400">Architect your narrative path. Select one of the 5 AI-optimized directions.</p>
                </div>

                <!-- LEFT: CONCEPT CARDS -->
                <div class="lg:col-span-5 space-y-4">
                    <template x-for="(strategy, index) in strategies" :key="index">
                        <button 
                            type="button"
                            @click="selectedStrategyIndex = index"
                            :class="selectedStrategyIndex === index ? 'border-teal-500 bg-teal-500/5 ring-1 ring-teal-500' : 'border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 hover:border-zinc-300 dark:hover:border-zinc-700 shadow-sm dark:shadow-none'"
                            class="w-full p-6 pb-12 rounded-[32px] border text-left transition-all group relative overflow-hidden"
                        >
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-[10px] font-black tracking-[0.2em] uppercase text-zinc-400 dark:text-zinc-500" x-text="'Vector 0' + (index + 1)"></span>
                                <div x-show="selectedStrategyIndex === index" class="w-2 h-2 bg-teal-500 rounded-full shadow-[0_0_10px_rgba(20,184,166,0.8)]"></div>
                            </div>
                            <h3 class="text-xl font-black text-zinc-900 dark:text-white leading-tight mb-2" x-text="strategy.title"></h3>
                            <div class="flex items-center gap-2">
                                <span class="text-[9px] font-black text-teal-600 dark:text-teal-400 uppercase tracking-widest">Optimized Probability</span>
                                <div class="flex gap-0.5">
                                    <template x-for="i in 5">
                                        <div :class="i <= (5 - index) ? 'bg-teal-500' : 'bg-zinc-200 dark:bg-zinc-800'" class="w-2 h-1 rounded-full"></div>
                                    </template>
                                </div>
                            </div>
                        </button>
                    </template>
                </div>

                <!-- RIGHT: BRIEFING PANEL -->
                <div class="lg:col-span-7">
                    <div class="bg-zinc-50 dark:bg-zinc-900 rounded-[40px] border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-2xl dark:shadow-none">
                        <div class="p-8 border-b border-zinc-200 dark:border-zinc-800 bg-gradient-to-br from-white to-zinc-50 dark:from-zinc-900 dark:to-zinc-800 flex items-center justify-between">
                            <h3 class="text-[10px] font-black tracking-[0.3em] uppercase text-zinc-400 dark:text-zinc-500">Targeting Architecture</h3>
                            
                            <!-- Bookmark Toggle -->
                            <button @click="toggleBookmark(currentStrategy.title)" class="focus:outline-none transition-transform hover:scale-110 active:scale-95 group/save">
                                <div class="flex items-center gap-2">
                                    <span class="text-[9px] font-black uppercase tracking-widest transition-colors" :class="bookmarks[currentStrategy.title]?.is_saved ? 'text-teal-500' : 'text-zinc-400 group-hover/save:text-zinc-600 dark:group-hover/save:text-zinc-300'" x-text="bookmarks[currentStrategy.title]?.is_saved ? 'Saved to Vault' : 'Save Concept'"></span>
                                    <svg class="w-5 h-5 transition-all" 
                                        :class="bookmarks[currentStrategy.title]?.is_saved ? 'text-teal-500 fill-current' : 'text-zinc-300 dark:text-zinc-600 group-hover/save:text-zinc-400'"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 20V4z"></path>
                                    </svg>
                                </div>
                            </button>
                        </div>
                        
                        <div class="p-10 space-y-12">
                            <!-- Hook Section -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <p class="text-[10px] text-teal-600 dark:text-teal-400 font-black uppercase tracking-widest">Retention Hook</p>
                                    <button @click="regenerateHook(currentStrategy.title)" class="text-[9px] font-black text-zinc-400 hover:text-teal-500 uppercase tracking-widest transition-colors flex items-center gap-2">
                                        <svg :class="isRegeneratingHook ? 'animate-spin' : ''" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        Regenerate Hook
                                    </button>
                                </div>
                                <div class="p-6 rounded-[24px] bg-white dark:bg-black/40 border border-zinc-100 dark:border-zinc-800 italic text-zinc-700 dark:text-zinc-300 font-medium leading-relaxed relative group">
                                    <span x-text="currentMegaHook"></span>
                                    <button @click="copyToClipboard(currentMegaHook, 'Hook')" class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity text-zinc-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 01-2-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Thumbnail Section -->
                            <div class="space-y-6">
                                <div class="flex items-center justify-between">
                                    <p class="text-[10px] text-cyan-600 dark:text-cyan-400 font-black uppercase tracking-widest">Visual Core Prompt</p>
                                    <button @click="regenerateThumbnail(currentStrategy.title)" class="text-[9px] font-black text-zinc-400 hover:text-cyan-500 uppercase tracking-widest transition-colors flex items-center gap-2">
                                        <svg :class="isRegeneratingThumbnail ? 'animate-spin' : ''" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        Re-Architect Visuals
                                    </button>
                                </div>

                                <div class="aspect-video rounded-[32px] bg-white dark:bg-black/60 border border-zinc-100 dark:border-zinc-800 overflow-hidden relative group">
                                    <template x-if="currentThumbnailUrl">
                                        <div class="relative w-full h-full group/img">
                                            <img :src="currentThumbnailUrl" class="w-full h-full object-cover">
                                            
                                            <!-- Download Overlay -->
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center gap-4">
                                                <a :href="currentThumbnailUrl" target="_blank" download class="bg-white text-black px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:scale-105 transition active:scale-95 flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                    Download High-Res
                                                </a>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!currentThumbnailUrl">
                                        <div class="w-full h-full flex flex-col items-center justify-center space-y-4">
                                            <div class="w-16 h-16 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-300 dark:text-zinc-600">
                                                <svg x-show="currentThumbnailStatus !== 'generating'" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                <svg x-show="currentThumbnailStatus === 'generating'" class="w-8 h-8 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            </div>
                                            <button @click="generateImage(currentStrategy.id)" x-show="currentThumbnailStatus !== 'generating'" class="text-[10px] font-black uppercase tracking-widest text-teal-500 hover:text-teal-400 transition-colors">Generate High-Fidelity Preview</button>
                                        </div>
                                    </template>
                                </div>
                                
                                <div class="p-6 rounded-[24px] bg-zinc-100 dark:bg-white/5 border border-zinc-200 dark:border-transparent text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed" x-text="currentThumbnail"></div>
                            </div>

                            <!-- Launch Button -->
                            <div x-data="{ isLaunching: false }">
                                @if($project->status === 'waiting_for_strategy_selection')
                                <form action="{{ route('projects.select-strategy', $project) }}" method="POST" @submit="isLaunching = true">
                                    @csrf
                                    <input type="hidden" name="strategy_index" :value="selectedStrategyIndex">
                                    <button type="submit" 
                                        :disabled="isLaunching"
                                        class="w-full py-6 rounded-3xl bg-gradient-to-r from-teal-500 to-cyan-600 text-white dark:text-black font-black text-lg shadow-xl dark:shadow-[0_20px_50px_rgba(20,184,166,0.2)] hover:shadow-teal-500/40 hover:-translate-y-1 active:scale-95 transition-all flex items-center justify-center gap-3 border-t border-white/20"
                                    >
                                        <span x-show="!isLaunching">LAUNCH MISSION: VECTOR <span x-text="selectedStrategyIndex + 1"></span></span>
                                        <span x-show="isLaunching" class="flex items-center gap-2 italic">
                                            <svg class="animate-spin h-5 w-5 text-current" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            Inhabiting Narrative...
                                        </span>
                                    </button>
                                </form>
                                @else
                                <div class="space-y-4">
                                    <template x-if="currentStrategy.title === @js($project->selected_title)">
                                        <div class="w-full py-4 px-6 rounded-2xl bg-zinc-100 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center gap-2 text-[10px] font-black uppercase tracking-widest text-zinc-500">
                                            <svg class="w-4 h-4 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            Currently Active In This Mission
                                        </div>
                                    </template>
                                    <template x-if="currentStrategy.title !== @js($project->selected_title)">
                                        <form :action="`/projects/titles/${currentStrategy.id}/clone`" method="POST" @submit="isLaunching = true">
                                            @csrf
                                            <button type="submit" 
                                                :disabled="isLaunching"
                                                class="w-full py-5 rounded-[28px] bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-black text-xs uppercase tracking-widest shadow-xl hover:-translate-y-1 active:scale-95 transition-all flex items-center justify-center gap-3 border border-zinc-700 dark:border-zinc-200"
                                            >
                                                <span x-show="!isLaunching">Launch as New Mission</span>
                                                <span x-show="isLaunching" class="flex items-center gap-2 italic">
                                                    <svg class="animate-spin h-5 w-5 text-current" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                    Cloning Vector...
                                                </span>
                                            </button>
                                        </form>
                                    </template>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        @endif

        @php
            $statusesToHideChapters = ['waiting_for_concept_selection', 'waiting_for_strategy_selection'];
        @endphp
        <div class="" 
             x-show="'{{ $project->status }}' !== 'waiting_for_concept_selection' && '{{ $project->status }}' !== 'waiting_for_strategy_selection'"
             x-data="{ activeTab: 1 }">
            
            <!-- System Diagnostics (Loading / Failed) -->
            @if(in_array($project->status, ['pending', 'failed', 'generating_concepts', 'generating_strategies', 'architecting_chapters', 'generating_structure', 'generating_monthly_plan']))
            <div class="max-w-4xl mx-auto">
                <div class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[40px] p-12 text-center relative overflow-hidden" 
                     x-data="{ dots: '.' }" 
                     x-init="setInterval(() => dots = dots.length >= 3 ? '.' : dots + '.', 500)">
                    
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-teal-500/5 to-transparent opacity-50"></div>

                    <div class="relative z-10">
                        <div class="w-20 h-20 bg-zinc-900 dark:bg-white rounded-full flex items-center justify-center mx-auto mb-8 shadow-2xl">
                            <svg class="w-10 h-10 {{ $project->status === 'failed' ? 'text-red-500' : 'text-teal-500 animate-spin' }}" fill="none" viewBox="0 0 24 24">
                                @if($project->status === 'failed')
                                    <path stroke="currentColor" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                @else
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                @endif
                            </svg>
                        </div>

                        <h2 class="text-2xl font-black mb-2 uppercase tracking-tight">
                            @if($project->status === 'failed') <span class="text-red-600">MISSION TERMINATED</span>
                            @elseif($project->status === 'generating_concepts') ARCHITECTING VECTORS
                            @elseif($project->status === 'generating_strategies') ARCHITECTING VECTORS
                            @elseif($project->status === 'generating_monthly_plan' || $project->status === 'generating_structure' || $project->status === 'architecting_chapters') ENGINEERING CORE
                            @elseif($project->status === 'generating_thumbnail_concept') FINALIZING VISUALS
                            @elseif($project->status === 'generating_chapters') CRAFTING NARRATIVE
                            @elseif($project->status === 'pending') QUEUEING MISSION
                            @else INITIALIZING MISSION @endif
                        </h2>

                        <p class="text-zinc-500 dark:text-zinc-400 font-medium max-w-md mx-auto mb-10 uppercase text-[10px] tracking-widest">
                            @if($project->status === 'failed') Critical Engine Failure. Deployment aborted.
                            @elseif($project->status === 'generating_concepts' || $project->status === 'generating_strategies') Calculating viral trajectories for your niche<span x-text="dots"></span>
                            @elseif($project->status === 'generating_monthly_plan' || $project->status === 'generating_structure' || $project->status === 'architecting_chapters') Mapping cinematic arcs and narrative beats<span x-text="dots"></span>
                            @elseif($project->status === 'pending') Positioning in deployment queue<span x-text="dots"></span>
                            @else AI is synthesizing high-retention concepts<span x-text="dots"></span> @endif
                        </p>

                        @if($project->status === 'failed')
                            <form action="{{ route('projects.retry', $project) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-red-600 text-white px-10 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-red-700 transition shadow-xl active:scale-95">
                                    RE-IGNITE ENGINE
                                </button>
                            </form>
                        @endif

                        <div class="mt-12 pt-12 border-t border-zinc-200 dark:border-zinc-800 text-left">
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-4">Diagnostics</p>
                            <div class="space-y-2">
                                <div class="flex items-center gap-3 text-[10px] text-zinc-600 dark:text-zinc-400 font-bold uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 bg-teal-500 rounded-full shadow-[0_0_5px_teal]"></span>
                                    Niche Analysis: {{ $project->niche }} [LOCKED]
                                </div>
                                <div class="flex items-center gap-3 text-[10px] text-zinc-600 dark:text-zinc-400 font-bold uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 bg-teal-500 rounded-full shadow-[0_0_5px_teal]"></span>
                                    Geo-Targeting: {{ $project->tier1_country }} [ACTIVE]
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($project->status !== 'failed')
                    <meta http-equiv="refresh" content="5">
                @endif
            </div>
            @else

            <!-- 3-COLUMN MISSION CONTROL LAYOUT -->
            <div class="flex gap-0 min-h-[70vh]">

                <!-- LEFT SIDEBAR: CHAPTER PILLS -->
                <div class="w-52 flex-shrink-0 pr-4 py-2 border-r border-zinc-200 dark:border-zinc-800">
                    <p class="text-[10px] font-black text-zinc-400 dark:text-zinc-500 uppercase tracking-[0.3em] mb-4 px-2">Chapters</p>
                    <div class="space-y-1">
                        @foreach($project->chapters as $chapter)
                        <button
                            @click="activeTab = {{ $chapter->chapter_number }}"
                            :class="activeTab === {{ $chapter->chapter_number }}
                                ? 'bg-rose-600 text-white shadow-lg shadow-rose-600/30'
                                : 'bg-transparent text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white'"
                            class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-left transition-all duration-200 group"
                        >
                            <span
                                :class="activeTab === {{ $chapter->chapter_number }} ? 'bg-white/20 text-white' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400'"
                                class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-black flex-shrink-0 transition-colors">
                                {{ $chapter->chapter_number }}
                            </span>
                            <span class="text-[11px] font-bold leading-tight truncate">{{ $chapter->title }}</span>
                            @if($chapter->status === 'approved')
                                <span class="ml-auto w-1.5 h-1.5 bg-teal-400 rounded-full flex-shrink-0"></span>
                            @elseif($chapter->status === 'generating')
                                <span class="ml-auto w-1.5 h-1.5 bg-amber-400 rounded-full flex-shrink-0 animate-pulse"></span>
                            @endif
                        </button>
                        @endforeach

                        @if($project->chapters->isEmpty())
                            <p class="text-xs text-zinc-400 italic px-2 py-3">No chapters yet</p>
                        @endif
                    </div>
                </div>

                <!-- CENTER: MAIN CHAPTER VIEWPORT -->
                <div class="flex-1 min-w-0 px-8 py-2">
                    @foreach($project->chapters as $chapter)
                    <div x-show="activeTab === {{ $chapter->chapter_number }}"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0">

                        {{-- Chapter Header --}}
                        <div class="flex items-start justify-between mb-8 pb-5 border-b border-zinc-200 dark:border-zinc-800">
                            <div>
                                <div class="flex items-center gap-2 mb-2 flex-wrap">
                                    <span class="text-[10px] font-black text-rose-600 dark:text-rose-400 uppercase tracking-widest">Chapter {{ $chapter->chapter_number }}</span>
                                    @if($project->duration_minutes)
                                        <span class="text-zinc-300 dark:text-zinc-600 text-xs">·</span>
                                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Marathon Mode: {{ $project->duration_minutes }}hr Content Goal</span>
                                        <span class="text-zinc-300 dark:text-zinc-600 text-xs">·</span>
                                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Completed: {{ $project->chapters->where('status','approved')->count() }}/{{ $project->chapters->count() }}</span>
                                    @endif
                                </div>
                                <h2 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">{{ $chapter->title }}</h2>
                            </div>
                            @if($chapter->status === 'pending')
                                <form action="{{ route('projects.chapters.architect', [$project, $chapter]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-zinc-900 dark:bg-white text-white dark:text-black px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest hover:scale-[1.02] active:scale-95 transition-all shadow-md whitespace-nowrap">
                                        Architect Chapter {{ $chapter->chapter_number }}
                                    </button>
                                </form>
                            @elseif($chapter->status === 'completed')
                                <form action="{{ route('projects.chapters.approve', [$project, $chapter]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="bg-teal-500 text-white px-5 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-teal-600 active:scale-95 transition-all shadow-md whitespace-nowrap flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Authorize Phase
                                    </button>
                                </form>
                            @elseif($chapter->status === 'approved')
                                <span class="flex items-center gap-2 text-teal-500 text-[11px] font-black uppercase tracking-widest">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    Mission Ready
                                </span>
                            @endif
                        </div>

                        {{-- Chapter Content --}}
                        @if(in_array($chapter->status, ['completed', 'approved']))
                            <div class="space-y-12">
                                @foreach($chapter->scenes as $scene)
                                <div class="flex flex-col lg:flex-row gap-8 group">
                                    <div class="flex-1 space-y-3">
                                        <div class="flex items-center gap-3">
                                            <span class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.4em]">Node {{ str_pad($scene->scene_number, 2, '0', STR_PAD_LEFT) }}</span>
                                            <div class="h-px flex-1 bg-zinc-200 dark:bg-zinc-800"></div>
                                        </div>
                                        <p class="text-base font-medium text-zinc-700 dark:text-zinc-300 leading-relaxed">{{ $scene->narration_text }}</p>
                                    </div>
                                    <div class="w-full lg:w-64 xl:w-72 flex-shrink-0">
                                        <div class="aspect-video bg-zinc-100 dark:bg-zinc-900 rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800 relative group/img">
                                            @if($scene->image_url)
                                                <img src="{{ url($scene->image_url) }}" class="w-full h-full object-cover group-hover/img:scale-105 transition-transform duration-500">
                                                <a href="{{ url($scene->image_url) }}" download class="absolute top-2 right-2 opacity-0 group-hover/img:opacity-100 transition bg-black/70 p-2 rounded-xl text-white hover:bg-teal-500" title="Download image">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                </a>
                                            @else
                                                <div class="w-full h-full" x-data="{
                                                    imageUrl: null,
                                                    isGenerating: false,
                                                    pollInterval: null,
                                                    generateImage() {
                                                        this.isGenerating = true;
                                                        // Trigger generation
                                                        fetch('{{ route('projects.scenes.generate-image', [$project, $chapter, $scene]) }}', {
                                                            method: 'POST',
                                                            headers: {
                                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                                'Accept': 'application/json'
                                                            }
                                                        });
                                                        
                                                        // Start polling
                                                        this.pollImage();
                                                    },
                                                    pollImage() {
                                                        this.pollInterval = setInterval(async () => {
                                                            try {
                                                                const res = await fetch('{{ route('projects.scenes.image-status', [$project, $chapter, $scene]) }}');
                                                                const data = await res.json();
                                                                if (data.image_url) {
                                                                    this.imageUrl = data.image_url;
                                                                    this.isGenerating = false;
                                                                    clearInterval(this.pollInterval);
                                                                }
                                                            } catch (e) {
                                                                console.error('Polling failed', e);
                                                            }
                                                        }, 5000);
                                                    }
                                                }">
                                                    
                                                    <!-- Loading State -->
                                                    <div x-show="isGenerating" class="w-full h-full flex flex-col items-center justify-center gap-3 p-4 bg-zinc-900/50 absolute inset-0 z-10">
                                                        <div class="w-8 h-8 border-2 border-rose-500/20 border-t-rose-500 rounded-full animate-spin"></div>
                                                        <span class="text-[9px] font-black text-rose-500 uppercase tracking-widest animate-pulse">Rendering</span>
                                                    </div>

                                                    <!-- Result State (Loaded via AJAX) -->
                                                    <template x-if="imageUrl">
                                                        <div class="w-full h-full absolute inset-0 z-20">
                                                            <img :src="imageUrl" class="w-full h-full object-cover group-hover/img:scale-105 transition-transform duration-500">
                                                            <a :href="imageUrl" download class="absolute top-2 right-2 opacity-0 group-hover/img:opacity-100 transition bg-black/70 p-2 rounded-xl text-white hover:bg-teal-500" title="Download image">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                            </a>
                                                        </div>
                                                    </template>

                                                    <!-- Initial State / Trigger Button -->
                                                    <div x-show="!imageUrl && !isGenerating" class="w-full h-full flex flex-col items-center justify-center gap-3 p-4">
                                                        <svg class="w-7 h-7 text-zinc-300 dark:text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                        <button type="button" @click="generateImage" class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-xl font-black text-[10px] uppercase tracking-widest transition active:scale-95 flex items-center gap-1.5 shadow-lg shadow-rose-600/20">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                            Generate Image
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="absolute bottom-2 left-2 bg-black/60 text-white text-[9px] font-black px-2 py-0.5 rounded-lg">{{ $scene->duration_seconds }}s</div>
                                        </div>
                                        @if($scene->visual_prompt)
                                        <p class="mt-2 text-[10px] text-zinc-400 leading-snug px-1">{{ Str::limit($scene->visual_prompt, 90) }}</p>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>

                        @elseif($chapter->status === 'pending')
                            {{-- "Chapter Script Needed" — matching reference image exactly --}}
                            <div class="flex flex-col items-center justify-center py-24 text-center">
                                <div class="w-16 h-16 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center mb-6">
                                    <svg class="w-8 h-8 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <h3 class="text-2xl font-black text-zinc-800 dark:text-zinc-200 italic mb-3" style="font-family: Georgia, serif;">Chapter Script Needed</h3>
                                <p class="text-zinc-400 text-sm mb-10 leading-relaxed">Architect this chapter to contribute to your {{ $project->duration_minutes }}-hour<br>marathon goal.</p>
                                <form action="{{ route('projects.chapters.architect', [$project, $chapter]) }}" method="POST" class="w-full max-w-sm">
                                    @csrf
                                    <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-lg shadow-rose-600/20 hover:shadow-rose-600/40 hover:-translate-y-0.5 active:scale-95 transition-all">
                                        ARCHITECT CHAPTER {{ $chapter->chapter_number }} (300W/SCENE)
                                    </button>
                                </form>
                            </div>

                        @elseif($chapter->status === 'generating')
                            <div class="flex flex-col items-center justify-center py-28">
                                <div class="w-12 h-12 border-4 border-rose-500/20 border-t-rose-500 rounded-full animate-spin mb-6"></div>
                                <h3 class="text-xl font-black uppercase tracking-tight mb-2">Writing Chapter {{ $chapter->chapter_number }}</h3>
                                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-rose-500 animate-pulse">AI Narrative Models Active</p>
                                <meta http-equiv="refresh" content="10">
                            </div>
                        @endif

                    </div>
                    @endforeach

                    @if($project->chapters->isEmpty())
                        <div class="flex flex-col items-center justify-center py-32 text-center">
                            <div class="w-16 h-16 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center mb-5">
                                <svg class="w-8 h-8 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <h3 class="text-lg font-black text-zinc-400 italic">No chapters yet</h3>
                            <p class="text-sm text-zinc-400 mt-1">Complete strategy selection to begin chapter architecture.</p>
                        </div>
                    @endif
                </div>

                <!-- RIGHT SIDEBAR: CHARACTER ROSTER -->
                <div class="w-60 flex-shrink-0 pl-6 py-2 border-l border-zinc-200 dark:border-zinc-800 sticky top-8 self-start overflow-y-auto max-h-[calc(100vh-6rem)]">
                    <p class="text-[10px] font-black text-rose-500 uppercase tracking-[0.3em] mb-4 px-1">Character Roster</p>

                    @if($project->character_profiles && count($project->character_profiles) > 0)
                        <div class="space-y-3">
                            @foreach($project->character_profiles as $char)
                            <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-zinc-100 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700 transition-all">
                                <h4 class="font-black text-[12px] text-zinc-900 dark:text-white uppercase tracking-tight leading-tight">{{ $char['name'] ?? 'Unknown' }}</h4>
                                <p class="text-[9px] font-black text-rose-500 uppercase tracking-widest mt-0.5 mb-2">{{ $char['role'] ?? $char['archetype'] ?? 'Mission Asset' }}</p>
                                @if(!empty($char['personality']))
                                <p class="text-[10px] leading-relaxed text-zinc-600 dark:text-zinc-300 mb-1.5 italic">{{ $char['personality'] }}</p>
                                @endif
                                @if(!empty($char['appearance']))
                                <p class="text-[9px] text-zinc-400 leading-relaxed border-t border-zinc-100 dark:border-zinc-800 pt-1.5 mt-1.5">
                                    <span class="font-black text-zinc-500 dark:text-zinc-500 not-italic uppercase tracking-wider">Look:</span> {{ $char['appearance'] }}
                                </p>
                                @endif
                                @if(!empty($char['backstory']) || !empty($char['background']))
                                <p class="text-[9px] text-zinc-400 leading-relaxed border-t border-zinc-100 dark:border-zinc-800 pt-1.5 mt-1.5">
                                    <span class="font-black text-zinc-500 dark:text-zinc-500 not-italic uppercase tracking-wider">Story:</span> {{ Str::limit($char['backstory'] ?? $char['background'], 100) }}
                                </p>
                                @endif
                                @if(!empty($char['speaking_style']))
                                <p class="text-[9px] text-zinc-400 leading-relaxed border-t border-zinc-100 dark:border-zinc-800 pt-1.5 mt-1.5">
                                    <span class="font-black text-zinc-500 dark:text-zinc-500 not-italic uppercase tracking-wider">Voice:</span> {{ $char['speaking_style'] }}
                                </p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-5 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-800 text-center">
                            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Profiles Pending</p>
                            <p class="text-[10px] text-zinc-400 mt-1">Generated during architecture</p>
                        </div>
                    @endif

                    @if($project->mega_hook)
                    <div class="mt-4 bg-zinc-900 dark:bg-black rounded-2xl p-4 text-white relative overflow-hidden">
                        <div class="absolute -right-3 -top-3 w-14 h-14 bg-rose-500/10 rounded-full blur-xl"></div>
                        <p class="text-[8px] font-black uppercase tracking-[0.2em] mb-2 opacity-30">Hook</p>
                        <p class="text-xs leading-relaxed text-zinc-300 italic">"{{ Str::limit($project->mega_hook, 150) }}"</p>
                    </div>
                    @endif

                    <a href="{{ route('projects.export', $project) }}" class="mt-4 flex items-center justify-center gap-2 w-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-300 px-4 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Export Payload
                    </a>
                </div>

            </div>
            @endif
        </div>
    </div>

</x-app-layout>
