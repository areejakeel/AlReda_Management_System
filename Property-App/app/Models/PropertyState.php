<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PropertyState extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name_ar', 'name_en'];

    protected $searchableFields = ['*'];

    protected $table = 'property_states';

    public $timestamps = false;

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
