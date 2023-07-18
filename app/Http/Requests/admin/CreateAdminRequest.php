<?php

namespace App\Http\Requests\admin;

use App\Http\Requests\BaseRequest;

class CreateAdminRequest extends BaseRequest
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
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required'],
            'role_id' => ['required', 'exists:roles,id'],
        ];
    }
}
