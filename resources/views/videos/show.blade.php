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
                    {{ $project->status === 'waiting_for_title_selection' ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800' : '' }}
                    {{ in_array($project->status, ['generating_concepts','generating_strategies','architecting_chapters','generating_structure']) ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800' : '' }}
                    {{ $project->status === 'waiting_for_title_selection' || in_array($project->status, ['generating_concepts','generating_strategies','architecting_chapters','generating_structure']) ? '' : 'bg-teal-50 dark:bg-teal-900/20 text-teal-600 dark:text-teal-400 border-teal-200 dark:border-teal-800' }}
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

        @if(in_array($project->status, ['waiting_for_title_selection', 'generating_concept_details', 'waiting_for_launch']) || request('concept'))
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
            strategies: @js($project->generatedTitles->map(fn($t) => [
                'id' => $t->id,
                'title' => $t->title,
                'mega_hook' => $t->mega_hook,
                'thumbnail_concept' => $t->thumbnail_concept,
                'thumbnail_url' => $t->thumbnail_url,
                'thumbnail_status' => $t->thumbnail_status,
                'short_script' => $t->short_script
            ])),
            selectedStrategyIndex: @js($project->generatedTitles->search(fn($t) => $t->title === $project->selected_title) ?: 0),
            isRegeneratingHook: false,
            isRegeneratingThumbnail: false,
            isLaunching: false,
            bookmarks: @js($project->generatedTitles->pluck('is_saved', 'title')),
            
            get currentStrategy() {
                return this.strategies[this.selectedStrategyIndex] || {};
            },
            get currentMegaHook() {
                return this.currentStrategy.mega_hook || 'Architecting retention hook...';
            },
            get currentThumbnail() {
                return this.currentStrategy.thumbnail_concept || 'Synthesizing visual core...';
            },
            get currentThumbnailUrl() {
                return this.currentStrategy.thumbnail_url || null;
            },
            get currentThumbnailStatus() {
                return this.currentStrategy.thumbnail_status || 'pending';
            },
            get currentShortScript() {
                return this.currentStrategy.short_script || { scene: 'Visualizing opening...', narration: 'Drafting impact copy...' };
            },

            copyToClipboard(text, type) {
                navigator.clipboard.writeText(text);
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: `${type} copied to clipboard`, type: 'success' } }));
            },
            async pollStatus(titleId) {
                const interval = setInterval(async () => {
                    try {
                        const res = await fetch(`/projects/titles/${titleId}/status`);
                        const data = await res.json();
                        
                        if (data.success) {
                            const strategy = this.strategies.find(s => s.id === titleId);
                            if (strategy) {
                                strategy.mega_hook = data.mega_hook;
                                strategy.thumbnail_concept = data.thumbnail_concept;
                                strategy.thumbnail_url = data.thumbnail_url;
                                strategy.thumbnail_status = data.thumbnail_status;
                                
                                // Reset loading states if data arrived or generation failed/blocked
                                if (['completed', 'failed', 'blocked'].includes(strategy.thumbnail_status)) {
                                    clearInterval(interval);
                                }
                            }
                        }
                    } catch (e) {
                        console.error('Status poll failed', e);
                    }
                }, 3000);
            },
            async selectTitle() {
                if (this.isLaunching) return;
                this.isLaunching = true;
                
                try {
                    const res = await fetch(`/projects/{{ $project->id }}/select-title`, {
                        method: 'POST',
                        headers: { 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ title_id: this.currentStrategy.id })
                    });
                    
                    if (res.redirected) {
                        window.location.href = res.url;
                    }
                } catch (e) {
                    this.isLaunching = false;
                    console.error('Title selection failed', e);
                }
            },
            async toggleBookmark(index, event) {
                event.stopPropagation(); // Prevents clicking the card and selecting the title
                const strategy = this.strategies[index];
                
                try {
                    const res = await fetch(`/projects/titles/${strategy.id}/toggle-bookmark`, {
                        method: 'POST',
                        headers: { 
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await res.json();
                    if (data.success) {
                        this.bookmarks[strategy.title] = data.is_saved;
                        window.dispatchEvent(new CustomEvent('notify', { 
                            detail: { 
                                message: data.is_saved ? 'Concept saved to Data Vault' : 'Concept removed from Data Vault', 
                                type: 'success' 
                            } 
                        }));
                    }
                } catch (e) {
                    console.error('Bookmark toggle failed', e);
                }
            },
            async regenerateHook() {
                if (this.isRegeneratingHook) return;
                this.isRegeneratingHook = true;
                
                try {
                    const res = await fetch(`/projects/{{ $project->id }}/regenerate-hook`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ title_id: this.currentStrategy.id })
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.currentStrategy.mega_hook = data.mega_hook;
                        window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Mega-Hook Regenerated', type: 'success' } }));
                    } else {
                        throw new Error(data.error || 'Failed to regenerate');
                    }
                } catch (e) {
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: e.message, type: 'error' } }));
                } finally {
                    this.isRegeneratingHook = false;
                }
            },
            async regenerateThumbnailPrompt() {
                if (this.isRegeneratingThumbnail) return;
                this.isRegeneratingThumbnail = true;
                
                try {
                    const res = await fetch(`/projects/{{ $project->id }}/regenerate-thumbnail`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ title_id: this.currentStrategy.id })
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.currentStrategy.thumbnail_concept = data.thumbnail_concept;
                        window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Thumbnail Prompt Regenerated', type: 'success' } }));
                    } else {
                        throw new Error(data.error || 'Failed to regenerate');
                    }
                } catch (e) {
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: e.message, type: 'error' } }));
                } finally {
                    this.isRegeneratingThumbnail = false;
                }
            },
            async generateThumbnailImage() {
                if (this.currentStrategy.thumbnail_status === 'generating') return;
                this.currentStrategy.thumbnail_status = 'generating';
                
                try {
                    const res = await fetch(`/projects/titles/${this.currentStrategy.id}/generate-image`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Image Generation Enqueued', type: 'success' } }));
                        // Start polling for this specific title
                        this.pollStatus(this.currentStrategy.id);
                    } else {
                        throw new Error(data.error || 'Failed to generate');
                    }
                } catch (e) {
                    this.currentStrategy.thumbnail_status = 'failed';
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: e.message, type: 'error' } }));
                }
            },
            init() {
                this.$watch('selectedStrategyIndex', (val) => {
                    const title = this.strategies[val]?.title || @js($project->selected_title ?? $project->topic);
                    window.dispatchEvent(new CustomEvent('title-updated', { detail: title }));
                });

                // Auto-resume polling if page loaded with a strategy already generating an image
                this.strategies.forEach(strategy => {
                    if (strategy.thumbnail_status === 'generating') {
                        this.pollStatus(strategy.id);
                    }
                });
            }
        }">
            <!-- STAGE A: TITLE SELECTION -->
            <template x-if="'{{ $project->status }}' === 'waiting_for_title_selection'">
                <div class="space-y-8">
                    <div class="text-center mb-12">
                        <h2 class="text-4xl font-black mb-3 uppercase tracking-tighter">Select Your Winning Vector</h2>
                        <p class="text-zinc-500 dark:text-zinc-400 uppercase text-[11px] tracking-[0.3em] font-bold">5 viral trajectories architected for your niche</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 max-w-3xl mx-auto">
                        <template x-for="(strategy, index) in strategies" :key="index">
                            <div 
                                @click="selectedStrategyIndex = index; selectTitle();"
                                class="group relative w-full p-8 rounded-[32px] border transition-all duration-500 overflow-hidden bg-zinc-50 dark:bg-zinc-900/50 border-zinc-200 dark:border-zinc-800 hover:border-red-500 dark:hover:border-red-500 hover:scale-[1.02] active:scale-[0.98] cursor-pointer"
                            >
                                <div class="absolute inset-0 bg-gradient-to-r from-red-500/0 via-red-500/5 to-red-500/0 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <div class="relative flex items-center justify-between">
                                    <div class="flex items-center gap-6">
                                        <div class="w-12 h-12 rounded-2xl bg-zinc-900 dark:bg-white flex items-center justify-center text-white dark:text-zinc-900 font-black text-xs">
                                            0<span x-text="index + 1"></span>
                                        </div>
                                        <h3 class="text-xl font-black text-zinc-900 dark:text-white group-hover:text-red-500 transition-colors" x-text="strategy.title"></h3>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <!-- Bookmark Button (Heart icon) -->
                                        <button 
                                            @click.stop="toggleBookmark(index, $event)"
                                            class="w-10 h-10 rounded-full flex items-center justify-center transition-colors relative z-20 hover:bg-zinc-200 dark:hover:bg-zinc-800"
                                            :class="bookmarks[strategy.title] ? 'text-red-500 bg-red-50 dark:bg-red-900/20' : 'text-zinc-300 dark:text-zinc-600 hover:text-red-500'"
                                            title="Save to Data Vault"
                                        >
                                            <svg class="w-5 h-5 transition-transform group-hover:scale-110" :fill="bookmarks[strategy.title] ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                            </svg>
                                        </button>
                                        <svg class="w-6 h-6 text-zinc-300 dark:text-zinc-700 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <!-- STAGE B: CONCEPT ARCHITECTURE -->
            <template x-if="'{{ $project->status }}' === 'waiting_for_launch'">
                <div class="max-w-4xl mx-auto space-y-6">
                    <!-- Title Header -->
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500">Select Winning Title</h3>
                        <button class="text-[9px] font-black uppercase tracking-widest text-zinc-400 hover:text-red-500 flex items-center gap-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Regen Titles
                        </button>
                    </div>
                    
                    <div class="w-full p-4 rounded-3xl bg-red-600 text-white font-black text-lg flex items-center justify-between shadow-[0_10px_30px_rgba(220,38,38,0.3)]">
                        <span x-text="currentStrategy.title"></span>
                        <svg class="w-5 h-5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 5h5M5 10h5m-5 5h5m5-10h5m-5 5h5m-5 10h5"></path></svg>
                    </div>

                    <!-- Thumbnail Card -->
                    <div class="bg-zinc-950/40 backdrop-blur-xl border border-white/5 rounded-[32px] p-8 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black uppercase tracking-widest text-red-500">Thumbnail Concept</span>
                            <div class="flex gap-4">
                                <button @click="copyToClipboard(currentThumbnail, 'Thumbnail Prompt')" class="text-[9px] font-black uppercase tracking-widest text-zinc-500 hover:text-white flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 01-2-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    Copy
                                </button>
                                <button @click="regenerateThumbnailPrompt" :disabled="isRegeneratingThumbnail" class="text-[9px] font-black uppercase tracking-widest text-zinc-500 hover:text-white flex items-center gap-1 disabled:opacity-50">
                                    <svg x-show="!isRegeneratingThumbnail" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    <svg x-show="isRegeneratingThumbnail" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Regenerate
                                </button>
                                <button @click="generateThumbnailImage" :disabled="currentStrategy.thumbnail_status === 'generating'" class="text-[9px] font-black uppercase tracking-widest text-red-500 hover:text-red-400 flex items-center gap-1 disabled:opacity-50">
                                    <svg x-show="currentStrategy.thumbnail_status !== 'generating'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <svg x-show="currentStrategy.thumbnail_status === 'generating'" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Generate Image
                                </button>
                            </div>
                        </div>
                        <p class="text-zinc-300 italic leading-relaxed text-sm" x-text="currentThumbnail"></p>
                    </div>

                    <!-- Mega Hook Card -->
                    <div class="bg-zinc-950/40 backdrop-blur-xl border border-white/5 rounded-[32px] p-8 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black uppercase tracking-widest text-red-500">The Mega-Hook (First 30s)</span>
                            <div class="flex gap-4">
                                <button @click="copyToClipboard(currentMegaHook, 'Mega-Hook')" class="text-[9px] font-black uppercase tracking-widest text-zinc-500 hover:text-white flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 01-2-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    Copy
                                </button>
                                <button @click="regenerateHook" :disabled="isRegeneratingHook" class="text-[9px] font-black uppercase tracking-widest text-zinc-500 hover:text-white flex items-center gap-1 disabled:opacity-50">
                                    <svg x-show="!isRegeneratingHook" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    <svg x-show="isRegeneratingHook" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Regenerate
                                </button>
                            </div>
                        </div>
                        <p class="text-zinc-300 italic leading-relaxed text-sm" x-text="currentMegaHook"></p>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-4 pt-4">
                        <form action="{{ route('projects.update', $project) }}" method="POST" class="w-1/3">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="waiting_for_title_selection">
                            <button type="submit" class="w-full py-6 rounded-3xl bg-zinc-900 border border-zinc-800 text-white font-black text-xs uppercase tracking-widest hover:bg-zinc-800 transition shadow-xl active:scale-95">
                                Back
                            </button>
                        </form>
                        <form action="{{ route('projects.launch', $project) }}" method="POST" class="w-2/3" @submit="isLaunching = true">
                            @csrf
                            <button type="submit" 
                                :disabled="isLaunching"
                                class="w-full py-6 rounded-3xl bg-red-600 text-white font-black text-lg shadow-[0_20px_50px_rgba(220,38,38,0.2)] hover:shadow-red-600/40 hover:-translate-y-1 active:scale-95 transition-all flex items-center justify-center gap-3"
                            >
                                <span x-show="!isLaunching">Launch Project</span>
                                <span x-show="isLaunching" class="flex items-center gap-2 italic">
                                    <svg class="animate-spin h-5 w-5 text-current" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Engaging Mission Engine...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </template>
            <!-- STAGE C: ARCHITECTING DETAILS (Loading) -->
            <template x-if="'{{ $project->status }}' === 'generating_concept_details'">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[40px] p-24 text-center relative overflow-hidden">
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_-20%,rgba(220,38,38,0.1),transparent)] flex items-center justify-center">
                            <div class="animate-pulse flex flex-col items-center">
                                <div class="w-20 h-20 rounded-full border-4 border-red-600/20 border-t-red-600 animate-spin mb-8"></div>
                                <h2 class="text-3xl font-black text-white mb-4 italic tracking-tight">Architecting Narrative Details...</h2>
                                <p class="text-zinc-400 font-medium max-w-md mx-auto leading-relaxed">
                                    Our AI is currently synthesizing the mega-hook, thumbnail concept, and script preview for your selected title.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        @endif

        @php
            $statusesToHideChapters = ['waiting_for_concept_selection', 'waiting_for_strategy_selection', 'waiting_for_title_selection', 'generating_concept_details', 'waiting_for_launch', 'failed_stage_2'];
        @endphp
        <div class="" 
             x-show="!@js($statusesToHideChapters).includes('{{ $project->status }}') && '{{ $project->status }}' !== 'generating_concept_details'"
             x-data="{ activeTab: 1 }">
            
            <!-- System Diagnostics (Loading / Failed) -->
            @if(in_array($project->status, ['pending', 'failed', 'generating_concepts', 'generating_strategies', 'generating_concept_details', 'architecting_chapters', 'generating_structure', 'generating_monthly_plan']))
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
                            @if($project->status === 'failed' || $project->status === 'failed_stage_2') <span class="text-red-600">MISSION TERMINATED</span>
                            @elseif($project->status === 'generating_concepts') ARCHITECTING VECTORS
                            @elseif($project->status === 'generating_concept_details') ARCHITECTING CONCEPT
                            @elseif($project->status === 'generating_monthly_plan' || $project->status === 'generating_structure' || $project->status === 'architecting_chapters') ENGINEERING CORE
                            @elseif($project->status === 'generating_thumbnail_concept') FINALIZING VISUALS
                            @elseif($project->status === 'generating_chapters') CRAFTING NARRATIVE
                            @elseif($project->status === 'pending') QUEUEING MISSION
                            @else INITIALIZING MISSION @endif
                        </h2>

                        <p class="text-zinc-500 dark:text-zinc-400 font-medium max-w-md mx-auto mb-10 uppercase text-[10px] tracking-widest">
                            @if($project->status === 'failed' || $project->status === 'failed_stage_2') Critical Engine Failure. Deployment aborted.
                            @elseif($project->status === 'generating_concepts') Calculating viral trajectories for your niche<span x-text="dots"></span>
                            @elseif($project->status === 'generating_concept_details') Synthesizing narrative hook and visual core<span x-text="dots"></span>
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
