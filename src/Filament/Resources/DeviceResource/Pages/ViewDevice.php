<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\DeviceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\DeviceResource;

class ViewDevice extends ViewRecord
{
    protected static string $resource = DeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
