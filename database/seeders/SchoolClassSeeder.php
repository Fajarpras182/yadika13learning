<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SchoolClass;
use App\Models\Major;

class SchoolClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $majors = Major::all()->keyBy('code');

        $classes = [
            [
                'name' => 'X Teknik Informatika',
                'major_id' => $majors['TI']->id,
                'year' => 2024,
                'is_active' => true,
            ],
            [
                'name' => 'XI Teknik Informatika',
                'major_id' => $majors['TI']->id,
                'year' => 2023,
                'is_active' => true,
            ],
            [
                'name' => 'XII Teknik Informatika',
                'major_id' => $majors['TI']->id,
                'year' => 2022,
                'is_active' => true,
            ],
            [
                'name' => 'X TKJ 1',
                'major_id' => $majors['TKJ']->id,
                'year' => 2024,
                'is_active' => true,
            ],
            [
                'name' => 'XI TKJ 1',
                'major_id' => $majors['TKJ']->id,
                'year' => 2023,
                'is_active' => true,
            ],
            [
                'name' => 'XII TKJ 1',
                'major_id' => $majors['TKJ']->id,
                'year' => 2022,
                'is_active' => true,
            ],
            [
                'name' => 'X AK 1',
                'major_id' => $majors['AK']->id,
                'year' => 2024,
                'is_active' => true,
            ],
            [
                'name' => 'XI AK 1',
                'major_id' => $majors['AK']->id,
                'year' => 2023,
                'is_active' => true,
            ],
            [
                'name' => 'XII AK 1',
                'major_id' => $majors['AK']->id,
                'year' => 2022,
                'is_active' => true,
            ],
        ];

        foreach ($classes as $class) {
            SchoolClass::create($class);
        }
    }
}
