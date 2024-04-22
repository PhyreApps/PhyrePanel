<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'disk_space',
        'bandwidth',
        'databases',
        'ftp_accounts',
        'email_accounts',
        'subdomains',
        'parked_domains',
        'addon_domains',
        'ssl_certificates',
        'daily_backups',
        'free_domain',
        'additional_services',
        'features',
        'limitations',
        'default_server_application_type',
        'default_database_server_type',
        'default_remote_database_server_id',
        'default_server_application_settings',
    ];

    protected $casts = [
        'additional_services' => 'array',
        'features' => 'array',
        'limitations' => 'array',
        'default_server_application_settings' => 'array',
    ];
}
