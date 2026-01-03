<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Syofyanzuhad\FilamentZktecoAdms\Models\Device;
use Syofyanzuhad\FilamentZktecoAdms\Models\DeviceCommand;
use Syofyanzuhad\FilamentZktecoAdms\Services\AdmsResponseBuilder;

class DeviceCmdController extends Controller
{
    public function __construct(
        protected AdmsResponseBuilder $responseBuilder
    ) {}

    public function __invoke(Request $request): Response
    {
        $serialNumber = $request->query('SN');
        $commandId = $request->query('ID');
        $return = $request->query('Return');

        if (! $serialNumber) {
            return $this->responseBuilder->error();
        }

        $deviceModel = config('zkteco-adms.models.device', Device::class);
        $device = $deviceModel::where('serial_number', $serialNumber)->first();

        if ($device) {
            $device->markAsOnline();
        }

        // If command ID provided, acknowledge the command
        if ($commandId) {
            $commandModel = config('zkteco-adms.models.device_command', DeviceCommand::class);
            $command = $commandModel::find($commandId);

            if ($command) {
                // Return value of 0 means success
                if ($return === '0' || $return === null) {
                    $command->markAsAcknowledged($return);
                } else {
                    $command->markAsFailed($return);
                }
            }
        }

        return $this->responseBuilder->ok();
    }
}
