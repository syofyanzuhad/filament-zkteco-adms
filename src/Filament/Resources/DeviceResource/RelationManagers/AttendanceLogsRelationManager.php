<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\DeviceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AttendanceLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'attendanceLogs';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('pin')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('pin')
            ->columns([
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
                        default => 'Unknown',
                    })
                    ->color(fn ($state): string => match ($state) {
                        0 => 'success',
                        1 => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('verify_type_label')
                    ->label('Verify Type'),
            ])
            ->defaultSort('punched_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
