<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WalletOperation extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['value', 'type', 'describtion', 'wallet_id'];

    protected $searchableFields = ['*'];

    protected $table = 'wallet_operations';

    protected $casts = [
        'type' => 'boolean',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
