<x-app-layout>
    <div x-data="storyStudio()" 
         @start-polling.window="pollStatus($event.detail.sceneId || $event.detail.titleId, null, !!$event.detail.titleId)"
         @tokens-updated.window="tokenBalance = $event.detail.voice_tokens"
         class="max-w-[1600px] mx-auto">
        <div class="flex flex-col lg:flex-row gap-8">
            
            {{-- LEFT COLUMN: STEP 1 & 2 --}}
            <div class="w-full lg:w-96 space-y-6">
                
                {{-- STEP 1: SELECT STORY --}}
                <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
                    <div class="p-6 border-b border-zinc-100 dark:border-zinc-800">
                        <h3 class="text-xs font-black text-purple-600 uppercase tracking-[0.2em] mb-4">Step 1 Select Story</h3>
                        <div class="relative group">
                            <select @change="selectProject($event.target.value)" class="w-full bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-sm font-bold focus:ring-purple-500/20 focus:border-purple-600 transition-all appearance-none cursor-pointer">
                                <option value="">Choose a Story...</option>
                                <template x-for="project in projects" :key="project.id">
                                    <option :value="project.id" :selected="selectedProject?.id == project.id" x-text="(project.is_fully_ready ? '✅ ' : '') + (project.selected_title || project.topic || 'Unnamed Story')"></option>
                                </template>
                            </select>
                            <svg class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none group-hover:text-purple-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                {{-- STEP 2: SELECT CHAPTER --}}
                <div x-show="selectedProject" x-transition.opacity class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
                    <div class="p-6 border-b border-zinc-100 dark:border-zinc-800">
                        <h3 class="text-xs font-black text-purple-600 uppercase tracking-[0.2em]">Step 2 Select Chapter</h3>
                    </div>
                    <div class="max-h-[300px] overflow-y-auto p-2 space-y-1">
                        {{-- PROLOGUE: MEGA HOOK --}}
                        <template x-if="selectedProject?.mega_hook">
                            <button @click="selectedChapter = null; isHookSelected = true"
                                    :class="isHookSelected ? 'bg-rose-600 text-white shadow-lg shadow-rose-500/20' : 'hover:bg-zinc-50 dark:hover:bg-zinc-800 dark:text-zinc-400 text-zinc-600'"
                                    class="w-full text-left px-4 py-3 rounded-xl transition-all duration-200 flex items-center justify-between group">
                                <div>
                                    <span class="font-bold text-sm">Prologue</span>
                                    <p class="text-[10px] opacity-60 font-medium truncate max-w-[180px]">The Mega-Hook (Intro)</p>
                                </div>
                                <span class="text-[9px] font-black uppercase opacity-60">High Impact</span>
                            </button>
                        </template>

                        <template x-for="chapter in selectedProject?.chapters" :key="chapter.id">
                            <button @click="selectChapter(chapter); isHookSelected = false"
                                    :class="selectedChapter?.id === chapter.id ? 'bg-purple-600 text-white shadow-lg shadow-purple-500/20' : 'hover:bg-zinc-50 dark:hover:bg-zinc-800 dark:text-zinc-400 text-zinc-600'"
                                    class="w-full text-left px-4 py-3 rounded-xl transition-all duration-200 flex items-center justify-between group">
                                <div>
                                    <span class="font-bold text-sm" x-text="'Chapter ' + chapter.chapter_number"></span>
                                    <p class="text-[10px] opacity-60 font-medium truncate max-w-[180px]" x-text="chapter.title || 'Untitled'"></p>
                                </div>
                                <span class="text-[9px] font-black uppercase opacity-60" x-text="chapter.scenes.length + ' Scenes'"></span>
                            </button>
                        </template>
                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN: SCENES & FINE-TUNE --}}
            <div class="flex-1 space-y-6">
                
                {{-- HEADER AREA --}}
                <div class="bg-white dark:bg-zinc-900 p-8 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 shadow-sm flex items-center gap-6">
                    <div class="w-16 h-16 bg-purple-600 rounded-3xl flex items-center justify-center text-white shadow-xl shadow-purple-500/30">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                    </div>
                    <div>
                        <h2 class="font-black text-xl text-gray-800 dark:text-white leading-tight uppercase italic tracking-tight">
                            {{ __('Story Voices Studio') }}
                        </h2>
                        <div class="flex items-center gap-4 mt-1">
                            <p class="text-zinc-500 dark:text-zinc-400 font-medium" x-text="selectedProject ? 'Chapter Voice Synchronization Suite' : 'Convert your story scripts into expressive AI voice narration'"></p>
                            <div class="flex items-center gap-2 bg-purple-500/10 border border-purple-500/20 px-3 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 bg-purple-500 rounded-full animate-pulse"></span>
                                <span class="text-[10px] font-black text-purple-600 dark:text-purple-400 uppercase tracking-widest">
                                    <span x-text="tokenBalance"></span> Credits Available
                                </span>
                            </div>
                            <template x-if="selectedProject?.is_fully_ready">
                                <div class="flex items-center gap-2 bg-green-500/10 border border-green-500/20 px-3 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                    <span class="text-[10px] font-black text-green-600 dark:text-green-400 uppercase tracking-widest">Fully Synchronized</span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- SCENES LIST --}}
                <div x-show="selectedChapter" x-transition.opacity class="space-y-6">
                    
                    {{-- CHAPTER NARRAION SUITE (BULK) --}}
                    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-zinc-900 dark:to-indigo-950/30 rounded-[2.5rem] p-8 text-zinc-900 dark:text-white border border-purple-100 dark:border-purple-800/30 shadow-xl shadow-purple-500/5">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                            <div class="space-y-2 text-center md:text-left">
                                <div class="flex items-center justify-center md:justify-start gap-3">
                                    <span class="px-3 py-1 bg-purple-100 dark:bg-purple-500/20 rounded-full text-[10px] font-black uppercase tracking-widest border border-purple-200 dark:border-purple-400/30 text-purple-600 dark:text-purple-400">Bulk Operations</span>
                                    <h2 class="text-xl font-black">Chapter Narration Suite</h2>
                                </div>
                                <p class="text-zinc-500 dark:text-purple-200/60 text-sm font-medium">Synchronize and generate all scene voices with shared settings</p>
                            </div>

                            <div class="flex flex-col sm:flex-row items-center gap-6">
                                {{-- Global Settings --}}
                                <div class="flex gap-4">
                                    <div class="space-y-1">
                                        <label class="text-[9px] font-black uppercase text-purple-500 dark:text-purple-300 tracking-tighter">Speed</label>
                                        <select x-model="globalSettings.speed" class="bg-white dark:bg-purple-900/20 border-purple-200 dark:border-purple-400/20 rounded-lg text-xs font-bold py-1.5 px-3 focus:ring-purple-500 focus:border-purple-500 text-zinc-900 dark:text-white transition-all">
                                            <option value="0.8">0.8x</option>
                                            <option value="0.9">0.9x</option>
                                            <option value="1.0">1.0x</option>
                                            <option value="1.1">1.1x</option>
                                            <option value="1.2">1.2x</option>
                                        </select>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[9px] font-black uppercase text-purple-500 dark:text-purple-300 tracking-tighter">Voice</label>
                                        <select x-model="globalSettings.voice_id" class="bg-white dark:bg-purple-900/20 border-purple-200 dark:border-purple-400/20 rounded-lg text-xs font-bold py-1.5 px-3 focus:ring-purple-500 focus:border-purple-500 text-zinc-900 dark:text-white transition-all">
                                            <option value="af_heart">Heart (F)</option>
                                            <option value="af_nicole">Nicole (F)</option>
                                            <option value="af_sky">Sky (F)</option>
                                            <option value="am_adam">Adam (M)</option>
                                            <option value="am_michael">Michael (M)</option>
                                        </select>
                                    </div>
                                </div>

                                <button @click="bulkGenerate()" :disabled="isBulkGenerating" class="flex flex-col items-center gap-1 bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:from-purple-700 hover:to-indigo-700 transition-all shadow-xl shadow-purple-500/25 active:scale-95 disabled:opacity-50">
                                    <div class="flex items-center gap-3">
                                        <svg x-show="!isBulkGenerating" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        <svg x-show="isBulkGenerating" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span x-text="isBulkGenerating ? 'Synthesizing...' : 'Generate All'"></span>
                                    </div>
                                    <span x-show="!isBulkGenerating" class="text-[8px] opacity-60 tracking-[0.2em]" x-text="(selectedChapter?.scenes.length * tokenCost) + ' Tokens Total'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <template x-for="scene in selectedChapter?.scenes" :key="scene.id">
                        <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 p-6 flex flex-col xl:flex-row gap-8 transition-all hover:border-rose-500/30 group">
                            
                            {{-- Scene Info --}}
                            <div class="flex-1 space-y-4">
                                <div class="flex items-center gap-3">
                                    <span class="text-[10px] font-black text-purple-600 uppercase tracking-[0.3em]" x-text="'Scene ' + scene.scene_number"></span>
                                    <template x-if="scene.is_generating">
                                        <div class="flex items-center gap-2 bg-purple-500/10 border border-purple-500/20 px-3 py-1 rounded-full">
                                            <span class="w-1.5 h-1.5 bg-purple-500 rounded-full animate-ping"></span>
                                            <span class="text-[9px] font-black text-purple-600 dark:text-purple-400 uppercase tracking-widest">Synthesizing...</span>
                                        </div>
                                    </template>
                                    <div class="h-px flex-1 bg-zinc-100 dark:bg-zinc-800"></div>
                                </div>
                                <p class="text-base font-medium text-zinc-700 dark:text-zinc-300 leading-relaxed" x-text="scene.narration_text"></p>
                                
                                {{-- Player Preview --}}
                                <div x-show="scene.audio_path" class="pt-4 flex items-center gap-4">
                                    <audio :src="'/storage/' + scene.audio_path" controls class="h-8 max-w-sm"></audio>
                                    <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">Active File</span>
                                </div>
                            </div>

                            {{-- FINE-TUNE CONTROLS --}}
                            <div class="xl:w-80 bg-zinc-50 dark:bg-zinc-950 rounded-2xl p-6 border border-zinc-100 dark:border-zinc-800 space-y-6" x-data="{ 
                                config: { 
                                    voice_id: scene.voice_id || 'af_heart', 
                                    speed: 1.0, 
                                    pitch: 1.0,
                                    volume: 1.0 
                                },
                                isGenerating: false,
                                isPreviewing: false,
                                async generate(full = true) {
                                    if(full) {
                                        this.isGenerating = true;
                                        scene.is_generating = true;
                                    } else {
                                        this.isPreviewing = true;
                                    }

                                    try {
                                        const response = await fetch('{{ route('story-voices.generate') }}', {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Content-Type': 'application/json',
                                                'Accept': 'application/json'
                                            },
                                            body: JSON.stringify({
                                                scene_id: scene.id,
                                                voice_id: this.config.voice_id,
                                                speed: this.config.speed,
                                                volume: this.config.volume,
                                                preview_only: !full
                                            })
                                        });
                                        const data = await response.json();
                                        if (data.success) {
                                            if(full) {
                                                this.$dispatch('notify', { detail: { message: 'Synthesis Queued!', type: 'info' } });
                                                // Trigger global polling via event
                                                this.$dispatch('start-polling', { sceneId: scene.id });
                                            } else {
                                                this.$dispatch('notify', { detail: { message: 'Preview Ready!', type: 'success' } });
                                                if(data.audio_url) {
                                                    let audio = new Audio(data.audio_url);
                                                    audio.play();
                                                }
                                            }
                                            if (data.tokens_remaining !== undefined) {
                                                this.$dispatch('tokens-updated', { detail: { voice_tokens: data.tokens_remaining } });
                                            }
                                        } else {
                                            throw new Error(data.message);
                                        }
                                    } catch (e) {
                                        this.$dispatch('notify', { detail: { message: e.message, type: 'error' } });
                                        if(full) scene.is_generating = false;
                                    } finally {
                                        this.isGenerating = false;
                                        this.isPreviewing = false;
                                    }
                                }
                            }">
                                <h4 class="text-[10px] font-black text-zinc-400 uppercase tracking-widest border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-4">Fine-Tune Controls</h4>
                                
                                {{-- Speed Slider --}}
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between text-[10px] font-black text-zinc-500 uppercase">
                                        <span>Speed</span>
                                        <span class="text-purple-500" x-text="config.speed.toFixed(2) + 'x'"></span>
                                    </div>
                                    <div class="relative">
                                        <input type="range" x-model.number="config.speed" min="0.5" max="2.0" step="0.05" class="w-full accent-purple-500">
                                        <div class="flex justify-between mt-1 text-[8px] font-bold text-zinc-400">
                                            <span>Slow</span>
                                            <span>Normal</span>
                                            <span>Fast</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Pitch Slider (UI Only for now) --}}
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between text-[10px] font-black text-zinc-500 uppercase">
                                        <span>Pitch</span>
                                        <span class="text-purple-500" x-text="'+' + Math.round((config.pitch - 1) * 10) + 'Hz'"></span>
                                    </div>
                                    <div class="relative">
                                        <input type="range" x-model.number="config.pitch" min="0.8" max="1.2" step="0.05" class="w-full accent-purple-500">
                                        <div class="flex justify-between mt-1 text-[8px] font-bold text-zinc-400">
                                            <span>Deep</span>
                                            <span>Normal</span>
                                            <span>High</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Volume Slider --}}
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between text-[10px] font-black text-zinc-500 uppercase">
                                        <span>Volume</span>
                                        <span class="text-purple-500" x-text="Math.round(config.volume * 100) + '%'"></span>
                                    </div>
                                    <div class="relative">
                                        <input type="range" x-model.number="config.volume" min="0.5" max="1.5" step="0.05" class="w-full accent-purple-500">
                                        <div class="flex justify-between mt-1 text-[8px] font-bold text-zinc-400">
                                            <span>Soft</span>
                                            <span>Medium</span>
                                            <span>Loud</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-4 space-y-3">
                                    <button @click="generate(false)" :disabled="isPreviewing || isGenerating" class="w-full flex items-center justify-center gap-2 bg-zinc-800 text-white py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-zinc-700 transition-all disabled:opacity-50">
                                        <svg x-show="!isPreviewing" class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"></path></svg>
                                        <svg x-show="isPreviewing" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span>Preview Voice</span>
                                    </button>

                                    <button @click="generate(true)" :disabled="isGenerating || isPreviewing" class="w-full flex flex-col items-center justify-center gap-1 bg-purple-600 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-purple-700 transition-all shadow-xl shadow-purple-500/20 active:scale-95 disabled:opacity-50">
                                        <div class="flex items-center gap-2">
                                            <svg x-show="!isGenerating" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                                            <svg x-show="isGenerating" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            <span x-text="isGenerating ? 'Synthesizing...' : 'Generate Full Scene'"></span>
                                        </div>
                                        <span x-show="!isGenerating" class="text-[8px] opacity-60 tracking-[0.2em]" x-text="tokenCost + ' Tokens / Scene'"></span>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </template>
                {{-- MEGA HOOK VIEW --}}
                <div x-show="isHookSelected" x-transition.opacity class="space-y-6">
                    <div class="bg-gradient-to-br from-rose-50 to-orange-50 dark:from-zinc-900 dark:to-rose-950/30 rounded-[2.5rem] p-8 text-zinc-900 dark:text-white border border-rose-100 dark:border-rose-800/30 shadow-xl shadow-rose-500/5">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                            <div class="space-y-2 text-center md:text-left">
                                <div class="flex items-center justify-center md:justify-start gap-3">
                                    <span class="px-3 py-1 bg-rose-100 dark:bg-rose-500/20 rounded-full text-[10px] font-black uppercase tracking-widest border border-rose-200 dark:border-rose-400/30 text-rose-600 dark:text-rose-400">Winning Vector</span>
                                    <h2 class="text-xl font-black">The Mega-Hook</h2>
                                </div>
                                <p class="text-zinc-500 dark:text-rose-200/60 text-sm font-medium">The critical first 30 seconds of your viral video</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 p-6 flex flex-col xl:flex-row gap-8 transition-all hover:border-rose-500/30 group">
                        {{-- Hook Info --}}
                        <div class="flex-1 space-y-4">
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] font-black text-rose-600 uppercase tracking-[0.3em]">Narration Script</span>
                                <template x-if="selectedProject?.mega_hook?.is_generating">
                                    <div class="flex items-center gap-2 bg-rose-500/10 border border-rose-500/20 px-3 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-ping"></span>
                                        <span class="text-[9px] font-black text-rose-600 dark:text-rose-400 uppercase tracking-widest">Synthesizing...</span>
                                    </div>
                                </template>
                                <div class="h-px flex-1 bg-zinc-100 dark:bg-zinc-800"></div>
                            </div>
                            <p class="text-base font-medium text-zinc-700 dark:text-zinc-300 leading-relaxed italic" x-text="selectedProject?.mega_hook?.text"></p>
                            
                            {{-- Player Preview --}}
                            <div x-show="selectedProject?.mega_hook?.audio_path" class="pt-4 flex items-center gap-4">
                                <audio :src="'/storage/' + selectedProject?.mega_hook?.audio_path" controls class="h-8 max-w-sm"></audio>
                                <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">Master Intro File</span>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="xl:w-48 flex flex-col gap-3">
                            <button @click="generateVoice(selectedProject.mega_hook.id, true)" :disabled="selectedProject?.mega_hook?.is_generating" class="w-full bg-zinc-900 dark:bg-white dark:text-zinc-900 text-white px-4 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                                <svg x-show="!selectedProject?.mega_hook?.is_generating" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path></svg>
                                <svg x-show="selectedProject?.mega_hook?.is_generating" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span x-text="selectedProject?.mega_hook?.audio_path ? 'Regenerate' : 'Generate Voice'"></span>
                            </button>
                            <div class="text-center">
                                <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest" x-text="tokenCost + ' Tokens'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- EMPTY STATE --}}
                <div x-show="!selectedChapter" class="flex flex-col items-center justify-center py-20 bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 border-dashed">
                    <div class="w-20 h-20 bg-zinc-50 dark:bg-zinc-950 rounded-full flex items-center justify-center text-zinc-300 dark:text-zinc-800 mb-6">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <p class="text-lg font-bold text-zinc-800 dark:text-zinc-200">Terminal Standby</p>
                    <p class="text-sm text-zinc-500 font-medium max-w-xs text-center mt-2">Please select a story and chapter from the left panel to begin voice synthesis.</p>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function storyStudio() {
            return {
                projects: @json($projects),
                search: '',
                selectedProject: null,
                selectedChapter: null,
                isHookSelected: false,
                isBulkGenerating: false,
                globalSettings: {
                    voice_id: 'af_heart',
                    speed: 1.0,
                    volume: 1.0
                },
                tokenBalance: {{ Auth::user()->voiceTokensBalance() }},
                tokenCost: {{ Auth::user()->plan->voice_token_cost ?? 50 }},

                get filteredProjects() {
                    if (!this.search) return this.projects;
                    return this.projects.filter(p => 
                        (p.selected_title || p.topic || '').toLowerCase().includes(this.search.toLowerCase())
                    );
                },

                selectProject(projectId) {
                    if (!projectId) {
                        this.selectedProject = null;
                        this.selectedChapter = null;
                        return;
                    }
                    const rawProject = this.projects.find(p => p.id == projectId);
                    if (rawProject) {
                        // Extract the selected mega_hook data if available
                        const selectedTitle = rawProject.generated_titles?.[0] || null;
                        this.selectedProject = {
                            ...rawProject,
                            mega_hook: selectedTitle ? {
                                id: selectedTitle.id,
                                text: selectedTitle.mega_hook,
                                audio_path: selectedTitle.mega_hook_audio_path,
                                is_generating: false
                            } : null
                        };
                    }
                    this.selectedChapter = null;
                },

                selectChapter(chapter) {
                    this.selectedChapter = chapter;
                },

                async pollStatus(modelId, chapterId = null, isHook = false) {
                    // Record which chapter this poll belongs to
                    if (!chapterId && !isHook) chapterId = this.selectedChapter?.id;
                    
                    let model = null;
                    if (isHook) {
                        model = this.selectedProject?.mega_hook;
                    } else {
                        model = this.selectedChapter?.id === chapterId 
                            ? this.selectedChapter?.scenes.find(s => s.id === modelId) 
                            : null;
                    }

                    try {
                        const params = isHook ? `title_id=${modelId}` : `scene_id=${modelId}`;
                        const response = await fetch(`{{ route('story-voices.check-status') }}?${params}`);
                        const data = await response.json();

                        if (data.status === 'completed') {
                            if (model) {
                                if (isHook) {
                                    model.mega_hook_audio_path = data.audio_path;
                                } else {
                                    model.audio_path = data.audio_path;
                                }
                                model.is_generating = false;
                            }
                            this.$dispatch('notify', { detail: { message: 'Synthesis Complete!', type: 'success' } });
                        } else {
                            // Still pending, poll again in 3 seconds
                            setTimeout(() => this.pollStatus(modelId, chapterId, isHook), 3000);
                        }
                    } catch (e) {
                        console.error("Polling error", e);
                        if (model) model.is_generating = false;
                    }
                },

                async bulkGenerate() {
                    if (!this.selectedChapter) return;
                    this.isBulkGenerating = true;
                    try {
                        const response = await fetch('{{ route('story-voices.bulk-generate') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                chapter_id: this.selectedChapter.id,
                                voice_id: this.globalSettings.voice_id,
                                speed: this.globalSettings.speed,
                                volume: this.globalSettings.volume
                            })
                        });
                        const data = await response.json();
                        if (data.success) {
                            // Mark all as generating
                            this.selectedChapter.scenes.forEach(scene => {
                                scene.is_generating = true;
                                this.pollStatus(scene.id);
                            });

                            if (data.tokens_remaining !== undefined) {
                                this.tokenBalance = data.tokens_remaining;
                                window.dispatchEvent(new CustomEvent('tokens-updated', { 
                                    detail: { voice_tokens: data.tokens_remaining } 
                                }));
                            }
                            this.$dispatch('notify', { detail: { message: `Queued ${this.selectedChapter.scenes.length} voices for background processing.`, type: 'info' } });
                        } else {
                            throw new Error(data.message || 'Bulk generation failed');
                        }
                    } catch (e) {
                        this.$dispatch('notify', { detail: { message: e.message, type: 'error' } });
                    } finally {
                        this.isBulkGenerating = false;
                    }
                },

                async generateVoice(id, isHook = false) {
                    const model = isHook ? this.selectedProject.mega_hook : this.selectedChapter.scenes.find(s => s.id === id);
                    if (!model) return;

                    model.is_generating = true;

                    try {
                        const url = isHook ? '{{ route('story-voices.megahook') }}' : '{{ route('story-voices.generate') }}';
                        const body = isHook ? { title_id: id } : { scene_id: id };
                        
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                ...body,
                                voice_id: this.globalSettings.voice_id,
                                speed: this.globalSettings.speed,
                                volume: this.globalSettings.volume
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            if (data.tokens_remaining !== undefined) {
                                this.tokenBalance = data.tokens_remaining;
                                window.dispatchEvent(new CustomEvent('tokens-updated', { 
                                    detail: { voice_tokens: data.tokens_remaining } 
                                }));
                            }
                            this.pollStatus(id, null, isHook);
                            this.$dispatch('notify', { detail: { message: 'Generation started...', type: 'info' } });
                        } else {
                            throw new Error(data.message || 'Generation failed');
                        }
                    } catch (e) {
                        model.is_generating = false;
                        this.$dispatch('notify', { detail: { message: e.message, type: 'error' } });
                    }
                }
            }
        }
    </script>
    <style>
        input[type="range"] {
            -webkit-appearance: none;
            height: 4px;
            background: #e4e4e7;
            border-radius: 5px;
            background-image: linear-gradient(#9333ea, #9333ea);
            background-repeat: no-repeat;
        }
        .dark input[type="range"] {
            background: #27272a;
        }
        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            height: 16px;
            width: 16px;
            border-radius: 50%;
            background: #9333ea;
            cursor: pointer;
            box-shadow: 0 0 2px 0 rgba(0,0,0,0.5);
            transition: background .3s ease-in-out;
        }
        input[type="range"]::-webkit-slider-runnable-track {
            -webkit-appearance: none;
            box-shadow: none;
            border: none;
            background: transparent;
        }
    </style>
    @endpush
</x-app-layout>
