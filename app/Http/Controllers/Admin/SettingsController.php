<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $settings = [
            'site_name' => SiteSetting::get('site_name', 'LinkFolio'),
            'site_description' => SiteSetting::get('site_description', 'Create your personal portfolio page'),
            'site_keywords' => SiteSetting::get('site_keywords', 'portfolio, links, bio'),
            'premium_price' => SiteSetting::get('premium_price', '9.99'),
            'premium_currency' => SiteSetting::get('premium_currency', 'USD'),
            'contact_email' => SiteSetting::get('contact_email', 'admin@linkfolio.com'),
            'footer_text' => SiteSetting::get('footer_text', ''),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_description' => ['nullable', 'string', 'max:500'],
            'site_keywords' => ['nullable', 'string', 'max:500'],
            'premium_price' => ['required', 'numeric', 'min:0'],
            'premium_currency' => ['required', 'string', 'max:10'],
            'contact_email' => ['required', 'email'],
            'footer_text' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($request->only([
            'site_name', 'site_description', 'site_keywords',
            'premium_price', 'premium_currency', 'contact_email', 'footer_text',
        ]) as $key => $value) {
            SiteSetting::set($key, $value);
        }

        return back()->with('success', 'Settings updated successfully!');
    }
}
