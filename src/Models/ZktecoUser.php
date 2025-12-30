<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ZktecoUser extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_enabled' => 'boolean',
        'fingerprints' => 'array',
        'face_templates' => 'array',
    ];

    public function getTable(): string
    {
        return config('zkteco-adms.table_prefix', 'zkteco_') . 'users';
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(config('zkteco-adms.models.attendance_log'), 'pin', 'pin');
    }

    public function getPrivilegeLabelAttribute(): string
    {
        return match ($this->privilege) {
            0 => 'User',
            14 => 'Admin',
            default => 'Unknown',
        };
    }
}
