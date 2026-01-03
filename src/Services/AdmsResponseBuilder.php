<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Services;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Syofyanzuhad\FilamentZktecoAdms\Models\Device;

class AdmsResponseBuilder
{
    public function ok(): Response
    {
        return response('OK', 200, ['Content-Type' => 'text/plain']);
    }

    public function error(): Response
    {
        return response('ERROR', 200, ['Content-Type' => 'text/plain']);
    }

    public function deviceOptions(Device $device): Response
    {
        $config = config('zkteco-adms.response', [
            'error_delay' => 60,
            'delay' => 30,
            'realtime' => 1,
            'encrypt' => 0,
        ]);

        $options = [
            "GET OPTION FROM: {$device->serial_number}",
            "Stamp={$device->att_stamp}",
            "OpStamp={$device->op_stamp}",
            "ErrorDelay={$config['error_delay']}",
            "Delay={$config['delay']}",
            'TransTimes=' . config('zkteco-adms.device.default_trans_times', 10),
            'TransInterval=' . config('zkteco-adms.device.default_trans_interval', 1),
            'TransFlag=TransData AttLog OpLog AttPhoto',
            "Realtime={$config['realtime']}",
            "Encrypt={$config['encrypt']}",
        ];

        return response(implode("\n", $options), 200, ['Content-Type' => 'text/plain']);
    }

    public function commands(Collection $commands): Response
    {
        if ($commands->isEmpty()) {
            return response('OK', 200, ['Content-Type' => 'text/plain']);
        }

        $commandStrings = [];

        foreach ($commands as $command) {
            $commandStrings[] = "C:{$command->id}:{$command->command_content}";
        }

        return response(implode("\n", $commandStrings), 200, ['Content-Type' => 'text/plain']);
    }
}
