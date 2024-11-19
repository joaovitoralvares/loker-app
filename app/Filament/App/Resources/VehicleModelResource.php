<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\VehicleModelResource\Pages;
use App\Filament\App\Resources\VehicleModelResource\RelationManagers;
use App\Models\VehicleModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehicleModelResource extends Resource
{
    protected static ?string $model = \App\Models\Vehicle\VehicleModel::class;

    protected static ?string $navigationGroup = 'Veículos';

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $label = 'Modelo';

    protected static ?string $pluralLabel = 'Modelos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('brand_id')
                    ->label('Marca')
                    ->relationship('brand', 'name')
                    ->searchable(['name'])
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nome'),
                    ])
                    ->createOptionModalHeading('Cadastrar Nova Marca')
                    ->required(),
                Forms\Components\TextInput::make('description')
                    ->label('Descrição')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\TextColumn::make('brand.name')
                        ->label('Marca')
                        ->searchable()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('description')
                        ->label('Descrição')
                        ->searchable()
                        ->sortable(),
                ])->from('lg'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ManageVehicleModels::route('/'),
        ];
    }
}
