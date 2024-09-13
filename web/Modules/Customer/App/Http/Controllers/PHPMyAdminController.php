<?php

namespace Modules\Customer\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\HostingSubscription;
use App\Models\PHPMyAdminSSOToken;

class PHPMyAdminController extends Controller
{
    public function login($id)
    {
        ///usr/share/doc/phpmyadmin.

        $hostingSubscription = HostingSubscription::where('id', $id)
        //    ->where('customer_id', auth()->user()->id)// TODO
            ->first();

        // Delete old sso tokens
        PHPMyAdminSSOToken::where('customer_id', $hostingSubscription->customer_id)
            ->where('hosting_subscription_id', $hostingSubscription->id)
            ->delete();

        // Create new sso token
        $ssoToken = new PHPMyAdminSSOToken();
        $ssoToken->customer_id = $hostingSubscription->customer_id;
        $ssoToken->hosting_subscription_id = $hostingSubscription->id;
        $ssoToken->token = md5(uniqid() . time() . $hostingSubscription->customer_id . $hostingSubscription->id);
        $ssoToken->expires_at = now()->addMinutes(1);
        $ssoToken->ip_address = request()->ip();
        $ssoToken->user_agent = request()->userAgent();
        $ssoToken->save();

        $currentUrl = url('/');
        $currentUrl = str_replace(':8443', ':8440', $currentUrl);

        return redirect($currentUrl . '/phyre-sso.php?server=1&token=' . $ssoToken->token . '&panel_url=' . url('/'));

    }

    public function validateToken()
    {
        $token = request()->input('token');
        $ssoToken = PHPMyAdminSSOToken::where('token', $token)->first();
        if (!$ssoToken) {
            return response()->json(['error' => 'Invalid token'], 400);
        }

        if ($ssoToken->expires_at < now()) {
            return response()->json(['error' => 'Token expired'], 400);
        }

        // Delete token after validation
        $ssoToken->delete();

        $hostingSubscription = HostingSubscription::where('id', $ssoToken->hosting_subscription_id)->first();
        if (!$hostingSubscription) {
            return response()->json(['error' => 'Invalid hosting subscription'], 400);
        }

        return response()->json([
            'success' => true,
            'databaseLoginDetails' => [
                'host' => '127.0.0.1',
                'username' => $hostingSubscription->system_username,
                'password' => $hostingSubscription->system_password,
            ]
        ]);
    }

}
