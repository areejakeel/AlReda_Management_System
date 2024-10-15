<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SecretaryResource extends JsonResource
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
            "secretary_img"=>$this->secretary_img,
            "learning_grades"=>$this->learning_grades,
           
        ];
    }
}
