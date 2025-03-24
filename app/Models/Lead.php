<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'address',
        'pincode',
        'status',
        'notes',
        'expected_amount',
        'follow_up_date',
        'source',
        'location',
        'additional_info'
    ];

    protected $casts = [
        'expected_amount' => 'decimal:2',
        'follow_up_date' => 'date',
        'additional_info' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sale(): HasOne
    {
        return $this->hasOne(Sale::class);
    }

    public function canAddNewLead(): bool
    {
        return Attendance::isPresent($this->user_id, now()->toDateString());
    }

    public function shareWithOtherBrand($brandId, $notes)
    {
        $this->update([
            'status' => 'shared',
            'additional_info' => array_merge($this->additional_info ?? [], [
                'shared_with_brand' => $brandId,
                'shared_notes' => $notes,
                'shared_at' => now()
            ])
        ]);
    }
} 