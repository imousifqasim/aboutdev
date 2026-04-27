@extends('layouts.dashboard')

@section('sidebar')
    @include('user.partials.sidebar')
@endsection

@section('mobile-nav')
    @include('user.partials.mobile-nav')
@endsection

@section('dashboard-content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Subscription & Payments</h1>

    <!-- Current Plan -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold">Current Plan</h2>
                <p class="text-gray-600 dark:text-gray-400">
                    @if($user->isPremium())
                        <span class="inline-flex items-center gap-1 text-yellow-600 font-semibold">
                            <i class="fas fa-crown"></i> Premium
                        </span>
                        @if($user->activeSubscription)
                            — Valid until {{ $user->activeSubscription->end_date ? $user->activeSubscription->end_date->format('M d, Y') : 'Lifetime' }}
                        @endif
                    @else
                        <span class="font-semibold">Free Plan</span> — Limited features
                    @endif
                </p>
            </div>
            @if($user->isPremium())
                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm font-medium"><i class="fas fa-check mr-1"></i> Active</span>
            @endif
        </div>
    </div>

    @if(!$user->isPremium())
    <!-- Premium Benefits -->
    <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-xl p-6 mb-6 text-white">
        <h2 class="text-xl font-bold mb-3"><i class="fas fa-crown mr-2"></i> Upgrade to Premium</h2>
        <div class="grid sm:grid-cols-2 gap-2 mb-4 text-sm">
            <p><i class="fas fa-check mr-2"></i> Unlimited links</p>
            <p><i class="fas fa-check mr-2"></i> All premium themes</p>
            <p><i class="fas fa-check mr-2"></i> Remove branding</p>
            <p><i class="fas fa-check mr-2"></i> Gallery & video sections</p>
            <p><i class="fas fa-check mr-2"></i> Detailed analytics</p>
            <p><i class="fas fa-check mr-2"></i> Custom domain</p>
        </div>
        <p class="text-2xl font-bold">$9.99 <span class="text-lg font-normal opacity-80">/year</span></p>
    </div>

    <!-- Payment Methods -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Payment Methods</h2>
        <div class="grid sm:grid-cols-2 gap-3 mb-6">
            @foreach($paymentMethods as $method)
                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="font-medium text-sm">{{ $method['name'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $method['details'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
            <h3 class="font-medium text-blue-800 dark:text-blue-400 text-sm mb-2"><i class="fas fa-info-circle mr-1"></i> How to Pay</h3>
            <ol class="text-sm text-blue-700 dark:text-blue-400 space-y-1 list-decimal list-inside">
                <li>Choose a payment method above</li>
                <li>Send $9.99 using the details provided</li>
                <li>Fill in the form below with your payment details</li>
                <li>Upload a screenshot of your payment</li>
                <li>Wait for admin approval (usually within 24 hours)</li>
            </ol>
        </div>

        <!-- Payment Submit Form -->
        <h3 class="font-semibold mb-3">Submit Payment</h3>
        <form method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Payment Method</label>
                        <select name="method" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 outline-none">
                            <option value="">Select method</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method['value'] }}">{{ $method['name'] }}</option>
                            @endforeach
                        </select>
                        @error('method') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Amount (USD)</label>
                        <input type="number" name="amount" step="0.01" value="9.99" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 outline-none">
                        @error('amount') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Transaction ID</label>
                    <input type="text" name="transaction_id" required placeholder="Enter your transaction ID"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 outline-none">
                    @error('transaction_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Payment Screenshot</label>
                    <input type="file" name="screenshot" accept="image/*" required
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700">
                    @error('screenshot') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="gradient-bg text-white px-6 py-3 rounded-xl font-semibold hover:opacity-90 transition">
                    <i class="fas fa-paper-plane mr-2"></i> Submit Payment
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Payment History -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-20 lg:mb-0">
        <h2 class="text-lg font-semibold mb-4">Payment History</h2>
        @if($payments->count() > 0)
            <div class="space-y-3">
                @foreach($payments as $payment)
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div>
                            <p class="font-medium text-sm">{{ ucfirst(str_replace('_', ' ', $payment->method)) }} — ${{ number_format($payment->amount, 2) }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->created_at->format('M d, Y H:i') }} &middot; ID: {{ $payment->transaction_id }}</p>
                            @if($payment->admin_note)
                                <p class="text-xs text-gray-500 mt-1"><i class="fas fa-comment mr-1"></i> {{ $payment->admin_note }}</p>
                            @endif
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            {{ $payment->status === 'approved' ? 'bg-green-100 text-green-700' : ($payment->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-sm text-center py-4">No payments yet.</p>
        @endif
    </div>
</div>
@endsection
