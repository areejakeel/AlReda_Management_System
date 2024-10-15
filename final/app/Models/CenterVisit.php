<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CenterVisit extends Model
{
    use HasFactory;
    protected $table = 'center_visits';

    protected $fillable = [
        'visit_time',
        'userappointment_id'

    ];

    public function userAppointment()
    {
        return $this->belongsTo(User_Appointment::class, 'userappointment_id','id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function referredDoctor()
    {
        return $this->belongsTo(Doctor::class, 'referred_doctor_id');
    }
}

