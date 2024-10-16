<?php

namespace Database\Seeders;

use App\Models\Governorate;
use Illuminate\Database\Seeder;

class GovernorateSeeder extends Seeder
{
    public function run(): void
    {
        Governorate::query()->insert(
            [
                [
                    'id' => 1,
                    'name_ar' => "دمشق",
                    'name_en' => "Damascus",
                ],
                [
                    'id' => 2,
                    'name_ar' => "حمص",
                    'name_en' => "Homs",
                ],
                [
                    'id' => 3,
                    'name_ar' => "حماة",
                    'name_en' => "Hama",
                ],
                [
                    'id' => 4,
                    'name_ar' => "حلب",
                    'name_en' => "Aleppo",
                ],
                [
                    'id' => 5,
                    'name_ar' => "درعا",
                    'name_en' => "Dara'a",
                ],
                [
                    'id' => 6,
                    'name_ar' => "اللاذقية",
                    'name_en' => "Latakia",
                ],
                [
                    'id' => 7,
                    'name_ar' => "طرطوس",
                    'name_en' => "Tartus",
                ],
                [
                    'id' => 8,
                    'name_ar' => "الرقة",
                    'name_en' => "Al Raqqah",
                ],
                [
                    'id' => 9,
                    'name_ar' => "الحسكة",
                    'name_en' => "Al Hasakah",
                ],
                [
                    'id' => 10,
                    'name_ar' => "السويداء",
                    'name_en' => "Al Suwayda'a",
                ],
                [
                    'id' => 11,
                    'name_ar' => "دير الزور",
                    'name_en' => "Deir Alzur",
                ],
                [
                    'id' => 12,
                    'name_ar' => "ادلب",
                    'name_en' => "Idlib",
                ],
                [
                    'id' => 13,
                    'name_ar' => "القنيطرة",
                    'name_en' => "Al Quneitra",
                ],
                [
                    'id' => 14,
                    'name_ar' => "ريف دمشق",
                    'name_en' => "Rural Damascus",
                ],
            ]
        );
    }
}
