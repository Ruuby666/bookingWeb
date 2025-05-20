<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'location',
        'price_per_night',
        'capacity',
        'size',
        'bedrooms',
        'bathrooms',
        'images_div',
        'tv',
        'entertainment',
        'parking',
        'pool',
        'garden',
        'safeBox',
        'terrace',
        'wifi',
        'lat',
        'lng',
    ];


    protected $casts = [
        'price_per_night' => 'decimal:2',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'parking' => 'boolean',
        'entertainment' => 'boolean',
        'pool' => 'boolean',
        'garden' => 'boolean',
        'safeBox' => 'boolean',
        'terrace' => 'boolean',
        'wifi' => 'boolean',
    ];


    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}

