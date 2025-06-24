<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'check_in',
        'check_out',
        'status',
        'notes',
        'invoice',
        'guests',
        'total_price',
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'total_price' => 'decimal:2',
        'invoice' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public static function updateReservationJson()
    {
        $reservations = Reservation::all()->toArray();
        Storage::put('reservations.json', encrypt(json_encode($reservations, JSON_PRETTY_PRINT)));
        error_log("Archivo reservations.json actualizado tras creación de usuario.");
    }

    public static function markAsInvoiced($reservationId)
    {
        $reservation = self::findOrFail($reservationId);
        $reservation->invoice = true;
        $reservation->save();
        self::updateReservationJson();
        return $reservation;
    }
}
