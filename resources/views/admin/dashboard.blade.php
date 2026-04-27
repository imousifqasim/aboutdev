@extends('layouts.dashboard')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('mobile-nav')
    @include('admin.partials.mobile-nav')
@endsection

@section('dashboard-content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Admin Dashboard</h1>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-600 dark:text-gray-400">Total Users</span>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
            </div>
            <div class="text-3xl font-bold">{{ number_format($totalUsers) }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-600 dark:text-gray-400">Premium Users</span>
                <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-crown text-yellow-600"></i>
                </div>
            </div>
            <div class="text-3xl font-bold">{{ number_format($premiumUsers) }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-600 dark:text-gray-400">Pending Payments</span>
                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-red-600"></i>
                </div>
            </div>
            <div class="text-3xl font-bold">{{ $pendingPayments }}</div>
            @if($pendingPayments > 0)
                <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="text-xs text-primary-600 hover:underline">Review now</a>
            @endif
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-600 dark:text-gray-400">Total Revenue</span>
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-green-600"></i>
                </div>
            </div>
            <div class="text-3xl font-bold">${{ number_format($totalRevenue, 2) }}</div>
        </div>
    </div>

    <!-- Growth Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 mb-8">
        <h3 class="font-semibold mb-4">User Growth (Last 6 Months)</h3>
        <div class="h-48 flex items-end gap-4">
            @foreach($monthlyUsers as $data)
                <div class="flex-1 flex flex-col items-center gap-1">
                    <span class="text-xs font-medium">{{ $data['count'] }}</span>
                    <div class="w-full bg-primary-400 dark:bg-primary-600 rounded-t transition-all" style="height: {{ max(8, min($data['count'] * 20, 100)) }}%"></div>
                    <span class="text-xs text-gray-500 whitespace-nowrap">{{ $data['month'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6 mb-20 lg:mb-0">
        <!-- Recent Users -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold">Recent Users</h3>
                <a href="{{ route('admin.users.index') }}" class="text-sm text-primary-600 hover:underline">View all</a>
            </div>
            <div class="space-y-3">
                @foreach($recentUsers as $user)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 gradient-bg rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold">Recent Payments</h3>
                <a href="{{ route('admin.payments.index') }}" class="text-sm text-primary-600 hover:underline">View all</a>
            </div>
            <div class="space-y-3">
                @forelse($recentPayments as $payment)
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium">{{ $payment->user->name ?? 'Deleted' }}</p>
                            <p class="text-xs text-gray-500">${{ number_format($payment->amount, 2) }} via {{ ucfirst(str_replace('_', ' ', $payment->method)) }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $payment->status === 'approved' ? 'bg-green-100 text-green-700' : ($payment->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">No payments yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
