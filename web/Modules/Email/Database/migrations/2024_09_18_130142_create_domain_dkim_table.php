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
        Schema::create('domain_dkim', function (Blueprint $table) {
            $table->id();

            $table->string('domain_name');
            $table->string('description')->default('');
            $table->string('selector')->default('mail');
            $table->text('private_key');
            $table->text('public_key');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_dkim');
    }
};
