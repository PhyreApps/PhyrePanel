<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
        
            $table->string('domain')->nullable();
            $table->string('email')->nullable();
            $table->longText('certificate')->nullable();
            $table->longText('private_key')->nullable();
            $table->longText('chain')->nullable();
            $table->longText('fullchain')->nullable();
            $table->string('expires_at')->nullable();
            $table->string('status')->nullable();
            $table->bigInteger('domain_id')->nullable();
            $table->bigInteger('domain_ssl_certificate_id')->nullable();
            $table->string('type')->nullable();
            $table->string('issuer')->nullable();
            $table->string('valid_from')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
