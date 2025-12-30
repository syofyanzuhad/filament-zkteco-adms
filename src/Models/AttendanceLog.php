<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        return $this->belongsTo(config('zkteco-adms.models.device'));
    }

    public function zktecoUser(): BelongsTo
    {
        return $this->belongsTo(config('zkteco-adms.models.user'), 'pin', 'pin');
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
