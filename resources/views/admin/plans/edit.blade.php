<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Plan: ') }} {{ $plan->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                    <ul class="text-sm text-red-700 dark:text-red-400 font-medium list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm rounded-[40px] overflow-hidden">
                <div class="p-10 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <div>
                        <h3 class="text-[24px] font-black text-gray-900 dark:text-white tracking-tight">Plan Configuration</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Adjust the tier rules applied to the {{ $plan->name }} subscription.</p>
                    </div>
                    <a href="{{ route('admin.plans.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-900">&larr; Back to Plans</a>
                </div>

                <div class="p-10">
                    <form method="POST" action="{{ route('admin.plans.update', $plan) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Core Details -->
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wider">Core Pricing & Allocations</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label for="name" class="block text-[11px] font-black text-gray-600 uppercase tracking-widest mb-2">Display Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $plan->name) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-teal-500 focus:border-teal-500">
                            </div>
                            
                            <div>
                                <label for="price" class="block text-[11px] font-black text-gray-600 uppercase tracking-widest mb-2">Price (₦)</label>
                                <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $plan->price) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-teal-500 focus:border-teal-500">
                            </div>

                            <div>
                                <label for="monthly_credits" class="block text-[11px] font-black text-gray-600 uppercase tracking-widest mb-2">Monthly Credits (Script Tokens)</label>
                                <input type="number" name="monthly_credits" id="monthly_credits" value="{{ old('monthly_credits', $plan->monthly_credits) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-teal-500 focus:border-teal-500">
                            </div>

                            <div>
                                <label for="monthly_image_tokens" class="block text-[11px] font-black text-gray-600 uppercase tracking-widest mb-2">Monthly Image Tokens</label>
                                <input type="number" name="monthly_image_tokens" id="monthly_image_tokens" value="{{ old('monthly_image_tokens', $plan->monthly_image_tokens) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-purple-500 focus:border-purple-500">
                            </div>

                            <div>
                                <label for="rollover_percent" class="block text-[11px] font-black text-gray-600 uppercase tracking-widest mb-2">Rollover % (Max 1 Month Cap)</label>
                                <input type="number" min="0" max="100" name="rollover_percent" id="rollover_percent" value="{{ old('rollover_percent', $plan->rollover_percent) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-teal-500 focus:border-teal-500">
                            </div>
                        </div>

                        <!-- System Restrictors -->
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wider pt-6 border-t border-gray-100">Engine Restrictors</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div>
                                <label for="max_tokens_per_request" class="block text-[11px] font-black text-gray-600 uppercase tracking-widest mb-2">Internal Context Cap (Tokens/Req)</label>
                                <input type="number" name="max_tokens_per_request" id="max_tokens_per_request" value="{{ old('max_tokens_per_request', $plan->max_tokens_per_request) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-teal-500 focus:border-teal-500">
                            </div>
                            
                            <div>
                                <label for="concurrent_jobs" class="block text-[11px] font-black text-gray-600 uppercase tracking-widest mb-2">Concurrent Generation Jobs</label>
                                <input type="number" name="concurrent_jobs" id="concurrent_jobs" value="{{ old('concurrent_jobs', $plan->concurrent_jobs) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-teal-500 focus:border-teal-500">
                            </div>

                            <div>
                                <label for="batch_generation_limit" class="block text-[11px] font-black text-gray-600 uppercase tracking-widest mb-2">Batch Generator Capacity</label>
                                <input type="number" name="batch_generation_limit" id="batch_generation_limit" value="{{ old('batch_generation_limit', $plan->batch_generation_limit) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-teal-500 focus:border-teal-500">
                            </div>
                        </div>

                        <!-- Feature Flags -->
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wider pt-6 border-t border-gray-100">Plan Feature Access</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-10">
                            <label class="flex items-center space-x-3 p-4 border border-gray-100 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                                <input type="checkbox" name="bulk_upload" value="1" {{ old('bulk_upload', $plan->bulk_upload) ? 'checked' : '' }} class="w-5 h-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                <div>
                                    <span class="block text-sm font-bold text-gray-900">Bulk Upload Capability</span>
                                    <span class="block text-xs text-gray-500 font-medium">Allow mounting CSV files to the generator</span>
                                </div>
                            </label>

                            <label class="flex items-center space-x-3 p-4 border border-gray-100 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                                <input type="checkbox" name="api_access" value="1" {{ old('api_access', $plan->api_access) ? 'checked' : '' }} class="w-5 h-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                <div>
                                    <span class="block text-sm font-bold text-gray-900">Programmatic API Access</span>
                                    <span class="block text-xs text-gray-500 font-medium">Issue API Gateway keys</span>
                                </div>
                            </label>
                            
                            <label class="flex items-center space-x-3 p-4 border border-gray-100 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                                <input type="checkbox" name="series_memory" value="1" {{ old('series_memory', $plan->series_memory) ? 'checked' : '' }} class="w-5 h-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                <div>
                                    <span class="block text-sm font-bold text-gray-900">Series Memory (Studio)</span>
                                    <span class="block text-xs text-gray-500 font-medium">Enable long-term knowledge retention</span>
                                </div>
                            </label>

                            <label class="flex items-center space-x-3 p-4 border border-gray-100 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                                <input type="checkbox" name="team_members" value="1" {{ old('team_members', $plan->team_members) ? 'checked' : '' }} class="w-5 h-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                <div>
                                    <span class="block text-sm font-bold text-gray-900">Team Members Roles</span>
                                    <span class="block text-xs text-gray-500 font-medium">Allows onboarding extra users to workspace</span>
                                </div>
                            </label>

                            <label class="flex items-center space-x-3 p-4 border border-gray-100 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                                <input type="checkbox" name="priority_queue" value="1" {{ old('priority_queue', $plan->priority_queue) ? 'checked' : '' }} class="w-5 h-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                <div>
                                    <span class="block text-sm font-bold text-gray-900">Priority AI Queue</span>
                                    <span class="block text-xs text-gray-500 font-medium">Bypass token limits to high priority processing</span>
                                </div>
                            </label>
                            
                            <label class="flex items-center space-x-3 p-4 border border-gray-100 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                                <input type="checkbox" name="direct_support" value="1" {{ old('direct_support', $plan->direct_support) ? 'checked' : '' }} class="w-5 h-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                <div>
                                    <span class="block text-sm font-bold text-gray-900">Direct Support</span>
                                    <span class="block text-xs text-gray-500 font-medium">Access to VIP chat tier</span>
                                </div>
                            </label>
                        </div>

                        <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                            <button type="submit" class="bg-teal-600 text-white px-8 py-3 rounded-xl font-black text-[13px] uppercase tracking-widest hover:bg-teal-700 transition shadow-lg shadow-teal-100 dark:shadow-none">
                                Update Plan Restrictions
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
