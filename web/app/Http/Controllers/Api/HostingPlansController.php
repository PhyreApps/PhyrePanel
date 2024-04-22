<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Request\CustomerCreateRequest;
use App\Http\Controllers\ApiController;
use App\Models\Customer;
use App\Models\HostingPlan;
use Illuminate\Http\Request;

class HostingPlansController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/api/hosting-plans",
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
        $findHostingPlans = HostingPlan::all();

        return response()->json([
            'status' => 'ok',
            'message' => 'Hosting Plans found',
            'data' => [
                'hostingPlans' => $findHostingPlans->toArray(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:hosting_plans',
        ]);

        $hostingPlan = new HostingPlan();

        if ($request->name) {
            $hostingPlan->name = $request->name;
        }
        if ($request->description) {
            $hostingPlan->description = $request->description;
        }
        if ($request->disk_space) {
            $hostingPlan->disk_space = $request->disk_space;
        }
        if ($request->bandwidth) {
            $hostingPlan->bandwidth = $request->bandwidth;
        }
        if ($request->databases) {
            $hostingPlan->databases = $request->databases;
        }
        if ($request->ftp_accounts) {
            $hostingPlan->ftp_accounts = $request->ftp_accounts;
        }
        if ($request->email_accounts) {
            $hostingPlan->email_accounts = $request->email_accounts;
        }
        if ($request->subdomains) {
            $hostingPlan->subdomains = $request->subdomains;
        }
        if ($request->parked_domains) {
            $hostingPlan->parked_domains = $request->parked_domains;
        }
        if ($request->addon_domains) {
            $hostingPlan->addon_domains = $request->addon_domains;
        }
        if ($request->ssl_certificates) {
            $hostingPlan->ssl_certificates = $request->ssl_certificates;
        }
        if ($request->daily_backups) {
            $hostingPlan->daily_backups = $request->daily_backups;
        }
        if ($request->free_domain) {
            $hostingPlan->free_domain = $request->free_domain;
        }
        if ($request->additional_services) {
            $hostingPlan->additional_services = $request->additional_services;
        }
        if ($request->features) {
            $hostingPlan->features = $request->features;
        }
        if ($request->limitations) {
            $hostingPlan->limitations = $request->limitations;
        }
        if ($request->default_server_application_type) {
            $hostingPlan->default_server_application_type = $request->default_server_application_type;
        }
        if ($request->default_database_server_type) {
            $hostingPlan->default_database_server_type = $request->default_database_server_type;
        }
        if ($request->default_remote_database_server_id) {
            $hostingPlan->default_remote_database_server_id = $request->default_remote_database_server_id;
        }
        if ($request->default_server_application_settings) {
            $hostingPlan->default_server_application_settings = $request->default_server_application_settings;
        }

        $hostingPlan->save();

        return response()->json([
            'status' => 'ok',
            'message' => 'Hosting Plan created',
            'data' => [
                'hostingPlan' => $hostingPlan,
            ],
        ]);
    }
}
