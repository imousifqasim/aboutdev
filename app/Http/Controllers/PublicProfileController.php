<?php

namespace App\Http\Controllers;

use App\Models\Analytic;
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
}
