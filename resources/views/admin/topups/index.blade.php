<x-app-layout>
    <div class="py-12 bg-zinc-50 dark:bg-zinc-950 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-black text-gray-950 dark:text-white tracking-tight">Top-up Packages</h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Manage one-time credit bundles available for purchase.</p>
                </div>
                <a href="{{ route('admin.topup-packages.create') }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-lg shadow-indigo-600/20 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    New Bundle
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($packages as $package)
                    <div class="bg-white dark:bg-zinc-900 border {{ $package->is_active ? 'border-zinc-200 dark:border-zinc-800' : 'border-dashed border-zinc-300 dark:border-zinc-700 opacity-75' }} rounded-[32px] overflow-hidden shadow-sm hover:shadow-xl dark:hover:shadow-none transition-all flex flex-col group relative">
                        @if(!$package->is_active)
                            <div class="absolute top-4 right-4 z-10">
                                <span class="px-2 py-1 bg-zinc-200 dark:bg-zinc-800 text-zinc-500 rounded text-[8px] font-black uppercase tracking-tighter">Inactive</span>
                            </div>
                        @endif

                        <div class="p-8 border-b border-zinc-100 dark:border-zinc-800">
                            <div class="flex items-baseline gap-2 mb-1">
                                <span class="text-3xl font-black text-gray-950 dark:text-white italic">{{ number_format($package->credits / 1000) }}k</span>
                                <span class="text-sm font-bold text-gray-400 uppercase tracking-widest">Credits</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300">{{ $package->name }}</h3>
                        </div>

                        <div class="p-8 bg-zinc-50/50 dark:bg-zinc-900/50 flex-1 flex flex-col justify-center items-center">
                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-[0.2em] mb-1">Market Price</p>
                            <p class="text-4xl font-black text-indigo-600 dark:text-indigo-400 leading-none">₦{{ number_format($package->price) }}</p>
                        </div>

                        <div class="p-6 mt-auto border-t border-zinc-100 dark:border-zinc-800 flex gap-3">
                            <a href="{{ route('admin.topup-packages.edit', $package) }}" class="flex-1 inline-flex items-center justify-center py-3 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-900 dark:text-white rounded-xl font-bold text-sm transition-colors border border-transparent hover:border-zinc-300 dark:hover:border-zinc-600">
                                Edit
                            </a>
                            <form action="{{ route('admin.topup-packages.destroy', $package) }}" method="POST" onsubmit="return confirm('Archive this bundle?')" class="flex-none">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-3 bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white rounded-xl transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
