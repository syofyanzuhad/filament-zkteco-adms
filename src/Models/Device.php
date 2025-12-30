<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    protected $guarded = [];

    protected $casts = [
        'last_activity_at' => 'datetime',
        'last_sync_at' => 'datetime',
        'options' => 'array',
    ];

    public function getTable(): string
    {
        return config('zkteco-adms.table_prefix', 'zkteco_') . 'devices';
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(config('zkteco-adms.models.attendance_log'));
    }

    public function commands(): HasMany
    {
        return $this->hasMany(config('zkteco-adms.models.device_command'));
    }

    public function pendingCommands(): HasMany
    {
        return $this->commands()->where('status', 'pending');
    }

    public function isOnline(): bool
    {
        if (! $this->last_activity_at) {
            return false;
        }

        $threshold = config('zkteco-adms.device.offline_threshold_minutes', 10);

        return $this->last_activity_at->diffInMinutes(now()) < $threshold;
    }

    public function markAsOnline(): void
    {
        $this->update([
            'status' => 'online',
            'last_activity_at' => now(),
        ]);
    }

    public function markAsOffline(): void
    {
        $this->update([
            'status' => 'offline',
        ]);
    }
}
