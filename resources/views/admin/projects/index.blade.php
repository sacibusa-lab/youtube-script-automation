<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin: Global Story Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-[40px] border border-gray-100 dark:border-gray-700">
                <div class="p-10">
                    <div class="flex items-center justify-between mb-10">
                        <div>
                            <h3 class="text-[28px] font-black text-gray-900 dark:text-white tracking-tight italic uppercase">Global Activity</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Every story generated across the platform.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @foreach($projects as $project)
                            <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-[32px] border border-transparent flex items-center justify-between group hover:bg-white dark:hover:bg-gray-800 transition-all shadow-sm hover:shadow-md">
                                <div class="flex items-center gap-6">
                                    <!-- User Badge -->
                                    <div class="w-12 h-12 rounded-2xl bg-white dark:bg-gray-800 flex items-center justify-center font-black text-teal-600 shadow-sm border border-gray-100 dark:border-gray-700">
                                        {{ strtoupper(substr($project->user->name, 0, 1)) }}
                                    </div>
                                    
                                    <div>
                                        <div class="flex items-center gap-3">
                                            <h4 class="font-black text-gray-900 dark:text-gray-100 tracking-tight">{{ $project->selected_title ?? 'Generation Started' }}</h4>
                                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-2 py-0.5 bg-white dark:bg-gray-800 rounded-lg">{{ $project->niche }}</span>
                                        </div>
                                        <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wide mt-1 underline decoration-teal-500/30">
                                            By {{ $project->user->name }} • {{ $project->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-6">
                                    <div class="text-right">
                                        <div class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest 
                                            {{ $project->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                            {{ str_replace('_', ' ', $project->status) }}
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-200 group-hover:text-teal-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-10">
                        {{ $projects->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
