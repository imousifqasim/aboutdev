@extends('layouts.dashboard')

@section('sidebar')
    @include('user.partials.sidebar')
@endsection

@section('mobile-nav')
    @include('user.partials.mobile-nav')
@endsection

@section('dashboard-content')
<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold">Welcome back, {{ $user->name }}!</h1>
        <p class="text-gray-600 dark:text-gray-400">Here's an overview of your profile performance.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-600 dark:text-gray-400">Profile Views</span>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-eye text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
            <div class="text-3xl font-bold">{{ number_format($totalViews) }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-600 dark:text-gray-400">Link Clicks</span>
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-mouse-pointer text-green-600 dark:text-green-400"></i>
                </div>
            </div>
            <div class="text-3xl font-bold">{{ number_format($totalClicks) }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-600 dark:text-gray-400">Total Links</span>
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-link text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
            <div class="text-3xl font-bold">{{ $totalLinks }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-600 dark:text-gray-400">Plan</span>
                <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-crown text-yellow-600 dark:text-yellow-400"></i>
                </div>
            </div>
            <div class="text-xl font-bold">
                @if($user->isPremium())
                    <span class="text-yellow-600">Premium</span>
                @else
                    <span>Free</span>
                    <a href="{{ route('payments.index') }}" class="text-sm text-primary-600 hover:underline block mt-1">Upgrade</a>
                @endif
            </div>
        </div>
    </div>

    <!-- Profile Link -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 mb-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-semibold mb-1">Your Profile Link</h3>
                <div class="flex items-center gap-2">
                    <code class="text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 px-3 py-1 rounded-lg text-sm">
                        {{ url('/' . $user->profile->username) }}
                    </code>
                    <button onclick="navigator.clipboard.writeText('{{ url('/' . $user->profile->username) }}'); this.innerHTML='<i class=\'fas fa-check\'></i>'; setTimeout(() => this.innerHTML='<i class=\'fas fa-copy\'></i>', 2000)"
                        class="text-gray-500 hover:text-primary-600 p-1">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
            <a href="{{ route('profile.show', $user->profile->username) }}" target="_blank" class="text-sm gradient-bg text-white px-4 py-2 rounded-lg hover:opacity-90 transition">
                <i class="fas fa-external-link-alt mr-1"></i> View Profile
            </a>
        </div>
    </div>

    <!-- Analytics Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 mb-8">
        <h3 class="font-semibold mb-4">Last 30 Days</h3>
        <div class="h-64 flex items-end gap-1" id="chart">
            @foreach($chartData as $day)
                <div class="flex-1 flex flex-col items-center gap-1 group relative">
                    <div class="absolute -top-8 hidden group-hover:block bg-gray-800 text-white text-xs px-2 py-1 rounded whitespace-nowrap z-10">
                        {{ $day['date'] }}: {{ $day['views'] }} views, {{ $day['clicks'] }} clicks
                    </div>
                    <div class="w-full bg-blue-400 dark:bg-blue-600 rounded-t transition-all hover:bg-blue-500" style="height: {{ max(2, ($day['views'] > 0 ? min($day['views'] * 10, 100) : 0)) }}%"></div>
                </div>
            @endforeach
        </div>
        <div class="flex justify-between mt-2 text-xs text-gray-500">
            <span>30 days ago</span>
            <span>Today</span>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('profile.edit') }}" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition text-center">
            <i class="fas fa-user-edit text-2xl text-blue-600 mb-2"></i>
            <p class="font-medium">Edit Profile</p>
        </a>
        <a href="{{ route('links.index') }}" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition text-center">
            <i class="fas fa-link text-2xl text-purple-600 mb-2"></i>
            <p class="font-medium">Manage Links</p>
        </a>
        <a href="{{ route('payments.index') }}" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-md transition text-center">
            <i class="fas fa-crown text-2xl text-yellow-600 mb-2"></i>
            <p class="font-medium">Subscription</p>
        </a>
    </div>
</div>
@endsection
