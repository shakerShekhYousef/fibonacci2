<?php

use App\Models\Student;
use App\Models\Teacher;

if (! function_exists('get_profile')) {
    function get_profile($user)
    {
        $user_data = $user->toArray();
        switch ($user->account_type) {
            case 'teacher':
                $teacher = Teacher::where('user_id', $user->id)->first();
                $user_data['teacher_id'] = $teacher->id;
                $user_data['first_name'] = $teacher->first_name;
                $user_data['last_name'] = $teacher->last_name;
                $user_data['image'] = $teacher->image;
                $user_data['can_add_videos'] = $teacher->can_add_videos;
                break;
            case 'student':
                $student = Student::with(['specialty', 'balance'])->where('user_id', $user->id)->first();

                $user_data['student_id'] = $student->id;
                $user_data['first_name'] = $student->first_name;
                $user_data['last_name'] = $student->last_name;
                $user_data['image'] = $student->image;
                $user_data['specialty'] = $student->specialty;
                break;
            default:
                return $user;
        }

        return $user_data;
    }
}
