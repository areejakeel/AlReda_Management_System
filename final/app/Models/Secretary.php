<?php

namespace App\Models;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Secretary  extends \Illuminate\Foundation\Auth\User implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    public $table='secretary';
    protected $primaryKey = "id";
    
    protected $fillable = [
     
        'first_name',
        'last_name', 
        'email',
        'password',
        'UserName',
        'address',
        'phone',
        'gender',
        'birthdate',
        'secretary_img',
        'learning grades',
        'user_id'
      
    
        

    ];
    public function saveImage(UploadedFile $image)
    {
        $imagePath = $image->store('secretary_img', 'public');
        $this->image_path = $imagePath;
        $this->save();
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];}
       
      
        public function user()
        {
            return $this->belongsTo(User::class);
        }
}
