<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Analytic extends Model
{
    use HasFactory;

    protected $table = 'analytics';

    protected $fillable = [
        'user_id',
        'profile_views',
        'link_clicks',
        'date',
        'referrer',
        'country',
        'device',
    ];

    protected $casts = [
        'date' => 'date',
        'profile_views' => 'integer',
        'link_clicks' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
