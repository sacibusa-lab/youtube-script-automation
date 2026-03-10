{{-- Character Card Partial --}}
<div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6 relative group overflow-hidden transition-all duration-300 hover:border-zinc-700 hover:shadow-xl hover:shadow-teal-900/10 flex flex-col">

    {{-- Badge: Global / Private --}}
    <div class="flex items-center justify-between mb-4">
        @if($character->is_global)
            <span class="text-[10px] font-bold uppercase tracking-widest text-amber-400 bg-amber-400/10 px-2 py-1 rounded-full">⭐ Starter</span>
        @else
            <span class="text-[10px] font-bold uppercase tracking-widest text-teal-400 bg-teal-400/10 px-2 py-1 rounded-full">My Character</span>
        @endif

        @if($character->niche)
            <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-400 bg-zinc-800 px-2 py-1 rounded-full">{{ $character->niche }}</span>
        @endif
    </div>

    {{-- Reference Image --}}
    @if($character->reference_image_url)
        <div class="w-20 h-20 rounded-2xl overflow-hidden mb-4 border border-zinc-700">
            <img src="{{ $character->reference_image_url }}" alt="{{ $character->name }}" class="w-full h-full object-cover">
        </div>
    @else
        <div class="w-20 h-20 rounded-2xl bg-zinc-800 border border-zinc-700 flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </div>
    @endif

    {{-- Name & Bio --}}
    <h3 class="text-white font-black text-lg leading-snug mb-1">{{ $character->name }}</h3>
    <p class="text-zinc-500 text-sm leading-relaxed line-clamp-3 mb-4 flex-1">{{ $character->description }}</p>

    {{-- Visual Traits Preview --}}
    @if(!empty($character->visual_traits))
        <div class="flex flex-wrap gap-1 mb-4">
            @foreach(array_filter($character->visual_traits) as $key => $val)
                <span class="text-[10px] bg-zinc-800 text-zinc-400 px-2 py-1 rounded-full capitalize">{{ $val }}</span>
            @endforeach
        </div>
    @endif

    {{-- Actions --}}
    @unless($readonly ?? false)
    <div class="flex items-center gap-3 mt-auto pt-4 border-t border-zinc-800">
        <a href="{{ route('characters.edit', $character) }}" class="flex-1 text-center text-sm font-bold text-zinc-400 hover:text-white bg-zinc-800 hover:bg-zinc-700 px-4 py-2 rounded-xl transition">
            Edit
        </a>
        <form action="{{ route('characters.destroy', $character) }}" method="POST" onsubmit="return confirm('Remove this character?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-zinc-600 hover:text-red-500 transition p-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        </form>
    </div>
    @endunless
</div>
