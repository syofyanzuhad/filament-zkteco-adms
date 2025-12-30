<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        return $this->belongsTo(config('zkteco-adms.models.device'));
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
