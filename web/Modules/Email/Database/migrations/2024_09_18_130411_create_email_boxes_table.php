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
        Schema::create('email_boxes', function (Blueprint $table) {
            $table->id();

            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('name')->nullable();
            $table->string('maildir')->nullable();
            $table->bigInteger('quota')->nullable();
            $table->string('local_part')->nullable();
            $table->string('domain')->nullable();
            $table->dateTime('created')->default('2000-01-01 00:00:00');
            $table->dateTime('modified')->default('2000-01-01 00:00:00');
            $table->tinyInteger('active')->default(1);
            $table->string('phone')->nullable();
            $table->string('email_other')->nullable();
            $table->string('token')->nullable();
            $table->dateTime('token_validity')->default('2000-01-01 00:00:00');
            $table->dateTime('password_expiry')->default('2000-01-01 00:00:00');
            $table->tinyInteger('smtp_active')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_boxes');
    }
};
