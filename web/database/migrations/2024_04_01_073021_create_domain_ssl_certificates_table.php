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
        Schema::create('domain_ssl_certificates', function (Blueprint $table) {
            $table->id();

            $table->string('domain');

            $table->string('provider')->nullable();

            $table->integer('customer_id')->nullable();
            $table->integer('is_active')->nullable();
            $table->integer('is_wildcard')->nullable();
            $table->integer('is_auto_renew')->nullable();

            $table->timestamp('expiration_date')->nullable();
            $table->timestamp('renewal_date')->nullable();
            $table->timestamp('renewed_date')->nullable();
            $table->timestamp('renewed_until_date')->nullable();

            $table->longText('certificate')->nullable();
            $table->longText('private_key')->nullable();
            $table->longText('certificate_chain')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_ssl_certificates');
    }
};
