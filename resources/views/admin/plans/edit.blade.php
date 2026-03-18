<x-app-layout>
    <div class="py-12 bg-zinc-50 dark:bg-zinc-950 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('admin.plans.index') }}" class="text-teal-600 dark:text-teal-400 hover:opacity-80 text-sm font-bold flex items-center mb-4 transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Tier List
                </a>
                <h1 class="text-3xl font-black text-gray-950 dark:text-white tracking-tight">Modify Tier: {{ $plan->name }}</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Update limits and pricing for this subscription level.</p>
            </div>

            @if ($errors->any())
                <div class="mb-8 p-6 bg-rose-500/10 border border-rose-500/20 rounded-2xl">
                    <h3 class="text-rose-600 dark:text-rose-400 font-black text-xs uppercase tracking-widest mb-4 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Configuration Errors Detected
                    </h3>
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="text-rose-500 dark:text-rose-400/80 text-[11px] font-bold uppercase tracking-tight">• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.plans.update', $plan) }}" method="POST" class="space-y-8 pb-20">
                @csrf
                @method('PATCH')

                <!-- Base Metrics -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[32px] p-10 shadow-sm relative overflow-hidden group">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="p-3 bg-teal-500/10 rounded-2xl">
                            <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h2 class="text-xl font-black text-gray-950 dark:text-white uppercase tracking-wider">Base Configuration</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest">Plan Name</label>
                            <input type="text" name="name" value="{{ old('name', $plan->name) }}" class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-5 py-4 text-gray-950 dark:text-white font-bold focus:border-teal-500 focus:ring-0 transition-all">
                        </div>
                        <div class="space-y-2">
                             <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest">Pricing Model</label>
                             <label class="flex items-center gap-3 p-4 bg-zinc-50 dark:bg-zinc-950 rounded-2xl border border-zinc-200 dark:border-zinc-800 cursor-pointer hover:border-teal-500/50 transition-all">
                                 <input type="checkbox" name="is_active" value="1" {{ old('is_active', $plan->is_active) ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-zinc-300 dark:border-zinc-800 text-teal-600 focus:ring-teal-500 bg-transparent">
                                 <span class="text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-tight">Active for display</span>
                             </label>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest">Monthly Price (₦)</label>
                            <input type="number" name="price" value="{{ old('price', $plan->price) }}" class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-5 py-4 text-gray-950 dark:text-white font-bold focus:border-teal-500 focus:ring-0 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Token Allocations -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[32px] p-10 shadow-sm relative overflow-hidden group">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="p-3 bg-indigo-500/10 rounded-2xl">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                        </div>
                        <h2 class="text-xl font-black text-gray-950 dark:text-white uppercase tracking-wider">Token limits</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-10">
                        <div class="space-y-4">
                            <h3 class="text-teal-600 dark:text-teal-400 text-xs font-black uppercase tracking-widest flex items-center">
                                <span class="w-2 h-2 bg-teal-500 rounded-full mr-3"></span>
                                Script Ecosystem
                            </h3>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1.5 px-1">Monthly Script Credits</label>
                                    <input type="number" name="monthly_credits" value="{{ old('monthly_credits', $plan->monthly_credits) }}" class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-5 py-4 text-gray-950 dark:text-white font-bold focus:border-teal-500 ring-0">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1.5 px-1">Rollover Percentage (0-100)</label>
                                    <input type="number" name="rollover_percent" value="{{ old('rollover_percent', $plan->rollover_percent) }}" class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-5 py-4 text-gray-950 dark:text-white font-bold focus:border-teal-500 ring-0">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h3 class="text-rose-600 dark:text-rose-400 text-xs font-black uppercase tracking-widest flex items-center">
                                <span class="w-2 h-2 bg-rose-500 rounded-full mr-3"></span>
                                Image Economy
                            </h3>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1.5 px-1">Free Image Tokens / Month</label>
                                    <input type="number" name="monthly_image_tokens" value="{{ old('monthly_image_tokens', $plan->monthly_image_tokens) }}" class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-5 py-4 text-gray-950 dark:text-white font-bold focus:border-teal-500 ring-0">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1.5 px-1">Credit Cost per Image</label>
                                    <input type="number" name="image_credit_cost" value="{{ old('image_credit_cost', $plan->image_credit_cost) }}" class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-5 py-4 text-gray-950 dark:text-white font-bold focus:border-teal-500 ring-0">
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <h3 class="text-purple-600 dark:text-purple-400 text-xs font-black uppercase tracking-widest flex items-center">
                                <span class="w-2 h-2 bg-purple-500 rounded-full mr-3"></span>
                                Voice Synthesis
                            </h3>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1.5 px-1">Monthly Voice Tokens</label>
                                    <input type="number" name="monthly_voice_tokens" value="{{ old('monthly_voice_tokens', $plan->monthly_voice_tokens) }}" class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-5 py-4 text-gray-950 dark:text-white font-bold focus:border-teal-500 ring-0">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1.5 px-1">Voice Credit Cost (per scene)</label>
                                    <input type="number" name="voice_token_cost" value="{{ old('voice_token_cost', $plan->voice_token_cost) }}" class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-5 py-4 text-gray-950 dark:text-white font-bold focus:border-teal-500 ring-0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Infrastructure & Limits -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[32px] p-10 shadow-sm relative overflow-hidden group">
                     <div class="flex items-center gap-4 mb-8">
                        <div class="p-3 bg-amber-500/10 rounded-2xl">
                            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <h2 class="text-xl font-black text-gray-950 dark:text-white uppercase tracking-wider">Infrastructure Limits</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                        <div class="space-y-2">
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest">Max Tokens per Request</label>
                            <input type="number" name="max_tokens_per_request" value="{{ old('max_tokens_per_request', $plan->max_tokens_per_request) }}" class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-5 py-4 text-gray-950 dark:text-white font-bold focus:border-teal-500 ring-0">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest">Concurrent Job Limit</label>
                            <input type="number" name="concurrent_jobs" value="{{ old('concurrent_jobs', $plan->concurrent_jobs) }}" class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-5 py-4 text-gray-950 dark:text-white font-bold focus:border-teal-500 ring-0">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest">Batch Gen Limit</label>
                            <input type="number" name="batch_generation_limit" value="{{ old('batch_generation_limit', $plan->batch_generation_limit) }}" class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-5 py-4 text-gray-950 dark:text-white font-bold focus:border-teal-500 ring-0">
                        </div>
                        <div class="space-y-2">
                             <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest">Max Images Per Script</label>
                             <input type="number" name="max_images_per_script" value="{{ old('max_images_per_script', $plan->max_images_per_script) }}" class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-5 py-4 text-gray-950 dark:text-white font-bold focus:border-teal-500 ring-0">
                        </div>
                        <div class="space-y-2">
                             <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest">Regen Attempts</label>
                             <input type="number" name="max_regeneration_attempts" value="{{ old('max_regeneration_attempts', $plan->max_regeneration_attempts) }}" class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-5 py-4 text-gray-950 dark:text-white font-bold focus:border-teal-500 ring-0">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @php
                            $features = [
                                'api_access' => 'REST API Gateway',
                                'bulk_upload' => 'Mass CSV Processing',
                                'priority_queue' => 'Priority Render Queue',
                                'direct_support' => 'Dedicated Support',
                                'series_memory' => 'AI Story Memory',
                                'team_members' => 'Multi-user Teams'
                            ];
                        @endphp

                        @foreach($features as $field => $label)
                            <label class="flex items-center gap-3 p-4 bg-zinc-50 dark:bg-zinc-950 rounded-2xl border border-zinc-200 dark:border-zinc-800 cursor-pointer hover:border-teal-500/50 transition-all">
                                <input type="checkbox" name="{{ $field }}" value="1" {{ old($field, $plan->$field) ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-zinc-300 dark:border-zinc-800 text-teal-600 focus:ring-teal-500 bg-transparent">
                                <span class="text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-tight">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 fixed bottom-8 right-8 z-50">
                    <button type="button" onclick="window.history.back()" class="px-8 py-5 bg-white dark:bg-zinc-800 text-gray-500 dark:text-gray-400 rounded-2xl font-black text-xs uppercase tracking-widest border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-all shadow-xl">
                        Abandon Changes
                    </button>
                    <button type="submit" class="px-12 py-5 bg-teal-600 hover:bg-teal-500 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-2xl shadow-teal-600/30 transition-all transform active:scale-95 flex items-center gap-3">
                        Deploy Configuration
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
