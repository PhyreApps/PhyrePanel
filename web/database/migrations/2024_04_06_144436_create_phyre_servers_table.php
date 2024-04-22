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
        Schema::create('phyre_servers', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            $table->string('ip')->unique();
            $table->string('port')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('status')->default('offline');
            $table->string('server_type')->nullable();
            $table->string('server_version')->nullable();
            $table->string('server_os')->nullable();
            $table->string('server_arch')->nullable();
            $table->string('server_uptime')->nullable();
            $table->string('server_cpu')->nullable();
            $table->string('server_ram')->nullable();
            $table->string('server_disk')->nullable();
            $table->string('server_swap')->nullable();
            $table->string('server_load')->nullable();
            $table->string('server_processes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phyre_servers');
    }
};
