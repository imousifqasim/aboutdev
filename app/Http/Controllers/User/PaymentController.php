<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user()->load(['profile', 'activeSubscription']);
        $payments = $request->user()->payments()->latest()->get();

        $paymentMethods = [
            [
                'name' => 'Binance (USDT)',
                'value' => 'binance',
                'details' => 'UID: 827969859',
            ],
            [
                'name' => 'JazzCash',
                'value' => 'jazzcash',
                'details' => 'Number: 03286477314',
            ],
            [
                'name' => 'Raast ID',
                'value' => 'raast',
                'details' => 'ID: 03286477314',
            ],
            [
                'name' => 'Meezan Bank',
                'value' => 'meezan_bank',
                'details' => 'Account: 26720109424781 | Title: Tousif Ahmad',
            ],
        ];

        return view('user.payments.index', compact('user', 'payments', 'paymentMethods'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $pendingPayment = $user->payments()->where('status', 'pending')->first();
        if ($pendingPayment) {
            return back()->with('error', 'You already have a pending payment. Please wait for it to be reviewed.');
        }

        $request->validate([
            'method' => ['required', 'string', 'in:binance,jazzcash,raast,meezan_bank'],
            'amount' => ['required', 'numeric', 'min:1'],
            'transaction_id' => ['required', 'string', 'max:255', 'unique:payments,transaction_id'],
            'screenshot' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
        ]);

        $screenshotPath = $request->file('screenshot')->store('payment-screenshots', 'public');

        Payment::create([
            'user_id' => $user->id,
            'method' => $request->method,
            'amount' => $request->amount,
            'transaction_id' => $request->transaction_id,
            'screenshot_path' => $screenshotPath,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Payment submitted successfully! It will be reviewed shortly.');
    }
}
