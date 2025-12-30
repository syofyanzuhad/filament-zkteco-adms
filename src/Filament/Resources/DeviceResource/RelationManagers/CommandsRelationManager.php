<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\DeviceResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Syofyanzuhad\FilamentZktecoAdms\Models\DeviceCommand;

class CommandsRelationManager extends RelationManager
{
    protected static string $relationship = 'commands';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('command_type')
                    ->options([
                        'INFO' => 'Get Device Info',
                        'REBOOT' => 'Reboot Device',
                        'CLEAR' => 'Clear Data',
                        'DATA' => 'Send Data',
                        'CHECK' => 'Check Connection',
                    ])
                    ->required(),
                Textarea::make('command_content')
                    ->required()
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('command_type')
            ->columns([
                TextColumn::make('command_type')
                    ->badge(),
                TextColumn::make('command_content')
                    ->limit(30),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'sent' => 'info',
                        'acknowledged' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('sent_at')
                    ->dateTime(),
                TextColumn::make('acknowledged_at')
                    ->dateTime(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'sent' => 'Sent',
                        'acknowledged' => 'Acknowledged',
                        'failed' => 'Failed',
                    ]),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('retry')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn (DeviceCommand $record) => in_array($record->status, ['failed', 'sent']))
                    ->action(fn (DeviceCommand $record) => $record->retry()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
