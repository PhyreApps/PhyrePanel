<?php

namespace Modules\Email\App\Http\Livewire;

use Livewire\Component;
use Modules\Email\App\Models\DomainDkim;
use Modules\Email\App\Models\DomainDkimSigning;
use Modules\Email\DkimDomainSetup;

class DkimSetup extends Component
{
    public $domain;

    public function render()
    {
        $secure = $this->secure();
        $verify = $this->verify();
        $mainDomain = $this->getMainDomain();

        return view('email::livewire.dkim-setup', [
            'secure' => $secure,
            'verify' => $verify,
            'mainDomain' => $mainDomain,
            'serverIp' => $_SERVER['SERVER_ADDR'],
        ]);
    }

    public function getMainDomain()
    {
        $getMainDomain = '';
        $parseDomain = explode('.', $this->domain);
        if (count($parseDomain) > 2) {
            $getMainDomain = $parseDomain[1] . '.' . $parseDomain[2];
        } else {
            $getMainDomain = $this->domain;
        }

        return $getMainDomain;
    }
    public function verify()
    {
        $getMainDomain = $this->getMainDomain();

        $checks = [];
        $checkOne = shell_exec('dig @1.1.1.1 +short MX '.$getMainDomain);
        $checkOnePass = false;
        if (str_contains($checkOne, '10 mail.'.$this->domain)) {
            $checkOnePass = true;
        }
        $checks[] = [
            'check' => 'MX',
            'pass' => $checkOnePass,
            'result'=>$checkOne,
            'must'=>'10 mail'.$this->domain
        ];

        $checkTwo = shell_exec('dig @1.1.1.1 +short A mail.'.$this->domain);
        $checkTwo = trim($checkTwo);
        $getIpOfDomain = gethostbyname('mail.'.$this->domain);
        $checkTwoPass = false;
        if ($checkTwo == $getIpOfDomain) {
            $checkTwoPass = true;
        }
        $checks[] = [
            'check'=>'IP',
            'pass'=>$checkTwoPass,
            'result'=>$checkTwo,
            'must'=>$getIpOfDomain
        ];

        $checkThree = shell_exec('dig @1.1.1.1 +short -x ' . $getIpOfDomain);
        $checkThree = trim($checkThree);
        $checkTreePass = false;
        if (str_contains($checkThree, $this->domain)) {
            $checkTreePass = true;
        }

        $checks[] = [
            'check'=>'Reverse DNS',
            'pass'=>$checkTreePass,
            'result'=>$checkThree,
            'must'=>$this->domain
        ];

        return [
            'checks' => $checks,
            'pass' => $checkOnePass && $checkTwoPass && $checkTreePass,
        ];
    }

    public function secure()
    {
        $output = DkimDomainSetup::run($this->domain);
        if (isset($output['privateKey'])) {
            $findDomainDkim = DomainDkim::where('domain_name', $this->domain)->first();
            if (!$findDomainDkim) {
                $findDomainDkim = new DomainDkim();
                $findDomainDkim->domain_name = $this->domain;
            }
            $findDomainDkim->selector = 'mail';
            $findDomainDkim->private_key = $output['privateKey'];
            $findDomainDkim->public_key = $output['text'];
            $findDomainDkim->save();

            $findDomainDkimSigning = DomainDkimSigning::where('author', '*')->where('dkim_id', $findDomainDkim->id)->first();
            if (!$findDomainDkimSigning) {
                $findDomainDkimSigning = new DomainDkimSigning();
                $findDomainDkimSigning->author = '*';
                $findDomainDkimSigning->dkim_id = $findDomainDkim->id;
                $findDomainDkimSigning->save();
            }
        }

        return $output;
    }
}
