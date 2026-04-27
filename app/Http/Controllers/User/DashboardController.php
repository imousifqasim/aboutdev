<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Analytic;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user()->load(['profile', 'links', 'activeSubscription']);

        $totalViews = Analytic::where('user_id', $user->id)->sum('profile_views');
        $totalClicks = $user->links->sum('clicks');
        $totalLinks = $user->links->count();

        $recentAnalytics = Analytic::where('user_id', $user->id)
            ->where('date', '>=', now()->subDays(30))
            ->orderBy('date')
            ->get();

        $chartData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayData = $recentAnalytics->firstWhere('date', $date);
            $chartData[] = [
                'date' => now()->subDays($i)->format('M d'),
                'views' => $dayData ? $dayData->profile_views : 0,
                'clicks' => $dayData ? $dayData->link_clicks : 0,
            ];
        }

        return view('user.dashboard', compact('user', 'totalViews', 'totalClicks', 'totalLinks', 'chartData'));
    }
}
