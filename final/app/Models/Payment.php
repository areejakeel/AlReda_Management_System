<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'type',
        'user_id',
        'name',
        'receipt',
        'amount',
        'operation',
        'date'
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function store($data){
        $this->create($data);
    }
}
