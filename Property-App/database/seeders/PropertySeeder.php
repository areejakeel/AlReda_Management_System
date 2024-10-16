<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyState;
use Database\Factories\PropertyFactory;
use Database\Factories\PropertyStateFactory;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run()
    {
        PropertyState::query()->insert([
            [
                'id' => 1,
                'name_ar' => "متاح",
                'name_en' => "Available"
            ],
            [
                'id' => 2,
                'name_ar' => "مؤجر",
                'name_en' => "Leased"
            ],
            [
                'id' => 3,
                'name_ar' => "مباع",
                'name_en' => "Sold"
            ],
            [
                'id' => 4,
                'name_ar' => "مرفوض",
                'name_en' => "reject"
            ],
            [
                'id' => 5,
                'name_ar' => "قيد المراجعة",
                'name_en' => "processing"
            ]

        ]);
    }
}
