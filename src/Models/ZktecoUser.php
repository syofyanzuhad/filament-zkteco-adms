<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $pin
 * @property string|null $name
 * @property string|null $card_number
 * @property int $privilege
 * @property string|null $password
 * @property string|null $group
 * @property bool $is_enabled
 * @property array|null $fingerprints
 * @property array|null $face_templates
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
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
        return $this->hasMany(config('zkteco-adms.models.attendance_log', AttendanceLog::class), 'pin', 'pin');
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
