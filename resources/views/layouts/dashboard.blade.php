@extends('layouts.app')

@section('content')
<div class="flex min-h-[calc(100vh-4rem)]">
    <!-- Sidebar -->
    <aside class="hidden lg:flex lg:flex-col w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 p-4" x-data="{ collapsed: false }">
        <div class="space-y-1">
            @yield('sidebar')
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 p-4 sm:p-6 lg:p-8">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 text-green-700 dark:text-green-400 rounded-lg flex items-center" x-data="{ show: true }" x-show="show">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
                <button @click="show = false" class="ml-auto text-green-700 dark:text-green-400"><i class="fas fa-times"></i></button>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 text-red-700 dark:text-red-400 rounded-lg flex items-center" x-data="{ show: true }" x-show="show">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
                <button @click="show = false" class="ml-auto text-red-700 dark:text-red-400"><i class="fas fa-times"></i></button>
            </div>
        @endif
        @yield('dashboard-content')
    </div>
</div>

<!-- Mobile Bottom Nav -->
<div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 z-50">
    <div class="flex justify-around py-2">
        @yield('mobile-nav')
    </div>
</div>
@endsection
