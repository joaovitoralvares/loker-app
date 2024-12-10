<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\CategoryResource\Pages;
use App\Filament\App\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use App\ValueObjects\MoneyValue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

class CategoryResource extends Resource
{
    protected static ?string $model = \App\Models\Vehicle\Category::class;

    protected static ?string $navigationGroup = 'Veículos';

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $label = 'Categoria';

    protected static ?string $pluralLabel = 'Categorias';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->maxLength(255)
                    ->label('Descrição')
                    ->extraInputAttributes(['onChange' => 'this.value = this.value.toUpperCase()'])
                ,
                Forms\Components\TextInput::make('daily_price')
                    ->placeholder('90,50')
                    ->label('Diária')
                    ->formatStateUsing(fn ($state) => !empty($state) ? MoneyValue::from($state)->toBRL() : 0)
                    ->dehydrateStateUsing(fn ($state) =>!empty($state) ? MoneyValue::fromBRL($state)->getRawAmount() : 0)
                    ->mask(RawJs::make(<<<'JS'
                            $money($input, ',')
                        JS
                        ))
                        ->prefix('R$')
                        ->required()
                        ->maxValue(10000)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\TextColumn::make('description'),
                    Tables\Columns\TextColumn::make('daily_price')
                        ->formatStateUsing(fn ($state) => Number::currency($state/100, 'BRL'))
                        ->description('Diária', 'above'),
                ])->from('lg')
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
            'index' => Pages\ManageCategories::route('/'),
        ];
    }
}
