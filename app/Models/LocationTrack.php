<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationTrack extends Model
{
    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'address',
        'speed',
        'accuracy',
        'tracked_at'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'tracked_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getDailyTimeline($userId, $date)
    {
        return self::where('user_id', $userId)
            ->whereDate('tracked_at', $date)
            ->orderBy('tracked_at')
            ->get();
    }

    public static function getCurrentLocation($userId)
    {
        return self::where('user_id', $userId)
            ->latest('tracked_at')
            ->first();
    }
} 