<?php

namespace Modules\LetsEncrypt\Filament\Clusters;

use Filament\Clusters\Cluster;

class LetsEncryptCluster extends Cluster
{
    protected static ?string $navigationIcon = 'lets_encrypt-logo';

    protected static ?string $navigationGroup = 'Server Management';

}
