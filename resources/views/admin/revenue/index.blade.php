<x-app-layout>
    <div class="py-12 bg-white dark:bg-zinc-950 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h1 class="text-3xl font-black text-gray-950 dark:text-white tracking-tight uppercase italic">Revenue Terminal</h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm font-bold uppercase tracking-widest">Financial Performance & Stream Analysis</p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-400 text-xs font-bold rounded-full border border-emerald-500/20 uppercase tracking-widest">Live Engine</span>
                </div>
            </div>

            <!-- Key Financial Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- MRR -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-500/10 rounded-full blur-2xl group-hover:bg-indigo-500/20 transition-all duration-500"></div>
                    <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-1">Est. Monthly Recurring</p>
                    <h3 class="text-3xl font-black text-gray-950 dark:text-white italic">₦{{ number_format($mrr) }}</h3>
                    <p class="text-[9px] text-gray-400 mt-2 flex items-center font-bold">
                        <svg class="w-3 h-3 mr-1 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        ACTIVE SUBSCRIPTIONS
                    </p>
                </div>

                <!-- Monthly Sales -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all duration-500"></div>
                    <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-1">Current Month Sales</p>
                    <h3 class="text-3xl font-black text-gray-950 dark:text-white italic">₦{{ number_format($monthlyRevenue) }}</h3>
                    <p class="text-[9px] text-gray-400 mt-2 font-bold uppercase tracking-widest">
                        COLLECTED IN {{ now()->format('F') }}
                    </p>
                </div>

                <!-- Lifetime Revenue -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-all duration-500"></div>
                    <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-1">Cumulative Platform Sales</p>
                    <h3 class="text-3xl font-black text-gray-950 dark:text-white italic">₦{{ number_format($totalRevenue) }}</h3>
                    <p class="text-[9px] text-gray-400 mt-2 font-bold uppercase tracking-widest">
                        TOTAL NET REVENUE
                    </p>
                </div>

                <!-- Subscription vs Topup Ratio -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-500/10 rounded-full blur-2xl group-hover:bg-purple-500/20 transition-all duration-500"></div>
                    <p class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-1">Revenue Stream Mix</p>
                    <div class="flex flex-col gap-1 mt-1">
                        @foreach($revenueByType as $type)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400 text-[10px] uppercase font-black tracking-tighter">{{ $type->type }}:</span>
                                <span class="font-black text-gray-900 dark:text-white text-xs">₦{{ number_format($type->total / 1000) }}k</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Revenue Trend Chart Placeholder (Visual Component) -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[32px] p-10 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-12">
                    <h2 class="text-xl font-black text-gray-950 dark:text-white uppercase tracking-tight italic">6-Month Trend Analysis</h2>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-indigo-500 rounded-full"></span>
                        <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Gross Sales Velocity</span>
                    </div>
                </div>
                
                <div class="relative h-64 w-full flex items-end justify-between px-4 pb-2 border-l border-b border-gray-800">
                    {{-- Simple CSS-based Bar Chart --}}
                    @php 
                        $maxVal = $chartData->max('total') ?: 1; 
                    @endphp
                    @foreach($chartData as $data)
                        <div class="flex-grow flex flex-col items-center group relative mx-1">
                            <div class="w-full max-w-[40px] bg-indigo-500/20 border border-indigo-500/40 rounded-t-lg transition-all duration-500 group-hover:bg-indigo-500 group-hover:h-[{{ ($data->total / $maxVal) * 100 }}%]" 
                                 style="height: {{ ($data->total / $maxVal) * 90 }}%">
                                <div class="opacity-0 group-hover:opacity-100 absolute -top-10 left-1/2 -translate-x-1/2 bg-gray-800 border border-gray-700 px-2 py-1 rounded text-[10px] text-white whitespace-nowrap z-20 transition-opacity">
                                    ₦{{ number_format($data->total) }}
                                </div>
                            </div>
                            <span class="text-[10px] text-gray-500 mt-2 font-bold uppercase tracking-tighter">{{ Carbon\Carbon::parse($data->month)->format('M') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Transactions Table -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[32px] shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                    <h2 class="text-xl font-black text-gray-950 dark:text-white uppercase tracking-tight italic">Transaction Audit Stream</h2>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Live Ledger</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-gray-500 dark:text-gray-400 text-[10px] uppercase tracking-widest font-black">
                            <tr>
                                <th class="px-8 py-4">Transaction Date</th>
                                <th class="px-8 py-4">Corporate Customer</th>
                                <th class="px-8 py-4">Allocation Type</th>
                                <th class="px-8 py-4">Financial Reference</th>
                                <th class="px-8 py-4 text-right">Gross Intake</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @forelse($recentPayments as $payment)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors duration-200">
                                    <td class="px-8 py-4 whitespace-nowrap text-xs font-bold text-gray-500 dark:text-gray-400">
                                        {{ $payment->created_at->format('M d, Y • H:i') }}
                                    </td>
                                    <td class="px-8 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-gray-950 dark:text-white tracking-tight leading-none mb-1">{{ $payment->user?->name ?? 'Deleted User' }}</span>
                                            <span class="text-[10px] text-gray-500 font-bold uppercase tracking-tighter">{{ $payment->user?->email }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="px-2 py-0.5 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-[9px] rounded font-black uppercase tracking-widest w-fit">
                                                {{ $payment->type }}
                                            </span>
                                            <span class="text-[10px] text-gray-400 font-bold mt-1 tracking-tight">
                                                {{ $payment->type === 'subscription' ? $payment->plan?->name : $payment->topupPackage?->name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-4 whitespace-nowrap text-[10px] text-gray-400 font-mono">
                                        {{ $payment->reference }}
                                    </td>
                                    <td class="px-8 py-4 whitespace-nowrap text-right">
                                        <span class="text-sm font-black text-emerald-600 dark:text-emerald-400 italic">
                                            +₦{{ number_format($payment->amount, 2) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">
                                        No transactions recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-8 py-6 border-t border-zinc-100 dark:border-zinc-800">
                    {{ $recentPayments->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
