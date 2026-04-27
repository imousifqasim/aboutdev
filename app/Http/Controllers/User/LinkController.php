<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Link;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LinkController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user()->load('profile');
        $links = $request->user()->links()->orderBy('position')->get();
        return view('user.links.index', compact('links', 'user'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user->isPremium() && $user->links()->count() >= 5) {
            return back()->with('error', 'Free plan allows up to 5 links. Upgrade to Premium for unlimited links.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:500'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]);

        $maxPosition = $user->links()->max('position') ?? 0;

        $user->links()->create([
            'title' => $request->title,
            'url' => $request->url,
            'icon' => $request->icon,
            'position' => $maxPosition + 1,
        ]);

        return back()->with('success', 'Link added successfully!');
    }

    public function update(Request $request, Link $link): RedirectResponse
    {
        if ($link->user_id !== $request->user()->id) {
            abort(403);
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:500'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]);

        $link->update($request->only('title', 'url', 'icon'));

        return back()->with('success', 'Link updated successfully!');
    }

    public function destroy(Request $request, Link $link): RedirectResponse
    {
        if ($link->user_id !== $request->user()->id) {
            abort(403);
        }

        $link->delete();

        return back()->with('success', 'Link deleted successfully!');
    }

    public function toggleActive(Request $request, Link $link): RedirectResponse
    {
        if ($link->user_id !== $request->user()->id) {
            abort(403);
        }

        $link->update(['is_active' => !$link->is_active]);

        return back()->with('success', 'Link status updated!');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:links,id'],
        ]);

        foreach ($request->order as $position => $id) {
            Link::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->update(['position' => $position]);
        }

        return back()->with('success', 'Links reordered!');
    }
}
