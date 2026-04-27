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
            'username' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:profiles,username,' . $profile->id, 'not_in:admin,dashboard,login,register,logout,forgot-password,reset-password,email,click,home,api,css,js,images,storage'],
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
                $url = $request->input("social_{$platform}");
                if (filter_var($url, FILTER_VALIDATE_URL) && in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'])) {
                    $socialLinks[$platform] = $url;
                }
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

    public function updateSpotlight(Request $request): RedirectResponse
    {
        $request->validate([
            'spotlight_label' => ['nullable', 'string', 'max:50'],
            'spotlight_url' => ['nullable', 'url', 'max:255'],
            'spotlight_icon' => ['nullable', 'string', 'max:50'],
        ]);

        $profile = $request->user()->profile;
        $profile->update($request->only(['spotlight_label', 'spotlight_url', 'spotlight_icon']));

        return back()->with('success', 'Spotlight button updated!');
    }

    public function updateTestimonials(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user->isPremium()) {
            return back()->with('error', 'Testimonials require premium subscription.');
        }

        $request->validate([
            'testimonial_name' => ['required', 'string', 'max:100'],
            'testimonial_role' => ['nullable', 'string', 'max:100'],
            'testimonial_text' => ['required', 'string', 'max:500'],
        ]);

        $profile = $user->profile;
        $testimonials = $profile->testimonials ?? [];
        $testimonials[] = [
            'name' => $request->testimonial_name,
            'role' => $request->testimonial_role ?? '',
            'text' => $request->testimonial_text,
        ];

        $profile->update(['testimonials' => $testimonials]);

        return back()->with('success', 'Testimonial added!');
    }

    public function removeTestimonial(Request $request, int $index): RedirectResponse
    {
        $profile = $request->user()->profile;
        $testimonials = $profile->testimonials ?? [];

        if (isset($testimonials[$index])) {
            array_splice($testimonials, $index, 1);
            $profile->update(['testimonials' => $testimonials]);
        }

        return back()->with('success', 'Testimonial removed.');
    }

    public function updateResume(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user->isPremium()) {
            return back()->with('error', 'Resume feature requires premium subscription.');
        }

        $request->validate([
            'resume_type' => ['required', 'in:education,experience,skill'],
            'resume_title' => ['required', 'string', 'max:255'],
            'resume_subtitle' => ['nullable', 'string', 'max:255'],
            'resume_period' => ['nullable', 'string', 'max:100'],
            'resume_description' => ['nullable', 'string', 'max:500'],
        ]);

        $profile = $user->profile;
        $resume = $profile->resume ?? ['education' => [], 'experience' => [], 'skills' => []];
        $type = $request->resume_type;
        $key = $type === 'skill' ? 'skills' : $type;

        $entry = ['title' => $request->resume_title];
        if ($type !== 'skill') {
            $entry['subtitle'] = $request->resume_subtitle ?? '';
            $entry['period'] = $request->resume_period ?? '';
            $entry['description'] = $request->resume_description ?? '';
        }

        $resume[$key][] = $entry;
        $profile->update(['resume' => $resume]);

        return back()->with('success', ucfirst($type) . ' added!');
    }

    public function removeResumeItem(Request $request, string $type, int $index): RedirectResponse
    {
        $profile = $request->user()->profile;
        $resume = $profile->resume ?? ['education' => [], 'experience' => [], 'skills' => []];
        $key = $type === 'skill' ? 'skills' : $type;

        if (isset($resume[$key][$index])) {
            array_splice($resume[$key], $index, 1);
            $profile->update(['resume' => $resume]);
        }

        return back()->with('success', 'Item removed.');
    }

    public function toggleContactForm(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user->isPremium()) {
            return back()->with('error', 'Contact form requires premium subscription.');
        }

        $profile = $user->profile;
        $profile->update(['contact_form_enabled' => !$profile->contact_form_enabled]);
        $status = $profile->contact_form_enabled ? 'enabled' : 'disabled';

        return back()->with('success', "Contact form {$status}!");
    }

    public function updateBackground(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user->isPremium()) {
            return back()->with('error', 'Background image requires premium subscription.');
        }

        $request->validate([
            'background_image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ]);

        $profile = $user->profile;

        if ($profile->background_image) {
            Storage::disk('public')->delete($profile->background_image);
        }

        $path = $request->file('background_image')->store('backgrounds', 'public');
        $profile->update(['background_image' => $path]);

        return back()->with('success', 'Background image updated!');
    }

    public function removeBackground(Request $request): RedirectResponse
    {
        $profile = $request->user()->profile;

        if ($profile->background_image) {
            Storage::disk('public')->delete($profile->background_image);
            $profile->update(['background_image' => null]);
        }

        return back()->with('success', 'Background image removed.');
    }

    public function emailSignature(Request $request): View
    {
        $user = $request->user()->load('profile');
        return view('user.email-signature', compact('user'));
    }

    public function messages(Request $request): View
    {
        $user = $request->user();
        $messages = $user->contactMessages()->latest()->paginate(20);
        return view('user.messages.index', compact('messages'));
    }

    public function markMessageRead(Request $request, \App\Models\ContactMessage $message): RedirectResponse
    {
        if ($message->user_id !== $request->user()->id) {
            abort(403);
        }

        $message->update(['is_read' => true]);

        return back()->with('success', 'Message marked as read.');
    }

    public function deleteMessage(Request $request, \App\Models\ContactMessage $message): RedirectResponse
    {
        if ($message->user_id !== $request->user()->id) {
            abort(403);
        }

        $message->delete();

        return back()->with('success', 'Message deleted.');
    }
}
