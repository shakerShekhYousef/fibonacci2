<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //DB::table('permissions')->truncate();
        DB::table('permissions')->insert([
            [
                'name' => 'can view courses',
                'code' => 'view_course',
                'group' => 'Courses',
            ],
            [
                'name' => 'can create courses',
                'code' => 'create_course',
                'group' => 'Courses',
            ],
            [
                'name' => 'can update courses',
                'code' => 'update_course',
                'group' => 'Courses',
            ],
            [
                'name' => 'can delete courses',
                'code' => 'delete_course',
                'group' => 'Courses',
            ],
            [
                'name' => 'can view subjects',
                'code' => 'view_subjects',
                'group' => 'Subjects',
            ],
            [
                'name' => 'can create subjects',
                'code' => 'create_subjects',
                'group' => 'Subjects',
            ],
            [
                'name' => 'can update subjects',
                'code' => 'update_subjects',
                'group' => 'Subjects',
            ],
            [
                'name' => 'can delete subjects',
                'code' => 'delete_subjects',
                'group' => 'Subjects',
            ],
            [
                'name' => 'can view videos',
                'code' => 'view_videos',
                'group' => 'Videos',
            ],

            [
                'name' => 'can create videos',
                'code' => 'create_videos',
                'group' => 'Videos',
            ],
            [
                'name' => 'can update videos',
                'code' => 'update_videos',
                'group' => 'Videos',
            ],

            [
                'name' => 'can delete videos',
                'code' => 'delete_videos',
                'group' => 'Videos',
            ],
            [
                'name' => 'can view users',
                'code' => 'view_users',
                'group' => 'Users',
            ],
            [
                'name' => 'can create users',
                'code' => 'create_users',
                'group' => 'Users',
            ],
            [
                'name' => 'can update users',
                'code' => 'update_users',
                'group' => 'Users',
            ],
        ]);
    }
}
