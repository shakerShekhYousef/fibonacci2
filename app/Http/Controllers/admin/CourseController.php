<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\Courses\FreeTrailRequest;
use App\Models\Course;
use App\Models\Video;
use App\Repositories\admin\CourseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    protected $courseRepo;

    /**
     * constructor
     *
     * middlewares
     *
     * @return void
     */
    public function __construct(CourseRepository $courseRepository)
    {
        $this->courseRepo = $courseRepository;
        $this->middleware('adminRole:create_course')->only('create');
        $this->middleware('adminRole:view_course')->only(['index', 'show']);
        $this->middleware('adminRole:update_course')->only(['update', 'setFreeTrail']);
        $this->middleware('adminRole:delete_course')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses = Course::with(['teacher', 'subject'])->get();

        return success_response($courses);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|max:50',
            'name_en' => 'required|max:50',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'preview' => 'required|mimetypes:video/mp4',
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
            'thumbnail' => 'required|image|mimes:jpg,jpeg,png,gif',
        ]);
        if ($validator->fails()) {
            return error_response($validator->errors()->first());
        }
        $course = Course::create([
            'name_ar' => $request['name_ar'],
            'name_en' => $request['name_en'],
            'description_ar' => $request['description_ar'],
            'description_en' => $request['description_en'],
            'teacher_id' => $request['teacher_id'],
            'subject_id' => $request['subject_id'],
        ]);
        if ($course) {
            if ($request->hasFile('preview')) {
                $file = $request->file('preview');
                $fileName = time().'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs('courses', $fileName, 'public');
                $course->video_preview = $path;
                $course->save();
            }
            if ($request->hasFile('thumbnail')) {
                $file = $request->file('thumbnail');
                $fileName = time().'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs('courses', $fileName, 'public');
                $course->thumbnail = $path;
                $course->save();
            }

            return success_response($course);
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
        $course = Course::with(['teacher', 'subject'])->find($id);
        if ($course == null) {
            return not_found_response('course not found');
        }
        $videos = Video::with(['teacher'])->where('course_id', $id)->get();
        $data = $course->toArray();
        $data['videos'] = $videos;

        return success_response($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $course = Course::find($id);
        if ($course == null) {
            return not_found_response('course not found');
        }
        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|max:50',
            'name_en' => 'required|max:50',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'preview' => 'nullable|mimes:mp4,mkv',
            'teacher_id' => 'nullable|exists:teachers,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,gif,png',
        ]);
        if ($validator->fails()) {
            return error_response($validator->errors()->first());
        }
        $course->update([
            'name_ar' => $request['name_ar'],
            'name_en' => $request['name_en'],
            'description_en' => $request['description_en'],
            'description_ar' => $request['description_ar'],
        ]);

        if ($request->hasFile('preview')) {
            if ($course->video_preview != null) {
                Storage::disk('public')->delete($course->video_preview);
            }
            $file = $request->file('preview');
            $fileName = time().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('courses', $fileName, 'public');
            $course->video_preview = $path;
            $course->save();
        }
        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail != null) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $file = $request->file('thumbnail');
            $fileName = time().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('courses', $fileName, 'public');
            $course->thumbnail = $path;
            $course->save();
        }

        return success_response($course);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Course::where('id', $id)->delete();

        return success_response();
    }

    /**
     * set course free trail
     *
     * @param  int  $id
     * @param  FreeTrailRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function setFreeTrail(FreeTrailRequest $request, $id)
    {
        $result = $this->courseRepo->setFreeTrail($id, $request['end_free_trail']);

        return success_response();
    }
}
