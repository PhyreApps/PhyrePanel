<?php

namespace App\Http\Controllers\Api\Request;

class CustomerCreateRequest extends AuthorizedApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:customers,email',
        ];
    }
}
