@extends('layouts.dashboard')

@section('sidebar')
    @include('user.partials.sidebar')
@endsection

@section('mobile-nav')
    @include('user.partials.mobile-nav')
@endsection

@section('dashboard-content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Messages</h1>
        @if(!auth()->user()->isPremium())
            <span class="text-sm bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 px-3 py-1 rounded-full">
                <i class="fas fa-crown mr-1"></i> Enable contact form with Premium
            </span>
        @else
            <form method="POST" action="{{ route('profile.contact-form') }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="text-sm px-4 py-2 rounded-lg font-medium transition {{ auth()->user()->profile->contact_form_enabled ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    <i class="fas fa-{{ auth()->user()->profile->contact_form_enabled ? 'toggle-on' : 'toggle-off' }} mr-1"></i>
                    Contact Form {{ auth()->user()->profile->contact_form_enabled ? 'Enabled' : 'Disabled' }}
                </button>
            </form>
        @endif
    </div>

    @if($messages->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
            <i class="fas fa-inbox text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <p class="text-gray-500 dark:text-gray-400">No messages yet.</p>
            @if(auth()->user()->isPremium() && !auth()->user()->profile->contact_form_enabled)
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Enable the contact form to start receiving messages.</p>
            @endif
        </div>
    @else
        <div class="space-y-3">
            @foreach($messages as $message)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 {{ !$message->is_read ? 'border-l-4 border-l-primary-500' : '' }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="font-semibold text-sm">{{ $message->sender_name }}</span>
                                <span class="text-xs text-gray-400">&lt;{{ $message->sender_email }}&gt;</span>
                                @if(!$message->is_read)
                                    <span class="bg-primary-100 text-primary-700 text-xs px-2 py-0.5 rounded-full">New</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $message->message }}</p>
                            <p class="text-xs text-gray-400 mt-2">{{ $message->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex items-center gap-2 ml-4">
                            @if(!$message->is_read)
                                <form method="POST" action="{{ route('messages.read', $message) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-gray-400 hover:text-green-500 transition" title="Mark as read">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @endif
                            <a href="mailto:{{ $message->sender_email }}" class="text-gray-400 hover:text-primary-500 transition" title="Reply via email">
                                <i class="fas fa-reply"></i>
                            </a>
                            <form method="POST" action="{{ route('messages.destroy', $message) }}" onsubmit="return confirm('Delete this message?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-500 transition" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">
            {{ $messages->links() }}
        </div>
    @endif
</div>
@endsection
