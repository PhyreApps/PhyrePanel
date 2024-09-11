<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GitRepository extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'branch',
        'last_commit_hash',
        'last_commit_message',
        'last_commit_date',
        'status',
        'status_message',
        'dir',
        'domain_id',
        'git_ssh_key_id',
    ];

}
