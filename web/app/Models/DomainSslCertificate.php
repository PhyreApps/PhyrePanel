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
}
