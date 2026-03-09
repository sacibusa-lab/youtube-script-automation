<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-black text-gray-900 font-outfit tracking-tight">Verify Your Email</h2>
        <p class="text-sm text-gray-600 mt-2">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by entering the 6-digit code we just emailed to you?') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 font-medium text-sm text-teal-600 text-center bg-teal-50 p-3 rounded-xl border border-teal-100">
            {{ __('A new verification code has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <form method="POST" action="{{ route('verification.verify') }}">
        @csrf

        <!-- OTP Code Input -->
        <div class="mb-6">
            <x-input-label for="otp_code" value="{{ __('6-Digit Code') }}" />
            
            <x-text-input id="otp_code" class="block mt-1 w-full text-center text-3xl tracking-[0.5em] font-mono py-4 uppercase" 
                          type="text" 
                          name="otp_code" 
                          maxlength="6"
                          required 
                          autofocus />

            <x-input-error :messages="$errors->get('otp_code')" class="mt-2 text-center" />
        </div>

        <div class="flex items-center justify-between mt-8">
            <button type="button" 
                    onclick="document.getElementById('resend-code-form').submit();"
                    class="text-sm text-gray-600 hover:text-gray-900 font-medium underline">
                {{ __('Resend Code') }}
            </button>

            <button type="submit" class="inline-flex items-center px-6 py-3 bg-zinc-900 border border-transparent rounded-full font-bold text-xs text-white uppercase tracking-widest hover:bg-zinc-800 focus:bg-zinc-800 active:bg-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Verify') }}
            </button>
        </div>
    </form>

    <!-- Hidden Resend Form -->
    <form id="resend-code-form" method="POST" action="{{ route('verification.send') }}" class="hidden">
        @csrf
    </form>

    <div class="mt-8 pt-6 border-t border-gray-100 text-center">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-500 hover:text-gray-900 font-medium">
                {{ __('Log out and try another account') }}
            </button>
        </form>
    </div>
</x-guest-layout>
