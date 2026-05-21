<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * @property int         $id
 * @property int         $owner_id
 * @property string      $title
 * @property string      $description
 * @property string      $location
 * @property float       $price_per_night
 * @property int         $capacity
 * @property int         $size
 * @property string      $bedrooms
 * @property int         $bathrooms
 * @property int         $min_nights
 * @property string      $images_div
 * @property string|null $tv
 * @property bool        $entertainment
 * @property bool        $parking
 * @property bool        $pool
 * @property bool        $garden
 * @property bool        $safeBox
 * @property bool        $terrace
 * @property bool        $wifi
 * @property float       $lat
 * @property float       $lng
 */
class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'title',
        'description',
        'location',
        'price_per_night',
        'capacity',
        'size',
        'bedrooms',
        'bathrooms',
        'min_nights',
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
        'lat'             => 'decimal:7',
        'lng'             => 'decimal:7',
        'parking'         => 'boolean',
        'entertainment'   => 'boolean',
        'pool'            => 'boolean',
        'garden'          => 'boolean',
        'safeBox'         => 'boolean',
        'terrace'         => 'boolean',
        'wifi'            => 'boolean',
    ];

    // --- Relations ---

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function reservationPrices(): HasMany
    {
        return $this->hasMany(ReservationPrice::class);
    }

    // --- Methods ---

    public function priceForDate(string $date): ?float
    {
        return $this->reservationPrices()
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->value('price_per_night');
    }

    protected static function booted(): void
    {
        static::deleted(function (Property $property): void {
            Storage::disk('public')->deleteDirectory('images/'.$property->images_div);
        });
    }
}