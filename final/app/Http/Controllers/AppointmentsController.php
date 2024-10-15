<?php

namespace App\Http\Controllers;
use App\Models\Center;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Wallet;
use Illuminate\Support\Facades\Validator;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use App\Models\Patient;
use App\Models\User_Appointment;
use App\Models\Visit_Infos;
use Carbon\Carbon;

class AppointmentsController extends Controller
{

    //////////////////////////////// تخزين المواعيد

    public function AppointmentStore(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'appointment_date' => 'required|date_format:Y-m-d',
            'visit_type' => 'required|in:center,home',
            'doctor_id' => 'required|exists:doctors,id',
            'time_slots' => 'required|array',
            'time_slots.*.start_time' => 'required|date_format:H:i:s',
            'time_slots.*.end_time' => 'required|date_format:H:i:s',
            'time_slots.*.is_booked' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $appointment = new Appointment();
        $appointment->appointment_date = $request->input('appointment_date');
        $appointment->visit_type = $request->input('visit_type');
        $appointment->doctor_id = $request->input('doctor_id');

        $timeSlots = $request->input('time_slots');
        $appointment->time_slots = json_encode($timeSlots);

        $appointment->save();

        return response()->json(['data' => $appointment, 'message' => 'Appointment added successfully'], 201);
    }
    /////////////////////////// جدول كسر تخزين المواعيد مع الدكتز=ور والعيادة

    public function Appointment_ClinicStore(Request $request){
        $input=$request->all();

        $validator=Validator::make($input,[
            //'day'=>'required',
            'appointment_id'=>'required',
            'clinics_id'=>'required',
            'doctors_id'=>'required',
        ]);
        $doctor = Doctor::find($input['doctors_id']);
        if ($doctor->clinics_id != $input['clinics_id']) {
            return response()->json([
                'error' => 'clinics_id does not match the doctor\'s clinics_id',
                'doctor_clinics_id' => $doctor->clinics_id
            ], 400);
        }
        $duplicateAppointmentClinic = Appointment__Clinics::where('appointment_id', $input['appointment_id'])
        ->where('clinics_id', $input['clinics_id'])
        ->exists();;

        if( $duplicateAppointmentClinic){
            return response()->json('error',400);
        }
        else{
        if ($validator->fails()) {
            return response()->json($validator->errors()->tojson(),400);
        }}


    $appointmentC= new Appointment__Clinics();
    $appointmentC->fill([

        'appointment_id' =>$input['appointment_id'],
        'clinics_id' =>$input['clinics_id'],
        'doctors_id' =>$input['doctors_id'],
    ]);

     $appointmentC->save();
    return response()->json(['data' => $appointmentC, 'message' => 'appointmentadded sucessfully']);
 }


