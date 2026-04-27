<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user()->load('profile');
        return view('user.profile.edit', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->profile;

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:profiles,username,' . $profile->id],
            'bio' => ['nullable', 'string', 'max:1000'],
            'location' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        $user->update(['name' => $request->name]);

        $data = $request->only([
            'username', 'bio', 'location', 'company', 'website',
            'meta_title', 'meta_description',
        ]);
        $data['username'] = strtolower($data['username']);

        if ($request->hasFile('image')) {
            if ($profile->image) {
                Storage::disk('public')->delete($profile->image);
            }
            $data['image'] = $request->file('image')->store('profiles', 'public');
        }

        $socialLinks = [];
        $platforms = ['twitter', 'instagram', 'facebook', 'linkedin', 'github', 'youtube', 'tiktok'];
        foreach ($platforms as $platform) {
            if ($request->filled("social_{$platform}")) {
                $socialLinks[$platform] = $request->input("social_{$platform}");
            }
        }
        $data['social_links'] = $socialLinks;

        $profile->update($data);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updateTheme(Request $request): RedirectResponse
    {
        $request->validate([
            'theme' => ['required', 'string', 'in:default,dark,gradient,minimal,bold,ocean,sunset,forest'],
        ]);

        $user = $request->user();
        $profile = $user->profile;

        $premiumThemes = ['gradient', 'bold', 'ocean', 'sunset', 'forest'];
        if (in_array($request->theme, $premiumThemes) && !$user->isPremium()) {
            return back()->with('error', 'This theme requires a premium subscription.');
        }

        $profile->update(['theme' => $request->theme]);

        return back()->with('success', 'Theme updated successfully!');
    }

    public function updateGallery(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user->isPremium()) {
            return back()->with('error', 'Gallery feature requires premium subscription.');
        }

        $request->validate([
            'gallery_images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $profile = $user->profile;
        $gallery = $profile->gallery ?? [];

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $gallery[] = $image->store('gallery', 'public');
            }
        }

        $profile->update(['gallery' => $gallery]);

        return back()->with('success', 'Gallery updated successfully!');
    }

    public function removeGalleryImage(Request $request, int $index): RedirectResponse
    {
        $profile = $request->user()->profile;
        $gallery = $profile->gallery ?? [];

        if (isset($gallery[$index])) {
            Storage::disk('public')->delete($gallery[$index]);
            array_splice($gallery, $index, 1);
            $profile->update(['gallery' => $gallery]);
        }

        return back()->with('success', 'Image removed.');
    }

    public function updateVideos(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user->isPremium()) {
            return back()->with('error', 'Video feature requires premium subscription.');
        }

        $request->validate([
            'video_url' => ['required', 'url'],
            'video_title' => ['nullable', 'string', 'max:255'],
        ]);

        $profile = $user->profile;
        $videos = $profile->videos ?? [];
        $videos[] = [
            'url' => $request->video_url,
            'title' => $request->video_title ?? '',
        ];

        $profile->update(['videos' => $videos]);

        return back()->with('success', 'Video added successfully!');
    }

    public function removeVideo(Request $request, int $index): RedirectResponse
    {
        $profile = $request->user()->profile;
        $videos = $profile->videos ?? [];

        if (isset($videos[$index])) {
            array_splice($videos, $index, 1);
            $profile->update(['videos' => $videos]);
        }

        return back()->with('success', 'Video removed.');
    }
}
