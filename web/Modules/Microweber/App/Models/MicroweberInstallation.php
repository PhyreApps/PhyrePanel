<?php

namespace Modules\Microweber\App\Models;

use App\Models\Domain;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MicroweberInstallation extends Model
{
    use HasFactory;

    public function domain()
    {
        return $this->hasOne(Domain::class, 'id', 'domain_id');
    }

}
