<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Specialty::insert([
            [
                'name_ar' => 'بكلوريا',
                'name_en' => 'Bachelor',
            ],
            [
                'name_ar' => 'تاسع',
                'name_en' => '9th Grade',
            ],
        ]);
    }
}
