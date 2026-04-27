<a href="{{ route('dashboard') }}" class="flex flex-col items-center py-1 px-3 {{ request()->routeIs('dashboard') ? 'text-primary-600' : 'text-gray-500' }}">
    <i class="fas fa-tachometer-alt text-lg"></i>
    <span class="text-xs mt-1">Dashboard</span>
</a>
<a href="{{ route('profile.edit') }}" class="flex flex-col items-center py-1 px-3 {{ request()->routeIs('profile.*') ? 'text-primary-600' : 'text-gray-500' }}">
    <i class="fas fa-user text-lg"></i>
    <span class="text-xs mt-1">Profile</span>
</a>
<a href="{{ route('links.index') }}" class="flex flex-col items-center py-1 px-3 {{ request()->routeIs('links.*') ? 'text-primary-600' : 'text-gray-500' }}">
    <i class="fas fa-link text-lg"></i>
    <span class="text-xs mt-1">Links</span>
</a>
<a href="{{ route('payments.index') }}" class="flex flex-col items-center py-1 px-3 {{ request()->routeIs('payments.*') ? 'text-primary-600' : 'text-gray-500' }}">
    <i class="fas fa-crown text-lg"></i>
    <span class="text-xs mt-1">Premium</span>
</a>
<a href="{{ route('messages.index') }}" class="flex flex-col items-center py-1 px-3 relative {{ request()->routeIs('messages.*') ? 'text-primary-600' : 'text-gray-500' }}">
    <i class="fas fa-inbox text-lg"></i>
    <span class="text-xs mt-1">Messages</span>
    @php $unreadMobile = auth()->user()->contactMessages()->where('is_read', false)->count(); @endphp
    @if($unreadMobile > 0)
        <span class="absolute top-0 right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center" style="font-size: 10px;">{{ $unreadMobile }}</span>
    @endif
</a>
