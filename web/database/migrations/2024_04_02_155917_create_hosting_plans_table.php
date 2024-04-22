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
        Schema::create('hosting_plans', function (Blueprint $table) {
            $table->id();

            $table->string('name');
          //  $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->integer('disk_space')->nullable();
            $table->integer('bandwidth')->nullable();
            $table->integer('databases')->nullable();
            $table->integer('ftp_accounts')->nullable();
            $table->integer('email_accounts')->nullable();
            $table->integer('subdomains')->nullable();
            $table->integer('parked_domains')->nullable();
            $table->integer('addon_domains')->nullable();
            $table->integer('ssl_certificates')->nullable();
            $table->integer('daily_backups')->nullable();
            $table->integer('free_domain')->nullable();

            $table->longText('additional_services')->nullable();
            $table->longText('features')->nullable();
            $table->longText('limitations')->nullable();

            $table->string('default_server_application_type')->nullable();
            $table->longText('default_server_application_settings')->nullable();
            $table->string('default_database_server_type')->nullable();
            $table->bigInteger('default_remote_database_server_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hosting_plans');
    }
};
