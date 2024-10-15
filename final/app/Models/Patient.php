<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    public $table='patients';
    use HasFactory;
    protected $fillable = [
     
        'first_name',
        'father_name',
        'last_name',];
        
        public function user()
        {
            return $this->belongsTo(User::class);
        }
        public function userAppointments()
        {
            return $this->hasMany(User_Appointment::class);
        }
        
}
