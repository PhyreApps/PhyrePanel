<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RemoteBackupServer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'hostname',
        'port',
        'username',
        'password',
        'path',
        'ssh_private_key',
        'ssh_private_key_password',
    ];
}
