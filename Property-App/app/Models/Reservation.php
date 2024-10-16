<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'property_id',
        'user_id',
        'start_date',
        'end_date',
        'price',
        'reservation_state_id',
        'reservation_type_id',
    ];

    protected $searchableFields = ['*'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservationState()
    {
        return $this->belongsTo(ReservationState::class);
    }

    public function reservationType()
    {
        return $this->belongsTo(ReservationType::class);
    }
}
