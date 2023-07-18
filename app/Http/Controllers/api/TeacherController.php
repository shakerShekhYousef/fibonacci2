<?php

namespace App\Http\Controllers\api;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Teacher;

class TeacherController extends Controller
{
    private $teacherRepository;

    public function __construct()
    {
        return $this->middleware('auth:sanctum');
    }

    public function get_my_courses()
    {
        $user_id = auth()->user()->id;
        $teacher = Teacher::where('user_id', $user_id)->first();
        if (! $teacher) {
            throw new GeneralException('Sorry!, You don\'t have a permission for this action');
        }
        $courses = Course::where('teacher_id', $teacher->id)->get();

        return success_response($courses);
    }
}
