<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pricing Plans | Axelit StoryBee</title>

    <!-- Fonts: Outfit & Public Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --brand-primary: #14b8a6;
            --brand-secondary: #0f766e;
            --dark-bg: #09090b;
            --dark-card: #18181b;
        }

        body {
            font-family: 'Public Sans', sans-serif;
            background-color: var(--dark-bg);
            color: #f4f4f5;
        }

        .font-outfit { font-family: 'Outfit', sans-serif; }

        .glass {
            background: rgba(24, 24, 27, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .text-gradient {
            background: linear-gradient(135deg, #2dd4bf 0%, #0d9488 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-primary {
            background: linear-gradient(135deg, #14b8a6 0%, #0f766e 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(20, 184, 166, 0.3);
        }

        .hero-glow {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, rgba(20, 184, 166, 0.1) 0%, transparent 70%);
            z-index: -1;
            pointer-events: none;
        }
    </style>
</head>
<body class="antialiased overflow-x-hidden">
    <div class="hero-glow"></div>

    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 glass border-b border-white/5">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-teal-400 to-teal-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <span class="text-xl font-outfit font-black tracking-tighter uppercase whitespace-nowrap">Axelit <span class="text-teal-500">StoryBee</span></span>
                </a>
            </div>

            <div class="hidden md:flex items-center gap-8">
                <a href="{{ route('home') }}#features" class="text-sm font-semibold text-zinc-400 hover:text-white transition-colors">Features</a>
                <a href="{{ route('pricing') }}" class="text-sm font-semibold text-white transition-colors">Pricing</a>
                <a href="{{ route('login') }}" class="text-sm font-semibold text-zinc-400 hover:text-white transition-colors">Login</a>
                <a href="{{ route('register') }}" class="btn-primary px-6 py-2.5 rounded-full text-sm font-bold shadow-lg shadow-teal-500/20">Get Started</a>
            </div>
        </div>
    </nav>

    <!-- Pricing Section -->
    <section class="pt-40 pb-32 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20 uppercase tracking-[0.3em] font-black text-xs text-teal-500">
                Transparent Pricing
                <h1 class="text-5xl md:text-6xl font-outfit font-black text-white mt-4 tracking-tight">Scale Your Storytelling.</h1>
                <p class="text-zinc-400 mt-6 text-lg max-w-2xl mx-auto">Choose the plan that fits your production speed. From hobbyists to full-scale media agencies.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($plans as $plan)
                    <div class="glass p-8 rounded-[2.5rem] flex flex-col hover:border-teal-500/40 transition-all group relative overflow-hidden">
                        @if($plan->name == 'Creator' || $plan->name == 'Standard')
                            <div class="absolute top-0 right-0 bg-teal-500 text-white text-[10px] font-black px-4 py-1.5 rounded-bl-2xl uppercase tracking-widest">
                                Popular
                            </div>
                        @endif

                        <div class="mb-8">
                            <h3 class="text-xl font-outfit font-bold text-white mb-2">{{ $plan->name }}</h3>
                            <div class="flex items-baseline gap-1">
                                <span class="text-4xl font-outfit font-black text-white">&#8358;{{ number_format($plan->price) }}</span>
                                <span class="text-zinc-500 font-bold text-sm">/mo</span>
                            </div>
                        </div>

                        <div class="space-y-4 mb-10 flex-1">
                            <div class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-teal-500/10 flex items-center justify-center shrink-0">
                                    <svg class="w-3 h-3 text-teal-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                </div>
                                <span class="text-sm font-semibold text-zinc-300">{{ number_format($plan->monthly_credits) }} Script Credits</span>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-teal-500/10 flex items-center justify-center shrink-0">
                                    <svg class="w-3 h-3 text-teal-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                </div>
                                <span class="text-sm font-semibold text-zinc-300">
                                    @if($plan->monthly_image_tokens > 0)
                                        {{ number_format($plan->monthly_image_tokens) }} Free Images / Month
                                    @else
                                        Pay-per-image billing
                                    @endif
                                </span>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-teal-500/10 flex items-center justify-center shrink-0">
                                    <svg class="w-3 h-3 text-teal-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                </div>
                                <span class="text-sm font-semibold text-zinc-300">{{ $plan->max_images_per_script == 999 ? 'Unlimited' : $plan->max_images_per_script }} Images per script</span>
                            </div>

                            @if($plan->rollover_percent > 0)
                            <div class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-teal-500/10 flex items-center justify-center shrink-0">
                                    <svg class="w-3 h-3 text-teal-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                </div>
                                <span class="text-sm font-semibold text-zinc-300">{{ $plan->rollover_percent }}% Credit Rollover</span>
                            </div>
                            @endif

                             @if($plan->concurrent_jobs > 1)
                            <div class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full bg-teal-500/10 flex items-center justify-center shrink-0">
                                    <svg class="w-3 h-3 text-teal-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                </div>
                                <span class="text-sm font-semibold text-zinc-300">{{ $plan->concurrent_jobs }} Concurrent Missions</span>
                            </div>
                            @endif
                        </div>

                        <a href="{{ route('register', ['plan_id' => $plan->id]) }}" 
                           class="w-full py-4 rounded-2xl text-center font-bold text-sm transition-all {{ $plan->name == 'Creator' ? 'btn-primary text-white shadow-lg shadow-teal-500/20' : 'bg-white/5 border border-white/10 text-white hover:bg-white/10' }}">
                            Select {{ $plan->name }}
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="mt-20 glass p-8 rounded-[2.5rem] border-dashed border-white/10 text-center">
                <p class="text-zinc-500 text-sm font-medium italic">Running a larger operation? <a href="mailto:support@axelit.media" class="text-teal-500 underline">Contact us for customized enterprise solutions.</a></p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-20 px-6 border-t border-white/5">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="flex items-center gap-2">
                <span class="text-xl font-outfit font-black tracking-tighter uppercase whitespace-nowrap">Axelit <span class="text-teal-500">StoryBee</span></span>
            </div>
            <p class="text-sm text-zinc-500 font-medium">&copy; {{ date('Y') }} Axelit Media. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
