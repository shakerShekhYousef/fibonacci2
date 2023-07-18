<?php

namespace App\Http\Requests\admin\Videos;

use App\Http\Requests\BaseRequest;

class CreateVideoRequest extends BaseRequest
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
            'title' => ['required', 'max:255'],
            'description' => ['required', 'max:255'],
            'price' => ['required', 'numeric'],
            'video' => ['required', 'mimetypes:video/mp4'],
            'course_id' => ['required', 'exists:courses,id'],
        ];
    }
}
