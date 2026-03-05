<x-app-layout>
    <div class="py-12 bg-zinc-50 dark:bg-zinc-950 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('admin.topup-packages.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:opacity-80 text-sm font-bold flex items-center mb-4 transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Bundles
                </a>
                <h1 class="text-3xl font-black text-gray-950 dark:text-white tracking-tight">Edit Credit Bundle</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Adjust pricing or contents for this package.</p>
            </div>

            <form action="{{ route('admin.topup-packages.update', $package) }}" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')

                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[32px] p-10 shadow-sm space-y-8">
                    <div class="space-y-2">
                        <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest">Bundle Label</label>
                        <input type="text" name="name" value="{{ old('name', $package->name) }}" required class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-5 py-4 text-gray-950 dark:text-white font-bold focus:border-indigo-500 focus:ring-0 transition-all">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest">Total Credits</label>
                            <input type="number" name="credits" value="{{ old('credits', $package->credits) }}" required class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-5 py-4 text-gray-950 dark:text-white font-bold focus:border-indigo-500 focus:ring-0 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest">Sale Price (₦)</label>
                            <input type="number" name="price" value="{{ old('price', $package->price) }}" required class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-2xl px-5 py-4 text-gray-950 dark:text-white font-bold focus:border-indigo-500 focus:ring-0 transition-all">
                        </div>
                    </div>

                    <label class="flex items-center gap-4 p-5 bg-zinc-50 dark:bg-zinc-950 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-800 cursor-pointer hover:border-indigo-500/50 transition-all">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $package->is_active) ? 'checked' : '' }} class="w-6 h-6 rounded-lg border-zinc-300 dark:border-zinc-800 text-indigo-600 focus:ring-indigo-500 bg-transparent">
                        <div>
                            <p class="text-sm font-black text-gray-950 dark:text-white uppercase tracking-tight leading-none">Visible to Users</p>
                            <p class="text-[10px] text-gray-400 mt-1 font-bold">Uncheck to hide this bundle from the marketplace.</p>
                        </div>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4">
                    <button type="submit" class="w-full py-5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-2xl shadow-indigo-600/30 transition-all transform active:scale-95 flex items-center justify-center gap-3">
                        Deploy Bundle Updates
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
