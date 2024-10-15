<?php

namespace App\Http\Controllers;
use App\Models\Secretary;
use App\Models\User_Appointment;
use App\Models\Appointment__Clinics;
use App\Models\CenterVisit;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use App\Http\Resources\SecretaryResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class SecretaryController extends Controller
{



    public function addSecretary(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'UserName' => 'required|unique:secretary,UserName',
            'birthdate'=>'required|date',
            'gender' => 'required|in:female,male',
            'address' => 'required|string',
            'phone' => 'required|regex:/^09[0-9]{8}$/',
            'secretary_img' => 'required|image',
            'learning_grades' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'phone' => $request->input('phone'),
            'role_id' => 3,
        ]);
        $user->save();

        $secretaryData = $request->only([
            'first_name', 'last_name', 'email', 'password_Centre', 'UserName', 'gender','birthdate', 'address', 'phone', 'learning_grades', 'center_id'
        ]);
        $secretaryData['password'] = $user->password;
        $secretaryData['user_id'] = $user->id;
    
        if ($request->hasFile('secretary_img')) {
            $image = $request->file('secretary_img');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('image'), $filename);
            $secretaryData['secretary_img'] = url('/image/' . $filename);
        }
    
        // Create the secretary
        $secretary = new Secretary($secretaryData);
        $secretary->save();
    
        $token = JWTAuth::fromUser($user);
    
        return response()->json([
            'data' => $secretary,
            'role' => $user->role_id,
            'message' => 'Secretary added successfully as a user with access',
        ]);
    }
    
    

    public function storeVisit(Request $request)
    {

        $userappointment_id = $request->input('userappointment_id');
        $userAppointment = User_Appointment::find($userappointment_id);
    
        if (  !$userAppointment) {
            return response()->json(['message' => 'not found userAppointment.'], 404);
        }
        $userAppointment->status = 'attended';
        $userAppointment->save();
        $appointmet_time=$userAppointment->appointment_time;
        $date = date("Y-m-d", strtotime(   $appointmet_time));
        $visit_time=$date;

        $centerVisit = CenterVisit::create([
         'visit_time' => $visit_time,
         'userappointment_id'=>$userappointment_id,
     ]);
    
        return response()->json(['message' => "Center visit created successfully"], 200);}


/////////////////////////////ShowSecretary

       public function ShowSecretary()
       {
        $Secretary = Secretary::all();
    
       if ( $Secretary->isEmpty()) {
           return response()->json([
               'message' => 'No  Secretary found',
           ]);
       }
    
       return response()->json([
           'data' =>   $Secretary,
          'message' => 'Successfully retrieved Secretaries',
       ]);
       }
     ////////////////////////////////Secretarydelete
     
     public function Secretarydelete($id): JsonResponse
{
    // Find the secretary record
    $secretary = Secretary::findOrFail($id);

    // Delete the associated user record
    $user = $secretary->user;
    $user->delete();

    // Delete the secretary record
    $secretary->delete();

    return response()->json([
        'message' => 'Secretary deleted successfully',
    ], 200);
}


////////////////////////////////////////////عرض جميع الزيارات
public function getAllVisits()
{
    $visits = DB::table('center_visits')
    ->join('user_appointments', 'center_visits.userappointment_id', '=', 'user_appointments.id')
    ->join('appointments', 'user_appointments.appointment_id', '=', 'appointments.id')
    ->join('doctors', 'appointments.doctor_id', '=', 'doctors.id')
    ->join('patients', 'user_appointments.patient_id', '=', 'patients.id')
    ->join('users', 'patients.user_id', '=', 'users.id')
    ->join('clinics', 'doctors.clinics_id', '=', 'clinics.id')
        ->select(
            'user_appointments.appointment_time',
            'clinics.clinic_name as clinic_name',
            'doctors.first_name as doctor_first_name',
            'doctors.last_name as doctor_last_name',
            'users.first_name as user_first_name',
            'users.last_name as user_last_name'
        )
        ->get();

    return response()->json(['visits' => $visits], 200);
}
}

    