 //////////////////////////////////////////////////////  تابع اظهار جميع المواعيد
 public function getAppointments()
{

    $appointments = Appointment::all();


    return response()->json(['data' => $appointments, 'message' => 'appointments retrieved successfully']);
}




/////////////////////////////////  تابع اظهار ايام الذي يعمل فيها طبيب معين

public function showDoctorAppointments(Request $request, $Id)
{
    $appointments = DB::table('appointments')
        ->where('doctor_id', $Id)
        ->select('appointment_date', 'visit_type')
        ->distinct()
        ->get();
    $appointmentDetails = [];
    foreach ($appointments as $appointment) {
        $date = Carbon::parse($appointment->appointment_date);
        $dayName = $date->dayName;

        $appointmentDetails[] = [
            'date' => $date->toDateString(),
            'day_name' => $dayName,
            'visit_type' => $appointment->visit_type
        ];
    }
    $appointmentDetails = array_unique($appointmentDetails, SORT_REGULAR);

    return response()->json($appointmentDetails);
}

/////////////////////////////////// /////////////////    تابع اظهار مواعيد المتاعة لطبيب معين خلال يوم معين

public function showDoctorAppointmentsByDay(Request $request, $Id)
{
    $selectedDate = $request->input('date');
    $selectedDay = Carbon::parse($selectedDate)->dayName;
    $appointments = DB::table('appointments')
        ->leftJoin('user_appointments', 'appointments.id', '=', 'user_appointments.appointment_id')
        ->where('appointments.doctor_id', $Id)
        ->whereDate('appointments.appointment_date', $selectedDate)
        ->where('appointments.visit_type', 'center')
        ->select('appointments.id', 'appointments.appointment_date', 'appointments.visit_type', 'appointments.time_slots', 'user_appointments.status')
        ->get();
    $appointmentDetails = [];
    $processedTimes = [];

    foreach ($appointments as $appointment) {
        $timeSlots = json_decode($appointment->time_slots, true);

        foreach ($timeSlots as $slot) {
            $startTime = strtotime($slot['start_time']);
            $endTime = strtotime($slot['end_time']);
            $isBooked = $slot['is_booked'];
            if (!in_array($slot['start_time'], $processedTimes) && !$isBooked) {
                $appointmentDetails[] = [
                    'id' => $appointment->id,
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'is_booked' => $isBooked,
                ];
                $processedTimes[] = $slot['start_time'];
            }
        }
    }

    return response()->json($appointmentDetails);
}


////////////////////////////////////////////////////////////////////////////    تابع حجز موعد للمركز
public function bookAppointment(Request $request, $appointmentId)
{
    $appointment = Appointment::find($appointmentId);
    if (!$appointment) {
        return response()->json(['error' => 'الموعد غير موجود'], 404);
    }
    $selectedTime = $request->input('selected_time');
    $status = "no_show";

    $timeSlots = json_decode($appointment->time_slots, true);
    $slotFound = false;
    foreach ($timeSlots as &$slot) {
        if ($slot['start_time'] == $selectedTime) {
            $slotFound = true;
            if ($slot['is_booked'] == true) {
                return response()->json(['error' => 'الوقت المحدد محجوز بالفعل'], 400);
            }

            $slot['is_booked'] = true;
            break;
        }
    }
    if (!$slotFound) {
        return response()->json(['error' => 'الوقت المحدد غير صالح'], 400);
    }
    $user_id = auth()->user()->id;
    $patient_id= DB::table('patients')
    ->where('user_id', $user_id)
    ->value('id');
    $appointment->time_slots = json_encode($timeSlots);
    $appointment->save();
    $userAppointment = new User_Appointment();
    $userAppointment->patient_id = $patient_id;
    $userAppointment->appointment_id = $appointmentId;
    $userAppointment->appointment_time = Carbon::parse($appointment->appointment_date)->format('Y-m-d') . ' ' . $selectedTime;
    $userAppointment->status = $status;
    $userAppointment->save();
    $appointmentDateTime = Carbon::parse($appointment->appointment_date)->format('Y-m-d') . ' ' . $selectedTime;
    $message = 'تم حجز الموعد بنجاح في ' . $appointmentDateTime;
    ################################################### Notification ##########################################################
    $doctor = Doctor::find($appointment->doctor_id);
    $user = User::find($doctor->user_id);
    (new Notification)->store(1,"Appointment On: ".$appointment->appointment_date." at: ".$selectedTime ,$user->id);
    ############################################################################################################################

    return response()->json(['message' => $message], 200);  return response()->json(['message' => $message], 200);
}


///////////////////////////////////////////////////// عرض  مواعيد المتاحة للزيارة المنزلية لدكتور معين
public function showDoctorAppointmenthome(Request $request, $Id)
{
    $selectedDate = $request->input('date');
    $selectedDay = Carbon::parse($selectedDate)->dayName;
    $appointments = DB::table('appointments')
        ->join('appointment__clinics', 'appointments.id', '=', 'appointment__clinics.appointment_id')
        ->leftJoin('user_appointments', 'appointments.id', '=', 'user_appointments.appointment_id')
        ->where('appointment__clinics.doctors_id', $Id)
        ->whereDate('appointments.the_beginning_of_the_appointment', $selectedDate)
        ->where('appointments.visit_type', 'home')
        ->select('appointments.id', 'appointments.the_beginning_of_the_appointment', 'appointments.the_end_of_the_appointment', 'appointments.visit_type', 'appointments.duration', 'user_appointments.status', 'appointments.time_slots')
        ->get();

    $appointmentDetails = [];

    foreach ($appointments as $appointment) {
        $start_time = date('h:i A', strtotime($appointment->the_beginning_of_the_appointment));
        $end_time = date('h:i A', strtotime($appointment->the_end_of_the_appointment));

        $appointmentDetails[] = [
            'id' => $appointment->id,
            'start_time' => $start_time,
            'end_time' => $end_time,
        ];
    }

    return response()->json($appointmentDetails);
}





