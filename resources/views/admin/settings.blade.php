<x-app-layout>
    <div class="min-h-screen bg-transparent" x-data="{ activeTab: 'site' }">
        <div class="max-w-screen-2xl mx-auto space-y-8">
            
            <!-- Page Header -->
            <div class="flex items-center justify-between mb-8 px-4 sm:px-0">
                <div>
                    <h1 class="text-3xl font-black text-gray-950 dark:text-white tracking-tight uppercase italic">Settings Terminal</h1>
                    <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm font-bold uppercase tracking-widest">Configure platform identity, payments, and communications</p>
                </div>
            </div>

            @if (session('success'))
                <div class="mb-8 p-4 bg-teal-500/10 border border-teal-500/20 rounded-2xl flex items-center gap-3 mx-4 sm:mx-0">
                    <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <p class="text-sm font-bold text-teal-600 dark:text-teal-400">{{ session('success') }}</p>
                </div>
            @endif

            <div class="flex flex-col lg:flex-row gap-8 items-start">
                <!-- Sidebar Menu -->
                <div class="w-full lg:w-72 shrink-0 px-4 sm:px-0">
                    <div class="bg-white dark:bg-zinc-900 rounded-[32px] border border-zinc-200 dark:border-zinc-800 p-4 shadow-sm">
                        <nav class="space-y-2">
                            <button @click="activeTab = 'site'" 
                                    :class="activeTab === 'site' ? 'bg-teal-500 text-white shadow-lg shadow-teal-500/20' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800'"
                                    class="w-full flex items-center px-5 py-4 text-sm font-bold rounded-2xl transition-all text-left">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span>Site Identity</span>
                            </button>

                            <button @click="activeTab = 'payment'" 
                                    :class="activeTab === 'payment' ? 'bg-teal-500 text-white shadow-lg shadow-teal-500/20' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800'"
                                    class="w-full flex items-center px-5 py-4 text-sm font-bold rounded-2xl transition-all text-left">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                <span>Payment Integration</span>
                            </button>

                            <button @click="activeTab = 'email'" 
                                    :class="activeTab === 'email' ? 'bg-teal-500 text-white shadow-lg shadow-teal-500/20' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800'"
                                    class="w-full flex items-center px-5 py-4 text-sm font-bold rounded-2xl transition-all text-left">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <span>Email Architecture</span>
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Main Content Panel -->
                <div class="flex-1 w-full px-4 sm:px-0">
                    <form method="POST" action="{{ route('admin.settings.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Site Settings Section -->
                        <div x-show="activeTab === 'site'" x-cloak class="bg-white dark:bg-zinc-900 rounded-[32px] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                            <div class="p-8 border-b border-zinc-100 dark:border-zinc-800">
                                <h3 class="text-xl font-black text-gray-950 dark:text-white tracking-tight uppercase italic">Site Brand Identity</h3>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest mt-1">Configure your platform aesthetics and identity</p>
                            </div>
                            <div class="p-8 space-y-8">
                                <div>
                                    <label for="platform_name" class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-3">Platform Designation</label>
                                    <input type="text" id="platform_name" name="platform_name" value="{{ old('platform_name', $settings['platform_name'] ?? config('app.name')) }}" class="w-full bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-teal-500/20 text-gray-900 dark:text-white font-bold transition-all">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="bg-zinc-50 dark:bg-zinc-800/30 p-6 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-700">
                                        <label for="logo" class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-4">Master Logo (Primary)</label>
                                        @if(isset($settings['logo']))
                                            <div class="mb-5 p-4 bg-white dark:bg-zinc-900 rounded-xl inline-block border border-zinc-100 dark:border-zinc-800 shadow-sm">
                                                <img src="{{ Storage::url($settings['logo']) }}" alt="Logo" class="h-10">
                                            </div>
                                        @endif
                                        <input type="file" id="logo" name="logo" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-teal-500 file:text-white hover:file:bg-teal-600 transition-all">
                                    </div>
                                    <div class="bg-zinc-50 dark:bg-zinc-800/30 p-6 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-700">
                                        <label for="favicon" class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-4">System Favicon (Icon)</label>
                                        @if(isset($settings['favicon']))
                                            <div class="mb-5 p-4 bg-white dark:bg-zinc-900 rounded-xl inline-block border border-zinc-100 dark:border-zinc-800 shadow-sm">
                                                <img src="{{ Storage::url($settings['favicon']) }}" alt="Favicon" class="w-10 h-10">
                                            </div>
                                        @endif
                                        <input type="file" id="favicon" name="favicon" accept="image/*,.ico" class="w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-teal-500 file:text-white hover:file:bg-teal-600 transition-all">
                                    </div>
                                </div>

                                <div>
                                    <label for="footer_text" class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-3">Legal Footer Signature</label>
                                    <input type="text" id="footer_text" name="footer_text" value="{{ old('footer_text', $settings['footer_text'] ?? '') }}" class="w-full bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 rounded-2xl px-5 py-4 text-sm text-gray-900 dark:text-white font-bold transition-all">
                                </div>
                            </div>
                        </div>

                        <!-- Payment Settings Section -->
                        <div x-show="activeTab === 'payment'" x-cloak class="bg-white dark:bg-zinc-900 rounded-[32px] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                            <div class="p-8 border-b border-zinc-100 dark:border-zinc-800">
                                <h3 class="text-xl font-black text-gray-950 dark:text-white tracking-tight uppercase italic">Payment Processor Config</h3>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest mt-1">Configure checkout systems and gateway handshakes</p>
                            </div>
                            <div class="p-8 space-y-8">
                                <div>
                                    <label for="paystack_public_key" class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-3">Paystack Public Key</label>
                                    <input type="text" id="paystack_public_key" name="paystack_public_key" value="{{ old('paystack_public_key', $settings['paystack_public_key'] ?? '') }}" class="w-full bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 rounded-2xl px-5 py-4 text-sm font-mono transition-all" placeholder="pk_...">
                                </div>
                                <div x-data="{ show: false }" class="relative">
                                    <label for="paystack_secret_key" class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-3">Paystack Private Key</label>
                                    <input :type="show ? 'text' : 'password'" id="paystack_secret_key" name="paystack_secret_key" value="{{ old('paystack_secret_key', $settings['paystack_secret_key'] ?? '') }}" class="w-full bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 rounded-2xl px-5 py-4 text-sm font-mono transition-all pr-12" placeholder="sk_...">
                                    <button type="button" @click="show = !show" class="absolute right-4 top-[38px] text-gray-400 hover:text-teal-500 transition-colors">
                                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 1.254 0 2.415.279 3.447.778m3.933 3.933A9.978 9.978 0 0121.542 12c-1.274 4.057-5.064 7-9.542 7-1.254 0-2.415-.279-3.447-.778m3.933-3.933l.847.847m1.586 1.586l2.121 2.121M3 3l18 18"/></svg>
                                    </button>
                                </div>
                                <div x-data="{ show: false }" class="relative">
                                    <label for="paystack_webhook_secret" class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-3">Gateway Webhook Hash</label>
                                    <input :type="show ? 'text' : 'password'" id="paystack_webhook_secret" name="paystack_webhook_secret" value="{{ old('paystack_webhook_secret', $settings['paystack_webhook_secret'] ?? '') }}" class="w-full bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 rounded-2xl px-5 py-4 text-sm font-mono transition-all pr-12">
                                    <button type="button" @click="show = !show" class="absolute right-4 top-[38px] text-gray-400 hover:text-teal-500 transition-colors">
                                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 1.254 0 2.415.279 3.447.778m3.933 3.933A9.978 9.978 0 0121.542 12c-1.274 4.057-5.064 7-9.542 7-1.254 0-2.415-.279-3.447-.778m3.933-3.933l.847.847m1.586 1.586l2.121 2.121M3 3l18 18"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Email Settings Section -->
                        <div x-show="activeTab === 'email'" x-cloak class="bg-white dark:bg-zinc-900 rounded-[32px] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                            <div class="p-8 border-b border-zinc-100 dark:border-zinc-800">
                                <h3 class="text-xl font-black text-gray-950 dark:text-white tracking-tight uppercase italic">SMTP Mail Relay Architecture</h3>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest mt-1">Configure transactional mail servers and sender protocol</p>
                            </div>
                            <div class="p-8 space-y-8">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                    <div class="md:col-span-3">
                                        <label for="mail_host" class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-3">SMTP Host Endpoint</label>
                                        <input type="text" id="mail_host" name="mail_host" value="{{ old('mail_host', $settings['mail_host'] ?? '') }}" class="w-full bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 rounded-2xl px-5 py-4 text-sm font-bold transition-all" placeholder="smtp.provider.com">
                                    </div>
                                    <div>
                                        <label for="mail_port" class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-3">Relay Port</label>
                                        <input type="text" id="mail_port" name="mail_port" value="{{ old('mail_port', $settings['mail_port'] ?? '587') }}" class="w-full bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 rounded-2xl px-5 py-4 text-sm font-bold transition-all" placeholder="587">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div>
                                        <label for="mail_username" class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-3">Station Username</label>
                                        <input type="text" id="mail_username" name="mail_username" value="{{ old('mail_username', $settings['mail_username'] ?? '') }}" class="w-full bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 rounded-2xl px-5 py-4 text-sm font-bold transition-all">
                                    </div>
                                    <div x-data="{ show: false }" class="relative">
                                        <label for="mail_password" class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-3">Station Access Key</label>
                                        <input :type="show ? 'text' : 'password'" id="mail_password" name="mail_password" value="{{ old('mail_password', $settings['mail_password'] ?? '') }}" class="w-full bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 rounded-2xl px-5 py-4 text-sm font-bold transition-all pr-12">
                                        <button type="button" @click="show = !show" class="absolute right-4 top-[38px] text-gray-400 hover:text-teal-500 transition-colors">
                                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 1.254 0 2.415.279 3.447.778m3.933 3.933A9.978 9.978 0 0121.542 12c-1.274 4.057-5.064 7-9.542 7-1.254 0-2.415-.279-3.447-.778m3.933-3.933l.847.847m1.586 1.586l2.121 2.121M3 3l18 18"/></svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div>
                                        <label for="mail_encryption" class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-3">Encryption Protocol</label>
                                        <select id="mail_encryption" name="mail_encryption" class="w-full bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 rounded-2xl px-5 py-4 text-sm font-bold transition-all focus:ring-0">
                                            <option value="tls" {{ (old('mail_encryption', $settings['mail_encryption'] ?? 'tls')) == 'tls' ? 'selected' : '' }}>TLS (Recommended)</option>
                                            <option value="ssl" {{ (old('mail_encryption', $settings['mail_encryption'] ?? '')) == 'ssl' ? 'selected' : '' }}>SSL (Legacy)</option>
                                            <option value="null" {{ (old('mail_encryption', $settings['mail_encryption'] ?? '')) == 'null' ? 'selected' : '' }}>Plain Text (Insecure)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="mail_from_name" class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-3">Manifest Sender Name</label>
                                        <input type="text" id="mail_from_name" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name'] ?? config('app.name')) }}" class="w-full bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 rounded-2xl px-5 py-4 text-sm font-bold transition-all">
                                    </div>
                                </div>

                                <div>
                                    <label for="mail_from_address" class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-3">Origin Email Address</label>
                                    <input type="email" id="mail_from_address" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}" class="w-full bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 rounded-2xl px-5 py-4 text-sm font-bold transition-all" placeholder="noreply@domain.com">
                                </div>
                            </div>
                        </div>

                        <!-- Global Persist Trigger -->
                        <div class="mt-10 flex justify-end px-4 sm:px-0">
                            <button type="submit" class="bg-gray-900 dark:bg-teal-500 text-white px-12 py-5 rounded-2xl font-black text-[12px] uppercase tracking-[0.3em] hover:bg-black dark:hover:bg-teal-600 transition-all shadow-2xl active:scale-95 flex items-center gap-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                Synchronize Platform
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
