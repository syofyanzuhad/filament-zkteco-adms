<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Syofyanzuhad\FilamentZktecoAdms\Events\AttendanceReceived;
use Syofyanzuhad\FilamentZktecoAdms\Events\DeviceConnected;
use Syofyanzuhad\FilamentZktecoAdms\Events\UserSynced;
use Syofyanzuhad\FilamentZktecoAdms\Models\Device;
use Syofyanzuhad\FilamentZktecoAdms\Services\AdmsRequestParser;
use Syofyanzuhad\FilamentZktecoAdms\Services\AdmsResponseBuilder;

class CDataController extends Controller
{
    public function __construct(
        protected AdmsRequestParser $parser,
        protected AdmsResponseBuilder $responseBuilder
    ) {}

    public function __invoke(Request $request): Response
    {
        $serialNumber = $request->query('SN');
        $table = $request->query('table');
        $options = $request->query('options');

        if (! $serialNumber) {
            return $this->responseBuilder->error();
        }

        $device = $this->findOrCreateDevice($serialNumber, $request);

        if (! $device) {
            return $this->responseBuilder->error();
        }

        $device->markAsOnline();

        // GET request with options=all is device registration/config request
        if ($request->isMethod('GET') && $options === 'all') {
            return $this->handleOptionsRequest($device, $request);
        }

        // POST request with table parameter is data submission
        if ($request->isMethod('POST') && $table) {
            return $this->handleDataSubmission($device, $table, $request);
        }

        return $this->responseBuilder->ok();
    }

    protected function handleOptionsRequest(Device $device, Request $request): Response
    {
        $device->update([
            'push_version' => $request->query('pushver'),
            'device_type' => $request->query('DeviceType'),
            'firmware_version' => $request->query('FWVersion'),
        ]);

        if (config('zkteco-adms.events.dispatch_device_connected')) {
            event(new DeviceConnected($device));
        }

        return $this->responseBuilder->deviceOptions($device);
    }

    protected function handleDataSubmission(Device $device, string $table, Request $request): Response
    {
        $body = $request->getContent();
        $stamp = $request->query('Stamp');

        switch (strtoupper($table)) {
            case 'ATTLOG':
                $this->processAttendanceLogs($device, $body, $stamp);
                break;
            case 'OPERLOG':
                $this->processOperationLogs($device, $body, $stamp);
                break;
            case 'OPTIONS':
                $this->processOptions($device, $body);
                break;
        }

        return $this->responseBuilder->ok();
    }

    protected function processAttendanceLogs(Device $device, string $body, ?string $stamp): void
    {
        $logs = $this->parser->parseAttendanceLogs($body);
        $modelClass = config('zkteco-adms.models.attendance_log');

        foreach ($logs as $log) {
            $record = $modelClass::create([
                'device_id' => $device->id,
                'pin' => $log['pin'],
                'punched_at' => $log['punched_at'],
                'status' => $log['status'],
                'verify_type' => $log['verify_type'],
                'work_code' => $log['work_code'],
                'reserved_1' => $log['reserved_1'],
                'reserved_2' => $log['reserved_2'],
                'raw_data' => ['raw' => $log['raw']],
            ]);

            if (config('zkteco-adms.events.dispatch_attendance_received')) {
                event(new AttendanceReceived($record, $device));
            }
        }

        if ($stamp) {
            $device->update(['att_stamp' => max($device->att_stamp, (int) $stamp)]);
        }

        $device->update(['last_sync_at' => now()]);
    }

    protected function processOperationLogs(Device $device, string $body, ?string $stamp): void
    {
        $operations = $this->parser->parseOperationLogs($body);
        $userModel = config('zkteco-adms.models.user');

        foreach ($operations as $op) {
            if ($op['type'] === 'user' && isset($op['pin'])) {
                $user = $userModel::updateOrCreate(
                    ['pin' => $op['pin']],
                    [
                        'name' => $op['name'] ?? null,
                        'card_number' => $op['card'] ?? null,
                        'privilege' => (int) ($op['privilege'] ?? $op['pri'] ?? 0),
                        'password' => $op['password'] ?? $op['passwd'] ?? null,
                        'group' => $op['group'] ?? $op['grp'] ?? null,
                    ]
                );

                if (config('zkteco-adms.events.dispatch_user_synced')) {
                    event(new UserSynced($user, $device));
                }
            }

            if ($op['type'] === 'fingerprint' && isset($op['pin'])) {
                $user = $userModel::where('pin', $op['pin'])->first();

                if ($user) {
                    $fingerprints = $user->fingerprints ?? [];
                    $fingerprints[$op['fid'] ?? 0] = [
                        'size' => $op['size'] ?? null,
                        'valid' => $op['valid'] ?? null,
                        'tmp' => $op['tmp'] ?? null,
                    ];
                    $user->update(['fingerprints' => $fingerprints]);
                }
            }

            if ($op['type'] === 'face' && isset($op['pin'])) {
                $user = $userModel::where('pin', $op['pin'])->first();

                if ($user) {
                    $faceTemplates = $user->face_templates ?? [];
                    $faceTemplates[$op['fid'] ?? 0] = [
                        'size' => $op['size'] ?? null,
                        'valid' => $op['valid'] ?? null,
                        'tmp' => $op['tmp'] ?? null,
                    ];
                    $user->update(['face_templates' => $faceTemplates]);
                }
            }
        }

        if ($stamp) {
            $device->update(['op_stamp' => max($device->op_stamp, (int) $stamp)]);
        }
    }

    protected function processOptions(Device $device, string $body): void
    {
        $options = $this->parser->parseOptions($body);

        if (! empty($options)) {
            $device->update(['options' => array_merge($device->options ?? [], $options)]);
        }
    }

    protected function findOrCreateDevice(string $serialNumber, Request $request): ?Device
    {
        $modelClass = config('zkteco-adms.models.device');

        $device = $modelClass::where('serial_number', $serialNumber)->first();

        if (! $device && config('zkteco-adms.device.auto_register')) {
            $device = $modelClass::create([
                'serial_number' => $serialNumber,
                'name' => "Device {$serialNumber}",
                'ip_address' => $request->ip(),
                'status' => 'online',
            ]);
        }

        return $device;
    }
}
