<a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center py-1 px-3 {{ request()->routeIs('admin.dashboard') ? 'text-primary-600' : 'text-gray-500' }}">
    <i class="fas fa-tachometer-alt text-lg"></i>
    <span class="text-xs mt-1">Dashboard</span>
</a>
<a href="{{ route('admin.users.index') }}" class="flex flex-col items-center py-1 px-3 {{ request()->routeIs('admin.users.*') ? 'text-primary-600' : 'text-gray-500' }}">
    <i class="fas fa-users text-lg"></i>
    <span class="text-xs mt-1">Users</span>
</a>
<a href="{{ route('admin.payments.index') }}" class="flex flex-col items-center py-1 px-3 {{ request()->routeIs('admin.payments.*') ? 'text-primary-600' : 'text-gray-500' }}">
    <i class="fas fa-credit-card text-lg"></i>
    <span class="text-xs mt-1">Payments</span>
</a>
<a href="{{ route('admin.settings.index') }}" class="flex flex-col items-center py-1 px-3 {{ request()->routeIs('admin.settings.*') ? 'text-primary-600' : 'text-gray-500' }}">
    <i class="fas fa-cog text-lg"></i>
    <span class="text-xs mt-1">Settings</span>
</a>
