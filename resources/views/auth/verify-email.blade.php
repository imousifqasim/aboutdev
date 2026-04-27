@extends('layouts.app')
@section('title', 'Verify Email - LinkFolio')

@section('content')
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md text-center">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-envelope text-blue-600 dark:text-blue-400 text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold mb-3">Verify Your Email</h1>
            <p class="text-gray-600 dark:text-gray-400 mb-6">We've sent a verification link to your email address. Please check your inbox and click the link to verify.</p>

            @if(session('message'))
                <div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-xl text-sm">
                    {{ session('message') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="gradient-bg text-white px-6 py-3 rounded-xl font-semibold hover:opacity-90 transition">
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
