<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\Question\CreateQuestionRequest;
use App\Http\Requests\api\Question\GetQuestionsRequest;
use App\Models\Student;
use App\Models\StudentQuiz;
use App\Repositories\api\QuestionRepository;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    protected $questionRepo;

    public function __construct(QuestionRepository $questionRepository)
    {
        $this->questionRepo = $questionRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(GetQuestionsRequest $request)
    {
        $questions = $this->questionRepo->all($request['quiz_id']);

        return success_response($questions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateQuestionRequest $request)
    {
        $result = $this->questionRepo->create($request);

        return success_response($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $qeustion = $this->questionRepo->show($id);

        return success_response($qeustion);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * answer a quiz
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function answerQuestion(Request $request)
    {
        // $request->validate([
        //     'question_id' => 'required|exists:questions,id',
        //     'answer_id' => 'required',
        //     'quiz_id' => 'required|exists:quizzes,id'
        // ]);

        $student = Student::where('user_id', auth()->user()->id)->first();
        if (! $student) {
            return error_response(trans('You can not take this quiz'));
        }
        $array = [];
        foreach ($request->all() as $item) {
            $check = StudentQuiz::where('student_id', $student->id)
                ->where('quiz_id', $item['quiz_id'])
                ->where('question_id', $item['question_id'])
                ->first();
            if (! $check) {
                $array[] = [
                    'quiz_id' => $item['quiz_id'],
                    'question_id' => $item['question_id'],
                    'answer_id' => $item['answer_id'],
                    'student_id' => $student->id,
                ];
            }
        }
        StudentQuiz::insert($array);

        return success_response();
    }
}
