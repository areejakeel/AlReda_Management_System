<?php


namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\DoctorResource;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Http\Request;
use App\Models\Record;
use App\Models\Doctor;
use App\Models\RecordDoctor;
use App\Models\Clinics;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Role;
use App\Models\Appointment__Clinics;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;


class DoctorController extends Controller
{

    public function Doctorstore(Request $request): JsonResponse
        {
          $validator = Validator::make($request->all(), [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8',
                'UserName' => 'required|unique:doctors,UserName',
                'gender' => 'required|in:female,male',
                'address' => 'required|string',
                'phone' => 'required|regex:/^09[0-9]{8}$/',
                'doctor_img' => 'required|image',
                'learning_grades' => 'required|string',
                'specialization' => 'required|string',
                'clinics_id' => 'required|exists:clinics,id',
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
                'role_id' => 2,
            ]);
            $user->save();

            $doctorData = $request->only([
                'first_name', 'last_name', 'email', 'UserName', 'gender', 'address', 'phone', 'learning_grades', 'specialization', 'clinics_id'
            ]);
            $doctorData['password'] = $user->password;
            $doctorData['user_id']=$user->id;
            if ($request->hasFile('doctor_img')) {
                $image = $request->file('doctor_img');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('image'), $filename);
                $doctorData['doctor_img'] = url('/image/' . $filename);
            }

            $doctor = new Doctor($doctorData);
          $doctor->save();

            return response()->json([
                'data' => $doctor,
                'clinic' => $doctor->clinics->clinic_name,
                'role' => $user->role_id,
                'message' => 'Doctor added successfully as a user with access',
            ]);
        }




        /////////////////////////////////
        public function getDoctorByClinic($clinics_id)
{
    $clinic = Clinics::find($clinics_id);

    if (!$clinic) {
        return response()->json(['error' => 'Clinic not found'], 404);
    }
    $doctors = $clinic->doctors;

    return response()->json($doctors);
}

// search method
public function search($first_name)
{
  $doctor= Doctor ::where(function($query)use($first_name)
 { $query-> where("first_name","like","%".$first_name."%")
           -> orWhere("last_name","like","%".$first_name."%");})->get();
    if (!$doctor) {
        return response()->json( 'This Doctor not found', 404);
    }
    return response()->json([DoctorResource::collection($doctor), 'This Doctor  that you need'], 200);

}
    public function showProfile(Request $request, $id)
    {
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json('Doctor not found', 404);
        }

       if ($doctor->user_id != $request->user()->id) {
          return response()->json('Unauthorized', 401);
     }

        return response()->json($doctor, 200);
    }
    public function getDoctorsByClinic($clinics_id){

        $doctors=Doctor::returnDoctorsInClinic($clinics_id);
        return response()->json($doctors);
          }
    public function showAllDoctors()
    {
        $doctors = Doctor::with(['clinics'=> function($query)
        {

            $query->select('id','clinic_name');
        }])->get();


        if ($doctors->isEmpty()) {
            return response()->json([
                'message' => 'No doctors found',
            ]);
        }
         foreach($doctors as $doctor){

            if($doctor->clinics){

                $doctor->clinic_name=$doctor->clinics->clinic_name;
            }
            unset($doctor->clinics);
         }
        return response()->json([
            'data' =>  $doctors ,
            'message' => 'Successfully retrieved doctors',
        ]);
    }

    public function showdoctor($id)
    {
        return Doctor::find($id);
    }




      /////////////////////////////////////////////////////////
public function showDoctorRecords()
{
    $user = auth()->user();
    $doctor = Doctor::where('user_id',$user->id)->first();
    $records = Record::whereHas('patient.userAppointments', function ($query) use ($doctor) {
        $query->whereHas('appointment', function ($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id);
        });
    })->with(['patient.userAppointments' => function ($query) use ($doctor) {
        $query->whereHas('appointment', function ($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id);
        });
    }])->get();
    return response()->json([
        'records' => $records,
    ]);
}
public function showRecordfullname(Request $request, $first_name, $last_name, $father_name){

    $records = DB::table('records')
    ->join('records', 'records.id', '=', 'record_dectors.records_id')
    ->join('doctors', 'record_dectors.doctors_id', '=', 'doctors.id')
    ->select(DB::raw("CONCAT(doctors.first_name, ' ', doctors.last_name, ' ', doctors.father_name) AS doctor_name"), 'records.*', 'record_doctors.doctors_id')
    >where(function ($query) use ($first_name, $last_name, $father_name) {
        $query->where('records.first_name', 'LIKE', '%' . $first_name . '%')
            ->where('records.last_name', 'LIKE', '%' . $last_name . '%')
            ->where('records.father_name', 'LIKE', '%' . $$father_name . '%');
    })
    -

$user_ids = $records->pluck('user_id');
return response()->json([
    'records' => $records,

]);

}


public function showDoctorAppointments(Request $request, $Id)
{

        $appointments = DB::table('appointments')
        ->join('appointment__clinics', 'appointments.id', '=', 'appointment__clinics.appointment_id')
        ->where('appointment__clinics.doctors_id', $Id)
        ->get();

        $appointmentTimes = [];
        $appointmentDay = '';

        foreach ($appointments as $appointment) {
            $start = strtotime($appointment->the_beginning_of_the_appointment);
            $end = strtotime($appointment->the_end_of_the_appointment);
            $duration = $appointment->duration;
            for ($current = $start; $current < $end; $current += ($duration * 60)) {
                $appointmentTime = date('H:i:s', $current);
                $modifiedEnd = strtotime("+$duration minutes", $current);
                $modifiedEndTime = date('H:i:s', $modifiedEnd);
                $appointmentTimes[] = $appointmentTime;
                $start += ($duration * 60);
                if ($appointmentDay == '') {
                    $appointmentDate = date('Y-m-d', strtotime($appointment->the_beginning_of_the_appointment));
                    $appointmentDay = date('l', strtotime($appointmentDate));
                    if ($appointmentDay == 'Monday') {
                        $appointmentDay = 'الإثنين';
                    } else if ($appointmentDay == 'Tuesday') {
                        $appointmentDay = 'الثلاثاء';
                    } else if ($appointmentDay == 'Wednesday') {
                        $appointmentDay = 'الأربعاء';
                    } else if ($appointmentDay == 'Thursday') {
                        $appointmentDay = 'الخميس';
                    } else if ($appointmentDay == 'Friday') {
                        $appointmentDay = 'الجمعة';
                    } else if ($appointmentDay == 'Saturday') {
                        $appointmentDay = 'السبت';
                    } else if ($appointmentDay == 'Sunday') {
                        $appointmentDay = 'الأحد';
                    }
                }
            }
        }

        // إرجاع المواعيد واسم اليوم كاستجابة JSON
        $response = [
            'appointment_times' => $appointmentTimes,
            'appointment_day' => $appointmentDay
        ];
        return response()->json($response);
    }

      // عرض جميع الأطباء
    public function showdoctors(): JsonResponse
    {
        $doctors = Doctor::all();
        return response()->json(['data' => $doctors, 'message' => 'Doctors retrieved successfully']);
    }


    public function Doctordelete($id): JsonResponse
    {
        // Find the doctor record
         // Delete the associated user record
       $doctor = Doctor::findOrFail($id);

        $user = $doctor->user;
        $user->delete();

        // Delete the doctor record
        $doctor->delete();

        return response()->json([
            'message' => 'Doctor deleted successfully',
        ], 200);
    }


}

