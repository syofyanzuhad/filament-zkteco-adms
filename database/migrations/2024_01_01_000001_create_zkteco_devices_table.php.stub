<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('zkteco-adms.table_prefix', 'zkteco_') . 'devices', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->string('name')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('model')->nullable();
            $table->string('firmware_version')->nullable();
            $table->string('push_version')->nullable();
            $table->string('device_type')->nullable();
            $table->enum('status', ['online', 'offline', 'unknown'])->default('unknown');
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->unsignedBigInteger('att_stamp')->default(0);
            $table->unsignedBigInteger('op_stamp')->default(0);
            $table->json('options')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('zkteco-adms.table_prefix', 'zkteco_') . 'devices');
    }
};
