<x-app-layout>
    <div class="h-screen flex flex-col bg-[#141414] text-gray-300 overflow-hidden" x-data="{ 
        activeChapterIndex: 0,
        activeSceneIndex: 0,
        isPlaying: false,
        currentTime: 0,
        playInterval: null,
        isSaving: false,
        lastSaved: null,
        voiceEnabled: true,
        isSpeaking: false,
        
        project: {{ json_encode($project) }},
        
        get activeChapter() { 
            return (this.project.chapters && this.project.chapters.length > 0) 
                ? this.project.chapters[this.activeChapterIndex] 
                : null;
        },
        get activeScene() { 
            return (this.activeChapter && this.activeChapter.scenes && this.activeChapter.scenes.length > 0)
                ? this.activeChapter.scenes[this.activeSceneIndex]
                : null;
        },
        
        get totalDuration() {
            let total = 0;
            if (this.project.chapters) {
                this.project.chapters.forEach(c => {
                    if (c.scenes) {
                        c.scenes.forEach(s => total += s.duration_seconds);
                    }
                });
            }
            return total || 1; // Avoid division by zero
        },

        get playheadPercentage() {
            return (this.currentTime / this.totalDuration) * 100;
        },
        
        selectScene(chapterIndex, sceneIndex) {
            this.activeChapterIndex = chapterIndex;
            this.activeSceneIndex = sceneIndex;
            
            // Calculate time offset for this scene
            let timeOffset = 0;
            for (let c = 0; c < chapterIndex; c++) {
                const ch = this.project.chapters[c];
                if (ch && ch.scenes) {
                    ch.scenes.forEach(s => timeOffset += s.duration_seconds);
                }
            }
            const currentCh = this.project.chapters[chapterIndex];
            if (currentCh && currentCh.scenes) {
                for (let s = 0; s < sceneIndex; s++) {
                    timeOffset += currentCh.scenes[s].duration_seconds;
                }
            }
            this.currentTime = timeOffset;
            this.stopSpeaking();
        },

        togglePlay() {
            if (this.isPlaying) {
                this.pause();
            } else {
                this.play();
            }
        },

        play() {
            this.isPlaying = true;
            this.playInterval = setInterval(() => {
                this.currentTime += 0.1; // 100ms steps
                if (this.currentTime >= this.totalDuration) {
                    this.pause();
                    this.currentTime = 0;
                }
                this.syncIndexesToTime();
            }, 100);

            // Immediate trigger for first scene
            if (this.activeScene) {
                this.speak(this.activeScene.narration_text);
            }
        },

        pause() {
            this.isPlaying = false;
            clearInterval(this.playInterval);
            this.stopSpeaking();
        },

        syncIndexesToTime() {
            let elapsed = 0;
            const oldChapter = this.activeChapterIndex;
            const oldScene = this.activeSceneIndex;
            
            const chapters = this.project.chapters || [];

            for (let c = 0; c < chapters.length; c++) {
                const chapter = chapters[c];
                const scenes = chapter.scenes || [];
                for (let s = 0; s < scenes.length; s++) {
                    const scene = scenes[s];
                    if (this.currentTime >= elapsed && this.currentTime < elapsed + scene.duration_seconds) {
                        this.activeChapterIndex = c;
                        this.activeSceneIndex = s;
                        
                        // If scene changed during playback, trigger voice
                        if (this.isPlaying && (oldChapter !== c || oldScene !== s)) {
                            this.speak(scene.narration_text);
                        }
                        return;
                    }
                    elapsed += scene.duration_seconds;
                }
            }
        },

        speak(text) {
            if (!this.voiceEnabled || !text) return;
            
            this.stopSpeaking();
            
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.rate = 1.0;
            utterance.pitch = 1.0;
            
            utterance.onstart = () => { this.isSpeaking = true; };
            utterance.onend = () => { this.isSpeaking = false; };
            utterance.onerror = () => { this.isSpeaking = false; };
            
            window.speechSynthesis.speak(utterance);
        },

        stopSpeaking() {
            window.speechSynthesis.cancel();
            this.isSpeaking = false;
        },

        testVoice() {
            const strings = [
                "Voice synthesis engine active and ready for deployment.",
                "Audio link established. Studio narration is operational.",
                "System check complete. Voice lab is online."
            ];
            const text = strings[Math.floor(Math.random() * strings.length)];
            
            // Forced check for speech support
            if (!('speechSynthesis' in window)) {
                alert('Your browser does not support Speech Synthesis. Please try Chrome or Edge.');
                return;
            }
            
            this.speak(text);
        },

        formatTime(seconds) {
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            const s = Math.floor(seconds % 60);
            const ms = Math.floor((seconds % 1) * 100);
            return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}:${String(ms).padStart(2, '0')}`;
        },

        async saveState() {
            this.isSaving = true;
            try {
                const response = await fetch('{{ route('projects.studio.save', $project) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ chapters: this.project.chapters })
                });
                
                const data = await response.json();
                if (data.success) {
                    this.lastSaved = new Date().toLocaleTimeString();
                    // Optional: Show a subtle toast or brief button change
                }
            } catch (e) {
                console.error('Save failed', e);
                alert('Connection error: Workshop state not saved.');
            } finally {
                this.isSaving = false;
            }
        },

        resolveUrl(path) {
            if (!path) return '';
            return path.startsWith('/') ? path : '/' + path;
        },

        init() {
            console.log('Production Studio Initialized:', this.project);
        }
    }">
        <!-- Top Navigation / Menu Bar -->
        <div class="h-12 border-b border-black bg-[#1e1e1e] flex items-center justify-between px-4 shrink-0">
            <div class="flex items-center gap-6">
                <a href="{{ route('projects.show', $project) }}" class="text-gray-500 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black text-red-600 uppercase tracking-widest bg-red-600/10 px-2 py-0.5 rounded">Studio</span>
                    <h1 class="text-xs font-bold uppercase tracking-widest text-gray-400">{{ $project->selected_title ?? $project->topic }}</h1>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 text-[10px] font-bold text-gray-500 uppercase tracking-tighter">
                    <span>Rendering Engine:</span>
                    <span class="text-green-500">Together AI + SDXL</span>
                </div>
                <button class="bg-[#3e3e3e] hover:bg-[#4e4e4e] text-white px-4 py-1.5 rounded text-[10px] font-black uppercase tracking-widest transition">
                    Export Media
                </button>
            </div>
        </div>

        <!-- Main Workspace -->
        <div class="flex-1 flex overflow-hidden min-h-0">
            
            <!-- Left Panel: Project Assets (Library) -->
            <div class="w-72 border-r border-black bg-[#1a1a1a] flex flex-col overflow-hidden">
                <div class="p-4 border-b border-black bg-[#252525]">
                    <h2 class="text-[10px] font-black uppercase tracking-widest text-gray-500">Project Assets</h2>
                </div>
                <div class="flex-1 overflow-y-auto p-2 space-y-4">
                    <template x-if="!project.chapters || project.chapters.length === 0">
                        <div class="p-4 text-center">
                            <p class="text-[10px] font-bold text-gray-700 uppercase tracking-widest">Generating Production Data...</p>
                        </div>
                    </template>
                    <template x-for="(chapter, cIdx) in project.chapters" :key="chapter.id">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 px-2 py-1 text-[10px] font-bold text-gray-600 uppercase">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                <span x-text="'Chapter ' + chapter.chapter_number"></span>
                            </div>
                            <div class="grid grid-cols-2 gap-1">
                                <template x-for="(scene, sIdx) in (chapter.scenes || [])" :key="scene.id">
                                    <button 
                                        @click="selectScene(cIdx, sIdx)"
                                        class="aspect-video bg-[#252525] rounded border border-transparent hover:border-red-600/50 overflow-hidden relative group transition-all"
                                        :class="{ 'border-red-600 ring-1 ring-red-600': activeChapterIndex === cIdx && activeSceneIndex === sIdx }"
                                    >
                                        <template x-if="scene.image_url">
                                            <img :src="resolveUrl(scene.image_url)" class="w-full h-full object-cover opacity-60 group-hover:opacity-100 transition-opacity">
                                        </template>
                                        <template x-if="!scene.image_url">
                                            <div class="w-full h-full flex items-center justify-center bg-black/20">
                                                <span class="text-[8px] font-bold text-gray-700" x-text="'SC ' + scene.scene_number"></span>
                                            </div>
                                        </template>
                                        <div class="absolute bottom-1 right-1 text-[8px] font-black text-white/40 group-hover:text-white/80 transition-colors" x-text="scene.duration_seconds + 's'"></div>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Center Panel: Program Monitor -->
            <div class="flex-1 bg-black flex flex-col relative overflow-hidden group">
                <div class="absolute top-4 left-6 z-10 flex flex-col gap-2">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></div>
                        <span class="text-[10px] font-black uppercase tracking-[0.3em] text-white/60">Program: Final Sequence</span>
                    </div>
                    <div x-show="isSpeaking" x-transition class="flex items-center gap-2 bg-rose-600/20 px-2 py-0.5 rounded border border-rose-600/30">
                        <svg class="w-3 h-3 text-rose-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                        <span class="text-[8px] font-black text-rose-500 uppercase tracking-widest">Audio Active</span>
                    </div>
                </div>

                <div class="flex-1 flex items-center justify-center p-8">
                    <div class="w-full max-w-4xl aspect-video bg-[#1a1a1a] shadow-2xl relative overflow-hidden rounded-lg group-hover:scale-[1.01] transition-transform duration-700 border border-white/5">
                        <template x-if="activeScene && activeScene.image_url">
                            <img :src="resolveUrl(activeScene.image_url)" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!activeScene || !activeScene.image_url">
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#1a1a1a] to-[#0f0f0f]">
                                <div class="text-center">
                                    <svg class="w-12 h-12 text-white/5 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <p class="text-[10px] font-black text-white/20 uppercase tracking-widest" x-text="!activeScene ? 'Architecture Pending' : 'Awaiting Visual Render'"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Monitor Controls -->
                <div class="h-16 border-t border-white/5 bg-[#1a1a1a] flex items-center justify-center gap-8 shrink-0">
                    <button class="text-gray-600 hover:text-white transition" @click="currentTime = Math.max(0, currentTime - 5); syncIndexesToTime()">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M8.445 14.832A1 1 0 0010 14v-2.798l5.445 3.63A1 1 0 0017 14V6a1 1 0 00-1.555-.832L10 8.798V6a1 1 0 00-1.555-.832l-6 4a1 1 0 000 1.664l6 4z"></path></svg>
                    </button>
                    <button @click="togglePlay()" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-white hover:bg-white/10 transition shadow-xl">
                        <svg x-show="!isPlaying" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                        <svg x-show="isPlaying" x-cloak class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 4h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h2a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2z" /><path d="M17 4h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h2a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2z" /></svg>
                    </button>
                    <button class="text-gray-600 hover:text-white transition" @click="currentTime = Math.min(totalDuration, currentTime + 5); syncIndexesToTime()">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M11.555 4.832a1 1 0 00-1.555.832v2.798L4.555 4.832A1 1 0 003 5.664V14.336a1 1 0 001.555.832L10 11.534v2.798a1 1 0 001.555.832l6-4a1 1 0 000-1.664l-6-4z"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Right Panel: Inspector -->
            <div class="w-80 border-l border-black bg-[#1a1a1a] flex flex-col overflow-hidden">
                <div class="p-4 border-b border-black bg-[#252525] flex items-center justify-between">
                    <h2 class="text-[10px] font-black uppercase tracking-widest text-gray-400">Effect Controls</h2>
                    <template x-if="activeScene">
                        <span class="text-[9px] font-black text-red-600" x-text="'SC ' + activeScene.scene_number"></span>
                    </template>
                </div>
                <div class="p-6 space-y-8 overflow-y-auto">
                    <template x-if="activeScene">
                        <div class="space-y-8">
                            <!-- Narration Segment -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest">Narration Script</label>
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <span class="text-[8px] font-black uppercase text-zinc-600 group-hover:text-zinc-400 transition-colors">Voice Lab Preview</span>
                                        <div class="relative w-7 h-4 rounded-full bg-zinc-800 border border-white/5 transition-colors" :class="voiceEnabled ? 'bg-rose-600/30 border-rose-600/50' : ''">
                                            <input type="checkbox" x-model="voiceEnabled" class="sr-only">
                                            <div class="absolute top-0.5 left-0.5 w-2.5 h-2.5 rounded-full bg-zinc-600 transition-transform" :class="voiceEnabled ? 'translate-x-3 bg-white' : ''"></div>
                                        </div>
                                    </label>
                                </div>
                                <button 
                                    @click="testVoice()"
                                    class="w-full mb-3 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-500 hover:text-white py-1.5 rounded-lg border border-white/5 text-[8px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2"
                                >
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                                    Test Audio Output
                                </button>
                                <div class="relative group/field">
                                    <textarea 
                                        x-model="activeScene.narration_text"
                                        class="w-full bg-[#0f0f0f] border-none rounded-xl text-xs font-medium text-gray-300 leading-relaxed p-4 focus:ring-1 focus:ring-red-600/50 transition-all min-h-[120px]"
                                    ></textarea>
                                    <div class="absolute bottom-2 right-2 text-[8px] font-black text-gray-700 uppercase tracking-widest group-hover/field:text-red-900 transition-colors">Editable</div>
                                </div>
                            </div>

                            <!-- Visual Prompt -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest">Visual Prompt</label>
                                    <button class="text-[9px] font-black text-red-500 hover:underline uppercase tracking-tighter">Regenerate</button>
                                </div>
                                <div class="p-4 bg-[#0f0f0f] rounded-xl border border-white/5 space-y-4">
                                    <textarea 
                                        x-model="activeScene.visual_prompt"
                                        class="w-full bg-transparent border-none p-0 text-[11px] font-bold text-gray-400 italic focus:ring-0 leading-relaxed"
                                    ></textarea>
                                    <div class="flex items-center gap-2 pt-2 border-t border-white/5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                                        <span class="text-[8px] font-black text-gray-600 uppercase tracking-widest">Cinematic Hyper-Realistic</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Scene Metadata -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-4 bg-[#252525] rounded-xl border border-white/5">
                                    <p class="text-[8px] font-black text-gray-600 uppercase tracking-widest mb-1">Duration</p>
                                    <p class="text-sm font-black text-white" x-text="activeScene.duration_seconds + 's'"></p>
                                </div>
                                <div class="p-4 bg-[#252525] rounded-xl border border-white/5">
                                    <p class="text-[8px] font-black text-gray-600 uppercase tracking-widest mb-1">Status</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                                        <span class="text-[10px] font-black text-gray-300 uppercase">Ready</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template x-if="!activeScene">
                        <div class="h-full flex flex-col items-center justify-center text-center p-8">
                            <svg class="w-12 h-12 text-zinc-800 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            <h3 class="text-xs font-black text-zinc-600 uppercase tracking-widest mb-2">No Active Scene</h3>
                            <p class="text-[10px] text-zinc-700 font-bold uppercase tracking-tighter">Please wait for chapter architecture to complete before editing assets.</p>
                        </div>
                    </template>
                </div>
                
                <div class="p-4 bg-[#252525] border-t border-black">
                    <button 
                        @click="saveState()"
                        :disabled="isSaving"
                        class="w-full bg-red-600 hover:bg-red-700 disabled:bg-gray-700 disabled:opacity-50 text-white py-3 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] shadow-lg shadow-red-900/20 transition transform active:scale-[0.98] flex items-center justify-center gap-2"
                    >
                        <template x-if="isSaving">
                            <svg class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        <span x-text="isSaving ? 'Encrypting & Saving...' : (lastSaved ? 'Last Saved at ' + lastSaved : 'Save Workshop State')"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Bottom Panel: Timeline -->
        <div class="h-56 bg-[#1a1a1a] border-t border-black flex flex-col shrink-0">
            <div class="h-8 border-b border-black bg-[#252525] flex items-center px-4 justify-between">
                <div class="flex items-center gap-6">
                    <button class="text-gray-600 hover:text-white transition"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg></button>
                    <div class="flex items-center gap-3">
                        <span class="text-[10px] font-black text-blue-500 uppercase tracking-widest">Video 1</span>
                        <span class="text-[10px] font-black text-green-500 uppercase tracking-widest">Audio 1</span>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-[10px] font-mono text-gray-500" x-text="formatTime(currentTime)"></div>
                    <div class="h-4 w-[1px] bg-gray-800"></div>
                    <div class="flex items-center gap-2">
                        <button class="text-gray-600 hover:text-white transition"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 4.25a.75.75 0 01.75-.75h12.5a.75.75 0 01.75.75v.75H3.75v-.75zm0 2.25h12.5v10.75a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5V6.5zM10 9a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 9zm-2.5.75a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0v-4.5zM13.25 9a.75.75 0 011.5 0v4.5a.75.75 0 01-1.5 0V9z" clip-rule="evenodd"></path></svg></button>
                    </div>
                </div>
            </div>
            
            <div class="flex-1 overflow-x-auto overflow-y-hidden relative bg-[#0f0f0f] flex items-center p-4 gap-1 min-w-full">
                <!-- Time Markers Background -->
                <div class="absolute top-0 left-0 w-full h-full flex pointer-events-none opacity-5">
                    <template x-for="i in 50">
                        <div class="h-full border-l border-white w-20"></div>
                    </template>
                </div>

                <!-- Playhead -->
                <div class="absolute top-0 w-[2px] h-full bg-blue-500 z-30 shadow-[0_0_15px_rgba(59,130,246,0.5)] transition-all duration-100 ease-linear"
                     :style="'left: ' + playheadPercentage + '%'">
                    <div class="absolute -top-1 -left-1.5 w-4 h-4 bg-blue-500 rounded-sm rotate-45"></div>
                </div>

                <template x-for="(chapter, cIndex) in (project.chapters || [])">
                    <div class="flex gap-1 shrink-0">
                        <template x-for="(scene, sIndex) in (chapter.scenes || [])" :key="scene.id">
                            <div 
                                @click="selectScene(cIndex, sIndex)"
                                class="h-32 bg-[#2a2a2a] border border-black rounded relative group cursor-pointer hover:bg-[#3a3a3a] transition-all overflow-hidden shrink-0"
                                :class="{ 'ring-2 ring-red-600 ring-inset bg-[#3a3a3a]': activeChapterIndex === cIndex && activeSceneIndex === sIndex }"
                                :style="'width: ' + (scene.duration_seconds * 15) + 'px; min-width: 100px;'"
                            >
                                <template x-if="scene.image_url">
                                    <img :src="resolveUrl(scene.image_url)" class="absolute inset-0 w-full h-full object-cover opacity-20 filter grayscale group-hover:grayscale-0 transition-all">
                                </template>
                                <div class="relative z-10 p-2">
                                    <p class="text-[8px] font-black text-gray-500 uppercase tracking-tighter" x-text="'SC ' + scene.scene_number"></p>
                                    <p class="text-[7px] font-bold text-gray-400 truncate mt-1" x-text="scene.narration_text"></p>
                                </div>
                                <div class="absolute bottom-1 left-2 text-[8px] font-black text-white/20" x-text="scene.duration_seconds + 's'"></div>
                            </div>
                        </template>
                        <!-- Chapter Spacer -->
                        <div class="w-2"></div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #444; }
    </style>
    @endpush
</x-app-layout>
