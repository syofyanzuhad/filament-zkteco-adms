<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Syofyanzuhad\FilamentZktecoAdms\Models\AttendanceLog;
use Syofyanzuhad\FilamentZktecoAdms\Models\Device;

class AttendanceReceived
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public AttendanceLog $attendanceLog,
        public Device $device
    ) {}
}
