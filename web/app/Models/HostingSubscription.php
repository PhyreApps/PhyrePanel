<?php

namespace App\Models;

use App\Actions\CreateLinuxWebUser;
use App\Actions\GetLinuxUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HostingSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'phyre_server_id',
        'external_id',
        'domain',
        'customer_id',
        'hosting_plan_id',
        'system_username',
        'system_password',
        'description',
        'setup_date',
        'expiry_date',
        'renewal_date',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $create = $model->_createLinuxWebUser($model);
            if (isset($create['system_username']) && isset($create['system_password'])) {
                $model->system_username = $create['system_username'];
                $model->system_password = $create['system_password'];
            } else {
                return false;
            }
        });

        static::created(function ($model) {
            $makeMainDomain = new Domain();
            $makeMainDomain->hosting_subscription_id = $model->id;
            $makeMainDomain->domain = $model->domain;
            $makeMainDomain->is_main = 1;
            $makeMainDomain->status = Domain::STATUS_ACTIVE;
            $makeMainDomain->save();
        });

        static::deleting(function ($model) {

            $getLinuxUser = new GetLinuxUser();
            $getLinuxUser->setUsername($model->system_username);
            $getLinuxUserStatus = $getLinuxUser->handle();

            if (! empty($getLinuxUserStatus)) {
                shell_exec('userdel '.$model->system_username);
                shell_exec('rm -rf /home/'.$model->system_username);
            }
            $findRelatedDomains = Domain::where('hosting_subscription_id', $model->id)->get();
            if ($findRelatedDomains->count() > 0) {
                foreach ($findRelatedDomains as $domain) {
                    $domain->delete();
                }
            }

        });

    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function hostingPlan()
    {
        return $this->belongsTo(HostingPlan::class);
    }

    public function databases()
    {
        return $this->hasMany(Database::class);
    }

    private function _createLinuxWebUser($model): array
    {
        $findCustomer = Customer::where('id', $model->customer_id)->first();
        if (! $findCustomer) {
            return [];
        }

        $systemUsername = $this->_generateUsername($model->domain . $findCustomer->id);
        if ($this->_startsWithNumber($systemUsername)) {
            $systemUsername = $this->_generateUsername(Str::random(4));
        }

        $getLinuxUser = new GetLinuxUser();
        $getLinuxUser->setUsername($systemUsername);
        $linuxUser = $getLinuxUser->handle();

        if (! empty($linuxUser)) {
            $systemUsername = $this->_generateUsername($systemUsername.$findCustomer->id.Str::random(4));
        }

        $systemPassword = Str::random(14);

        $createLinuxWebUser = new CreateLinuxWebUser();
        $createLinuxWebUser->setUsername($systemUsername);
        $createLinuxWebUser->setPassword($systemPassword);
        $createLinuxWebUserOutput = $createLinuxWebUser->handle();

        if (strpos($createLinuxWebUserOutput, 'Creating home directory') !== false) {

            return [
                'system_username' => $systemUsername,
                'system_password' => $systemPassword
            ];

        }

        return [];

    }
    private static function _generateUsername($string)
    {
        $removedMultispace = preg_replace('/\s+/', ' ', $string);
        $sanitized = preg_replace('/[^A-Za-z0-9\ ]/', '', $removedMultispace);
        $lowercased = strtolower($sanitized);
        $lowercased = str_replace(' ', '', $lowercased);
        $lowercased = trim($lowercased);
        if (strlen($lowercased) > 10) {
            $lowercased = substr($lowercased, 0, 4);
        }

        $username = $lowercased.rand(1111, 9999).Str::random(4);
        $username = strtolower($username);

        return $username;
    }
    private function _startsWithNumber($string) {
        return strlen($string) > 0 && ctype_digit(substr($string, 0, 1));
    }
}
