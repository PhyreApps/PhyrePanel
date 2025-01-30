<?php

namespace Modules\SSLManager\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\SSLManager\Database\factories\CertificateFactory;

class Certificate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
    
    protected static function newFactory(): CertificateFactory
    {
        //return CertificateFactory::new();
    }
}
