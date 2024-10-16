<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasFactory;
    use Searchable;

    protected $fillable = ['name', 'email', 'password'];

    protected $searchableFields = ['*'];

    protected $hidden = ['password'];

    protected $with = ['roles'];

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function favourite()
    {
        return $this->hasOne(Favourite::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class)
            ->select(['roles.id', 'roles.name_' . request()->header('lang')]);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
