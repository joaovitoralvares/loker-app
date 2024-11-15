<?php

namespace App\Filament\App\Resources;

use App\Enum\MaritalStatusEnum;
use App\Enum\PersonTypeEnum;
use App\Filament\App\Resources\OwnerResource\Pages;
use App\Filament\App\Resources\OwnerResource\RelationManagers;
use App\Models\Owner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OwnerResource extends Resource
{
    protected static ?string $model = Owner::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $label = 'Proprietário';

    protected static ?string $pluralLabel = 'Proprietários';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()->schema([
                    Forms\Components\Tabs::make()->tabs([
                        Forms\Components\Tabs\Tab::make('Identificação')->schema([
                            Forms\Components\Fieldset::make('Informações Básicas')->schema([
                                Forms\Components\TextInput::make('user.name')
                                    ->label('Nome')
                                    ->placeholder('José da Silva')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('user.email')
                                    ->placeholder('jose@silva.com.br')
                                    ->required()
                                    ->email()
                                    ->maxLength(255)
                                    ->unique('users', 'email'),
                                Forms\Components\Select::make('user.person_type')
                                    ->label('Tipo de pessoa')
                                    ->options(PersonTypeEnum::toOptions())
                                    ->required(),
                                Forms\Components\TextInput::make('user.cpf_cnpj')->label('CPF/CNPJ')->required(),
                            ])->visibleOn('create'),
                            Forms\Components\Fieldset::make('Informações Básicas')->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nome')
                                    ->placeholder('José da Silva')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->placeholder('jose@silva.com.br')
                                    ->required()
                                    ->email()
                                    ->maxLength(255)
                                    ->unique('users'),
                                Forms\Components\Select::make('person_type')
                                    ->label('Tipo de pessoa')
                                    ->options(PersonTypeEnum::toOptions())
                                    ->required(),
                                Forms\Components\TextInput::make('cpf_cnpj')
                                    ->label('CPF/CNPJ')->readOnly(),
                            ])->visibleOn('edit')
                            ->relationship('user'),

                            Forms\Components\Fieldset::make('Dados adicionais')->schema([
                                Forms\Components\TextInput::make('rg')
                                    ->label('RG')
                                    ->placeholder('99999999')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('ie')
                                    ->label('Inscrição Estadual')
                                    ->placeholder('99999999')
                                    ->maxLength(255),
                            ]),
                        ]),
                    ])
                ])->columns(1),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Split::make([
                    TextColumn::make('user.name'),
                    TextColumn::make('user.email'),
                    TextColumn::make('user.person_type')
                        ->formatStateUsing(fn ($state) => PersonTypeEnum::from($state)->label()),
                    TextColumn::make('user.cpf_cnpj'),
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
            'index' => Pages\ListOwners::route('/'),
            'create' => Pages\CreateOwner::route('/create'),
            'edit' => Pages\EditOwner::route('/{record}/edit'),
        ];
    }
}
