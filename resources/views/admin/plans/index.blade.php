<x-app-layout>
    <div class="py-12 bg-zinc-50 dark:bg-zinc-950 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-black text-gray-950 dark:text-white tracking-tight">Subscription Plans</h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Configure service tiers, credit limits, and premium features.</p>
                </div>
                <a href="{{ route('admin.plans.create') }}" class="px-6 py-3 bg-teal-600 hover:bg-teal-500 text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-lg shadow-teal-600/20 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    New Tier
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($plans as $plan)
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[32px] overflow-hidden shadow-sm hover:shadow-xl dark:hover:shadow-none transition-all flex flex-col group">
                        <div class="p-8 border-b border-zinc-100 dark:border-zinc-800">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex flex-col gap-2">
                                    <span class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 rounded-lg text-[9px] font-black uppercase tracking-widest border border-zinc-200 dark:border-zinc-700">
                                        ID: #{{ $plan->id }}
                                    </span>
                                    @if($plan->is_active)
                                        <span class="px-3 py-1 bg-teal-500/10 text-teal-600 dark:text-teal-400 rounded-lg text-[9px] font-black uppercase tracking-widest border border-teal-500/20">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-rose-500/10 text-rose-600 dark:text-rose-400 rounded-lg text-[9px] font-black uppercase tracking-widest border border-rose-500/20">
                                            Inactive
                                        </span>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Monthly Cost</p>
                                    <p class="text-2xl font-black text-gray-900 dark:text-white leading-none">₦{{ number_format($plan->price) }}</p>
                                </div>
                            </div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-1">{{ $plan->name }}</h3>
                        </div>

                        <div class="p-8 bg-zinc-50/50 dark:bg-zinc-900/50 flex-1 space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Script Credits</span>
                                <span class="text-sm font-black text-gray-900 dark:text-zinc-200">{{ number_format($plan->monthly_credits / 1000) }}k Tokens</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Image Cost</span>
                                <span class="text-sm font-black text-teal-600 dark:text-teal-400">{{ number_format($plan->image_credit_cost) }} units</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Max Request</span>
                                <span class="text-sm font-black text-gray-900 dark:text-zinc-200">{{ number_format($plan->max_tokens_per_request) }} tokens</span>
                            </div>
                            
                            <div class="pt-4 flex flex-wrap gap-2">
                                @if($plan->api_access) <span class="px-2 py-0.5 bg-indigo-500/10 text-indigo-500 rounded text-[8px] font-black uppercase">API Access</span> @endif
                                @if($plan->bulk_upload) <span class="px-2 py-0.5 bg-rose-500/10 text-rose-500 rounded text-[8px] font-black uppercase">Bulk Upload</span> @endif
                                @if($plan->priority_queue) <span class="px-2 py-0.5 bg-amber-500/10 text-amber-500 rounded text-[8px] font-black uppercase">Priority</span> @endif
                            </div>
                        </div>

                        <div class="p-6 mt-auto border-t border-zinc-100 dark:border-zinc-800">
                            <a href="{{ route('admin.plans.edit', $plan) }}" class="w-full inline-flex items-center justify-center py-3 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-900 dark:text-white rounded-xl font-bold text-sm transition-colors border border-transparent hover:border-zinc-300 dark:hover:border-zinc-600">
                                Configure Tier
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
