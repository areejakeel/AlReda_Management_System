<?php

namespace App\Models;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    public $table='users';
   // protected $guard = 'user';
    protected $primaryKey = "id";
    
    protected $fillable = [
     
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'role_id'
    ];

    protected $searchableFields = ['*'];

 
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

   
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }
    public function secretary()
    {
        return $this->hasOne(Secretary::class);
    }
    public function appointments()
    {
        return $this->hasMany(User_Appointment::class);
    }

    public function appointmentClinics()
    {
        return $this->hasManyThrough(AppointmentClinic::class, UserAppointment::class);
    }

    public function appointmentAppointments()
    {
        return $this->hasManyThrough(Appointment::class, UserAppointment::class);
    }
    public function patient()
    {
        return $this->hasOne(Patient::class);
    }
}

