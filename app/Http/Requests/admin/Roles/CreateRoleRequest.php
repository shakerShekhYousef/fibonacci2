<?php

namespace App\Http\Requests\admin\Roles;

use App\Http\Requests\BaseRequest;

class CreateRoleRequest extends BaseRequest
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
            'name' => ['required', 'unique:roles,name'],
            'permissions' => ['required', 'array'],
        ];
    }
}
