<nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-50" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <div class="w-8 h-8 gradient-bg rounded-lg flex items-center justify-center">
                        <i class="fas fa-link text-white text-sm"></i>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">DropLaunch</span>
                </a>
            </div>

            <div class="hidden sm:flex sm:items-center sm:space-x-4">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 text-sm font-medium">
                            <i class="fas fa-shield-alt mr-1"></i> Admin
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 text-sm font-medium">
                            <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                        </a>
                    @endif
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 text-gray-600 dark:text-gray-300 hover:text-primary-600 px-3 py-2 text-sm font-medium">
                            <span>{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border dark:border-gray-700 py-1 z-50">
                            @if(auth()->user()->profile)
                                <a href="{{ route('profile.show', auth()->user()->profile->username) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" target="_blank">
                                    <i class="fas fa-external-link-alt mr-2"></i> View Profile
                                </a>
                            @endif
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-user-edit mr-2"></i> Edit Profile
                            </a>
                            <hr class="my-1 dark:border-gray-700">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 text-sm font-medium">Login</a>
                    <a href="{{ route('register') }}" class="gradient-bg text-white px-4 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition">Get Started</a>
                @endauth

                <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="text-gray-600 dark:text-gray-300 hover:text-primary-600 p-2 rounded-lg">
                    <i class="fas fa-moon" x-show="!darkMode"></i>
                    <i class="fas fa-sun" x-show="darkMode" x-cloak></i>
                </button>
            </div>

            <div class="sm:hidden flex items-center space-x-2">
                <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" class="text-gray-600 dark:text-gray-300 p-2">
                    <i class="fas fa-moon" x-show="!darkMode"></i>
                    <i class="fas fa-sun" x-show="darkMode" x-cloak></i>
                </button>
                <button @click="mobileOpen = !mobileOpen" class="text-gray-600 dark:text-gray-300 p-2">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="mobileOpen" x-cloak @click.away="mobileOpen = false" class="sm:hidden bg-white dark:bg-gray-800 border-t dark:border-gray-700 pb-3">
        @auth
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Admin Panel</a>
            @else
                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Dashboard</a>
            @endif
            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Edit Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700">Logout</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Login</a>
            <a href="{{ route('register') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Register</a>
        @endauth
    </div>
</nav>
