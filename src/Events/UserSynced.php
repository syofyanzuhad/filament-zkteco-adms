<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Syofyanzuhad\FilamentZktecoAdms\Models\Device;
use Syofyanzuhad\FilamentZktecoAdms\Models\ZktecoUser;

class UserSynced
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public ZktecoUser $user,
        public Device $device
    ) {}
}
