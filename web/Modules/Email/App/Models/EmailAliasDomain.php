<?php

namespace Modules\Email\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Email\Database\factories\EmailAliasDomainFactory;

class EmailAliasDomain extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [

        'alias_domain',
        'target_domain',

    ];

}
