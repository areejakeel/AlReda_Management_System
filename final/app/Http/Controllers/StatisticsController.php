<?php

namespace App\Http\Controllers;
use App\Models\Doctor;
use App\Models\Record;
use App\Models\Patient;
use App\Models\CenterVisit;
use App\Models\User;
use Carbon\CarbonInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class StatisticsController extends Controller
{
    public function getMonthlyVisitStatistics()
{

    $currentYear = date('Y');
    $totalVisits = DB::table('center_visits')
        ->whereYear('visit_time', $currentYear)
        ->count();
    $visitStatistics = DB::table('center_visits')
        ->select(DB::raw('MONTH(visit_time) as month'), DB::raw('count(*) as visit_count'))
        ->whereYear('visit_time', $currentYear)
        ->groupBy(DB::raw('MONTH(visit_time)'))
        ->orderBy('month')
        ->get();
    $formattedStatistics = [];
    for ($month = 1; $month <= 12; $month++) {
        $monthData = $visitStatistics->firstWhere('month', $month);
        $visitCount = $monthData ? $monthData->visit_count : 0;
        $percentage = $totalVisits > 0 ? ($visitCount / $totalVisits) * 100 : 0;

        $formattedStatistics[] = [
            'month' => $month,
            'visit_percentage' => round($percentage, 2),
        ];
    }

    return response()->json($formattedStatistics);
}

    public function getAgeGroupStatistics()
{
    $currentYear = date('Y');
    $totalRecords = DB::table('records')->count();
    $ageGroups = DB::table('records')
        ->select(DB::raw('YEAR(CURDATE()) - YEAR(birthdate) AS age'), DB::raw('COUNT(*) as count'))
        ->groupBy(DB::raw('YEAR(CURDATE()) - YEAR(birthdate)'))
        ->get()
        ->groupBy(function ($item) {
            $age = $item->age;
            if ($age < 15) {
                return '0-14';
            } elseif ($age >= 15 && $age <= 30) {
                return '15-30';
            } elseif ($age >= 31 && $age <= 60) {
                return '31-60';
            } else {
                return '61+';
            }
        })
        ->map(function ($group) {
            return $group->sum('count');
        });
        $ageRanges = [
            '0-14' => 'Children',
            '15-30' => 'Youth',
            '31-60' => 'Adults',
            '61+' => 'Seniors'
        ];

    // Format statistics
    $formattedStatistics = [];
    foreach ($ageRanges as $range => $name) {
        $groupCount = $ageGroups->get($range, 0);
        $percentage = $totalRecords > 0 ? ($groupCount / $totalRecords) * 100 : 0;
        $formattedStatistics[] = [
            'age_group' => $range,
            'name' => $name,
            'count' => $groupCount,
            'percentage' => round($percentage, 2)
        ];
    }

    return response()->json($formattedStatistics);
}

    public function getMonthlyPatientStatistics()
{
    $currentYear = date('Y');
    $totalPatients = DB::table('patients')
        ->whereYear('created_at', $currentYear)
        ->count();
    $patientStatistics = DB::table('patients')
        ->select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as patient_count'))
        ->whereYear('created_at', $currentYear)
        ->groupBy(DB::raw('MONTH(created_at)'))
        ->orderBy('month')
        ->get();

    $formattedStatistics = [];
    for ($month = 1; $month <= 12; $month++) {
        $monthData = $patientStatistics->firstWhere('month', $month);
        $patientCount = $monthData ? $monthData->patient_count : 0;
        $percentage = $totalPatients > 0 ? ($patientCount / $totalPatients) * 100 : 0;

        $formattedStatistics[] = [
            'month' => $month,
            'patient_percentage' => round($percentage, 2),
        ];
    }

    return response()->json($formattedStatistics);
}

 /////////////////////////////////////////////// تابع يجلب عدد الزوار
    public function getVisitCount()
{
    $visitCount = CenterVisit::count();
    $formattedVisitCount = $this->formatVisitCount($visitCount);

    return response()->json(['visit_count' => $formattedVisitCount], 200);
}

    private function formatVisitCount($count)
{
    if ($count >= 1000000) {
        return number_format($count / 1000000, 2) . 'M';  // Millions
    } elseif ($count >= 1000) {
        return number_format($count / 1000, 2) . 'K';  // Thousands
    } elseif ($count >= 100) {
        return number_format($count / 100, 2) . 'H';  // Hundreds
    } elseif ($count >= 10) {
        return number_format($count / 10, 2) . 'T';  // Tens
    } else {
        return $count . 'U';  // Units
    }
}

 //////////////////////////////////////جلب اكثر فئة ترددا


    public function getMostFrequentAge()
{
    $ageGroups = DB::table('records')
        ->select(DB::raw('YEAR(CURDATE()) - YEAR(birthdate) AS age'), DB::raw('COUNT(*) as count'))
        ->groupBy(DB::raw('YEAR(CURDATE()) - YEAR(birthdate)'))
        ->get()
        ->mapToGroups(function ($item) {
            $age = $item->age;
            $count = $item->count;
            if ($age < 15) {
                return ['0-14' => $count];
            } elseif ($age >= 15 && $age <= 30) {
                return ['15-30' => $count];
            } elseif ($age >= 31 && $age <= 60) {
                return ['31-60' => $count];
            } else {
                return ['61+' => $count];
            }
        });
    $ageGroups = $ageGroups->map(function ($group) {
        return $group->sum();
    });
    $ageRanges = [
        '0-14' => 'Children',
        '15-30' => 'Youth',
        '31-60' => 'Adults',
        '61+' => 'Seniors'
    ];
    $mostFrequentAgeGroup = $ageGroups->sortDesc()->keys()->first();

    if ($mostFrequentAgeGroup) {
        return $ageRanges[$mostFrequentAgeGroup];
    } else {
        return 'No age groups found.';
    }
}

////////////////////////////////////////////////// تابع يظهر عدد المستخدمين
    public function getPatientCount()
{
    $patientCount = Patient::count();
    $formattedPatientCount = $this->formatCount($patientCount);

    return response()->json(['patient_count' => $formattedPatientCount], 200);
}

    private function formatCount($count)
{
    if ($count >= 1000000) {
        return number_format($count / 1000000, 2) . 'M';  // Millions
    } elseif ($count >= 1000) {
        return number_format($count / 1000, 2) . 'K';  // Thousands
    } elseif ($count >= 100) {
        return number_format($count / 100, 2) . 'H';  // Hundreds
    } elseif ($count >= 10) {
        return number_format($count / 10, 2) . 'T';  // Tens
    } else {
        return $count . 'U';  // Units
    }
}

///////////////////////////////////////////////////////عدد الزيارات عند دكتور معين
    public function getVisitCountByDoctor($doctorId)
{
    $visitCount = CenterVisit::where('doctor_id', $doctorId)->count();
    $formattedVisitCount = $this->formatVisitCount($visitCount);

    return response()->json(['visit_count' => $formattedVisitCount], 200);
}

//    public function doctorStatistics():JsonResponse{
//        $user = auth()->user();
//        try {
//            $doctor = Doctor::where('user_id',$user->id)->first();
//            ######################################## Monthly Visits ###########################################
//            $currentYear = date('Y');
//            $totalVisits = DB::table('center_visits')
//                ->leftJoin('user_appointments', 'center_visits.userappointment_id', '=', 'user_appointments.id')
//                ->leftJoin('appointments', 'user_appointments.appointment_id', '=', 'appointments.id')
//                ->whereYear('center_visits.visit_time', $currentYear)
//                ->where(function ($query) use ($doctor) {
//                    $query->where('appointments.doctor_id', $doctor->id)
//                        ->orWhereNull('appointments.doctor_id');
//                })->count();
//            $visitStatistics = DB::table('center_visits')
//                ->leftJoin('user_appointments', 'center_visits.userappointment_id', '=', 'user_appointments.id')
//                ->leftJoin('appointments', 'user_appointments.appointment_id', '=', 'appointments.id')
//                ->select(
//                    DB::raw('MONTH(center_visits.visit_time) as month'),
//                    DB::raw('COUNT(center_visits.id) as visit_count')
//                )->whereYear('center_visits.visit_time', $currentYear)
//                ->where(function ($query) use ($doctor) {
//                    $query->where('appointments.doctor_id', $doctor->id)
//                        ->orWhereNull('appointments.doctor_id');
//                })->groupBy(DB::raw('MONTH(center_visits.visit_time)'))
//                ->orderBy('month')->get();
//            $monthlystatistics = [];
//            for ($month = 1; $month <= 12; $month++) {
//                $monthData = $visitStatistics->firstWhere('month', $month);
//                $visitCount = $monthData ? $monthData->visit_count : 0;
//                $percentage = $totalVisits > 0 ? ($visitCount / $totalVisits) * 100 : 0;
//
//                $monthlystatistics[] = [
//                    'month' => $month,
//                    'visit_percentage' => round($percentage, 2),
//                ];
//            }
//            #################################################################################################
//            #################################### Weekly Appointments ########################################
//            $currentDate = now();
//            $startOfWeek = $currentDate->startOfWeek();
//            $endOfWeek = $currentDate->endOfWeek();
//            $totalAppointments = DB::table('user_appointments')
//                ->leftJoin('appointments', 'user_appointments.appointment_id', '=', 'appointments.id')
//                ->whereBetween('user_appointments.appointment_time', [$startOfWeek, $endOfWeek])
//                ->where(function ($query) use ($doctor) {
//                    $query->where('appointments.doctor_id', $doctor->id)
//                        ->orWhereNull('appointments.doctor_id');
//                })->count();
//            $appointmentsStatistics = DB::table('user_appointments')
//                ->leftJoin('appointments', 'user_appointments.appointment_id', '=', 'appointments.id')
//                ->select(
//                    DB::raw('DAYOFWEEK(user_appointments.appointment_time) as day'),
//                    DB::raw('COUNT(user_appointments.id) as visit_count')
//                )->whereBetween('user_appointments.appointment_time', [$startOfWeek, $endOfWeek])
//                ->where(function ($query) use ($doctor) {
//                    $query->where('appointments.doctor_id', $doctor->id)
//                        ->orWhereNull('appointments.doctor_id');
//                })->groupBy(DB::raw('DAYOFWEEK(user_appointments.appointment_time)'))
//                ->orderBy('day')->get();
//
//            $weeklystatistics = [];
//            for ($day = 1; $day <= 7; $day++) {
//                $dayData = $appointmentsStatistics->firstWhere('day', $day);
//                $visitCount = $dayData ? $dayData->visit_count : 0;
//
//                $weeklystatistics[] = [
//                    'day' => $day,
//                    'appointment_count' => $visitCount,
//                ];
//            }
//            #################################################################################################
//            return response()->json([
//                'monthly_visits'=> $monthlystatistics,
//                'weekly_appointments' => $weeklystatistics,
//                ],200);
//        }catch (Exception $exception){
//            return response()->json([
//                'success' => false,
//                'message' => $exception->getMessage()
//            ],500);
//        }
//    }

    public function doctorStatistics(): JsonResponse
    {
        $user = auth()->user();

        try {
            $doctor = Doctor::where('user_id', $user->id)->first();
            if (!$doctor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doctor not found'
                ], 404);
            }
            ######################################## Monthly Visits ###########################################
            $currentYear = date('Y');
            $totalVisits = DB::table('center_visits')
                ->leftJoin('user_appointments', 'center_visits.userappointment_id', '=', 'user_appointments.id')
                ->leftJoin('appointments', 'user_appointments.appointment_id', '=', 'appointments.id')
                ->whereYear('center_visits.visit_time', $currentYear)
                ->where(function ($query) use ($doctor) {
                    $query->where('appointments.doctor_id', $doctor->id)
                        ->orWhereNull('appointments.doctor_id');
                })->count();

            $visitStatistics = DB::table('center_visits')
                ->leftJoin('user_appointments', 'center_visits.userappointment_id', '=', 'user_appointments.id')
                ->leftJoin('appointments', 'user_appointments.appointment_id', '=', 'appointments.id')
                ->select(
                    DB::raw('MONTH(center_visits.visit_time) as month'),
                    DB::raw('COUNT(center_visits.id) as visit_count')
                )->whereYear('center_visits.visit_time', $currentYear)
                ->where(function ($query) use ($doctor) {
                    $query->where('appointments.doctor_id', $doctor->id)
                        ->orWhereNull('appointments.doctor_id');
                })->groupBy(DB::raw('MONTH(center_visits.visit_time)'))
                ->orderBy('month')->get();
            $monthlystatistics = [];
            for ($month = 1; $month <= 12; $month++) {
                $monthData = $visitStatistics->firstWhere('month', $month);
                $visitCount = $monthData ? $monthData->visit_count : 0;
                $percentage = $totalVisits > 0 ? ($visitCount / $totalVisits) * 100 : 0;
                $monthlystatistics[] = [
                    'month' => $month,
                    'visit_percentage' => round($percentage, 2),
                ];
            }
            #################################### Weekly Appointments ########################################
            $startOfWeek = Carbon::now('Asia/Damascus')->startOfWeek(CarbonInterface::SATURDAY);
            $endOfWeek = Carbon::now('Asia/Damascus')->endOfWeek(CarbonInterface::FRIDAY);
            $totalAppointments = DB::table('user_appointments')
                ->leftJoin('appointments', 'user_appointments.appointment_id', '=', 'appointments.id')
                ->whereBetween('user_appointments.appointment_time', [$startOfWeek, $endOfWeek])
                ->where(function ($query) use ($doctor) {
                    $query->where('appointments.doctor_id', $doctor->id)
                        ->orWhereNull('appointments.doctor_id');
                })->count();
            $appointmentsStatistics = DB::table('user_appointments')
                ->leftJoin('appointments', 'user_appointments.appointment_id', '=', 'appointments.id')
                ->select(
                    DB::raw('DAYOFWEEK(user_appointments.appointment_time) as day'),
                    DB::raw('COUNT(user_appointments.id) as visit_count')
                )->whereBetween('user_appointments.appointment_time', [$startOfWeek, $endOfWeek])
                ->where(function ($query) use ($doctor) {
                    $query->where('appointments.doctor_id', $doctor->id)
                        ->orWhereNull('appointments.doctor_id');
                })->groupBy(DB::raw('DAYOFWEEK(user_appointments.appointment_time)'))
                ->orderBy('day')->get();
            $weeklystatistics = [];
            for ($day = 1; $day <= 7; $day++) {
                $adjustedDay = ($day + 5) % 7 + 1;

                $dayData = $appointmentsStatistics->firstWhere('day', $adjustedDay);
                $visitCount = $dayData ? $dayData->visit_count : 0;

                $weeklystatistics[] = [
                    'day' => $day,
                    'appointment_count' => $visitCount,
                ];
            }
            #################################################################################################
            return response()->json([
                'monthly_visits' => $monthlystatistics,
                'weekly_appointments' => $weeklystatistics,
            ], 200);

        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }



}
