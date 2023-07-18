<?php

namespace App\Http\Controllers\admin\teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Teacher;

class CourseController extends Controller
{
    /**
     * get teacher courses
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        $user = auth()->user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        $courses = Course::where('teacher_id', $teacher->id)->get();

        return success_response($courses);
    }
}
