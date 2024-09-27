<?php

namespace Modules\Email\App\Http\Livewire;

use Livewire\Component;
use Modules\Email\DkimDomainSetup;

class DkimSetup extends Component
{
    public $domain;

    public function render()
    {
        $secure = $this->secure();

        return view('email::livewire.dkim-setup', [
            'secure' => $secure,
        ]);
    }

    public function verify()
    {
        $getMainDomain = '';
        $parseDomain = explode('.', $this->domain);
        if (count($parseDomain) > 2) {
            $getMainDomain = $parseDomain[1] . '.' . $parseDomain[2];
        } else {
            $getMainDomain = $this->domain;
        }

        $checks = [];
        $checkOne = shell_exec('dig @1.1.1.1 +short MX '.$getMainDomain);
        $checkOnePass = false;
        if (str_contains($checkOne, '10 '.$this->domain)) {
            $checkOnePass = true;
        }
        $checks[] = [
            'check' => 'MX',
            'pass' => $checkOnePass,
            'result'=>$checkOne
        ];

        $checkTwo = shell_exec('dig @1.1.1.1 +short A '.$this->domain);
        $checkTwo = trim($checkTwo);
        $getIpOfDomain = gethostbyname($this->domain);
        $checkTwoPass = false;
        if ($checkTwo == $getIpOfDomain) {
            $checkTwoPass = true;
        }
        $checks[] = [
            'check'=>'IP',
            'pass'=>$checkTwoPass,
            'result'=>$checkTwo
        ];

        $checkThree = shell_exec('dig @1.1.1.1 +short -x ' . $getIpOfDomain);
        $checkThree = trim($checkThree);
        $checkTreePass = false;
        if ($checkThree == $this->domain) {
            $checkTreePass = true;
        }
        $checks[] = [
            'check'=>'Reverse DNS',
            'pass'=>$checkTreePass,
            'result'=>$checkThree
        ];

        return $checks;
    }

    public function secure()
    {
        $output = DkimDomainSetup::run($this->domain);

        return $output;
    }
}