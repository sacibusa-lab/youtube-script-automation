<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin: Command Center') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Platform Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Total Users -->
                <div class="bg-white dark:bg-gray-800 p-8 rounded-[32px] border border-gray-100 dark:border-gray-700 shadow-sm group">
                    <p class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1 px-1">Total Users</p>
                    <h4 class="text-[32px] font-black text-gray-900 dark:text-white leading-none">{{ $totalUsers }}</h4>
                </div>

                <!-- Total Stories -->
                <div class="bg-white dark:bg-gray-800 p-8 rounded-[32px] border border-gray-100 dark:border-gray-700 shadow-sm group">
                    <p class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1 px-1">Global Stories</p>
                    <h4 class="text-[32px] font-black text-gray-900 dark:text-white leading-none">{{ $totalStories }}</h4>
                </div>

                <!-- Active AI Keys -->
                <div class="bg-white dark:bg-gray-800 p-8 rounded-[32px] border border-gray-100 dark:border-gray-700 shadow-sm group">
                    <p class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1 px-1">Active AI Keys</p>
                    <h4 class="text-[32px] font-black {{ $activeKeysCount > 0 ? 'text-green-600' : 'text-red-600' }} leading-none">
                        {{ $activeKeysCount }}<span class="text-sm font-bold text-gray-400 ml-1">/ {{ $totalSystemKeys }}</span>
                    </h4>
                </div>

                    <a href="{{ route('admin.revenue.index') }}" class="text-white font-black text-[13px] uppercase tracking-widest flex items-center justify-between hover:translate-x-1 transition-transform">
                        Revenue & Growth
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </a>
                    
                    <a href="{{ route('admin.api-gateway.index') }}" class="text-white font-black text-[13px] uppercase tracking-widest flex items-center justify-between hover:translate-x-1 transition-transform">
                        Manage APIs
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                    
                    <a href="{{ route('admin.plans.index') }}" class="text-white font-black text-[13px] uppercase tracking-widest flex items-center justify-between hover:translate-x-1 transition-transform">
                        Manage Plans
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </a>
                    
                    <a href="{{ route('admin.settings.index') }}" class="text-red-100 font-black text-[13px] uppercase tracking-widest flex items-center justify-between hover:translate-x-1 hover:text-white transition-all">
                        Paystack Settings
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Global Stories -->
                <div class="bg-white dark:bg-gray-800 rounded-[40px] border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
                    <div class="p-10">
                        <h3 class="text-[20px] font-black text-gray-900 dark:text-white mb-6 tracking-tight italic">Latest Global Stories</h3>
                        <div class="space-y-4">
                            @foreach($recentProjects as $project)
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/30 rounded-[24px]">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center font-bold text-teal-600 text-xs">
                                            {{ substr($project->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-gray-800 dark:text-gray-200 truncate max-w-[150px]">{{ $project->selected_title ?? 'Untitled' }}</p>
                                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">{{ $project->user->name }} • {{ $project->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <div class="px-3 py-1 bg-white dark:bg-gray-800 rounded-lg text-[9px] font-black text-gray-500 uppercase tracking-widest border border-gray-100 dark:border-gray-600">
                                        {{ $project->status }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="bg-white dark:bg-gray-800 rounded-[40px] border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
                    <div class="p-10">
                        <h3 class="text-[20px] font-black text-gray-900 dark:text-white mb-6 tracking-tight italic">Newest Members</h3>
                        <div class="space-y-4">
                            @foreach($recentUsers as $user)
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/30 rounded-[24px]">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center font-black text-red-600 text-xs">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-gray-800 dark:text-gray-200">{{ $user->name }}</p>
                                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wide">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                    @if($user->isAdmin())
                                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-[9px] font-black uppercase tracking-widest">Admin</span>
                                    @else
                                        <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-lg text-[9px] font-black uppercase tracking-widest">User</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-8">
                            <a href="{{ route('admin.users.index') }}" class="w-full inline-flex items-center justify-center bg-gray-900 dark:bg-gray-700 text-white py-4 rounded-[20px] font-black text-[13px] uppercase tracking-widest hover:bg-black transition shadow-xl">
                                Manage All Users
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
