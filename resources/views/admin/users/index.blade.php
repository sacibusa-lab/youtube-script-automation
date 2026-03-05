<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin: User Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-[40px] border border-gray-100 dark:border-gray-700">
                <div class="p-10">
                    <div class="flex items-center justify-between mb-10">
                        <div>
                            <h3 class="text-[28px] font-black text-gray-900 dark:text-white tracking-tight italic uppercase">Platform Members</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Manage all registered users and their roles.</p>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-[20px] font-bold text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-[20px] font-bold text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left border-b border-gray-100 dark:border-gray-700">
                                    <th class="pb-6 text-[11px] font-black text-gray-400 uppercase tracking-widest px-4">User</th>
                                    <th class="pb-6 text-[11px] font-black text-gray-400 uppercase tracking-widest px-4">Stories</th>
                                    <th class="pb-6 text-[11px] font-black text-gray-400 uppercase tracking-widest px-4">Role</th>
                                    <th class="pb-6 text-[11px] font-black text-gray-400 uppercase tracking-widest px-4">Joined</th>
                                    <th class="pb-6 text-[11px] font-black text-gray-400 uppercase tracking-widest px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                @foreach($users as $user)
                                    <tr class="group hover:bg-gray-50 dark:hover:bg-gray-700/20 transition-colors">
                                        <td class="py-6 px-4">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center font-black text-gray-400">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="font-black text-gray-800 dark:text-gray-100 text-sm tracking-tight">{{ $user->name }}</p>
                                                    <p class="text-[11px] text-gray-400 font-bold">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-6 px-4">
                                            <span class="font-black text-sm text-gray-700 dark:text-gray-300">{{ $user->videos_count }}</span>
                                        </td>
                                        <td class="py-6 px-4">
                                            @if($user->isAdmin())
                                                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-[9px] font-black uppercase tracking-widest">Admin</span>
                                            @else
                                                <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded-lg text-[9px] font-black uppercase tracking-widest">User</span>
                                            @endif
                                        </td>
                                        <td class="py-6 px-4">
                                            <span class="text-[11px] font-bold text-gray-400 uppercase">{{ $user->created_at->format('M d, Y') }}</span>
                                        </td>
                                        <td class="py-6 px-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm">
                                                        {{ $user->isAdmin() ? 'Demote' : 'Make Admin' }}
                                                    </button>
                                                </form>
                                                
                                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Wait! Are you sure you want to PERMANENTLY remove this user?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 rounded-xl text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-10">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
