<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $device_id
 * @property string $pin
 * @property Carbon $punched_at
 * @property int $status
 * @property int $verify_type
 * @property int|null $work_code
 * @property string|null $reserved_1
 * @property string|null $reserved_2
 * @property array|null $raw_data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class AttendanceLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'punched_at' => 'datetime',
        'raw_data' => 'array',
    ];

    public function getTable(): string
    {
        return config('zkteco-adms.table_prefix', 'zkteco_') . 'attendance_logs';
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(config('zkteco-adms.models.device', Device::class));
    }

    public function zktecoUser(): BelongsTo
    {
        return $this->belongsTo(config('zkteco-adms.models.user', ZktecoUser::class), 'pin', 'pin');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            0 => 'Check In',
            1 => 'Check Out',
            2 => 'Break Out',
            3 => 'Break In',
            4 => 'OT In',
            5 => 'OT Out',
            default => 'Unknown',
        };
    }

    public function getVerifyTypeLabelAttribute(): string
    {
        return match ($this->verify_type) {
            0 => 'Password',
            1 => 'Fingerprint',
            2 => 'Card',
            15 => 'Face',
            default => 'Unknown',
        };
    }
}
