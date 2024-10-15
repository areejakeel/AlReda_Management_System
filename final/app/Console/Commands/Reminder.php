<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\Patient;
use App\Models\User_Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class Reminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointment:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tomorrow = Carbon::now()->addDay()->startOfDay();
        $today = Carbon::today();
        $appointments1 = User_Appointment::with(['appointment' => function($query) use ($tomorrow) {
            $query->whereDate('appointment_date', $tomorrow);
        }])->whereHas('appointment', function($query) use ($tomorrow) {
                $query->whereDate('appointment_date', $tomorrow);
            })->where('status', 'no_show')->get();
        if ($appointments1) {
            foreach ($appointments1 as $appointment) {
                $patient = Patient::find($appointment->patient_id);
                (new Notification)->store(7, "You have An Appointment Tomorrow At: " . Carbon::parse($appointment->appointment_time)->format('H:i:s'), $patient->user_id);
            }
        }
        $appointments2 = User_Appointment::with(['appointment' => function($query) use ($today) {
            $query->whereDate('appointment_date','<', $today);
        }])->whereHas('appointment', function($query) use ($today) {
                $query->whereDate('appointment_date','<', $today);
            })->where('status', 'no_show')->get();
        if ($appointments2) {
            foreach ($appointments2 as $appointment) {
                $patient = Patient::find($appointment->patient_id);
                (new Notification)->store(8, "You have missed Appointment At: " . $appointment->appointment_time, $patient->user_id);
            }
        }
        return Command::SUCCESS;
    }
}
