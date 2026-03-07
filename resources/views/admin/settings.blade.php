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
                    <h3 class="text-[24px] font-black text-gray-900 dark:text-white tracking-tight">System Settings</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Configure platform identity and payment integrations.</p>
                </div>

                <div class="p-10">
                    <form method="POST" action="{{ route('admin.settings.store') }}" enctype="multipart/form-data" class="space-y-6">
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

                        <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Platform Identity</h4>
                            
                            <div class="space-y-6">
                                <div>
                                    <label for="platform_name" class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-2">Platform Name</label>
                                    <input type="text" 
                                        id="platform_name" 
                                        name="platform_name" 
                                        value="{{ old('platform_name', $settings['platform_name'] ?? config('app.name')) }}" 
                                        class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm focus:ring-teal-500 focus:border-teal-500 transition-colors"
                                        placeholder="StoryBee">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="logo" class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-2">Primary Logo</label>
                                        @if(isset($settings['logo']))
                                            <div class="mb-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg inline-block border border-gray-200 dark:border-gray-700">
                                                <img src="{{ Storage::url($settings['logo']) }}" alt="Current Logo" class="h-8 object-contain">
                                            </div>
                                        @endif
                                        <input type="file" 
                                            id="logo" 
                                            name="logo" 
                                            accept="image/*"
                                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 dark:file:bg-teal-900/30 dark:file:text-teal-400">
                                    </div>

                                    <div>
                                        <label for="favicon" class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-2">Favicon (Icon)</label>
                                        @if(isset($settings['favicon']))
                                            <div class="mb-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg inline-block border border-gray-200 dark:border-gray-700">
                                                <img src="{{ Storage::url($settings['favicon']) }}" alt="Current Favicon" class="w-8 h-8 object-contain">
                                            </div>
                                        @endif
                                        <input type="file" 
                                            id="favicon" 
                                            name="favicon" 
                                            accept="image/*,.ico"
                                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 dark:file:bg-teal-900/30 dark:file:text-teal-400">
                                    </div>
                                </div>

                                <div>
                                    <label for="footer_text" class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-2">Copyright / Footer Text</label>
                                    <input type="text" 
                                        id="footer_text" 
                                        name="footer_text" 
                                        value="{{ old('footer_text', $settings['footer_text'] ?? '© ' . date('Y') . ' Yoursite. All rights reserved.') }}" 
                                        class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm focus:ring-teal-500 focus:border-teal-500 transition-colors"
                                        placeholder="&copy; 2024 Your Company. All rights reserved.">
                                </div>
                            </div>
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
