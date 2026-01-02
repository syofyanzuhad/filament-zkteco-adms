<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\AttendanceLogResource\Pages;
use Syofyanzuhad\FilamentZktecoAdms\FilamentZktecoAdmsPlugin;
use Syofyanzuhad\FilamentZktecoAdms\Models\AttendanceLog;

class AttendanceLogResource extends Resource
{
    protected static ?string $model = AttendanceLog::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return FilamentZktecoAdmsPlugin::get()->getNavigationGroup()
            ?? config('zkteco-adms.filament.navigation_group', 'ZKTeco ADMS');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Attendance Details')
                ->schema([
                    Select::make('device_id')
                        ->relationship('device', 'name')
                        ->required(),
                    TextInput::make('pin')
                        ->required(),
                    DateTimePicker::make('punched_at')
                        ->required(),
                    Select::make('status')
                        ->options([
                            0 => 'Check In',
                            1 => 'Check Out',
                            2 => 'Break Out',
                            3 => 'Break In',
                            4 => 'OT In',
                            5 => 'OT Out',
                        ]),
                    Select::make('verify_type')
                        ->options([
                            0 => 'Password',
                            1 => 'Fingerprint',
                            2 => 'Card',
                            15 => 'Face',
                        ]),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('device.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pin')
                    ->label('User PIN')
                    ->searchable(),
                Tables\Columns\TextColumn::make('zktecoUser.name')
                    ->label('User Name')
                    ->placeholder('Unknown'),
                Tables\Columns\TextColumn::make('punched_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        0 => 'Check In',
                        1 => 'Check Out',
                        2 => 'Break Out',
                        3 => 'Break In',
                        4 => 'OT In',
                        5 => 'OT Out',
                        default => 'Unknown',
                    })
                    ->color(fn ($state): string => match ($state) {
                        0 => 'success',
                        1 => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('verify_type_label')
                    ->label('Verify Type'),
            ])
            ->defaultSort('punched_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('device')
                    ->relationship('device', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        0 => 'Check In',
                        1 => 'Check Out',
                        2 => 'Break Out',
                        3 => 'Break In',
                        4 => 'OT In',
                        5 => 'OT Out',
                    ]),
                Tables\Filters\Filter::make('punched_at')
                    ->schema([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('punched_at', '>=', $date))
                            ->when($data['until'], fn ($q, $date) => $q->whereDate('punched_at', '<=', $date));
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendanceLogs::route('/'),
            'view' => Pages\ViewAttendanceLog::route('/{record}'),
        ];
    }
}
