<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'photo',
        'whatsapp_number',
        'pincode',
        'address',
        'location',
        'designation',
        'date_of_joining',
        'status',
        'settings',
        'target_amount',
        'target_leads'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_joining' => 'date',
            'settings' => 'array',
            'target_amount' => 'decimal:2',
            'target_leads' => 'integer'
        ];
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function locationTracks(): HasMany
    {
        return $this->hasMany(LocationTrack::class);
    }

    public function schedule()
    {
        return $this->hasMany(Schedule::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSalesperson(): bool
    {
        return $this->role === 'salesperson';
    }

    public function isDealer(): bool
    {
        return $this->role === 'dealer';
    }

    public function isCarpenter(): bool
    {
        return $this->role === 'carpenter';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }
}
