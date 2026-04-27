<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $profile->meta_title ?: $user->name . ' - DropLaunch' }}</title>
    <meta name="description" content="{{ $profile->meta_description ?: $profile->bio }}">
    <meta property="og:title" content="{{ $profile->meta_title ?: $user->name }}">
    <meta property="og:description" content="{{ $profile->meta_description ?: $profile->bio }}">
    <meta property="og:type" content="profile">
    <meta property="og:url" content="{{ url('/' . $profile->username) }}">
    @if($profile->og_image || $profile->image)
        <meta property="og:image" content="{{ Storage::url($profile->og_image ?: $profile->image) }}">
    @endif
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $profile->meta_title ?: $user->name }}">
    <meta name="twitter:description" content="{{ $profile->meta_description ?: $profile->bio }}">
    <link rel="canonical" href="{{ url('/' . $profile->username) }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>tailwind.config = { darkMode: 'class' }</script>
    <style>
        [x-cloak] { display: none !important; }
        @php
            $themeStyles = match($profile->theme) {
                'dark' => 'body { background: #111827; color: #f3f4f6; } .link-btn { background: #1f2937; border-color: #374151; color: #f3f4f6; } .link-btn:hover { background: #374151; }',
                'gradient' => 'body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; } .link-btn { background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border-color: rgba(255,255,255,0.3); color: white; } .link-btn:hover { background: rgba(255,255,255,0.25); }',
                'minimal' => 'body { background: #fafafa; } .link-btn { background: transparent; border: 1px solid #e5e7eb; } .link-btn:hover { border-color: #9ca3af; }',
                'bold' => 'body { background: #dc2626; color: white; } .link-btn { background: rgba(0,0,0,0.2); border-color: rgba(255,255,255,0.3); color: white; } .link-btn:hover { background: rgba(0,0,0,0.3); }',
                'ocean' => 'body { background: linear-gradient(135deg, #06b6d4, #2563eb); color: white; } .link-btn { background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border-color: rgba(255,255,255,0.3); color: white; } .link-btn:hover { background: rgba(255,255,255,0.25); }',
                'sunset' => 'body { background: linear-gradient(135deg, #fb923c, #ec4899); color: white; } .link-btn { background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border-color: rgba(255,255,255,0.3); color: white; } .link-btn:hover { background: rgba(255,255,255,0.25); }',
                'forest' => 'body { background: linear-gradient(135deg, #16a34a, #059669); color: white; } .link-btn { background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border-color: rgba(255,255,255,0.3); color: white; } .link-btn:hover { background: rgba(255,255,255,0.25); }',
                default => 'body { background: #f9fafb; } .link-btn { background: white; border-color: #e5e7eb; } .link-btn:hover { border-color: #9ca3af; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }',
            };
        @endphp
        {!! $themeStyles !!}
    </style>
</head>
<body class="min-h-screen flex flex-col items-center py-12 px-4">
    <div class="w-full max-w-lg mx-auto">
        <!-- Profile Header -->
        <div class="text-center mb-8">
            @if($profile->image)
                <img src="{{ Storage::url($profile->image) }}" alt="{{ $user->name }}"
                    class="w-24 h-24 rounded-full object-cover mx-auto mb-4 ring-4 ring-white/30 shadow-lg">
            @else
                <div class="w-24 h-24 rounded-full mx-auto mb-4 flex items-center justify-center text-3xl font-bold
                    {{ in_array($profile->theme, ['default', 'minimal']) ? 'bg-gradient-to-br from-purple-500 to-blue-500 text-white' : 'bg-white/20 text-current' }}">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
            <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
            @if($profile->bio)
                <p class="mt-2 opacity-80 max-w-sm mx-auto text-sm leading-relaxed">{{ $profile->bio }}</p>
            @endif
            @if($profile->location)
                <p class="mt-2 text-sm opacity-60"><i class="fas fa-map-marker-alt mr-1"></i> {{ $profile->location }}</p>
            @endif

            <!-- Social Icons -->
            @if($profile->social_links && count($profile->social_links) > 0)
                <div class="flex justify-center gap-3 mt-4">
                    @foreach($profile->social_links as $platform => $url)
                        @if($url)
                            <a href="{{ $url }}" target="_blank" rel="noopener"
                                class="w-10 h-10 rounded-full flex items-center justify-center transition
                                {{ in_array($profile->theme, ['default', 'minimal']) ? 'bg-gray-100 hover:bg-gray-200 text-gray-600' : 'bg-white/15 hover:bg-white/25' }}">
                                <i class="fab fa-{{ $platform }}"></i>
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Links -->
        <div class="space-y-3">
            @foreach($links as $link)
                <a href="{{ route('link.click', $link) }}" target="_blank"
                    class="link-btn block w-full px-6 py-4 rounded-xl border text-center font-medium transition-all duration-200 hover:-translate-y-0.5">
                    @if($link->icon)
                        <i class="fa{{ in_array($link->icon, ['github', 'linkedin', 'twitter', 'instagram', 'youtube']) ? 'b' : 's' }} fa-{{ $link->icon }} mr-2"></i>
                    @endif
                    {{ $link->title }}
                </a>
            @endforeach
        </div>

        <!-- Gallery -->
        @if($profile->is_premium && $profile->gallery && count($profile->gallery) > 0)
            <div class="mt-8">
                <h2 class="text-lg font-semibold mb-3 text-center opacity-80">Gallery</h2>
                <div class="grid grid-cols-3 gap-2">
                    @foreach($profile->gallery as $image)
                        <img src="{{ Storage::url($image) }}" alt="" class="w-full h-24 object-cover rounded-lg">
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Videos -->
        @if($profile->is_premium && $profile->videos && count($profile->videos) > 0)
            <div class="mt-8">
                <h2 class="text-lg font-semibold mb-3 text-center opacity-80">Videos</h2>
                <div class="space-y-3">
                    @foreach($profile->videos as $video)
                        <div class="rounded-lg overflow-hidden">
                            @php
                                $videoUrl = $video['url'];
                                $embedUrl = '';
                                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $videoUrl, $matches)) {
                                    $embedUrl = 'https://www.youtube.com/embed/' . $matches[1];
                                } elseif (preg_match('/vimeo\.com\/(\d+)/', $videoUrl, $matches)) {
                                    $embedUrl = 'https://player.vimeo.com/video/' . $matches[1];
                                }
                            @endphp
                            @if($embedUrl)
                                <iframe src="{{ $embedUrl }}" class="w-full aspect-video rounded-lg" frameborder="0" allowfullscreen></iframe>
                            @else
                                <a href="{{ $videoUrl }}" target="_blank" class="link-btn block w-full px-6 py-3 rounded-xl border text-center text-sm">
                                    <i class="fas fa-play mr-2"></i> {{ $video['title'] ?: 'Watch Video' }}
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Branding -->
        @if($profile->show_branding)
            <div class="mt-12 text-center">
                <a href="{{ url('/') }}" class="text-xs opacity-40 hover:opacity-60 transition">
                    Made with DropLaunch
                </a>
            </div>
        @endif
    </div>
</body>
</html>
