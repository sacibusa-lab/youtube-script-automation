<x-app-layout>
    <div class="max-w-2xl mx-auto space-y-8">
        {{-- Header --}}
        <div>
            <a href="{{ route('characters.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-400 hover:text-teal-600 transition mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Character Library
            </a>
            <h1 class="text-[34px] font-black text-gray-900 dark:text-white leading-none">New Character</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Define their visual DNA once. Reuse forever.</p>
        </div>

        <form action="{{ route('characters.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Name & Niche --}}
            <div class="bg-white dark:bg-gray-800 rounded-[28px] border border-gray-100 dark:border-gray-700 p-8 space-y-6">
                <h2 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Identity</h2>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Character Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required maxlength="100"
                        placeholder="e.g. Alicia"
                        class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-3 text-gray-900 dark:text-white font-medium focus:ring-2 focus:ring-teal-500 focus:border-transparent transition">
                    @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Short Bio <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="3" required maxlength="1000"
                        placeholder="e.g. A calm, relatable 30-year-old financial educator who makes complex money concepts simple."
                        class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-3 text-gray-900 dark:text-white font-medium focus:ring-2 focus:ring-teal-500 focus:border-transparent transition resize-none">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Associated Niche <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input type="text" name="niche" value="{{ old('niche') }}" maxlength="100"
                        placeholder="e.g. US Wealth Logic, True Crime..."
                        class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-3 text-gray-900 dark:text-white font-medium focus:ring-2 focus:ring-teal-500 focus:border-transparent transition">
                </div>
            </div>

            {{-- Visual DNA --}}
            <div class="bg-white dark:bg-gray-800 rounded-[28px] border border-gray-100 dark:border-gray-700 p-8 space-y-6">
                <div>
                    <h2 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Visual DNA</h2>
                    <p class="text-sm text-gray-400 mt-1">These details are injected into every AI image prompt featuring this character.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach([
                        ['key' => 'age', 'label' => 'Age / Age Range', 'placeholder' => 'e.g. Late 20s, 30'],
                        ['key' => 'ethnicity', 'label' => 'Ethnicity / Skin Tone', 'placeholder' => 'e.g. Light-skinned Black woman'],
                        ['key' => 'hair', 'label' => 'Hair', 'placeholder' => 'e.g. Short natural curls, dark brown'],
                        ['key' => 'eyes', 'label' => 'Eyes', 'placeholder' => 'e.g. Dark brown, warm almond-shaped'],
                        ['key' => 'build', 'label' => 'Body Build', 'placeholder' => 'e.g. Slender, medium height'],
                        ['key' => 'style', 'label' => 'Clothing Style', 'placeholder' => 'e.g. Smart casual, neutral tones'],
                    ] as $field)
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">{{ $field['label'] }}</label>
                        <input type="text" name="visual_traits[{{ $field['key'] }}]" value="{{ old('visual_traits.'.$field['key']) }}"
                            placeholder="{{ $field['placeholder'] }}"
                            class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-3 text-gray-900 dark:text-white font-medium text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent transition">
                    </div>
                    @endforeach
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Signature Detail</label>
                    <input type="text" name="visual_traits[signature_detail]" value="{{ old('visual_traits.signature_detail') }}"
                        placeholder="e.g. Always wears small gold hoop earrings, has a freckle above her left brow"
                        class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-3 text-gray-900 dark:text-white font-medium text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent transition">
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-4">
                <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white px-8 py-4 rounded-2xl font-black text-[15px] uppercase tracking-widest transition-all shadow-lg shadow-teal-100 dark:shadow-none hover:-translate-y-0.5">
                    Save Character
                </button>
                <a href="{{ route('characters.index') }}" class="px-6 py-4 rounded-2xl text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 font-bold transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
