<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Image extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['image', 'property_id'];

    protected $searchableFields = ['*'];

    public $timestamps = false;

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
