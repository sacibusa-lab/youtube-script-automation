<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin: System Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-8 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
                    <p class="text-sm font-bold text-green-700 dark:text-green-400">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-[40px] border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="p-10 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-[24px] font-black text-gray-900 dark:text-white tracking-tight">Payment Integrations</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Configure Paystack API keys for billing the subscription plans.</p>
                </div>

                <div class="p-10">
                    <form method="POST" action="{{ route('admin.settings.store') }}" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label for="paystack_public_key" class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-2">Paystack Public Key</label>
                            <input type="text" 
                                id="paystack_public_key" 
                                name="paystack_public_key" 
                                value="{{ old('paystack_public_key', $settings['paystack_public_key'] ?? '') }}" 
                                class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm focus:ring-teal-500 focus:border-teal-500 transition-colors"
                                placeholder="pk_test_...">
                        </div>

                        <div>
                            <label for="paystack_secret_key" class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-2">Paystack Secret Key</label>
                            <input type="password" 
                                id="paystack_secret_key" 
                                name="paystack_secret_key" 
                                value="{{ old('paystack_secret_key', $settings['paystack_secret_key'] ?? '') }}" 
                                class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm focus:ring-teal-500 focus:border-teal-500 transition-colors"
                                placeholder="sk_test_...">
                        </div>
                        
                        <div>
                            <label for="paystack_webhook_secret" class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-2">Webhook Secret (Optional)</label>
                            <input type="password" 
                                id="paystack_webhook_secret" 
                                name="paystack_webhook_secret" 
                                value="{{ old('paystack_webhook_secret', $settings['paystack_webhook_secret'] ?? '') }}" 
                                class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm focus:ring-teal-500 focus:border-teal-500 transition-colors"
                                placeholder="...">
                            <p class="mt-2 text-xs text-gray-500">Used to verify incoming requests from Paystack.</p>
                        </div>

                        <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                            <button type="submit" class="bg-teal-600 text-white px-8 py-3 rounded-xl font-black text-[13px] uppercase tracking-widest hover:bg-teal-700 transition shadow-lg shadow-teal-100 dark:shadow-none">
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
