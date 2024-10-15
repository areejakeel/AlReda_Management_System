<?php

namespace App\Models;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends \Illuminate\Foundation\Auth\User implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    public $table='admin';
   // protected $guard = 'admins';

    protected $primaryKey = "id";
    
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone', 
        'password',
    
    ];

    protected $searchableFields = ['*'];

//protected $with = ['roles'];
 
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
   
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
