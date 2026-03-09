<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $siteSettings['platform_name'] ?? config('app.name', 'StoryBee') }}</title>

    @if(isset($siteSettings['favicon']))
        <link rel="icon" type="image/png" href="{{ Storage::url($siteSettings['favicon']) }}">
    @else
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>⚡</text></svg>">
    @endif

    <!-- Fonts: Public Sans & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@100;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
    
    <script>
        // On page load or when changing themes, best to add inline in `head` to avoid FOUC
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        :root {
            --primary-start: #0f766e;
            --primary-end: #14b8a6;
            --sidebar-width: 260px;
            --header-height: 80px;
        }
        
        body { 
            font-family: 'Public Sans', sans-serif; 
            overflow-x: hidden; 
        }

        .font-outfit { font-family: 'Outfit', sans-serif; }

        [x-cloak] { display: none !important; }
        
        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 50;
            border-right-width: 2px; 
        }

        .sidebar-link {
            border-radius: 10px;
            padding: 12px 16px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 4px;
        }
        
        /* Light Mode Sidebar */
        .sidebar-link { color: #64748b; }
        .sidebar-link:hover { color: #0d9488; background-color: #f0fdfa; }
        
        /* Dark Mode Sidebar */
        .dark .sidebar-link { color: #94a3b8; }
        .dark .sidebar-link:hover { color: #2dd4bf; background-color: rgba(13, 148, 136, 0.1); }

        .sidebar-link.active {
            background: linear-gradient(135deg, var(--primary-start) 0%, var(--primary-end) 100%);
            color: white !important;
            box-shadow: 0 4px 6px -1px rgba(13, 148, 136, 0.2);
        }
        
        /* Main Layout */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Marquee Animation */
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-33.3333%); }
        }
        .animate-marquee {
            animation: marquee 30s linear infinite;
            width: max-content;
        }
        .animate-marquee:hover {
            animation-play-state: paused;
        }

        .top-header {
            height: var(--header-height);
            position: sticky;
            top: 0;
            z-index: 40;
            backdrop-filter: blur(8px);
            border-bottom-width: 2px;
            display: flex;
            align-items: center;
            padding: 0 32px;
        }
        
        .content-area {
            padding: 32px;
            max-width: 1600px;
            margin: 0 auto;
            width: 100%;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s ease; }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
        }
    </style>
