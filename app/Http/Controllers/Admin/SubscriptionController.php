<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Subscription::with('user');

        if ($request->filled('plan')) {
            $query->where('plan', $request->plan);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $subscriptions = $query->latest()->paginate(20);

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function togglePremium(User $user): RedirectResponse
    {
        $profile = $user->profile;

        if ($profile->is_premium) {
            $profile->update(['is_premium' => false, 'show_branding' => true]);
            Subscription::where('user_id', $user->id)->where('is_active', true)->update(['is_active' => false]);
            return back()->with('success', 'Premium removed from user.');
        }

        $profile->update(['is_premium' => true, 'show_branding' => false]);
        Subscription::where('user_id', $user->id)->where('is_active', true)->update(['is_active' => false]);
        Subscription::create([
            'user_id' => $user->id,
            'plan' => 'premium',
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'is_active' => true,
        ]);

        return back()->with('success', 'Premium activated for user.');
    }
}
