<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $guest_id
 * @property int $property_id
 * @property \Carbon\Carbon $check_in
 * @property \Carbon\Carbon $check_out
 * @property string $status
 * @property string|null $notes
 * @property bool $invoice
 * @property int $guests
 * @property float $total_price
 * @property-read Property $property
 * @property-read Guest $guest
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static> where(string|\Closure $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 * @method static static findOrFail(mixed $id)
 */
class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_id',
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

    public function guest(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function property(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
