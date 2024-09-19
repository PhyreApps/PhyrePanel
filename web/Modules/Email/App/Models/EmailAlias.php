<?php

namespace Modules\Email\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Email\Database\factories\EmailAliasFactory;

class EmailAlias extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'address',
        'goto',
        'domain',
    ];


    public function getForwardAttribute()
    {
        return $this->address . '@' . $this->domain;
    }
}
