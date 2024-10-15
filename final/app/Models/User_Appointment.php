<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class User_Appointment extends Model
{

    use HasFactory;
    protected $table = 'user_appointments';
    protected $fillable = ['patient_id', 'appcl_id', 'appointment_id', 'visit_type', 'status','Astatus'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Appointment__Clinics::class, 'appcl_id');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
    public function centerVisits()
    {
        return $this->hasMany(CenterVisit::class, 'userappointment_id');
    }

}


