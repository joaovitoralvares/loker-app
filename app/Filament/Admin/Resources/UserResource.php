<?php

namespace App\Filament\Admin\Resources;

use App\Enum\RoleEnum;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Company;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Fieldset::make('Dados pessoais')->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('José da Silva')
                            ->label('Nome'),
                        Forms\Components\TextInput::make('email')
                            ->required()
                            ->email()
                            ->maxLength(255)
                            ->placeholder('user@email.com')
                            ->label('Email'),
                        Forms\Components\Checkbox::make('is_admin')
                            ->default(false)
                            ->label('Administrador?'),
                    ]),

                    Forms\Components\Repeater::make('userCompanies')->schema([
                        Forms\Components\Select::make('company_id')
                            ->searchable()
                            ->options(Company::query()->limit(10)->pluck('name','id')->toArray())
                            ->getSearchResultsUsing(fn(string $search): array => Company::where('name', 'like', "%{$search}%")->limit(10)->pluck('name', 'id')->toArray())
                            ->label('Empresa')
                            ->requiredIf('is_admin', false),
                        Forms\Components\Select::make('role')
                            ->requiredIf('is_admin', false)
                            ->label('Função')
                            ->options(RoleEnum::toOptions()),
                    ])->relationship('userCompanies')->label('Empresas'),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('name')
                            ->sortable()
                            ->searchable(),
                        Tables\Columns\TextColumn::make('email')->label('Email')
                            ->searchable(),
                    ]),
                    Tables\Columns\TextColumn::make('is_admin')
                        ->formatStateUsing(fn ($state) => $state ? 'Administrador' : '')
                        ->hidden(fn ($state) => !$state),
                ])->from('lg'),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_admin')
                    ->label('Administrador?')
                    ->query(fn (Builder $query): Builder => $query->where('is_admin', true))
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\UserResource\Pages\ListUsers::route('/'),
            'create' => \App\Filament\Admin\Resources\UserResource\Pages\CreateUser::route('/create'),
            'edit' => \App\Filament\Admin\Resources\UserResource\Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
