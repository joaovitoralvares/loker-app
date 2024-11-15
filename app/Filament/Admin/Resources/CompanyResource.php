<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;
    protected static ?string $label = 'Empresa';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Dados da empresa')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->autocapitalize()
                            ->placeholder('Nome da empresa')
                            ->label('Nome'),
                        Forms\Components\TextInput::make('cnpj')
                            ->required()
                            ->placeholder('Cnpj da empresa')
                            ->label('CNPJ')
                            ->mask('99.999.999/9999-99')
                            ->placeholder('00.000.000/0000-00')
                            ->rule('cnpj')
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\TextColumn::make('name')->label('Nome')
                        ->sortable()
                        ->searchable(),
                    Tables\Columns\TextColumn::make('cnpj')->label('CNPJ')
                        ->searchable()
                ])->from('lg')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\CompanyResource\Pages\ListCompanies::route('/'),
            'create' => \App\Filament\Admin\Resources\CompanyResource\Pages\CreateCompany::route('/create'),
            'edit' => \App\Filament\Admin\Resources\CompanyResource\Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
