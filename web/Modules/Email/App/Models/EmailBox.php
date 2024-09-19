<?php

namespace Modules\Email\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Email\Database\factories\EmailBoxFactory;

class EmailBox extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'username',
        'password',
        'name',
        'maildir',
        'quota',
        'local_part',
        'domain',
        'created',
        'modified',
        'active',
        'phone',
        'email_other',
        'token',
        'token_validity',
        'password_expiry',
        'smtp_active',
    ];


    public function getQuotaFormatedAttribute()
    {
        if ($this->quota == 0) {
            return 'Unlimited';
        }

        return $this->quota . ' MB';
    }

    public function getEmailAttribute()
    {
        return $this->username . '@' . $this->domain;
    }
}
