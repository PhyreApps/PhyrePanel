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
        Schema::create('database_users', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('database_id')->nullable();

            $table->string('username')->nullable();
            $table->string('username_prefix')->nullable();
            $table->string('password')->nullable();

            $table->tinyInteger('access_to_all_databases_on_subscription')->nullable();
            $table->string('access_control')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('database_users');
    }
};