    public function bookAppointmenthome(Request $request, $appointmentId)
    {
        $appointment = Appointment::find($appointmentId);

        if ($appointment->visit_type !== 'home') {
            return response()->json(['error' => 'نوع الزيارة غير متوافق. يجب أن تكون الزيارة منزلية.'], 400);
        }

        $selectedTime = $request->input('selected_time');
        $status = "attended";
        $timeSlots = json_decode($appointment->time_slots, true);

        if (isset($timeSlots[$selectedTime]) && !$timeSlots[$selectedTime]['is_booked']) {
            $timeSlots[$selectedTime]['is_booked'] = true;
            $appointment->time_slots = json_encode($timeSlots);
            $appointment->save();
        } else {
            return response()->json(['error' => 'الوقت المحدد غير صالح أو محجوز بالفعل'], 400);
        }

        $appointmentClinic = $appointment->clinic;
        if (!$appointmentClinic) {
            return response()->json(['error' => 'لا يوجد عيادة مرتبطة بهذا الموعد'], 400);
        }

        if ($request->input('confirm_booking')) {
            $userAppointment = new User_Appointment();
            $userAppointment->user_id = auth()->user()->id;
            $userAppointment->appcl_id = $appointmentClinic->id;
            $userAppointment->appointment_id = $appointmentId;
            $userAppointment->appointment_time = Carbon::parse($appointment->the_beginning_of_the_appointment)->format('Y-m-d') . ' ' . $selectedTime;
            $userAppointment->status = $status;
            $userAppointment->save();

            $visitInfo = new Visit_Infos([
                'first_name' => $request->input('first_name'),
                'father_name' => $request->input('father_name'),
                'last_name' => $request->input('last_name'),
                'age' => $request->input('age'),
                'address' => $request->input('address'),
                'phone_number' => $request->input('phone_number'),
                'user_appointment_id' => $userAppointment->id
            ]);
            $userAppointment->visitInfo()->save($visitInfo);

            $appointmentDateTime = Carbon::parse($appointment->the_beginning_of_the_appointment)->format('Y-m-d') . ' ' . $selectedTime;
            $message = 'تم حجز الموعد بنجاح في ' . $appointmentDateTime;
            return response()->json(['message' => $message], 200);
        } else {
            return response()->json(['message' => 'يرجى تأكيد الحجز وإدخال المعلومات المطلوبة'], 400);
        }
    }



    //////////////////////////////////////////////// عرض المواعيد المحجوزة لمريض معين
    public function getUserAppointments()
    {
        $user_id = auth()->user()->id;
        $patient_id = DB::table('patients')
            ->where('user_id', $user_id)
            ->value('id');

        if (!$patient_id) {
            return response()->json(['error' => 'Patient not found'], 404);
        }
        $userAppointments = User_Appointment::with(['appointment.doctor'])
            ->where('patient_id', $patient_id)
            ->where('status', 'no_show')
            ->get();
        $appointments = $userAppointments->map(function ($userAppointment) {
            $appointmentDate = Carbon::parse($userAppointment->appointment_time);
            return [
                'appointment_date' => $appointmentDate->format('Y-m-d'),
                'day_of_week' => $appointmentDate->format('l'),
                'doctor_first_name' => $userAppointment->appointment->doctor->first_name,
                'doctor_last_name' => $userAppointment->appointment->doctor->last_name,
                'appointment_id' => $userAppointment->appointment->id,
                'price' => $userAppointment->appointment->doctor->clinics->price,
                'appointment_status' => $userAppointment->Astatus,
                'appointment_time' => $appointmentDate->format('H:i:s'),
            ];
        });
        return response()->json($appointments);

    }


