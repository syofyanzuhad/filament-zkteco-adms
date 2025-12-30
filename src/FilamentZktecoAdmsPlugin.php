<?php

namespace Syofyanzuhad\FilamentZktecoAdms;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\AttendanceLogResource;
use Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\DeviceCommandResource;
use Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\DeviceResource;
use Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\ZktecoUserResource;

class FilamentZktecoAdmsPlugin implements Plugin
{
    protected bool $hasDeviceResource = true;

    protected bool $hasAttendanceLogResource = true;

    protected bool $hasUserResource = true;

    protected bool $hasCommandResource = true;

    protected ?string $navigationGroup = null;

    public function getId(): string
    {
        return 'filament-zkteco-adms';
    }

    public function register(Panel $panel): void
    {
        $resources = [];

        if ($this->hasDeviceResource) {
            $resources[] = DeviceResource::class;
        }

        if ($this->hasAttendanceLogResource) {
            $resources[] = AttendanceLogResource::class;
        }

        if ($this->hasUserResource) {
            $resources[] = ZktecoUserResource::class;
        }

        if ($this->hasCommandResource) {
            $resources[] = DeviceCommandResource::class;
        }

        $panel->resources($resources);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function deviceResource(bool $condition = true): static
    {
        $this->hasDeviceResource = $condition;

        return $this;
    }

    public function attendanceLogResource(bool $condition = true): static
    {
        $this->hasAttendanceLogResource = $condition;

        return $this;
    }

    public function userResource(bool $condition = true): static
    {
        $this->hasUserResource = $condition;

        return $this;
    }

    public function commandResource(bool $condition = true): static
    {
        $this->hasCommandResource = $condition;

        return $this;
    }


    public function navigationGroup(?string $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function getNavigationGroup(): ?string
    {
        return $this->navigationGroup;
    }
}
