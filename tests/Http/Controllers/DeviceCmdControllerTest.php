<?php

use Syofyanzuhad\FilamentZktecoAdms\Models\Device;
use Syofyanzuhad\FilamentZktecoAdms\Models\DeviceCommand;

describe('DeviceCmdController', function () {
    it('returns error when no serial number provided', function () {
        $response = $this->get('/iclock/devicecmd');

        $response->assertStatus(200);
        expect($response->getContent())->toContain('ERR');
    });

    it('returns OK when device not found', function () {
        $response = $this->get('/iclock/devicecmd?SN=NONEXISTENT');

        $response->assertStatus(200);
        expect($response->getContent())->toContain('OK');
    });

    it('acknowledges command with success return', function () {
        $device = Device::create([
            'serial_number' => 'ACKDEVICE',
            'name' => 'Ack Device',
        ]);

        $command = DeviceCommand::create([
            'device_id' => $device->id,
            'command_type' => 'REBOOT',
            'command_content' => 'REBOOT',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $response = $this->get("/iclock/devicecmd?SN=ACKDEVICE&ID={$command->id}&Return=0");

        $response->assertStatus(200);
        expect($response->getContent())->toContain('OK');

        $command->refresh();
        expect($command->status)->toBe('acknowledged');
        expect($command->acknowledged_at)->not->toBeNull();
        expect($command->response)->toBe('0');
    });

    it('acknowledges command with null return as success', function () {
        $device = Device::create([
            'serial_number' => 'NULLRETURN',
            'name' => 'Null Return Device',
        ]);

        $command = DeviceCommand::create([
            'device_id' => $device->id,
            'command_type' => 'INFO',
            'command_content' => 'INFO',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $response = $this->get("/iclock/devicecmd?SN=NULLRETURN&ID={$command->id}");

        $response->assertStatus(200);

        $command->refresh();
        expect($command->status)->toBe('acknowledged');
    });

    it('marks command as failed with non-zero return', function () {
        $device = Device::create([
            'serial_number' => 'FAILDEVICE',
            'name' => 'Fail Device',
        ]);

        $command = DeviceCommand::create([
            'device_id' => $device->id,
            'command_type' => 'REBOOT',
            'command_content' => 'REBOOT',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $response = $this->get("/iclock/devicecmd?SN=FAILDEVICE&ID={$command->id}&Return=-1");

        $response->assertStatus(200);

        $command->refresh();
        expect($command->status)->toBe('failed');
        expect($command->response)->toBe('-1');
    });

    it('marks device as online', function () {
        $device = Device::create([
            'serial_number' => 'CMDOLINE',
            'name' => 'Cmd Online Device',
            'status' => 'offline',
        ]);

        $this->get('/iclock/devicecmd?SN=CMDOLINE');

        $device->refresh();
        expect($device->status)->toBe('online');
    });

    it('handles command acknowledgment via POST', function () {
        $device = Device::create([
            'serial_number' => 'POSTDEVICE',
            'name' => 'Post Device',
        ]);

        $command = DeviceCommand::create([
            'device_id' => $device->id,
            'command_type' => 'CHECK',
            'command_content' => 'CHECK',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $response = $this->post("/iclock/devicecmd?SN=POSTDEVICE&ID={$command->id}&Return=0");

        $response->assertStatus(200);

        $command->refresh();
        expect($command->status)->toBe('acknowledged');
    });

    it('ignores invalid command ID', function () {
        Device::create([
            'serial_number' => 'INVALIDCMD',
            'name' => 'Invalid Cmd Device',
        ]);

        $response = $this->get('/iclock/devicecmd?SN=INVALIDCMD&ID=99999&Return=0');

        $response->assertStatus(200);
        expect($response->getContent())->toContain('OK');
    });

    it('stores response value in command', function () {
        $device = Device::create([
            'serial_number' => 'RESPDEVICE',
            'name' => 'Response Device',
        ]);

        $command = DeviceCommand::create([
            'device_id' => $device->id,
            'command_type' => 'DATA',
            'command_content' => 'DATA USER PIN=1',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $this->get("/iclock/devicecmd?SN=RESPDEVICE&ID={$command->id}&Return=0");

        $command->refresh();
        expect($command->response)->toBe('0');
    });
});
