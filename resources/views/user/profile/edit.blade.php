@extends('layouts.dashboard')

@section('sidebar')
    @include('user.partials.sidebar')
@endsection

@section('mobile-nav')
    @include('user.partials.mobile-nav')
@endsection

@section('dashboard-content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Edit Profile</h1>

    <!-- Profile Form -->
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Basic Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium mb-2">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-primary-500 outline-none">
                    @error('name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Username</label>
                    <input type="text" name="username" value="{{ old('username', $user->profile->username) }}" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-primary-500 outline-none">
                    @error('username') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2">Bio</label>
                    <textarea name="bio" rows="3" maxlength="1000"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-primary-500 outline-none">{{ old('bio', $user->profile->bio) }}</textarea>
                    @error('bio') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Location</label>
                    <input type="text" name="location" value="{{ old('location', $user->profile->location) }}"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Company</label>
                    <input type="text" name="company" value="{{ old('company', $user->profile->company) }}"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Website</label>
                    <input type="url" name="website" value="{{ old('website', $user->profile->website) }}"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Profile Image</label>
                    <div class="flex items-center gap-4">
                        @if($user->profile->image)
                            <img src="{{ Storage::url($user->profile->image) }}" alt="" class="w-16 h-16 rounded-full object-cover">
                        @else
                            <div class="w-16 h-16 gradient-bg rounded-full flex items-center justify-center text-white text-xl font-bold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <input type="file" name="image" accept="image/*"
                            class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    </div>
                    @error('image') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Social Links -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">Social Links</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @php $socialLinks = $user->profile->social_links ?? []; @endphp
                @foreach(['twitter' => 'Twitter', 'instagram' => 'Instagram', 'facebook' => 'Facebook', 'linkedin' => 'LinkedIn', 'github' => 'GitHub', 'youtube' => 'YouTube', 'tiktok' => 'TikTok'] as $key => $label)
                    <div>
                        <label class="block text-sm font-medium mb-2"><i class="fab fa-{{ $key }} mr-1"></i> {{ $label }}</label>
                        <input type="url" name="social_{{ $key }}" value="{{ old("social_{$key}", $socialLinks[$key] ?? '') }}" placeholder="https://"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-primary-500 outline-none text-sm">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- SEO Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">SEO Settings</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Meta Title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $user->profile->meta_title) }}" maxlength="255"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Meta Description</label>
                    <textarea name="meta_description" rows="2" maxlength="500"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-primary-500 outline-none">{{ old('meta_description', $user->profile->meta_description) }}</textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="gradient-bg text-white px-8 py-3 rounded-xl font-semibold hover:opacity-90 transition">
            <i class="fas fa-save mr-2"></i> Save Changes
        </button>
    </form>

    <!-- Theme Selection -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mt-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Theme</h2>
        <form method="POST" action="{{ route('profile.theme') }}">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                @foreach(['default' => ['Default', 'bg-white border-gray-200', false], 'dark' => ['Dark', 'bg-gray-900 border-gray-700', false], 'gradient' => ['Gradient', 'bg-gradient-to-br from-purple-500 to-blue-500', true], 'minimal' => ['Minimal', 'bg-gray-50 border-gray-100', false], 'bold' => ['Bold', 'bg-red-600', true], 'ocean' => ['Ocean', 'bg-gradient-to-br from-cyan-500 to-blue-600', true], 'sunset' => ['Sunset', 'bg-gradient-to-br from-orange-400 to-pink-500', true], 'forest' => ['Forest', 'bg-gradient-to-br from-green-600 to-emerald-500', true]] as $theme => [$label, $classes, $premium])
                    <label class="relative cursor-pointer">
                        <input type="radio" name="theme" value="{{ $theme }}" class="peer hidden" {{ $user->profile->theme === $theme ? 'checked' : '' }}>
                        <div class="h-20 rounded-lg border-2 {{ $classes }} peer-checked:ring-2 peer-checked:ring-primary-500 peer-checked:ring-offset-2 dark:peer-checked:ring-offset-gray-800 flex items-end p-2 transition">
                            <span class="text-xs font-medium {{ in_array($theme, ['dark', 'gradient', 'bold', 'ocean', 'sunset', 'forest']) ? 'text-white' : 'text-gray-700' }}">
                                {{ $label }}
                                @if($premium) <i class="fas fa-crown text-yellow-400 ml-1"></i> @endif
                            </span>
                        </div>
                    </label>
                @endforeach
            </div>
            <button type="submit" class="bg-gray-800 dark:bg-gray-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition">
                Apply Theme
            </button>
        </form>
    </div>

    <!-- Gallery (Premium) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mt-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Gallery @if(!$user->isPremium()) <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full ml-2"><i class="fas fa-crown"></i> Premium</span> @endif</h2>
        </div>
        @if($user->isPremium())
            @if($user->profile->gallery && count($user->profile->gallery) > 0)
                <div class="grid grid-cols-3 gap-3 mb-4">
                    @foreach($user->profile->gallery as $index => $image)
                        <div class="relative group">
                            <img src="{{ Storage::url($image) }}" alt="" class="w-full h-24 object-cover rounded-lg">
                            <form method="POST" action="{{ route('profile.gallery.remove', $index) }}" class="absolute top-1 right-1 hidden group-hover:block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white w-6 h-6 rounded-full text-xs hover:bg-red-600"><i class="fas fa-times"></i></button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('profile.gallery') }}" enctype="multipart/form-data" class="flex items-center gap-3">
                @csrf
                <input type="file" name="gallery_images[]" multiple accept="image/*" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700">
                <button type="submit" class="bg-gray-800 dark:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm">Upload</button>
            </form>
        @else
            <p class="text-gray-500 text-sm">Upgrade to Premium to add images to your gallery.</p>
        @endif
    </div>

    <!-- Videos (Premium) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mt-6 mb-20 lg:mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Videos @if(!$user->isPremium()) <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full ml-2"><i class="fas fa-crown"></i> Premium</span> @endif</h2>
        </div>
        @if($user->isPremium())
            @if($user->profile->videos && count($user->profile->videos) > 0)
                <div class="space-y-2 mb-4">
                    @foreach($user->profile->videos as $index => $video)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="text-sm font-medium">{{ $video['title'] ?: 'Untitled' }}</p>
                                <p class="text-xs text-gray-500 truncate max-w-xs">{{ $video['url'] }}</p>
                            </div>
                            <form method="POST" action="{{ route('profile.videos.remove', $index) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('profile.videos') }}" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <input type="text" name="video_title" placeholder="Video title" class="flex-1 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm outline-none">
                <input type="url" name="video_url" placeholder="YouTube/Vimeo URL" required class="flex-1 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm outline-none">
                <button type="submit" class="bg-gray-800 dark:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm whitespace-nowrap">Add Video</button>
            </form>
        @else
            <p class="text-gray-500 text-sm">Upgrade to Premium to embed videos on your profile.</p>
        @endif
    </div>
</div>
@endsection
