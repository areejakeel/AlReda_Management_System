<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Governorate extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name_ar', 'name_en'];

    protected $searchableFields = ['*'];

    public $timestamps = false;

    public function regions()
    {
        return $this->hasMany(Region::class);
    }
}
