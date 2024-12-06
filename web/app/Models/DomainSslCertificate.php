<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainSslCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain',
        'certificate',
        'private_key',
        'certificate_chain',
        'customer_id',
        'is_active',
        'is_wildcard',
        'is_auto_renew',
        'expiration_date',
        'renewal_date',
        'renewed_date',
        'renewed_until_date',
    ];
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {

            shell_exec('rm -rf '.'/root/.acmephp/master/certs/'.$model->domain);
            shell_exec('rm -rf '.'/etc/letsencrypt/live/'.$model->domain);

        });
    }

    public function getSSLFiles()
    {
        $findDomain = Domain::where('domain', $this->domain)->first();
        if ($findDomain) {
            $domainRoot = $findDomain->domain_root;
            $certPath = $domainRoot . '/certs/' . $this->domain;

            return [
                'certificate' => $certPath . '/public/cert.pem',
                'certificateChain' => $certPath . '/public/fullchain.pem',
                'privateKey' => $certPath . '/private/key.private.pem',
            ];
        }

        return null;
    }

    public function relatedDomain()
    {
        return $this->belongsTo(Domain::class, 'domain', 'domain');
    }
}
