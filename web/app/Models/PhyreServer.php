<?php

namespace App\Models;

use App\ApiSDK\PhyreApiSDK;
use App\Events\ModelPhyreServerCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpseclib3\Net\SSH2;

class PhyreServer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip',
        'port',
        'username',
        'password',
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            event(new ModelPhyreServerCreated($model));
        });

    }

    public function syncResources()
    {
        // Sync customers
        $centralServerCustomerExternalIds = [];
        $getCentralServerCustomers = Customer::where('phyre_server_id', $this->id)->get();
        if ($getCentralServerCustomers->count() > 0) {
            foreach ($getCentralServerCustomers as $customer) {
                $centralServerCustomerExternalIds[] = $customer->external_id;
            }
        }

        $phyreApiSDK = new PhyreApiSDK($this->ip, 8443, $this->username, $this->password);
        $getPhyreServerCustomers = $phyreApiSDK->getCustomers();
        if (isset($getPhyreServerCustomers['data']['customers'])) {
            $phyreServerCustomerIds = [];
            foreach ($getPhyreServerCustomers['data']['customers'] as $customer) {
                $phyreServerCustomerIds[] = $customer['id'];
            }

            // Delete customers to main server that are not in external server
            foreach ($centralServerCustomerExternalIds as $centralServerCustomerExternalId) {
                if (!in_array($centralServerCustomerExternalId, $phyreServerCustomerIds)) {
                    $getCustomer = Customer::where('external_id', $centralServerCustomerExternalId)
                        ->where('phyre_server_id', $this->id)
                        ->first();
                    if ($getCustomer) {
                        $getCustomer->delete();
                    }
                }
            }

            // Add customers to main server from external server
            foreach ($getPhyreServerCustomers['data']['customers'] as $phyreServerCustomer) {
                $findCustomer = Customer::where('external_id', $phyreServerCustomer['id'])
                    ->where('phyre_server_id', $this->id)
                    ->first();
                if (!$findCustomer) {
                    $findCustomer = new Customer();
                    $findCustomer->phyre_server_id = $this->id;
                    $findCustomer->external_id = $phyreServerCustomer['id'];
                }
                $findCustomer->name = $phyreServerCustomer['name'];
                $findCustomer->username = $phyreServerCustomer['username'];
                $findCustomer->email = $phyreServerCustomer['email'];
                $findCustomer->phone = $phyreServerCustomer['phone'];
                $findCustomer->address = $phyreServerCustomer['address'];
                $findCustomer->city = $phyreServerCustomer['city'];
                $findCustomer->state = $phyreServerCustomer['state'];
                $findCustomer->zip = $phyreServerCustomer['zip'];
                $findCustomer->country = $phyreServerCustomer['country'];
                $findCustomer->company = $phyreServerCustomer['company'];
                $findCustomer->saveQuietly();
            }
        }

        // Sync Hosting Subscriptions
        $centralServerHostingSubscriptionsExternalIds = [];
        $getCentralHostingSubscriptions = HostingSubscription::where('phyre_server_id', $this->id)->get();
        if ($getCentralHostingSubscriptions->count() > 0) {
            foreach ($getCentralHostingSubscriptions as $customer) {
                $centralServerHostingSubscriptionsExternalIds[] = $customer->external_id;
            }
        }
        $getPhyreServerHostingSubscriptions = $phyreApiSDK->getHostingSubscriptions();
        if (isset($getPhyreServerHostingSubscriptions['data']['HostingSubscriptions'])) {
            foreach ($getPhyreServerHostingSubscriptions['data']['HostingSubscriptions'] as $phyreServerHostingSubscription) {

                $findHostingSubscription = HostingSubscription::where('external_id', $phyreServerHostingSubscription['id'])
                    ->where('phyre_server_id', $this->id)
                    ->first();
                if (!$findHostingSubscription) {
                    $findHostingSubscription = new HostingSubscription();
                    $findHostingSubscription->phyre_server_id = $this->id;
                    $findHostingSubscription->external_id = $phyreServerHostingSubscription['id'];
                }

                $findHostingSubscriptionCustomer = Customer::where('external_id', $phyreServerHostingSubscription['customer_id'])
                    ->where('phyre_server_id', $this->id)
                    ->first();
                if ($findHostingSubscriptionCustomer) {
                    $findHostingSubscription->customer_id = $findHostingSubscriptionCustomer->id;
                }

                $findHostingSubscription->system_username = $phyreServerHostingSubscription['system_username'];
                $findHostingSubscription->system_password = $phyreServerHostingSubscription['system_password'];

                $findHostingSubscription->domain = $phyreServerHostingSubscription['domain'];
                $findHostingSubscription->save();

            }
        }


//        // Sync Hosting Plans
//        $getHostingPlans = HostingPlan::all();
//        if ($getHostingPlans->count() > 0) {
//            foreach ($getHostingPlans as $hostingPlan) {
//
//            }
//        }
    }

    public function updateServer()
    {
        $ssh = new SSH2($this->ip);
        if ($ssh->login($this->username, $this->password)) {
//
//            $output = $ssh->exec('cd /usr/local/phyre/web && /usr/local/phyre/php/bin/php artisan apache:ping-websites-with-curl');
//            dd($output);

            $output = '';
            $output .= $ssh->exec('wget https://raw.githubusercontent.com/PhyreApps/PhyrePanel/main/update/update-web-panel.sh -O /usr/local/phyre/update/update-web-panel.sh');
            $output .= $ssh->exec('chmod +x /usr/local/phyre/update/update-web-panel.sh');
            $output .= $ssh->exec('/usr/local/phyre/update/update-web-panel.sh');

            dd($output);

            $this->healthCheck();
        }
    }

    public function healthCheck()
    {
        try {
            $phyreApiSDK = new PhyreApiSDK($this->ip, 8443, $this->username, $this->password);
            $response = $phyreApiSDK->healthCheck();
            if (isset($response['status']) && $response['status'] == 'ok') {
                $this->status = 'Online';
                $this->save();
            } else {
                $this->status = 'Offline';
                $this->save();
            }
        } catch (\Exception $e) {
            $this->status = 'Offline';
            $this->save();
        }

    }

}
