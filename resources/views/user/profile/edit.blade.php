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
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mt-6 mb-6">
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

    <!-- Spotlight/CTA Button -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mt-6 mb-6">
        <h2 class="text-lg font-semibold mb-4"><i class="fas fa-star text-yellow-500 mr-2"></i>Spotlight Button</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Add a prominent call-to-action button to your profile (e.g., "Hire Me", "Visit My Website").</p>
        <form method="POST" action="{{ route('profile.spotlight') }}">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Button Label</label>
                    <input type="text" name="spotlight_label" value="{{ old('spotlight_label', $user->profile->spotlight_label) }}" placeholder="e.g., Hire Me" maxlength="50"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-primary-500 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Button URL</label>
                    <input type="url" name="spotlight_url" value="{{ old('spotlight_url', $user->profile->spotlight_url) }}" placeholder="https://..."
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-primary-500 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Icon</label>
                    <select name="spotlight_icon" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-primary-500 outline-none text-sm">
                        <option value="">None</option>
                        <option value="briefcase" {{ ($user->profile->spotlight_icon ?? '') === 'briefcase' ? 'selected' : '' }}>Briefcase</option>
                        <option value="globe" {{ ($user->profile->spotlight_icon ?? '') === 'globe' ? 'selected' : '' }}>Globe</option>
                        <option value="envelope" {{ ($user->profile->spotlight_icon ?? '') === 'envelope' ? 'selected' : '' }}>Email</option>
                        <option value="calendar" {{ ($user->profile->spotlight_icon ?? '') === 'calendar' ? 'selected' : '' }}>Calendar</option>
                        <option value="shopping-cart" {{ ($user->profile->spotlight_icon ?? '') === 'shopping-cart' ? 'selected' : '' }}>Shop</option>
                        <option value="download" {{ ($user->profile->spotlight_icon ?? '') === 'download' ? 'selected' : '' }}>Download</option>
                        <option value="phone" {{ ($user->profile->spotlight_icon ?? '') === 'phone' ? 'selected' : '' }}>Phone</option>
                        <option value="heart" {{ ($user->profile->spotlight_icon ?? '') === 'heart' ? 'selected' : '' }}>Heart</option>
                        <option value="star" {{ ($user->profile->spotlight_icon ?? '') === 'star' ? 'selected' : '' }}>Star</option>
                        <option value="rocket" {{ ($user->profile->spotlight_icon ?? '') === 'rocket' ? 'selected' : '' }}>Rocket</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="mt-4 bg-gray-800 dark:bg-gray-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition">
                Save Spotlight
            </button>
        </form>
    </div>

    <!-- Background Image (Premium) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mt-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Background Image @if(!$user->isPremium()) <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full ml-2"><i class="fas fa-crown"></i> Premium</span> @endif</h2>
        @if($user->isPremium())
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Add a full-page hero background image to your public profile (about.me style).</p>
            @if($user->profile->background_image)
                <div class="relative mb-4">
                    <img src="{{ Storage::url($user->profile->background_image) }}" alt="" class="w-full h-40 object-cover rounded-lg">
                    <form method="POST" action="{{ route('profile.background.remove') }}" class="absolute top-2 right-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white w-8 h-8 rounded-full text-sm hover:bg-red-600"><i class="fas fa-times"></i></button>
                    </form>
                </div>
            @endif
            <form method="POST" action="{{ route('profile.background') }}" enctype="multipart/form-data" class="flex items-center gap-3">
                @csrf
                <input type="file" name="background_image" accept="image/*" required class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700">
                <button type="submit" class="bg-gray-800 dark:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm">Upload</button>
            </form>
        @else
            <p class="text-gray-500 text-sm">Upgrade to Premium to add a full-page hero background.</p>
        @endif
    </div>

    <!-- Testimonials (Premium) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mt-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Testimonials @if(!$user->isPremium()) <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full ml-2"><i class="fas fa-crown"></i> Premium</span> @endif</h2>
        @if($user->isPremium())
            @if($user->profile->testimonials && count($user->profile->testimonials) > 0)
                <div class="space-y-3 mb-4">
                    @foreach($user->profile->testimonials as $index => $testimonial)
                        <div class="flex items-start justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="text-sm italic text-gray-600 dark:text-gray-300">"{{ $testimonial['text'] }}"</p>
                                <p class="text-sm font-semibold mt-2">{{ $testimonial['name'] }}</p>
                                @if(!empty($testimonial['role']))
                                    <p class="text-xs text-gray-500">{{ $testimonial['role'] }}</p>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('profile.testimonials.remove', $index) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm ml-3"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('profile.testimonials') }}" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <input type="text" name="testimonial_name" placeholder="Person's name" required class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm outline-none">
                    <input type="text" name="testimonial_role" placeholder="Role / Company (optional)" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm outline-none">
                </div>
                <textarea name="testimonial_text" placeholder="What did they say about you?" required maxlength="500" rows="2"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm outline-none"></textarea>
                <button type="submit" class="bg-gray-800 dark:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm">Add Testimonial</button>
            </form>
        @else
            <p class="text-gray-500 text-sm">Upgrade to Premium to add testimonials to your profile.</p>
        @endif
    </div>

    <!-- Resume / CV (Premium) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mt-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Resume / CV @if(!$user->isPremium()) <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full ml-2"><i class="fas fa-crown"></i> Premium</span> @endif</h2>
        @if($user->isPremium())
            @php $resume = $user->profile->resume ?? ['education' => [], 'experience' => [], 'skills' => []]; @endphp

            <!-- Experience -->
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 mt-4"><i class="fas fa-briefcase mr-1"></i> Experience</h3>
            @foreach($resume['experience'] ?? [] as $index => $item)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg mb-2">
                    <div>
                        <p class="text-sm font-medium">{{ $item['title'] }}</p>
                        <p class="text-xs text-gray-500">{{ $item['subtitle'] ?? '' }} {{ !empty($item['period']) ? '| ' . $item['period'] : '' }}</p>
                    </div>
                    <form method="POST" action="{{ route('profile.resume.remove', ['type' => 'experience', 'index' => $index]) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            @endforeach

            <!-- Education -->
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 mt-4"><i class="fas fa-graduation-cap mr-1"></i> Education</h3>
            @foreach($resume['education'] ?? [] as $index => $item)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg mb-2">
                    <div>
                        <p class="text-sm font-medium">{{ $item['title'] }}</p>
                        <p class="text-xs text-gray-500">{{ $item['subtitle'] ?? '' }} {{ !empty($item['period']) ? '| ' . $item['period'] : '' }}</p>
                    </div>
                    <form method="POST" action="{{ route('profile.resume.remove', ['type' => 'education', 'index' => $index]) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            @endforeach

            <!-- Skills -->
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 mt-4"><i class="fas fa-code mr-1"></i> Skills</h3>
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($resume['skills'] ?? [] as $index => $item)
                    <span class="inline-flex items-center gap-1 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full text-sm">
                        {{ $item['title'] }}
                        <form method="POST" action="{{ route('profile.resume.remove', ['type' => 'skill', 'index' => $index]) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 ml-1"><i class="fas fa-times text-xs"></i></button>
                        </form>
                    </span>
                @endforeach
            </div>

            <!-- Add Resume Item -->
            <form method="POST" action="{{ route('profile.resume') }}" class="space-y-3 mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg" x-data="{ type: 'experience' }">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <select name="resume_type" x-model="type" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm outline-none">
                            <option value="experience">Experience</option>
                            <option value="education">Education</option>
                            <option value="skill">Skill</option>
                        </select>
                    </div>
                    <div>
                        <input type="text" name="resume_title" required placeholder="Title / Skill name" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm outline-none">
                    </div>
                </div>
                <div x-show="type !== 'skill'" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <input type="text" name="resume_subtitle" placeholder="Company / Institution" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm outline-none">
                    <input type="text" name="resume_period" placeholder="Period (e.g., 2020 - Present)" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm outline-none">
                </div>
                <div x-show="type !== 'skill'">
                    <textarea name="resume_description" placeholder="Description (optional)" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm outline-none"></textarea>
                </div>
                <button type="submit" class="bg-gray-800 dark:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm">Add Item</button>
            </form>
        @else
            <p class="text-gray-500 text-sm">Upgrade to Premium to add your resume/CV to your profile.</p>
        @endif
    </div>

    <!-- Contact Form Toggle (Premium) -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mt-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold">Contact Form @if(!$user->isPremium()) <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full ml-2"><i class="fas fa-crown"></i> Premium</span> @endif</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Let visitors send you messages directly from your profile page.</p>
            </div>
            @if($user->isPremium())
                <form method="POST" action="{{ route('profile.contact-form') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $user->profile->contact_form_enabled ? 'bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400' }}">
                        <i class="fas fa-{{ $user->profile->contact_form_enabled ? 'toggle-on' : 'toggle-off' }} mr-1"></i>
                        {{ $user->profile->contact_form_enabled ? 'Enabled' : 'Disabled' }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- QR Code -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mt-6 mb-20 lg:mb-6">
        <h2 class="text-lg font-semibold mb-4"><i class="fas fa-qrcode mr-2"></i>QR Code</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Share your profile with a QR code. Anyone can scan it to visit your page.</p>
        <div class="flex flex-col sm:flex-row items-center gap-6">
            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(url('/' . $user->profile->username)) }}" alt="QR Code" class="w-48 h-48">
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Your profile URL:</p>
                <p class="text-sm font-mono bg-gray-50 dark:bg-gray-700 px-4 py-2 rounded-lg">{{ url('/' . $user->profile->username) }}</p>
                <a href="https://api.qrserver.com/v1/create-qr-code/?size=400x400&format=png&data={{ urlencode(url('/' . $user->profile->username)) }}" download="droplaunch-qr.png" class="inline-block mt-3 bg-gray-800 dark:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm hover:opacity-90 transition">
                    <i class="fas fa-download mr-1"></i> Download QR
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
