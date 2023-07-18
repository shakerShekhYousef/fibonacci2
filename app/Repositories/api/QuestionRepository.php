<?php

namespace App\Repositories\api;

use App\Exceptions\GeneralException;
use App\Http\Requests\api\Question\CreateQuestionRequest;
use App\Models\Answer;
use App\Models\Question;
use Exception;

class QuestionRepository
{
    /**
     * get questions in quiez
     *
     * @param  int  $quiez_id
     * @return mixed
     */
    public function questions($quiez_id)
    {
        $data = Question::where('quiz_id')->get();

        return $data;
    }

    /**
     * create new question
     *
     * @param  CreateQuestionRequest  $request
     * @return instanceOf Question
     */
    public function create(CreateQuestionRequest $request)
    {
        try {
            $question = Question::create(
                $request->except('answers')
            );
            if ($question) {
                $data = [];
                foreach ($request['answers'] as $answer) {
                    array_push($data, [
                        'text' => $answer['text'],
                        'is_correct' => $answer['is_correct'],
                        'question_id' => $question->id,
                    ]);
                }
                Answer::insert($data);
                $newQeustion = Question::with(['answers'])->find($question->id);

                return $newQeustion;
            }
            throw new GeneralException('server error');
        } catch (Exception $e) {
            throw new GeneralException('server error');
        }
    }

    /**
     * get question details
     *
     * @param  int  $question_id
     * @return instanceOf Question
     */
    public function show($question_id)
    {
        $question = Question::with(['answers'])->find($question_id);

        return $question;
    }

    /**
     * get questions in quiz
     *
     * @param  int  $quiz_id
     * @return mixed
     */
    public function all($quiz_id)
    {
        $questions = Question::with(['answers'])->where('quiz_id', $quiz_id)->get();

        return $questions;
    }
}
