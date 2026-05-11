<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'is_admin',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
    ];

    // --- Relations ---

    public function properties()
    {
        return $this->hasMany(Property::class, 'owner_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // --- Methods ---
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
    public function isAdmin()
    {
        return $this->is_admin;
    }
}
