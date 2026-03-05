<x-app-layout>
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-2">My Analytics</h1>
                <p class="text-gray-500 font-medium">Real-time usage and credit tracking for your Antigravity stories.</p>
            </div>
            
            <div class="flex items-center gap-4 bg-white p-2 rounded-2xl border border-gray-100 shadow-sm">
                <div class="px-6 py-2">
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Current Balance</span>
                    <span class="text-xl font-black text-teal-600">{{ number_format($creditBalance, 2) }}</span>
                </div>
                <div class="h-10 w-px bg-gray-100"></div>
                <button class="bg-teal-600 text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-teal-700 transition shadow-lg shadow-teal-100">
                    Buy Credits
                </button>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-sm hover:shadow-md transition group">
                <div class="w-12 h-12 bg-teal-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-teal-600 transition-colors">
                    <svg class="w-6 h-6 text-teal-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h3 class="text-gray-400 font-bold text-xs uppercase tracking-widest mb-2">Credits Consumed</h3>
                <p class="text-3xl font-black text-gray-900">{{ number_format($stats->total_credits_spent ?? 0, 2) }}</p>
                <div class="mt-4 flex items-center text-xs font-bold text-teal-600">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path></svg>
                    <span>Lifetime usage</span>
                </div>
            </div>

            <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-sm hover:shadow-md transition group">
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 transition-colors">
                    <svg class="w-6 h-6 text-blue-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                </div>
                <h3 class="text-gray-400 font-bold text-xs uppercase tracking-widest mb-2">Input Tokens</h3>
                <p class="text-3xl font-black text-gray-900">{{ number_format($stats->total_input ?? 0) }}</p>
                <div class="mt-4 flex items-center text-xs font-bold text-blue-600">
                    <span class="opacity-70 group-hover:opacity-100">Across {{ $stats->total_calls ?? 0 }} AI calls</span>
                </div>
            </div>

            <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-sm hover:shadow-md transition group">
                <div class="w-12 h-12 bg-purple-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-purple-600 transition-colors">
                    <svg class="w-6 h-6 text-purple-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                </div>
                <h3 class="text-gray-400 font-bold text-xs uppercase tracking-widest mb-2">Output Tokens</h3>
                <p class="text-3xl font-black text-gray-900">{{ number_format($stats->total_output ?? 0) }}</p>
                <div class="mt-4 flex items-center text-xs font-bold text-purple-600">
                    <span class="opacity-70 group-hover:opacity-100">AI Narrative Generation</span>
                </div>
            </div>

            <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-sm hover:shadow-md transition group">
                <div class="w-12 h-12 bg-orange-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-orange-600 transition-colors">
                    <svg class="w-6 h-6 text-orange-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                </div>
                <h3 class="text-gray-400 font-bold text-xs uppercase tracking-widest mb-2">Stories Created</h3>
                <p class="text-3xl font-black text-gray-900">{{ $storyStats->count() }}</p>
                <div class="mt-4 flex items-center text-xs font-bold text-orange-600">
                    <span class="opacity-70 group-hover:opacity-100">From concept to script</span>
                </div>
            </div>
        </div>

        <!-- Story Usage Section -->
        <div class="bg-white border border-gray-100 rounded-[40px] shadow-sm overflow-hidden">
            <div class="px-10 py-8 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">Story-Specific Consumption</h2>
                <div class="flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-widest">
                    <span>Sorting by Most Recent</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white">
                            <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Story Concept</th>
                            <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Job Type</th>
                            <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Tokens</th>
                            <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Credits Spent</th>
                            <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Saved Titles</th>
                            <th class="px-10 py-5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($storyStats as $story)
                        <tr class="hover:bg-teal-50/10 transition group">
                            <td class="px-10 py-6">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-900 group-hover:text-teal-600 transition-colors">{{ $story->selected_title ?? $story->topic }}</span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">{{ $story->niche }}</span>
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                <span class="bg-gray-100 text-gray-500 text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-tighter">
                                    {{ $story->status }}
                                </span>
                            </td>
                            <td class="px-10 py-6 text-center">
                                <span class="text-sm font-bold text-gray-700">{{ number_format($story->tokens_used) }}</span>
                            </td>
                            <td class="px-10 py-6 text-center">
                                <div class="inline-flex items-center bg-teal-50 text-teal-700 text-xs font-black px-4 py-1.5 rounded-xl border border-teal-100">
                                    {{ number_format($story->credits_used, 2) }}
                                </div>
                            </td>
                            <td class="px-10 py-6 text-center">
                                <span class="text-xs font-bold text-gray-500">{{ $story->saved_count }} Favs</span>
                            </td>
                            <td class="px-10 py-6 text-right">
                                <a href="{{ route('projects.show', $story) }}" class="text-teal-600 hover:text-teal-700 font-bold text-xs uppercase tracking-widest flex items-center justify-end gap-1">
                                    Details
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-10 py-20 text-center">
                                <div class="flex flex-col items-center opacity-30">
                                    <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <p class="font-black text-sm uppercase tracking-widest">No usage data found.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
