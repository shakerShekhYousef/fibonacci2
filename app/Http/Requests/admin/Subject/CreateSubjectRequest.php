<?php

namespace App\Http\Requests\admin\Subject;

use Illuminate\Foundation\Http\FormRequest;

class CreateSubjectRequest extends FormRequest
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
            'name_ar' => 'required|max:50',
            'name_en' => 'nullable|max:50',
            'specialty_id' => 'required|exists:specialties,id',
        ];
    }
}
