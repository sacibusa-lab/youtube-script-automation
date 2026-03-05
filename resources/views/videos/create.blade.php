<x-app-layout>
    <div class="py-12 px-4 sm:px-6 lg:px-8 font-outfit" x-data="projectWizard()">
        
        <!-- Header Section -->
        <div class="max-w-5xl mx-auto mb-12 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-4 bg-clip-text text-transparent bg-gradient-to-r from-teal-600 to-cyan-700 dark:from-teal-400 dark:to-cyan-500">
                Launch Mission
            </h1>
            <p class="text-zinc-500 dark:text-zinc-400 text-lg max-w-2xl mx-auto">
                Configure your narrative architecture. Our AI Engine will handle the cinematic orchestration.
            </p>
        </div>

        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Main Wizard Area -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- Progress Stepper -->
                <div class="bg-zinc-50 dark:bg-zinc-900/50 backdrop-blur-xl border border-zinc-200 dark:border-zinc-800 rounded-3xl p-6 mb-8 flex items-center justify-between shadow-sm dark:shadow-none">
                    <template x-for="n in 4" :key="n">
                        <div class="flex items-center flex-1 last:flex-none">
                            <div 
                                :class="step >= n ? 'bg-teal-500 text-white dark:text-black shadow-[0_0_15px_rgba(20,184,166,0.3)] dark:shadow-[0_0_15px_rgba(20,184,166,0.5)]' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500'" 
                                class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition-all duration-500"
                                x-text="n"
                            ></div>
                            <div 
                                x-show="n < 4" 
                                :class="step > n ? 'bg-teal-500' : 'bg-zinc-200 dark:bg-zinc-800'" 
                                class="h-1 flex-1 mx-4 rounded-full transition-all duration-700"
                            ></div>
                        </div>
                    </template>
                </div>

                <form id="project-form" method="POST" action="{{ route('projects.store') }}" @submit.prevent="submitForm">
                    @csrf
                    
                    <!-- STEP 1: MARKET & NICHE -->
                    <div x-show="step === 1" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-8">
                        <div>
                            <h2 class="text-2xl font-bold mb-6 flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center text-sm">01</span>
                                Market Foundation
                            </h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <template x-for="(label, id) in strategies" :key="id">
                                    <button 
                                        type="button"
                                        @click="form.strategy = id"
                                        :class="form.strategy === id ? 'border-teal-500 bg-teal-500/5 ring-1 ring-teal-500' : 'border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 hover:border-zinc-300 dark:hover:border-zinc-700'"
                                        class="p-4 rounded-2xl border text-left transition-all group relative overflow-hidden"
                                    >
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-bold tracking-wider uppercase text-zinc-400 dark:text-zinc-500" x-text="'Strategy ' + id"></span>
                                            <div x-show="form.strategy === id" class="w-5 h-5 bg-teal-500 rounded-full flex items-center justify-center">
                                                <svg class="w-3 h-3 text-white dark:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                        </div>
                                        <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-1" x-text="label"></h3>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-500" x-text="strategyDescriptions[id]"></p>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div x-show="form.strategy" class="space-y-4">
                            <h3 class="text-sm font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">Select Narrative Niche</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <template x-for="niche in availableNiches" :key="niche.id">
                                    <button 
                                        type="button"
                                        @click="form.niche_id = niche.id; form.niche_name = niche.name"
                                        :class="form.niche_id == niche.id ? 'border-teal-500 bg-teal-500/10 text-teal-600 dark:text-teal-400' : 'border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:border-zinc-400 dark:hover:border-zinc-600'"
                                        class="px-4 py-3 rounded-xl border text-sm font-bold transition-all text-center"
                                        x-text="niche.name"
                                    ></button>
                                </template>
                            </div>
                            <input type="hidden" name="niche_id" :value="form.niche_id">
                        </div>

                        <div class="pt-4">
                            <h3 class="text-sm font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-4">Target Geographic Audience</h3>
                            <div class="flex flex-wrap gap-3">
                                <template x-for="country in countries" :key="country">
                                    <button 
                                        type="button"
                                        @click="form.tier1_country = country"
                                        :class="form.tier1_country === country ? 'bg-zinc-900 text-white dark:bg-white dark:text-black' : 'bg-zinc-50 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600'"
                                        class="px-6 py-2 rounded-full text-xs font-bold transition-all"
                                        x-text="country"
                                    ></button>
                                </template>
                            </div>
                            <input type="hidden" name="tier1_country" :value="form.tier1_country">
                        </div>
                    </div>

                    <!-- STEP 2: STORY CORE -->
                    <div x-show="step === 2" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-8">
                        <div>
                            <h2 class="text-2xl font-bold mb-6 flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center text-sm">02</span>
                                Narrative Architecture
                            </h2>

                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-sm font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-4">Content Framework</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($structures as $structure)
                                        <button 
                                            type="button"
                                            @click="form.structure_id = '{{ $structure->id }}'; form.structure_name = '{{ $structure->name }}'"
                                            :class="form.structure_id == '{{ $structure->id }}' ? 'border-teal-500 bg-teal-500/5 ring-1 ring-teal-500' : 'border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 hover:border-zinc-300 dark:hover:border-zinc-700'"
                                            class="p-5 rounded-2xl border text-left transition-all flex items-center justify-between group"
                                        >
                                            <span class="font-bold text-zinc-700 dark:text-zinc-300 group-hover:text-zinc-900 dark:group-hover:text-white transition-colors">{{ $structure->name }}</span>
                                            <div :class="form.structure_id == '{{ $structure->id }}' ? 'border-teal-500 bg-teal-500' : 'border-zinc-300 dark:border-zinc-700'" class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all">
                                                <div x-show="form.structure_id == '{{ $structure->id }}'" class="w-2 h-2 rounded-full bg-white dark:bg-black"></div>
                                            </div>
                                        </button>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="content_structure_id" :value="form.structure_id">
                                </div>

                                <div>
                                    <h3 class="text-sm font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-4">Dominant Emotional Frequency</h3>
                                    <div class="flex flex-wrap gap-3">
                                        @foreach($emotions as $emotion)
                                        <button 
                                            type="button"
                                            @click="form.emotion_id = '{{ $emotion->id }}'; form.emotion_name = '{{ $emotion->name }}'"
                                            :class="form.emotion_id == '{{ $emotion->id }}' ? 'bg-cyan-600 dark:bg-cyan-500 text-white dark:text-black shadow-lg dark:shadow-[0_0_15px_rgba(6,182,212,0.4)]' : 'bg-zinc-50 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600'"
                                            class="px-6 py-3 rounded-xl text-sm font-bold transition-all"
                                        >
                                            {{ $emotion->name }}
                                        </button>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="emotional_tone_id" :value="form.emotion_id">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: ENGINE CONFIG -->
                    <div x-show="step === 3" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-10">
                        <div>
                            <h2 class="text-2xl font-bold mb-8 flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center text-sm">03</span>
                                Engine Tuning
                            </h2>

                            <div class="space-y-12">
                                <!-- Hybrid Intensity Slider -->
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-sm font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">Hybrid Narrative Intensity</h3>
                                        <span class="text-2xl font-black text-teal-600 dark:text-teal-400" x-text="form.intensity + '%'"></span>
                                    </div>
                                    <input type="range" name="hybrid_intensity" min="10" max="90" step="10" x-model="form.intensity" 
                                        class="w-full h-2 bg-zinc-200 dark:bg-zinc-800 rounded-lg appearance-none cursor-pointer accent-teal-500">
                                    <div class="flex justify-between text-[10px] text-zinc-500 dark:text-zinc-600 mt-2 font-bold uppercase tracking-tighter">
                                        <span>Documentary-Heavy</span>
                                        <span>Balanced</span>
                                        <span>Cinematic-Heavy</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <!-- Risk Mode -->
                                    <div>
                                        <h3 class="text-sm font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-4">Cognitive Risk Mode</h3>
                                        <div class="grid grid-cols-1 gap-3">
                                            <template x-for="mode in riskModes" :key="mode">
                                                <button 
                                                    type="button"
                                                    @click="form.risk_mode = mode"
                                                    :class="form.risk_mode === mode ? 'border-red-500 bg-red-500/10 text-red-600 dark:text-red-500' : 'border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:border-zinc-300 dark:hover:border-zinc-700'"
                                                    class="p-4 rounded-xl border text-sm font-bold transition-all flex items-center gap-3"
                                                >
                                                    <div :class="form.risk_mode === mode ? 'bg-red-500' : 'bg-zinc-300 dark:bg-zinc-800'" class="w-2 h-2 rounded-full"></div>
                                                    <span x-text="mode"></span>
                                                </button>
                                            </template>
                                        </div>
                                        <input type="hidden" name="risk_mode" :value="form.risk_mode">
                                    </div>

                                    <!-- Duration -->
                                    <div>
                                        <h3 class="text-sm font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-4">Mission Duration</h3>
                                        <div class="grid grid-cols-3 gap-3">
                                            <template x-for="dur in durations" :key="dur">
                                                <button 
                                                    type="button"
                                                    @click="form.duration = dur"
                                                    :class="form.duration == dur ? 'bg-zinc-900 text-white dark:bg-white dark:text-black border-transparent' : 'bg-zinc-50 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-800'"
                                                    class="h-20 rounded-xl flex flex-col items-center justify-center font-bold border hover:border-zinc-400 dark:hover:border-zinc-600 transition-all"
                                                >
                                                    <span class="text-xl" x-text="dur === 1 ? '60s' : dur"></span>
                                                    <span class="text-[10px] uppercase opacity-50" x-text="dur === 1 ? 'Short' : 'Minutes'"></span>
                                                </button>
                                            </template>
                                        </div>
                                        <input type="hidden" name="duration_minutes" :value="form.duration">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 4: CONFIRM & LAUNCH -->
                    <div x-show="step === 4" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0" class="space-y-8">
                        <div class="text-center py-8">
                            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-teal-500/10 text-teal-600 dark:text-teal-400 mb-6 border border-teal-500/20 animate-pulse">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <h2 class="text-3xl font-extrabold mb-2">Ready to Ignite?</h2>
                            <p class="text-zinc-500 dark:text-zinc-400">Review your parameters and launch the story engine.</p>
                        </div>

                        <div class="bg-zinc-50 dark:bg-zinc-900/80 rounded-3xl p-8 border border-zinc-200 dark:border-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-800">
                            <div class="grid grid-cols-2 gap-4 pb-6">
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-zinc-400 dark:text-zinc-600 mb-1 tracking-widest">Niche & Strategy</p>
                                    <p class="text-lg font-bold text-zinc-900 dark:text-white" x-text="form.niche_name || 'Not Selected'"></p>
                                    <p class="text-xs text-zinc-500" x-text="strategies[form.strategy] || ''"></p>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-zinc-400 dark:text-zinc-600 mb-1 tracking-widest">Target Market</p>
                                    <p class="text-lg font-bold text-zinc-900 dark:text-white" x-text="form.tier1_country"></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 py-6">
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-zinc-400 dark:text-zinc-600 mb-1 tracking-widest">Narrative Style</p>
                                    <p class="text-lg font-bold text-zinc-900 dark:text-white" x-text="form.structure_name || 'Default'"></p>
                                    <p class="text-xs text-zinc-500" x-text="form.emotion_name || ''"></p>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-zinc-400 dark:text-zinc-600 mb-1 tracking-widest">Engine Tuning</p>
                                    <p class="text-lg font-bold text-zinc-900 dark:text-white" x-text="form.intensity + '% Intensity'"></p>
                                    <p class="text-xs text-zinc-500" x-text="form.risk_mode + ' Risk • ' + form.duration + 'm Duration'"></p>
                                </div>
                            </div>
                        </div>

                        <button 
                            type="submit" 
                            :disabled="isSubmitting"
                            class="w-full py-6 rounded-2xl bg-gradient-to-r from-teal-500 to-cyan-600 text-white dark:text-black font-black text-xl shadow-xl dark:shadow-[0_20px_50px_rgba(20,184,166,0.3)] hover:shadow-teal-500/50 hover:-translate-y-1 active:scale-95 transition-all flex items-center justify-center gap-3 border-t border-white/20"
                        >
                            <span x-show="!isSubmitting">IGNITE ENGINE</span>
                            <span x-show="isSubmitting" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                ORCHESTRATING...
                            </span>
                        </button>
                    </div>

                    <!-- Navigation Footer -->
                    <div class="pt-12 flex items-center justify-between border-t border-zinc-100 dark:border-zinc-900 mt-8">
                        <button 
                            type="button" 
                            x-show="step > 1" 
                            @click="step--" 
                            class="px-8 py-3 rounded-xl border border-zinc-200 dark:border-zinc-800 text-zinc-500 dark:text-zinc-400 font-bold hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors"
                        >
                            Back
                        </button>
                        <div class="flex-1"></div>
                        <button 
                            type="button" 
                            x-show="step < 4" 
                            @click="nextStep()" 
                            class="group px-10 py-4 rounded-xl bg-zinc-900 text-white dark:bg-zinc-100 dark:text-black font-extrabold hover:bg-black dark:hover:bg-white transition-all flex items-center gap-2 shadow-lg"
                        >
                            Continue
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Sidebar Briefing -->
            <div class="lg:col-span-4 sticky top-12">
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[32px] overflow-hidden shadow-2xl dark:shadow-none">
                    <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 bg-gradient-to-br from-zinc-50 to-white dark:from-zinc-900 dark:to-zinc-800">
                        <h3 class="text-sm font-black tracking-widest uppercase text-zinc-400 dark:text-zinc-500">Mission Briefing</h3>
                    </div>
                    <div class="p-8 space-y-8">
                        <div>
                            <p class="text-[10px] text-zinc-400 dark:text-zinc-600 font-bold uppercase tracking-widest mb-3">Project Persona</p>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-teal-600 dark:text-teal-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-zinc-900 dark:text-white font-bold leading-tight" x-text="form.niche_name || 'Undefined Identity'"></p>
                                    <p class="text-xs text-zinc-500" x-text="form.tier1_country + ' Regional Focus'"></p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <p class="text-[10px] text-zinc-400 dark:text-zinc-600 font-bold uppercase tracking-widest mb-3">Core Payload</p>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-zinc-500">Architecture</span>
                                    <span class="text-zinc-900 dark:text-white font-bold" x-text="form.structure_name || 'Pending'"></span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-zinc-500">Emotional Bias</span>
                                    <span class="text-zinc-900 dark:text-white font-bold" x-text="form.emotion_name || 'Pending'"></span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-zinc-500">Duration</span>
                                    <span class="text-zinc-900 dark:text-white font-bold" x-text="form.duration === 1 ? 'YouTube Short (60s)' : form.duration + ' Minutes'"></span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-zinc-100 dark:border-zinc-800">
                            <p class="text-[10px] text-zinc-400 dark:text-zinc-600 font-bold uppercase tracking-widest mb-4">Orchestration Probability</p>
                            <div class="flex items-end gap-1 mb-2">
                                <template x-for="i in 5">
                                    <div :class="i <= (form.strategy ? (parseInt(form.strategy) + 2) : 1) ? 'bg-teal-500' : 'bg-zinc-100 dark:bg-zinc-800'" class="w-full h-1.5 rounded-full transition-all duration-1000"></div>
                                </template>
                            </div>
                            <p class="text-[10px] text-zinc-500">AI stability is verified for these parameters.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function projectWizard() {
            return {
                step: 1,
                isSubmitting: false,
                form: {
                    strategy: '',
                    niche_id: '',
                    niche_name: '',
                    tier1_country: 'USA',
                    structure_id: '',
                    structure_name: '',
                    emotion_id: '',
                    emotion_name: '',
                    intensity: 50,
                    risk_mode: 'Safe',
                    duration: 30
                },
                strategies: {
                    '1': 'High CPM (Wealth & Tech)',
                    '2': 'High Retention (Mystery & History)',
                    '3': 'Viral Potential (Survival)'
                },
                strategyDescriptions: {
                    '1': 'Target capital-heavy audiences with premium production values.',
                    '2': 'Optimized for deep storytelling and maximum watch time.',
                    '3': 'Focused on hook velocity and mass-market psychological triggers.'
                },
                riskModes: ['Safe', 'Moderate', 'Aggressive'],
                durations: [1, 30, 45, 60],
                countries: ['USA', 'UK', 'Canada', 'Australia', 'Ireland'],
                niches: @json($niches),

                get availableNiches() {
                    return this.form.strategy ? this.niches[this.form.strategy] : [];
                },

                nextStep() {
                    if (this.step === 1 && (!this.form.strategy || !this.form.niche_id)) {
                        alert('Please select a Strategy and Niche to proceed.');
                        return;
                    }
                    if (this.step === 2 && (!this.form.structure_id || !this.form.emotion_id)) {
                        alert('Please select Narrative Structure and Emotion.');
                        return;
                    }
                    this.step++;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                submitForm() {
                    this.isSubmitting = true;
                    // Actual submission
                    this.$el.submit();
                }
            }
        }
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&display=swap');
        .font-outfit { font-family: 'Outfit', sans-serif; }
        
        /* Custom scrollbar for stealth look */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .dark ::-webkit-scrollbar-thumb { background: #27272a; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .dark ::-webkit-scrollbar-thumb:hover { background: #3f3f46; }

        input[type=range]::-webkit-slider-thumb {
            -webkit-appearance: none;
            height: 24px;
            width: 24px;
            border-radius: 50%;
            background: #14b8a6;
            cursor: pointer;
            box-shadow: 0 0 15px rgba(20, 184, 166, 0.3);
            border: 4px solid #fff;
        }
        .dark input[type=range]::-webkit-slider-thumb {
            box-shadow: 0 0 15px rgba(20, 184, 166, 0.5);
            border-color: #000;
        }
    </style>
</x-app-layout>


