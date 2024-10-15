<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
class Articles extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    public $table='articles';
    protected $primaryKey = "id";
    
    protected $fillable = [
     
        'title',
        'content', 
        'doctors_id'
        

    ];

    public function doctor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctors_id');
    }
    
  public static function returnarticlesfordoctor($doctors_id)
    {
    
        return self::where('doctors_id',$doctors_id)->get();
    }

}
