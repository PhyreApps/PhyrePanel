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
        Schema::create('microweber_installations', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('domain_id')->nullable();

            $table->string('app_version')->nullable();
            $table->string('installation_type')->nullable();
            $table->string('installation_path')->nullable();
            $table->string('template')->nullable();
            $table->string('template_screenshot_url')->nullable();

            $table->string('db_version')->nullable();
            $table->string('db_engine')->nullable();
            $table->string('db_host')->nullable();
            $table->string('db_port')->nullable();
            $table->string('db_name')->nullable();
            $table->string('db_user')->nullable();
            $table->string('db_password')->nullable();
            $table->string('db_prefix')->nullable();

            $table->string('admin_email')->nullable();
            $table->string('admin_password')->nullable();
            $table->string('admin_username')->nullable();
            $table->string('admin_first_name')->nullable();
            $table->string('admin_last_name')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('microweber_installations');
    }
};
