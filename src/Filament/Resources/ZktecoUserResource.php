<?php

namespace Syofyanzuhad\FilamentZktecoAdms\Filament\Resources;

use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Syofyanzuhad\FilamentZktecoAdms\Filament\Resources\ZktecoUserResource\Pages;
use Syofyanzuhad\FilamentZktecoAdms\FilamentZktecoAdmsPlugin;
use Syofyanzuhad\FilamentZktecoAdms\Models\ZktecoUser;

class ZktecoUserResource extends Resource
{
    protected static ?string $model = ZktecoUser::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'ZKTeco User';

    protected static ?string $pluralModelLabel = 'ZKTeco Users';

    public static function getNavigationGroup(): ?string
    {
        return FilamentZktecoAdmsPlugin::get()->getNavigationGroup()
            ?? config('zkteco-adms.filament.navigation_group', 'ZKTeco ADMS');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Section::make('User Information')
                ->schema([
                    Forms\Components\TextInput::make('pin')
                        ->required()
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('name'),
                    Forms\Components\TextInput::make('card_number')
                        ->label('Card Number'),
                    Forms\Components\Select::make('privilege')
                        ->options([
                            0 => 'User',
                            14 => 'Admin',
                        ])
                        ->default(0),
                    Forms\Components\TextInput::make('group'),
                    Forms\Components\Toggle::make('is_enabled')
                        ->default(true),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pin')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('card_number')
                    ->label('Card'),
                Tables\Columns\TextColumn::make('privilege')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 14 ? 'Admin' : 'User')
                    ->color(fn ($state): string => $state === 14 ? 'primary' : 'gray'),
                Tables\Columns\IconColumn::make('is_enabled')
                    ->boolean(),
                Tables\Columns\TextColumn::make('attendance_logs_count')
                    ->counts('attendanceLogs')
                    ->label('Attendance Count'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('privilege')
                    ->options([
                        0 => 'User',
                        14 => 'Admin',
                    ]),
                Tables\Filters\TernaryFilter::make('is_enabled'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListZktecoUsers::route('/'),
            'create' => Pages\CreateZktecoUser::route('/create'),
            'view' => Pages\ViewZktecoUser::route('/{record}'),
            'edit' => Pages\EditZktecoUser::route('/{record}/edit'),
        ];
    }
}
