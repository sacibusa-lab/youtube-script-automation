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

        // ── Character Library ─────────────────────────────────────────────
        characterLibrary: [],
        charLibOpen: false,

        async loadCharacterLibrary() {
            try {
                const res = await fetch('{{ route('characters.api-list') }}');
                this.characterLibrary = await res.json();
            } catch(e) {
                console.warn('Could not load character library:', e);
            }
        },

        get activeSceneCharacter() {
            if (!this.activeScene || !this.activeScene.locked_character_slug) return null;
            return this.characterLibrary.find(c => c.slug === this.activeScene.locked_character_slug) || null;
        },

        lockCharacter(character) {
            if (this.activeScene) {
                this.activeScene.locked_character_slug = character.slug;
                this.activeScene.locked_character_name = character.name;
            }
            this.charLibOpen = false;
        },

        unlockCharacter() {
            if (this.activeScene) {
                this.activeScene.locked_character_slug = null;
                this.activeScene.locked_character_name = null;
            }
        },

        // ── Drag-to-Reorder ──────────────────────────────────────────────
        dragSrc: null,
        dragOver: null,

        onDragStart(e, cIdx, sIdx) {
            this.dragSrc = { cIdx, sIdx };
            e.dataTransfer.effectAllowed = 'move';
            e.target.closest('[draggable]').classList.add('opacity-40');
        },
        onDragEnd(e) {
            e.target.closest('[draggable]').classList.remove('opacity-40');
            this.dragOver = null;
        },
        onDragOver(e, cIdx, sIdx) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            this.dragOver = { cIdx, sIdx };
        },
        onDrop(cIdx, sIdx) {
            if (!this.dragSrc) return;
            const src = this.dragSrc;
            if (src.cIdx === cIdx && src.sIdx === sIdx) { this.dragSrc = null; return; }

            if (src.cIdx === cIdx) {
                const scenes = this.project.chapters[cIdx].scenes;
                const [moved] = scenes.splice(src.sIdx, 1);
                scenes.splice(sIdx, 0, moved);
            } else {
                const [moved] = this.project.chapters[src.cIdx].scenes.splice(src.sIdx, 1);
                this.project.chapters[cIdx].scenes.splice(sIdx, 0, moved);
            }

            this.dragSrc = null;
            this.dragOver = null;
            this.project = { ...this.project };
        },

        // ── Drag-Edge Duration Resize ────────────────────────────────────
        resizing: null,

        startResize(e, cIdx, sIdx) {
            e.stopPropagation();
            e.preventDefault();
            const scene = this.project.chapters[cIdx].scenes[sIdx];
            this.resizing = { cIdx, sIdx, startX: e.clientX, startDuration: scene.duration_seconds };

            const onMove = (ev) => {
                if (!this.resizing) return;
                const delta = ev.clientX - this.resizing.startX;
                const newDuration = Math.max(5, Math.round(this.resizing.startDuration + delta / 15));
                this.project.chapters[this.resizing.cIdx].scenes[this.resizing.sIdx].duration_seconds = newDuration;
            };
            const onUp = () => {
                this.resizing = null;
                window.removeEventListener('mousemove', onMove);
                window.removeEventListener('mouseup', onUp);
            };
            window.addEventListener('mousemove', onMove);
            window.addEventListener('mouseup', onUp);
        },

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
                    if (c.scenes) c.scenes.forEach(s => total += s.duration_seconds);
                });
            }
            return total || 1;
        },

        get playheadPercentage() {
            return (this.currentTime / this.totalDuration) * 100;
        },

        selectScene(chapterIndex, sceneIndex) {
            this.activeChapterIndex = chapterIndex;
            this.activeSceneIndex = sceneIndex;
            let timeOffset = 0;
            for (let c = 0; c < chapterIndex; c++) {
                const ch = this.project.chapters[c];
                if (ch && ch.scenes) ch.scenes.forEach(s => timeOffset += s.duration_seconds);
            }
            const currentCh = this.project.chapters[chapterIndex];
            if (currentCh && currentCh.scenes) {
                for (let s = 0; s < sceneIndex; s++) timeOffset += currentCh.scenes[s].duration_seconds;
            }
            this.currentTime = timeOffset;
            this.stopSpeaking();
        },

        togglePlay() { this.isPlaying ? this.pause() : this.play(); },

        play() {
            this.isPlaying = true;
            this.playInterval = setInterval(() => {
                this.currentTime += 0.1;
                if (this.currentTime >= this.totalDuration) { this.pause(); this.currentTime = 0; }
                this.syncIndexesToTime();
            }, 100);
            if (this.activeScene) this.speak(this.activeScene.narration_text);
        },

        pause() {
            this.isPlaying = false;
            clearInterval(this.playInterval);
            this.stopSpeaking();
        },

        syncIndexesToTime() {
            let elapsed = 0;
            const old = { c: this.activeChapterIndex, s: this.activeSceneIndex };
            for (let c = 0; c < (this.project.chapters || []).length; c++) {
                for (let s = 0; s < (this.project.chapters[c].scenes || []).length; s++) {
                    const scene = this.project.chapters[c].scenes[s];
                    if (this.currentTime >= elapsed && this.currentTime < elapsed + scene.duration_seconds) {
                        this.activeChapterIndex = c;
                        this.activeSceneIndex = s;
                        if (this.isPlaying && (old.c !== c || old.s !== s)) this.speak(scene.narration_text);
                        return;
                    }
                    elapsed += scene.duration_seconds;
                }
            }
        },

        speak(text) {
            if (!this.voiceEnabled || !text) return;
            this.stopSpeaking();
            const u = new SpeechSynthesisUtterance(text);
            u.rate = 1.0; u.pitch = 1.0;
            u.onstart = () => this.isSpeaking = true;
            u.onend = () => this.isSpeaking = false;
            u.onerror = () => this.isSpeaking = false;
            window.speechSynthesis.speak(u);
        },

        stopSpeaking() { window.speechSynthesis.cancel(); this.isSpeaking = false; },

        testVoice() {
            if (!('speechSynthesis' in window)) { alert('Browser does not support Speech Synthesis. Try Chrome.'); return; }
            this.speak('Voice synthesis engine active. Studio narration is operational.');
        },

        formatTime(secs) {
            const m = Math.floor(secs / 60);
            const s = Math.floor(secs % 60);
            const ms = Math.floor((secs % 1) * 100);
            return `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}:${String(ms).padStart(2,'0')}`;
        },

        async saveState() {
            this.isSaving = true;
            try {
                const res = await fetch('{{ route('projects.studio.save', $project) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ chapters: this.project.chapters })
                });
                const data = await res.json();
                if (data.success) this.lastSaved = new Date().toLocaleTimeString();
            } catch(e) {
                alert('Save failed. Check your connection.');
            } finally {
                this.isSaving = false;
            }
        },

        resolveUrl(path) {
            if (!path) return '';
            return path.startsWith('/') ? path : '/' + path;
        },

        init() {
            console.log('Studio v2 (Interactive Timeline) initialized.', this.project);
            this.loadCharacterLibrary();
        }
    }">

        {{-- ── Top Menu Bar ────────────────────────────────────────────── --}}
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
                    <span>Engine:</span>
                    <span class="text-green-500">OpenRouter + Flux</span>
                </div>
                <a href="{{ route('projects.export', $project) }}" class="bg-[#3e3e3e] hover:bg-[#4e4e4e] text-white px-4 py-1.5 rounded text-[10px] font-black uppercase tracking-widest transition">
                    Export ZIP
                </a>
            </div>
        </div>

        {{-- ── Main Workspace ──────────────────────────────────────────── --}}
        <div class="flex-1 flex overflow-hidden min-h-0">

            {{-- Left Panel: Assets --}}
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
                                <span x-text="'Ch ' + chapter.chapter_number"></span>
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
                                        <div class="absolute bottom-1 right-1 text-[8px] font-black text-white/40 group-hover:text-white/80" x-text="scene.duration_seconds + 's'"></div>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Center Panel: Program Monitor --}}
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

                {{-- Monitor Controls --}}
                <div class="h-16 border-t border-white/5 bg-[#1a1a1a] flex items-center justify-center gap-8 shrink-0">
                    <button class="text-gray-600 hover:text-white transition" @click="currentTime = Math.max(0, currentTime - 5); syncIndexesToTime()">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M8.445 14.832A1 1 0 0010 14v-2.798l5.445 3.63A1 1 0 0017 14V6a1 1 0 00-1.555-.832L10 8.798V6a1 1 0 00-1.555-.832l-6 4a1 1 0 000 1.664l6 4z"></path></svg>
                    </button>
                    <button @click="togglePlay()" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-white hover:bg-white/10 transition shadow-xl">
                        <svg x-show="!isPlaying" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                        <svg x-show="isPlaying" x-cloak class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M9 4h-2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2v-12a2 2 0 0 0-2-2z"/><path d="M17 4h-2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2v-12a2 2 0 0 0-2-2z"/></svg>
                    </button>
                    <button class="text-gray-600 hover:text-white transition" @click="currentTime = Math.min(totalDuration, currentTime + 5); syncIndexesToTime()">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M11.555 4.832a1 1 0 00-1.555.832v2.798L4.555 4.832A1 1 0 003 5.664V14.336a1 1 0 001.555.832L10 11.534v2.798a1 1 0 001.555.832l6-4a1 1 0 000-1.664l-6-4z"></path></svg>
                    </button>
                </div>
            </div>

            {{-- Right Panel: Inspector --}}
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
                            {{-- Narration --}}
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest">Narration Script</label>
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <span class="text-[8px] font-black uppercase text-zinc-600 group-hover:text-zinc-400 transition-colors">Voice Lab</span>
                                        <div class="relative w-7 h-4 rounded-full bg-zinc-800 border border-white/5 transition-colors" :class="voiceEnabled ? 'bg-rose-600/30 border-rose-600/50' : ''">
                                            <input type="checkbox" x-model="voiceEnabled" class="sr-only">
                                            <div class="absolute top-0.5 left-0.5 w-2.5 h-2.5 rounded-full bg-zinc-600 transition-transform" :class="voiceEnabled ? 'translate-x-3 bg-white' : ''"></div>
                                        </div>
                                    </label>
                                </div>
                                <button @click="testVoice()" class="w-full mb-3 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-500 hover:text-white py-1.5 rounded-lg border border-white/5 text-[8px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                                    Test Audio Output
                                </button>
                                <div class="relative group/field">
                                    <textarea x-model="activeScene.narration_text" class="w-full bg-[#0f0f0f] border-none rounded-xl text-xs font-medium text-gray-300 leading-relaxed p-4 focus:ring-1 focus:ring-red-600/50 transition-all min-h-[120px]"></textarea>
                                    <div class="absolute bottom-2 right-2 text-[8px] font-black text-gray-700 uppercase tracking-widest group-hover/field:text-red-900 transition-colors">Editable</div>
                                </div>
                            </div>

                            {{-- Visual Prompt --}}
                            <div class="space-y-3">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest">Visual Prompt</label>
                                <div class="p-4 bg-[#0f0f0f] rounded-xl border border-white/5 space-y-4">
                                    <textarea x-model="activeScene.visual_prompt" class="w-full bg-transparent border-none p-0 text-[11px] font-bold text-gray-400 italic focus:ring-0 leading-relaxed"></textarea>
                                    <div class="flex items-center gap-2 pt-2 border-t border-white/5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                                        <span class="text-[8px] font-black text-gray-600 uppercase tracking-widest">Cinematic Hyper-Realistic</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Scene Metadata --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-4 bg-[#252525] rounded-xl border border-white/5">
                                    <p class="text-[8px] font-black text-gray-600 uppercase tracking-widest mb-1">Duration</p>
                                    <p class="text-sm font-black text-white" x-text="activeScene.duration_seconds + 's'"></p>
                                    <p class="text-[8px] text-gray-700 mt-1">← Drag right edge</p>
                                </div>
                                <div class="p-4 bg-[#252525] rounded-xl border border-white/5">
                                    <p class="text-[8px] font-black text-gray-600 uppercase tracking-widest mb-1">Status</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                                        <span class="text-[10px] font-black text-gray-300 uppercase">Ready</span>
                                    </div>
                                </div>
                            </div>

                            {{-- ── Character Lock ───────────────────────────── --}}
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest flex items-center gap-1.5">
                                        <svg class="w-3 h-3 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                        Character Lock
                                    </label>
                                    <a href="{{ route('characters.index') }}" target="_blank" class="text-[8px] font-bold text-amber-600/70 hover:text-amber-400 uppercase tracking-widest transition">Manage →</a>
                                </div>

                                {{-- Currently locked character --}}
                                <template x-if="activeSceneCharacter">
                                    <div class="flex items-center gap-2 bg-amber-500/10 border border-amber-500/20 rounded-xl p-3">
                                        <div class="w-7 h-7 rounded-full bg-amber-500/20 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[10px] font-black text-amber-300 truncate" x-text="activeSceneCharacter.name"></p>
                                            <p class="text-[8px] text-amber-600/70 uppercase tracking-widest" x-text="activeSceneCharacter.niche || 'Global'"></p>
                                        </div>
                                        <button @click="unlockCharacter()" class="text-amber-700 hover:text-red-400 transition shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </template>

                                {{-- No character locked --}}
                                <template x-if="!activeSceneCharacter">
                                    <button
                                        @click="charLibOpen = !charLibOpen"
                                        class="w-full flex items-center gap-2 bg-zinc-800/50 hover:bg-zinc-800 text-zinc-500 hover:text-amber-400 py-2.5 px-3 rounded-xl border border-white/5 hover:border-amber-500/20 text-[8px] font-black uppercase tracking-widest transition-all"
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Pin a Character
                                    </button>
                                </template>

                                {{-- Character Picker Dropdown --}}
                                <div x-show="charLibOpen" x-transition @click.outside="charLibOpen = false"
                                    class="bg-[#1e1e1e] border border-zinc-700 rounded-xl overflow-hidden shadow-2xl">
                                    <div class="p-2 border-b border-zinc-800">
                                        <p class="text-[8px] font-black text-zinc-500 uppercase tracking-widest px-1">Select Character</p>
                                    </div>
                                    <div class="max-h-48 overflow-y-auto">
                                        <template x-if="characterLibrary.length === 0">
                                            <div class="p-4 text-center">
                                                <p class="text-[9px] text-zinc-600 font-bold uppercase">No characters in library yet.</p>
                                                <a href="{{ route('characters.create') }}" target="_blank" class="text-[9px] text-amber-600 hover:text-amber-400 font-bold underline">Create one →</a>
                                            </div>
                                        </template>
                                        <template x-for="char in characterLibrary" :key="char.id">
                                            <button
                                                @click="lockCharacter(char)"
                                                class="w-full flex items-center gap-3 px-3 py-2.5 hover:bg-zinc-800 transition text-left"
                                            >
                                                <div class="w-6 h-6 rounded-full bg-zinc-700 flex items-center justify-center shrink-0">
                                                    <template x-if="char.reference_image">
                                                        <img :src="char.reference_image" class="w-full h-full rounded-full object-cover">
                                                    </template>
                                                    <template x-if="!char.reference_image">
                                                        <svg class="w-3.5 h-3.5 text-zinc-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                                    </template>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-[10px] font-black text-gray-300 truncate" x-text="char.name"></p>
                                                    <p class="text-[8px] text-zinc-600 uppercase" x-text="char.is_global ? '⭐ Starter' : 'My Character'"></p>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template x-if="!activeScene">
                        <div class="h-full flex flex-col items-center justify-center text-center p-8">
                            <svg class="w-12 h-12 text-zinc-800 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            <h3 class="text-xs font-black text-zinc-600 uppercase tracking-widest mb-2">No Active Scene</h3>
                            <p class="text-[10px] text-zinc-700 font-bold uppercase tracking-tighter">Select a scene from the timeline or asset panel.</p>
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
                        <span x-text="isSaving ? 'Saving...' : (lastSaved ? 'Saved at ' + lastSaved : 'Save Workshop State')"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ── Bottom Panel: Interactive Timeline ─────────────────────── --}}
        <div class="h-60 bg-[#1a1a1a] border-t border-black flex flex-col shrink-0">

            {{-- Timeline Toolbar --}}
            <div class="h-9 border-b border-black bg-[#252525] flex items-center px-4 justify-between shrink-0">
                <div class="flex items-center gap-5">
                    <span class="text-[10px] font-black text-gray-600 uppercase tracking-widest">Timeline</span>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-1.5">
                            <div class="w-2.5 h-2.5 rounded-sm bg-blue-500/60"></div>
                            <span class="text-[9px] font-bold text-gray-600 uppercase">Video</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div class="w-2.5 h-2.5 rounded-sm bg-green-500/40"></div>
                            <span class="text-[9px] font-bold text-gray-600 uppercase">Audio</span>
                        </div>
                    </div>
                    <span class="text-[9px] font-bold text-zinc-700 uppercase tracking-widest hidden lg:block">· Drag clip to reorder · Drag right edge to resize ·</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-[11px] font-mono text-gray-400" x-text="formatTime(currentTime)"></div>
                    <div class="h-4 w-[1px] bg-gray-800"></div>
                    <div class="text-[9px] font-mono text-gray-700" x-text="formatTime(totalDuration)"></div>
                </div>
            </div>

            {{-- Track Area --}}
            <div class="flex-1 overflow-hidden flex flex-col">

                {{-- VIDEO TRACK --}}
                <div class="flex-1 bg-[#0e0e0e] flex items-stretch relative border-b border-black/60 overflow-x-auto min-h-0">
                    {{-- Track Label --}}
                    <div class="w-16 shrink-0 bg-[#1a1a1a] border-r border-black flex items-center justify-center sticky left-0 z-20">
                        <span class="text-[9px] font-black text-gray-600 uppercase tracking-widest">V1</span>
                    </div>

                    {{-- Playhead --}}
                    <div class="absolute top-0 bottom-0 left-16 right-0 overflow-hidden pointer-events-none">
                        <div class="absolute top-0 bottom-0 w-[2px] bg-blue-500 z-30 shadow-[0_0_12px_rgba(59,130,246,0.5)] transition-all duration-100 ease-linear"
                             :style="'left: ' + playheadPercentage + '%'">
                            <div class="absolute -top-0 -left-[5px] w-3 h-3 bg-blue-500 rotate-45"></div>
                        </div>
                    </div>

                    {{-- Scene Clips --}}
                    <div class="flex items-stretch gap-[2px] p-1.5 overflow-x-visible h-full">
                        <template x-for="(chapter, cIdx) in (project.chapters || [])" :key="chapter.id">
                            <div class="flex items-stretch gap-[2px] h-full">
                                <template x-for="(scene, sIdx) in (chapter.scenes || [])" :key="scene.id">
                                    <div
                                        class="relative h-full rounded-md cursor-grab active:cursor-grabbing select-none group/clip transition-all duration-75 overflow-visible flex-shrink-0"
                                        :class="{
                                            'ring-2 ring-inset ring-red-500 z-10': activeChapterIndex === cIdx && activeSceneIndex === sIdx,
                                            'ring-2 ring-inset ring-blue-400/60 scale-[1.03] z-10': dragOver && dragOver.cIdx === cIdx && dragOver.sIdx === sIdx
                                        }"
                                        :style="'width: ' + Math.max(80, scene.duration_seconds * 15) + 'px'"
                                        draggable="true"
                                        @dragstart="onDragStart($event, cIdx, sIdx)"
                                        @dragend="onDragEnd($event)"
                                        @dragover.prevent="onDragOver($event, cIdx, sIdx)"
                                        @drop="onDrop(cIdx, sIdx)"
                                        @click="selectScene(cIdx, sIdx)"
                                    >
                                        {{-- Clip BG + Thumbnail --}}
                                        <div class="absolute inset-0 rounded-md overflow-hidden">
                                            <template x-if="scene.image_url">
                                                <img :src="resolveUrl(scene.image_url)" class="w-full h-full object-cover opacity-15 group-hover/clip:opacity-35 transition-opacity">
                                            </template>
                                            <div class="absolute inset-0 rounded-md border"
                                                :class="(activeChapterIndex === cIdx && activeSceneIndex === sIdx)
                                                    ? 'bg-red-600/20 border-red-600/50'
                                                    : 'bg-blue-900/25 border-blue-500/20 group-hover/clip:border-blue-400/50'">
                                            </div>
                                        </div>

                                        {{-- Clip Content --}}
                                        <div class="relative z-10 px-2 pt-1.5 pb-1 h-full flex flex-col justify-between pointer-events-none overflow-hidden">
                                            <div class="overflow-hidden">
                                                <p class="text-[8px] font-black text-blue-300/70 uppercase tracking-tighter" x-text="'Ch' + chapter.chapter_number + ' SC' + scene.scene_number"></p>
                                                <p class="text-[7px] font-semibold text-gray-500 truncate mt-0.5" x-text="scene.narration_text"></p>
                                            </div>
                                            <span class="text-[8px] font-bold text-white/20 group-hover/clip:text-white/50 transition-colors" x-text="scene.duration_seconds + 's'"></span>
                                        </div>

                                        {{-- Resize Handle (right edge) --}}
                                        <div
                                            class="absolute top-0 right-0 w-4 h-full cursor-ew-resize z-20 flex items-center justify-end pr-0.5 opacity-0 group-hover/clip:opacity-100 transition-opacity"
                                            @mousedown.stop="startResize($event, cIdx, sIdx)"
                                        >
                                            <div class="w-1 h-10 bg-white/25 rounded-full hover:bg-white/60 transition-colors"></div>
                                        </div>
                                    </div>
                                </template>

                                {{-- Chapter gap indicator --}}
                                <div class="w-4 h-full flex items-center justify-center flex-shrink-0 relative">
                                    <div class="w-[1px] h-full bg-zinc-700/50"></div>
                                    <span class="absolute top-1 text-[7px] font-black text-zinc-700 uppercase rotate-90" x-text="'Ch'+chapter.chapter_number" style="white-space:nowrap"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- AUDIO TRACK --}}
                <div class="h-10 bg-[#0a0a0a] flex items-stretch flex-shrink-0 overflow-x-auto">
                    <div class="w-16 shrink-0 bg-[#1a1a1a] border-r border-black flex items-center justify-center sticky left-0">
                        <span class="text-[9px] font-black text-gray-600 uppercase tracking-widest">A1</span>
                    </div>
                    <div class="flex items-center gap-[2px] px-1.5 h-full overflow-x-visible flex-nowrap">
                        <template x-for="(chapter, cIdx) in (project.chapters || [])" :key="chapter.id">
                            <div class="flex items-center gap-[2px] h-full flex-shrink-0">
                                <template x-for="(scene, sIdx) in (chapter.scenes || [])" :key="scene.id">
                                    <div
                                        class="h-[70%] rounded border border-green-500/10 bg-green-900/10 flex-shrink-0 overflow-hidden"
                                        :style="'width: ' + Math.max(80, scene.duration_seconds * 15) + 'px'"
                                    >
                                        {{-- Fake waveform bars --}}
                                        <div class="flex items-end h-full gap-[1px] px-1 pb-0.5">
                                            <template x-for="b in Math.max(4, Math.floor(scene.duration_seconds))">
                                                <div class="flex-1 rounded-sm bg-green-500/30 hover:bg-green-400/50 transition-colors" :style="'height: ' + (30 + Math.random() * 50) + '%'"></div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                                <div class="w-4 flex-shrink-0"></div>
                            </div>
                        </template>
                    </div>
                </div>
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
