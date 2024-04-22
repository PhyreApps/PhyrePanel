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
        Schema::create('domains', function (Blueprint $table) {
            $table->id();

            $table->string('domain');
            $table->string('ip')->nullable();

            $table->string('home_root')->nullable();
            $table->string('domain_root')->nullable();
            $table->string('domain_public')->nullable();

            $table->integer('hosting_subscription_id')->nullable();

            $table->string('screenshot')->nullable();
            $table->integer('is_secure_with_ssl')->nullable();

            $table->integer('is_main')->nullable();

            $table->string('server_application_type')->nullable()->default('apache_php');
            $table->longText('server_application_settings')->nullable();

            $table->tinyInteger('is_installed_default_app_template')->nullable();

            $table->string('status')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
