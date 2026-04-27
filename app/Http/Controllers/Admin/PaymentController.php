<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Payment::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->latest()->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    public function show(Payment $payment): View
    {
        $payment->load(['user.profile', 'reviewer']);
        return view('admin.payments.show', compact('payment'));
    }

    public function approve(Request $request, Payment $payment): RedirectResponse
    {
        return DB::transaction(function () use ($request, $payment) {
            $payment = Payment::lockForUpdate()->find($payment->id);

            if ($payment->status !== 'pending') {
                return back()->with('error', 'This payment has already been reviewed.');
            }

            $payment->update([
                'status' => 'approved',
                'admin_note' => $request->admin_note,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            $user = $payment->user;
            $profile = $user->profile;
            $profile->update(['is_premium' => true, 'show_branding' => false]);

            Subscription::where('user_id', $user->id)->where('is_active', true)->update(['is_active' => false]);

            Subscription::create([
                'user_id' => $user->id,
                'plan' => 'premium',
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'is_active' => true,
            ]);

            return back()->with('success', 'Payment approved. User upgraded to Premium!');
        });
    }

    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        $request->validate([
            'admin_note' => ['required', 'string', 'max:500'],
        ]);

        return DB::transaction(function () use ($request, $payment) {
            $payment = Payment::lockForUpdate()->find($payment->id);

            if ($payment->status !== 'pending') {
                return back()->with('error', 'This payment has already been reviewed.');
            }

            $payment->update([
                'status' => 'rejected',
                'admin_note' => $request->admin_note,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            return back()->with('success', 'Payment rejected.');
        });
    }
}
