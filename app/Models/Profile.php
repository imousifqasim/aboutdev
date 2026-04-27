<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'username',
        'bio',
        'image',
        'theme',
        'is_premium',
        'location',
        'company',
        'website',
        'social_links',
        'gallery',
        'videos',
        'meta_title',
        'meta_description',
        'og_image',
        'show_branding',
        'custom_domain',
    ];

    protected $casts = [
        'social_links' => 'array',
        'gallery' => 'array',
        'videos' => 'array',
        'is_premium' => 'boolean',
        'show_branding' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
