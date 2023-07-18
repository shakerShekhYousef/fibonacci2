<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\api\CourseVideoProgressResource;
use App\Http\Resources\api\VideoResource;
use App\Models\Course;
use App\Models\StudentVideo;
use App\Models\Video;
use App\Repositories\api\CourseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class CourseController extends Controller
{
    /**
     * get courses list
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    private $courseRepository;

    public function __construct(CourseRepository $courseRepository)
    {
        return $this->courseRepository = $courseRepository;
    }

    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'nullable|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'course_id' => 'nullable|exists:courses,id',
        ]);
        if ($validator->fails()) {
            return error_response($validator->errors()->first());
        }
        $data = [];
        if ($request['subject_id'] != null) {
            if ($request['teacher_id'] != null) {
                $data = Course::with(['teacher', 'subject'])->where('subject_id', $request['subject_id'])
                    ->where('teacher_id', $request['teacher_id'])->get();
            } else {
                $data = Course::with(['teacher', 'subject'])->where('subject_id', $request['subject_id'])->get();
            }
        } elseif ($request['teacher_id'] != null) {
            $data = Course::with(['teacher', 'subject'])
                ->where('teacher_id', $request['teacher_id'])->get();
        } elseif ($request['course_id'] != null) {
            $data = Course::with(['teacher', 'subject'])->find($request['course_id']);
            $videos = Video::query()->where('course_id',$data['id'])->get();
            $data['videos']=VideoResource::collection($videos);
        }

        return success_response($data);
    }

    public function get_course_videos_progress()
    {
        $progress = $this->courseRepository->get_course_videos_progress();

        return success_response(CourseVideoProgressResource::collection($progress));
    }
    /**
     * test splitting video
     */
    public function uploadVideo(Request $request)
    {
        $request->validate(['video' => 'required|mimes:mp4']);
        $file = $request->file('video');
        // $fileName = time() . '.' . $file->getClientOriginalExtension();
        // $path = $file->storeAs('test', $fileName, 'public');
        FFMpeg::open($file)
            ->export()
            ->inFormat(new \FFMpeg\Format\Video\X264)
            ->resize(640, 480)
            ->save('new.mp4');
        return success_response();
    }

    public function get_courses_subscribed(){
        //Get auth user id
        $auth_id = auth()->id();
        //Get Courses
        $courses = StudentVideo::query()
            ->join('videos','videos.id','=','student_videos.video_id')
            ->join('courses','courses.id','videos.course_id')
            ->where('student_id',$auth_id)
            ->distinct('courses.id')
            ->get('courses.*');
        ;
        //Response
        return success_response($courses);
    }
}
