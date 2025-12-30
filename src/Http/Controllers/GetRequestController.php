<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Syofyanzuhad\FilamentZktecoAdms\Services\AdmsResponseBuilder;

class GetRequestController extends Controller
{
    public function __construct(
        protected AdmsResponseBuilder $responseBuilder
    ) {}

    public function __invoke(Request $request): Response
    {
        $serialNumber = $request->query('SN');

        if (! $serialNumber) {
            return $this->responseBuilder->error();
        }

        $deviceModel = config('zkteco-adms.models.device');
        $device = $deviceModel::where('serial_number', $serialNumber)->first();

        if (! $device) {
            return $this->responseBuilder->ok();
        }

        $device->markAsOnline();

        // Get pending commands for this device
        $pendingCommands = $device->pendingCommands()
            ->orderBy('created_at')
            ->take(10)
            ->get();

        if ($pendingCommands->isEmpty()) {
            return $this->responseBuilder->ok();
        }

        // Mark commands as sent
        foreach ($pendingCommands as $command) {
            $command->markAsSent();
        }

        return $this->responseBuilder->commands($pendingCommands);
    }
}
