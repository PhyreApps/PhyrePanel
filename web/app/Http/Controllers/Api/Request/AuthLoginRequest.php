<?php

namespace App\Http\Controllers\Api\Request;

class AuthLoginRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string',
            //    'browserAgent' => 'required|string',
        ];
    }
}
