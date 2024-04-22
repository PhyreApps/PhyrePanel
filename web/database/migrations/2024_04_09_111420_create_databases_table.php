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
        Schema::create('databases', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('hosting_subscription_id')->nullable();

            $table->string('database_name')->nullable();
            $table->string('database_name_prefix')->nullable();

            $table->tinyInteger('is_remote_database_server')->nullable();
            $table->bigInteger('remote_database_server_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('databases');
    }
};
