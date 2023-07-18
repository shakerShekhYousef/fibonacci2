<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Subject::insert([
            [
                'name_ar' => 'معلوماتية',
                'name_en' => 'IT',
                'specialty_id' => 1,
            ],
        ]);
    }
}
