<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * get my profile
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $user = User::find(auth()->user()->id);
        $user_data = $user->toArray();
        switch ($user->account_type) {
            case 'teacher':
                $teacher = Teacher::where('user_id', $user->id)->first();
                $user_data['first_name'] = $teacher->first_name;
                $user_data['last_name'] = $teacher->last_name;
                $user_data['image'] = $teacher->image;
                break;
            case 'student':
                $student = Student::with(['specialty', 'balance'])->where('user_id', $user->id)->first();
                $user_data['first_name'] = $student->first_name;
                $user_data['last_name'] = $student->last_name;
                $user_data['image'] = $student->image;
                $user_data['specialty'] = $student->specialty;
                $user_data['balance'] = $student->balance->balance;
                break;
        }

        return success_response($user_data);
    }

    /**
     * update profile
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|max:50',
            'last_name' => 'nullable|max:50',
            'specialty_id' => 'nullable|exists:specialties,id',
        ]);
        if ($validator->fails()) {
            return error_response($validator->errors()->first());
        }
        $user = User::find(auth()->user()->id);
        if ($user->account_type == 'student') {
            $student = Student::where('user_id', $user->id)->first();
            if ($request['first_name'] != null) {
                $student->first_name = $request['first_name'];
            }
            if ($request['last_name'] != null) {
                $student->last_name = $request['last_name'];
            }
            if ($request['specialty_id'] != null) {
                $student->specialty_id = $request['specialty_id'];
            }
            $student->save();
        } elseif ($user->account_type == 'teacher') {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if ($request['first_name'] != null) {
                $teacher->first_name = $request['first_name'];
            }
            if ($request['last_name'] != null) {
                $teacher->last_name = $request['last_name'];
            }
            $teacher->save();
        }
        $user_data = get_profile($user);

        return success_response($user_data);
    }

    /**
     * update profile image
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update_image(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,jpeg,png,gif',
        ]);
        if ($validator->fails()) {
            return error_response($validator->errors()->first());
        }
        $file = $request->file('image');
        $fileName = time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('users', $fileName, 'public');
        $user = User::find(auth()->user()->id);
        switch ($user->account_type) {
            case 'teacher':
                $teacher = Teacher::where('user_id', $user->id)->first();
                if ($teacher->image != null) {
                    Storage::disk('public')->delete($teacher->image);
                }
                $teacher->image = $path;
                $teacher->save();
                break;
            case 'student':
                $student = Student::where('user_id', $user->id)->first();
                if ($student->image != null) {
                    Storage::disk('public')->delete($student->image);
                }
                $student->image = $path;
                $student->save();
                break;
        }
        $user_data = get_profile($user);

        return success_response($user_data);
    }
}
