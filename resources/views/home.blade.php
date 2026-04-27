@extends('layouts.app')

@section('title', 'LinkFolio - Create Your Personal Portfolio Page')
@section('description', 'Build a beautiful portfolio page with all your important links. Share your work, social profiles, and more in one place.')

@section('content')
<!-- Hero Section -->
<section class="gradient-bg text-white py-20 lg:py-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                Your Personal Portfolio,<br>
                <span class="text-yellow-300">One Link Away</span>
            </h1>
            <p class="text-xl text-white/80 mb-8 leading-relaxed">
                Create a stunning portfolio page with all your important links, social profiles, and content. Share everything about you in one beautiful page.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('register') }}" class="bg-white text-purple-700 px-8 py-4 rounded-xl text-lg font-semibold hover:bg-gray-100 transition shadow-lg">
                    <i class="fas fa-rocket mr-2"></i> Get Started Free
                </a>
                <a href="#features" class="border-2 border-white/50 text-white px-8 py-4 rounded-xl text-lg font-semibold hover:bg-white/10 transition">
                    <i class="fas fa-arrow-down mr-2"></i> Learn More
                </a>
            </div>
            <p class="mt-6 text-white/60 text-sm">No credit card required. Free plan available.</p>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-20 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold mb-4">Everything You Need</h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg max-w-2xl mx-auto">Powerful features to help you create the perfect portfolio page.</p>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-8 hover:shadow-lg transition">
                <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center mb-5">
                    <i class="fas fa-link text-blue-600 dark:text-blue-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">Custom Links</h3>
                <p class="text-gray-600 dark:text-gray-400">Add unlimited links to your social profiles, websites, projects, and more.</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-8 hover:shadow-lg transition">
                <div class="w-14 h-14 bg-purple-100 dark:bg-purple-900/50 rounded-xl flex items-center justify-center mb-5">
                    <i class="fas fa-palette text-purple-600 dark:text-purple-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">Beautiful Themes</h3>
                <p class="text-gray-600 dark:text-gray-400">Choose from multiple stunning themes to make your page uniquely yours.</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-8 hover:shadow-lg transition">
                <div class="w-14 h-14 bg-green-100 dark:bg-green-900/50 rounded-xl flex items-center justify-center mb-5">
                    <i class="fas fa-chart-line text-green-600 dark:text-green-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">Analytics</h3>
                <p class="text-gray-600 dark:text-gray-400">Track profile views and link clicks to understand your audience.</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-8 hover:shadow-lg transition">
                <div class="w-14 h-14 bg-yellow-100 dark:bg-yellow-900/50 rounded-xl flex items-center justify-center mb-5">
                    <i class="fas fa-images text-yellow-600 dark:text-yellow-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">Gallery & Videos</h3>
                <p class="text-gray-600 dark:text-gray-400">Showcase your work with image galleries and video embeds.</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-8 hover:shadow-lg transition">
                <div class="w-14 h-14 bg-red-100 dark:bg-red-900/50 rounded-xl flex items-center justify-center mb-5">
                    <i class="fas fa-mobile-alt text-red-600 dark:text-red-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">Fully Responsive</h3>
                <p class="text-gray-600 dark:text-gray-400">Your page looks perfect on any device — desktop, tablet, or phone.</p>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-8 hover:shadow-lg transition">
                <div class="w-14 h-14 bg-indigo-100 dark:bg-indigo-900/50 rounded-xl flex items-center justify-center mb-5">
                    <i class="fas fa-search text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">SEO Optimized</h3>
                <p class="text-gray-600 dark:text-gray-400">Built-in SEO with meta tags and Open Graph for better visibility.</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section class="py-20 bg-gray-50 dark:bg-gray-800/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold mb-4">Simple Pricing</h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Start for free, upgrade when you need more.</p>
        </div>
        <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <!-- Free Plan -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition">
                <h3 class="text-2xl font-bold mb-2">Free</h3>
                <div class="text-4xl font-bold mb-6">$0 <span class="text-lg text-gray-500 font-normal">/forever</span></div>
                <ul class="space-y-3 mb-8 text-gray-600 dark:text-gray-400">
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Up to 5 links</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Basic theme</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Profile page</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Basic analytics</li>
                    <li class="text-gray-400"><i class="fas fa-times text-red-400 mr-2"></i> LinkFolio branding</li>
                </ul>
                <a href="{{ route('register') }}" class="block w-full text-center py-3 px-6 border-2 border-primary-600 text-primary-600 rounded-xl font-semibold hover:bg-primary-50 dark:hover:bg-primary-900/20 transition">
                    Get Started
                </a>
            </div>
            <!-- Premium Plan -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 border-2 border-purple-500 hover:shadow-lg transition relative">
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 gradient-bg text-white px-4 py-1 rounded-full text-sm font-semibold">
                    Most Popular
                </div>
                <h3 class="text-2xl font-bold mb-2">Premium</h3>
                <div class="text-4xl font-bold mb-6">$9.99 <span class="text-lg text-gray-500 font-normal">/year</span></div>
                <ul class="space-y-3 mb-8 text-gray-600 dark:text-gray-400">
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Unlimited links</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> All premium themes</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Remove branding</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Gallery & video sections</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Detailed analytics</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Custom domain</li>
                </ul>
                <a href="{{ route('register') }}" class="block w-full text-center py-3 px-6 gradient-bg text-white rounded-xl font-semibold hover:opacity-90 transition">
                    Upgrade Now
                </a>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-20 gradient-bg">
    <div class="max-w-4xl mx-auto px-4 text-center text-white">
        <h2 class="text-3xl sm:text-4xl font-bold mb-4">Ready to Build Your Page?</h2>
        <p class="text-xl text-white/80 mb-8">Join thousands of creators who use LinkFolio to share their work.</p>
        <a href="{{ route('register') }}" class="bg-white text-purple-700 px-8 py-4 rounded-xl text-lg font-semibold hover:bg-gray-100 transition shadow-lg inline-block">
            <i class="fas fa-rocket mr-2"></i> Create Your Page Now
        </a>
    </div>
</section>
@endsection
