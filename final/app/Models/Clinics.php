<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class Clinics extends Model
{
    use HasFactory;


    protected $table = "clinics";
    protected $primaryKey = "id";
   //protected $primaryKey = "clinics_id";

    protected $searchableFields = ['*'];

    public $timestamps = false;

    protected $fillable=[

        'clinic_name',
        'clinic_img',
        'description',
        'price'

    ];

    public function saveImage(UploadedFile $image)
    {
        $imagePath = $image->store('clinic_img', 'public');
        $this->image_path = $imagePath;
        $this->save();
    }
    public function center(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(center::class,'center_id');
   }
    public function doctors(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(Doctor::class, 'clinics_id');
}
}
