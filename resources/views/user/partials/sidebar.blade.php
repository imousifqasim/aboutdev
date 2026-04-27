<div class="mb-6">
    <div class="flex items-center space-x-3 px-3 py-2">
        @if(auth()->user()->profile && auth()->user()->profile->image)
            <img src="{{ Storage::url(auth()->user()->profile->image) }}" alt="" class="w-10 h-10 rounded-full object-cover">
        @else
            <div class="w-10 h-10 gradient-bg rounded-full flex items-center justify-center text-white font-bold">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
        @endif
        <div class="min-w-0">
            <p class="font-semibold text-sm truncate">{{ auth()->user()->name }}</p>
            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->profile->username ?? '' }}</p>
        </div>
    </div>
</div>

<a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
    <i class="fas fa-tachometer-alt w-5"></i>
    <span>Dashboard</span>
</a>
<a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('profile.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
    <i class="fas fa-user w-5"></i>
    <span>Edit Profile</span>
</a>
<a href="{{ route('links.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('links.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
    <i class="fas fa-link w-5"></i>
    <span>Links</span>
</a>
<a href="{{ route('payments.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('payments.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
    <i class="fas fa-credit-card w-5"></i>
    <span>Subscription</span>
</a>

<hr class="my-4 dark:border-gray-700">

@if(auth()->user()->profile)
    <a href="{{ route('profile.show', auth()->user()->profile->username) }}" target="_blank" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
        <i class="fas fa-external-link-alt w-5"></i>
        <span>View Public Page</span>
    </a>
@endif
