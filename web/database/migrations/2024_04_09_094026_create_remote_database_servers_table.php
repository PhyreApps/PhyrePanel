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
        Schema::create('remote_database_servers', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            $table->string('host')->nullable();
            $table->string('port')->nullable();

            $table->string('database_type')->nullable();

            $table->string('username')->nullable();
            $table->string('password')->nullable();

            $table->string('status')->default('offline');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remote_database_servers');
    }
};
