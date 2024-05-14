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
        Schema::create('minecraft_servers', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('customer_id')->nullable();
            $table->string('name')->nullable();
            $table->string('ip')->nullable();
            $table->string('port')->nullable();
            $table->string('rcon_port')->nullable();
            $table->string('rcon_password')->nullable();
            $table->string('status')->nullable();
            $table->string('version')->nullable();
            $table->string('memory')->nullable();
            $table->string('disk')->nullable();
            $table->string('cpu')->nullable();
            $table->string('players')->nullable();
            $table->string('max_players')->nullable();
            $table->string('world')->nullable();
            $table->string('seed')->nullable();
            $table->string('difficulty')->nullable();
            $table->string('game_mode')->nullable();
            $table->string('level_type')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('minecraft_servers');
    }
};
