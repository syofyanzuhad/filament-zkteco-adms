<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\ZktecoUserResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\ZktecoUserResource;

class ListZktecoUsers extends ListRecords
{
    protected static string $resource = ZktecoUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
