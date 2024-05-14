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
        Schema::create('remote_backup_servers', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->string('hostname')->nullable();
            $table->string('port')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('path')->nullable();

            $table->longText('ssh_private_key')->nullable();
            $table->string('ssh_private_key_password')->nullable();

            $table->string('status')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remote_backup_servers');
    }
};
