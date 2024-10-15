<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationType;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{

    protected Notification $Notification;
    protected NotificationType $NotificationType;


    public function __construct(NotificationType $notificationType,Notification $notification){
        $this->NotificationType = $notificationType;
        $this->Notification = $notification;
    }


    public function notifications():JsonResponse{
        $user = auth()->user();
        try {
            $notificationss = Notification::with('type')->where('to_user','=', $user->id)
                ->whereNull('read_at')->orderBy('created_at', 'desc')
                ->take(5)->get();
            $notifications = collect();
            foreach ($notificationss as $item){
                $notifications->push([
                    'id' => $item->id,
                    'title' => $item->type->type,
                    'data' => $item->data
                ]);
            }
            return response()->json(['notifications'=>$notifications],200);
        }catch (Exception $exception){
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ],500);
        }
    }

    public function notification_read(Request $request):JsonResponse{
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:notifications,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ],422);
        }
        try {
            $not = Notification::find($request->id);
            $not->read_at = Carbon::now();
            $not->save();
            return response()->json([
                'success' => true,
                'message' => "Read Successfully.."
            ],200);
        }catch (Exception $exception){
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ],500);
        }
    }

}
