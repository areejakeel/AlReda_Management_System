<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
     class Advert extends Model { 
        use HasApiTokens,
        HasFactory,
        Notifiable;
        public $table = 'adverts';
        protected $primaryKey = "id";
        protected $fillable = [ 
        'description',
        'image',
        'status', 
    ]; 
        protected $searchableFields = ['*'];
        //   protected $appends = ['previous_status'];
        // public function getPreviousStatusAttribute() 
        // {
        //      return $this->attributes['previous_status'] ?? null;
        //      } 
        // public function setPreviousStatusAttribute($value)
        //  {
        //      $this->attributes['previous_status'] = $value;
        //  } 
            
            
            }