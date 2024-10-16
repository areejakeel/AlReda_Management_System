<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Property extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'price',
        'space',
        'property_state_id',
        'region_id',
    ];

    protected $searchableFields = ['*'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function propertyState()
    {
        return $this->belongsTo(PropertyState::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function favourites()
    {
        return $this->belongsToMany(
            Favourite::class,
            'favorate_property',
            'property_id',
            'favorate_id'
        );
    }
}
