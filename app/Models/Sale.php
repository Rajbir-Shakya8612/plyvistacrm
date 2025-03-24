<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    protected $fillable = [
        'user_id',
        'lead_id',
        'amount',
        'payment_status',
        'payment_method',
        'notes',
        'product_details',
        'sale_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'product_details' => 'array',
        'sale_date' => 'date'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
} 