<?php

namespace App\Http\Requests\api\Question;

use App\Http\Requests\BaseRequest;

class CreateQuestionRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'text' => ['required'],
            'answers' => ['required', 'array'],
            'quiz_id' => ['required', 'exists:quizzes,id'],
        ];
    }
}
