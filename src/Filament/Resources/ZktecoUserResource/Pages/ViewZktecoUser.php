<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\ZktecoUserResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\ZktecoUserResource;

class ViewZktecoUser extends ViewRecord
{
    protected static string $resource = ZktecoUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
