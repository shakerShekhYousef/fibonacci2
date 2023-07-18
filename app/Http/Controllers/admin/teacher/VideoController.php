<?php

namespace App\Http\Controllers\admin\teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\Videos\CreateVideoRequest as VideosCreateVideoRequest;
use App\Http\Requests\admin\Videos\UpdateVideoRequest;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Video;
use App\Repositories\admin\VideoRepository;
use App\Trait\FileTrait;

class VideoController extends Controller
{
    use FileTrait;

    protected $videoRepo;

    public function __construct(VideoRepository $videoRepository)
    {
        $this->videoRepo = $videoRepository;
    }

    public function checkCourse($id)
    {
        $user = auth()->user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        $check = Course::where('id', $id)
            ->where('teacher_id', $teacher->id)->count();
        if ($check > 0) {
            return true;
        }

        return false;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $videos = $this->videoRepo->videos($id);

        return success_response($videos);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VideosCreateVideoRequest $request)
    {
        if ($this->checkCourse($request['course_id'])) {
            $video = $this->videoRepo->create($request->except('video'));
            if ($video) {
                if ($request->hasFile('video')) {
                    $path = $this->upload($request->file('video'), 'videos');
                    $video->file = $path;
                    $video->save();
                }

                return success_response($video);
            }
        } else {
            return forbidden_response('forbidden');
        }

        return server_error_response();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $video = $this->videoRepo->show($id);
        if ($this->checkCourse($video->course_id)) {
            return success_response($video);
        }

        return forbidden_response('forbidden');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVideoRequest $request, $id)
    {
        $video = Video::find($id);
        if ($this->checkCourse($video->course_id)) {
            $result = $this->videoRepo->update($id, $request);

            return success_response($result);
        }

        return forbidden_response('forbidden');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $video = Video::Find($id);
        if ($this->checkCourse($video->course_id)) {
            $this->videoRepo->destroy($id);

            return success_response();
        }

        return forbidden_response('forbidden');
    }
}
