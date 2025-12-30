<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('zkteco-adms.table_prefix', 'zkteco_') . 'users', function (Blueprint $table) {
            $table->id();
            $table->string('pin')->unique();
            $table->string('name')->nullable();
            $table->string('card_number')->nullable();
            $table->unsignedTinyInteger('privilege')->default(0);
            $table->string('password')->nullable();
            $table->string('group')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->json('fingerprints')->nullable();
            $table->json('face_templates')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('zkteco-adms.table_prefix', 'zkteco_') . 'users');
    }
};
