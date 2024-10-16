<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name_ar', 'name_en'];

    protected $searchableFields = ['*'];

    public $timestamps = false;

    protected $hidden=['pivot'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
