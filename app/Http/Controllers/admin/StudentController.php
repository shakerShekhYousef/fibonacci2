<?php

namespace App\Http\Controllers\admin;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentBalance;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['user', 'specialty', 'balance'])->paginate(20);

        return success_response($students);
    }

    /**
     * change student balance
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function change_balance(Request $request)
    {

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'balance' => 'required|numeric',
        ]);

        $balance = StudentBalance::firstOrCreate(['student_id' => $request['student_id']]);

        $balance->balance = $request['balance'];
        $balance->save();

        return success_response();

        throw new GeneralException('error');
    }
}
