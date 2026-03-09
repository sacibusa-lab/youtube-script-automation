<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $siteSettings['platform_name'] ?? config('app.name', 'StoryBee') }} | Cinematic AI Video Automation</title>

    @if(isset($siteSettings['favicon']))
        <link rel="icon" type="image/png" href="{{ Storage::url($siteSettings['favicon']) }}">
    @else
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>⚡</text></svg>">
    @endif
    
    <!-- Fonts: Outfit & Public Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --brand-primary: #14b8a6;
            --brand-secondary: #0f766e;
            --dark-bg: #050505;
            --dark-card: #0f0f0f;
            --accent-glow: rgba(20, 184, 166, 0.15);
        }

        body {
            font-family: 'Public Sans', sans-serif;
            background-color: var(--dark-bg);
            color: #fafafa;
        }

        .font-outfit { font-family: 'Outfit', sans-serif; }

        .glass {
            background: rgba(15, 15, 15, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .text-gradient {
            background: linear-gradient(135deg, #fff 0%, #a1a1aa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .text-teal-gradient {
            background: linear-gradient(135deg, #2dd4bf 0%, #0d9488 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-premium {
            background: #fff;
            color: #000;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-premium:hover {
            transform: scale(1.02);
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.2);
        }

        .hero-glow {
            position: absolute;
            top: -10%;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            height: 60%;
            background: radial-gradient(circle at center, var(--accent-glow) 0%, transparent 70%);
            z-index: -1;
            pointer-events: none;
        }

        .animate-reveal {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .animate-reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .pipeline-step {
            position: relative;
        }

        .pipeline-line {
            position: absolute;
            top: 2rem;
            left: 50%;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, rgba(20, 184, 166, 0.3) 0%, transparent 100%);
            z-index: -1;
        }

        @media (max-width: 768px) {
            .pipeline-line { display: none; }
        }

        /* Scanline effect */
        .scanline {
            width: 100%;
            height: 100px;
            z-index: 10;
            background: linear-gradient(0deg, transparent 0%, rgba(20, 184, 166, 0.05) 50%, transparent 100%);
            opacity: 0.1;
            position: absolute;
            bottom: 100%;
            animation: scanline 8s linear infinite;
        }

        @keyframes scanline {
            0% { bottom: 100%; }
            100% { bottom: -100px; }
        /* Accordion specific */
        .faq-answer {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
    </style>
</head>
<body class="antialiased overflow-x-hidden" x-data="{ 
    scrolled: false,
    reveal() {
        const reveals = document.querySelectorAll('.animate-reveal');
        reveals.forEach(el => {
            const windowHeight = window.innerHeight;
            const elementTop = el.getBoundingClientRect().top;
            const elementVisible = 150;
            if (elementTop < windowHeight - elementVisible) {
                el.classList.add('visible');
            }
        });
    }
}" x-init="reveal(); window.addEventListener('scroll', () => { scrolled = window.scrollY > 20; reveal(); })">
    
    <div class="hero-glow"></div>

    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 transition-all duration-500" :class="scrolled ? 'glass py-4' : 'py-8'">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
            <div class="flex items-center gap-2">
                @if(isset($siteSettings['logo']))
                    <img src="{{ Storage::url($siteSettings['logo']) }}" alt="Logo" class="h-8 w-auto object-contain">
                @else
                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-black" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99z"/></svg>
                    </div>
                @endif
                <span class="text-xl font-outfit font-black tracking-tighter uppercase whitespace-nowrap">{{ $siteSettings['platform_name'] ?? 'STORYBEE' }}</span>
            </div>

            <div class="hidden md:flex items-center gap-10">
                <a href="#pipeline" class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-400 hover:text-white transition-colors">The Pipeline</a>
                <a href="#features" class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-400 hover:text-white transition-colors">Features</a>
                <a href="#demos" class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-400 hover:text-white transition-colors">Demos</a>
                <a href="#pricing" class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-400 hover:text-white transition-colors">Pricing</a>
                <a href="#faq" class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-400 hover:text-white transition-colors">FAQ</a>
                <a href="{{ route('login') }}" class="text-[11px] font-black uppercase tracking-[0.2em] text-zinc-400 hover:text-white transition-colors">Access</a>
                <a href="#pricing" class="btn-premium px-8 py-3 rounded-full text-[11px] font-black uppercase tracking-widest">Join Beta</a>
            </div>

            <button class="md:hidden text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg></button>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-48 pb-32 px-6">
        <div class="max-w-7xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-white/10 bg-white/5 mb-10 animate-reveal">
                <span class="text-[10px] font-black text-white bg-white/10 px-4 py-2 rounded-full border border-white/20 uppercase tracking-[0.3em] backdrop-blur-md">StoryBee: v2.5 Next-Gen Engine Active</span>
            </div>
            
            <h1 class="text-6xl md:text-8xl font-black font-outfit uppercase tracking-tighter leading-[0.85] mt-8 mb-6 relative z-10">
                Viral YouTube <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-br from-red-500 via-amber-500 to-teal-500 animate-pulse-slow">Story Builder.</span>
            </h1>
            
            <p class="text-zinc-400 text-lg md:text-2xl max-w-2xl mx-auto font-medium mb-12 relative z-10">
                The world's most advanced platform for generating high-CPM YouTube story marathons. Scripted for Tier-1 markets, visualized for maximum retention.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-6 animate-reveal">
                <a href="{{ route('register') }}" class="btn-premium px-12 py-5 rounded-2xl text-[13px] font-black uppercase tracking-widest">
                    Start Production
                </a>
                <a href="#pipeline" class="px-12 py-5 rounded-2xl text-[13px] font-black uppercase tracking-widest border border-white/10 hover:bg-white/5 transition-all text-white">
                    View Pipeline
                </a>
            </div>

            <!-- Command Center Mockup -->
            <div class="mt-32 relative px-4 animate-reveal">
                <div class="glass p-3 rounded-[3rem] shadow-2xl relative overflow-hidden group">
                    <div class="scanline"></div>
                    <div class="absolute inset-0 bg-gradient-to-b from-teal-500/5 to-transparent"></div>
                    <img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&q=80&w=2070" 
                         alt="System Terminal Interface" 
                         class="rounded-[2.5rem] w-full grayscale contrast-125 opacity-80 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-1000 border border-white/5">
                    
                    <!-- Floating Data Nodes -->
                    <div class="absolute bottom-12 left-12 glass px-6 py-4 rounded-2xl hidden lg:block border border-teal-500/20">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-teal-500 animate-pulse"></div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-teal-500">Processing Node Alpha</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Platform Compatibility -->
    <section class="py-20 border-y border-white/5 bg-zinc-950/50">
        <div class="max-w-7xl mx-auto px-6">
            <p class="text-[10px] font-black text-zinc-600 uppercase tracking-[0.5em] text-center mb-10">Optimized for</p>
            <div class="flex flex-wrap justify-center items-center gap-12 md:gap-24 opacity-30 grayscale hover:grayscale-0 transition-all duration-500">
                <span class="text-3xl font-black italic tracking-tighter">YouTube</span>
                <span class="text-3xl font-black italic tracking-tighter">TikTok</span>
                <span class="text-3xl font-black italic tracking-tighter">Snapchat</span>
                <span class="text-3xl font-black italic tracking-tighter">Meta AI</span>
            </div>
        </div>
    </section>

    <!-- Production Pipeline Section -->
    <section id="pipeline" class="py-40 px-6 relative overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-24 animate-reveal">
                <span class="text-[10px] font-black text-teal-500 uppercase tracking-[0.4em]">The Engine</span>
                <h2 class="text-5xl font-outfit font-black mt-4">Automated Workflow.</h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 relative">
                <!-- Step 1 -->
                <div class="glass p-8 rounded-[2rem] relative z-10 group hover:-translate-y-2 transition-transform duration-500 text-left animate-reveal">
                    <div class="text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-6 border-b border-white/5 pb-4">Phase 01 / Script</div>
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-teal-500/20 to-transparent border border-teal-500/30 flex items-center justify-center mb-8">
                        <svg class="w-8 h-8 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 font-outfit">Story Architect</h3>
                    <p class="text-zinc-500 text-sm leading-relaxed">Seamless 10-chapter structural blueprints with 'Mega-Hook' technology for the first 30 seconds.</p>
                </div>

                <!-- Step 2 -->
                <div class="glass p-8 rounded-[2rem] relative z-10 group hover:-translate-y-2 transition-transform duration-500 text-left animate-reveal" style="transition-delay: 0.1s">
                    <div class="text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-6 border-b border-white/5 pb-4">Phase 02 / Visuals</div>
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-red-500/20 to-transparent border border-red-500/30 flex items-center justify-center mb-8">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 font-outfit">Cinematic 4K Grids</h3>
                    <p class="text-zinc-500 text-sm leading-relaxed">Generate cinematic 4K scene images with AI-powered prompt engineering. Match your story's emotional beats.</p>
                </div>

                <!-- Step 3 -->
                <div class="glass p-8 rounded-[2rem] relative z-10 group hover:-translate-y-2 transition-transform duration-500 text-left animate-reveal" style="transition-delay: 0.2s">
                    <div class="text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-6 border-b border-white/5 pb-4">Phase 03 / Audio</div>
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-500/20 to-transparent border border-amber-500/30 flex items-center justify-center mb-8">
                        <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 font-outfit">Expressive Narration</h3>
                    <p class="text-zinc-500 text-sm leading-relaxed">Convert your story scripts into expressive AI voice narration. Tuned for documentary and cinematic tones.</p>
                </div>

                <!-- Step 4 -->
                <div class="glass p-8 rounded-[2rem] relative z-10 group hover:-translate-y-2 transition-transform duration-500 text-left animate-reveal" style="transition-delay: 0.3s">
                    <div class="text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-6 border-b border-white/5 pb-4">Phase 04 / Output</div>
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-white/10 to-transparent border border-white/20 flex items-center justify-center mb-8">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2 font-outfit">Million-View Foundation</h3>
                    <p class="text-zinc-500 text-sm leading-relaxed">Design the foundation of your next million-view narrative. Combine elements to produce Tier-1 retention content.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-40 px-6 bg-zinc-950/30">
        <div class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-24 items-center">
                <div class="animate-reveal">
                    <span class="text-[10px] font-black text-amber-500 uppercase tracking-[0.4em]">Consistency Engine</span>
                    <h2 class="text-5xl font-outfit font-black mt-4 leading-tight">No more character <br> hallucinations.</h2>
                    <p class="text-zinc-500 mt-8 text-lg font-medium leading-relaxed">
                        Unlike generic video tools, StoryBee uses a proprietary "Character Roster" system. Define your protagonist once, and they stay consistent across every scene. No drift, no errors.
                    </p>
                    <div class="mt-12 space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center border border-white/5 shadow-inner">
                                <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="font-bold text-zinc-300">Permanent Visual IDs</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center border border-white/5 shadow-inner">
                                <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="font-bold text-zinc-300">Style Consistency Matrix</span>
                        </div>
                    </div>
                </div>
                <div class="relative animate-reveal">
                    <div class="glass p-2 rounded-[2.5rem] overflow-hidden border border-white/10">
                        <img src="https://images.unsplash.com/photo-1620641788421-7a1c342ea42e?auto=format&fit=crop&q=80&w=1974" class="rounded-[2rem] contrast-125 opacity-70 group-hover:opacity-100 transition-opacity">
                    </div>
                    <div class="absolute -top-10 -right-10 glass px-8 py-6 rounded-3xl border border-white/10 hidden xl:block animate-bounce shadow-2xl">
                        <div class="text-3xl font-black font-outfit">100%</div>
                        <div class="text-[10px] font-black uppercase text-zinc-500 tracking-widest mt-1">Consistency Match</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Outputs / Live Demos Section -->
    <section id="demos" class="py-40 px-6 border-t border-white/5">
        <div class="max-w-[100rem] mx-auto">
            <div class="text-center mb-24 animate-reveal">
                <span class="text-[10px] font-black text-teal-500 uppercase tracking-[0.4em]">Live Feeds</span>
                <h2 class="text-5xl font-outfit font-black mt-4">System Outputs.</h2>
                <p class="text-zinc-500 mt-4 text-lg max-w-2xl mx-auto">Unedited, 100% AI-generated sequences straight from our production clusters.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 animate-reveal">
                <!-- Demo Item 1 -->
                <div class="glass rounded-3xl overflow-hidden group relative aspect-[9/16]">
                    <img src="https://images.unsplash.com/photo-1618331835717-814cb2c8b0bc?auto=format&fit=crop&q=80&w=800" alt="Demo Output" class="absolute inset-0 w-full h-full object-cover grayscale opacity-60 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="absolute bottom-6 left-6 opacity-0 group-hover:opacity-100 transition-all duration-500 translate-y-4 group-hover:translate-y-0 text-left">
                        <span class="text-[9px] font-black text-white bg-teal-500/20 px-3 py-1 rounded backdrop-blur-md uppercase tracking-widest border border-teal-500/30">Sci-Fi Short</span>
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-500 scale-90 group-hover:scale-100">
                        <div class="w-16 h-16 rounded-full bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/20">
                            <svg class="w-6 h-6 text-white ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Demo Item 2 -->
                <div class="glass rounded-3xl overflow-hidden group relative aspect-[9/16] mt-8">
                    <img src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?auto=format&fit=crop&q=80&w=800" alt="Demo Output" class="absolute inset-0 w-full h-full object-cover grayscale opacity-60 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="absolute bottom-6 left-6 opacity-0 group-hover:opacity-100 transition-all duration-500 translate-y-4 group-hover:translate-y-0 text-left">
                        <span class="text-[9px] font-black text-white bg-rose-500/20 px-3 py-1 rounded backdrop-blur-md uppercase tracking-widest border border-rose-500/30">Nature Doc</span>
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-500 scale-90 group-hover:scale-100">
                        <div class="w-16 h-16 rounded-full bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/20">
                            <svg class="w-6 h-6 text-white ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Demo Item 3 -->
                <div class="glass rounded-3xl overflow-hidden group relative aspect-[9/16]">
                    <img src="https://images.unsplash.com/photo-1685356983804-62c1dc5cd1e8?auto=format&fit=crop&q=80&w=800" alt="Demo Output" class="absolute inset-0 w-full h-full object-cover grayscale opacity-60 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="absolute bottom-6 left-6 opacity-0 group-hover:opacity-100 transition-all duration-500 translate-y-4 group-hover:translate-y-0 text-left">
                        <span class="text-[9px] font-black text-white bg-amber-500/20 px-3 py-1 rounded backdrop-blur-md uppercase tracking-widest border border-amber-500/30">Cyberpunk Ad</span>
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-500 scale-90 group-hover:scale-100">
                        <div class="w-16 h-16 rounded-full bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/20">
                            <svg class="w-6 h-6 text-white ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Demo Item 4 -->
                <div class="glass rounded-3xl overflow-hidden group relative aspect-[9/16] mt-8">
                    <img src="https://images.unsplash.com/photo-1620641788421-7a1c342ea42e?auto=format&fit=crop&q=80&w=800" alt="Demo Output" class="absolute inset-0 w-full h-full object-cover grayscale opacity-60 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="absolute bottom-6 left-6 opacity-0 group-hover:opacity-100 transition-all duration-500 translate-y-4 group-hover:translate-y-0 text-left">
                        <span class="text-[9px] font-black text-white bg-indigo-500/20 px-3 py-1 rounded backdrop-blur-md uppercase tracking-widest border border-indigo-500/30">TikTok Reel</span>
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-500 scale-90 group-hover:scale-100">
                        <div class="w-16 h-16 rounded-full bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/20">
                            <svg class="w-6 h-6 text-white ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials / Social Proof -->
    <section class="py-32 px-6 border-y border-white/5 bg-zinc-950/40 overflow-hidden relative">
        <div class="absolute top-0 left-0 w-1/3 h-full bg-gradient-to-r from-zinc-950 to-transparent z-10 pointer-events-none"></div>
        <div class="absolute top-0 right-0 w-1/3 h-full bg-gradient-to-l from-zinc-950 to-transparent z-10 pointer-events-none"></div>
        
        <div class="max-w-7xl mx-auto mb-16 text-center animate-reveal relative z-20">
            <span class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.4em]">Validation Logs</span>
        </div>

        <div class="flex gap-8 overflow-hidden w-[200%] md:w-full animate-reveal opacity-80 hover:opacity-100 transition-opacity">
            <!-- Review Card 1 -->
            <div class="glass p-8 rounded-[2rem] min-w-[350px] shadow-2xl relative">
                <div class="text-teal-500 mb-6">★★★★★</div>
                <p class="text-lg font-medium text-zinc-300 mb-6 leading-relaxed">"The character consistency matrix is completely unmatched. We generate full 3-minute faceless YT videos and the protagonist never loses visual ID."</p>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-zinc-800 border border-white/10 overflow-hidden">
                        <img src="https://ui-avatars.com/api/?name=MK&background=random" alt="Avatar">
                    </div>
                    <div>
                        <div class="text-sm font-bold text-white uppercase tracking-wider font-outfit">Marcus K.</div>
                        <div class="text-[10px] text-zinc-500 uppercase tracking-widest">YouTube Strategist</div>
                    </div>
                </div>
            </div>

            <!-- Review Card 2 -->
            <div class="glass p-8 rounded-[2rem] min-w-[350px] shadow-2xl relative">
                <div class="text-teal-500 mb-6">★★★★★</div>
                <p class="text-lg font-medium text-zinc-300 mb-6 leading-relaxed">"StoryBee effectively replaced our entire $4k/mo editing offshore team. The pipeline just works. Scripts in, viral 4k visuals out."</p>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-zinc-800 border border-white/10 overflow-hidden">
                        <img src="https://ui-avatars.com/api/?name=SJ&background=random" alt="Avatar">
                    </div>
                    <div>
                        <div class="text-sm font-bold text-white uppercase tracking-wider font-outfit">Sarah J.</div>
                        <div class="text-[10px] text-zinc-500 uppercase tracking-widest">Agency Director</div>
                    </div>
                </div>
            </div>

             <!-- Review Card 3 -->
             <div class="glass p-8 rounded-[2rem] min-w-[350px] shadow-2xl relative hidden md:block">
                <div class="text-teal-500 mb-6">★★★★★</div>
                <p class="text-lg font-medium text-zinc-300 mb-6 leading-relaxed">"The SDXL integration combined with their automated prompt building means I don't need to be an AI engineer to get cinematic shots."</p>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-zinc-800 border border-white/10 overflow-hidden">
                        <img src="https://ui-avatars.com/api/?name=DT&background=random" alt="Avatar">
                    </div>
                    <div>
                        <div class="text-sm font-bold text-white uppercase tracking-wider font-outfit">David T.</div>
                        <div class="text-[10px] text-zinc-500 uppercase tracking-widest">Content Creator</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-40 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-24 animate-reveal">
                <span class="text-[10px] font-black text-rose-500 uppercase tracking-[0.4em]">Operations</span>
                <h2 class="text-5xl font-outfit font-black mt-4 tracking-tight leading-tight uppercase italic">Production Tiers.</h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($plans as $plan)
                    <div class="glass p-10 rounded-[3rem] flex flex-col transition-all duration-500 border border-white/5 hover:border-teal-500/30 hover:shadow-[0_0_50px_rgba(20,184,166,0.1)] group relative overflow-hidden animate-reveal">
                        <!-- Hover Glow Effect -->
                        <div class="absolute inset-0 bg-gradient-to-b from-teal-500/0 via-teal-500/0 to-teal-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>

                        @if($plan->name == 'Creator' || $plan->name == 'Standard')
                            <div class="absolute top-0 right-0 bg-white text-black text-[9px] font-black px-5 py-2 rounded-bl-3xl uppercase tracking-widest shadow-lg">
                                Popular
                            </div>
                        @endif

                        <div class="mb-10 relative z-10">
                            <h3 class="text-[10px] font-black group-hover:text-teal-400 text-zinc-500 uppercase tracking-[0.3em] mb-4 transition-colors">{{ $plan->name }}</h3>
                            <div class="flex items-baseline gap-1">
                                <span class="text-5xl font-outfit font-black text-white leading-none">&#8358;{{ number_format($plan->price) }}</span>
                                <span class="text-zinc-600 font-bold text-xs">/mo</span>
                            </div>
                        </div>

                        <div class="space-y-6 mb-12 flex-1">
                            <div class="flex items-center gap-4">
                                <span class="text-xs font-bold text-zinc-400 font-outfit uppercase tracking-tighter">{{ number_format($plan->monthly_credits / 1000) }}k Script Tokens</span>
                            </div>

                            <div class="flex items-center gap-4">
                                <span class="text-xs font-bold text-zinc-400 font-outfit uppercase tracking-tighter">
                                    @if($plan->monthly_image_tokens > 0)
                                        {{ number_format($plan->monthly_image_tokens) }} Scene Assets
                                    @else
                                        Pay-per-scene billing
                                    @endif
                                </span>
                            </div>

                            <div class="flex items-center gap-4">
                                <span class="text-xs font-bold text-zinc-400 font-outfit uppercase tracking-tighter">{{ $plan->max_images_per_script == 999 ? 'Unlimited' : $plan->max_images_per_script }} Frames / Story</span>
                            </div>

                            @if($plan->rollover_percent > 0)
                            <div class="flex items-center gap-4">
                                <span class="text-xs font-bold text-zinc-400 font-outfit uppercase tracking-tighter">{{ $plan->rollover_percent }}% Resource Carryover</span>
                            </div>
                            @endif
                        </div>

                        <a href="{{ route('register', ['plan_id' => $plan->id]) }}" 
                           class="w-full py-5 rounded-2xl text-center font-black text-[11px] uppercase tracking-widest transition-all {{ $plan->name == 'Creator' ? 'btn-premium' : 'bg-white/5 border border-white/5 text-white hover:bg-white/10' }}">
                            Select Directive
                        </a>
                    </div>
                @endforeach
            </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-40 px-6 bg-zinc-950/30 border-t border-white/5">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-24 animate-reveal">
                <span class="text-[10px] font-black text-teal-500 uppercase tracking-[0.4em]">Database Query</span>
                <h2 class="text-5xl font-outfit font-black mt-4">System FAQ.</h2>
            </div>

            <div class="space-y-4 animate-reveal" x-data="{ active: null }">
                <!-- FAQ 1 -->
                <div class="glass rounded-2xl border border-white/5 overflow-hidden transition-all duration-300" :class="active === 1 ? 'border-teal-500/30 shadow-[0_0_30px_rgba(20,184,166,0.05)]' : 'hover:border-white/10'">
                    <button @click="active = active === 1 ? null : 1" class="w-full px-8 py-6 flex items-center justify-between text-left focus:outline-none">
                        <span class="font-outfit font-bold text-lg text-white">Do my unused tokens roll over to the next month?</span>
                        <svg class="w-5 h-5 text-teal-500 transform transition-transform duration-300" :class="active === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="active === 1" x-collapse x-cloak class="faq-answer">
                        <div class="px-8 pb-6 text-zinc-400 leading-relaxed font-medium">
                            Yes. Depending on your subscription tier, a high percentage of your unused Script Tokens and Scene Assets will automatically carry over to the next billing cycle. Specifically, standard plans rollover up to 70%, while premium plans rollover up to 90%.
                        </div>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="glass rounded-2xl border border-white/5 overflow-hidden transition-all duration-300" :class="active === 2 ? 'border-teal-500/30 shadow-[0_0_30px_rgba(20,184,166,0.05)]' : 'hover:border-white/10'">
                    <button @click="active = active === 2 ? null : 2" class="w-full px-8 py-6 flex items-center justify-between text-left focus:outline-none">
                        <span class="font-outfit font-bold text-lg text-white">Who owns the commercial rights to the generated videos?</span>
                        <svg class="w-5 h-5 text-teal-500 transform transition-transform duration-300" :class="active === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7-7-7-7"></path></svg>
                    </button>
                    <div x-show="active === 2" x-collapse x-cloak class="faq-answer">
                        <div class="px-8 pb-6 text-zinc-400 leading-relaxed font-medium">
                            You do. You retain 100% full commercial rights and ownership of all scripts, images, audio, and compiled videos generated through your Axelit StoryBee account. You are free to monetize on YouTube, TikTok, or use them for client agency work without attribution.
                        </div>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="glass rounded-2xl border border-white/5 overflow-hidden transition-all duration-300" :class="active === 3 ? 'border-teal-500/30 shadow-[0_0_30px_rgba(20,184,166,0.05)]' : 'hover:border-white/10'">
                    <button @click="active = active === 3 ? null : 3" class="w-full px-8 py-6 flex items-center justify-between text-left focus:outline-none">
                        <span class="font-outfit font-bold text-lg text-white">How does the Character Consistency system actually work?</span>
                        <svg class="w-5 h-5 text-teal-500 transform transition-transform duration-300" :class="active === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7-7-7-7"></path></svg>
                    </button>
                    <div x-show="active === 3" x-collapse x-cloak class="faq-answer">
                        <div class="px-8 pb-6 text-zinc-400 leading-relaxed font-medium">
                            We utilize a proprietary "Character Roster" matrix alongside fine-tuned SDXL control networks. Instead of prompting for a generic character every scene, the text orchestrator locks in structural identifiers (ethnicity, clothing, distinct features) which are mathematically enforced during the image synthesis phase across every frame.
                        </div>
                    </div>
                </div>
                
                <!-- FAQ 4 -->
                <div class="glass rounded-2xl border border-white/5 overflow-hidden transition-all duration-300" :class="active === 4 ? 'border-teal-500/30 shadow-[0_0_30px_rgba(20,184,166,0.05)]' : 'hover:border-white/10'">
                    <button @click="active = active === 4 ? null : 4" class="w-full px-8 py-6 flex items-center justify-between text-left focus:outline-none">
                        <span class="font-outfit font-bold text-lg text-white">What happens if I run out of credits mid-month?</span>
                        <svg class="w-5 h-5 text-teal-500 transform transition-transform duration-300" :class="active === 4 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7-7-7-7"></path></svg>
                    </button>
                    <div x-show="active === 4" x-collapse x-cloak class="faq-answer">
                        <div class="px-8 pb-6 text-zinc-400 leading-relaxed font-medium">
                            Your account is never locked. You simply transition to Pay-per-scene billing using one-time Token Top-ups. You can purchase additional compute power directly from your dashboard without changing your baseline subscription tier.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-32 px-6 border-t border-white/5 bg-zinc-950/20">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-12">
                <div class="col-span-2 flex flex-col items-center md:items-start gap-6 text-center md:text-left">
                    <div class="flex items-center gap-2">
                        @if(isset($siteSettings['logo']))
                            <img src="{{ Storage::url($siteSettings['logo']) }}" alt="Logo" class="h-6 w-auto object-contain grayscale opacity-50">
                        @else
                            <div class="w-6 h-6 bg-white rounded flex items-center justify-center">
                                <svg class="w-4 h-4 text-black" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 3.99L19.53 19H4.47L12 5.99z"/></svg>
                            </div>
                        @endif
                        <span class="text-lg font-outfit font-black tracking-tighter uppercase whitespace-nowrap">{{ $siteSettings['platform_name'] ?? 'STORYBEE' }}</span>
                    </div>
                    <p class="text-sm text-zinc-600 font-medium max-w-xs">Building the future of automated viral content through neural cinematography.</p>
                </div>
                
                <div class="flex flex-col gap-4">
                    <span class="text-[10px] font-black text-white uppercase tracking-[0.2em] mb-2">Product</span>
                    <a href="#features" class="text-sm text-zinc-500 hover:text-white transition-colors">Features</a>
                    <a href="#pricing" class="text-sm text-zinc-500 hover:text-white transition-colors">Pricing</a>
                    <a href="#pipeline" class="text-sm text-zinc-500 hover:text-white transition-colors">How it Works</a>
                </div>

                <div class="flex flex-col gap-4">
                    <span class="text-[10px] font-black text-white uppercase tracking-[0.2em] mb-2">Company</span>
                    <a href="#" class="text-sm text-zinc-500 hover:text-white transition-colors">About us</a>
                    <a href="#" class="text-sm text-zinc-500 hover:text-white transition-colors">Contact</a>
                </div>

                <div class="flex flex-col gap-4">
                    <span class="text-[10px] font-black text-white uppercase tracking-[0.2em] mb-2">Legal</span>
                    <a href="#" class="text-sm text-zinc-500 hover:text-white transition-colors">Terms of Service</a>
                    <a href="#" class="text-sm text-zinc-500 hover:text-white transition-colors">Privacy Policy</a>
                </div>

                <div class="flex flex-col gap-4">
                    <span class="text-[10px] font-black text-white uppercase tracking-[0.2em] mb-2">Resources</span>
                    <a href="#faq" class="text-sm text-zinc-500 hover:text-white transition-colors">FAQs</a>
                    <a href="#" class="text-sm text-zinc-500 hover:text-white transition-colors">Guides</a>
                </div>
            </div>
            
            <div class="mt-24 pt-12 border-t border-white/5 flex items-center justify-between gap-6">
                <p class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.2em]">{{ $siteSettings['footer_text'] ?? '© ' . date('Y') . ' Yoursite. All rights reserved.' }}</p>
                <div class="flex gap-4">
                    <a href="#" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-zinc-500 hover:text-white hover:bg-white/10 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
