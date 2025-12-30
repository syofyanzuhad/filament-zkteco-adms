<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Syofyanzuhad\FilamentZktecoAdms\Models\Device;

class DeviceConnected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Device $device
    ) {}
}
