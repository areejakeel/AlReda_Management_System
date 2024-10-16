<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Role::query()->insert(
            [
                [
                    'id' => 1,
                    'name_en' => "User",
                    'name_ar' => "مستخدم"
                ],
                [
                    'id' => 2,
                    'name_en' => "Admin",
                    'name_ar' => "ادمن"
                ]
            ]
        );
    }
}
