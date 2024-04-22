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
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->string('hosting_account_id');
            $table->string('name');
            $table->string('size');
            $table->string('max_size');
            $table->string('max_user_connections');
            $table->string('max_connections');
            $table->string('max_queries_per_hour');
            $table->string('max_updates_per_hour');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};
