<?php

namespace App\Filament\App\Resources\TransferResource\RelationManagers;

use App\Enum\OwnerTransactionStatusEnum;
use App\Models\Owner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('invoice_id')
            ->heading('Faturas')
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
                            Tables\Columns\Summarizers\Sum::make()->label('Total')->money('BRL', 100, 'pt_BR'),
                        ]),
                    Tables\Columns\TextColumn::make('status'
                    )->label('Status')
                        ->formatStateUsing(fn($state) => match ($state) {
                            OwnerTransactionStatusEnum::PENDING->value => 'Disponível',
                            OwnerTransactionStatusEnum::PAID->value => 'Repassado',
                        }),
                ])
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->actions([
            ])
            ->bulkActions([
            ]);
    }
}
