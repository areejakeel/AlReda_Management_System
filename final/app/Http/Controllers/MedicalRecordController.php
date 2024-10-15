<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Record;
use App\Models\Patient;
use App\Models\Referral;
use App\Models\User_Appointment;
use App\Models\Doctor;
use App\Models\Clinics;
use App\Models\RecordDoctor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MedicalRecordController extends Controller

{


public function storeRecord(Request $request)
    {

            $firstName = $request->input('first_name');
            $lastName = $request->input('last_name');
            $fatherName = $request->input('father_name');

            $existingRecord = Record::where('first_name', $firstName)
                                    ->where('last_name', $lastName)
                                    ->where('father_name', $fatherName)
                                    ->exists();

            if ($existingRecord) {
                return response()->json([
                    'message' => 'Names were duplicated in the medical record'
                ]);
            }

            $record = new Record();
            $X_ray_files = [];
            $analysis_files = [];

            if ($request->hasFile('X_ray')) {
                foreach ($request->file('X_ray') as $X_ray) {
                    if ($X_ray->isValid()) {
                        $filename = time() . '_' . $X_ray->getClientOriginalName();
                        $X_ray->move(public_path('image'), $filename);
                        $X_ray_files[] = url('/image/' . $filename);
                    }
                }
            }

            if ($request->hasFile('analysis')) {
                foreach ($request->file('analysis') as $analysis) {
                    if ($analysis->isValid()) {
                        $filename = time() . '_' . $analysis->getClientOriginalName();
                        $analysis->move(public_path('image'), $filename);
                        $analysis_files[] = url('/image/' . $filename);
                    }
                }
            }
            $patient = Patient::where('first_name', $firstName)
            ->where('last_name', $lastName)
            ->where('father_name', $fatherName)
            ->first();

        if ($patient) {
            $record->patient_id = $patient->id;
        } else {
            $patient = new Patient();
            $patient->first_name = $firstName;
            $patient->father_name = $fatherName;
            $patient->last_name = $lastName;
            $patient->user_id = null;
            $patient->save();
            $record->patient_id = $patient->id;

        }

            $record->X_ray = json_encode($X_ray_files)?:null;
            $record->analysis = json_encode($analysis_files)?:null;
            $record->first_name = $firstName;
            $record->father_name = $fatherName;
            $record->last_name = $lastName;
            $record->gender = $request->input('gender');
            $record->social_status = $request->input('social_status');
            $record->birthdate = $request->input('birthdate');
            $record->job = $request->input('job');
            $record->address = $request->input('address');
            $record->moblie_num = $request->input('moblie_num');
            $record->Blood_type = $request->input('Blood_type');
            $record->Previous_Opertios = $request->input('Previous_Opertios');
            $record->AllergyToMedication = $request->input('AllergyToMedication');
            $record->Chronic_Diseases = $request->input('Chronic_Diseases');
            $record->_first_name = $request->input('_first_name');
            $record->_last_name = $request->input('_last_name');
            $record->phone = $request->input('phone');
            $record->save();

            return response()->json([
                'data' => $record,
                'message' => 'Record added successfully']);
        }

        public function updateRecord(Request $request, int $id)
        {
            $validator = Validator::make($request->all(), [

                'first_name' => 'required|string',
                'father_name' => 'required|string',
                'last_name' => 'required|string',
                'gender'=>'required',
                'birthdate'=>'required|date',
                'job' => 'required|string',
                'address' => 'required|string',
                'moblie_num' => 'required|regex:/^09[0-9]{8}$/',
                'Blood_type'=>'required',
                'Previous_Opertios' => 'required|string',
                'AllergyToMedication'=>'required|string',
                'Chronic_Diseases'=>'required|string',
                'phone' => 'required|regex:/^09[0-9]{8}$/',
                '_first_name'=>'required|string',
                '_last_name'=>'required|string',
                  ]);
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $record = Record::find($id);
            if (!$record) {
                return response()->json('No such Profile found', 404);
            }

            $record->update([
                'first_name' => $request->first_name,
                'father_name' => $request->father_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'social_status' => $request->social_status,
                'birthdate' => $request->birthdate,
                'job' => $request->job,
                'address' => $request->address,
                'moblie_num' => $request->moblie_num,
                'Blood_type' => $request->Blood_type,
                'Previous_Opertios' => $request->Previous_Opertios,
                'AllergyToMedication'=>$request->AllergyToMedication,
                'Chronic_Diseases'=>$request->Chronic_Diseases,
                '_first_name'=>$request->_first_name,
                '_last_name'=>$request->_last_name,
                'phone'=>$request->phone
            ]);
//            dd($request->all());

            return response()->json([
                "message" => "This profile updated successfully",
                "data" => $record
            ], 200);
        }

        public function deleteRecord($id)
        {
            // Find the  record
            $record = Record::findOrFail($id);

            // Delete the associated  record

            $record->delete();

            return response()->json([
                'message' => 'record deleted successfully',
            ], 200);
        }


public function storeRecordDoctor(Request $request)
{
    $recordId = $request->input('record_id');
    $doctorId = $request->input('doctor_id');

    $record = Record::find($recordId);

    if (!$record) {
        return response()->json(['message' => 'Record not found'], 404);
    }

    $patient = $record->patient;

    if (!$patient) {
        return response()->json(['message' => 'Patient not found'], 404);
    }

    $patientId = $patient->id;

    // التحقق من وجود موعد للمريض مع الطبيب
    $userAppointment = DB::table('user_appointments')
        ->join('appointments', 'user_appointments.appointment_id', '=', 'appointments.id')
        ->where('user_appointments.patient_id', $patientId)
        ->where('appointments.doctor_id', $doctorId)
        ->first();

    if ($userAppointment) {
        $recordDoctor = new RecordDoctor();
        $recordDoctor->records_id = $recordId;
        $recordDoctor->doctors_id = $doctorId;
        $recordDoctor->save();

        return response()->json(['message' => 'Record-doctor association saved successfully'], 201);
    } else {
        return response()->json(['message' => 'The doctor is not associated with the registry'], 200);
    }
}



                 ///////////////////////////////////////////////////////////////////////////////////////
                 public function getPatientRecords()
                 {
                    $user_id = auth()->user()->id;
                    $patient_id = DB::table('patients')
                    ->where('user_id', $user_id)
                    ->value('id');
                     $patient = Patient::find($patient_id);
                     if (!$patient) {
                         return response()->json(['message' => 'Patient not found'], 404);
                     }
                     $records = Record::where('patient_id', $patient_id)->get();


                     return response()->json([
                         'message' => 'Patient records fetched successfully',
                         'records' => $records,
                         'patient_id'=>  $patient_id

                     ]);
                 }

  public function storeXrayAndAnalysis(Request $request, $id)
 {  $record = Record::find($id);
                   //$record = new Record();
 $X_ray_files =$record->X_ray? json_decode($record->X_ray,true):[];
 $analysis_files =$record->analysis? json_decode($record->analysis,true):[];

if ($request->hasFile('X_ray')) {
foreach ($request->file('X_ray') as $X_ray) {
if ($X_ray->isValid()) {
 $filename = time() . '_' . $X_ray->getClientOriginalName();
   $X_ray->move(public_path('image'), $filename);
  $X_ray_files[] = url('/image/' . $filename);
   }
  }
      }

 if ($request->hasFile('analysis')) {
 foreach ($request->file('analysis') as $analysis) {
 if ($analysis->isValid()) {
 $filename = time() . '_' . $analysis->getClientOriginalName();
  $analysis->move(public_path('image'), $filename);
    $analysis_files[] = url('/image/' . $filename);
                                 }
                             }
                         }

 $record->X_ray = json_encode($X_ray_files);
 $record->analysis = json_encode($analysis_files);
 // حفظ التغييرات في السجل
 $record->save();

  // إرجاع السجل المحدث
 return response()->json([
           'message' =>  $record,
                        // 'data' => $X_ray,aaa
                     //    'data2' => $filename,
            ], 200);

 }

public function showFiles($id){

    $record=Record::findOrFail($id);
    $X_rayFiles= json_decode($record->X_ray,true);
    $analysisFiles=json_decode($record->analysis,true);
     return response()->json([
        'X_ray'=>$X_rayFiles,
        'analysis'=>$analysisFiles
     ]);


    }
    public function showFiles2($id){

        $record=Record::findOrFail($id);
        $X_rayFiles= json_decode($record->X_ray,true);
        $analysisFiles=json_decode($record->analysis,true);
         return response()->json([
            'X_ray'=>$X_rayFiles,
            'analysis'=>$analysisFiles
         ]);


        }

        public function AddDescription(Request $request, $record_id)
                {
                    $user_id = auth()->user()->id;
                    $doctor_id = DB::table('doctors')
                   ->where('user_id', $user_id)
                   ->value('id');
                    $record = DB::table('records')
                        ->where('id', $record_id)
                        ->first();

                    if (!$record) {
                        return response()->json(['message' => 'Record not found'], 404);
                    }
                    $appointment = DB::table('user_appointments')
                        ->join('appointments', 'user_appointments.appointment_id', '=', 'appointments.id')
                        ->where('appointments.doctor_id', $doctor_id)
                        ->where('user_appointments.patient_id', $record->patient_id) // مطابقة المريض
                        ->first();

                    if (!$appointment) {
                        return response()->json(['message' => 'No appointment found for this patient with this doctor'], 404);
                    }
                    $doctor = Doctor::select('first_name', 'last_name')
                        ->where('id', $doctor_id)
                        ->first();

                    if (!$doctor) {
                        return response()->json(['message' => 'Doctor not found'], 404);
                    }
                    $doctorName = $doctor->first_name . ' ' . $doctor->last_name;
                    $description = $request->input('description');
                    $date = now()->toDateTimeString();

                    $newEntry = [
                        'description' => $description,
                        'Doctor Name' => $doctorName,
                        'date' => $date,
                    ];
                    $existingEntries = $record->description ? json_decode($record->description, true) : [];
                    $existingEntries[] = $newEntry;
                    DB::table('records')
                        ->where('id', $record->id)
                        ->update([
                            'description' => json_encode($existingEntries),
                        ]);

                    return response()->json([
                        'message' => 'Description added successfully',
                        'record' => $record,
                        'doctor name' => $doctorName,
                        'data for description' => $newEntry,
                    ]);
                }
           ////////////////////////////////////////////// إضافة إحالة
    public function AddReferral(Request $request) {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'record_id' => 'required',
            'doctor_id' => 'required',
            'notes' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ],422);
        }
        try {
            $doctor = Doctor::with('clinics')->where('user_id',$user->id)->first();
            Referral::create([
                'record_id' => $request->record_id,
                'doctor_id' => $request->doctor_id,
                'from_doctor' => $doctor->id,
                'date' => Carbon::now(),
                'notes' => $request->notes,
            ]);
            ################################################### Notification ##########################################################
            $doctorr = Doctor::with('clinics','user')->find($request->doctor_id);
            $record = Record::with('patient')->find($request->record_id);
            $useID =$record->patient->user->id;
            (new Notification)->store(4,"you have referral to: ".$doctorr->clinics->clinic_name ,$useID);
            (new Notification)->store(4,"you have referral from: ".$doctor->clinics->clinic_name ,$doctorr->user->id);
            ############################################################################################################################            return response()->json([
            return response()->json([
                'success' => true,
                'message' => "Referred Successfully.."
            ],200);
        }catch (Exception $exception){
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ],500);
        }
