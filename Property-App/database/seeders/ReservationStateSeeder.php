<?php

namespace Database\Seeders;

use App\Models\ReservationState;
use Illuminate\Database\Seeder;

class ReservationStateSeeder extends Seeder
{
    public function run(): void
    {
        ReservationState::query()->insert([
            [
                'id' => 1,
                'name_en' => 'Under Review',
                'name_ar' => 'قيد المراجعة',
            ],
            [
                'id' => 2,
                'name_en' => 'Accepted',
                'name_ar' => 'مقبول',
            ],
            [
                'id' => 3,
                'name_en' => 'Rejected',
                'name_ar' => 'مرفوض',
            ],
        ]);
    }
}
