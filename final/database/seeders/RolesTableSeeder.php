<?php

namespace Database\Seeders;

use App\Models\Center;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            ['id' => 1, 'position' => 'admin'],
            ['id' => 2, 'position' => 'doctor'],
            ['id' => 3, 'position' => 'secretary'],
            ['id' => 4, 'position' => 'user']
        ]);
        Center::create([
            'center_name' => 'ALreda',
            'description' => 'Medical Center',
            'phone' => '0231456987',
        ]);
    }
}
