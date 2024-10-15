<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'type_id',
        'data',
        'from_user',
        'to_user',
        'read_at',
        'is_deleted',
        'created_at',
        'updated_at'
    ];

    public function type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(NotificationType::class,'type_id','id');
    }

    public function forUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,'to_user','id');
    }

    public function store($type,$data,$user){
        $this->create([
            'type_id' => $type,
            'data' => $data,
            'to_user' => $user,
        ]);
    }


}
