<?php

namespace App\Http\Controllers;

use App\Models\Center;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\User;
use App\Models\User_Appointment;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected Payment $Payment;
    protected Wallet $Wallet;

    public function __construct(Payment $payment,Wallet $wallet){
        $this->Payment = $payment;
        $this->Wallet = $wallet;
    }



    public function donation(Request $request):JsonResponse{

        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'receipt' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ],422);
        }

        try {
            $input = request(['amount','name','phone']);
            $input['type'] = '1';
            $input['date'] = Carbon::now();
            $image = $request->file('receipt');
            $fileName = time() . '_' . $image->getClientOriginalExtension();
            $image->move(public_path('image'), $fileName);
            $input['receipt'] = url('/image/' . $fileName);
            $this->Payment->store($input);
            ################################################### Notification ##########################################################
            $admin = User::where('role_id',1)->first();
            (new Notification)->store(6,"There Is New Donation: ".$request->amount."SP ",$admin->id);
            ############################################################################################################################
            return response()->json([
                'success' => true,
                'message' => "Payment Successfully.."
            ],200);
        }catch (Exception $exception){
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ],500);
        }
    }

    public function payment(Request $request,$id):JsonResponse{
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'password' => 'required',
            'selected_time' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ],422);
        }
        try {
            $wallet = $this->Wallet->where('user_id',$user->id)->first();
            if (!Hash::check($request->password, $wallet->password)){
                return response()->json([
                    'success' => false,
                    'message' => "Incorrect wallet password"
                ],500);
            }
            $appointment = User_Appointment::with('appointment')->where('appointment_id',$id)
                ->whereTime('appointment_time',$request->selected_time)->first();
            if (!$appointment){
                return response()->json(['error' => 'الموعد غير موجود'], 404);
            }elseif ($appointment->Astatus ==1){
                return response()->json(['error' => 'الموعد مؤكد مسبقا'],400);
            }
            if ($wallet->balance < $request->amount){
                return response()->json([
                    'success' => false,
                    'message' => "You Do not Have Enough Balance.."
                ],500);
            }else{
                DB::beginTransaction();
                $wallet->balance -=$request->amount;
                $wallet->save();
                $input['amount'] = $request->amount;
                $input['user_id'] = $user->id;
                $input['type'] = '0';
                $input['operation'] = 'payment';
                $input['date'] = Carbon::now();
                $this->Payment->store($input);
                $appointment->Astatus = 1;
                $appointment->save();
                $center = Center::first();
                $center->balance +=$request->amount;
                $center->save();
                ################################################### Notification ##########################################################
                (new Notification)->store(3,"Appointment On: ".$appointment->appointment->appointment_date." at: ".$request->selected_time ,$user->id);
                ############################################################################################################################
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "Payment Successfully.."
                ],200);
            }
        }catch (Exception $exception){
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ],500);
        }
    }

    public function donations():JsonResponse{
        try {
            $donationss = Payment::where('type', 1)->get();
            $totalPayments = $donationss->sum('amount');
            $averagePayments = $donationss->avg('amount');
            $donations = collect();
            $result = collect();
            foreach ($donationss as $item){
                $donations->push([
                    'name' => $item->name,
                    'receipt' => $item->receipt,
                    'amount' => $item->amount,
                    'date' => $item->date
                ]);
            }
            $result->put('total_donations', $totalPayments);
            $result->put('average_donations', $averagePayments);
            $result->put('donations', $donations);
            return response()->json($result,200);
        }catch (Exception $exception){
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ],500);
        }
    }

    public function payments():JsonResponse{
        try {
            $paymentss = Payment::with('user')->where('type', 0)->get();
            $totalPayments = Center::first();
            $averagePayments = Payment::with('user')->where('type', 0)
                ->where('operation','payment')->get();
            $pavg = $averagePayments->avg('amount');
            $averageRefund = Payment::with('user')->where('type', 0)
                ->where('operation','refund')->get();
            $ravg = $averageRefund->avg('amount');
            $payments = collect();
            $result = collect();
            foreach ($paymentss as $item){
                $payments->push([
                    'name' => $item->user->first_name.' '.$item->user->last_name,
                    'amount' => $item->amount,
                    'operation' => $item->operation,
                    'date' => $item->date
                ]);
            }
            $result->put('total_balance', $totalPayments->balance);
            $result->put('average_payments', $pavg);
            $result->put('average_refunds', $ravg);
            $result->put('payments', $payments);
            return response()->json($result,200);
        }catch (Exception $exception){
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ],500);
        }
    }

}
