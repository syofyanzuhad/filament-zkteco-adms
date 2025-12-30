<?php

use Syofyanzuhad\FilamentZktecoAdms\Models\Device;
use Syofyanzuhad\FilamentZktecoAdms\Models\DeviceCommand;

describe('GetRequestController', function () {
    it('returns error when no serial number provided', function () {
        $response = $this->get('/iclock/getrequest');

        $response->assertStatus(200);
        expect($response->getContent())->toContain('ERR');
    });

    it('returns OK when device not found', function () {
        $response = $this->get('/iclock/getrequest?SN=NONEXISTENT');

        $response->assertStatus(200);
        expect($response->getContent())->toContain('OK');
    });

    it('returns OK when no pending commands', function () {
        Device::create([
            'serial_number' => 'NOCOMMANDS',
            'name' => 'No Commands Device',
        ]);

        $response = $this->get('/iclock/getrequest?SN=NOCOMMANDS');

        $response->assertStatus(200);
        expect($response->getContent())->toContain('OK');
    });

    it('returns pending commands', function () {
        $device = Device::create([
            'serial_number' => 'CMDDEVICE',
            'name' => 'Command Device',
        ]);

        DeviceCommand::create([
            'device_id' => $device->id,
            'command_type' => 'INFO',
            'command_content' => 'INFO',
            'status' => 'pending',
        ]);

        $response = $this->get('/iclock/getrequest?SN=CMDDEVICE');

        $response->assertStatus(200);
        $content = $response->getContent();

        expect($content)->toContain('C:');
        expect($content)->toContain('INFO');
    });

    it('marks commands as sent after returning them', function () {
        $device = Device::create([
            'serial_number' => 'SENTDEVICE',
            'name' => 'Sent Device',
        ]);

        $command = DeviceCommand::create([
            'device_id' => $device->id,
            'command_type' => 'REBOOT',
            'command_content' => 'REBOOT',
            'status' => 'pending',
        ]);

        $this->get('/iclock/getrequest?SN=SENTDEVICE');

        $command->refresh();
        expect($command->status)->toBe('sent');
        expect($command->sent_at)->not->toBeNull();
    });

    it('returns multiple pending commands', function () {
        $device = Device::create([
            'serial_number' => 'MULTIDEVICE',
            'name' => 'Multi Device',
        ]);

        DeviceCommand::create([
            'device_id' => $device->id,
            'command_type' => 'INFO',
            'command_content' => 'INFO',
            'status' => 'pending',
        ]);

        DeviceCommand::create([
            'device_id' => $device->id,
            'command_type' => 'CHECK',
            'command_content' => 'CHECK',
            'status' => 'pending',
        ]);

        $response = $this->get('/iclock/getrequest?SN=MULTIDEVICE');

        $response->assertStatus(200);
        $content = $response->getContent();

        expect($content)->toContain('INFO');
        expect($content)->toContain('CHECK');
    });

    it('does not return already sent commands', function () {
        $device = Device::create([
            'serial_number' => 'SENTONLY',
            'name' => 'Sent Only Device',
        ]);

        DeviceCommand::create([
            'device_id' => $device->id,
            'command_type' => 'INFO',
            'command_content' => 'INFO',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $response = $this->get('/iclock/getrequest?SN=SENTONLY');

        $response->assertStatus(200);
        expect($response->getContent())->toContain('OK');
        expect($response->getContent())->not->toContain('C:');
    });

    it('marks device as online', function () {
        $device = Device::create([
            'serial_number' => 'ONLINECHECK',
            'name' => 'Online Check Device',
            'status' => 'offline',
        ]);

        $this->get('/iclock/getrequest?SN=ONLINECHECK');

        $device->refresh();
        expect($device->status)->toBe('online');
    });

    it('limits commands to 10 per request', function () {
        $device = Device::create([
            'serial_number' => 'LIMITDEVICE',
            'name' => 'Limit Device',
        ]);

        // Create 15 pending commands
        for ($i = 1; $i <= 15; $i++) {
            DeviceCommand::create([
                'device_id' => $device->id,
                'command_type' => 'INFO',
                'command_content' => "INFO{$i}",
                'status' => 'pending',
            ]);
        }

        $response = $this->get('/iclock/getrequest?SN=LIMITDEVICE');
        $response->assertStatus(200);

        // Check that only 10 commands were marked as sent
        $sentCount = DeviceCommand::where('device_id', $device->id)
            ->where('status', 'sent')
            ->count();

        expect($sentCount)->toBe(10);

        // 5 should still be pending
        $pendingCount = DeviceCommand::where('device_id', $device->id)
            ->where('status', 'pending')
            ->count();

        expect($pendingCount)->toBe(5);
    });
});
