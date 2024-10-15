<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    public function toArray($request)
    {
        return  [
            "id"=>$this->id,
            "first_name"=>$this->first_name,
            "last_name"=>$this->last_name,
            "email"=>$this->email,
            "password_Centre"=>$this->password_Centre,
            "UserName"=>$this->UserName,
            "gender"=>$this->gender,
            "address" => $this->address,
            "phone"=>$this->phone,
            "doctor_img"=>$this->doctor_img,
            "learning_grades"=>$this->learning_grades,
            "specializations_id"=>$this->specializations_id,
            "clinics_id"=>$this->clinics_id,
  
        ];
    }
}
