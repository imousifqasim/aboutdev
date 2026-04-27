<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalUsers = User::where('role', 'user')->count();
        $premiumUsers = User::whereHas('profile', fn($q) => $q->where('is_premium', true))->count();
        $pendingPayments = Payment::where('status', 'pending')->count();
        $totalRevenue = Payment::where('status', 'approved')->sum('amount');

        $recentUsers = User::where('role', 'user')->latest()->take(5)->get();
        $recentPayments = Payment::with('user')->latest()->take(5)->get();

        $monthlyUsers = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyUsers[] = [
                'month' => $date->format('M Y'),
                'count' => User::where('role', 'user')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }

        return view('admin.dashboard', compact(
            'totalUsers', 'premiumUsers', 'pendingPayments', 'totalRevenue',
            'recentUsers', 'recentPayments', 'monthlyUsers'
        ));
    }
}
