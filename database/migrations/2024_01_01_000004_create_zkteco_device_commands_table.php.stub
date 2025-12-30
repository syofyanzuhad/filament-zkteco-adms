<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('zkteco-adms.table_prefix', 'zkteco_') . 'device_commands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')
                ->constrained(config('zkteco-adms.table_prefix', 'zkteco_') . 'devices')
                ->cascadeOnDelete();
            $table->string('command_type');
            $table->text('command_content');
            $table->enum('status', ['pending', 'sent', 'acknowledged', 'failed'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->text('response')->nullable();
            $table->unsignedInteger('retry_count')->default(0);
            $table->timestamps();

            $table->index(['device_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('zkteco-adms.table_prefix', 'zkteco_') . 'device_commands');
    }
};