</head>
<body class="antialiased bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 transition-colors duration-300">

    <!-- Sidebar -->
    <aside class="sidebar flex flex-col p-5 bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 transition-colors duration-300 shadow-xl dark:shadow-none">
        
        <!-- Dashboard Button -->
        <div class="mb-6">
            <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" 
               class="sidebar-link {{ (request()->routeIs('dashboard') || request()->routeIs('admin.dashboard')) ? 'active' : '' }} justify-between !py-3.5 !px-5 !rounded-xl border border-transparent {{ !(request()->routeIs('dashboard') || request()->routeIs('admin.dashboard')) ? 'hover:border-teal-100 bg-white dark:bg-gray-700 dark:hover:border-gray-600 shadow-sm border-gray-200' : '' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span class="text-base font-bold dark:text-gray-200">Main Command</span>
                </div>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 space-y-1 overflow-y-auto px-1">
            <p class="px-4 text-[10px] font-black text-rose-500/80 dark:text-rose-400/80 uppercase tracking-[0.2em] mb-3 mt-4">Creative Suite</p>

            <a href="{{ route('studio.index') }}" class="sidebar-link {{ request()->routeIs('studio.*') ? 'active' : '' }} gap-3.5 group">
                <svg class="w-5 h-5 opacity-70 group-hover:text-rose-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                <div class="flex items-center justify-between flex-1">
                    <span>Production Studio</span>
                    <span class="text-[8px] font-black bg-rose-500/10 text-rose-500 px-1.5 py-0.5 rounded leading-none uppercase tracking-tighter">PRO</span>
                </div>
            </a>

            <a href="{{ route('projects.create') }}" class="sidebar-link {{ request()->routeIs('projects.create') ? 'active' : '' }} gap-3.5">
                <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>New Story</span>
            </a>

            <a href="{{ route('projects.index') }}" class="sidebar-link {{ request()->routeIs('projects.index') ? 'active' : '' }} gap-3.5">
                <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <span>My Library</span>
            </a>

            <a href="{{ route('projects.bookmarks') }}" class="sidebar-link {{ request()->routeIs('projects.bookmarks') ? 'active' : '' }} gap-3.5">
                <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 4a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 20V4z"></path></svg>
                <span>Saved Concepts</span>
            </a>

            <a href="{{ route('analytics') }}" class="sidebar-link {{ request()->routeIs('analytics') ? 'active' : '' }} gap-3.5">
                <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <span>My Analytics</span>
            </a>

            @if(Auth::user()->isAdmin())
                <p class="px-4 text-[10px] font-black text-red-500/60 dark:text-red-400/60 uppercase tracking-[0.2em] mb-3 mt-6">Administrative Panel</p>

                <a href="{{ route('admin.revenue.index') }}" class="sidebar-link {{ request()->routeIs('admin.revenue.*') ? 'active' : '' }} gap-3.5">
                    <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Revenue & Sales</span>
                </a>

                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }} gap-3.5">
                    <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span>User Management</span>
                </a>

                <a href="{{ route('admin.plans.index') }}" class="sidebar-link {{ request()->routeIs('admin.plans.*') ? 'active' : '' }} gap-3.5">
                    <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span>Subscription Plans</span>
                </a>

                <a href="{{ route('admin.topup-packages.index') }}" class="sidebar-link {{ request()->routeIs('admin.topup-packages.*') ? 'active' : '' }} gap-3.5">
                    <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    <span>Top-up Packages</span>
                </a>

                <a href="{{ route('admin.api-gateway.index') }}" class="sidebar-link {{ request()->routeIs('admin.api-gateway.*') ? 'active' : '' }} gap-3.5">
                    <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                    <span>API Infrastructure</span>
                </a>

                <a href="{{ route('admin.projects.index') }}" class="sidebar-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }} gap-3.5">
                    <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Global Project Logs</span>
                </a>

                <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }} gap-3.5">
                    <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>System Settings</span>
                </a>
            @endif

        </nav>

        <!-- Profile -->
        <div class="mt-auto pt-4 border-t border-gray-300 dark:border-gray-700">
             <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800 transition cursor-pointer border border-transparent hover:border-zinc-300 dark:hover:border-zinc-700">
                <div class="w-10 h-10 rounded-full bg-teal-50 dark:bg-zinc-800 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold border border-teal-100 dark:border-zinc-700 shadow-sm">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-zinc-800 dark:text-zinc-200 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-zinc-400 font-bold uppercase">View Profile</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-gray-400 hover:text-red-500 transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg></button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper flex flex-col bg-zinc-50 dark:bg-zinc-950 transition-colors duration-300">
        
        <x-exhausted-credits-banner />

        <!-- Header -->
        <header class="top-header justify-between bg-white/90 dark:bg-zinc-900/90 border-zinc-200 dark:border-zinc-800 transition-colors duration-300 shadow-sm dark:shadow-none">
            <div class="flex items-center gap-4">
                 <button class="lg:hidden text-gray-500 hover:text-teal-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white tracking-tight">
                    @if (request()->routeIs('dashboard')) Dashboard 
                    @elseif (request()->routeIs('projects.create')) New Story
                    @elseif (request()->routeIs('projects.show')) Story Details
                    @elseif (request()->routeIs('analytics*')) Analytics
                    @else Overview @endif
                </h2>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- Token Usage UI -->
                <div x-data="{ 
                        scriptCredits: {{ Auth::user()->scriptCreditsBalance() ?? 0 }}, 
                        imageTokens: {{ Auth::user()->imageTokensBalance() ?? 0 }} 
                     }" 
                     @tokens-updated.window="scriptCredits = $event.detail.script_credits || 0; imageTokens = $event.detail.image_tokens || 0"
                     class="hidden md:flex items-center gap-3 bg-zinc-100 dark:bg-zinc-800/50 px-3 py-1.5 rounded-xl border border-zinc-200 dark:border-zinc-700/50 transition-colors">
                    
                    <div class="flex items-center gap-1.5" title="Script Credits">
                        <svg class="w-4 h-4 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="text-xs font-bold font-outfit text-zinc-700 dark:text-zinc-300" x-text="scriptCredits">{{ Auth::user()->scriptCreditsBalance() ?? 0 }}</span>
                    </div>

                    <div class="w-px h-4 bg-zinc-300 dark:bg-zinc-700"></div>

                    <div class="flex items-center gap-1.5" title="Image Tokens">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="text-xs font-bold font-outfit text-zinc-700 dark:text-zinc-300" x-text="imageTokens">{{ Auth::user()->imageTokensBalance() ?? 0 }}</span>
                    </div>
                </div>

                <!-- Theme Toggle -->
                <button type="button" 
                        onclick="
                            if (document.documentElement.classList.contains('dark')) {
                                document.documentElement.classList.remove('dark');
                                localStorage.theme = 'light';
                            } else {
                                document.documentElement.classList.add('dark');
                                localStorage.theme = 'dark';
                            }
                        "
                        class="w-10 h-10 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-500 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:text-white transition"
                        title="Toggle Dark Mode">
                    <!-- Sun Icon -->
                    <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <!-- Moon Icon -->
                    <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>

                <button class="w-10 h-10 rounded-full bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-zinc-400 hover:text-teal-600 hover:border-teal-200 transition shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </button>
            </div>
        </header>

        <!-- Content -->
        <main class="content-area">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="max-w-screen-2xl mx-auto mb-6 px-4">
                    <div class="bg-teal-500/10 border border-teal-500/20 p-4 rounded-2xl flex items-center gap-3">
                        <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <p class="text-sm font-bold text-teal-600 dark:text-teal-400">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('warning'))
                <div class="max-w-screen-2xl mx-auto mb-6 px-4">
                    <div class="bg-amber-500/10 border border-amber-500/20 p-4 rounded-2xl flex items-center gap-3">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <p class="text-sm font-bold text-amber-600 dark:text-amber-400">{{ session('warning') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="max-w-screen-2xl mx-auto mb-6 px-4">
                    <div class="bg-red-500/10 border border-red-500/20 p-4 rounded-2xl flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm font-bold text-red-600 dark:text-red-400">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if (isset($header))
            @endif
            
            {{ $slot }}
        </main>
        
    </div>


    @stack('scripts')
</body>
</html>
