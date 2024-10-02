<?php

namespace Modules\Email\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Email\Database\factories\DomainDkimSigningFactory;

class DomainDkimSigning extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    protected $table = 'domain_dkim_signings';


    public function dkim()
    {
        return $this->belongsTo(DomainDkim::class);
    }
}
