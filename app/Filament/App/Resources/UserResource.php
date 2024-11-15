<?php

namespace App\Filament\App\Resources;

use App\Enum\RoleEnum;
use App\Filament\App\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $label = 'Usuário';

    protected static ?string $pluralLabel = 'Usuários';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()->schema([
                    Forms\Components\Tabs::make()->tabs([
                        Forms\Components\Tabs\Tab::make('Identificação')->schema([
                            Forms\Components\Fieldset::make('Informações Básicas')->schema([
                                Forms\Components\TextInput::make('name'),
                                Forms\Components\TextInput::make('email'),
                                Forms\Components\Select::make('person_type')->label('Tipo de pessoa')->options([
                                    'individual' => 'Pessoa física',
                                    'corporate' => 'Pessoa jurídica',
                                ]),
                                Forms\Components\TextInput::make('cnpj_cpf')->label('CPF/CNPJ')->required(),
                                Forms\Components\Select::make('role')->options(RoleEnum::toOptions())->label('Tipo de usuário')
                            ]),

                        ]),
                        Forms\Components\Tabs\Tab::make('Informações Adicionais')->schema([
                            Forms\Components\Fieldset::make('Dados Pessoais')->schema([
                                Forms\Components\TextInput::make('rg')->label('RG')->required(),
                                Forms\Components\DatePicker::make('birth_date')->label('Data de nascimento')->required(),
                                Forms\Components\TextInput::make('phone')->label('Celular (Whatsapp)')->required(),
                                Forms\Components\Select::make('marital_status')->options([
                                    'single' => 'Solteiro(a)',
                                    'married' => 'Casado(a)',
                                    'widow' => 'Viúvo(a)',
                                    'divorced' => 'Divorciado(a)',
                                ])->label('Estado civil')->required(),
                                Forms\Components\TextInput::make('profession')->label('Profissão'),
                            ])
                        ]),
                        Forms\Components\Tabs\Tab::make('Endereço')->schema([
                            Forms\Components\Fieldset::make('Endereço')->schema([
                                Forms\Components\TextInput::make('postal_code')->label('CEP')->required(),
                                Forms\Components\TextInput::make('state')->label('Estado')->required(),
                                Forms\Components\TextInput::make('neighborhood')->label('Bairro')->required(),
                                Forms\Components\TextInput::make('city')->label('Cidade')->required(),
                                Forms\Components\TextInput::make('street')->label('Rua')->required(),
                                Forms\Components\TextInput::make('number')->label('Número')->required(),
                                Forms\Components\TextInput::make('complement')->label('Complemento')->required(),

                            ])
                        ])
                    ])
                ])->columns(1),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
