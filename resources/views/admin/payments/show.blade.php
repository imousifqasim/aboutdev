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
        <a href="{{ route('admin.payments.index') }}" class="text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold">Payment Details</h1>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Payment Info -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold mb-4">Payment Information</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">User</span>
                    <span class="font-medium">{{ $payment->user->name ?? 'Deleted' }} ({{ $payment->user->email ?? '' }})</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Method</span>
                    <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Amount</span>
                    <span class="font-medium">${{ number_format($payment->amount, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Transaction ID</span>
                    <span class="font-mono text-xs">{{ $payment->transaction_id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Date</span>
                    <span>{{ $payment->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Status</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $payment->status === 'approved' ? 'bg-green-100 text-green-700' : ($payment->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </div>
                @if($payment->admin_note)
                    <div>
                        <span class="text-gray-500 block mb-1">Admin Note</span>
                        <p class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">{{ $payment->admin_note }}</p>
                    </div>
                @endif
                @if($payment->reviewer)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Reviewed By</span>
                        <span>{{ $payment->reviewer->name }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Screenshot -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold mb-4">Payment Screenshot</h2>
            <img src="{{ Storage::url($payment->screenshot_path) }}" alt="Payment Screenshot" class="w-full rounded-lg border border-gray-200 dark:border-gray-700">
        </div>
    </div>

    <!-- Actions -->
    @if($payment->status === 'pending')
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mt-6">
            <h2 class="text-lg font-semibold mb-4">Actions</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <!-- Approve -->
                <form method="POST" action="{{ route('admin.payments.approve', $payment) }}" class="space-y-3">
                    @csrf
                    <textarea name="admin_note" placeholder="Optional note..." rows="2"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 outline-none text-sm"></textarea>
                    <button type="submit" class="w-full bg-green-600 text-white py-2.5 rounded-lg font-medium hover:bg-green-700 transition">
                        <i class="fas fa-check mr-2"></i> Approve Payment
                    </button>
                </form>

                <!-- Reject -->
                <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" class="space-y-3">
                    @csrf
                    <textarea name="admin_note" placeholder="Reason for rejection (required)..." rows="2" required
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 outline-none text-sm"></textarea>
                    @error('admin_note') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
                    <button type="submit" class="w-full bg-red-600 text-white py-2.5 rounded-lg font-medium hover:bg-red-700 transition">
                        <i class="fas fa-times mr-2"></i> Reject Payment
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
