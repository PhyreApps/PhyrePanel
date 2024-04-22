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
        Schema::create('hosting_subscriptions', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('phyre_server_id')->nullable();
            $table->bigInteger('external_id')->nullable();

            $table->bigInteger('customer_id')->nullable();
            $table->bigInteger('hosting_plan_id')->nullable();

            $table->string('domain')->nullable();
            $table->bigInteger('main_domain_id')->nullable();

            $table->string('system_username')->nullable();
            $table->string('system_password')->nullable();

            $table->longText('description')->nullable();

            $table->timestamp('setup_date')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->timestamp('renewal_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hosting_subscriptions');
    }
};
