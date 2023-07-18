<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\Student\SaveVideoProgressRequest;
use App\Repositories\api\StudentRepository;
use App\Http\Requests\api\Student\BuyVideoRequest;

class StudentController extends Controller
{
    protected $studentRepository;

    public function __construct(StudentRepository $studentRepository)
    {
        return $this->studentRepository = $studentRepository;
    }

    public function save_video_progress(SaveVideoProgressRequest $request)
    {
        $progress = $this->studentRepository->save_video_progress($request->all());
        return success_response($progress);
    }

    /**
     * @throws \App\Exceptions\GeneralException
     */
    public function buy_video(BuyVideoRequest $request){
        return $this->studentRepository->buy_video($request->all());
    }

}
