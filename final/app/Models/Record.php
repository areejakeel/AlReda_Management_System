<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Record extends Model
{
    use HasApiTokens, HasFactory, Notifiable;


    public $table='records';

    protected $primaryKey = "id";

    
    protected $fillable = [
     
        'first_name',
        'father_name',
        'last_name', 
        'birthdate',
        'gender',
        'address',
        'moblie_num',
        'Blood_type',
        'social_status',
        'job',
        'Previous_Opertios',
        'AllergyToMedication',
        'Chronic_Diseases',
        '_first_name',
        '_last_name',
        'phone',
        'X_ray',
        'analysis',
        'description',
        'date',
        'patient_id',
    ];

    protected $searchableFields = ['*'];


    public function patient()
    {
        return $this->belongsTo(Patient::class,'patient_id');
    }

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'record_dectors', 'records_id', 'doctors_id');
    }

}



