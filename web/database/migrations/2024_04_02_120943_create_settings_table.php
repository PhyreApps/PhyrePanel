<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('settings.database_table_name'), function (Blueprint $table) {
            $table->id();
            $table->string('key')
                ->unique()
                ->index();
            $table->json('value')
                ->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('settings.database_table_name'));
    }
};
