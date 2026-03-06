<x-app-layout>
    <div class="py-12 bg-white dark:bg-zinc-950 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Token Balance Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-500/10 rounded-full blur-2xl group-hover:bg-indigo-500/20 transition-all duration-500"></div>
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-indigo-500/10 rounded-xl">
                            <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Script Tokens Remaining</p>
                            <h3 class="text-3xl font-black text-gray-950 dark:text-white leading-none mt-1">{{ number_format(Auth::user()->total_credits - Auth::user()->used_credits) }}</h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all duration-500"></div>
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-emerald-500/10 rounded-xl">
                            <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">Image Assets Remaining</p>
                            <h3 class="text-3xl font-black text-gray-950 dark:text-white leading-none mt-1">{{ number_format(Auth::user()->total_image_tokens - Auth::user()->used_image_tokens) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs for Usage and Payments -->
            <div x-data="{ tab: 'usage' }" class="space-y-6">
                <div class="flex items-center space-x-4 bg-white dark:bg-zinc-900 p-1 rounded-xl border border-zinc-200 dark:border-zinc-800 w-fit">
                    <button @click="tab = 'usage'" :class="tab === 'usage' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-gray-400 hover:text-gray-900 dark:hover:text-white'" class="px-6 py-2 rounded-lg font-bold text-sm tracking-tight transition-all duration-300">
                        Usage Tracking
                    </button>
                    <button @click="tab = 'payments'" :class="tab === 'payments' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-gray-400 hover:text-gray-900 dark:hover:text-white'" class="px-6 py-2 rounded-lg font-bold text-sm tracking-tight transition-all duration-300">
                        Invoices & Billing
                    </button>
                    <a href="{{ route('topup.index') }}" class="px-6 py-2 text-indigo-600 dark:text-indigo-400 hover:opacity-80 font-bold text-sm tracking-tight transition-all duration-300 flex items-center space-x-2">
                        <span>Recharge Tokens</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </a>
                </div>

                <!-- Usage History Table -->
                <div x-show="tab === 'usage'" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm overflow-hidden transition-all duration-500">
                    <div class="px-6 py-5 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                        <h2 class="text-xl font-black text-gray-950 dark:text-white tracking-tight italic">Token Usage Stream</h2>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Live Audit Log</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-gray-500 dark:text-gray-400 text-[10px] uppercase tracking-widest font-black">
                                <tr>
                                    <th class="px-6 py-4">Timestamp</th>
                                    <th class="px-6 py-4">Operational Type</th>
                                    <th class="px-6 py-4">AI Infrastructure</th>
                                    <th class="px-6 py-4">Processing Details</th>
                                    <th class="px-6 py-4 text-right">Credit Impact</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                @forelse($usageLogs as $log)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-gray-500 dark:text-gray-400">
                                            {{ $log->created_at->format('M d, Y • H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-900 dark:text-white font-black tracking-tight capitalize">{{ str_replace('_', ' ', $log->job_type) }}</span>
                                            @if($log->image_count > 0)
                                                <span class="ml-2 px-2 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[9px] rounded font-black uppercase tracking-tighter">Image Gen</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 font-mono">
                                            {{ $log->model_used }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-gray-400">
                                            @if($log->type === 'image')
                                                FIXED_COST • TIER_CALC
                                            @else
                                                IN: {{ number_format($log->input_tokens) }} • OUT: {{ number_format($log->output_tokens) }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span class="text-sm font-black {{ $log->type === 'image' ? 'text-emerald-600 dark:text-emerald-400' : 'text-indigo-600 dark:text-indigo-400' }}">
                                                -{{ number_format($log->total_credits_deducted) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-20 text-center">
                                            <p class="text-gray-400 font-bold uppercase text-[10px] tracking-widest">System Silent</p>
                                            <p class="text-xs text-gray-500 mt-1 italic">No token transactions recorded on this baseline.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Payment History Table -->
                <div x-show="tab === 'payments'" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm overflow-hidden transition-all duration-500">
                    <div class="px-6 py-5 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                        <h2 class="text-xl font-black text-gray-950 dark:text-white tracking-tight italic">Financial Ledger</h2>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Transaction Records</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-gray-500 dark:text-gray-400 text-[10px] uppercase tracking-widest font-black">
                                <tr>
                                    <th class="px-6 py-4">Fulfillment Date</th>
                                    <th class="px-6 py-4">Audit Reference</th>
                                    <th class="px-6 py-4">Provision Type</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-right">Gross Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                @forelse($payments as $payment)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-gray-500 dark:text-gray-400">
                                            {{ $payment->created_at->format('M d, Y • H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-[10px] text-gray-400 font-mono">
                                            {{ $payment->reference }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-0.5 {{ $payment->type === 'subscription' ? 'bg-blue-500/10 text-blue-600' : 'bg-purple-500/10 text-purple-600' }} text-[9px] rounded font-black uppercase tracking-tighter">
                                                {{ $payment->type }}
                                            </span>
                                            <span class="ml-2 text-sm text-gray-950 dark:text-white font-black tracking-tight">
                                                {{ $payment->type === 'subscription' ? $payment->plan?->name : $payment->topupPackage?->name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($payment->status === 'success')
                                                <span class="flex items-center text-emerald-600 dark:text-emerald-400 text-xs font-black uppercase tracking-tighter">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Paid
                                                </span>
                                            @elseif($payment->status === 'pending')
                                                <span class="flex items-center text-amber-600 dark:text-amber-400 text-xs font-black uppercase tracking-tighter">
                                                    <svg class="w-4 h-4 mr-1.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                    </svg>
                                                    Pending
                                                </span>
                                            @else
                                                <span class="flex items-center text-rose-600 dark:text-rose-400 text-xs font-black uppercase tracking-tighter">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Halted
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right font-black text-gray-950 dark:text-white">
                                            ₦{{ number_format($payment->amount, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">
                                            No payment history found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </div>

        </div>
    </div>
</x-app-layout>
