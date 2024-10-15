<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $notification_types = [
            [
                'type' => 'New Appointment ',
                'created_at' => now(),
            ],
            [
                'type' => 'Appointment Canceled',
                'created_at' => now(),
            ],
            [
                'type' => 'Appointment Confirmed',
                'created_at' => now(),
            ],
            [
                'type' => 'New Referral',
                'created_at' => now(),
            ],
            [
                'type' => 'Wallet Charged',
                'created_at' => now(),
            ],
            [
                'type' => 'New Donation',
                'created_at' => now(),
            ],
            [
                'type' => 'Appointment Reminder',
                'created_at' => now(),
            ],
            [
                'type' => 'Missing Appointment',
                'created_at' => now(),
            ]
        ];
        DB::table('notification_types')->insert($notification_types);
    }
}
