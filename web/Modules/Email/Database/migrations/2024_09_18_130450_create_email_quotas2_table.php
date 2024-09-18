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
        Schema::create('email_quotas2', function (Blueprint $table) {
            $table->id();

            $table->string('username', 100)->nullable();
            $table->bigInteger('bytes')->default(0);
            $table->integer('messages')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_quotas2');
    }
};
