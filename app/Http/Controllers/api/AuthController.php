<?php

namespace App\Http\Controllers\api;

use App\Events\StudentRegisterEvent;
use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentBalance;
use App\Models\Teacher;
use App\Models\User;
use App\Trait\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Nette\Utils\Random;

class AuthController extends Controller
{
    use ResponseTrait;

    /**
     * register new account
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|unique:users,email',
            'password' => 'required',
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'account_type' => 'required|in:teacher,student',
            'specialty_id' => 'nullable|exists:specialties,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,gif,png',
        ]);
        $data = [
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'account_type' => $request['account_type'],
        ];
        $user = User::create($data);
        if ($user) {
            $user_data = $user->toArray();
            if ($user->account_type == 'teacher') {
                $teacher_data = [
                    'id' => $user->id,
                    'first_name' => $request['first_name'],
                    'last_name' => $request['last_name'],
                    'user_id' => $user->id,
                ];
                //update user's role
                $user->update([
                    'role_id' => Role::getRole('Teacher'),
                ]);
                //create teacher
                $teacher = Teacher::create($teacher_data);
                if ($teacher) {
                    $user_data['first_name'] = $teacher->first_name;
                    $user_data['last_name'] = $teacher->last_name;
                    if ($request->hasFile('image')) {
                        $file = $request->file('image');
                        $fileName = time() . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs('users', $fileName, 'public');
                        $teacher->image = $path;
                        $teacher->save();
                    }
                    $user_data['image'] = $teacher->image;
                }
            } elseif ($user->account_type == 'student') {
                $student_data = [
                    'id' => $user->id,
                    'first_name' => $request['first_name'],
                    'last_name' => $request['last_name'],
                    'user_id' => $user->id,
                    'specialty_id' => $request['specialty_id'],
                ];
                //update user's role
                $user->update([
                    'role_id' => Role::getRole('Student'),
                ]);
                //create student
                $student = Student::create($student_data);
                if ($student) {
                    event(new StudentRegisterEvent($student));
                    $user_data['first_name'] = $student->first_name;
                    $user_data['last_name'] = $student->last_name;

                    if ($request->hasFile('image')) {
                        $file = $request->file('image');
                        $fileName = time() . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs('users', $fileName, 'public');
                        $student->image = $path;
                        $student->save();
                    }
                    $user_data['image'] = $student->image;
                }
                $user_data['balance'] = StudentBalance::where('student_id', $student->id)->first();
            }
            Auth::login($user, true);
            $token = $user->createToken('api_token')->plainTextToken;

            return success_response(['user' => $user_data, 'token' => $token, 'remember_token' => $user->remember_token]);
        }
    }

    /**
     * login
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            'password' => 'required',
            'remember' => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            return error_response($validator->errors()->first());
        }
        $credentials = [
            'email' => $request['email'],
            'password' => $request['password'],
        ];
        $remember = false;
        if ($request['remember'] != null) {
            $remember = $request['remember'];
        }
        if (Auth::attempt($credentials, $remember)) {
            $user = User::find(auth()->user()->id);
            $user_data = get_profile($user);
            $token = $user->createToken('api_token')->plainTextToken;

            return success_response(['user' => $user_data, 'token' => $token, 'remember_token' => $user->remember_token]);
        }

        return $this->unAuthenticated();
    }

    /**
     * login with remember token
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login_remember(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'remember_token' => 'required|exists:users,remember_token',
        ]);
        if ($validator->fails()) {
            return error_response($validator->errors()->first());
        }
        $user = User::where('remember_token', $request['remember_token'])->first();
        if ($user != null) {
            $user_data = get_profile($user);
            $token = $user->createToken('api_token')->plainTextToken;

            return success_response(['user' => $user, 'token' => $token]);
        }

        return $this->unAuthenticated();
    }

    /**
     * request reset password code
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function send_code(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
        ]);
        if ($validator->fails()) {
            return error_response($validator->errors()->first());
        }
        $user = User::where('email', $request['email'])->first();
        do {
            $code = Random::generate(6, '0-9');
            $check = User::where('reset_code', $code)->first();
        } while ($check);
        $user->reset_code = $code;
        $user->save();

        Mail::to($request['email'])->send(new PasswordResetMail($request['email']));

        return response()->json(['status' => 1, 'message' => 'mail sent to ' . $request['email']]);
    }

    /**
     * confirm reset code
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function confirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|exists:users,reset_code',
            'email' => 'required|exists:users,email',
        ]);
        if ($validator->fails()) {
            return error_response($validator->errors()->first());
        }
        $user = User::where('email', $request['email'])
            ->where('reset_code', $request['code'])->first();
        if ($user != null) {
            $user->code_confirmed = 1;
            $user->save();

            return success_response();
        }

        return error_response('confirmation code error');
    }

    /**
     * reset password
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function reset_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'email' => 'required|exists:users,email',
        ]);
        if ($validator->fails()) {
            return error_response($validator->errors()->first());
        }
        $user = User::where('email', $request['email'])
            ->where('code_confirmed', 1)->first();
        if ($user != null) {
            $user->password = Hash::make($request['password']);
            $user->code_confirmed = 0;
            $user->save();

            return success_response();
        }

        return server_error_response();
    }
}
