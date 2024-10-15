<?php

namespace App\Models;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
class Appointment extends Model

    {
        use  HasApiTokens, HasFactory, Notifiable;

        public $table = 'appointments';
        protected $primaryKey = "id";

        protected $fillable = [
            'appointment_date',
            'visit_type',
            'doctor_id',
            'time_slots',
        ];

        protected $searchableFields = ['*'];

        public function doctor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
        {
            return $this->belongsTo(Doctor::class, 'doctor_id'); // تغيير اسم الحقل
        }

        public function user()
        {
            return $this->belongsTo(User::class);
        }

        public function clinic()
        {
            return $this->hasOne(Appointment__Clinics::class, 'appointment_id');
        }
    }


