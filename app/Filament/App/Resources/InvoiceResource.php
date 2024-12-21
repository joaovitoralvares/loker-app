<?php

namespace App\Filament\App\Resources;

use App\Enum\ContractInvoiceStatusEnum;
use App\Filament\App\Resources\InvoiceResource\Pages;
use App\Filament\App\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use App\ValueObjects\MoneyValue;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'Fatura';

    protected static ?string $pluralLabel = 'Faturas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\DatePicker::make('start_date')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->label('Data de inicio')
                        ->disabled(),
                    Forms\Components\DatePicker::make('end_date')
                        ->displayFormat('d/m/Y')
                        ->native(false)
                        ->label('Data de fechamento'),
                    Forms\Components\DatePicker::make('due_date')
                        ->displayFormat('d/m/Y')
                        ->native(false)
                        ->label('Data de vencimento'),
                    Forms\Components\DatePicker::make('payment_date')
                        ->displayFormat('d/m/Y')
                        ->native(false)
                        ->label('Data de pagamento')
                        ->disabled(),
                    Forms\Components\TextInput::make('amount')
                        ->disabledOn('edit')
                        ->label('Total')
                        ->placeholder('4.000')
                        ->mask(RawJs::make(<<<'JS'
                            $money($input, ',')
                        JS
                        ))
                        ->formatStateUsing(fn($state) => MoneyValue::from($state)->toBRL())
                        ->dehydrateStateUsing(fn($state) => MoneyValue::fromBRL($state)->getRawAmount())
                        ->prefix('R$')
                        ->required()
                        ->minValue(1)
                        ->maxValue(10000000),
                    Forms\Components\Placeholder::make('status')->content(fn(Invoice $invoice) => match ($invoice->status) {
                        ContractInvoiceStatusEnum::DRAFT->value => 'Aberto',
                        ContractInvoiceStatusEnum::PENDING->value => 'Aguardando pagamento',
                        ContractInvoiceStatusEnum::PAID->value => 'Paga',
                        ContractInvoiceStatusEnum::OVERDUE->value => 'Vencida',
                    }),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['contract.customer.user', 'contract.owner.user']))
            ->columns([
                Tables\Columns\Layout\Split::make([
                    TextColumn::make('period')->description('Período', 'above'),
                    Tables\Columns\Layout\Split::make([
                        TextColumn::make('amount')
                            ->description('Valor', 'above')
                            ->money('BRL', 100),
                        IconColumn::make('status')
                            ->alignEnd()
                            ->icon(fn(string $state): string => match ($state) {
                                ContractInvoiceStatusEnum::DRAFT->value => 'heroicon-o-pencil',
                                ContractInvoiceStatusEnum::PENDING->value => 'heroicon-o-clock',
                                ContractInvoiceStatusEnum::PAID->value => 'heroicon-o-check-circle',
                                ContractInvoiceStatusEnum::OVERDUE->value => 'heroicon-o-x-circle',
                            })
                            ->color(fn(string $state): string => match ($state) {
                                ContractInvoiceStatusEnum::DRAFT->value => 'info',
                                ContractInvoiceStatusEnum::PENDING->value => 'warning',
                                ContractInvoiceStatusEnum::PAID->value => 'success',
                                ContractInvoiceStatusEnum::OVERDUE->value => 'danger',
                            })
                            ->tooltip(fn(string $state): string => match ($state) {
                                ContractInvoiceStatusEnum::DRAFT->value => 'Aberta',
                                ContractInvoiceStatusEnum::PENDING->value => 'Fechada',
                                ContractInvoiceStatusEnum::PAID->value => 'Paga',
                                ContractInvoiceStatusEnum::OVERDUE->value => 'Vencida',
                            })->size('sm'),
                    ]),
                ])->from('md'),
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Split::make([
                        TextColumn::make('id')->description('ID', 'above')->formatStateUsing(fn($state) => '#' . $state),
                        TextColumn::make('contract.id')->description('Numero do contrato', 'above'),
                        TextColumn::make('payment_date')->date('d/m/Y')->description('Data de pagamento', 'above'),
                        TextColumn::make('contract.customer.user.name')->description('Cliente', 'above'),
                        TextColumn::make('contract.owner.user.name')->description('Proprietario', 'above'),
                    ])->from('md'),
                ])->collapsible()

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn(Invoice $record): string => self::getUrl('edit', ['record' => $record]))
                    ->visible(fn(Invoice $record): bool => $record->status !== ContractInvoiceStatusEnum::PAID->value),
                Tables\Actions\ViewAction::make()
                    ->url(fn(Invoice $record): string => self::getUrl('edit', ['record' => $record]))
                    ->visible(fn(Invoice $record): bool => $record->status === ContractInvoiceStatusEnum::PAID->value),
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
            RelationManagers\TransactionsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
            'view' => Pages\ViewInvoice::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
