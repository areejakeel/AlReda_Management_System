<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Region extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name_ar', 'name_en', 'x', 'y', 'governorate_id'];

    protected $searchableFields = ['*'];

    public $timestamps = false;

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
