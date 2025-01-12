<?php

namespace App\Filament\App\Resources\CustomerResource\Widgets;

use App\Enum\SecurityDepositTransactionStatusEnum;
use App\Models\SecurityDepositTransaction;
use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class SecurityDepositTransactionsTable extends BaseWidget
{
    public ?Model $record = null;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Caução')
            ->query(
                SecurityDepositTransaction::query()->whereHas('account', function($query) {
                    $query->where('company_id', Filament::getTenant()->id)
                        ->where('customer_id', $this->record?->id);
                })->orderByDesc('date')->orderByDesc('created_at')
            )
            ->columns([
                TextColumn::make('id')
                    ->formatStateUsing(fn ($state) => "#$state")
                    ->label('ID'),
                TextColumn::make('amount')
                    ->label('Valor')
                ->money('BRL', 100, 'pt_BR')
                ->summarize(
                    Tables\Columns\Summarizers\Sum::make()
                    ->label('Saldo')
                    ->money('BRL', 100, 'pt_BR')
                ),
                TextColumn::make('description')->label('Descrição'),
                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        SecurityDepositTransactionStatusEnum::PAID->value => 'Pago',
                        SecurityDepositTransactionStatusEnum::SCHEDULED->value => 'Agendado',
                    }),
                TextColumn::make('date')->label('Data')->date('d/m/Y'),
            ]);
    }
}
