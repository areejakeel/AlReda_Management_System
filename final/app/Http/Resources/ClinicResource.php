<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClinicResource extends JsonResource
{
    public function toArray($request)
    {
        return  [
            "id"=>$this->id,
            "clinic_name"=>$this->clinic_name,       
            "description"=>$this->description,
            "clinic_img"=>$this->clinic_img,
  
        ];
    }
}
