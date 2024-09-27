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

    public function secure()
    {
        $output = DkimDomainSetup::run($this->domain);

        return $output;
    }
}
