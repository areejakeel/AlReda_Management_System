<?php

namespace Database\Seeders;

use App\Models\ReservationType;
use Illuminate\Database\Seeder;

class ReservationTypeSeeder extends Seeder
{
    public function run(): void
    {
        ReservationType::query()->insert([
            [
                'id' => 1,
                'name_ar' => "شراء",
                'name_en' => "Buy"
            ],
            [
                'id' => 2,
                'name_ar' => "اجار",
                'name_en' => "Rent"
            ],
            [
                'id' => 3,
                'name_ar' => "اجار يومي",
                'name_en' => "Daily Rent"
            ],
        ]);
    }
}
