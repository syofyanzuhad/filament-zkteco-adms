<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\AttendanceLogResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\AttendanceLogResource;

class ListAttendanceLogs extends ListRecords
{
    protected static string $resource = AttendanceLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
