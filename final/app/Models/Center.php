<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;;

class Center extends Model
{

    use HasApiTokens, HasFactory, Notifiable;


    protected $table = "center";

    protected $primaryKey = "id";

    protected $searchableFields = ['*'];

    public $timestamps = false;

    protected $fillable=[
        'center_name',
        'center_img',
        'description',
        'phone',
        'balance'
    ];
    public function saveImage(UploadedFile $image)
    {
        $imagePath = $image->store('doctor_img', 'public');
        $this->image_path = $imagePath;
        $this->save();
    }


    public function clinics()
    {
        return $this->hasMany(clinics::class);
    }
}
