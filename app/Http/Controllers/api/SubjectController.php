<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    /**
     * get list of subjects
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'specialty_id' => 'required|exists:specialties,id',
        ]);
        if ($validator->fails()) {
            return error_response($validator->errors()->first());
        }
        $subjects = Subject::where('specialty_id', $request['specialty_id'])->get();

        return success_response($subjects);
    }
}
