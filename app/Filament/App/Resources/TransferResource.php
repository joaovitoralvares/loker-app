<?php

namespace App\Filament\App\Resources;

use App\Enum\OwnerTransactionStatusEnum;
use App\Filament\App\Resources\TransferResource\Pages;
use App\Filament\App\Resources\TransferResource\RelationManagers;
use App\Filament\App\Resources\TransferResource\Widgets\TransactionsAvailableForTransfer;
use App\Models\Invoice;
use App\Models\Owner;
use App\Models\OwnerTransaction;
use App\Models\OwnerTransfer;
use App\ValueObjects\MoneyValue;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TransferResource extends Resource
{
    protected static ?string $model = OwnerTransfer::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static ?string $label = 'Repasse';

    protected static ?string $pluralLabel = 'Repasses';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Repasses realizados')
            ->columns([
                Tables\Columns\TextColumn::make('owner_id')->label('Proprietário')
                    ->formatStateUsing(fn($state) => Owner::query()->with('user')->find($state)?->user->name),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Valor')
                    ->money('BRL', 100, 'pt_BR')
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()->label('Total repassado')->money('BRL', 100, 'pt_BR'),
                    ]),
                Tables\Columns\TextColumn::make('transferred_at')
                ->label('Data')
                ->date('d/m/Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('owner_id')
                    ->label('Proprietário')
                    ->searchable()
                    ->preload()
                    ->options(fn() => Owner::query()
                        ->with(['user'])
                        ->where('company_id', Filament::getTenant()->id)
                        ->take(10)->get()->pluck("user.name", "id")
                        ->toArray()
                    )
                    ->selectablePlaceholder(false)
                    ->default(),
                Tables\Filters\Filter::make('transferred_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Data início')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Data fim')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('transferred_at', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('transferred_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('De: ' . Carbon::parse($data['from'])->format('d/m/Y'))
                                ->removable(false);
                        }

                        if ($data['until'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Até: ' . Carbon::parse($data['until'])->format('d/m/Y'))
                                ->removable(false);
                        }

                        return $indicators;
                    })
            ])
            ->actions([
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TransactionsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransfers::route('/'),
            'view' => Pages\ViewTransfer::route('/{record}'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            TransactionsAvailableForTransfer::class
        ];
    }
}
