<?php

namespace App\Filament\App\Resources\InvoiceResource\RelationManagers;

use App\Actions\AddTransactionToInvoice;
use App\Actions\DeleteTransaction;
use App\Enum\ContractInvoiceTransactionTypeEnum;
use App\Models\Invoice;
use App\Models\Transaction;
use App\ValueObjects\MoneyValue;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    protected static ?string $label = 'Lançamento';
    protected static ?string $pluralLabel = 'Lançamentos';
    protected static ?string $title = 'Lançamentos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('description')
                    ->label('Descrição')
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->disabledOn('edit')
                    ->label('Valor')
                    ->placeholder('4.000')
                    ->mask(RawJs::make(<<<'JS'
                            $money($input, ',')
                        JS
                    ))
                    ->formatStateUsing(function ($state) {
                        return !empty($state) ? MoneyValue::from($state)->toBRL() : '';
                    })
                    ->dehydrateStateUsing(fn($state) => MoneyValue::fromBRL($state)->getRawAmount())
                    ->prefix('R$')
                    ->required()
                    ->minValue(1)
                    ->maxValue(10000000),
                Forms\Components\Select::make('type')->label('Tipo')->options([
                    ContractInvoiceTransactionTypeEnum::DEBIT->value => 'Cobrança',
                    ContractInvoiceTransactionTypeEnum::CREDIT->value => 'Desconto',
                ])->default(ContractInvoiceTransactionTypeEnum::DEBIT->value),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('description')->label('Descrição'),
                Tables\Columns\TextColumn::make('amount')->money('BRL', 100)->label('Valor'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d/m/y H:i')->label('Data'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data) {
                        $data['invoice_id'] = $this->getRelationship()->getParent()->id;
                        $action = app(AddTransactionToInvoice::class);
                        DB::beginTransaction();
                        $transaction = $action->execute($data);
                        DB::commit();
                        return $transaction;
                    })->after(fn(Component $livewire) => $livewire->dispatch('refreshInvoice'))
                    ->label('Novo lançamento')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->action(function (Tables\Actions\DeleteAction $action) {
                    try {
                        DB::beginTransaction();
                        $action->process(static function (Transaction $record) {
                            $action = app(DeleteTransaction::class);
                            $action->execute($record);
                        });
                        DB::commit();
                    } catch (\Throwable $exception) {
                        dd($exception);
                        DB::rollBack();
                        $action->failure();
                        return;
                    }

                    $action->success();
                })->after(fn(Component $livewire) => $livewire->dispatch('refreshInvoice')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

}