   ////////////////////////////////////////////////////// تابع الغاء موعد

   public function cancelAppointment(Request $request, $appointmentId)
{
    $appointment = Appointment::find($appointmentId);
    if (!$appointment) {
        return response()->json(['error' => 'الموعد غير موجود'], 404);
    }
    $selectedTime = $request->input('selected_time');
    $timeSlots = json_decode($appointment->time_slots, true);

    if (!is_array($timeSlots)) {
        return response()->json(['error' => 'الوقت المحدد غير صالح'], 400);
    }

    $slotFound = false;
    foreach ($timeSlots as &$slot) {
        if ($slot['start_time'] === $selectedTime) {
            $slotFound = true;
            if ($slot['is_booked']) {
                $slot['is_booked'] = false;
                $appointment->time_slots = json_encode($timeSlots);
                $appointment->save();
                break;
            } else {
                return response()->json(['error' => 'الوقت المحدد غير محجوز'], 400);
            }
        }
    }

    if (!$slotFound) {
        return response()->json(['error' => 'الوقت المحدد غير صالح'], 400);
    }

    $user_id = auth()->user()->id;
    $patient_id = DB::table('patients')
        ->where('user_id', $user_id)
        ->value('id');

    if (!$patient_id) {
        return response()->json(['error' => 'Patient not found'], 404);
    }
    $userAppointment = User_Appointment::with('appointment.doctor.clinics')->where('patient_id', $patient_id)
        ->where('appointment_id', $appointmentId)
        ->where('appointment_time', Carbon::parse($appointment->appointment_date)->format('Y-m-d') . ' ' . $selectedTime)
        ->where('status', 'no_show')
        ->first();

    if ($userAppointment) {
        $now = Carbon::now();
        $center = Center::first();
        if ($now->lt(Carbon::parse($userAppointment->appointment_time)) && $userAppointment->Astatus == 1){
            $wallet = Wallet::where('user_id',$user_id)->first();
            if ($wallet){
                $amount = $userAppointment->appointment->doctor->clinics->price;
                $wallet->balance += $amount;
                $wallet->save();
                $center->balance -= $amount;
                $center->save();
                Payment::create([
                    'type' => 0,
                    'user_id' => $user_id,
                    'amount' => $amount,
                    'operation' => 'refund',
                    'date' => Carbon::now(),
                ]);
            }
        }elseif ($now->equalTo(Carbon::parse($userAppointment->appointment_time)) && $userAppointment->Astatus == 1){
            $wallet = Wallet::where('user_id',$user_id)->first();
            if ($wallet){
                $amount = $userAppointment->appointment->doctor->clinics->price;
                $wallet->balance += $amount / 2;
                $wallet->save();
                $center->balance -= $amount / 2;
                $center->save();
                Payment::create([
                    'type' => 0,
                    'user_id' => $user_id,
                    'amount' => $amount / 2,
                    'operation' => 'refund',
                    'date' => Carbon::now(),
                ]);
            }
        }
        $userAppointment->delete();
        ################################################### Notification ##########################################################
        $doctor = Doctor::find($appointment->doctor_id);
        $user = User::find($doctor->user_id);
        (new Notification)->store(2,"Appointment On: ".$appointment->appointment_date." at: ".$selectedTime ,$user->id);
        ############################################################################################################################
        $message = 'تم إلغاء الحجز بنجاح';
        return response()->json(['message' => $message], 200);
    } else {
        return response()->json(['error' => 'لايمكنك الغاء الحجز'], 400);
    }
}


   /////////////////////////////////// تابع عرض المواعيد المزارة ليوزر




