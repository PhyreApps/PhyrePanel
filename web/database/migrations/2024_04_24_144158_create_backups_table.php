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

            $table->string('backup_type')->nullable();
            $table->string('status')->nullable();
            $table->longText('backup_log')->nullable();

            $table->string('path')->nullable();
            $table->string('root_path')->nullable();
            $table->string('temp_path')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();

            $table->string('size')->nullable();
            $table->string('disk')->nullable();
            $table->string('process_id')->nullable();
            $table->longText('settings')->nullable();

            $table->tinyInteger('queued')->nullable();
            $table->timestamp('queued_at')->nullable();

            $table->tinyInteger('started')->nullable();
            $table->timestamp('started_at')->nullable();

            $table->tinyInteger('completed')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->tinyInteger('failed')->nullable();
            $table->timestamp('failed_at')->nullable();

            $table->tinyInteger('cancelled')->nullable();
            $table->timestamp('cancelled_at')->nullable();

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
