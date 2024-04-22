<?php

namespace App\Http\Controllers\Api\Request;

class AuthorizedApiRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;

        $user = auth()->user();

        if ($user) {
            return true;
        }

        return false;
    }
}
