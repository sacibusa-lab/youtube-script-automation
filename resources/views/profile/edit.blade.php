<x-app-layout>
<div class="min-h-screen bg-zinc-950 py-10 px-4 sm:px-6 lg:px-8" x-data="{ tab: 'account' }">
    <div class="max-w-6xl mx-auto space-y-8">

        {{-- ── Page Header ──────────────────────────────────────────────────── --}}
        <div class="flex items-end justify-between">
            <div>
                <p class="text-[10px] font-black text-red-500 uppercase tracking-[0.3em] mb-1">Account</p>
                <h1 class="text-3xl font-black text-white tracking-tight">Your Profile</h1>
                <p class="text-sm text-zinc-500 mt-1">Manage your account, subscription, and billing.</p>
            </div>
            <div class="flex items-center gap-3">
                @if($user->plan)
                    <span class="px-3 py-1 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-[10px] font-black uppercase tracking-widest rounded-full">
                        {{ $user->plan->name }}
                    </span>
                @endif
                <span class="text-[10px] font-bold text-zinc-600 uppercase">Member since {{ $user->created_at->format('M Y') }}</span>
            </div>
        </div>

        {{-- ── Tab Nav ──────────────────────────────────────────────────────── --}}
        <div class="flex items-center gap-1 bg-zinc-900 border border-zinc-800 rounded-xl p-1 w-fit">
            @foreach([
                ['key' => 'account',      'label' => 'Account'],
                ['key' => 'subscription', 'label' => 'Subscription'],
                ['key' => 'security',     'label' => 'Security'],
                ['key' => 'billing',      'label' => 'Billing'],
            ] as $t)
            <button
                @click="tab = '{{ $t['key'] }}'"
                :class="tab === '{{ $t['key'] }}' ? 'bg-zinc-700 text-white shadow' : 'text-zinc-500 hover:text-zinc-200'"
                class="px-5 py-2 rounded-lg text-[11px] font-black uppercase tracking-widest transition-all"
            >{{ $t['label'] }}</button>
            @endforeach
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        {{-- TAB: ACCOUNT ──────────────────────────────────────────────────── --}}
        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        <div x-show="tab === 'account'" x-transition>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Avatar & Summary Card --}}
                <div class="lg:col-span-1">
                    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 flex flex-col items-center text-center gap-4">
                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-red-600 to-orange-500 flex items-center justify-center shadow-lg shadow-red-900/30">
                            <span class="text-3xl font-black text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-white">{{ $user->name }}</h2>
                            <p class="text-xs text-zinc-500">{{ $user->email }}</p>
                        </div>
                        <div class="w-full pt-4 border-t border-zinc-800 space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-black text-zinc-600 uppercase tracking-widest">Script Tokens</span>
                                <span class="text-sm font-black text-indigo-400">{{ number_format($scriptBalance) }}</span>
                            </div>
                            <div class="w-full bg-zinc-800 rounded-full h-1.5">
                                <div class="bg-indigo-500 h-1.5 rounded-full transition-all" style="width: {{ 100 - $scriptUsedPct }}%"></div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-black text-zinc-600 uppercase tracking-widest">Image Assets</span>
                                <span class="text-sm font-black text-emerald-400">{{ number_format($imageBalance) }}</span>
                            </div>
                            <div class="w-full bg-zinc-800 rounded-full h-1.5">
                                <div class="bg-emerald-500 h-1.5 rounded-full transition-all" style="width: {{ 100 - $imageUsedPct }}%"></div>
                            </div>
                        </div>
                        <a href="{{ route('topup.index') }}" class="w-full mt-2 py-2.5 bg-red-600 hover:bg-red-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition text-center">
                            Buy More Tokens
                        </a>
                    </div>
                </div>

                {{-- Edit Profile Form --}}
                <div class="lg:col-span-2">
                    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-8">
                        <h3 class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-6">Personal Information</h3>

                        @if(session('status') === 'profile-updated')
                            <div class="mb-6 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold rounded-xl flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Profile updated successfully.
                            </div>
                        @endif

                        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                            @csrf
                            @method('PATCH')

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Full Name</label>
                                    <input
                                        type="text" name="name"
                                        value="{{ old('name', $user->name) }}"
                                        class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-red-500/50 transition"
                                        required autofocus
                                    >
                                    @error('name')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Email Address</label>
                                    <input
                                        type="email" name="email"
                                        value="{{ old('email', $user->email) }}"
                                        class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-red-500/50 transition"
                                        required
                                    >
                                    @error('email')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="pt-2 flex items-center gap-4">
                                <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-red-900/20 transition active:scale-[0.98]">
                                    Save Changes
                                </button>
                                <span class="text-xs text-zinc-600">Last updated {{ $user->updated_at->diffForHumans() }}</span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        {{-- TAB: SUBSCRIPTION ─────────────────────────────────────────────── --}}
        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        <div x-show="tab === 'subscription'" x-transition>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Current Plan Card --}}
                <div class="lg:col-span-2 bg-zinc-900 border border-zinc-800 rounded-2xl p-8 relative overflow-hidden">
                    <div class="absolute -right-8 -top-8 w-40 h-40 bg-indigo-500/5 rounded-full blur-3xl"></div>
                    <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Current Plan</p>
                    @if($user->plan)
                        <h2 class="text-3xl font-black text-white mb-1">{{ $user->plan->name }}</h2>
                        <p class="text-sm text-zinc-500 mb-6">{{ $user->plan->description ?? 'Your active subscription plan.' }}</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
                            <div class="bg-zinc-800/60 rounded-xl p-4 border border-zinc-700/50">
                                <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-1">Script Tokens</p>
                                <p class="text-xl font-black text-indigo-400">{{ number_format($user->plan->credits ?? 0) }}</p>
                                <p class="text-[9px] text-zinc-600">per month</p>
                            </div>
                            <div class="bg-zinc-800/60 rounded-xl p-4 border border-zinc-700/50">
                                <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-1">Image Assets</p>
                                <p class="text-xl font-black text-emerald-400">{{ number_format($user->plan->image_tokens ?? 0) }}</p>
                                <p class="text-[9px] text-zinc-600">per month</p>
                            </div>
                            <div class="bg-zinc-800/60 rounded-xl p-4 border border-zinc-700/50">
                                <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest mb-1">Price</p>
                                <p class="text-xl font-black text-white">₦{{ number_format($user->plan->price ?? 0) }}</p>
                                <p class="text-[9px] text-zinc-600">per month</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('topup.index') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition">
                                Upgrade Plan
                            </a>
                            <a href="{{ route('billing.history') }}" class="px-5 py-2.5 bg-zinc-700 hover:bg-zinc-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition">
                                View All Invoices
                            </a>
                        </div>
                    @else
                        <div class="py-8 text-center">
                            <p class="text-zinc-500 text-sm font-bold mb-4">No active plan found.</p>
                            <a href="{{ route('topup.index') }}" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition">
                                Choose a Plan
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Token Usage Summary --}}
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 space-y-6">
                    <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">This Month's Usage</p>
                    <div class="space-y-5">
                        <div>
                            <div class="flex justify-between mb-1.5">
                                <span class="text-xs font-bold text-zinc-400">Script Tokens</span>
                                <span class="text-xs font-black text-white">{{ $scriptUsedPct }}% used</span>
                            </div>
                            <div class="w-full bg-zinc-800 rounded-full h-2">
                                <div class="bg-gradient-to-r from-indigo-600 to-indigo-400 h-2 rounded-full transition-all" style="width: {{ $scriptUsedPct }}%"></div>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-[9px] text-zinc-600">{{ number_format($user->used_credits) }} used</span>
                                <span class="text-[9px] text-zinc-600">{{ number_format($user->total_credits) }} total</span>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between mb-1.5">
                                <span class="text-xs font-bold text-zinc-400">Image Assets</span>
                                <span class="text-xs font-black text-white">{{ $imageUsedPct }}% used</span>
                            </div>
                            <div class="w-full bg-zinc-800 rounded-full h-2">
                                <div class="bg-gradient-to-r from-emerald-600 to-emerald-400 h-2 rounded-full transition-all" style="width: {{ $imageUsedPct }}%"></div>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-[9px] text-zinc-600">{{ number_format($user->used_image_tokens) }} used</span>
                                <span class="text-[9px] text-zinc-600">{{ number_format($user->total_image_tokens) }} total</span>
                            </div>
                        </div>
                    </div>

                    {{-- Recent Usage Log --}}
                    @if($recentUsage->count() > 0)
                    <div class="pt-4 border-t border-zinc-800">
                        <p class="text-[9px] font-black text-zinc-600 uppercase tracking-widest mb-3">Recent Activity</p>
                        <div class="space-y-2">
                            @foreach($recentUsage as $log)
                            <div class="flex items-center justify-between">
                                <p class="text-[10px] text-zinc-400 capitalize">{{ str_replace('_', ' ', $log->job_type) }}</p>
                                <span class="text-[10px] font-black {{ $log->type === 'image' ? 'text-emerald-400' : 'text-indigo-400' }}">
                                    -{{ number_format($log->total_credits_deducted) }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        {{-- TAB: SECURITY ─────────────────────────────────────────────────── --}}
        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        <div x-show="tab === 'security'" x-transition>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Change Password --}}
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-8">
                    <h3 class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-1">Change Password</h3>
                    <p class="text-xs text-zinc-600 mb-6">Use a strong password with at least 10 characters.</p>

                    @if(session('status') === 'password-updated')
                        <div class="mb-6 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold rounded-xl flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Password changed successfully.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.password') }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Current Password</label>
                            <input
                                type="password" name="current_password"
                                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-red-500/50 transition"
                                autocomplete="current-password"
                            >
                            @error('current_password', 'updatePassword')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">New Password</label>
                            <input
                                type="password" name="password"
                                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-red-500/50 transition"
                                autocomplete="new-password"
                            >
                            @error('password', 'updatePassword')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Confirm New Password</label>
                            <input
                                type="password" name="password_confirmation"
                                class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-red-500/50 transition"
                                autocomplete="new-password"
                            >
                        </div>

                        <button type="submit" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-red-900/20 transition active:scale-[0.98]">
                            Update Password
                        </button>
                    </form>
                </div>

                {{-- Danger Zone --}}
                <div class="bg-zinc-900 border border-red-900/30 rounded-2xl p-8">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <h3 class="text-[10px] font-black text-red-500 uppercase tracking-widest">Danger Zone</h3>
                    </div>
                    <p class="text-xs text-zinc-500 mb-6">Once you delete your account, all of your data — videos, characters, and tokens — will be permanently removed. This action is irreversible.</p>

                    <div x-data="{ open: false }">
                        <button @click="open = true" class="px-5 py-2.5 bg-red-600/10 hover:bg-red-600/20 border border-red-600/30 text-red-500 text-[10px] font-black uppercase tracking-widest rounded-xl transition">
                            Delete Account
                        </button>

                        {{-- Confirm Modal --}}
                        <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80" x-transition @keydown.escape.window="open = false">
                            <div class="bg-zinc-900 border border-red-900/50 rounded-2xl p-8 max-w-md w-full shadow-2xl" @click.stop>
                                <h4 class="text-lg font-black text-white mb-2">Are you absolutely sure?</h4>
                                <p class="text-sm text-zinc-500 mb-6">This will permanently delete your account and all associated data. Enter your password to confirm.</p>

                                <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4">
                                    @csrf
                                    @method('DELETE')
                                    <div>
                                        <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Confirm Password</label>
                                        <input type="password" name="password"
                                            class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500/50 transition"
                                        >
                                        @error('password', 'userDeletion')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="flex gap-3">
                                        <button type="submit" class="flex-1 py-3 bg-red-600 hover:bg-red-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition">
                                            Yes, Delete Account
                                        </button>
                                        <button type="button" @click="open = false" class="flex-1 py-3 bg-zinc-700 hover:bg-zinc-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        {{-- TAB: BILLING ──────────────────────────────────────────────────── --}}
        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        <div x-show="tab === 'billing'" x-transition>
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden">
                <div class="px-8 py-5 border-b border-zinc-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-black text-white">Payment History</h3>
                        <p class="text-[10px] text-zinc-500 uppercase tracking-widest mt-0.5">Last 10 transactions</p>
                    </div>
                    <a href="{{ route('billing.history') }}" class="text-[10px] font-black text-indigo-400 hover:text-indigo-300 uppercase tracking-widest transition">
                        View All →
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-zinc-800/60 text-zinc-500 text-[10px] uppercase tracking-widest font-black">
                            <tr>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Reference</th>
                                <th class="px-6 py-4">Plan / Package</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            @forelse($payments as $payment)
                            <tr class="hover:bg-zinc-800/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-zinc-400">
                                    {{ $payment->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-[10px] text-zinc-600 font-mono">
                                    {{ $payment->reference }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 {{ $payment->type === 'subscription' ? 'bg-blue-500/10 text-blue-400' : 'bg-purple-500/10 text-purple-400' }} text-[9px] rounded font-black uppercase tracking-tighter">
                                        {{ $payment->type }}
                                    </span>
                                    <span class="ml-2 text-sm text-white font-bold">
                                        {{ $payment->type === 'subscription' ? $payment->plan?->name : $payment->topupPackage?->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($payment->status === 'success')
                                        <span class="flex items-center gap-1.5 text-emerald-400 text-[10px] font-black uppercase">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Paid
                                        </span>
                                    @elseif($payment->status === 'pending')
                                        <span class="flex items-center gap-1.5 text-amber-400 text-[10px] font-black uppercase">
                                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                            Pending
                                        </span>
                                    @else
                                        <span class="flex items-center gap-1.5 text-red-400 text-[10px] font-black uppercase">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Failed
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right font-black text-white">
                                    ₦{{ number_format($payment->amount, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <svg class="w-10 h-10 text-zinc-800 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                    <p class="text-zinc-600 font-bold text-xs uppercase tracking-widest">No transactions yet</p>
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
</x-app-layout>
