<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin: System Settings') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: 'site' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-8 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
                    <p class="text-sm font-bold text-green-700 dark:text-green-400">{{ session('success') }}</p>
                </div>
            @endif

            <div class="flex flex-col md:flex-row gap-8">
                <!-- Sidebar Menu -->
                <div class="w-full md:w-64 shrink-0">
                    <div class="bg-white dark:bg-gray-800 rounded-[32px] border border-gray-100 dark:border-gray-700 p-4 shadow-sm">
                        <nav class="space-y-1">
                            <button @click="activeTab = 'site'" 
                                    :class="activeTab === 'site' ? 'bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                    class="w-full flex items-center px-4 py-3 text-sm font-bold rounded-2xl transition-all">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                Site Settings
                            </button>

                            <button @click="activeTab = 'payment'" 
                                    :class="activeTab === 'payment' ? 'bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                    class="w-full flex items-center px-4 py-3 text-sm font-bold rounded-2xl transition-all">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                Payment Settings
                            </button>

                            <button @click="activeTab = 'email'" 
                                    :class="activeTab === 'email' ? 'bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                    class="w-full flex items-center px-4 py-3 text-sm font-bold rounded-2xl transition-all">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                Email Settings
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-1">
                    <form method="POST" action="{{ route('admin.settings.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Site Settings -->
                        <div x-show="activeTab === 'site'" class="bg-white dark:bg-gray-800 rounded-[32px] border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden animate-reveal">
                            <div class="p-8 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Site Identity</h3>
                                <p class="text-xs text-gray-500 font-medium">Configure how your platform appears to the world.</p>
                            </div>
                            <div class="p-8 space-y-6">
                                <div>
                                    <label for="platform_name" class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-2">Platform Name</label>
                                    <input type="text" id="platform_name" name="platform_name" value="{{ old('platform_name', $settings['platform_name'] ?? config('app.name')) }}" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm focus:ring-teal-500 focus:border-teal-500 transition-all">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="logo" class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-2">Primary Logo</label>
                                        @if(isset($settings['logo']))
                                            <div class="mb-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-xl inline-block border border-gray-200 dark:border-gray-700">
                                                <img src="{{ Storage::url($settings['logo']) }}" alt="Logo" class="h-8">
                                            </div>
                                        @endif
                                        <input type="file" id="logo" name="logo" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 dark:file:bg-teal-900/30">
                                    </div>
                                    <div>
                                        <label for="favicon" class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-2">Favicon</label>
                                        @if(isset($settings['favicon']))
                                            <div class="mb-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-xl inline-block border border-gray-200 dark:border-gray-700">
                                                <img src="{{ Storage::url($settings['favicon']) }}" alt="Favicon" class="w-8 h-8">
                                            </div>
                                        @endif
                                        <input type="file" id="favicon" name="favicon" accept="image/*,.ico" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 dark:file:bg-teal-900/30">
                                    </div>
                                </div>

                                <div>
                                    <label for="footer_text" class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-2">Footer Copyright Text</label>
                                    <input type="text" id="footer_text" name="footer_text" value="{{ old('footer_text', $settings['footer_text'] ?? '') }}" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm focus:ring-teal-500 focus:border-teal-500 transition-all">
                                </div>
                            </div>
                        </div>

                        <!-- Payment Settings -->
                        <div x-show="activeTab === 'payment'" class="bg-white dark:bg-gray-800 rounded-[32px] border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden animate-reveal">
                            <div class="p-8 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Payment Integration</h3>
                                <p class="text-xs text-gray-500 font-medium">Configure Paystack or other gateways.</p>
                            </div>
                            <div class="p-8 space-y-6">
                                <div>
                                    <label for="paystack_public_key" class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-2">Paystack Public Key</label>
                                    <input type="text" id="paystack_public_key" name="paystack_public_key" value="{{ old('paystack_public_key', $settings['paystack_public_key'] ?? '') }}" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm transition-all" placeholder="pk_...">
                                </div>
                                <div>
                                    <label for="paystack_secret_key" class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-2">Paystack Secret Key</label>
                                    <input type="password" id="paystack_secret_key" name="paystack_secret_key" value="{{ old('paystack_secret_key', $settings['paystack_secret_key'] ?? '') }}" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm transition-all" placeholder="sk_...">
                                </div>
                                <div>
                                    <label for="paystack_webhook_secret" class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-2">Paystack Webhook Secret</label>
                                    <input type="password" id="paystack_webhook_secret" name="paystack_webhook_secret" value="{{ old('paystack_webhook_secret', $settings['paystack_webhook_secret'] ?? '') }}" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm transition-all">
                                </div>
                            </div>
                        </div>

                        <!-- Email Settings -->
                        <div x-show="activeTab === 'email'" class="bg-white dark:bg-gray-800 rounded-[32px] border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden animate-reveal">
                            <div class="p-8 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Email (SMTP) Settings</h3>
                                <p class="text-xs text-gray-500 font-medium">Configure how the platform sends transactional emails (OTP, Receipts).</p>
                            </div>
                            <div class="p-8 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="md:col-span-2">
                                        <label for="mail_host" class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-2">SMTP Host</label>
                                        <input type="text" id="mail_host" name="mail_host" value="{{ old('mail_host', $settings['mail_host'] ?? '') }}" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm transition-all" placeholder="smtp.mailtrap.io">
                                    </div>
                                    <div>
                                        <label for="mail_port" class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-2">SMTP Port</label>
                                        <input type="text" id="mail_port" name="mail_port" value="{{ old('mail_port', $settings['mail_port'] ?? '587') }}" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm transition-all" placeholder="587">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="mail_username" class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-2">SMTP Username</label>
                                        <input type="text" id="mail_username" name="mail_username" value="{{ old('mail_username', $settings['mail_username'] ?? '') }}" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm transition-all">
                                    </div>
                                    <div>
                                        <label for="mail_password" class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-2">SMTP Password</label>
                                        <input type="password" id="mail_password" name="mail_password" value="{{ old('mail_password', $settings['mail_password'] ?? '') }}" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm transition-all">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="mail_encryption" class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-2">Encryption</label>
                                        <select id="mail_encryption" name="mail_encryption" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm transition-all">
                                            <option value="tls" {{ (old('mail_encryption', $settings['mail_encryption'] ?? 'tls')) == 'tls' ? 'selected' : '' }}>TLS</option>
                                            <option value="ssl" {{ (old('mail_encryption', $settings['mail_encryption'] ?? '')) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                            <option value="null" {{ (old('mail_encryption', $settings['mail_encryption'] ?? '')) == 'null' ? 'selected' : '' }}>None</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="mail_from_name" class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-2">Sender Name</label>
                                        <input type="text" id="mail_from_name" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name'] ?? config('app.name')) }}" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm transition-all">
                                    </div>
                                </div>

                                <div>
                                    <label for="mail_from_address" class="block text-[11px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest mb-2">Sender Email Address</label>
                                    <input type="email" id="mail_from_address" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}" class="w-full bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm transition-all" placeholder="noreply@axelit.media">
                                </div>
                            </div>
                        </div>

                        <!-- Save Button Area -->
                        <div class="mt-8 flex justify-end">
                            <button type="submit" class="bg-teal-600 text-white px-10 py-4 rounded-2xl font-black text-[14px] uppercase tracking-widest hover:bg-teal-700 transition-all shadow-xl shadow-teal-500/20 active:scale-95">
                                Persist Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
