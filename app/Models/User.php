<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $phone_number
 * @property bool $is_admin
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'is_admin',
        'is_super_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'is_admin',
        'is_super_admin',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'is_super_admin' => 'boolean',
    ];

    // --- Relations ---

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'owner_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    // --- Methods ---

    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }
}
