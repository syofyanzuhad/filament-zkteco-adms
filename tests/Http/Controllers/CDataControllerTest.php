<?php

use Illuminate\Support\Facades\Event;
use Syofyanzuhad\FilamentZktecoAdms\Events\AttendanceReceived;
use Syofyanzuhad\FilamentZktecoAdms\Events\DeviceConnected;
use Syofyanzuhad\FilamentZktecoAdms\Events\UserSynced;
use Syofyanzuhad\FilamentZktecoAdms\Models\AttendanceLog;
use Syofyanzuhad\FilamentZktecoAdms\Models\Device;
use Syofyanzuhad\FilamentZktecoAdms\Models\ZktecoUser;

beforeEach(function () {
    Event::fake();
});

describe('CDataController', function () {
    it('returns error when no serial number provided', function () {
        $response = $this->get('/iclock/cdata');

        $response->assertStatus(200);
        expect($response->getContent())->toContain('ERR');
    });

    it('auto-registers device when enabled', function () {
        config(['zkteco-adms.device.auto_register' => true]);

        $response = $this->get('/iclock/cdata?SN=TEST123');

        $response->assertStatus(200);
        expect($response->getContent())->toContain('OK');

        $this->assertDatabaseHas('zkteco_devices', [
            'serial_number' => 'TEST123',
            'status' => 'online',
        ]);
    });

    it('does not auto-register device when disabled', function () {
        config(['zkteco-adms.device.auto_register' => false]);

        $response = $this->get('/iclock/cdata?SN=NEWDEVICE');

        $response->assertStatus(200);

        $this->assertDatabaseMissing('zkteco_devices', [
            'serial_number' => 'NEWDEVICE',
        ]);
    });

    it('returns device options on registration request', function () {
        config(['zkteco-adms.device.auto_register' => true]);

        $response = $this->get('/iclock/cdata?SN=TEST123&options=all&pushver=2.4.1');

        $response->assertStatus(200);
        $content = $response->getContent();

        expect($content)->toContain('GET OPTION FROM:');
        expect($content)->toContain('Stamp=');
        expect($content)->toContain('OpStamp=');

        Event::assertDispatched(DeviceConnected::class);
    });

    it('updates device info on registration', function () {
        Device::create([
            'serial_number' => 'EXISTING123',
            'name' => 'Test Device',
        ]);

        $response = $this->get('/iclock/cdata?SN=EXISTING123&options=all&pushver=2.4.1&FWVersion=Ver 6.60&DeviceType=ZK-FP');

        $response->assertStatus(200);

        $this->assertDatabaseHas('zkteco_devices', [
            'serial_number' => 'EXISTING123',
            'push_version' => '2.4.1',
            'firmware_version' => 'Ver 6.60',
            'device_type' => 'ZK-FP',
        ]);
    });

    it('processes attendance logs', function () {
        $device = Device::create([
            'serial_number' => 'ATTDEVICE',
            'name' => 'Attendance Device',
        ]);

        $attlogData = "1\t2024-01-15 08:30:00\t0\t1\t\t0\t0\n2\t2024-01-15 08:35:00\t0\t15\t\t0\t0";

        $response = $this->post('/iclock/cdata?SN=ATTDEVICE&table=ATTLOG&Stamp=100', [], [
            'Content-Type' => 'text/plain',
        ]);

        $response->assertStatus(200);
        expect($response->getContent())->toContain('OK');
    });

    it('creates attendance logs from POST data', function () {
        $device = Device::create([
            'serial_number' => 'ATTDEVICE2',
            'name' => 'Attendance Device 2',
        ]);

        $attlogData = "1\t2024-01-15 08:30:00\t0\t1\t\t0\t0";

        $response = $this->call(
            'POST',
            '/iclock/cdata?SN=ATTDEVICE2&table=ATTLOG&Stamp=100',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'text/plain'],
            $attlogData
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('zkteco_attendance_logs', [
            'device_id' => $device->id,
            'pin' => '1',
        ]);

        Event::assertDispatched(AttendanceReceived::class);
    });

    it('processes operation logs with user data', function () {
        $device = Device::create([
            'serial_number' => 'OPDEVICE',
            'name' => 'Op Device',
        ]);

        $operlogData = "USER PIN=1\tName=John Doe\tPri=0\tPasswd=\tCard=12345678\tGrp=1\tTZ=0000000100000000";

        $response = $this->call(
            'POST',
            '/iclock/cdata?SN=OPDEVICE&table=OPERLOG&Stamp=200',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'text/plain'],
            $operlogData
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('zkteco_users', [
            'pin' => '1',
            'name' => 'John Doe',
            'card_number' => '12345678',
        ]);

        Event::assertDispatched(UserSynced::class);
    });

    it('updates att_stamp after processing attendance logs', function () {
        $device = Device::create([
            'serial_number' => 'STAMPDEVICE',
            'name' => 'Stamp Device',
            'att_stamp' => 50,
        ]);

        $attlogData = "1\t2024-01-15 08:30:00\t0\t1\t\t0\t0";

        $this->call(
            'POST',
            '/iclock/cdata?SN=STAMPDEVICE&table=ATTLOG&Stamp=100',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'text/plain'],
            $attlogData
        );

        $device->refresh();
        expect($device->att_stamp)->toBe(100);
    });

    it('marks device as online on any request', function () {
        $device = Device::create([
            'serial_number' => 'OFFLINEDEV',
            'name' => 'Offline Device',
            'status' => 'offline',
        ]);

        $this->get('/iclock/cdata?SN=OFFLINEDEV');

        $device->refresh();
        expect($device->status)->toBe('online');
        expect($device->last_activity_at)->not->toBeNull();
    });
});
