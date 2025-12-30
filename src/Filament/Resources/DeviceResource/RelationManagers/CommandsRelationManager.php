<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\DeviceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Syofyanzuhad\FilamentZktecoAdms\Models\DeviceCommand;

class CommandsRelationManager extends RelationManager
{
    protected static string $relationship = 'commands';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('command_type')
                    ->options([
                        'INFO' => 'Get Device Info',
                        'REBOOT' => 'Reboot Device',
                        'CLEAR' => 'Clear Data',
                        'DATA' => 'Send Data',
                        'CHECK' => 'Check Connection',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('command_content')
                    ->required()
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('command_type')
            ->columns([
                Tables\Columns\TextColumn::make('command_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('command_content')
                    ->limit(30),
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
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'sent' => 'Sent',
                        'acknowledged' => 'Acknowledged',
                        'failed' => 'Failed',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('retry')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn (DeviceCommand $record) => in_array($record->status, ['failed', 'sent']))
                    ->action(fn (DeviceCommand $record) => $record->retry()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
