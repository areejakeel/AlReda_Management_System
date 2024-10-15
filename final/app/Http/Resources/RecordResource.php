<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RecordResource extends JsonResource
{
   
    public function toArray($request)
    {
        return [
      'id'=>$this->id,
      'X_ray'=>json_decode($this->X_ray),
      'analysis'=>json_decode($this->analysis),

        ];
    }
}
