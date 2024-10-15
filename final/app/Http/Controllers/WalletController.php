<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    protected Wallet $Wallet;

    public function __construct(Wallet $wallet){
        $this->Wallet = $wallet;
    }

    public function charge(Request $request):JsonResponse{

        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'wallet_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ],422);
        }
        try {
            $wallet = $this->Wallet->with('user')->where('accountID',$request->wallet_id)->first();
            if (!$wallet){
                return response()->json([
                    'success' => false,
                    'message' => "Wallet Not Found.."
                ],404);
            }
            $wallet->balance += $request->amount;
            $wallet->save();
            ################################################### Notification ##########################################################
            (new Notification)->store(5,"Your Wallet has charged with: ".$request->amount."SP ",$wallet->user->id);
            ############################################################################################################################
           return response()->json([
               'success' => true,
               'message' => "Wallet Charged Successfully.."
           ],200);
        }catch (Exception $exception){
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ],500);
        }
    }
}
