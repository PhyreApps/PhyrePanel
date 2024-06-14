<?php

namespace app\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Domain;
use App\Models\HostingSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DomainsController extends ApiController
{
    public function index(Request $request)
    {
        $findDomainsQuery = Domain::query();

        $filterDomain = $request->get('domain', null);
        if ($filterDomain) {
            $findDomainsQuery->where('domain', $filterDomain);
        }

        $findDomains = $findDomainsQuery->get();

        return response()->json([
            'status' => 'ok',
            'message' => 'Domains found',
            'data' => [
                'domains' => $findDomains,
            ],
        ]);

    }

    public function store(Request $request)
    {

        $domain = new Domain();
        $domain->domain = $request->domain;
        $domain->domain_root = $request->domain_root;
        $domain->ip = '';
        $domain->hosting_subscription_id = $request->hosting_subscription_id;
        $domain->server_application_type = $request->server_application_type;
        $domain->server_application_settings = $request->server_application_settings;
        $domain->save();

        return response()->json([
            'status' => 'ok',
            'message' => 'Domain created',
            'data' => [
                'domain' => $domain,
            ],
        ]);
    }

    public function destroy($id)
    {
        $findDomain = Domain::where('id', $id)->first();
        if ($findDomain) {

            if ($findDomain->is_main) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Main domain cannot be deleted',
                ], 400);
            }

            $findDomain->delete();

            return response()->json([
                'status' => 'ok',
                'message' => 'Domain is deleted',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Domain not found',
        ], 404);
    }

    public function update($id, Request $request)
    {
        $findDomain = Domain::where('id', $id)->first();
        if ($findDomain) {

            if (!empty($request->domain) && $request->domain != $findDomain->domain) {

                $findNewDomainNameIfExist = Domain::where('domain', $request->domain)->first();
                if ($findNewDomainNameIfExist) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Domain already exist',
                    ], 400);
                }
                $findHostingSubscription = HostingSubscription::where('id', $findDomain->hosting_subscription_id)->first();
                if (!$findHostingSubscription) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Hosting subscription not found',
                    ], 404);
                }

                $findHostingSubscription->domain = $request->domain;
                $findHostingSubscription->save();

                $findDomain->domain = $request->domain;
            }

            $findDomain->save();

            return response()->json([
                'status' => 'ok',
                'message' => 'Domain updated',
                'data' => [
                    'domain' => $findDomain,
                ],
            ]);
        }

    }
}
