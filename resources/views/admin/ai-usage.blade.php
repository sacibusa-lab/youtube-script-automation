<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white tracking-tight">AI Token Economy</h2>
    </x-slot>

    <div class="space-y-8">
        <!-- Overview Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-[32px] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1 px-1">Total Tokens</p>
                    <h4 class="text-[32px] font-black text-gray-900 dark:text-white leading-none">{{ number_format($grandTotalTokens) }}</h4>
                </div>
                <div class="absolute top-[-20px] right-[-20px] w-24 h-24 bg-teal-50 dark:bg-teal-900/10 rounded-full group-hover:scale-110 transition-transform"></div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-8 rounded-[32px] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1 px-1">Estimated Cost</p>
                    <h4 class="text-[32px] font-black text-teal-600 leading-none">${{ number_format($grandTotalCost, 4) }}</h4>
                </div>
                <div class="absolute top-[-20px] right-[-20px] w-24 h-24 bg-teal-50 dark:bg-teal-900/10 rounded-full group-hover:scale-110 transition-transform"></div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-8 rounded-[32px] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1 px-1">Total AI Calls</p>
                    <h4 class="text-[32px] font-black text-gray-900 dark:text-white leading-none">{{ number_format($stats->sum('total_calls')) }}</h4>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-8 rounded-[32px] border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1 px-1">Avg Cost / Call</p>
                    <h4 class="text-[32px] font-black text-gray-900 dark:text-white leading-none">
                        ${{ $stats->sum('total_calls') > 0 ? number_format($grandTotalCost / $stats->sum('total_calls'), 5) : '0.00' }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Provider Breakdown -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-[40px] border border-gray-100 dark:border-gray-700">
                <div class="p-10">
                    <h3 class="text-[20px] font-black text-gray-900 dark:text-white tracking-tight mb-6">Provider Breakdown</h3>
                    <div class="space-y-4">
                        @foreach($stats as $stat)
                            <div class="p-6 bg-gray-50 dark:bg-gray-700/30 rounded-[24px]">
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-sm font-black uppercase tracking-widest text-teal-600 dark:text-teal-400">{{ $stat->provider }}</span>
                                    <span class="text-lg font-black dark:text-white">${{ number_format($stat->total_cost, 4) }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Input Tokens</p>
                                        <p class="text-sm font-black dark:text-gray-200">{{ number_format($stat->total_input) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Output Tokens</p>
                                        <p class="text-sm font-black dark:text-gray-200">{{ number_format($stat->total_output) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Daily Trends -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-[40px] border border-gray-100 dark:border-gray-700">
                <div class="p-10">
                    <h3 class="text-[20px] font-black text-gray-900 dark:text-white tracking-tight mb-6">Daily Trends (Last 14 Days)</h3>
                    <div class="overflow-hidden">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-700">
                                    <th class="pb-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Date</th>
                                    <th class="pb-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tokens</th>
                                    <th class="pb-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Cost</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($dailyStats as $day)
                                    <tr>
                                        <td class="py-4 text-sm font-bold text-gray-600 dark:text-gray-300">{{ $day->date }}</td>
                                        <td class="py-4 text-sm font-black dark:text-gray-100">{{ number_format($day->total_tokens) }}</td>
                                        <td class="py-4 text-sm font-black text-teal-600 dark:text-teal-400 text-right">${{ number_format($day->daily_cost, 4) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-8 text-center text-gray-400 font-bold uppercase tracking-widest text-[11px]">No data available yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
