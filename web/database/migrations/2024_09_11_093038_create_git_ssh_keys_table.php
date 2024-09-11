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
        Schema::create('git_ssh_keys', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->longText('private_key');
            $table->longText('public_key');
            $table->string('passphrase')->nullable();
            $table->string('status')->nullable();
            $table->string('status_message')->nullable();

            $table->unsignedBigInteger('hosting_subscription_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('git_ssh_keys');
    }
};
