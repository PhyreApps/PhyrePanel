<?php

namespace App\Http\Controllers\Api\Request;

class HostingSubscriptionCreateRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'hosting_plan_id' => 'required|exists:hosting_plans,id',
            'domain' => 'required|regex:/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i',
        ];
    }
}
