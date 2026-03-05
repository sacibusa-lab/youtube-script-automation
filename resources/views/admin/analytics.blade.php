<x-app-layout>
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        
        <div class="mb-12">
            <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-2">System Analytics</h1>
            <p class="text-gray-500 font-medium">Monitoring AI infrastructure performance, costs, and user engagement.</p>
        </div>

        <!-- Global Totals -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <div class="bg-gray-900 rounded-[32px] p-8 text-white shadow-2xl overflow-hidden relative group">
                <div class="absolute top-0 right-0 p-6 opacity-10 group-hover:rotate-12 transition-transform">
                    <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20"><path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.69a2.306 2.306 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.69c.221.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.306 2.306 0 01-.567.267z"></path><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.184c-.622.117-1.195.342-1.676.662C6.602 13.234 6 14.009 6 15c0 .99.602 1.765 1.324 2.246A4.535 4.535 0 009 17.908V18a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 16.766 14 15.991 14 15c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 12.092V10.908c.622-.117 1.195-.342 1.676-.662C13.398 9.766 14 8.991 14 8c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 5.092V5z" clip-rule="evenodd"></path></svg>
                </div>
                <h3 class="text-teal-400 font-bold text-xs uppercase tracking-widest mb-2">Total AI Cost</h3>
                <p class="text-3xl font-black">${{ number_format($totals->total_cost ?? 0, 4) }}</p>
                <p class="mt-4 text-[10px] uppercase font-bold opacity-60">Estimated Platform Spend</p>
            </div>

            <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-sm transition group">
                <h3 class="text-gray-400 font-bold text-xs uppercase tracking-widest mb-2">Credits Consumed</h3>
                <p class="text-3xl font-black text-gray-900">{{ number_format($totals->total_credits ?? 0, 2) }}</p>
                <p class="mt-4 text-[10px] uppercase font-bold text-teal-600">User Credit Velocity</p>
            </div>

            <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-sm transition group">
                <h3 class="text-gray-400 font-bold text-xs uppercase tracking-widest mb-2">Total Tokens</h3>
                <p class="text-3xl font-black text-gray-900">{{ number_format($totals->total_tokens ?? 0) }}</p>
                <p class="mt-4 text-[10px] uppercase font-bold text-blue-600">System IO Throughput</p>
            </div>

            <div class="bg-white rounded-[32px] p-8 border border-gray-100 shadow-sm transition group">
                <h3 class="text-gray-400 font-bold text-xs uppercase tracking-widest mb-2">Active AI Users</h3>
                <p class="text-3xl font-black text-gray-900">{{ $totals->active_users ?? 0 }}</p>
                <p class="mt-4 text-[10px] uppercase font-bold text-purple-600">Unique Users Generating</p>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-8">
            
            <!-- Provider Breakdown -->
            <div class="col-span-12 lg:col-span-5">
                <div class="bg-white border border-gray-100 rounded-[40px] shadow-sm overflow-hidden h-full">
                    <div class="px-10 py-8 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight">Provider Performance</h2>
                    </div>
                    <div class="p-8 space-y-6">
                        @foreach($providerStats as $ps)
                        <div class="flex items-center justify-between p-4 rounded-3xl bg-gray-50 border border-gray-100">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-white flex items-center justify-center font-black text-xs shadow-sm capitalize">
                                    {{ substr($ps->provider, 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-gray-900 capitalize">{{ $ps->provider }}</span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $ps->calls }} API Calls</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="block text-sm font-black text-gray-900">${{ number_format($ps->cost, 4) }}</span>
                                <span class="block text-[10px] font-bold text-teal-600 uppercase tracking-widest">{{ number_format($ps->credits, 1) }} Credits</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- User Leaderboard -->
            <div class="col-span-12 lg:col-span-7">
                <div class="bg-white border border-gray-100 rounded-[40px] shadow-sm overflow-hidden">
                    <div class="px-10 py-8 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight">Usage Leaderboard</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr>
                                    <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">User</th>
                                    <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Stories</th>
                                    <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Credits Consumed</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($userLeaderboard as $user)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-10 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-900">{{ $user->name }}</span>
                                            <span class="text-[10px] text-gray-400">{{ $user->email }}</span>
                                        </div>
                                    </td>
                                    <td class="px-10 py-6 text-center">
                                        <span class="text-xs font-black text-gray-700 bg-gray-100 px-3 py-1 rounded-full">{{ $user->stories_created }}</span>
                                    </td>
                                    <td class="px-10 py-6 text-right font-black text-teal-600">
                                        {{ number_format($user->total_spent, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
