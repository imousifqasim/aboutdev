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
                'dark' => 'body { background: #111827; color: #f3f4f6; } .link-btn { background: #1f2937; border-color: #374151; color: #f3f4f6; } .link-btn:hover { background: #374151; } .spotlight-btn { background: linear-gradient(135deg, #8B5CF6, #6366F1); } .section-title { color: #d1d5db; } .testimonial-card { background: #1f2937; border-color: #374151; } .resume-item { border-color: #374151; } .contact-form input, .contact-form textarea { background: #1f2937; border-color: #374151; color: #f3f4f6; }',
                'gradient' => 'body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; } .link-btn { background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border-color: rgba(255,255,255,0.3); color: white; } .link-btn:hover { background: rgba(255,255,255,0.25); } .spotlight-btn { background: rgba(255,255,255,0.25); backdrop-filter: blur(10px); } .section-title { color: rgba(255,255,255,0.9); } .testimonial-card { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); } .resume-item { border-color: rgba(255,255,255,0.2); } .contact-form input, .contact-form textarea { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white; }',
                'minimal' => 'body { background: #fafafa; } .link-btn { background: transparent; border: 1px solid #e5e7eb; } .link-btn:hover { border-color: #9ca3af; } .spotlight-btn { background: #111827; color: white; } .section-title { color: #374151; } .testimonial-card { background: white; border-color: #e5e7eb; } .resume-item { border-color: #e5e7eb; } .contact-form input, .contact-form textarea { background: white; border-color: #e5e7eb; }',
                'bold' => 'body { background: #dc2626; color: white; } .link-btn { background: rgba(0,0,0,0.2); border-color: rgba(255,255,255,0.3); color: white; } .link-btn:hover { background: rgba(0,0,0,0.3); } .spotlight-btn { background: rgba(0,0,0,0.4); } .section-title { color: rgba(255,255,255,0.9); } .testimonial-card { background: rgba(0,0,0,0.15); border-color: rgba(255,255,255,0.2); } .resume-item { border-color: rgba(255,255,255,0.2); } .contact-form input, .contact-form textarea { background: rgba(0,0,0,0.15); border-color: rgba(255,255,255,0.2); color: white; }',
                'ocean' => 'body { background: linear-gradient(135deg, #06b6d4, #2563eb); color: white; } .link-btn { background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border-color: rgba(255,255,255,0.3); color: white; } .link-btn:hover { background: rgba(255,255,255,0.25); } .spotlight-btn { background: rgba(255,255,255,0.25); backdrop-filter: blur(10px); } .section-title { color: rgba(255,255,255,0.9); } .testimonial-card { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); } .resume-item { border-color: rgba(255,255,255,0.2); } .contact-form input, .contact-form textarea { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white; }',
                'sunset' => 'body { background: linear-gradient(135deg, #fb923c, #ec4899); color: white; } .link-btn { background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border-color: rgba(255,255,255,0.3); color: white; } .link-btn:hover { background: rgba(255,255,255,0.25); } .spotlight-btn { background: rgba(255,255,255,0.25); backdrop-filter: blur(10px); } .section-title { color: rgba(255,255,255,0.9); } .testimonial-card { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); } .resume-item { border-color: rgba(255,255,255,0.2); } .contact-form input, .contact-form textarea { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white; }',
                'forest' => 'body { background: linear-gradient(135deg, #16a34a, #059669); color: white; } .link-btn { background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border-color: rgba(255,255,255,0.3); color: white; } .link-btn:hover { background: rgba(255,255,255,0.25); } .spotlight-btn { background: rgba(255,255,255,0.25); backdrop-filter: blur(10px); } .section-title { color: rgba(255,255,255,0.9); } .testimonial-card { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); } .resume-item { border-color: rgba(255,255,255,0.2); } .contact-form input, .contact-form textarea { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); color: white; }',
                default => 'body { background: #f9fafb; } .link-btn { background: white; border-color: #e5e7eb; } .link-btn:hover { border-color: #9ca3af; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); } .spotlight-btn { background: linear-gradient(135deg, #8B5CF6, #3B82F6); color: white; } .section-title { color: #374151; } .testimonial-card { background: white; border-color: #e5e7eb; } .resume-item { border-color: #e5e7eb; } .contact-form input, .contact-form textarea { background: white; border-color: #e5e7eb; }',
            };
        @endphp
        {!! $themeStyles !!}
        .hero-bg { position: relative; }
        .hero-bg::before { content: ''; position: absolute; inset: 0; background: rgba(0,0,0,0.5); }
        .hero-bg > * { position: relative; z-index: 1; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center {{ $profile->background_image ? '' : 'py-12' }} px-4">
    @if($profile->background_image && $profile->is_premium)
        <!-- Hero Background -->
        <div class="hero-bg w-full min-h-[300px] flex items-center justify-center bg-cover bg-center mb-8 rounded-none" style="background-image: url('{{ Storage::url($profile->background_image) }}');">
            <div class="text-center py-16 px-4">
                @if($profile->image)
                    <img src="{{ Storage::url($profile->image) }}" alt="{{ $user->name }}"
                        class="w-28 h-28 rounded-full object-cover mx-auto mb-4 ring-4 ring-white/50 shadow-xl">
                @else
                    <div class="w-28 h-28 rounded-full mx-auto mb-4 flex items-center justify-center text-4xl font-bold bg-white/20 text-white backdrop-blur-sm">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <h1 class="text-3xl font-bold text-white">{{ $user->name }}</h1>
                @if($profile->bio)
                    <p class="mt-2 text-white/80 max-w-md mx-auto text-sm leading-relaxed">{{ $profile->bio }}</p>
                @endif
                @if($profile->location)
                    <p class="mt-2 text-sm text-white/60"><i class="fas fa-map-marker-alt mr-1"></i> {{ $profile->location }}</p>
                @endif
            </div>
        </div>
    @endif

    <div class="w-full max-w-lg mx-auto {{ $profile->background_image && $profile->is_premium ? '' : '' }}">
        @if(!($profile->background_image && $profile->is_premium))
            <!-- Profile Header (non-hero) -->
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
            </div>
        @endif

        <!-- Social Icons -->
        @if($profile->social_links && count($profile->social_links) > 0)
            <div class="flex justify-center gap-3 {{ ($profile->background_image && $profile->is_premium) ? 'mb-6' : 'mb-6 -mt-4' }}">
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

        <!-- Spotlight Button -->
        @if($profile->spotlight_label && $profile->spotlight_url)
            <div class="mb-6">
                <a href="{{ $profile->spotlight_url }}" target="_blank" rel="noopener"
                    class="spotlight-btn block w-full px-6 py-4 rounded-xl text-center font-bold text-lg transition-all duration-200 hover:opacity-90 hover:-translate-y-0.5 shadow-lg text-white">
                    @if($profile->spotlight_icon)
                        <i class="fas fa-{{ $profile->spotlight_icon }} mr-2"></i>
                    @endif
                    {{ $profile->spotlight_label }}
                </a>
            </div>
        @endif

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

        <!-- Resume / CV -->
        @if($profile->is_premium && $profile->resume)
            @php $resume = $profile->resume; @endphp
            @if(!empty($resume['experience']) || !empty($resume['education']) || !empty($resume['skills']))
                <div class="mt-10">
                    <h2 class="text-lg font-semibold mb-4 text-center section-title opacity-80">Resume</h2>

                    @if(!empty($resume['experience']))
                        <h3 class="text-sm font-semibold mb-3 opacity-70"><i class="fas fa-briefcase mr-1"></i> Experience</h3>
                        <div class="space-y-3 mb-6">
                            @foreach($resume['experience'] as $item)
                                <div class="resume-item border-l-2 pl-4 py-1">
                                    <p class="font-medium text-sm">{{ $item['title'] }}</p>
                                    @if(!empty($item['subtitle']))
                                        <p class="text-xs opacity-70">{{ $item['subtitle'] }}</p>
                                    @endif
                                    @if(!empty($item['period']))
                                        <p class="text-xs opacity-50">{{ $item['period'] }}</p>
                                    @endif
                                    @if(!empty($item['description']))
                                        <p class="text-xs opacity-60 mt-1">{{ $item['description'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($resume['education']))
                        <h3 class="text-sm font-semibold mb-3 opacity-70"><i class="fas fa-graduation-cap mr-1"></i> Education</h3>
                        <div class="space-y-3 mb-6">
                            @foreach($resume['education'] as $item)
                                <div class="resume-item border-l-2 pl-4 py-1">
                                    <p class="font-medium text-sm">{{ $item['title'] }}</p>
                                    @if(!empty($item['subtitle']))
                                        <p class="text-xs opacity-70">{{ $item['subtitle'] }}</p>
                                    @endif
                                    @if(!empty($item['period']))
                                        <p class="text-xs opacity-50">{{ $item['period'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($resume['skills']))
                        <h3 class="text-sm font-semibold mb-3 opacity-70"><i class="fas fa-code mr-1"></i> Skills</h3>
                        <div class="flex flex-wrap gap-2 mb-6">
                            @foreach($resume['skills'] as $skill)
                                <span class="text-xs px-3 py-1.5 rounded-full font-medium
                                    {{ in_array($profile->theme, ['default', 'minimal']) ? 'bg-gray-100 text-gray-700' : 'bg-white/15 text-current' }}">
                                    {{ $skill['title'] }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        @endif

        <!-- Gallery -->
        @if($profile->is_premium && $profile->gallery && count($profile->gallery) > 0)
            <div class="mt-8">
                <h2 class="text-lg font-semibold mb-3 text-center section-title opacity-80">Gallery</h2>
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
                <h2 class="text-lg font-semibold mb-3 text-center section-title opacity-80">Videos</h2>
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

        <!-- Testimonials -->
        @if($profile->is_premium && $profile->testimonials && count($profile->testimonials) > 0)
            <div class="mt-10">
                <h2 class="text-lg font-semibold mb-4 text-center section-title opacity-80">Testimonials</h2>
                <div class="space-y-3">
                    @foreach($profile->testimonials as $testimonial)
                        <div class="testimonial-card border rounded-xl p-5">
                            <p class="text-sm italic opacity-80 mb-3">"{{ $testimonial['text'] }}"</p>
                            <div>
                                <p class="text-sm font-semibold">{{ $testimonial['name'] }}</p>
                                @if(!empty($testimonial['role']))
                                    <p class="text-xs opacity-60">{{ $testimonial['role'] }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Contact Form -->
        @if($profile->is_premium && $profile->contact_form_enabled)
            <div class="mt-10">
                <h2 class="text-lg font-semibold mb-4 text-center section-title opacity-80">Get in Touch</h2>
                @if(session('success'))
                    <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm text-center">
                        {{ session('success') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('profile.contact', $profile->username) }}" class="contact-form space-y-3">
                    @csrf
                    <input type="text" name="sender_name" placeholder="Your Name" required
                        class="w-full px-4 py-3 rounded-xl border text-sm outline-none focus:ring-2 focus:ring-purple-400">
                    <input type="email" name="sender_email" placeholder="Your Email" required
                        class="w-full px-4 py-3 rounded-xl border text-sm outline-none focus:ring-2 focus:ring-purple-400">
                    <textarea name="message" placeholder="Your Message" rows="4" required maxlength="2000"
                        class="w-full px-4 py-3 rounded-xl border text-sm outline-none focus:ring-2 focus:ring-purple-400 resize-none"></textarea>
                    @error('sender_name') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
                    @error('sender_email') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
                    @error('message') <p class="text-red-400 text-xs">{{ $message }}</p> @enderror
                    <button type="submit" class="spotlight-btn w-full px-6 py-3 rounded-xl font-semibold text-white transition hover:opacity-90">
                        <i class="fas fa-paper-plane mr-2"></i> Send Message
                    </button>
                </form>
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
