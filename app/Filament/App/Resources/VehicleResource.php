<?php

namespace App\Filament\App\Resources;

use App\Enum\VehicleStatusEnum;
use App\Enum\VehicleTransmissionEnum;
use App\Filament\App\Resources\VehicleResource\Pages;
use App\Filament\App\Resources\VehicleResource\RelationManagers;
use App\Models\Owner;
use App\Models\Vehicle\Brand;
use App\Models\Vehicle\Category;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleModel;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Components\Tab;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationGroup = 'Veículos';

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $label = 'Veículo';

    protected static ?string $pluralLabel = 'Veículos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()->schema([
                    Forms\Components\Tabs::make()->tabs([
                        Forms\Components\Tabs\Tab::make('Dados básicos')->schema([
                            Forms\Components\Select::make('category_id')
                                ->relationship('category', 'description')
                                ->createOptionForm(fn(Form $form) => CategoryResource::form($form))
                                ->createOptionUsing(function (array $data) {
                                    $category = Category::query()->make($data);
                                    $category->company()->associate(Filament::getTenant());
                                    $category->save();
                                    return $category;
                                })
                                ->required()
                                ->columnStart(1)
                                ->label('Categoria'),
                            Forms\Components\Select::make('brand_id')
                                ->relationship('brand', 'name')
                                ->createOptionForm(fn(Form $form) => BrandResource::form($form))
                                ->createOptionUsing(function (array $data) {
                                    $brand = Brand::query()->make($data);
                                    $brand->company()->associate(Filament::getTenant());
                                    $brand->save();
                                })
                                ->required()
                                ->live()
                                ->label('Marca'),
                            Forms\Components\Select::make('model_id')
                                ->relationship('model', 'description')
                                ->options(fn(Forms\Get $get) => VehicleModel::where('brand_id', $get('brand_id'))->pluck('description', 'id'))
                                ->disabled(fn(Forms\Get $get) => !filled($get('brand_id')))
                                ->createOptionForm(fn(Form $form) => VehicleModelResource::form($form))
                                ->createOptionUsing(function (array $data) {
                                    $model = VehicleModel::query()->make($data);
                                    $model->company()->associate(Filament::getTenant());
                                    $model->save();
                                })
                                ->required()
                                ->label('Modelo'),
                            Forms\Components\Select::make('year')
                                ->required()
                                ->options(function () {
                                    $years = [];
                                    for ($year = now()->addYear()->year; $year > 2000; $year--) {
                                        $years[$year] = $year;
                                    }
                                    return $years;
                                })->label('Ano'),
                            Forms\Components\TextInput::make('plate')
                                ->required()
                                ->label('Placa')
                                ->unique()
                                ->extraInputAttributes(['onChange' => 'this.value = this.value.toUpperCase()'])
                            ,
                            Forms\Components\TextInput::make('color')
                                ->required()
                                ->label('Cor')
                                ->extraInputAttributes(['onChange' => 'this.value = this.value.toUpperCase()'])
                            ,
                            Forms\Components\Select::make('owner_id')
                                ->options(Owner::query()->join('users', 'users.id', '=', 'owners.user_id')->pluck('users.name', 'owners.id'))
                                ->searchable()
                                ->preload()
                                ->label('Proprietário')
                        ])->columns(2),
                        Forms\Components\Tabs\Tab::make('Motorização')->schema([
                            Forms\Components\TextInput::make('engine')
                                ->required()
                                ->label('Motor')
                                ->placeholder('1.0')
                                ->maxLength(255),
                            Forms\Components\Select::make('transmission')
                                ->options(VehicleTransmissionEnum::toOptions())
                                ->required()
                                ->label('Transmissão')
                                ->default(VehicleTransmissionEnum::MANUAL->value)
                                ->selectablePlaceholder(false),
                        ]),
                        Forms\Components\Tabs\Tab::make('Disponibilidade')->schema([
                            Forms\Components\Select::make('status')
                                ->options(VehicleStatusEnum::toOptions())
                                ->required()
                                ->default(VehicleStatusEnum::AVAILABLE->value)
                                ->selectablePlaceholder(false)
                        ]),
                        Forms\Components\Tabs\Tab::make('Informações Adicionais')->schema([
                            Forms\Components\TextInput::make('renavam')
                                ->required()
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('chassi')->required()->columnSpan(2),
                            Forms\Components\TextInput::make('odometer')->required()
                                ->numeric()
                                ->suffix('KM')
                                ->label('Odômetro'),
                        ])->columns(4),
                        Forms\Components\Tabs\Tab::make('Foto')
                            ->schema([
                                FileUpload::make('image_url')
                                    ->disk('public')
                                    ->label('Foto do veículo')
                                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                            ])->columns(2)
                    ]),
                ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\Layout\View::make('filament.app.image-column'),
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('category.description')->description('Categoria', 'above'),
                        Tables\Columns\TextColumn::make('brand.name')->description('Marca', 'above'),
                        Tables\Columns\TextColumn::make('model.description')->description('Modelo', 'above'),
                    ]),

                ])->from('lg')
            ])->contentGrid([
                'md' => 2,
                'lg' => 2,
                'xl' => 2
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
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
