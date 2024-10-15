<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit_Infos extends Model
{  protected $table = 'visit_infos';
    use HasFactory;
    protected $fillable = [
        'user_appointment_id', 'first_name', 'father_name', 'last_name', 'age', 'address', 'phone_number'
    ];

    
    public function userAppointment()
    {
        return $this->belongsTo(User_Appointment::class,'user_appointment_id');
    }
}

