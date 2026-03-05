<x-app-layout>
    <div class="py-12 bg-zinc-50 dark:bg-zinc-950 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('admin.users.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm font-bold flex items-center mb-4 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to User List
                </a>
                <h1 class="text-3xl font-extrabold text-white tracking-tight">Manage Account: {{ $user->name }}</h1>
                <p class="text-gray-400 text-sm mt-1 italic">Manually adjust plan limits, credits, and tokens.</p>
            </div>

            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-8">
                @csrf
                @method('PATCH')

                <!-- Plan Selection -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-8 shadow-sm relative overflow-hidden">
                    <div class="flex items-center space-x-4 mb-8">
                        <div class="p-3 bg-indigo-500/20 rounded-2xl">
                            <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <h2 class="text-xl font-bold text-white uppercase tracking-wider">Subscription Plan</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-black text-gray-500 uppercase tracking-widest mb-2">Current Active Tier</label>
                            <select name="plan_id" class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-0 transition-all">
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" {{ $user->plan_id == $plan->id ? "selected" : "" }} class="bg-white dark:bg-zinc-950">
                                        {{ $plan->name }} (₦{{ number_format($plan->price) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Token Balances -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-3xl p-8 shadow-sm relative overflow-hidden">
                    <div class="flex items-center space-x-4 mb-8">
                        <div class="p-3 bg-emerald-500/20 rounded-2xl">
                            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h2 class="text-xl font-bold text-white uppercase tracking-wider">Token Balances</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Script Tokens -->
                        <div class="space-y-4">
                            <h3 class="text-indigo-400 text-[10px] font-black uppercase tracking-widest flex items-center">
                                <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full mr-2"></span>
                                Script Credits
                            </h3>
                            <div class="flex space-x-4">
                                <div class="flex-1">
                                    <label class="block text-[10px] text-gray-500 font-bold mb-1">Total Allocated</label>
                                    <input type="number" name="total_credits" value="{{ $user->total_credits }}" class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-0">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-[10px] text-gray-500 font-bold mb-1">Used (Month)</label>
                                    <input type="number" name="used_credits" value="{{ $user->used_credits }}" class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-0">
                                </div>
                            </div>
                        </div>

                        <!-- Image Tokens -->
                        <div class="space-y-4">
                            <h3 class="text-purple-400 text-[10px] font-black uppercase tracking-widest flex items-center">
                                <span class="w-1.5 h-1.5 bg-purple-500 rounded-full mr-2"></span>
                                Image Assets
                            </h3>
                            <div class="flex space-x-4">
                                <div class="flex-1">
                                    <label class="block text-[10px] text-gray-500 font-bold mb-1">Total Limit</label>
                                    <input type="number" name="total_image_tokens" value="{{ $user->total_image_tokens }}" class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-0">
                                </div>
                                <div class="flex-1">
                                    <label class="block text-[10px] text-gray-500 font-bold mb-1">Used (Script Count)</label>
                                    <input type="number" name="used_image_tokens" value="{{ $user->used_image_tokens }}" class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4">
                    <button type="button" onclick="window.history.back()" class="px-8 py-4 bg-gray-900 text-gray-400 rounded-2xl font-bold border border-gray-800 hover:bg-gray-800 transition-colors">
                        Cancel Changes
                    </button>
                    <button type="submit" class="px-12 py-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-2xl font-black shadow-lg shadow-indigo-600/20 transition-all transform active:scale-95">
                        Save Account Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
