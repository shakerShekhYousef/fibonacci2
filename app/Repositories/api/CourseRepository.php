<?php

namespace App\Repositories\api;

use App\Exceptions\GeneralException;
use App\Models\Course;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class CourseRepository extends BaseRepository
{
    public function model()
    {
        return Course::class;
    }

    public function get_course_videos_progress()
    {
        //get course id
        if (! isset($_GET['course_id'])) {
            throw new GeneralException('Please enter course id.');
        } else {
            $course_id = $_GET['course_id'];
        }
        try {
            //get student id
            $student_id = auth()->user()->id;
            //videos progress
            return DB::table('student_videos')
                ->join('videos', 'student_videos.video_id', '=', 'videos.id')
                ->join('courses', 'videos.course_id', '=', 'courses.id')
                ->where([['courses.id', $course_id], ['student_videos.student_id', $student_id]])
                ->get([
                    'student_videos.id', 'student_videos.student_id',
                    'student_videos.video_id', 'student_videos.stopped_at',
                ]);
        } catch (GeneralException $exception) {
            return $exception->getMessage();
        }
    }
}
