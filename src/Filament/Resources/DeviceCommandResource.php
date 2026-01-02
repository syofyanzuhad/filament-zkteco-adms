<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\DeviceCommandResource\Pages;
use Syofyanzuhad\FilamentZktecoAdms\FilamentZktecoAdmsPlugin;
use Syofyanzuhad\FilamentZktecoAdms\Models\DeviceCommand;

class DeviceCommandResource extends Resource
{
    protected static ?string $model = DeviceCommand::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-command-line';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return FilamentZktecoAdmsPlugin::get()->getNavigationGroup()
            ?? config('zkteco-adms.filament.navigation_group', 'ZKTeco ADMS');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Command Details')
                ->schema([
                    Select::make('device_id')
                        ->relationship('device', 'name')
                        ->required()
                        ->searchable(),
                    Select::make('command_type')
                        ->options([
                            'INFO' => 'Get Device Info',
                            'REBOOT' => 'Reboot Device',
                            'CLEAR' => 'Clear Data',
                            'DATA' => 'Send Data',
                            'CHECK' => 'Check Connection',
                        ])
                        ->required()
                        ->live(),
                    Textarea::make('command_content')
                        ->required()
                        ->rows(3),
                    Select::make('status')
                        ->options([
                            'pending' => 'Pending',
                            'sent' => 'Sent',
                            'acknowledged' => 'Acknowledged',
                            'failed' => 'Failed',
                        ])
                        ->default('pending')
                        ->disabled(),
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
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('command_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('command_content')
                    ->limit(40),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'sent' => 'info',
                        'acknowledged' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('sent_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('acknowledged_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('retry_count')
                    ->label('Retries'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('device')
                    ->relationship('device', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'sent' => 'Sent',
                        'acknowledged' => 'Acknowledged',
                        'failed' => 'Failed',
                    ]),
                Tables\Filters\SelectFilter::make('command_type')
                    ->options([
                        'INFO' => 'Get Device Info',
                        'REBOOT' => 'Reboot Device',
                        'CLEAR' => 'Clear Data',
                        'DATA' => 'Send Data',
                        'CHECK' => 'Check Connection',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('retry')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (DeviceCommand $record) => in_array($record->status, ['failed', 'sent']))
                    ->action(fn (DeviceCommand $record) => $record->retry()),
                DeleteAction::make(),
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
            'index' => Pages\ListDeviceCommands::route('/'),
            'create' => Pages\CreateDeviceCommand::route('/create'),
            'view' => Pages\ViewDeviceCommand::route('/{record}'),
        ];
    }
}
