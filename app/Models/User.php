<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
        'void_pin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'void_pin',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isGudang(): bool
    {
        return in_array($this->role, ['gudang', 'admin']);
    }

    public function isKasir(): bool
    {
        return in_array($this->role, ['kasir', 'admin']);
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }

        return $this->role === $roles;
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(CashierShift::class);
    }

    public function inbounds(): HasMany
    {
        return $this->hasMany(Inbound::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
