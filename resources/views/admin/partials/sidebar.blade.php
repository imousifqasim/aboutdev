<div class="mb-6">
    <div class="flex items-center space-x-3 px-3 py-2">
        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/50 rounded-full flex items-center justify-center">
            <i class="fas fa-shield-alt text-red-600 dark:text-red-400"></i>
        </div>
        <div>
            <p class="font-semibold text-sm">Admin Panel</p>
            <p class="text-xs text-gray-500">{{ auth()->user()->name }}</p>
        </div>
    </div>
</div>

<a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
    <i class="fas fa-tachometer-alt w-5"></i>
    <span>Dashboard</span>
</a>
<a href="{{ route('admin.users.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
    <i class="fas fa-users w-5"></i>
    <span>Users</span>
</a>
<a href="{{ route('admin.payments.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.payments.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
    <i class="fas fa-credit-card w-5"></i>
    <span>Payments</span>
    @php $pendingCount = \App\Models\Payment::where('status', 'pending')->count(); @endphp
    @if($pendingCount > 0)
        <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full ml-auto">{{ $pendingCount }}</span>
    @endif
</a>
<a href="{{ route('admin.subscriptions.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.subscriptions.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
    <i class="fas fa-crown w-5"></i>
    <span>Subscriptions</span>
</a>
<a href="{{ route('admin.settings.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.settings.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
    <i class="fas fa-cog w-5"></i>
    <span>Settings</span>
</a>

<hr class="my-4 dark:border-gray-700">

<a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
    <i class="fas fa-arrow-left w-5"></i>
    <span>Back to Site</span>
</a>