   public function getUserAppointmentsAttended()
   {
       $user_id = auth()->user()->id;
       $patient_id = DB::table('patients')
           ->where('user_id', $user_id)
           ->value('id');

       if (!$patient_id) {
           return response()->json(['error' => 'Patient not found'], 404);
       }

       $patient = Patient::with([
           'userAppointments.appointment',
           'userAppointments.appointment.doctor',
       ])->find($patient_id);

       if (!$patient) {
           return response()->json(['error' => 'Patient not found'], 404);
       }

       $appointments = $patient->userAppointments->filter(function ($userAppointment) {
           return $userAppointment->status === "attended";
       })->map(function ($userAppointment) {
           $appointmentDate = Carbon::parse($userAppointment->appointment_time);
           return [
               'appointment_date' => $appointmentDate->format('Y-m-d'),
               'day_of_week' => $appointmentDate->format('l'),
               'doctor_first_name' => $userAppointment->appointment->doctor->first_name,
               'doctor_last_name' => $userAppointment->appointment->doctor->last_name,
               'appointment_id' => $userAppointment->appointment_id,
               'appointment_time' => $appointmentDate->format('H:i:s'),
           ];
       });
       return response()->json($appointments);}



/////////////////////////////////////  عرض جميع المواعيد المحجوزة
public function showAllBookedAppointments()
{
    $appointments = User_Appointment::with([
        'appointment',
        'appointment.doctor',
        'patient'
    ])->get();

    $appointmentDetails = [];

    foreach ($appointments as $userAppointment) {
        $appointmentDate = Carbon::parse($userAppointment->appointment_time);
        $appointmentDetails[] = [
            'appointment_date' => $appointmentDate->format('Y-m-d'),
            'day_of_week' => $appointmentDate->format('l'),
            'appointment_time' => $appointmentDate->format('H:i:s'),
            'doctor_name' => $userAppointment->appointment->doctor->first_name . ' ' . $userAppointment->appointment->doctor->last_name,
            'clinic_name' => $userAppointment->appointment->doctor->clinics->clinic_name,
            'user_name' => $userAppointment->patient->first_name . ' ' . $userAppointment->patient->father_name . ' ' . $userAppointment->patient->last_name,
            'user_appointment_id' => $userAppointment->id,
        ];
    }

    return response()->json($appointmentDetails);
}


 ///////////////////////////////////////////////

