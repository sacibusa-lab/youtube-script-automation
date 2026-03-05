<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Select Plan -->
        <div class="mt-8">
            <x-input-label :value="__('Select Your Subscription Plan')" class="text-sm font-bold text-gray-900 mb-4" />
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($plans as $plan)
                    <label class="relative flex cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm focus:outline-none hover:border-teal-400 hover:shadow-md transition">
                        <input type="radio" name="plan_id" value="{{ $plan->id }}" class="sr-only" required {{ old('plan_id') == $plan->id ? 'checked' : '' }} onchange="updatePlanSelection(this)">
                        <div class="flex flex-1 items-center">
                            <div class="flex flex-col">
                                <span class="block text-sm font-black text-gray-900">{{ $plan->name }}</span>
                                <span class="mt-1 flex items-center text-sm text-gray-500">
                                    ₦{{ number_format($plan->price) }} / mo
                                </span>
                                <span class="mt-2 text-xs font-medium text-teal-600 bg-teal-50 px-2 py-1 rounded inline-block w-max">
                                    {{ number_format($plan->monthly_credits) }} Tokens
                                </span>
                            </div>
                        </div>
                        <svg class="h-5 w-5 text-teal-600 hidden check-icon absolute top-4 right-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </label>
                @endforeach
            </div>
            <x-input-error :messages="$errors->get('plan_id')" class="mt-2" />
        </div>

        <script>
            function updatePlanSelection(input) {
                // Remove styling from all
                document.querySelectorAll('input[name="plan_id"]').forEach(radio => {
                    let parent = radio.closest('label');
                    parent.classList.remove('border-teal-500', 'ring-1', 'ring-teal-500');
                    parent.classList.add('border-gray-200');
                    parent.querySelector('.check-icon').classList.add('hidden');
                });
                
                // Add styling to selected
                let parent = input.closest('label');
                parent.classList.remove('border-gray-200');
                parent.classList.add('border-teal-500', 'ring-1', 'ring-teal-500');
                parent.querySelector('.check-icon').classList.remove('hidden');
            }
        </script>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4 px-8 py-3 bg-teal-600 hover:bg-teal-700 font-black uppercase tracking-widest text-xs">
                {{ __('Continue to Payment') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
