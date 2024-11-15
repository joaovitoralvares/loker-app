<?php

namespace App\Filament\App\Resources;

use App\Enum\MaritalStatusEnum;
use App\Enum\PersonTypeEnum;
use App\Enum\RoleEnum;
use App\Filament\App\Resources\CustomerResource\Pages;
use App\Filament\App\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $label = 'Cliente';
    protected static ?string $pluralLabel = 'Clientes';

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
                                    ->maxLength(255),
                                Forms\Components\Select::make('user.person_type')
                                    ->label('Tipo de pessoa')
                                    ->options(PersonTypeEnum::toOptions())
                                    ->required(),
                                Forms\Components\TextInput::make('user.cpf_cnpj')->label('CPF/CNPJ')->required(),
                            ]),
                            Forms\Components\Fieldset::make('CNH')->schema([
                                Forms\Components\TextInput::make('cnh_number')
                                    ->label('Número')
                                    ->placeholder('999999999999')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('cnh_category')
                                    ->label('Categoria')
                                    ->required()
                                    ->placeholder('B')
                                    ->maxLength(255),
                                Forms\Components\DatePicker::make('cnh_expiration_date')->label('Data de vencimento')
                                    ->format('Y-m-d')
                                    ->displayFormat('d/m/Y')
                                    ->required(),
                                Forms\Components\TextInput::make('cnh_security_code')
                                    ->label('Código de segurança')
                                    ->placeholder('0000000000000')
                                    ->maxLength(255)
                                    ->required(),
                            ]),
                        ]),

                        Forms\Components\Tabs\Tab::make('Informações Adicionais')->schema([
                            Forms\Components\Fieldset::make('Dados Pessoais')->schema([
                                Forms\Components\TextInput::make('rg')
                                    ->label('RG')
                                    ->placeholder('99999999')
                                    ->maxLength(255),
                                Forms\Components\DatePicker::make('birthday')
                                    ->label('Data de nascimento')
                                    ->required()
                                    ->format('Y-m-d')
                                    ->displayFormat('d/m/Y')
                                    ->maxDate(now()->subYears(18)),
                                Forms\Components\Select::make('marital_status')->options(MaritalStatusEnum::toOptions())
                                    ->label('Estado civil')
                                    ->required(),
                                Forms\Components\TextInput::make('profession')
                                    ->label('Profissão')
                                    ->placeholder('Engenheiro')
                                    ->maxLength(255),
                            ])
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
