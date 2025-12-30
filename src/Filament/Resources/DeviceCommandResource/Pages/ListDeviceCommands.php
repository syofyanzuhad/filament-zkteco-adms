<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\DeviceCommandResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\DeviceCommandResource;

class ListDeviceCommands extends ListRecords
{
    protected static string $resource = DeviceCommandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
