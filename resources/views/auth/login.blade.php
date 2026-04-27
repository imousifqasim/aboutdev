@extends('layouts.app')
@section('title', 'Login - DropLaunch')

@section('content')
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold">Welcome Back</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Sign in to your account</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
            @if(session('status'))
                <div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-xl text-sm">
                    {{ session('status') }}
                </div>
            @endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-medium mb-2">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition">
                        @error('email') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium mb-2">Password</label>
                        <input type="password" name="password" id="password" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition">
                        @error('password') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-sm text-primary-600 hover:text-primary-700">Forgot password?</a>
                    </div>
                    <button type="submit" class="w-full gradient-bg text-white py-3 rounded-xl font-semibold hover:opacity-90 transition">
                        Sign In
                    </button>
                </div>
            </form>
            <p class="text-center mt-6 text-sm text-gray-600 dark:text-gray-400">
                Don't have an account? <a href="{{ route('register') }}" class="text-primary-600 hover:text-primary-700 font-medium">Create one</a>
            </p>
        </div>
    </div>
</div>
@endsection
