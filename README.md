# Filament ZKTeco ADMS

[![Latest Version on Packagist](https://img.shields.io/packagist/v/syofyanzuhad/filament-zkteco-adms.svg?style=flat-square)](https://packagist.org/packages/syofyanzuhad/filament-zkteco-adms)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/syofyanzuhad/filament-zkteco-adms/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/syofyanzuhad/filament-zkteco-adms/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/syofyanzuhad/filament-zkteco-adms/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/syofyanzuhad/filament-zkteco-adms/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/syofyanzuhad/filament-zkteco-adms.svg?style=flat-square)](https://packagist.org/packages/syofyanzuhad/filament-zkteco-adms)

A Filament plugin to receive attendance data from ZKTeco devices via ADMS (Automatic Data Master Server) protocol. This package provides ADMS endpoints, database storage, and a complete Filament admin interface for managing devices and attendance logs.

## Version Compatibility

| Package Version | Filament | Laravel | PHP |
|-----------------|----------|---------|-----|
| `^2.0` | `^4.0` | `^11.0 \| ^12.0` | `^8.2` |
| `^1.0` | `^3.0` | `^10.0 \| ^11.0` | `^8.2` |

## Features

- ADMS protocol endpoints (`/iclock/cdata`, `/iclock/getrequest`, `/iclock/devicecmd`)
- Automatic device registration
- Attendance log storage with user sync
- Device command queue (reboot, clear logs, sync users)
- Full Filament admin panel integration
- Configurable table prefixes and route settings
- Event dispatching for real-time integrations

## Installation

**For Filament 4.x:**

```bash
composer require syofyanzuhad/filament-zkteco-adms:^2.0
```

**For Filament 3.x:**

```bash
composer require syofyanzuhad/filament-zkteco-adms:^1.0
```

Publish and run the migrations:

```bash
php artisan vendor:publish --tag="filament-zkteco-adms-migrations"
php artisan migrate
```

Optionally, publish the config file:

```bash
php artisan vendor:publish --tag="filament-zkteco-adms-config"
```

## Usage

### 1. Register the Plugin

Add the plugin to your Filament panel provider:

```php
// app/Providers/Filament/AdminPanelProvider.php

use Syofyanzuhad\FilamentZktecoAdms\FilamentZktecoAdmsPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(FilamentZktecoAdmsPlugin::make());
}
```

You can selectively disable resources:

```php
->plugin(
    FilamentZktecoAdmsPlugin::make()
        ->deviceResource()
        ->attendanceLogResource()
        ->userResource(false)      // Disable user resource
        ->commandResource(false)   // Disable command resource
)
```

### 2. Configure Your ZKTeco Device

On your ZKTeco device, configure the ADMS settings:

1. Go to **COMM** > **Cloud Server Setting** (or **ADMS**)
2. Set **Server Address**: `your-domain.com` or IP address
3. Set **Server Port**: `80` (or your app's port)
4. Enable **Domain Name** if using a domain
5. The device will connect to `/iclock/cdata`

### 3. Available Endpoints

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `/iclock/cdata` | GET, POST | Device registration & data submission |
| `/iclock/getrequest` | GET | Device fetches pending commands |
| `/iclock/devicecmd` | GET, POST | Command acknowledgment |
| `/iclock/test` | GET, POST | Connection test |

### 4. Filament Admin Panel

The plugin adds four resources to your Filament panel:

- **Devices** - View and manage connected ZKTeco devices
- **Attendance Logs** - View attendance records with filters
- **ZKTeco Users** - View synced users from devices
- **Device Commands** - View and manage command queue

### 5. Sending Commands to Devices

You can send commands to devices programmatically:

```php
use Syofyanzuhad\FilamentZktecoAdms\Models\Device;
use Syofyanzuhad\FilamentZktecoAdms\Services\DeviceCommandBuilder;

$device = Device::where('serial_number', 'ABC123')->first();
$commandBuilder = app(DeviceCommandBuilder::class);

// Reboot device
$commandBuilder->reboot($device);

// Clear attendance logs on device
$commandBuilder->clearAttendanceLogs($device);

// Get device info
$commandBuilder->info($device);

// Add user to device
$commandBuilder->addUser($device, [
    'pin' => '1',
    'name' => 'John Doe',
    'card' => '12345678',
    'privilege' => 0,  // 0 = user, 14 = admin
]);

// Delete user from device
$commandBuilder->deleteUser($device, '1');

// Sync time
$commandBuilder->syncTime($device);
```

### 6. Listening to Events

The package dispatches events that you can listen to:

```php
// app/Providers/EventServiceProvider.php

protected $listen = [
    \Syofyanzuhad\FilamentZktecoAdms\Events\AttendanceReceived::class => [
        \App\Listeners\ProcessAttendance::class,
    ],
    \Syofyanzuhad\FilamentZktecoAdms\Events\DeviceConnected::class => [
        \App\Listeners\OnDeviceConnected::class,
    ],
    \Syofyanzuhad\FilamentZktecoAdms\Events\UserSynced::class => [
        \App\Listeners\OnUserSynced::class,
    ],
];
```

Example listener:

```php
// app/Listeners/ProcessAttendance.php

namespace App\Listeners;

use Syofyanzuhad\FilamentZktecoAdms\Events\AttendanceReceived;

class ProcessAttendance
{
    public function handle(AttendanceReceived $event): void
    {
        $log = $event->attendanceLog;
        $device = $event->device;

        // Process attendance...
        // e.g., sync to HR system, send notification, etc.
    }
}
```

## Configuration

```php
// config/zkteco-adms.php

return [
    // Database table prefix
    'table_prefix' => env('ZKTECO_TABLE_PREFIX', 'zkteco_'),

    // Route configuration
    'routes' => [
        'prefix' => env('ZKTECO_ROUTE_PREFIX', 'iclock'),
        'middleware' => ['api'],
        'domain' => env('ZKTECO_ROUTE_DOMAIN', null),
    ],

    // Device settings
    'device' => [
        'auto_register' => env('ZKTECO_AUTO_REGISTER_DEVICES', true),
        'offline_threshold_minutes' => env('ZKTECO_OFFLINE_THRESHOLD', 10),
    ],

    // Event dispatching
    'events' => [
        'dispatch_attendance_received' => true,
        'dispatch_device_connected' => true,
        'dispatch_user_synced' => true,
    ],

    // Filament navigation
    'filament' => [
        'navigation_group' => 'ZKTeco ADMS',
        'navigation_icon' => 'heroicon-o-finger-print',
        'navigation_sort' => 50,
    ],
];
```

## Customizing Models

You can extend the default models by updating the config:

```php
// config/zkteco-adms.php

'models' => [
    'device' => \App\Models\CustomDevice::class,
    'attendance_log' => \App\Models\CustomAttendanceLog::class,
    'user' => \App\Models\CustomZktecoUser::class,
    'device_command' => \App\Models\CustomDeviceCommand::class,
],
```

## Testing

```bash
composer test
```

## Screenshots
![WhatsApp Image 2026-01-03 at 18 04 01 (1)](https://github.com/user-attachments/assets/487ebac6-46a9-4200-8a3c-12e4b2fd7952)
![WhatsApp Image 2026-01-03 at 18 04 01 (4)](https://github.com/user-attachments/assets/4e7e142d-e833-47d5-a01b-d98d3e59ead7)
![WhatsApp Image 2026-01-03 at 18 04 01](https://github.com/user-attachments/assets/f605fef0-b9d6-4f77-a4cf-8e904c28d943)

<img width="2880" height="2162" alt="zkteco syofyanzuhad dev_admin_devices_1 (1)" src="https://github.com/user-attachments/assets/49da0603-f72e-4545-8472-b0692eed8592" />
<img width="2880" height="2080" alt="zkteco syofyanzuhad dev_admin_devices_1" src="https://github.com/user-attachments/assets/fe06a306-16b6-41e6-9be2-482d311af754" />
<img width="2880" height="1902" alt="zkteco syofyanzuhad dev_admin_attendance-logs" src="https://github.com/user-attachments/assets/a8822712-b47d-4d5b-83d0-7188fabd87ce" />
<img width="2880" height="1902" alt="zkteco syofyanzuhad dev_admin_zkteco-users" src="https://github.com/user-attachments/assets/55a03e81-d507-411e-be3c-ec8e2856c26e" />
<img width="2880" height="1902" alt="zkteco syofyanzuhad dev_admin_device-commands" src="https://github.com/user-attachments/assets/dfbf6baf-f0b2-46fa-a112-91a447fee483" />

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Syofyan Zuhad](https://github.com/syofyanzuhad)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
