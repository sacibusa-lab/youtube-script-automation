<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Subscription Plans') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-8 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
                    <p class="text-sm font-bold text-green-700 dark:text-green-400">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-[40px] border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="p-10 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-[24px] font-black text-gray-900 dark:text-white tracking-tight">System Plans</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Configure token limits, prices, and feature availability per tier.</p>
                </div>

                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="py-4 px-6 text-[11px] font-black text-gray-400 uppercase tracking-widest">Plan Name</th>
                                <th class="py-4 px-6 text-[11px] font-black text-gray-400 uppercase tracking-widest">Price (₦)</th>
                                <th class="py-4 px-6 text-[11px] font-black text-gray-400 uppercase tracking-widest">Mo. Credits</th>
                                <th class="py-4 px-6 text-[11px] font-black text-gray-400 uppercase tracking-widest">Max Tokens</th>
                                <th class="py-4 px-6 text-[11px] font-black text-gray-400 uppercase tracking-widest">API Access</th>
                                <th class="py-4 px-6 text-[11px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($plans as $plan)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition-colors">
                                    <td class="py-4 px-6">
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $plan->name }}</p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ number_format($plan->price, 2) }}</p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ number_format($plan->monthly_credits) }}</p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ number_format($plan->max_tokens_per_request) }}</p>
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($plan->api_access)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20">Enabled</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-50 text-gray-600 ring-1 ring-inset ring-gray-500/10">Disabled</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <a href="{{ route('admin.plans.edit', $plan) }}" class="inline-flex items-center justify-center p-2 text-teal-600 hover:text-teal-900 bg-teal-50 hover:bg-teal-100 rounded-lg transition-colors font-bold text-xs">
                                            Edit Limits
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
