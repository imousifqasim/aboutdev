@extends('layouts.dashboard')

@section('sidebar')
    @include('admin.partials.sidebar')
@endsection

@section('mobile-nav')
    @include('admin.partials.mobile-nav')
@endsection

@section('dashboard-content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Site Settings</h1>

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        <!-- General Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">General</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Site Name</label>
                    <input type="text" name="site_name" value="{{ $settings['site_name'] }}" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 outline-none">
                    @error('site_name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Contact Email</label>
                    <input type="email" name="contact_email" value="{{ $settings['contact_email'] }}" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Footer Text</label>
                    <input type="text" name="footer_text" value="{{ $settings['footer_text'] }}"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 outline-none">
                </div>
            </div>
        </div>

        <!-- SEO Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">SEO & Branding</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Site Description</label>
                    <textarea name="site_description" rows="2"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 outline-none">{{ $settings['site_description'] }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Keywords</label>
                    <input type="text" name="site_keywords" value="{{ $settings['site_keywords'] }}"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 outline-none">
                    <p class="text-xs text-gray-500 mt-1">Comma-separated keywords</p>
                </div>
            </div>
        </div>

        <!-- Pricing Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Pricing</h2>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Premium Price</label>
                    <input type="number" name="premium_price" value="{{ $settings['premium_price'] }}" step="0.01" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Currency</label>
                    <input type="text" name="premium_currency" value="{{ $settings['premium_currency'] }}" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 outline-none">
                </div>
            </div>
        </div>

        <button type="submit" class="gradient-bg text-white px-8 py-3 rounded-xl font-semibold hover:opacity-90 transition mb-20 lg:mb-0">
            <i class="fas fa-save mr-2"></i> Save Settings
        </button>
    </form>
</div>
@endsection
