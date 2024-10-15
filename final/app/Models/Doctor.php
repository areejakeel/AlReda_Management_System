<?php

namespace App\Models;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class Doctor extends \Illuminate\Foundation\Auth\User implements JWTSubject

{
   
    use HasApiTokens, HasFactory, Notifiable;

    public $table='doctors';
    protected $primaryKey = "id";
    
    protected $fillable = [
     'id',
     
        'first_name',
        'last_name', 
        'email',
        'password',
        'UserName',
        'gender',
        'address',
        'phone',
        'doctor_img',
        'learning grades',
     'specialization',
        'clinics_id',
        'user_id',

    

    ];

    protected $searchableFields = ['*'];
  
 
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function saveImage(UploadedFile $image)
    {
        $imagePath = $image->store('doctor_img', 'public');
        $this->image_path = $imagePath;
        $this->save();
    }
    public function clinics(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Clinics::class, 'clinics_id');
    }
    
  
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];}
        public function records()
        {
            return $this->belongsToMany(Record::class, 'record_dectors', 'doctors_id', 'records_id');
        }
        
        public function user()
        {
            return $this->belongsTo(User::class);
        }

        public function appointments()
        {
            return $this->hasMany(Appointment::class, 'doctor_id'); 
        }
}
    