 public function showDoctorAppointmentsCenter(Request $request, $Id)
 {
     $appointments = DB::table('appointments')
         ->where('doctor_id', $Id)
         ->where('visit_type', 'center')
         ->distinct()
         ->get(['appointment_date', 'visit_type']);
     $appointmentDetails = [];
     foreach ($appointments as $appointment) {
         $date = Carbon::parse($appointment->appointment_date);
         $dayName = $date->dayName;
         $appointmentDetails[] = [
             'date' => $date->toDateString(),
             'day_name' => $dayName,
         ];
     }
     return response()->json($appointmentDetails);
 }




/////////////////////////////////////////////////////////////////
public function showDoctorAppointmentsByDay2(Request $request, $Id)
{
    $selectedDate = $request->input('date');

    // Fetch all appointments for the given doctor on the selected date
    $appointments = DB::table('appointments')
        ->leftJoin('user_appointments', 'appointments.id', '=', 'user_appointments.appointment_id')
        ->leftJoin('patients', 'user_appointments.patient_id', '=', 'patients.id')
        ->leftJoin('records', 'patients.id', '=', 'records.patient_id') // ارتباط جدول الريكوردات عبر patient_id
        ->leftJoin('center_visits', 'user_appointments.id', '=', 'center_visits.userappointment_id') // Join center_visits to check if visit occurred
        ->where('appointments.doctor_id', $Id)
        ->whereDate('appointments.appointment_date', $selectedDate)
        ->where('appointments.visit_type', 'center')
        ->select(
            'user_appointments.id as user_appointment_id',
            'appointments.id as appointment_id',
            'user_appointments.appointment_time',
            'patients.first_name as patient_firstname',
            'patients.father_name as patient_fathername',
            'patients.last_name as patient_lastname',
            'appointments.time_slots',
            'records.id as record_id', // استعلم عن id للريكورد في جدول الريكوردات
            DB::raw('CASE WHEN center_visits.id IS NOT NULL THEN true ELSE false END as is_visited') // Check if visit happened
        )
        ->get();

    // Check if appointments exist for the selected date and doctor
    if ($appointments->isEmpty()) {
        return response()->json(['error' => 'No appointments found for the selected date and doctor.'], 404);
    }

    // Decode time slots from the first appointment
    $timeSlots = json_decode($appointments->first()->time_slots, true);

    $appointmentDetails = [];

    foreach ($timeSlots as &$slot) {
        $isBooked = false;
        $patientfirst = null;
        $patientfather = null;
        $patientlast = null;
        $recordId = null;
        $userAppointmentId = null;
        $appointmentId = null;
        $isVisited = false;

        foreach ($appointments as $appt) {
            // Compare the appointment time with the slot start time
            $appointmentTime = date('H:i:s', strtotime($appt->appointment_time));
            $slotStartTime = date('H:i:s', strtotime($slot['start_time']));

            if ($appointmentTime == $slotStartTime) {
                $isBooked = true;
                $patientfirst = $appt->patient_firstname;
                $patientfather = $appt->patient_fathername;
                $patientlast = $appt->patient_lastname;
                $recordId = $appt->record_id ?? null;
                $userAppointmentId = $appt->user_appointment_id;
                $appointmentId = $appt->appointment_id;
                $isVisited = $appt->is_visited; // Get if visit occurred
                break;
            }
        }

        // Add appointment details
        $appointmentDetails[] = [
            'start_time' => date('g:i A', strtotime($slot['start_time'])), // Format start time to AM/PM
            'end_time' => date('g:i A', strtotime($slot['end_time'])),     // Format end time to AM/PM
            'is_booked' => $isBooked,
            'firstName' => $isBooked ? $patientfirst : null,
            'fatherName' => $isBooked ? $patientfather : null,
            'lastName' => $isBooked ? $patientlast : null,
            'record_id' => $recordId,
            'user_appointment_id' => $userAppointmentId,
            'appointment_id' => $appointmentId,
            'is_visited' => $isVisited, // Add is_visited to response
        ];
    }

    return response()->json([
        'appointment_date' => $selectedDate,
        'visit_type' => 'center',
        'doctor_id' => $Id,
        'appointments' => $appointmentDetails,
    ]);
}
// public function showDoctorAppointmentsByDay2(Request $request, $Id)
// {
//     $selectedDate = $request->input('date');

//     // Fetch all appointments for the given doctor on the selected date
//     $appointments = DB::table('appointments')
//         ->leftJoin('user_appointments', 'appointments.id', '=', 'user_appointments.appointment_id')
//         ->leftJoin('patients', 'user_appointments.patient_id', '=', 'patients.id')
//         ->leftJoin('records', 'patients.id', '=', 'records.patient_id') // ارتباط جدول الريكوردات عبر patient_id
//         ->where('appointments.doctor_id', $Id)
//         ->whereDate('appointments.appointment_date', $selectedDate)
//         ->where('appointments.visit_type', 'center')
//         ->select(
//             'user_appointments.id as user_appointment_id',
//             'appointments.id as appointment_id',
//             'user_appointments.appointment_time',
//             'patients.first_name as patient_firstname',
//             'patients.father_name as patient_fathername',
//             'patients.last_name as patient_lastname',
//             'appointments.time_slots',
//             'records.id as record_id' // استعلم عن id للريكورد في جدول الريكوردات
//         )
//         ->get();

//     // Assuming there is only one appointment record for the selected date and doctor
//     if ($appointments->isEmpty()) {
//         return response()->json(['error' => 'No appointments found for the selected date and doctor.'], 404);
//     }

//     $timeSlots = json_decode($appointments->first()->time_slots, true); // فك التسلسل الزمني لأول موعد للحصول على الكتل الزمنية

//     $appointmentDetails = [];

//     foreach ($timeSlots as &$slot) {
//         $isBooked = false;
//         $patientFullName = null;
//         $recordId = null; // تعيين record_id إلى null في البداية
//         $userAppointmentId = null;
//         $appointmentId = null;

//         foreach ($appointments as $appt) {
//             // Convert both times to the same format for comparison
//             $appointmentTime = date('H:i:s', strtotime($appt->appointment_time));
//             $slotStartTime = date('H:i:s', strtotime($slot['start_time']));

//             if ($appointmentTime == $slotStartTime) {
//                 $isBooked = true;
//                 $patientfirst = $appt->patient_firstname;
//                 $patientfather = $appt->patient_fathername;
//                 $patientlast = $appt->patient_lastname;
//                 $recordId = $appt->record_id ?? null; // حصول على record_id إذا وجد
//                 $userAppointmentId = $appt->user_appointment_id;
//                 $appointmentId = $appt->appointment_id;
//                 break;
//             }
//         }

//         $appointmentDetails[] = [
//             'start_time' => date('g:i A', strtotime($slot['start_time'])), // Format start time to "AM/PM"
//             'end_time' => date('g:i A', strtotime($slot['end_time'])),     // Format end time to "AM/PM"
//             'is_booked' => $isBooked,
//             'firstName' => $isBooked ?  $patientfirst : null,
//             'fatherName' => $isBooked ?  $patientfather : null,
//             'lastName' => $isBooked ?  $patientlast : null,
//             'record_id' => $recordId, // إضافة record_id إلى التفاصيل
//             'user_appointment_id' => $userAppointmentId, // إضافة user_appointment_id إلى التفاصيل
//             'appointment_id' => $appointmentId, // إضافة appointment_id إلى التفاصيل
//         ];
//     }

//     return response()->json([
//         'appointment_date' => $selectedDate,
//         'visit_type' => 'center',
//         'doctor_id' => $Id,
//         'appointments' => $appointmentDetails,
//     ]);
// }







public function bookAppointmentSecretary(Request $request,$appointmentId)
{

    $firstName = $request->input('first_name');
    $fatherName = $request->input('father_name');
    $lastName = $request->input('last_name');
    $selectedTime = $request->input('selected_time');
    $patient = DB::table('patients')
        ->where('first_name', $firstName)
        ->where('father_name', $fatherName)
        ->where('last_name', $lastName)
        ->first();
    if (!$patient) {
        $patientId = DB::table('patients')->insertGetId([
            'first_name' => $firstName,
            'father_name' => $fatherName,
            'last_name' => $lastName,
            'user_id' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    } else {
        $patientId = $patient->id;
    }

    $appointment = Appointment::find($appointmentId);


    if (!$appointment) {
        return response()->json(['error' => 'الموعد غير موجود'], 404);
    }
    $timeSlots = json_decode($appointment->time_slots, true);
    $slotFound = false;

    foreach ($timeSlots as &$slot) {
        if ($slot['start_time'] == $selectedTime) {
            $slotFound = true;
            if ($slot['is_booked']) {
                return response()->json(['error' => 'الوقت المحدد محجوز بالفعل'], 400);
            }

            $slot['is_booked'] = true;
            break;
        }
    }
    if (!$slotFound) {
        return response()->json(['error' => 'الوقت المحدد غير صالح'], 400);
    }
    $appointment->time_slots = json_encode($timeSlots);
    $appointment->save();

    $userAppointment = new User_Appointment();
    $userAppointment->patient_id = $patientId;
    $userAppointment->appointment_id = $appointmentId;
    $userAppointment->appointment_time = Carbon::parse($appointment->appointment_date)->format('Y-m-d') . ' ' . $selectedTime;
    $userAppointment->status = 'no_show';
    $userAppointment->save();
    $appointmentDateTime = Carbon::parse($appointment->appointment_date)->format('Y-m-d') . ' ' . $selectedTime;
    $message = 'تم حجز الموعد بنجاح في ' . $appointmentDateTime;
    return response()->json(['message' => $message], 200);
}

}












