<?php

namespace App\Filament\App\Resources;

use App\Casts\MoneyCast;
use App\Filament\App\Resources\ContractResource\Pages;
use App\Filament\App\Resources\ContractResource\RelationManagers;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Owner;
use App\Models\Vehicle\Category;
use App\Models\Vehicle\Vehicle;
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
use Illuminate\Support\Str;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make()->schema([
                    Forms\Components\Select::make('vehicle_id')
                        ->disabledOn('edit')
                        ->label('Veículo')
                        ->options(function (Forms\Get $get) {
                            if ($get('vehicle_id')) {
                                /** @var Vehicle $vehicle */
                                $vehicle = VehicleResource::getEloquentQuery()->find($get('vehicle_id'));
                                return [$vehicle->id => $vehicle->description()];
                            }
                            return VehicleResource::getEloquentQuery()
                                ->with(['model', 'category'])
                                ->available()
                                ->limit(10)
                                ->get()
                                ->mapWithKeys(fn($vehicle) => [$vehicle->id => $vehicle->description()])
                                ->toArray();
                        })
                        ->searchable()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Forms\Set $set, ?int $state) {
                            $vehicle = Vehicle::with('category', 'owner.user')->find($state);
                            $set('daily_price', Number::currency($vehicle?->category->daily_price / 100 ?? 0, 'BRL', 'pt_BR'));
                            $set('owner_id', $vehicle?->owner_id);
                        })
                        ->getSearchResultsUsing(function (string $search) {
                            return VehicleResource::getEloquentQuery()
                                ->with(['model', 'category'])
                                ->available()
                                ->where(function (Builder $query) use ($search) {
                                    $query->whereHas('model', function (Builder $query) use ($search) {
                                        $query->where('description', 'like', "%{$search}%");
                                    })->orWhereHas('category', function (Builder $query) use ($search) {
                                        $query->where('description', 'like', "%{$search}%");
                                    })->orWhere('plate', 'like', "%{$search}%");
                                })
                                ->limit(10)
                                ->get()
                                ->mapWithKeys(fn($vehicle) => [$vehicle->id => $vehicle->description()])
                                ->toArray();
                        })
                        ->required(),
                    Forms\Components\Select::make('owner_id')
                        ->disabledOn('edit')
                        ->key('owner')
                        ->label('Proprietário')
                        ->options(fn(Forms\Get $get) => Owner::with('user')->where('id', $get('owner_id'))->get()->pluck('user.name', 'id')->toArray())
                        ->required()
                        ->extraInputAttributes(['wire:key' => Str::random(10)]),
                    Forms\Components\TextInput::make('daily_price')
                        ->formatStateUsing(function ($state, Forms\Get $get) {
                            $dailyPrice = Category::query()
                                ->whereHas('vehicles', function (Builder $query) use ($get) {
                                    $query->where('id', $get('vehicle_id'));
                                })
                                ->first()?->daily_price ?? 0;
                            return Number::currency($dailyPrice / 100 ?? 0, 'BRL');
                        }
                        )
                        ->label('Diária')
                        ->required()
                        ->default(0)
                        ->disabled(),
                    Forms\Components\Select::make('customer_id')
                        ->label('Cliente')
                        ->disabledOn('edit')
                        ->preload()
                        ->options(
                            function () {
                                return CustomerResource::getEloquentQuery()
                                    ->limit(10)
                                    ->join('users', 'users.id', '=', 'customers.user_id')
                                    ->pluck('users.name', 'customers.id')
                                    ->toArray();
                            }
                        )
                        ->searchable()
                        ->getSearchResultsUsing(function ($search) {
                            return CustomerResource::getEloquentQuery()
                                ->limit(10)
                                ->join('users', 'users.id', '=', 'customers.user_id')
                                ->where('users.name', 'like', "%{$search}%")
                                ->pluck('users.name', 'customers.id')
                                ->toArray();
                        })
                        ->required(),
                    Forms\Components\TextInput::make('security_deposit_amount')
                        ->disabledOn('edit')
                        ->label('Caução')
                        ->placeholder('4.000')
                        ->mask(RawJs::make(<<<'JS'
                            $money($input, ',')
                        JS
                        ))
                        ->formatStateUsing(function (Forms\Get $get) {
                            $customerId = $get('customer_id');
                            $customer = Customer::with('securityDepositAccount.deposits')->find($customerId);
                            $amount = $customer?->securityDepositAccount?->deposits?->first()?->amount;
                            return MoneyValue::from($amount ?? 0)->toBRL();
                        })
                        ->dehydrateStateUsing(fn($state) => MoneyValue::fromBRL($state)->getRawAmount())
                        ->prefix('R$')
                        ->required()
                        ->minValue(1)
                        ->maxValue(1000000),
                    Forms\Components\DatePicker::make('start_date')
                        ->disabledOn('edit')
                        ->label('Data de início')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->required(),
                    Forms\Components\Checkbox::make('auto_renew')
                        ->label('Renovação automática')
                        ->default(true),
                ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\TextColumn::make('vehicle.model.description')
                        ->description('Descrição', 'above'),
                    Tables\Columns\TextColumn::make('customer.user.name')
                        ->description('Cliente', 'above'),
                    Tables\Columns\TextColumn::make('owner.user.name')
                        ->description('Proprietário', 'above'),
                    Tables\Columns\TextColumn::make('vehicle.category.daily_price')
                        ->formatStateUsing(fn($state) => Number::currency($state / 100 ?? 0, 'BRL', 'pt_BR'))
                        ->description('Diária', 'above'),
                    Tables\Columns\TextColumn::make('status')
                        ->description('Status', 'above')
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
            'index' => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
        ];
    }
}
