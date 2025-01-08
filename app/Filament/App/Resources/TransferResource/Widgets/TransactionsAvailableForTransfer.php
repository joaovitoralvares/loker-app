<?php

namespace App\Filament\App\Resources\TransferResource\Widgets;

use App\Enum\OwnerTransactionStatusEnum;
use App\Models\Owner;
use App\Models\OwnerTransaction;
use App\Models\OwnerTransfer;
use App\ValueObjects\MoneyValue;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TransactionsAvailableForTransfer extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Transações disponíveis para repasse')
            ->query(OwnerTransaction::query()->where('company_id', Filament::getTenant()->id)->where('status', OwnerTransactionStatusEnum::PENDING->value))
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
                Tables\Columns\TextColumn::make('status'
                )->label('Status')
                ->formatStateUsing(fn($state) => match ($state) {
                    OwnerTransactionStatusEnum::PENDING->value => 'Disponível',
                    OwnerTransactionStatusEnum::PAID->value => 'Repassado',
                }),

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
                        DatePicker::make('from')
                            ->label('Data início')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        DatePicker::make('until')
                            ->label('Data fim')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
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
}
