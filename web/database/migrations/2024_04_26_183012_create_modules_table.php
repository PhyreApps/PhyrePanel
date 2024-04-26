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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('namespace')->nullable();
            $table->tinyInteger('installed')->nullable();
            $table->string('version')->nullable();
            $table->string('author')->nullable();
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('developer_url')->nullable();
            $table->string('screenshot')->nullable();
            $table->string('license')->nullable();
            $table->string('license_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
