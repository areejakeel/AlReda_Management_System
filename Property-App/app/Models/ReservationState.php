<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReservationState extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name_ar', 'name_en'];

    protected $searchableFields = ['*'];

    protected $table = 'reservation_states';

    public $timestamps = false;

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
