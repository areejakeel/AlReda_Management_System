<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Favourite extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['user_id'];

    protected $searchableFields = ['*'];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function properties()
    {
        return $this->belongsToMany(
            Property::class,
            'favorate_property',
            'favorate_id'
        );
    }
}
