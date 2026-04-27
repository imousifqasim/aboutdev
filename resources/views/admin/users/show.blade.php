@extends('layouts.dashboard')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('mobile-nav')
    @include('admin.partials.mobile-nav')
@endsection

@section('dashboard-content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold">User Details</h1>
    </div>

    <!-- User Info -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 gradient-bg rounded-full flex items-center justify-center text-white text-xl font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold">{{ $user->name }}</h2>
                    <p class="text-gray-500">{{ $user->email }}</p>
                    <p class="text-sm text-gray-500 mt-1">@{{ $user->profile->username ?? 'N/A' }} &middot; Joined {{ $user->created_at->format('M d, Y') }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('admin.users.ban', $user) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-3 py-1.5 text-sm font-medium rounded-lg {{ $user->is_banned ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }}">
                        {{ $user->is_banned ? 'Unban' : 'Ban' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.users.premium', $user) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-3 py-1.5 text-sm font-medium rounded-lg {{ $user->profile && $user->profile->is_premium ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }}">
                        {{ $user->profile && $user->profile->is_premium ? 'Remove Premium' : 'Give Premium' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Links -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="font-semibold mb-3">Links ({{ $user->links->count() }})</h3>
            <div class="space-y-2">
                @forelse($user->links as $link)
                    <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm">
                        <div class="truncate">
                            <p class="font-medium">{{ $link->title }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $link->url }}</p>
                        </div>
                        <span class="text-xs text-gray-500">{{ $link->clicks }} clicks</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No links.</p>
                @endforelse
            </div>
        </div>

        <!-- Payments -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
            <h3 class="font-semibold mb-3">Payments ({{ $user->payments->count() }})</h3>
            <div class="space-y-2">
                @forelse($user->payments as $payment)
                    <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm">
                        <div>
                            <p class="font-medium">${{ number_format($payment->amount, 2) }} — {{ ucfirst(str_replace('_', ' ', $payment->method)) }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->created_at->format('M d, Y') }}</p>
                        </div>
                        <a href="{{ route('admin.payments.show', $payment) }}" class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $payment->status === 'approved' ? 'bg-green-100 text-green-700' : ($payment->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ ucfirst($payment->status) }}
                        </a>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No payments.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
