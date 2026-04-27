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
        <div>
            <h1 class="text-2xl font-bold">Manage Links</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm">
                {{ $links->count() }} link(s)
                @if(!$user->isPremium()) — {{ 5 - $links->count() }} remaining on Free plan @endif
            </p>
        </div>
    </div>

    <!-- Add Link Form -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6" x-data="{ showForm: false }">
        <button @click="showForm = !showForm" class="flex items-center gap-2 text-primary-600 font-medium">
            <i class="fas fa-plus-circle"></i>
            <span x-text="showForm ? 'Cancel' : 'Add New Link'"></span>
        </button>
        <form method="POST" action="{{ route('links.store') }}" x-show="showForm" x-cloak class="mt-4 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Title</label>
                    <input type="text" name="title" required placeholder="My Website"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">URL</label>
                    <input type="url" name="url" required placeholder="https://example.com"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-primary-500 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Icon (optional)</label>
                <select name="icon" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-primary-500 outline-none">
                    <option value="">No icon</option>
                    <option value="globe">Globe</option>
                    <option value="github">GitHub</option>
                    <option value="linkedin">LinkedIn</option>
                    <option value="twitter">Twitter</option>
                    <option value="instagram">Instagram</option>
                    <option value="youtube">YouTube</option>
                    <option value="envelope">Email</option>
                    <option value="briefcase">Portfolio</option>
                    <option value="file-alt">Resume</option>
                    <option value="shopping-cart">Shop</option>
                </select>
            </div>
            <button type="submit" class="gradient-bg text-white px-6 py-2.5 rounded-lg font-medium hover:opacity-90 transition">
                <i class="fas fa-plus mr-1"></i> Add Link
            </button>
        </form>
    </div>

    <!-- Links List -->
    <div class="space-y-3 mb-20 lg:mb-0">
        @forelse($links as $link)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4" x-data="{ editing: false }">
                <div x-show="!editing" class="flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="flex flex-col gap-1 text-gray-400 cursor-move">
                            <i class="fas fa-grip-vertical"></i>
                        </div>
                        <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                            @if($link->icon)
                                <i class="fa{{ in_array($link->icon, ['github', 'linkedin', 'twitter', 'instagram', 'youtube']) ? 'b' : 's' }} fa-{{ $link->icon }} text-gray-600 dark:text-gray-400"></i>
                            @else
                                <i class="fas fa-link text-gray-600 dark:text-gray-400"></i>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="font-medium text-sm truncate">{{ $link->title }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $link->url }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 ml-3 flex-shrink-0">
                        <span class="text-xs text-gray-500 hidden sm:inline">{{ $link->clicks }} clicks</span>
                        <form method="POST" action="{{ route('links.toggle', $link) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center {{ $link->is_active ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}" title="{{ $link->is_active ? 'Active' : 'Inactive' }}">
                                <i class="fas fa-{{ $link->is_active ? 'eye' : 'eye-slash' }} text-xs"></i>
                            </button>
                        </form>
                        <button @click="editing = true" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center" title="Edit">
                            <i class="fas fa-pen text-xs"></i>
                        </button>
                        <form method="POST" action="{{ route('links.destroy', $link) }}" onsubmit="return confirm('Delete this link?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center" title="Delete">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Edit Form -->
                <form x-show="editing" x-cloak method="POST" action="{{ route('links.update', $link) }}" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <input type="text" name="title" value="{{ $link->title }}" required
                            class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 outline-none text-sm">
                        <input type="url" name="url" value="{{ $link->url }}" required
                            class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 outline-none text-sm">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-primary-700">Save</button>
                        <button type="button" @click="editing = false" class="bg-gray-200 dark:bg-gray-700 px-4 py-2 rounded-lg text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        @empty
            <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <i class="fas fa-link text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">No links yet. Add your first link above!</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
