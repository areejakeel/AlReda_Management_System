<?php

namespace App\Models;

use App\Mail\WalletCreation;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class Wallet extends Model
{
    use HasFactory;

    protected $table = 'wallets';

    protected $fillable = [
        'accountID',
        'user_id',
        'balance',
        'password'
    ];

    protected $hidden = [
        'password'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    /**
     * @throws Exception
     */
    public function store($userID,$mail){
        $acountID = $this->generateAccountID();
        $password = random_int(1000000000, 9999999999);
        Wallet::create([
            'accountID' => $acountID,
            'user_id' => $userID,
            'password' =>  Hash::make($password)
        ]);
        Mail::to($mail)->send(new WalletCreation($acountID,$password));
    }

    /**
     * @throws Exception
     */
    function generateAccountID(): int
    {

        $accountID = random_int(1000000000, 9999999999);
        while ($this->where('accountID', $accountID)->exists()) {
            $accountID = random_int(1000000000, 9999999999);
        }

        return $accountID;
    }
}
