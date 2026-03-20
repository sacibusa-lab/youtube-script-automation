<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-xl text-gray-800 dark:text-white leading-tight uppercase italic tracking-tight">
                {{ __('Voice Generation Lab') }}
            </h2>
            <div class="flex items-center gap-3">
                <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Balance</span>
                <span class="px-3 py-1 bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 rounded-lg text-xs font-black shadow-sm border border-purple-100 dark:border-purple-800">
                    {{ number_format(Auth::user()->voiceTokensBalance()) }} V
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="voiceLab()">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- INPUT SECTION --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-zinc-900 rounded-[32px] border border-zinc-200 dark:border-zinc-800 p-8 shadow-sm">
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-xs font-black text-purple-600 uppercase tracking-[0.2em]">Input Text</h3>
                                <div class="flex items-center gap-2 px-3 py-1 bg-zinc-900 text-white rounded-lg select-none">
                                    <span class="text-[9px] font-black uppercase tracking-tighter opacity-70">Projected Cost:</span>
                                    <span class="text-[11px] font-bold font-outfit" x-text="text.length">0</span>
                                    <span class="text-[9px] font-black text-purple-400">Tokens</span>
                                </div>
                            </div>
                            <textarea 
                                x-model="text" 
                                class="w-full bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 text-sm font-medium focus:ring-purple-500/20 focus:border-purple-600 transition-all min-h-[200px]"
                                placeholder="Type or paste the text you want to turn into speech..."></textarea>
                            <div class="flex justify-end mt-2">
                                <span class="text-[10px] font-bold text-zinc-400" x-text="text.length + ' / 5000 characters'"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2 block">Voice Artist</label>
                                <select x-model="voiceId" class="w-full bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-sm font-bold focus:ring-purple-500/20 focus:border-purple-600 cursor-pointer">
                                    <template x-for="voice in voices" :key="voice.id">
                                        <option :value="voice.id" x-text="voice.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="flex items-center gap-4 pt-6">
                                <div class="flex-1">
                                    <label class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2 block text-center" x-text="'Speed: ' + speed">Speed</label>
                                    <input type="range" x-model="speed" min="0.5" max="2.0" step="0.1" class="w-full accent-purple-600">
                                </div>
                            </div>
                        </div>

                        <button 
                            @click="generateVoice()" 
                            :disabled="loading || !text"
                            class="w-full py-4 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-2xl font-black text-sm uppercase tracking-widest hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-xl shadow-zinc-200 dark:shadow-none flex items-center justify-center gap-3">
                            <template x-if="!loading">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                            </template>
                            <template x-if="loading">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </template>
                            <span x-text="loading ? 'Synthesizing...' : 'Generate Voiceover'"></span>
                        </button>
                    </div>
                </div>

                {{-- PREVIEW SECTION --}}
                <div class="space-y-6">
                    <div class="bg-zinc-50 dark:bg-zinc-950 rounded-[32px] border border-zinc-200 dark:border-zinc-800 p-8">
                        <h3 class="text-xs font-black text-zinc-400 uppercase tracking-[0.2em] mb-6">Recent Generation</h3>
                        
                        <div x-show="!lastAudioUrl" class="text-center py-12">
                            <div class="w-16 h-16 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 flex items-center justify-center mx-auto mb-4 text-zinc-300">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">No audio generated yet</p>
                        </div>

                        <div x-show="lastAudioUrl" x-transition class="space-y-6">
                            <div class="p-6 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
                                <audio x-ref="audioPlayer" :src="lastAudioUrl" class="hidden" @ended="playing = false"></audio>
                                
                                <div class="flex items-center gap-4 relative z-10">
                                    <button 
                                        @click="togglePlay()"
                                        class="w-12 h-12 rounded-full bg-purple-600 text-white flex items-center justify-center hover:scale-110 transition-transform shadow-lg shadow-purple-500/20">
                                        <svg x-show="!playing" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                                        <svg x-show="playing" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    </button>
                                    <div>
                                        <p class="text-xs font-black text-zinc-900 dark:text-white uppercase italic">Ready to Play</p>
                                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest mt-0.5" x-text="voiceId"></p>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-between items-center">
                                    <a :href="lastAudioUrl" download class="text-[10px] font-black text-purple-600 uppercase tracking-widest hover:underline">Download Audio</a>
                                    <span class="text-[9px] font-black text-zinc-300 uppercase italic">Last Result</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-purple-600 rounded-[32px] text-white overflow-hidden relative group">
                        <div class="relative z-10">
                            <h4 class="text-sm font-black uppercase italic tracking-tight mb-2">Need Story Narration?</h4>
                            <p class="text-[11px] text-purple-100 font-medium leading-relaxed mb-4">Go to the Story Voices studio to generate audio for your specific story scenes and chapters.</p>
                            <a href="{{ route('story-voices.index') }}" class="inline-flex items-center gap-2 bg-white text-purple-600 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-purple-50 transition-colors shadow-sm">
                                Open Story Studio
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </a>
                        </div>
                        <svg class="absolute -right-4 -bottom-4 w-24 h-24 text-purple-500/30 rotate-12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function voiceLab() {
            return {
                text: '',
                voiceId: 'af_heart',
                speed: 1.0,
                loading: false,
                lastAudioUrl: null,
                playing: false,
                voices: @json((new \App\Services\Media\VoiceOverService())->getAvailableVoices()),

                async generateVoice() {
                    if (!this.text || this.loading) return;
                    
                    this.loading = true;
                    try {
                        const response = await fetch("{{ route('voice-generation.generate') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                text: this.text,
                                voice_id: this.voiceId,
                                speed: this.speed,
                                volume: 1.0
                            })
                        });

                        const result = await response.json();
                        if (result.success) {
                            this.lastAudioUrl = result.audio_url;
                            // Update global balance
                            if (result.tokens_remaining !== undefined) {
                                this.tokenBalance = result.tokens_remaining;
                                window.dispatchEvent(new CustomEvent('tokens-updated', { 
                                    detail: { voice_tokens: result.tokens_remaining } 
                                }));
                            }
                        } else {
                            alert(result.message || 'Error generating voice');
                        }
                    } catch (e) {
                        console.error(e);
                        alert('Synthesis service unavailable');
                    } finally {
                        this.loading = false;
                    }
                },

                togglePlay() {
                    const audio = this.$refs.audioPlayer;
                    if (this.playing) {
                        audio.pause();
                        this.playing = false;
                    } else {
                        audio.play();
                        this.playing = true;
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
