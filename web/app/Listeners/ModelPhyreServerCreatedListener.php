<?php

namespace App\Listeners;


use App\Events\ModelPhyreServerCreated;
use App\Models\PhyreServer;
use Illuminate\Remote\Connection;
use phpseclib3\Net\SSH2;
use Spatie\Ssh\Ssh;

class ModelPhyreServerCreatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ModelPhyreServerCreated $event): void
    {
        $findPhyreServer =  PhyreServer::where('id', $event->model->id)->first();
        if (!$findPhyreServer) {
            return;
        }
        if ($findPhyreServer->status == 'installing') {
            return;
        }
        $username = $event->model->username;
        $password = $event->model->password;
        $ip = $event->model->ip;

        $ssh = new SSH2($ip);
        if ($ssh->login($username, $password)) {

            $ssh->exec('wget https://raw.githubusercontent.com/PhyreApps/PhyrePanel/main/installers/install.sh');
            $ssh->exec('chmod +x install.sh');
            $ssh->exec('./install.sh  >phyre-install.log 2>&1 </dev/null &');

            $findPhyreServer->status = 'installing';
            $findPhyreServer->save();

        } else {
            $findPhyreServer->status = 'can\'t connect to server';
            $findPhyreServer->save();
        }
    }
}
