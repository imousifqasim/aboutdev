@extends('layouts.dashboard')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('mobile-nav')
    @include('admin.partials.mobile-nav')
@endsection

@section('dashboard-content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">User Management</h1>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..."
                class="flex-1 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 outline-none text-sm">
            <select name="status" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 outline-none text-sm">
                <option value="">All Users</option>
                <option value="premium" {{ request('status') === 'premium' ? 'selected' : '' }}>Premium</option>
                <option value="free" {{ request('status') === 'free' ? 'selected' : '' }}>Free</option>
                <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Banned</option>
            </select>
            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700">
                <i class="fas fa-search mr-1"></i> Search
            </button>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-20 lg:mb-0">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">User</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">Username</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">Plan</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">Joined</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 gradient-bg rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">{{ $user->profile->username ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if($user->profile && $user->profile->is_premium)
                                    <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs font-medium"><i class="fas fa-crown mr-1"></i> Premium</span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-xs font-medium">Free</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($user->is_banned)
                                    <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-medium">Banned</span>
                                @else
                                    <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-medium">Active</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('admin.users.show', $user) }}" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg" title="View">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.users.ban', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-2 {{ $user->is_banned ? 'text-green-600 hover:bg-green-50' : 'text-yellow-600 hover:bg-yellow-50' }} rounded-lg" title="{{ $user->is_banned ? 'Unban' : 'Ban' }}">
                                            <i class="fas fa-{{ $user->is_banned ? 'unlock' : 'ban' }} text-xs"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg" title="Delete">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
