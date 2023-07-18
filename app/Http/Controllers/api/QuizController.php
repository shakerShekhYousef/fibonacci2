<?php

namespace App\Http\Controllers\api;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\Quiz\CreateQuizRequest;
use App\Http\Requests\api\Quiz\GetVideoQuizRequest;
use App\Http\Requests\api\Quiz\UpdateQuizRequest;
use App\Models\Quiz;
use App\Models\Student;
use App\Models\StudentQuiz;
use App\Repositories\api\QuizRepository;

class QuizController extends Controller
{
    private $quizRepository;

    public function __construct(QuizRepository $quizRepository)
    {
        return $this->quizRepository = $quizRepository;
    }

    /**
     * @throws GeneralException
     */
    public function add_quiz_to_video(CreateQuizRequest $request)
    {
        $quiz = $this->quizRepository->create($request->all());

        return success_response($quiz);
    }

    public function update(UpdateQuizRequest $request, Quiz $quiz)
    {
        $quiz = $this->quizRepository->update($quiz, $request->all());

        return success_response($quiz);
    }

    public function destroy(Quiz $quiz): string
    {
        $this->quizRepository->deleteById($quiz->id);

        return success_response('Quiz has been deleted successfully');
    }

    /**
     * get video quizes
     *
     * @param  GetVideoQuizRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function index(GetVideoQuizRequest $request)
    {
        $data = $this->quizRepository->videoQuiz($request['video_id']);

        return success_response($data);
    }

    /**
     * get student taken quizzes
     *
     * @return \Illuminate\Http\Response
     */
    public function student_quizes()
    {
        $student = Student::where('user_id', auth()->user()->id)->first();
        if (! $student) {
            return server_error_response();
        }
        $result = StudentQuiz::Relations()->where('student_id', $student->id)->get();

        return success_response($result);
    }
}
