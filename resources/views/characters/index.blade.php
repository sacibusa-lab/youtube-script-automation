<x-app-layout>
    <div class="space-y-10">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-[40px] font-black text-gray-900 dark:text-white leading-none tracking-tight">Character Library</h1>
                <p class="text-gray-500 dark:text-gray-400 font-medium mt-3">Saved characters ensure visual consistency across all your video marathons.</p>
            </div>
            <a href="{{ route('characters.create') }}" class="inline-flex items-center gap-3 bg-teal-600 hover:bg-teal-700 text-white px-8 py-4 rounded-2xl font-black text-[15px] uppercase tracking-widest transition-all shadow-lg shadow-teal-100 dark:shadow-none hover:-translate-y-1 group">
                <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                New Character
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-6 py-4 rounded-2xl font-semibold">
                {{ session('success') }}
            </div>
        @endif

        {{-- Global Starters Section --}}
        @php $globals = $characters->where('is_global', true); @endphp
        @if($globals->count())
        <div class="space-y-5">
            <div class="flex items-center gap-4">
                <div class="w-1.5 h-10 bg-gradient-to-b from-amber-400 to-amber-600 rounded-full"></div>
                <h2 class="text-[22px] font-black text-gray-900 dark:text-white tracking-tight">Starter Characters</h2>
                <span class="text-[11px] font-bold uppercase tracking-widest text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 px-3 py-1 rounded-full">Global</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($globals as $character)
                    @include('characters._card', ['character' => $character, 'readonly' => true])
                @endforeach
            </div>
        </div>
        @endif

        {{-- User's Personal Characters --}}
        @php $personal = $characters->where('is_global', false); @endphp
        <div class="space-y-5">
            <div class="flex items-center gap-4">
                <div class="w-1.5 h-10 bg-gradient-to-b from-teal-500 to-teal-700 rounded-full"></div>
                <h2 class="text-[22px] font-black text-gray-900 dark:text-white tracking-tight">My Characters</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($personal as $character)
                    @include('characters._card', ['character' => $character, 'readonly' => false])
                @empty
                    <div class="col-span-full text-center py-20 bg-white dark:bg-gray-800 rounded-[40px] border-2 border-dashed border-gray-100 dark:border-gray-700">
                        <div class="w-16 h-16 bg-teal-50 dark:bg-teal-900/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white mb-2">No custom characters yet</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-xs mx-auto">Create recurring characters to keep their looks consistent across all your stories.</p>
                        <a href="{{ route('characters.create') }}" class="inline-flex items-center gap-2 bg-teal-600 text-white px-8 py-3 rounded-xl font-black text-sm uppercase tracking-widest hover:bg-teal-700 transition">
                            Create First Character
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
