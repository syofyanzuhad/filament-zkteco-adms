<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $device_id
 * @property string $command_type
 * @property string $command_content
 * @property string $status
 * @property Carbon|null $sent_at
 * @property Carbon|null $acknowledged_at
 * @property string|null $response
 * @property int $retry_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class DeviceCommand extends Model
{
    protected $guarded = [];

    protected $casts = [
        'sent_at' => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    public function getTable(): string
    {
        return config('zkteco-adms.table_prefix', 'zkteco_') . 'device_commands';
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(config('zkteco-adms.models.device', Device::class));
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markAsAcknowledged(?string $response = null): void
    {
        $this->update([
            'status' => 'acknowledged',
            'acknowledged_at' => now(),
            'response' => $response,
        ]);
    }

    public function markAsFailed(?string $response = null): void
    {
        $this->update([
            'status' => 'failed',
            'response' => $response,
        ]);
    }

    public function retry(): void
    {
        $this->update([
            'status' => 'pending',
            'retry_count' => $this->retry_count + 1,
        ]);
    }
}
