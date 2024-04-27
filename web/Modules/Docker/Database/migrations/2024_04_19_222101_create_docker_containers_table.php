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
        Schema::create('docker_containers', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            $table->string('command')->nullable();
            $table->string('docker_id')->nullable();
            $table->string('image')->nullable();
            $table->longText('labels')->nullable();
            $table->string('local_volumes')->nullable();
            $table->string('mounts')->nullable();
            $table->string('names')->nullable();
            $table->string('networks')->nullable();
            $table->string('ports')->nullable();
            $table->string('running_for')->nullable();
            $table->string('size')->nullable();
            $table->string('state')->nullable();
            $table->string('status')->nullable();

            $table->string('build_type')->nullable();
            $table->integer('docker_template_id')->nullable();
            $table->longText('docker_compose')->nullable();

            $table->string('memory_limit')->nullable();
            $table->tinyInteger('unlimited_memory')->nullable();
            $table->tinyInteger('automatic_start')->nullable();
            $table->string('port')->nullable();
            $table->string('external_port')->nullable();
            $table->longText('volume_mapping')->nullable();

            $table->longText('environment_variables')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docker_containers');
    }
};
