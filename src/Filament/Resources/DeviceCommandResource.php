<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\DeviceCommandResource\Pages;
use Syofyanzuhad\FilamentZktecoAdms\FilamentZktecoAdmsPlugin;
use Syofyanzuhad\FilamentZktecoAdms\Models\DeviceCommand;

class DeviceCommandResource extends Resource
{
    protected static ?string $model = DeviceCommand::class;

    protected static ?string $navigationIcon = 'heroicon-o-command-line';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return FilamentZktecoAdmsPlugin::get()->getNavigationGroup()
            ?? config('zkteco-adms.filament.navigation_group', 'ZKTeco ADMS');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Command Details')
                ->schema([
                    Forms\Components\Select::make('device_id')
                        ->relationship('device', 'name')
                        ->required()
                        ->searchable(),
                    Forms\Components\Select::make('command_type')
                        ->options([
                            'INFO' => 'Get Device Info',
                            'REBOOT' => 'Reboot Device',
                            'CLEAR' => 'Clear Data',
                            'DATA' => 'Send Data',
                            'CHECK' => 'Check Connection',
                        ])
                        ->required()
                        ->live(),
                    Forms\Components\Textarea::make('command_content')
                        ->required()
                        ->rows(3),
                    Forms\Components\Select::make('status')
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
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('retry')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (DeviceCommand $record) => in_array($record->status, ['failed', 'sent']))
                    ->action(fn (DeviceCommand $record) => $record->retry()),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