//
//            $record = DB::table('records')->where('id', $record_id)->first();
//            if (!$record) {
//                return response()->json(['message' => 'Record not found'], 404);
//            }
//            $patientId = DB::table('records')
//                ->where('id', $record_id)
//                ->value('patient_id'); // استخدم value بدلاً من select للحصول على قيمة مباشرة
//
//            $doctor = Doctor::with('clinics')->select('first_name', 'last_name', 'clinics_id')->where('id', $doctor_id)->first();
//            if (!$doctor) {
//                return response()->json(['message' => 'Doctor not found'], 404);
//            }
//            $clinicName = $doctor->clinics ? $doctor->clinics->clinic_name : null;
//
//            $appointment_time = DB::table('user_appointments')
//                ->where('patient_id', $patientId)
//                ->value('appointment_time'); // استخدم value بدلاً من select للحصول على قيمة مباشرة
//                $date = date("Y-m-d", strtotime($appointment_time));
//            $appointmentExists = DB::table('record_dectors')
//                ->where('records_id', $record_id)
//                ->where('doctors_id', $doctor_id)
//                ->exists();
//
//            $Referrals = $record->Referrals ? json_decode($record->Referrals, true) : [];
//            $referralExists = false;
//            foreach ($Referrals as $referral) {
//                if ($referral['doctor_id'] == $doctor_id) {
//                    $referralExists = true;
//                    break;
//                }
//            }
//
//            if (!$appointmentExists && !$referralExists) {
//                return response()->json(['message' => 'Doctor not authorized to add description'], 403);
//            }
//
//            // الحصول على الطبيب المحال إليه
//            $referredDoctorId = $request->input('referred_doctor_id');
//            $referredDoctor = Doctor::select('first_name', 'last_name')->where('id', $referredDoctorId)->first();
//            if (!$referredDoctor) {
//                return response()->json(['message' => 'Referred doctor not found'], 404);
//            }
//
//            // إضافة الإحالة إلى السجل
//            $Referrals[] = [
//                'doctor_id' => $referredDoctorId,
//                'doctor_name' => $referredDoctor->first_name . ' ' . $referredDoctor->last_name,
//                'referring_doctor_id' => $doctor_id,
//                'clinic_name' => $clinicName,
//                'time' => $date // تأكد من أن الوقت عبارة عن قيمة نصية أو تنسيق مناسب للتخزين
//            ];
//
//            // تحديث السجل بالإحالات الجديدة
//            DB::table('records')->where('id', $record_id)->update(['Referrals' => json_encode($Referrals)]);
//
//            return response()->json([
//                'message' => 'Referral added successfully',
//                'doctor_name' => $doctor->first_name . ' ' . $doctor->last_name,
//                'referred_doctor' => [
//                    'id' => $referredDoctorId,
//                    'name' => $referredDoctor->first_name . ' ' . $referredDoctor->last_name,
//                ],
//                'referring_doctor_id' => $doctor_id,
//            ]);
    }

        ////////////////////////////////////////////// عرض الاحالات عند دكتور معين


    public function GetReferralsForDoctor()
    {
        $user = auth()->user();

        try {
            $doctor = Doctor::where('user_id', $user->id)->firstOrFail();
            $referralss = Referral::with('record', 'forDoctor.clinics', 'fromDoctor.clinics')
                ->where('doctor_id', $doctor->id)->get();
            $referrals = collect();
            foreach ($referralss as $item) {
                $fromClinicName = optional(optional($item->fromDoctor)->clinics)->clinic_name ?? 'Unknown Clinic';
                $referrals->push([
                    'from_clinic' => $fromClinicName,
                    'date' => $item->date,
                    'notes' => $item->notes,
                    'patient_record' => $item->record
                ]);
            }
            return response()->json(['referrals' => $referrals], 200);
        } catch (Exception $exception) {
            // Return error response if something goes wrong
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }


//    // استعلام للحصول على جميع السجلات
//    $records = DB::table('records')->get(['id', 'Referrals']);
//
//    if ($records->isEmpty()) {
//        return response()->json(['message' => 'No referrals found for this doctor'], 404);
//    }
//
//    $formattedReferrals = [];
//    foreach ($records as $record) {
//        $record_id = $record->id;
//        $referralData = json_decode($record->Referrals, true);
//
//        if (is_array($referralData)) {
//            foreach ($referralData as $ref) {
//                if (isset($ref['doctor_id']) && ($ref['doctor_id'] == $doctor_id || (isset($ref['referred_doctor_id']) && $ref['referred_doctor_id'] == $doctor_id))) {
//                    $formattedReferrals[] = [
//                        'record_id' => $record_id,
//                        'doctor_id' => $ref['doctor_id'],
//                        'doctor_name' => $ref['doctor_name'],
//                        'referring_doctor_id' => $ref['referring_doctor_id'] ?? null,
//                        'clinic_name' => $ref['clinic_name'] ?? null,
//                        'time' => $ref['time'] ?? null,
//                    ];
//                }
//            }
//        }
//    }
//
//    if (empty($formattedReferrals)) {
//        return response()->json(['message' => 'No referrals found for this doctor'], 404);
//    }
//
//    // إرجاع البيانات بتنسيق JSON
//    return response()->json([
//        'message' => 'Referrals fetched successfully',
//        'doctor_id' => $doctor_id,
//        'referrals' => $formattedReferrals,
//    ]);
    }

}
