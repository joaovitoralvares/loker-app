<?php

namespace App\Filament\App\Resources\InvoiceResource\Pages;

use App\Enum\ContractInvoiceStatusEnum;
use App\Filament\App\Resources\InvoiceResource;
use App\Models\Invoice;
use App\ValueObjects\MoneyValue;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected $listeners = [
        'refreshInvoice' => 'refreshForm',
    ];

    public function refreshForm(): void
    {
        $this->getRecord()->refresh();
        $this->form->fill($this->record->toArray());
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('pay')->label('Pagar')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (Invoice $invoice, Actions\Action $action) {
                    $invoice->status = ContractInvoiceStatusEnum::PAID->value;
                    $invoice->payment_date = Carbon::now();
                    $invoice->save();
                    $action->success();
                })->successRedirectUrl(ViewInvoice::getUrl(
                    ['record' => $this->getRecord()]
                ))
            ->visible(fn () => $this->record->status !== ContractInvoiceStatusEnum::PAID->value),
            Actions\Action::make('close')->label('Fechar fatura')
            ->color('info')
            ->requiresConfirmation()
            ->action(function (Invoice $invoice, Actions\Action $action) {
                $invoice->status = ContractInvoiceStatusEnum::PENDING->value;
                $invoice->save();
                $action->success();
            })->visible(fn () => $this->record->status === ContractInvoiceStatusEnum::DRAFT->value),
            Actions\DeleteAction::make(),
        ];
    }

}
