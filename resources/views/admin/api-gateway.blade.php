<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-gray-900 dark:text-white tracking-tight">
                {{ __('System API Gateway') }}
            </h2>
            <div class="flex items-center gap-2 px-4 py-2 bg-green-50 dark:bg-green-900/20 rounded-2xl border border-green-100 dark:border-green-800">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span class="text-[10px] font-black text-green-700 dark:text-green-400 uppercase tracking-widest">Orchestration Active</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-[40px] border border-gray-100 dark:border-gray-700 transition-colors">
                <div class="p-10 bg-white dark:bg-gray-800" x-data="{ openAddModal: false, modalProvider: '', modalName: '', showModalKey: false }">
                    
                    @if (session('success'))
                        <div class="mb-10 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-900/30 text-green-700 dark:text-green-400 px-6 py-4 rounded-[24px] flex items-center gap-4 animate-in fade-in slide-in-from-top-4 duration-500">
                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                            <span class="font-bold text-sm">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-10 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-900/30 text-red-700 dark:text-red-400 px-6 py-4 rounded-[24px] flex items-center gap-4 animate-in fade-in slide-in-from-top-4 duration-500">
                            <div class="w-2 h-2 rounded-full bg-red-500"></div>
                            <span class="font-bold text-sm">{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- Quick Stats Dashboard -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                        <div class="bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700 rounded-[32px] p-6 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center text-red-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Total System Keys</p>
                                <h5 class="text-xl font-black text-gray-900 dark:text-white">{{ $allActiveKeys->count() }}</h5>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700 rounded-[32px] p-6 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Active Roles</p>
                                <h5 class="text-xl font-black text-gray-900 dark:text-white">{{ $roles->where('is_active', true)->count() }} Agents</h5>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700 rounded-[32px] p-6 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center text-teal-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Global Strategy</p>
                                <h5 class="text-xl font-black text-gray-900 dark:text-white">{{ $allActiveKeys->firstWhere('is_primary', true) ? 'Forced Primary' : 'Auto-Failover' }}</h5>
                            </div>
                        </div>
                    </div>

                    <!-- Infrastructure Management -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
                        <!-- Strategy Sidebar -->
                        <div class="lg:col-span-4 space-y-8">
                            <div class="bg-gray-900 rounded-[32px] p-8 shadow-xl text-white relative overflow-hidden group">
                                <div class="absolute -right-4 -top-4 w-24 h-24 bg-red-600/20 rounded-full blur-2xl group-hover:bg-red-600/30 transition-colors"></div>
                                <h4 class="font-black text-[18px] mb-2 italic flex items-center gap-2">
                                    Selection Strategy
                                    <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>
                                </h4>
                                <p class="text-[11px] text-gray-400 font-bold uppercase tracking-widest mb-8 leading-relaxed">Choose how the platform picks its default key during intense generation cycles.</p>
                                
                                <form action="{{ route('admin.api-gateway.update-strategy') }}" method="POST">
                                    @csrf
                                    <div class="space-y-6">
                                        <div>
                                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3">Primary Global Key</label>
                                            <select name="primary_api_key_id" class="w-full bg-gray-800 border-none rounded-2xl py-4 px-6 text-sm font-bold focus:ring-2 focus:ring-red-500 transition-all text-gray-200">
                                                <option value="">Auto-Failover (Dynamic)</option>
                                                @foreach($allActiveKeys as $key)
                                                    <option value="{{ $key->id }}" {{ $key->is_primary ? 'selected' : '' }}>
                                                        {{ $key->provider }}: {{ $key->label ?? 'Default' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="pt-6 border-t border-gray-800">
                                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3">Media Rendering Authority</label>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach(['openai' => 'DALL-E', 'stabilityai' => 'SDXL', 'gemini' => 'Imagen'] as $p => $label)
                                                    <div class="flex items-center gap-2 px-3 py-2 rounded-xl {{ $allActiveKeys->firstWhere('provider', $p) ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'bg-gray-800 text-gray-600 border border-transparent opacity-50' }}">
                                                        <div class="w-1.5 h-1.5 rounded-full {{ $allActiveKeys->firstWhere('provider', $p) ? 'bg-blue-500 animate-pulse' : 'bg-gray-600' }}"></div>
                                                        <span class="text-[9px] font-black uppercase tracking-tighter">{{ $label }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <p class="text-[9px] text-gray-500 font-bold mt-3 leading-relaxed uppercase tracking-tighter italic">Renderer is automatically prioritized based on your active keys above.</p>
                                        </div>
                                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-4 rounded-2xl font-black text-[11px] uppercase tracking-widest transition shadow-lg shadow-red-900/40 transform active:scale-[0.98]">
                                            Deploy Infrastructure
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700/20 rounded-[32px] p-8 border border-dashed border-gray-200 dark:border-gray-700">
                                <h4 class="font-black text-[14px] text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2 uppercase tracking-tight">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Infrastructure Notes
                                </h4>
                                <ul class="text-[11px] text-gray-500 dark:text-gray-400 font-bold space-y-4 uppercase tracking-tighter">
                                    <li class="flex gap-3">
                                        <span class="text-red-500">•</span>
                                        <span>Failover priorities are calculated automatically based on token usage and error rates.</span>
                                    </li>
                                    <li class="flex gap-3">
                                        <span class="text-red-500">•</span>
                                        <span>Production Roles inherit settings from the primary key unless overridden.</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Providers Grid -->
                        <div class="lg:col-span-8 space-y-6">
                            @foreach($providers as $slug => $name)
                                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-[32px] p-8 shadow-sm transition-all">
                                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-6 mb-8 pb-8 border-b border-gray-50 dark:border-gray-700">
                                        <div class="flex items-center gap-5">
                                            <div class="w-14 h-14 rounded-3xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center font-black text-red-600 dark:text-red-400 text-xl shadow-inner">
                                                {{ strtoupper(substr($slug, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h4 class="font-black text-[22px] text-gray-900 dark:text-white leading-none mb-2">{{ $name }} Core</h4>
                                                <div class="flex items-center gap-3">
                                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50 dark:bg-gray-700 px-2 py-1 rounded-md">
                                                        {{ count($apiKeys->get($slug, [])) }} System Slots
                                                    </span>
                                                    @if(count($apiKeys->get($slug, [])) > 0)
                                                        <span class="flex items-center gap-1.5 text-[10px] font-black text-green-600 uppercase tracking-widest px-2 py-1 bg-green-50 dark:bg-green-900/20 rounded-md">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                            Online
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <button @click="openAddModal = true; modalProvider = '{{ $slug }}'; modalName = '{{ $name }}'" 
                                                class="bg-gray-900 dark:bg-red-600 text-white px-6 py-3.5 rounded-2xl font-black text-[11px] uppercase tracking-widest hover:bg-red-600 dark:hover:bg-red-700 transition shadow-lg shadow-gray-200 dark:shadow-none transform active:scale-95">
                                            + Register New Slot
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @forelse($apiKeys->get($slug, []) as $key)
                                            <div x-data="{ showKey: false }" class="p-6 rounded-[28px] border-2 {{ $key->is_primary ? 'border-red-500 bg-red-50/10' : 'border-gray-50 dark:border-gray-700 bg-gray-50/30' }} {{ $key->is_active ? '' : 'opacity-40 grayscale' }} transition-all relative group/card">
                                                @if($key->is_primary)
                                                    <div class="absolute -top-3 left-6 bg-red-600 text-white text-[9px] font-black uppercase tracking-widest px-3 py-1 rounded-full shadow-lg z-10 border-2 border-white dark:border-gray-800">
                                                        Global Authority
                                                    </div>
                                                @endif

                                                <div class="flex items-center justify-between mb-6">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-3 h-3 rounded-full {{ $key->is_active ? 'bg-green-500 shadow-[0_0_12px_rgba(34,197,94,0.6)]' : 'bg-gray-400' }}"></div>
                                                        <div class="font-black text-[15px] text-gray-800 dark:text-gray-100 tracking-tight">{{ $key->label ?? 'Unnamed Segment' }}</div>
                                                    </div>
                                                    <div class="flex items-center gap-1 opacity-0 group-hover/card:opacity-100 transition-opacity">
                                                        @if(!$key->is_primary && $key->is_active)
                                                            <form action="{{ route('admin.api-gateway.set-primary', $key) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" title="Elevate to Primary" class="p-2 rounded-xl text-teal-600 bg-teal-50 hover:bg-teal-100 transition">
                                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.342L9 3.511l-1.945-1.3a1 1 0 00-1.45.342c-.446.74-.705 1.6-.705 2.447v4h2v2h2v-2h2v-4c0-.847-.26-1.707-.705-2.447zM11 15v2a1 1 0 11-2 0v-2h2z" clip-rule="evenodd"></path></svg>
                                                                </button>
                                                            </form>
                                                        @endif
                                                        <form action="{{ route('admin.api-gateway.toggle', $key) }}" method="POST">
                                                            @csrf @method('PATCH')
                                                            <button type="submit" class="p-2 rounded-xl {{ $key->is_active ? 'text-blue-600 bg-blue-50' : 'text-gray-400 bg-gray-100' }} hover:scale-105 transition">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('admin.api-gateway.destroy', $key) }}" method="POST" onsubmit="return confirm('Archive this slot?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="p-2 rounded-xl text-red-600 bg-red-50 hover:bg-red-100 transition">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                                
                                                <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-inner">
                                                    <div class="text-[12px] font-mono text-gray-500 dark:text-gray-400 select-all overflow-hidden">
                                                        <span x-text="showKey ? '{{ $key->decrypted_value }}' : '••••••••{{ substr($key->decrypted_value, -4) }}'"></span>
                                                    </div>
                                                    <button @click="showKey = !showKey" class="ml-2 text-gray-400 hover:text-red-600 transition-colors">
                                                        <svg x-show="!showKey" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        <svg x-show="showKey" style="display: none;" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.04m4.5 4.5a3.5 3.5 0 004.5 4.5M15.133 15.133a3 3 0 11-4.266-4.266M17.272 17.272L21 21M3 3l3.59 3.59m0 0A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L12 12"/></svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-span-2 text-center py-10 bg-gray-50/50 dark:bg-gray-700/10 rounded-[32px] border-2 border-dashed border-gray-100 dark:border-gray-700">
                                                <p class="text-gray-400 dark:text-gray-500 text-[11px] font-black uppercase tracking-widest italic">No slots active for this segment</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Production AI Roles: THE MASTER GRID -->
                    <div class="pt-12 border-t border-gray-100 dark:border-gray-700">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
                            <div>
                                <h4 class="font-black text-[28px] text-gray-900 dark:text-white flex items-center gap-4 tracking-tighter">
                                    <div class="w-12 h-12 rounded-[20px] bg-red-600 flex items-center justify-center text-white shadow-lg shadow-red-200 dark:shadow-none">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    </div>
                                    Production AI Roles
                                </h4>
                                <p class="text-sm text-gray-400 font-bold uppercase tracking-widest mt-2 ml-16">Assign specialized heavy-hitters to your orchestration pipeline</p>
                            </div>
                            <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700 p-2 rounded-2xl border border-gray-100 dark:border-gray-600">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-3">Aggregator: OpenRouter + Native Rails</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                            @foreach($roles as $role)
                                <div class="bg-white dark:bg-gray-800 border-2 {{ $role->is_active ? 'border-gray-50 dark:border-gray-700 hover:border-red-500' : 'border-gray-200 dark:border-gray-800 opacity-60' }} rounded-[40px] p-8 shadow-sm hover:shadow-xl transition-all group relative overflow-hidden">
                                    <div class="absolute -right-12 -bottom-12 w-48 h-48 bg-gray-50 dark:bg-gray-700/20 rounded-full scale-0 group-hover:scale-100 transition-transform duration-700"></div>
                                    
                                    <div class="relative z-10">
                                        <div class="flex items-center gap-5 mb-6">
                                            <div class="w-14 h-14 shrink-0 rounded-[22px] bg-gray-900 dark:bg-gray-700 flex items-center justify-center text-white group-hover:bg-red-600 transition-colors shadow-lg">
                                                @if($role->slug === 'strategist')
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                                @elseif($role->slug === 'architect')
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                @elseif($role->slug === 'narrator')
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                                                @elseif($role->slug === 'artist')
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                @else
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between mb-1">
                                                    <h5 class="font-black text-[20px] text-gray-900 dark:text-white leading-none tracking-tighter truncate">{{ $role->name }}</h5>
                                                </div>
                                                <span class="text-[9px] font-black text-red-500 uppercase tracking-widest px-2 py-0.5 bg-red-50 dark:bg-red-900/40 rounded-full border border-red-100 dark:border-red-900/50">ACTIVE AGENT</span>
                                            </div>
                                        </div>

                                        <p class="text-gray-500 dark:text-gray-400 text-[12px] font-medium mb-8 leading-relaxed h-[36px] overflow-hidden line-clamp-2">{{ $role->description }}</p>
                                        
                                        <form action="{{ route('admin.api-gateway.update-role', $role) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="flex items-center justify-between gap-4">
                                                <label class="shrink-0 text-[10px] font-black text-gray-400 uppercase tracking-widest">Selected Brain</label>
                                                <div class="relative flex-1">
                                                    <select name="selected_model" onchange="this.form.submit()" 
                                                            class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl py-3 px-4 text-xs font-black text-gray-800 dark:text-white appearance-none focus:ring-2 focus:ring-red-500 transition-all hover:bg-gray-100 dark:hover:bg-gray-600">
                                                        @foreach($availableModels as $modelValue => $modelName)
                                                            <option value="{{ $modelValue }}" {{ $role->selected_model === $modelValue ? 'selected' : '' }}>
                                                                {{ $modelName }}
                                                                @if($role->recommended_model === $modelValue) (Recommended) @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Add Key Modal -->
                    <div x-show="openAddModal" 
                         x-cloak
                         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/80 backdrop-blur-md"
                         style="display: none;">
                        <div @click.away="openAddModal = false" class="bg-white dark:bg-gray-800 rounded-[48px] p-12 w-full max-w-xl shadow-2xl transition-all animate-in zoom-in-95 duration-200">
                            <div class="flex justify-between items-start mb-10">
                                <div>
                                    <h3 class="font-black text-[32px] text-gray-900 dark:text-white tracking-tighter leading-none mb-3">Connect <span x-text="modalName"></span></h3>
                                    <p class="text-gray-400 dark:text-gray-500 text-sm font-bold uppercase tracking-widest">Registering a platform-wide system key</p>
                                </div>
                                <button @click="openAddModal = false" class="p-3 rounded-2xl bg-gray-50 dark:bg-gray-700 text-gray-400 hover:text-red-500 hover:rotate-90 transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <form action="{{ route('admin.api-gateway.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="provider" :value="modalProvider">
                                
                                <div class="space-y-8">
                                    <div>
                                        <label class="block text-[11px] font-black text-gray-500 uppercase tracking-widest mb-3 px-1">Internal Key Reference</label>
                                        <input type="text" name="label" required placeholder="e.g. Master Production Key" 
                                               class="w-full bg-gray-50 dark:bg-gray-700 border-2 border-transparent rounded-[24px] py-5 px-8 focus:ring-0 focus:border-red-500 font-black text-gray-900 dark:text-white dark:placeholder-gray-500 transition-all text-lg">
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-black text-gray-500 uppercase tracking-widest mb-3 px-1">API Authentication Token</label>
                                        <div class="relative">
                                            <input :type="showModalKey ? 'text' : 'password'" name="api_key" required placeholder="sk-or-v1-..." 
                                                   class="w-full bg-gray-50 dark:bg-gray-700 border-2 border-transparent rounded-[24px] py-5 px-8 focus:ring-0 focus:border-red-500 font-black text-gray-900 dark:text-white dark:placeholder-gray-500 transition-all text-lg pr-16 tracking-widest">
                                            <button type="button" @click="showModalKey = !showModalKey" class="absolute inset-y-0 right-0 flex items-center pr-6 text-gray-400 hover:text-red-600 transition-colors">
                                                <svg x-show="!showModalKey" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <svg x-show="showModalKey" style="display: none;" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.04m4.5 4.5a3.5 3.5 0 004.5 4.5M15.133 15.133a3 3 0 11-4.266-4.266M17.272 17.272L21 21M3 3l3.59 3.59m0 0A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L12 12"/></svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex gap-4 pt-4">
                                        <button type="submit" 
                                                class="w-full bg-red-600 text-white py-6 rounded-[28px] font-black text-base uppercase tracking-widest shadow-2xl shadow-red-200 dark:shadow-none hover:bg-red-700 transition transform hover:-translate-y-1 active:translate-y-0">
                                            Establish Connection
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
