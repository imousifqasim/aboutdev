@extends('layouts.dashboard')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('mobile-nav')
    @include('admin.partials.mobile-nav')
@endsection

@section('dashboard-content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Payment Management</h1>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <form method="GET" class="flex gap-3">
            <select name="status" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 outline-none text-sm">
                <option value="">All Payments</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700">Filter</button>
        </form>
    </div>

    <!-- Payments Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-20 lg:mb-0">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">User</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">Method</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">Amount</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">Transaction ID</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">Date</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3">
                                <p class="font-medium">{{ $payment->user->name ?? 'Deleted' }}</p>
                                <p class="text-xs text-gray-500">{{ $payment->user->email ?? '' }}</p>
                            </td>
                            <td class="px-4 py-3">{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</td>
                            <td class="px-4 py-3 font-medium">${{ number_format($payment->amount, 2) }}</td>
                            <td class="px-4 py-3 text-xs font-mono">{{ $payment->transaction_id }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $payment->status === 'approved' ? 'bg-green-100 text-green-700' : ($payment->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $payment->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.payments.show', $payment) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">No payments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $payments->links() }}
        </div>
    </div>
</div>
@endsection
