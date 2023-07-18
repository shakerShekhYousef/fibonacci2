<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\Videos\CreateVideoRequest;
use App\Http\Requests\admin\Videos\UpdateVideoRequest;
use App\Models\Video;
use App\Repositories\admin\VideoRepository;
use App\Trait\FileTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
    use FileTrait;

    protected $videoRepo;

    /**
     * constructor
     *
     * @return void
     */
    public function __construct(VideoRepository $videoRepository)
    {
        $this->videoRepo = $videoRepository;
        $this->middleware('adminRole:view_videos')->only(['index', 'show']);
        $this->middleware('adminRole:create_videos')->only('create');
        $this->middleware('adminRole:update_videos')->only('update');
        $this->middleware('adminRole:delete_videos')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
        ]);
        if ($validator->fails()) {
            return error_response($validator->errors()->first());
        }
        $videos = Video::where('course_id', $request['course_id'])->get();

        return success_response($videos);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateVideoRequest $request)
    {
        $video = $this->videoRepo->create($request->except('video'));
        if ($video) {
            if ($request->hasFile('video')) {
                $path = $this->upload($request->file('video'), 'videos');
                $video->file = $path;
                $video->save();
            }

            return success_response($video);
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
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateVideoRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVideoRequest $request, $id)
    {
        $video = $this->videoRepo->update($id, $request);

        return success_response($video);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Video::where('id', $id)->delete();

        return success_response();
    }
}
