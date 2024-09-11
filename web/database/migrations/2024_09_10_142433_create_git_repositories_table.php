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
        Schema::create('git_repositories', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('url');
            $table->string('branch')->nullable();
            $table->string('tag')->nullable();
            $table->string('clone_from')->nullable();
            $table->string('last_commit_hash')->nullable();
            $table->string('last_commit_message')->nullable();
            $table->timestamp('last_commit_date')->nullable();

            $table->string('status')->nullable();
            $table->string('status_message')->nullable();

            $table->string('dir')->nullable();

            $table->unsignedBigInteger('domain_id');
            $table->unsignedBigInteger('git_ssh_key_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('git_repositories');
    }
};
