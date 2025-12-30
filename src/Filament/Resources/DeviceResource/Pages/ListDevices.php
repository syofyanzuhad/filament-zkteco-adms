<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\DeviceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\DeviceResource;

class ListDevices extends ListRecords
{
    protected static string $resource = DeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
