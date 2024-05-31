<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailAccount extends Model
{
    use HasFactory;


    protected $fillable = [
        'domain_id',
        'username',
        'password',
        'last_login',

    ];

    public function domain(): HasOne
    {
        return $this->hasOne(Domain::class);
    }

}
