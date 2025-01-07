<?php

namespace App\Filament\App\Resources;

use App\Enum\OwnerTransactionStatusEnum;
use App\Filament\App\Resources\TransferResource\Pages;
use App\Filament\App\Resources\TransferResource\RelationManagers;
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
    protected static ?string $model = OwnerTransaction::class;

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
            ->columns([
                Tables\Columns\TextColumn::make('owner_id')->label('Proprietário')
                    ->formatStateUsing(fn($state) => Owner::query()->with('user')->find($state)?->user->name),
                Tables\Columns\TextColumn::make('invoice_id')
                    ->label('Fatura')
                    ->formatStateUsing(fn($state) => '#' . $state),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Valor')
                    ->money('BRL', 100, 'pt_BR')
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()->label('Total disponível')->money('BRL', 100, 'pt_BR')->query(fn (\Illuminate\Database\Query\Builder $query) => $query->where('status', OwnerTransactionStatusEnum::PENDING->value)),
                    ]),
                Tables\Columns\TextColumn::make('status')->label('Status'),

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
                Tables\Filters\Filter::make('created_at')
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
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('make_transfer')
                    ->label('Realizar Repasse')
                    ->requiresConfirmation()
                ->modalDescription(fn (Collection $records) => 'Confirmar repasse de R$' . MoneyValue::from($records->sum('amount'))->toBRL() . '?')
                ->action(function (Collection $records, Tables\Actions\BulkAction $action) {
                    DB::transaction(function () use ($records, $action) {
                        $total = $records->sum('amount');
                        /** @var OwnerTransfer $transfer */
                        $transfer = OwnerTransfer::create([
                            'amount' => $total,
                            'owner_id' => $records->first()->owner_id,
                            'company_id' => Filament::getTenant()->id,
                            'transferred_at' => now(),
                        ]);

                        $records->each(function (OwnerTransaction $transaction) use ($transfer) {
                            $transaction->transfer()->associate($transfer);
                            $transaction->status = OwnerTransactionStatusEnum::PAID->value;
                            $transaction->save();
                        });

                        $action->successNotification(Notification::make()->title('Repasse realizado!')->success());
                        $action->success();
                    });
                } )
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
            'index' => Pages\ListTransfers::route('/'),
            'create' => Pages\CreateTransfer::route('/create'),
            'edit' => Pages\EditTransfer::route('/{record}/edit'),
        ];
    }
}
