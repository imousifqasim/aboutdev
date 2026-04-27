<?php

namespace App\Http\Controllers;

use App\Models\Analytic;
use App\Models\ContactMessage;
use App\Models\Link;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicProfileController extends Controller
{
    public function show(string $username): View
    {
        $profile = Profile::where('username', $username)->firstOrFail();
        $user = $profile->user;

        if ($user->is_banned) {
            abort(404);
        }

        $links = $user->links()->where('is_active', true)->orderBy('position')->get();

        Analytic::updateOrCreate(
            ['user_id' => $user->id, 'date' => now()->toDateString()],
            []
        )->increment('profile_views');

        return view('public.profile', compact('profile', 'user', 'links'));
    }

    public function trackClick(Link $link): RedirectResponse
    {
        if (!$link->is_active || $link->user->is_banned) {
            abort(404);
        }

        $link->increment('clicks');

        Analytic::updateOrCreate(
            ['user_id' => $link->user_id, 'date' => now()->toDateString()],
            []
        )->increment('link_clicks');

        return redirect($link->url);
    }

    public function sendMessage(Request $request, string $username): RedirectResponse
    {
        $profile = Profile::where('username', $username)->firstOrFail();
        $user = $profile->user;

        if ($user->is_banned || !$profile->contact_form_enabled || !$user->isPremium()) {
            abort(404);
        }

        $request->validate([
            'sender_name' => ['required', 'string', 'max:100'],
            'sender_email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        ContactMessage::create([
            'user_id' => $user->id,
            'sender_name' => $request->sender_name,
            'sender_email' => $request->sender_email,
            'message' => $request->message,
        ]);

        return back()->with('success', 'Message sent successfully!');
    }
}
