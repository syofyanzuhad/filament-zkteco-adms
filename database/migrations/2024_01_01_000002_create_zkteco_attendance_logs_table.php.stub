<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('zkteco-adms.table_prefix', 'zkteco_') . 'attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')
                ->constrained(config('zkteco-adms.table_prefix', 'zkteco_') . 'devices')
                ->cascadeOnDelete();
            $table->string('pin');
            $table->timestamp('punched_at');
            $table->unsignedTinyInteger('status')->default(0);
            $table->unsignedTinyInteger('verify_type')->default(0);
            $table->unsignedTinyInteger('work_code')->nullable();
            $table->string('reserved_1')->nullable();
            $table->string('reserved_2')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamps();

            $table->index(['device_id', 'pin', 'punched_at']);
            $table->index('punched_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('zkteco-adms.table_prefix', 'zkteco_') . 'attendance_logs');
    }
};
