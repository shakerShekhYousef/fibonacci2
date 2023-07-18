<?php

namespace App\Http\Controllers\admin;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * constructor
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * get list of teachers
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teachers = Teacher::with(['user'])->get();

        return success_response($teachers);
    }

    /**
     * give permission to teacher
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function give_permission($id)
    {
        $teacher = Teacher::find($id);
        if ($teacher) {
            $teacher->can_add_videos = 1;
            $teacher->save();

            return success_response();
        }
        throw new GeneralException('server errors');
    }

    /**
     * remove permission from teacher
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function remove_permission($id)
    {
        $teacher = Teacher::find($id);
        if ($teacher) {
            $teacher->can_add_videos = 0;
            $teacher->save();
        }

        return success_response();
    }

    /**
     * delete a teacher permanently
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required',
            'new_teacher_id' => 'required|exists:teachers,id',
        ]);
        $teacher = Teacher::find($request['teacher_id']);
        $user = User::find($teacher->user_id);
        Course::where('teacher_id', $request['teacher_id'])
            ->update(['teacher_id' => $request['new_teacher_id']]);
        $teacher->delete();
        $user->delete();

        return success_response();
    }
}
