<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    //Añadir id
    protected $fillable = [
        'title',
        'description',
        'location',
        'price_per_night',
        'capacity',
        'image_url',
        'lat',
        'lng',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price_per_night' => 'decimal:2',
    ];

    /**
     * Get the reservations for the property.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
