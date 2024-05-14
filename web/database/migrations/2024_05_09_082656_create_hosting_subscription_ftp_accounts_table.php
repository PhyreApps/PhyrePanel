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
        Schema::create('hosting_subscription_ftp_accounts', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('hosting_subscription_id')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('path')->nullable();
            $table->string('quota')->nullable();
            $table->tinyInteger('unlimited_quota')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hosting_subscription_ftp_accounts');
    }
};
