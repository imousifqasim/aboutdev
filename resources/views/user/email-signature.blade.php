@extends('layouts.dashboard')

@section('sidebar')
    @include('user.partials.sidebar')
@endsection

@section('mobile-nav')
    @include('user.partials.mobile-nav')
@endsection

@section('dashboard-content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Email Signature</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Preview -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold mb-4">Preview</h2>
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
                <div id="signature-preview">
                    <table cellpadding="0" cellspacing="0" border="0" style="font-family: Arial, sans-serif; font-size: 14px; color: #333333;">
                        <tr>
                            <td style="padding-right: 15px; vertical-align: top;">
                                @if($user->profile->image)
                                    <img src="{{ url(Storage::url($user->profile->image)) }}" alt="{{ $user->name }}" width="80" height="80" style="border-radius: 50%; object-fit: cover;">
                                @else
                                    <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #8B5CF6, #3B82F6); display: flex; align-items: center; justify-content: center; color: white; font-size: 32px; font-weight: bold; text-align: center; line-height: 80px;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td style="vertical-align: top;">
                                <p style="margin: 0 0 2px 0; font-size: 16px; font-weight: bold; color: #111827;">{{ $user->name }}</p>
                                @if($user->profile->company)
                                    <p style="margin: 0 0 2px 0; font-size: 13px; color: #6B7280;">{{ $user->profile->company }}</p>
                                @endif
                                @if($user->profile->location)
                                    <p style="margin: 0 0 8px 0; font-size: 12px; color: #9CA3AF;">{{ $user->profile->location }}</p>
                                @endif
                                <p style="margin: 0 0 4px 0;">
                                    <a href="{{ url('/' . $user->profile->username) }}" style="color: #8B5CF6; text-decoration: none; font-size: 13px;">{{ url('/' . $user->profile->username) }}</a>
                                </p>
                                @if($user->profile->social_links)
                                    <p style="margin: 8px 0 0 0; font-size: 13px;">
                                        @foreach($user->profile->social_links as $platform => $url)
                                            @if($url)
                                                <a href="{{ $url }}" style="color: #6B7280; text-decoration: none; margin-right: 10px;">{{ ucfirst($platform) }}</a>
                                            @endif
                                        @endforeach
                                    </p>
                                @endif
                                <p style="margin: 8px 0 0 0; font-size: 11px; color: #D1D5DB;">Made with <a href="{{ url('/') }}" style="color: #8B5CF6; text-decoration: none;">DropLaunch</a></p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Copy HTML -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold mb-4">Copy Your Signature</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Copy the HTML below and paste it into your email client's signature settings.</p>

            <div x-data="{ copied: false }">
                <button @click="
                    const el = document.getElementById('signature-preview');
                    const range = document.createRange();
                    range.selectNodeContents(el);
                    const sel = window.getSelection();
                    sel.removeAllRanges();
                    sel.addRange(range);
                    document.execCommand('copy');
                    sel.removeAllRanges();
                    copied = true;
                    setTimeout(() => copied = false, 2000);
                " class="w-full gradient-bg text-white px-6 py-3 rounded-xl font-semibold hover:opacity-90 transition mb-4">
                    <i class="fas fa-copy mr-2"></i>
                    <span x-text="copied ? 'Copied!' : 'Copy Signature'"></span>
                </button>
            </div>

            <div class="mt-4">
                <h3 class="text-sm font-semibold mb-2">How to use:</h3>
                <ol class="text-sm text-gray-500 dark:text-gray-400 space-y-2 list-decimal list-inside">
                    <li>Click "Copy Signature" above</li>
                    <li>Open your email settings (Gmail, Outlook, etc.)</li>
                    <li>Go to Signature settings</li>
                    <li>Paste the copied signature</li>
                    <li>Save your settings</li>
                </ol>
            </div>

            <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <p class="text-sm text-blue-600 dark:text-blue-400">
                    <i class="fas fa-info-circle mr-1"></i>
                    Your email signature updates automatically when you update your profile.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
