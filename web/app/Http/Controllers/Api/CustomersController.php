<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Request\CustomerCreateRequest;
use App\Http\Controllers\ApiController;
use App\Models\Customer;
use App\Models\HostingSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class CustomersController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/api/customers",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *
     *     @OA\PathItem (
     *     ),
     * )
     */
    public function index()
    {
        $findCustomers = Customer::all();

        return response()->json([
            'status' => 'ok',
            'message' => 'Customers found',
            'data' => [
                'customers' => $findCustomers,
            ],
        ]);

    }

    /**
     * @OA\Post(
     *      path="/api/customers",
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *
     *     @OA\PathItem (
     *     ),
     *
     *     @OA\RequestBody(
     *     required=true,
     *
     *     @OA\JsonContent(
     *     required={"name","email","phone"},
     *
     *     @OA\Property(property="name", type="string", example="John Doe", description="Name of the customer"),
     *     @OA\Property(property="email", type="string", example="jhon@gmail.com", description="Email of the customer"),
     *     @OA\Property(property="phone", type="string", example="1234567890", description="Phone of the customer")
     *    )
     *  )
     * )
     */
    public function store(CustomerCreateRequest $request)
    {
        $customer = new Customer();
        $customer->name = $request->name;
        $customer->email = $request->email;
        if ($request->has('username')) {
            $customer->username = $request->username;
        }
        if ($request->has('password')) {
            $customer->password = $request->password;
        }
        if ($request->has('phone')) {
            $customer->phone = $request->phone;
        }
        if ($request->has('address')) {
            $customer->address = $request->address;
        }
        if ($request->has('city')) {
            $customer->city = $request->city;
        }
        if ($request->has('state')) {
            $customer->state = $request->state;
        }
        if ($request->has('zip')) {
            $customer->zip = $request->zip;
        }
        if ($request->has('country')) {
            $customer->country = $request->country;
        }
        if ($request->has('company')) {
            $customer->company = $request->company;
        }
        $customer->save();

        return response()->json([
            'status' => 'ok',
            'message' => 'Customer created',
            'data' => [
                'customer' => $customer,
            ],
        ]);
    }

    public function getHostingSubscriptionsByCustomerId($customerId)
    {
        $findCustomer = Customer::where('id', $customerId)->first();
        if (! $findCustomer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer not found',
            ], 404);
        }

        $findHostingSubscriptions = HostingSubscription::where('customer_id', $customerId)->get();
        if ($findHostingSubscriptions->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No hosting subscriptions found for this customer',
            ], 404);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Hosting subscriptions found',
            'data' => [
                'hostingSubscriptions' => $findHostingSubscriptions,
            ],
        ]);
    }

    public function loginWithToken($customerId, Request $request)
    {
        $findCustomer = Customer::where('id', $customerId)->first();
        if (!$findCustomer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer not found',
            ], 404);
        }


        $findToken = $findCustomer->tokens()->where('token', $request->token)->where('name', 'externalLogin')->first();
        if (!$findToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token not found',
            ], 404);
        }

        Auth::guard('web_customer')->loginUsingId($findCustomer->id);

        return redirect('/customer');
    }
    public function generateLoginToken($customerId, Request $request)
    {
        $findCustomer = Customer::where('id', $customerId)->first();
        if (! $findCustomer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer not found',
            ], 404);
        }

        $findCustomer->tokens()->delete();

        $token = $findCustomer->createToken('externalLogin',['*'], now()->addMinute());

        return response()->json([
            'status' => 'ok',
            'message' => 'Token generated',
            'data' => [
                'token' => $token->accessToken->token,
            ],
        ]);

    }
}
