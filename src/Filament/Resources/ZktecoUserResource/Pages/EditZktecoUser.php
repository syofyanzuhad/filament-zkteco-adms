<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\ZktecoUserResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\ZktecoUserResource;

class EditZktecoUser extends EditRecord
{
    protected static string $resource = ZktecoUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
