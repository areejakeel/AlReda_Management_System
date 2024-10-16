<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run()
    {
        Region::query()->insert(
            [
                [
                    'id' => 1,
                    'name_ar' => "الميدان",
                    'name_en' => "Al-Midan",
                    'governorate_id' => 1,
                    'x' => 33.49256,
                    'y' => 36.29835
                ],
                [
                    'id' => 2,
                    'name_ar' => "المهاجرين",
                    'name_en' => "Muhajreen",
                    'governorate_id' => 1,
                    'x' => 33.52334,
                    'y' => 36.27650
                ],
                [
                    'id' => 3,
                    'name_ar' => "الصالحية",
                    'name_en' => "Al-Salihiyah",
                    'governorate_id' => 1,
                    'x' => 33.52923,
                    'y' => 36.28902
                ],
                [
                    'id' => 4,
                    'name_ar' => "كفرسوسة",
                    'name_en' => "Kafr Sousa",
                    'governorate_id' => 1,
                    'x' => 33.48430662627044,
                    'y' => 36.26968707569155
                ],
                [
                    'id' => 5,
                    'name_ar' => "المزة",
                    'name_en' => "Mezzeh",
                    'governorate_id' => 1,
                    'x' => 33.50120857587571,
                    'y' => 36.24601854968693
                ],
                [
                    'id' => 6,
                    'name_ar' => "القنوات",
                    'name_en' => "Qanawat",
                    'governorate_id' => 1,
                    'x' => 33.50786925955247,
                    'y' => 36.29258745014915
                ],
                [
                    'id' => 7,
                    'name_ar' => "ساروجة",
                    'name_en' => "Sarouja",
                    'governorate_id' => 1,
                    'x' => 33.51767976643389,
                    'y' => 36.29695178575244
                ],
                [
                    'id' => 8,
                    'name_ar' => "الشاغور",
                    'name_en' => "Al-Shaghur",
                    'governorate_id' => 1,
                    'x' => 33.48932610996097,
                    'y' => 36.31055505947799
                ],
                [
                    'id' => 9,
                    'name_ar' => "ببيلا",
                    'name_en' => "Bab Bila",
                    'governorate_id' => 1,
                    'x' => 33.46931255498021,
                    'y' => 36.31837351801455
                ],
                [
                    'id' => 10,
                    'name_ar' => "الشعلان",
                    'name_en' => "Sha'alan",
                    'governorate_id' => 1,
                    'x' => 33.489182946208736,
                    'y' => 36.31278665661482
                ],
                [
                    'id' => 11,
                    'name_ar' => "الحمراء",
                    'name_en' => "Al-Hamraa",
                    'governorate_id' => 1,
                    'x' => 33.51967593313504,
                    'y' => 36.291288168201376
                ],
                [
                    'id' => 12,
                    'name_ar' => "ركن الدين",
                    'name_en' => "Rukneddine",
                    'governorate_id' => 1,
                    'x' => 33.53984926224835,
                    'y' => 36.29982347668919
                ],
                [
                    'id' => 13,
                    'name_ar' => "برزة",
                    'name_en' => "Barzeh",
                    'governorate_id' => 1,
                    'x' => 33.55769178223628,
                    'y' => 36.31135976633894
                ],
                [
                    'id' => 14,
                    'name_ar' => "قدسيا",
                    'name_en' => "Qudsaya",
                    'governorate_id' => 14,
                    'x' => 33.5472328676433,
                    'y' => 36.2145477515173
                ],
                [
                    'id' => 15,
                    'name_ar' => "ضاحية قدسيا",
                    'name_en' => "Qudsaya Suburb",
                    'governorate_id' => 14,
                    'x' => 33.53788687284276,
                    'y' => 36.190831857162216
                ],
                [
                    'id' => 16,
                    'name_ar' => "جديدة عرطوز",
                    'name_en' => "Jdeidat Artouz",
                    'governorate_id' => 14,
                    'x' => 33.41921683733668,
                    'y' => 36.17700907165579
                ],
                [
                    'id' => 17,
                    'name_ar' => "معضمية الشام",
                    'name_en' => "Muadamiyat al-Sham",
                    'governorate_id' => 14,
                    'x' => 33.46162541323817,
                    'y' => 36.191668756123256
                ],
                [
                    'id' => 18,
                    'name_ar' => "صحنايا",
                    'name_en' => "Sahnaya",
                    'governorate_id' => 14,
                    'x' => 33.42948322884421,
                    'y' => 36.22276232046187
                ],
                [
                    'id' => 19,
                    'name_ar' => "الكسوة",
                    'name_en' => "Al-Kiswah",
                    'governorate_id' => 14,
                    'x' => 33.35909055088566,
                    'y' => 36.25200862639391
                ],
                [
                    'id' => 20,
                    'name_ar' => "النبك",
                    'name_en' => "Al-Nabek",
                    'governorate_id' => 14,
                    'x' => 34.013599514581436,
                    'y' => 36.73287504703827
                ],
                [
                    'id' => 21,
                    'name_ar' => "يبرزد",
                    'name_en' => "Yabroud",
                    'governorate_id' => 14,
                    'x' => 33.97183372377386,
                    'y' => 36.65845898731156
                ],
                [
                    'id' => 22,
                    'name_ar' => "دير عطية",
                    'name_en' => "Deir Atiyah",
                    'governorate_id' => 14,
                    'x' => 34.091889818549845,
                    'y' => 36.7619218598723
                ],
                [
                    'id' => 23,
                    'name_ar' => "داريا",
                    'name_en' => "Darayya",
                    'governorate_id' => 14,
                    'x' => 33.45639569158574,
                    'y' => 36.23605508724537
                ],
                [
                    'id' => 24,
                    'name_ar' => "قطنا",
                    'name_en' => "Qatana",
                    'governorate_id' => 14,
                    'x' => 33.43944302338939,
                    'y' => 36.08383609397402
                ],
                [
                    'id' => 25,
                    'name_ar' => "دير بعلبة",
                    'name_en' => "Deir Baalbah",
                    'governorate_id' => 2,
                    'x' => 34.75575499227291,
                    'y' => 36.75529450668995
                ],
                [
                    'id' => 26,
                    'name_ar' => "الوعر",
                    'name_en' => "Al-Ouaer",
                    'governorate_id' => 2,
                    'x' => 34.74343209722155,
                    'y' => 36.67060407703984
                ],
                [
                    'id' => 27,
                    'name_ar' => "الخالدية",
                    'name_en' => "Al Khalidiyyah",
                    'governorate_id' => 2,
                    'x' => 34.74619680075963,
                    'y' => 36.71945750736314
                ],
                [
                    'id' => 28,
                    'name_ar' => "بابا عمر",
                    'name_en' => "Baba Amr",
                    'governorate_id' => 2,
                    'x' => 34.70925368046634,
                    'y' => 36.68735325294675
                ],


            ]
        );
    }
}
