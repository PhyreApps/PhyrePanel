<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Request\HostingSubscriptionCreateRequest;
use App\Http\Controllers\ApiController;
use App\Models\Domain;
use App\Models\HostingSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HostingSubscriptionsController extends ApiController
{
    public function index()
    {
        $findHostingSubscriptions = HostingSubscription::all();

        return response()->json([
            'status' => 'ok',
            'message' => 'Hosting subscriptions found',
            'data' => [
                'hostingSubscriptions' => $findHostingSubscriptions,
            ],
        ]);

    }

    public function store(HostingSubscriptionCreateRequest $request)
    {

        $findDomain = Domain::where('domain', $request->domain)->first();
        if ($findDomain) {
            return response()->json([
                'status' => 'error',
                'message' => 'Domain already exists',
            ], 400);
        }

        if (isset($request->system_username)) {
            $findHostingSubscription = HostingSubscription::where('system_username', $request->system_username)->first();
            if ($findHostingSubscription) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'System username already exists',
                ], 400);
            }
        }

        $hostingSubscription = new HostingSubscription();
        $hostingSubscription->customer_id = $request->customer_id;
        $hostingSubscription->hosting_plan_id = $request->hosting_plan_id;
        $hostingSubscription->domain = $request->domain;

        if (isset($request->system_username)) {
            $hostingSubscription->system_username = $request->system_username;
        }

        if (isset($request->system_password)) {
            $hostingSubscription->system_password = $request->system_password;
        }

        $hostingSubscription->setup_date = Carbon::now();
        $hostingSubscription->save();

        return response()->json([
            'status' => 'ok',
            'message' => 'Hosting subscription created',
            'data' => [
                'hostingSubscription' => $hostingSubscription,
            ],
        ]);
    }

    public function destroy($id)
    {
        $findHostingSubscription = HostingSubscription::where('id', $id)->first();
        if ($findHostingSubscription) {
            $findHostingSubscription->delete();

            return response()->json([
                'status' => 'ok',
                'message' => 'Hosting subscription deleted',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Hosting subscription not found',
        ], 404);
    }

    public function update($id, Request $request)
    {
        $findHostingSubscription = HostingSubscription::where('id', $id)->first();
        if ($findHostingSubscription) {

            if (!empty($request->customer_id)) {
                $findHostingSubscription->customer_id = $request->customer_id;
            }

            $findHostingSubscription->save();

            return response()->json([
                'status' => 'ok',
                'message' => 'Hosting subscription updated',
                'data' => [
                    'hostingSubscription' => $findHostingSubscription,
                ],
            ]);
        }

    }

    public function suspend($id)
    {
        $findHostingSubscription = HostingSubscription::where('id', $id)->first();
        if ($findHostingSubscription) {

            $findDomains = Domain::where('hosting_subscription_id', $findHostingSubscription->id)->get();
            if ($findDomains->count() > 0) {
                foreach ($findDomains as $domain) {
                    $domain->status = Domain::STATUS_SUSPENDED;
                    $domain->save();
                }
            }

            return response()->json([
                'status' => 'ok',
                'message' => 'Hosting subscription suspended',
            ]);
        }

    }

    public function unsuspend($id)
    {
        $findHostingSubscription = HostingSubscription::where('id', $id)->first();
        if ($findHostingSubscription) {

            $findDomains = Domain::where('hosting_subscription_id', $findHostingSubscription->id)->get();
            if ($findDomains->count() > 0) {
                foreach ($findDomains as $domain) {
                    $domain->status = Domain::STATUS_ACTIVE;
                    $domain->save();
                }
            }

            return response()->json([
                'status' => 'ok',
                'message' => 'Hosting subscription unsuspended',
            ]);
        }

    }
}
